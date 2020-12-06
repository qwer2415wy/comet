<?php
include '../permissions.php';
handlePermission('view_filter');
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

$load = $_GET['load'];

if ($load == 'load') {
    $stmt = $pdo->query('SELECT `id`, `word`, `replacement`, `server` FROM nm_filter;');
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="data-card-body">';
            echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-70">
                                <div class="nm-input-container" style="position: relative; top: 7px; padding: 0 !important;">
                                     <input word="' . $row['id'] . '" type="text" placeholder="Value" value="' . $row['word'] . '">
                                 </div>
                            </div>
                            <div class="data-card-row-value nm-data-card-70">
                                <div class="nm-input-container" style="position: relative; top: 7px; right: -30px; padding: 0 !important;">
                                     <input replacement="' . $row['id'] . '" type="text" placeholder="Cancel swearing" value="' . $row['replacement'] . '">
                                 </div>
                            </div>
                            <div class="data-card-row-value nm-data-card-70">
                                <div class="nm-input-container" style="position: relative; top: 7px; right: -60px; padding: 0 !important;">
                                     <input server="' . $row['id'] . '" type="text" placeholder="Global" value="' . $row['server'] . '">
                                 </div>
                            </div>
                            <div class="data-card-row-value nm-data-card-50 text-right">
                                <button word="' . $row['id'] . '" style="background: transparent;border: none;position: relative;left: 25px;margin: 10px;"><i class="material-icons" style="color: red">delete</i></button>
                            </div>
                        </div>';
        }

        echo '</div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
    }
} else if ($load == 'add') {
    handlePermission('edit_filter');
    $string = $purifier->purify($_GET['string']);

    $data = explode("!", $string);
    try {
        $stmnt = $pdo->prepare("INSERT INTO nm_filter(`word`,`server`) VALUES (?,?);");
        $stmnt->bindParam(1, $data[0], 2);
        $stmnt->bindParam(2, $data[1], 2);
        $stmnt->execute();
    } catch (PDOException $ex) {
        errorAlert($ex->getMessage());
    }
} else if ($load == 'remove') {
    handlePermission('edit_filter');
    $id = $_GET['id'];

    try {
        $stmnt = $pdo->prepare("DELETE FROM nm_filter WHERE `id`=?;");
        $stmnt->bindParam(1, $id, 2);
        $stmnt->execute();
    } catch (PDOException $ex) {
        errorAlert($ex->getMessage());
    }
} else if ($load == 'update') {
    handlePermission('edit_filter');
    $id = $_GET['id'];
    $word = $purifier->purify($_GET['word']);
    $replacement = $purifier->purify($_GET['replacement']);
    $server = $purifier->purify($_GET['server']);

    try {
        if ($server != null) {
            $server = $server == 'NULL' || $replacement == '' ? null : $server;
            $type = PDO::PARAM_STR;
            if ($server == null) {
                $type = PDO::PARAM_NULL;
            }
            $stmnt = $pdo->prepare("UPDATE nm_filter SET `server`=? WHERE `id`=?;");
            $stmnt->bindParam(1, $server, $type);
            $stmnt->bindParam(2, $id, 1);
            $stmnt->execute();
            die(true);
        } else if ($replacement != null) {
            $replacement = $replacement == 'NULL' || $replacement == '' ? null : $replacement;
            $type = PDO::PARAM_STR;
            if ($replacement == null) {
                $type = PDO::PARAM_NULL;
            }
            $replacement = str_replace(';v1', ' ', $replacement);
            $replacement = str_replace(';v2', '&', $replacement);
            $replacement = str_replace(';v3', '%', $replacement);
            $stmnt = $pdo->prepare("UPDATE nm_filter SET `replacement`=? WHERE id=?;");
            $stmnt->bindParam(1, $replacement, $type);
            $stmnt->bindParam(2, $id, 1);
            $stmnt->execute();
            die(true);
        } else if ($word != null) {
            $stmnt = $pdo->prepare("UPDATE nm_filter SET `word`=? WHERE `id`=?;");
            $stmnt->bindParam(1, $word, 2);
            $stmnt->bindParam(2, $id, 1);
            $stmnt->execute();
            die(true);
        } else {
            die(false);
        }
    } catch (PDOException $ex) {
        errorAlert($ex->getMessage());
    }
}
