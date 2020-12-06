<?php
include '../permissions.php';
include '../MinecraftColorcodes.php';
handlePermission('view_servers');
header("Content-Type: text/html; UTF-8");
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

if (!isset($_GET['load'])) {
    try {
        $total = $pdo->query('SELECT COUNT(*) FROM nm_servers;');
        $total = $total->fetchColumn();
        $limit = 10;
        $pages = ceil($total / $limit);
        $page = $_GET['p'];
        $offset = ($page - 1) * $limit;
        $start = $offset + 1;
        $end = min(($offset + $limit), $total);
        $prevlink = ($page > 1) ? '<a nmservers="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
        $nextlink = ($page < $pages) ? '<a nmservers="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
        $stmt = $pdo->prepare('SELECT id, servername, displayname, ip, port, motd, restricted, online FROM nm_servers ORDER BY id LIMIT ? OFFSET ?;');
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->bindParam(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $iterator = new IteratorIterator($stmt);

            echo '<div class="data-card-body">

                <div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['VAR_ID'] . '</th>
                                <th>' . $lang['VAR_SERVER'] . '</th>
                                <th>' . $lang['SERVERS_DISPLAYNAME'] . '</th>
                                <th>' . $lang['VAR_IP'] . '</th>
                                <th>' . $lang['SERVERS_PORT'] . '</th>
                                <th>' . $lang['SERVERS_MOTD'] . '</th>
                                <th>' . $lang['SERVERS_RESTRICTED'] . '</th>
                                <th>' . $lang['SERVERS_ONLINE'] . '</th>';
            if (hasPermission('edit_servers')) {
                echo '<th class="text-right">' . $lang['VAR_ACTION'] . '</th>';
            }
            echo '</tr>
                            </thead>
                            <tbody>';
            foreach ($iterator as $row) {

                $online = $row['online'] != '1' ? '<span class="label label-danger">OFFLINE</span>' : '<span class="label label-success">ONLINE</span>';
                echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['id'] . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['servername'] . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $colorCodes->convert($row['displayname']) . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['ip'] . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['port'] . '</span>
                                    </div>
                                </td>
                                <td class="tdmessage">
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $colorCodes->convert(str_replace("%newline%", "\n", $row['motd']), true) . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <div class="nm-input-container" style="display: grid; position: relative; top: 0;padding: 0 !important;"><input type="checkbox" id="' . $row['id'] . '" name="set-restricted" nmsetserverrestricted="' . $row['id'] . '" class="switch-input" ' . isChecked($row['restricted']) . ' ' . hasSwitchPermission() . '>
                                            <label for="' . $row['id'] . '" class="switch-label switch-nomargin" style="margin-left: 0 !important;"><span style="position: relative !important;" class="toggle--on">' . $lang['VAR_TRUE'] . '</span><span style="position: relative !important;" class="toggle--off">' . $lang['VAR_FALSE'] . '</span></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        ' . $online . '
                                    </div>
                                </td>';
                if (hasPermission('edit_servers')) {
                    echo '<td class="text-right">
                                        <div class="fb-table-cell-wrapper">
                                            <button style="background: transparent; border: none" nmedit-server="' . $row['id'] . '" style="position: relative;right: -24px;"><i class="material-icons" style="color: orange">create</i></button>
                                            <button style="background: transparent; border: none" nmdelete-server="' . $row['id'] . '" style="position: relative;right: -24px;"><i class="material-icons" style="color: red">delete</i></button>
                                        </div>
                                    </td>';
                }
                echo '</tr>';
            }
            echo '</tbody>
                        </table>
                    </div>';

            echo '<div class="data-card-footer-pagination">
                    <div class="nm-pagination-bar">' . $start . '-' . $end . ' of ' . $total . ' ' . $prevlink . $nextlink . '</div>';
            if (hasPermission('edit_servers')) {
                echo '<button class="nm-button" id="create-server" modal="create-server"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['SERVERS_ADD_SERVER'] . ' </button>';
            }
            echo '</div>';

        } else {
            echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
            if (hasPermission('edit_servers')) {
                echo '<button class="nm-button" id="create-server" modal="create-server"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['SERVERS_ADD_SERVER'] . ' </button>';
            }
        }
    } catch (Exception $ex) {
        errorAlert($ex->getMessage());
    }
} else if ($_GET['load'] == 'groups') {
    try {
        $total = $pdo->query('SELECT COUNT(*) FROM nm_server_groups;');
        $total = $total->fetchColumn();
        $limit = 10;
        $pages = ceil($total / $limit);
        $page = $_GET['p'];
        $offset = ($page - 1) * $limit;
        $start = $offset + 1;
        $end = min(($offset + $limit), $total);
        $prevlink = ($page > 1) ? '<a nmservergroups="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
        $nextlink = ($page < $pages) ? '<a nmservergroups="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
        $stmt = $pdo->prepare('SELECT id, groupname, servers FROM nm_server_groups ORDER BY id LIMIT ? OFFSET ?;');
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->bindParam(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $iterator = new IteratorIterator($stmt);

            echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['VAR_ID'] . '</th>
                                <th>Group Name</th>
                                <th>Servers</th>';
            if (hasPermission('edit_permissions')) {
                echo '<th class="text-right" > ' . $lang['VAR_ACTION'] . ' </th >';
            }
            echo '</tr>
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
                                        <span>' . $row['groupname'] . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['servers'] . '</span>
                                    </div>
                                </td>';
                if (hasPermission('edit_permissions')) {
                    echo '<td class="text-right">
                                     <div class="fb-table-cell-wrapper">
                                         <button style="background: transparent; border: none" nmedit-servergroup="' . $row['id'] . '" style="position: relative;right: -24px;"><i class="material-icons" style="color: orange">create</i></button>
                                         <button style="background: transparent; border: none" nmdelete-servergroup="' . $row['id'] . '" style="position: relative;right: -24px;"><i class="material-icons" style="color: red">delete</i></button>
                                     </div>
                                </td>';
                }
                echo '</tr>';
            }
            echo '</tbody>
                        </table>
                    </div>';

            echo '<div class="data-card-footer-pagination">
                            <div class="nm-pagination-bar">' . $start . '-' . $end . ' of ' . $total . ' ' . $prevlink . $nextlink . '</div>';
            if (hasPermission('edit_permissions')) {
                echo '<button class="nm-button" id="create-servergroup" modal="create-servergroup"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> Create ServerGroup </button>';
            }
            echo '</div>';

        } else {
            echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
            if (hasPermission('edit_permissions')) {
                echo '<button class="nm-button" id="create-servergroup" modal="create-servergroup"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> Create ServerGroup </button>';
            }
        }
    } catch (Exception $ex) {
        errorAlert($ex->getMessage());
    }
} else if ($_GET['load'] == 'groupservers') {
    $id = $_GET['id'];
    $total = countServersInGroupById($id);
    $limit = 10;
    $pages = ceil($total / $limit);
    $page = $_GET['p'];
    $offset = ($page - 1) * $limit;
    $start = $offset + 1;
    $end = min(($offset + $limit), $total);
    $prevlink = ($page > 1) ? '<a nmgroupservers="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
    $nextlink = ($page < $pages) ? '<a nmgroupservers="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
    $stmt = $pdo->prepare('SELECT groupname, servers FROM nm_server_groups WHERE id=? LIMIT ? OFFSET ?');
    $stmt->bindParam(1, $id, PDO::PARAM_INT);
    $stmt->bindParam(2, $limit, PDO::PARAM_INT);
    $stmt->bindParam(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $groupname = $result['groupname'];
        $servers = $result['servers'];
        $servers = json_decode($servers);

        echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>Group Name</th>
                                <th>Server</th>';
        if (hasPermission('edit_permissions')) {
            echo '<th class="text-right" > ' . $lang['VAR_ACTION'] . ' </th >';
        }
        echo '</tr>
                            </thead>
                            <tbody>';

        foreach ($servers as $server) {
            echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $groupname . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . getServerNameById($server) . '</span>
                                    </div>
                                </td>';
            if (hasPermission('edit_permissions')) {
                echo '<td class="text-right">
                                     <div class="fb-table-cell-wrapper">
                                            <button style="background: transparent; border: none" nmdelete-groupserver="' . $server . '" servergroup="' . $id . '" style="position: relative;right: -24px;"><i class="material-icons" style="color: red">delete</i></button>
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
                            <a class="nm-button nm-raised" nmbackservergroups="" style="position: relative;top: 2px;left: 55px; margin-left: -48px !important;" type="submit">Back</a>';
        if (hasPermission('edit_permissions')) {
            echo '<button style="right: -40px;" class="nm-button" nmaddgroupserver="' . $id . '"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> Add Server </button>';
        }
        echo '</div>';

    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>
        <a class="nm-button nm-raised" nmbackservergroups="" style="position: relative;top: 2px;left: 55px; margin-left: -48px !important;" type="submit">Back</a>';
        if (hasPermission('edit_permissions')) {
            echo '<button style="right: -40px;" class="nm-button" nmaddgroupserver="' . $id . '"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> Add Server </button>';
        }
    }
} else if ($_GET['load'] == 'add_server') {
    handlePermission('edit_servers');
    $servername = filter_input(INPUT_GET, 'servername', FILTER_SANITIZE_STRING);
    $displayname = filter_input(INPUT_GET, 'displayname', FILTER_SANITIZE_STRING);
    if ($displayname == '') {
        $displayname = $servername;
    }
    $ip = filter_input(INPUT_GET, 'ip', FILTER_SANITIZE_STRING);
    $port = filter_input(INPUT_GET, 'port', FILTER_SANITIZE_STRING);
    $motd = $_GET['motd'] == '' ? '' : html_entity_decode($purifier->purify($_GET['motd']));
    $allowedversions = $_GET['allowed_versions'] == '' ? null : filter_input(INPUT_GET, 'allowed_versions', FILTER_SANITIZE_STRING);
    $restricted = (isset($_GET['restricted']) && $_GET['restricted'] == 'on' ? true : false);

    try {
        $stmnt = $pdo->prepare('SELECT `id` FROM nm_servers WHERE `servername`=?;');
        $stmnt->bindParam(1, $servername, PDO::PARAM_STR);
        $stmnt->execute();
        if ($stmnt->rowCount() == 0) {
            $stmnt = $pdo->prepare('INSERT INTO nm_servers(servername, displayname, ip, port, motd, allowed_versions, restricted) VALUES (?, ?, ?, ?, ?, ?, ?);');
            $stmnt->bindParam(1, $servername, PDO::PARAM_STR);
            $stmnt->bindParam(2, $displayname, PDO::PARAM_STR);
            $stmnt->bindParam(3, $ip, PDO::PARAM_STR);
            $stmnt->bindParam(4, $port, PDO::PARAM_STR);
            $stmnt->bindParam(5, $motd, PDO::PARAM_STR);
            $stmnt->bindParam(6, $allowedversions, PDO::PARAM_STR);
            $stmnt->bindParam(7, $restricted, PDO::PARAM_BOOL);
            $stmnt->execute();

            die('true');
        } else {
            die('false');
        }
    } catch (PDOException $ex) {
        errorAlert($ex->getMessage());
    }
} else if ($_GET['load'] == 'delete_server') {
    handlePermission('edit_servers');
    $id = $_GET['id'];

    try {
        $stmnt = $pdo->prepare('DELETE FROM nm_servers WHERE `id`=?;');
        $stmnt->bindParam(1, $id, PDO::PARAM_INT);
        $result = $stmnt->execute();
        die($result);
    } catch (PDOException $ex) {
        errorAlert($ex->getMessage());
    }
} else if ($_GET['load'] == 'load_server') {
    handlePermission('edit_servers');
    $id = $_GET['id'];
    echo '<div id="nmedit-server" class="modal" server="' . $id . '">
            <div class="modal-content" style="width: 600px !important;">
            <h3 style="font-size: 15px;font-weight: 200">Edit server with ID:' . $id . '</h3>
                <form id="nmedit-server-form" server="' . $id . '">';

    foreach ($pdo->query('SHOW COLUMNS FROM nm_servers;') as $item) {
        if ($item['Field'] == 'id' || $item['Field'] == 'restricted' || $item['Field'] == 'online') {
            continue;
        }
        $stmnt = $pdo->prepare('SELECT ' . $item['Field'] . ' FROM nm_servers WHERE id=?;');
        $stmnt->bindParam(1, $id, PDO::PARAM_INT);
        $stmnt->execute();
        $row = $stmnt->fetch();
        if ($item['Field'] == 'motd') {
            echo '<div class="nm-input-container">
                            <label for="' . $item['Field'] . '">' . $item['Field'] . '</label>
                            <textarea id="' . $item['Field'] . '" name="' . $item['Field'] . '" rows="2">' . $row[$item['Field']] . '</textarea>
                            </div>';
        } else {
            echo '<div class="nm-input-container">
                            <label for="' . $item['Field'] . '">' . $item['Field'] . '</label>
                            <input type="text" id="' . $item['Field'] . '" name="' . $item['Field'] . '" value="' . $row[$item['Field']] . '">
                            </div>';
        }
    }
    echo '          <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="nmedit-server">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="nmedit-server">Save</button>
                    </div>
                </form>
            </div>
        </div>';
} else if ($_GET['load'] == 'edit_server') {
    handlePermission('edit_servers');
    $id = $_GET['id'];
    $servername = filter_input(INPUT_GET, 'servername', FILTER_SANITIZE_STRING);
    $displayname = filter_input(INPUT_GET, 'displayname', FILTER_SANITIZE_STRING);
    if ($displayname == '') {
        $displayname = $servername;
    }
    $ip = filter_input(INPUT_GET, 'ip', FILTER_SANITIZE_STRING);
    $port = filter_input(INPUT_GET, 'port', FILTER_SANITIZE_STRING);
    $motd = html_entity_decode($purifier->purify($_GET['motd']));
    $allowedversions = $_GET['allowed_versions'] == '' ? null : filter_input(INPUT_GET, 'allowed_versions', FILTER_SANITIZE_STRING);

    try {
        $stmnt = $pdo->prepare('UPDATE nm_servers SET servername=?, displayname=?, ip=?, port=?, motd=?, allowed_versions=? WHERE id=?;');
        $stmnt->bindParam(1, $servername, PDO::PARAM_STR);
        $stmnt->bindParam(2, $displayname, PDO::PARAM_STR);
        $stmnt->bindParam(3, $ip, PDO::PARAM_STR);
        $stmnt->bindParam(4, $port, PDO::PARAM_STR);
        $stmnt->bindParam(5, $motd, PDO::PARAM_STR);
        $stmnt->bindParam(6, $allowedversions, PDO::PARAM_STR);
        $stmnt->bindParam(7, $id, PDO::PARAM_INT);
        $result = $stmnt->execute();
        die($result);
    } catch (PDOException $ex) {
        errorAlert($ex->getMessage());
    }
} else if ($_GET['load'] == 'setrestricted') {
    handlePermission('edit_servers');
    $id = $_GET['id'];
    $value = $_GET['value'];

    try {
        $stmnt = $pdo->prepare('UPDATE nm_servers SET restricted=? WHERE id=?');
        $stmnt->bindParam(1, $value, PDO::PARAM_STR);
        $stmnt->bindParam(2, $id, PDO::PARAM_INT);
        $result = $stmnt->execute();
        die($result);
    } catch (PDOException $ex) {
        errorAlert($ex->getMessage());
    }
} else if ($_GET['load'] == 'create_group') {
    handlePermission('edit_servers');
    $name = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING);
    $serverIds = filter_input(INPUT_GET, 'serverIds', FILTER_SANITIZE_STRING);

    try {
        $stmnt = $pdo->prepare('SELECT id FROM nm_server_groups WHERE groupname=?');
        $stmnt->bindParam(1, $servername, PDO::PARAM_STR);
        $stmnt->execute();
        if ($stmnt->rowCount() == 0) {
            $stmnt = $pdo->prepare('INSERT INTO nm_server_groups(groupname, servers) VALUES (?, ?);');
            $stmnt->bindParam(1, $name, PDO::PARAM_STR);
            $stmnt->bindValue(2, '[' . $serverIds . ']', PDO::PARAM_STR);
            $result = $stmnt->execute();
            die($result);
        }
        die('false');
    } catch (PDOException $ex) {
        errorAlert($ex->getMessage());
    }
} else if ($_GET['load'] == 'add_groupserver') {
    handlePermission('edit_servers');
    $groupid = filter_input(INPUT_GET, 'groupid', FILTER_SANITIZE_STRING);
    $server = filter_input(INPUT_GET, 'server', FILTER_SANITIZE_STRING);

    $serverid = getServerIdByName($server);

    echo $serverid . "\n";
    $servers = getServersFromGroupById($groupid);
    $servers = empty($servers) ? '[]' : $servers;
    echo $servers . "\n";
    $servers = json_decode($servers);

    $index = in_array($serverid, $servers);
    echo $index . "\n";
    if (!$index) {
        echo array_push($servers, $serverid) . "\n";
    }

    $servers = json_encode(array_values($servers));
    echo $servers . "\n";

    try {
        $stmnt = $pdo->prepare('UPDATE nm_server_groups SET servers=? WHERE id=?');
        $stmnt->bindParam(1, $servers, 2);
        $stmnt->bindParam(2, $groupid, 1);
        $result = $stmnt->execute();
        die($result);
    } catch (PDOException $ex) {
        errorAlert($ex->getMessage());
    }
} else if ($_GET['load'] == 'delete_servergroup') {
    handlePermission('edit_servers');
    $id = $_GET['id'];
    $stmnt = $pdo->prepare('DELETE FROM nm_server_groups WHERE id=?');
    $stmnt->bindParam(1, $id, 1);
    $result = $stmnt->execute();
    die($result);
} else if ($_GET['load'] == 'delete_server_from_group') {
    handlePermission('edit_servers');
    $groupid = $_GET['groupid'];
    $serverid = $_GET['serverid'];

    $servers = getServersFromGroupById($groupid);
    $servers = empty($servers) ? '[]' : $servers;
    $servers = json_decode($servers);

    $index = array_search($serverid, $servers);
    if ($index !== FALSE) {
        unset($servers[$index]);
    }

    $servers = json_encode(array_values($servers));
    echo $servers;

    try {
        $stmnt = $pdo->prepare('UPDATE nm_server_groups SET servers=? WHERE id=?');
        $stmnt->bindParam(1, $servers, 2);
        $stmnt->bindParam(2, $groupid, 1);
        $result = $stmnt->execute();
        die($result);
    } catch (PDOException $ex) {
        die($ex->getMessage());
    }
} else if ($_GET['load'] == 'load_add_server_to_group') {
    $groupid = $_GET['id'];
    echo '<div id="add-server-group" class="modal" group="' . $groupid . '">
            <div class="modal-content" style="width: 600px !important;">
            <h3 style="font-size: 15px;font-weight: 200">Add prefix to group</h3>
                <form id="add-server-group-form" group="' . $groupid . '">                   
                    <div class="nm-input-container">
                        <label>Server Name/Id</label>
                        <input type="text" name="server" required>
                    </div>';

    echo '<div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="add-server-group">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="add-server-group">Add</button>
                    </div>
                </form>
            </div>
        </div>';
    die();
}

function getServerNameById($id)
{
    global $pdo;
    $stmnt = $pdo->prepare('SELECT servername FROM nm_servers WHERE id=?');
    $stmnt->bindParam(1, $id, PDO::PARAM_INT);
    $stmnt->execute();
    return $stmnt->fetch()['servername'];
}

function getServerIdByName($name)
{
    global $pdo;
    if (is_numeric($name)) {
        return (int)$name;
    }
    $stmnt = $pdo->prepare('SELECT id FROM nm_servers WHERE servername=?;');
    $stmnt->bindParam(1, $name, PDO::PARAM_STR);
    $stmnt->execute();
    return (int)$stmnt->fetch()['id'];
}

function getServersFromGroupById($id)
{
    global $pdo;
    $stmnt = $pdo->prepare('SELECT servers FROM nm_server_groups WHERE id=?');
    $stmnt->bindParam(1, $id, PDO::PARAM_INT);
    $stmnt->execute();
    return $stmnt->fetch()['servers'];
}

function countServersInGroupById($id)
{
    $servers = getServersFromGroupById($id);
    $servers = str_replace('[', '', $servers);
    $servers = str_replace(']', '', $servers);
    return count(explode(',', $servers));
}

function hasSwitchPermission()
{
    if (!hasPermission('edit_servers')) {
        return 'disabled readonly';
    }
}