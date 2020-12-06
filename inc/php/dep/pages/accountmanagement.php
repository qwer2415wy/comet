<?php
include '../permissions.php';

$type = $_GET['type'];
if ($type == 'edit_account') {
    handlePermission('edit_accounts');
    $username = $purifier->purify($_GET['username']);
    $group = $purifier->purify($_GET['group']);
    try {
        $stmnt = $pdo->prepare('UPDATE nm_accounts SET `usergroup`=? WHERE `username`=?;');
        $stmnt->bindParam(1, $group, PDO::PARAM_STR);
        $stmnt->bindParam(2, $username, PDO::PARAM_STR);
        $stmnt->execute();
    } catch (PDOException $ex) {
        errorAlert($ex->getMessage());
    }
} else if ($type == 'delete_account') {
    handlePermission('edit_accounts');
    $id = $_GET['id'];
    try {
        $stmnt = $pdo->prepare('DELETE FROM nm_accounts WHERE `id`=?;');
        $stmnt->bindParam(1, $id, PDO::PARAM_INT);
        $stmnt->execute();
    } catch (PDOException $ex) {
        errorAlert($ex->getMessage());
    }
    echo 'done edit-account';
} else if ($type == 'delete_group') {
    handlePermission('edit_accounts');
    $id = $_GET['id'];
    try {
        $stmnt = $pdo->prepare('DELETE FROM nm_accountgroups WHERE id=?;');
        $stmnt->bindParam(1, $id, PDO::PARAM_INT);
        $stmnt->execute();
    } catch (PDOException $ex) {
        errorAlert($ex->getMessage());
    }
    echo 'done edit-account';
} else if ($type == 'create_account') {
    handlePermission('edit_accounts');
    $id = $purifier->purify($_GET['account']);
    //$pass = md5('HM$75Dh(r^#A22j@_' . $_GET['password']);
    $pass = password_hash($_GET['password'], PASSWORD_BCRYPT);
    $group = $name = $purifier->purify(filter_input(INPUT_GET, 'value', FILTER_SANITIZE_STRING));

    try {
        $stmnt = $pdo->prepare('SELECT `id` FROM nm_accounts WHERE `username`=?;');
        $stmnt->bindParam(1, $id, PDO::PARAM_STR);
        $stmnt->execute();
        if ($stmnt->rowCount() == 0) {
            $stmnt = $pdo->prepare('INSERT INTO nm_accounts(username, password, usergroup, notifications) VALUES (?, ?, ?, "[]");');
            $stmnt->bindParam(1, $id, PDO::PARAM_STR);
            $stmnt->bindParam(2, $pass, PDO::PARAM_STR);
            $stmnt->bindParam(3, $group, PDO::PARAM_STR);
            $stmnt->execute();
            die(true);
        }
    } catch (PDOException $ex) {
        errorAlert($ex->getMessage());
    }
    die(false);
} else if ($type == 'create_group') {
    handlePermission('edit_accounts');
    $name = $purifier->purify($_GET['name']);
    $permissions = $_GET['permission'];

    try {
        $stmnt = $pdo->prepare('SELECT id FROM nm_accountgroups WHERE `name`=?;');
        $stmnt->bindParam(1, $name, PDO::PARAM_STR);
        $stmnt->execute();
        if ($stmnt->rowCount() == 0) {
            $stmnt = $pdo->prepare('INSERT INTO nm_accountgroups(`name`) VALUES (?);');
            $stmnt->bindParam(1, $name, PDO::PARAM_STR);
            $stmnt->execute();

            foreach ($permissions as $perm) {
                $stmnt = $pdo->prepare("UPDATE nm_accountgroups SET " . $perm . "=1 WHERE `name`=?;");
                $stmnt->bindParam(1, $name, PDO::PARAM_STR);
                $stmnt->execute();
            }

            die('true');
        } else {
            die('false');
        }
    } catch (PDOException $ex) {
        errorAlert($ex->getMessage());
    }
} else if ($type == 'load_group') {
    $id = $_GET['id'];
    echo '<div id="edit-group" class="modal" group="' . $id . '">
            <div class="modal-content" style="width: 600px">
                <form id="edit-group-form" group="' . $id . '">
                    <div class="nm-input-container">
                        <label>Permissions</label>
                    </div>
                    <div class="row">';


    foreach ($pdo->query('SHOW COLUMNS FROM `nm_accountgroups`') as $item) {
        if ($item['Field'] == 'id' || $item['Field'] == 'name') {
            continue;
        }
        $stmnt = $pdo->prepare('SELECT ' . $item['Field'] . ' FROM nm_accountgroups WHERE id=?');
        $stmnt->bindParam(1, $id, 1);
        $stmnt->execute();
        $row = $stmnt->fetch();
        if ($row[0][0] == '1') {
            echo '<div class="col-3">';
            //echo '' . $item['Field'] . ': <input type="checkbox" name="permission[]" value="' . $item['Field'] . '"><br>';
            echo '<div class="nm-input-container">
                            <input type="checkbox" id="' . $item['Field'] . '" name="permission[]" value="' . $item['Field'] . '" checked="checked">
                            <label for="' . $item['Field'] . '">' . $item['Field'] . '</label>
                        </div>';
            echo '</div>';
        } else {
            echo '<div class="col-3">';
            echo '<div class="nm-input-container">
                            <input type="checkbox" id="' . $item['Field'] . '" name="permission[]" value="' . $item['Field'] . '">
                            <label for="' . $item['Field'] . '">' . $item['Field'] . '</label>
                        </div>';
            echo '</div>';
        }
    }
    echo '</div>
                    <div class="nm-input-container" style="    text-align: right;
    padding-right: 16px;
    margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="edit-group">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="edit-group">Save</button>
                    </div>
                </form>
            </div>
        </div>';
} else if ($type == 'edit_group') {
    handlePermission('edit_accounts');
    $id = $_GET['id'];
    $permissions = $_GET['permission'];

    try {
        $stmnt = $pdo->prepare('SELECT id FROM nm_accountgroups WHERE id=?');
        $stmnt->bindParam(1, $id, PDO::PARAM_INT);
        $stmnt->execute();
        if ($stmnt->rowCount() > 0) {

            foreach ($pdo->query('SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME`="nm_accountgroups"') as $item) {
                if ($item['COLUMN_NAME'] == 'id' || $item['COLUMN_NAME'] == 'name') {
                    continue;
                }
                $stmnt3 = $pdo->prepare("UPDATE nm_accountgroups SET " . $item['COLUMN_NAME'] . " = '0' WHERE id='" . $id . "'");
                $stmnt3->execute();
            }
            foreach ($permissions as $perm) {
                $stmnt = $pdo->prepare('UPDATE nm_accountgroups SET ' . $perm . '=1 WHERE id=?');
                $stmnt->bindParam(1, $id, PDO::PARAM_INT);
                $stmnt->execute();
            }
            die('true');
        } else {
            die('false');
        }
    } catch (PDOException $ex) {
        errorAlert($ex->getMessage());
    }
} else if ($type == 'change_password') {
    $old = $purifier->purify($_GET['old']);
    $new = $purifier->purify($_GET['new']);

    $salt = 'HM$75Dh(r^#A22j@_';
    //$pwhash = md5($salt . $old);

    //if ($_SESSION['user']->getPassword() == $pwhash) {
    if (password_verify($old, $_SESSION['user']->getPassword())) {
        $user = $_SESSION['user']->getUsername();
        //$pwhash = md5($salt . $new);
        $pwhash = password_hash($new, PASSWORD_BCRYPT);
        try {
            $stmnt = $pdo->prepare('UPDATE nm_accounts SET password=? WHERE username=?;');
            $stmnt->bindParam(1, $pwhash, PDO::PARAM_STR);
            $stmnt->bindParam(2, $user, PDO::PARAM_STR);
            $stmnt->execute();
            $_SESSION['user']->setPassword($pwhash);
            die('Password changed');
        } catch (PDOException $ex) {
            errorAlert($ex->getMessage());
        }
    } else {
        die('Wrong password');
    }
} else if ($type == 'change_language') {
    $language = $_GET['language'];

    $source = 'https://raw.githubusercontent.com/ChimpGamer/NetworkManager/master/Webbie/languages/' . $language . '.php';
    $destination = '../languages/' . $language . '.php';

    echo storeUrlToFilesystem($source, $destination);
}

function storeUrlToFilesystem($source, $destination)
{
    try {
        $fp = fopen($destination, 'w+');
        if ($fp === false) {
            throw new Exception('Could not open ' . $destination);
        }

        //Create a cURL handle.
        if (!extension_loaded('curl')) {
            throw new Exception('The Php extension cURL is not installed!');
        }
        $ch = curl_init($source);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($statusCode == 200) {
            echo 'Downloaded!';
        } else {
            echo "Status Code: " . $statusCode;
        }
        return true;
    } catch (Exception $e) {
        echo 'Caught exception: ', $e->getMessage(), "\n";
    }
    return false;
}