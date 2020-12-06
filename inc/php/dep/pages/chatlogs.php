<?php
include '../permissions.php';
handlePermission('view_chatlogs');
$defaultlang = 'English';
$dateformat = 'm/d/y h:i a';
foreach ($webSettings as $variable => $value) {
    if ($variable == 'default-language') {
        $defaultlang = $value;
    }
    if ($variable == 'date-format') {
        $dateformat = $value;
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
    $total = $pdo->query('SELECT COUNT(*) FROM nm_chatlogs;');
    $total = $total->fetchColumn();
    $limit = 10;
    $pages = ceil($total / $limit);
    $page = $_GET['p'];
    $offset = ($page - 1) * $limit;
    $start = $offset + 1;
    $end = min(($offset + $limit), $total);
    $prevlink = ($page > 1) ? '<a chatlogs="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
    $nextlink = ($page < $pages) ? '<a chatlogs="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
    $stmt = $pdo->prepare('SELECT `uuid`, `creator`,`tracked`, `time` FROM nm_chatlogs ORDER BY `time` DESC LIMIT ? OFFSET ?');
    $stmt->bindParam(1, $limit, PDO::PARAM_INT);
    $stmt->bindParam(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {

        echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['CHATLOG_CHATLOGID'] . '</th>
                                <th>' . $lang['VAR_CREATOR'] . '</th>
                                <th>' . $lang['CHATLOG_TRACKED'] . '</th>
                                <th>' . $lang['VAR_TIME'] . '</th>';
        if (hasPermission('edit_chatlog')) {
            echo '<th class="text-right">' . $lang['VAR_ACTION'] . '</th>';
        }
        echo '</tr>
                            </thead>
                            <tbody>';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><a href="../chatlog.php?uuid=' . $row['uuid'] . '">' . $lang['CHATLOG_CHATLOGS'] . ' #' . $row['uuid'] . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><a href="#' . $row['creator'] . '" player="' . $row['creator'] . '">' . getName($row['creator']) . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><a href="#' . $row['tracked'] . '" player="' . $row['tracked'] . '">' . getName($row ['tracked']) . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span> ' . date($dateformat, $row['time'] / 1000) . ' </span>
                                    </div>
                                </td>';
            if (hasPermission('edit_chatlog')) {
                echo '<td class="text-right">
                                     <div class="fb-table-cell-wrapper">
                                            <button style="background: transparent; border: none" nmdelete-chatlog="' . $row['uuid'] . '" style="position: relative;right: -24px;"><i class="material-icons" style="color: red">delete</i></button>
                                     </div>
                                </td>';
            }
            echo '</tr>';
        }
        echo '</tbody>
                        </table>
                    </div>';

        echo '<div class="data-card-footer-pagination">
                            <div class="nm-pagination-bar">' . $start . '-' . $end . ' of ' . $total . ' ' . $prevlink . $nextlink . '</div>
                        </div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
    }
} else {
    if ($_GET['load'] == 'delete_chatlog') {
        hasPermission('edit_chatlog');
        $uuid = $_GET['uuid'];

        try {
            $stmnt = $pdo->prepare('DELETE FROM nm_chatlogs WHERE `uuid`=?;');
            $stmnt->bindParam(1, $uuid, PDO::PARAM_STR);
            $result = $stmnt->execute();
            die($result);
        } catch (PDOException $ex) {
            errorAlert($ex->getMessage());
        }
    }
}