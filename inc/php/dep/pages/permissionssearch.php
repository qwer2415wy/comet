<?php
include_once '../permissions.php';
include '../MinecraftColorcodes.php';
handlePermission('view_permissions');
$colorCodes = new MinecraftColorcodes();
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
$type = $_GET['load'];

$search = str_replace(";v5", "[", $_GET['q']);
$search = str_replace(";v6", "]", $search);
$query = '%' . $search . '%';

if ($type == 'groups') {
    $total = $pdo->prepare('SELECT COUNT(*) FROM nm_permissions_groups WHERE (id LIKE ? || name LIKE ? || ladder LIKE ?)');
    $total->bindParam(1, $query, 2);
    $total->bindParam(2, $query, 2);
    $total->bindParam(3, $query, 2);
    $total->execute();
    $total = $total->fetchColumn();
    $limit = 10;
    $pages = ceil($total / $limit);
    $page = $_GET['p'];
    $offset = ($page - 1) * $limit;
    $start = $offset + 1;
    $end = min(($offset + $limit), $total);
    $prevlink = ($page > 1) ? '<a nmgroups="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
    $nextlink = ($page < $pages) ? '<a nmgroups="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
    $stmt = $pdo->prepare('SELECT id, name, ladder, rank FROM nm_permissions_groups WHERE (id LIKE ? || name LIKE ? || ladder LIKE ?) ORDER BY id ASC LIMIT ? OFFSET ?');
    $stmt->bindParam(1, $query, 2);
    $stmt->bindParam(2, $query, 2);
    $stmt->bindParam(3, $query, 2);
    $stmt->bindParam(4, $limit, PDO::PARAM_INT);
    $stmt->bindParam(5, $offset, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $iterator = new IteratorIterator($stmt);

        echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['VAR_ID'] . '</th>
                                <th>' . $lang['PERMISSIONS_GROUP_NAME'] . '</th>
                                <th>' . $lang['PERMISSIONS_GROUP_LADDER'] . '</th>
                                <th>' . $lang['PERMISSIONS_GROUP_RANK'] . '</th>
                                <th class="text-right">' . $lang['VAR_ACTION'] . '</th>
                            </tr>
                            </thead>
                            <tbody>';
        foreach ($iterator as $row) {

            echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['id'] . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><a nmgroupid1="' . $row['id'] . '">' . $row['name'] . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['ladder'] . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['rank'] . '</span>
                                    </div>
                                </td>';
            if (hasPermission('edit_permissions')) {
                echo '<td class="text-right">
                                     <div class="fb-table-cell-wrapper">
                                        <button class="nm-button nm-raised nm-raised-edit" nmedit-group="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_EDIT'] . '</button>
                                        <button class="nm-button nm-raised nm-raised-delete" nmdelete-group="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_DELETE'] . '</button>
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
                            <button style="color: grey;" class="nm-button" id="create-group" modal="create-group"><i class="material-icons">add</i> Create group </button>
                        </div>';


    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>
        <button style="color: grey;" class="nm-button" id="create-group" modal="create-group"><i class="material-icons">add</i> Create group </button>';
    }
} else if ($type == 'users') {
    $total = $pdo->prepare('SELECT COUNT(*) FROM nm_permissions_players WHERE (uuid LIKE ? || name LIKE ? || prefix LIKE ? || suffix LIKE ?)');
    $total->bindParam(1, $query, 2);
    $total->bindParam(2, $query, 2);
    $total->bindParam(3, $query, 2);
    $total->bindParam(4, $query, 2);
    $total->execute();
    $total = $total->fetchColumn();
    $limit = 10;
    $pages = ceil($total / $limit);
    $page = $_GET['p'];
    $offset = ($page - 1) * $limit;
    $start = $offset + 1;
    $end = min(($offset + $limit), $total);
    $prevlink = ($page > 1) ? '<a nmusers="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
    $nextlink = ($page < $pages) ? '<a nmusers="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
    $stmt = $pdo->prepare('SELECT uuid,name,prefix,suffix FROM nm_permissions_players WHERE (uuid LIKE ? || name LIKE ? || prefix LIKE ? || suffix LIKE ?) ORDER BY name DESC LIMIT ? OFFSET ?');
    $stmt->bindParam(1, $query, 2);
    $stmt->bindParam(2, $query, 2);
    $stmt->bindParam(3, $query, 2);
    $stmt->bindParam(4, $query, 2);
    $stmt->bindParam(5, $limit, PDO::PARAM_INT);
    $stmt->bindParam(6, $offset, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $iterator = new IteratorIterator($stmt);

        echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>Player</th>
                                <th>Prefix</th>
                                <th>Suffix</th>
                                <th class="text-right">Actions</th>
                            </tr>
                            </thead>
                            <tbody>';
        foreach ($iterator as $row) {

            echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><img src="https://crafatar.com/avatars/' . $row['uuid'] . '?size=20"> <a href="#' . $row['uuid'] . '" userpermissions="' . $row['uuid'] . '">' . $row['name'] . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $colorCodes->convert($row['prefix'], true) . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $colorCodes->convert($row['suffix'], true) . '</span>
                                    </div>
                                </td>';
            if (hasPermission('edit_permissions')) {
                echo '<td class="text-right">
                                     <div class="fb-table-cell-wrapper">
                                        <button class="nm-button nm-raised nm-raised-edit" nmedit-permplayer="' . $row['uuid'] . '" style="position: relative;right: -24px;">' . $lang['VAR_EDIT'] . '</button>
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
                            <div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
    }
}