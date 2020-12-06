<?php
include '../permissions.php';
handlePermission('edit_punishments');
$type = $_GET['type'];

if ($type == 'delete_punishment') {
    $id = $_GET['id'];
    $stmnt = $pdo->prepare('DELETE FROM nm_punishments WHERE `id`=?;');
    $stmnt->bindParam(1, $id, 2);
    $stmnt->execute();
    echo 'done delete-punishment';
} else if ($type == 'undo_punishment') {
    $id = $_GET['id'];
    $stmnt = $pdo->prepare('UPDATE nm_punishments SET `active`=0 WHERE `id`=?;');
    $stmnt->bindParam(1, $id, 2);
    $stmnt->execute();
    echo 'done unban-punishment';
} else if ($type == 'create_punishment') {
    $punishmenttype = $_GET['ptype'];
    $uuid = getUuid($_GET['username']);
    if ($uuid == null) {
        die('falseThat player is not known in our database!');
    }
    $punisher = getUuid($_SESSION['user']->getUsername());
    if ($punisher == null) {
        die('falseYour username is not an mc username!');
    }
    if ($uuid == $punisher) {
        die('same uuid');
    }
    $time = round(microtime(true) * 1000);
    echo $_GET['ends'] . "\n";
    echo strtotime($_GET['ends']) . "\n";
    $ends = $_GET['ends'] != "" ? strtotime($_GET['ends']) * 1000 : -1;
    $ip = getIP($uuid);
    $server = $_GET['server'] != "" ? filter_input(INPUT_GET, 'server', FILTER_SANITIZE_STRING) : null;
    $reason = html_entity_decode($purifier->purify($_GET['reason']));
    $silent = $_GET['silent'] == 'on' ? true : false;

    echo $punishmenttype . ', ' . $uuid . ', ' . $punisher . ', ' . $time . ', ' . $ends . ', ' . $ip . ', ' . $server . ', ' . $silent . ', ' . $reason . "\n";
    try {
        $stmnt = $pdo->prepare('INSERT nm_punishments (type, uuid, punisher, time, end, reason, ip, server, unbanner, silent, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmnt->bindParam(1, $punishmenttype, 1);
        $stmnt->bindParam(2, $uuid, 2);
        $stmnt->bindParam(3, $punisher, 2);
        $stmnt->bindParam(4, $time, 1);
        $stmnt->bindParam(5, $ends, 1);
        $stmnt->bindParam(6, $reason, 2);
        $stmnt->bindParam(7, $ip, 2);
        $stmnt->bindParam(8, $server, 2);
        $stmnt->bindValue(9, null, 2);
        $stmnt->bindValue(10, $silent, PDO::PARAM_BOOL);
        $stmnt->bindValue(11, true, PDO::PARAM_BOOL);
        $stmnt->execute();
        die('true');
    } catch (PDOException $ex) {
        die('false' . $ex->getTraceAsString());
    }
} else if ($type == 'create_pre_punishment') {
    $name = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING);
    $punishmenttype = $_GET['ptype'];
    $duration = $_GET['duration'] == "" ? -1 : $_GET['duration'];
    if (!is_numeric($duration)) {
        die('invalid duration');
    }
    $server = $_GET['server'] != "" ? filter_input(INPUT_GET, 'server', FILTER_SANITIZE_STRING) : null;
    $reason = html_entity_decode($purifier->purify($_GET['reason']));

    try {
        $stmnt = $pdo->prepare('INSERT nm_pre_punishments (name, type, duration, server, reason) VALUES (?, ?, ?, ?, ?)');
        $stmnt->bindParam(1, $name, PDO::PARAM_INT);
        $stmnt->bindParam(2, $punishmenttype, PDO::PARAM_INT);
        $stmnt->bindParam(3, $duration, PDO::PARAM_INT);
        $stmnt->bindParam(4, $server, PDO::PARAM_STR);
        $stmnt->bindParam(5, $reason, PDO::PARAM_STR);
        $stmnt->execute();
        die('true');
    } catch (PDOException $ex) {
        die('false' . $ex->getTraceAsString());
    }
} else if ($type == 'delete_pre_punishment') {
    $id = $_GET['id'];

    try {
        $stmnt = $pdo->prepare('DELETE FROM nm_pre_punishments WHERE id=?;');
        $stmnt->bindParam(1, $id, 1);
        $stmnt->execute();
        die('true');
    } catch (PDOException $ex) {
        die('false' . $ex->getTraceAsString());
    }

} else if ($type == 'edit_pre_punishment') {
    //handlePermission('edit_servers');
    $id = $_GET['id'];
    $name = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING);
    $ptype = filter_input(INPUT_GET, 'ptype', FILTER_SANITIZE_STRING);
    $duration = filter_input(INPUT_GET, 'duration', FILTER_SANITIZE_STRING);
    $server = filter_input(INPUT_GET, 'server', FILTER_SANITIZE_STRING);
    $reason = html_entity_decode($purifier->purify($_GET['reason']));
    echo $id;

    try {
        $stmnt = $pdo->prepare('UPDATE nm_pre_punishments SET `name`=?, `type`=?, `duration`=?, `server`=?, `reason`=? WHERE `id`=?;');
        $stmnt->bindParam(1, $name, PDO::PARAM_STR);
        $stmnt->bindParam(2, $ptype, PDO::PARAM_INT);
        $stmnt->bindParam(3, $duration, PDO::PARAM_STR);
        $stmnt->bindParam(4, $server, PDO::PARAM_STR);
        $stmnt->bindParam(5, $reason, PDO::PARAM_STR);
        $stmnt->bindParam(6, $id, PDO::PARAM_INT);
        $result = $stmnt->execute();
        die($result);
    } catch (PDOException $ex) {
        errorAlert($ex->getMessage());
    }
} else if ($type == 'load_pre_punishment') {
    $id = $_GET['id'];
    echo '<div id="nmedit-pre-punishment" class="modal" pre-punishment="' . $id . '">
            <div class="modal-content" style="width: 600px !important;">
            <h3 style="font-size: 15px;font-weight: 200">Edit pre punishment with ID:' . $id . '</h3>
                <form id="nmedit-pre-punishment-form" pre-punishment="' . $id . '">';

    foreach ($pdo->query('SHOW COLUMNS FROM nm_pre_punishments;') as $item) {
        if ($item['Field'] == 'id') {
            continue;
        }
        $stmnt = $pdo->prepare('SELECT ' . $item['Field'] . ' FROM nm_pre_punishments WHERE id=?;');
        $stmnt->bindParam(1, $id, PDO::PARAM_INT);
        $stmnt->execute();
        $row = $stmnt->fetch();
        $field = $item['Field'];
        if ($field == 'type') {
            $field = 'ptype';
        }
        echo '<div class="nm-input-container">
                            <label for="' . $item['Field'] . '">' . $item['Field'] . '</label>
                            <input type="text" id="' . $field . '" name="' . $field . '" value="' . $row[$item['Field']] . '">
                            </div>';
    }
    echo '          <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="nmedit-pre-punishment">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="nmedit-pre-punishment">Save</button>
                    </div>
                </form>
            </div>
        </div>';
} else if ($type == 'createPunishmentByReport') {
    $id = $_GET['id'];
    $uuid = getUUIDByPunishmentId($id);
    echo '
<div id="createPunishmentByReport" class="modal">
    <div class="modal-content" style="width: 600px !important;">
        <form id="createPunishmentByReport-form">
            <div class="nm-input-container">
                <label>Punishment Type *</label>
                <label>
                    <select name="ptype" id="punishmentByReport-type" required>
                        <option value="1">Ban</option>
                        <option value="2">Global Ban</option>
                        <option value="3">Temporary Ban</option>
                        <option value="4">Global Temporary Ban</option>
                        <option value="5">IP Ban</option>
                        <option value="6">Global IP Ban</option>
                        <option value="9">Mute</option>
                        <option value="10">Global Mute</option>
                        <option value="11">Temporary Mute</option>
                        <option value="12">Global Temporary Mute</option>
                        <option value="13">IP Mute</option>
                        <option value="14">Global IP Mute</option>
                        <option value="17">Kick</option>
                        <option value="18">Global Kick</option>
                        <option value="19">Warning</option>
                    </select>
                </label>
            </div>
            <div class="nm-input-container">
                <label>Username *</label>
                <label>
                    <input type="text" name="username" value="' . $uuid . '" required>
                </label>
            </div>
            <div class="nm-input-container" id="server">
                <label>Server *</label>
                <label>
                    <input type="text" name="server" id="serverinput" required>
                </label>
            </div>
            <div class="nm-input-container" id="expiredate">
                <label>Expiration</label>
                <label>
                    <input type="datetime-local" name="ends">
                </label>
            </div>
            <div class="nm-input-container">
                <label>Reason</label>
                <label>
                    <input type="text" name="reason" required>
                </label>
            </div>
            <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                <button class="nm-button" type="button" style="position: relative;right: -24px;"
                        close="createPunishmentByReport">Cancel
                </button>
                <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit"
                        close="createPunishmentByReport">Create
                </button>
            </div>
        </form>
    </div>
</div>';
}

function getUUIDByPunishmentId($id)
{
    global $pdo;
    $stmnt = $pdo->prepare('SELECT `uuid` FROM nm_punishments WHERE `id`=?;');
    $stmnt->bindParam(1, $id, PDO::PARAM_INT);
    $stmnt->execute();
    $row = $stmnt->fetch();
    return $row['uuid'];
}

function getIP($uuid)
{
    global $pdo;
    $statement = $pdo->prepare('SELECT `ip` FROM nm_players WHERE `uuid`=?;');
    $statement->bindParam(1, $uuid, PDO::PARAM_STR);
    $statement->execute();
    $row = $statement->fetch();
    return $row['ip'];
}