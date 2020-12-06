<?php
include '../permissions.php';
handlePermission('view_commandblocker');
$defaultlang = 'English';
foreach ($webSettings as $variable => $value) {
    if ($variable == 'default-language') {
        $defaultlang = $value;
    }
}
if (isset($_COOKIE['language'])) {
    $language = $_COOKIE['language'];
    $langdir = "../languages/";
    if (is_dir($langdir)) {
        if ($dh = opendir($langdir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '..' && $file != '.') {
                    $filename = pathinfo($file, PATHINFO_FILENAME);
                    $result = $langdir . $file;
                    switch ($language) {
                        case $filename:
                            include $result;
                            break;
                    }
                }
            }
            closedir($dh);
        }
    }
} else {
    include '../languages/' . $defaultlang . '.php';
}

if (!isset($_GET['load'])) {
    $stmt = $pdo->query('SELECT `id`, `command`, `server`, `customMessage`, `bypasspermission` FROM nm_blockedcommands;');
    if ($stmt->rowCount() > 0) {

        echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th style="width: 39%">Command</th>
                                <th style="width: 39%">Custom Response</th>
                                <th style="width: 30%">Server</th>
                                <th>PermissionBypass</th>';
        if (hasPermission('edit_tags')) {
            echo '<th>' . $lang['VAR_ACTION'] . '</th>';
        }
        echo '</tr>
                            </thead>
                            </table>
                            </div>
                            ';

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="data-card-body">';
            echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-50">
                                <div class="nm-input-container" style="position: relative; top: 7px; padding: 0 !important;">
                                     <input command="' . $row['id'] . '" type="text" placeholder="Value" value="' . $row['command'] . '">
                                 </div>
                            </div>
                            <div class="data-card-row-value nm-data-card-50">
                                <div class="nm-input-container" style="position: relative; top: 7px; padding: 0 !important; left: 3%">
                                     <input customMessage="' . $row['id'] . '" type="text" placeholder="Custom response" value="' . $row['customMessage'] . '">
                                 </div>
                            </div>
                            <div class="data-card-row-value nm-data-card-30">
                                <div class="nm-input-container" style="position: relative; top: 7px; padding: 0 !important; left: 10%;">
                                     <input server="' . $row['id'] . '" type="text" placeholder="Global" value="' . $row['server'] . '">
                                 </div>
                            </div>
                            <div class="data-card-row-value nm-data-card-10">
                                <div class="nm-input-container" style="position: relative; top: -12px; padding: 0 !important; left: 35%"><input type="checkbox" id="' . $row['id'] . '" name="set-bypasspermission" nmsetcommandbypasspermission="' . $row['id'] . '" class="switch-input" ' . isChecked($row['bypasspermission']) . ' ' . hasSwitchPermission() . '>
                                    <label for="' . $row['id'] . '" class="switch-label switch-cmdblock" style="margin-left: 0 !important;"><span style="position: relative !important;" class="toggle--on">' . $lang['VAR_TRUE'] . '</span><span style="position: relative !important;" class="toggle--off">' . $lang['VAR_FALSE'] . '</span></label>
                                </div>
                            </div>
                            <div class="data-card-row-value nm-data-card-10">
                                <button command="' . $row['id'] . '" style="background: transparent; border: none; position: relative;left: 25px;margin: 10px;"><i class="material-icons" style="color: red">delete</i></button>
                            </div>
                        </div>';
        }

        echo '</div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
    }
} else if ($_GET['load'] == 'add') {
    handlePermission('edit_commandblocker');
    $string = $purifier->purify($_GET['string']);

    $data = explode('!', $string);
    $perm = $data[2] == null || $data[2] == '' ? false : true;
    $command = str_replace(';v7', '+', $data[0]);
    try {
        $stmnt = $pdo->prepare('INSERT INTO nm_blockedcommands(`command`, `server`, `bypasspermission`) VALUES (?, ?, ?);');
        $stmnt->bindParam(1, $command, 2);
        $stmnt->bindParam(2, $data[1], 2);
        $stmnt->bindParam(3, $perm, PDO::PARAM_BOOL);
        $stmnt->execute();
        die(true);
    } catch (PDOException $ex) {
        errorAlert($ex->getMessage());
    }
} else if ($_GET['load'] == 'remove') {
    handlePermission('edit_commandblocker');
    $id = $_GET['id'];

    try {
        $stmnt = $pdo->prepare('DELETE FROM nm_blockedcommands WHERE `id`=?;');
        $stmnt->bindParam(1, $id, 2);
        $stmnt->execute();
    } catch (PDOException $ex) {
        errorAlert($ex->getMessage());
    }
} else if ($_GET['load'] == 'update') {
    handlePermission('edit_commandblocker');
    $id = $_GET['id'];
    $word = $purifier->purify(str_replace(';v7', '+', $_GET['command']));
    $server = $_GET['server'] == null ? "" : $purifier->purify($_GET['server']);
    $bypasspermission = $_GET['bypass'];

    try {
        if ($server != null || $server == '') {
            $stmnt = $pdo->prepare('UPDATE nm_blockedcommands SET `server`=? WHERE `id`=?;');
            $stmnt->bindParam(1, $server, PDO::PARAM_STR);
            $stmnt->bindParam(2, $id, PDO::PARAM_INT);
            $stmnt->execute();
            echo 'server true';
        }
        if ($word != null) {
            $stmnt = $pdo->prepare('UPDATE nm_blockedcommands SET `command`=? WHERE `id`=?;');
            $stmnt->bindParam(1, $word, PDO::PARAM_STR);
            $stmnt->bindParam(2, $id, PDO::PARAM_INT);
            $stmnt->execute();
            echo 'command true';
        }
        if ($bypasspermission != null) {
            $bypasspermission = filter_var($bypasspermission, FILTER_VALIDATE_BOOLEAN);
            $stmnt = $pdo->prepare('UPDATE nm_blockedcommands SET `bypasspermission`=? WHERE `id`=?;');
            $stmnt->bindValue(1, $bypasspermission, PDO::PARAM_BOOL);
            $stmnt->bindParam(2, $id, PDO::PARAM_INT);
            $result = $stmnt->execute();
            echo 'bypasspermission ' . $result;
        }
    } catch (PDOException $ex) {
        errorAlert($ex->getMessage());
    }
    if ($server == null || $word == null || $bypasspermission == null) {
        die('false. server: ' . $server == null);
    }
}

function hasSwitchPermission()
{
    if (!hasPermission('edit_commandblocker')) {
        return 'disabled readonly';
    }
}