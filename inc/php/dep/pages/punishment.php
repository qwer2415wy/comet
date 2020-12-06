<?php
include_once '../permissions.php';
include '../MinecraftColorcodes.php';
handlePermission('view_punishments');
$colorCodes = new MinecraftColorcodes();
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

$id = $_GET['id'];

if (!isset($_GET['load'])) {

    $stmt = $pdo->prepare('SELECT id, type, uuid, punisher, time, end, server, reason, silent, active, unbanner FROM nm_punishments where id=?');
    $stmt->bindParam(1, $id, 1);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {

        $row = $stmt->fetchAll()[0];

        echo '<div class="data-card-body">';
        echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-50">
                                <p>' . $lang['PUNISHMENTS_ID'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-50 text-right">
                                <p>' . $row['id'] . '</p>
                            </div>
                        </div>';
        echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-50">
                                <p>' . $lang['PUNISHMENTS_TYPE'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-50 text-right">
                                <p>' . ucwords(getPunishmentType($row['type'])) . '</p>
                            </div>
                        </div>';
        echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-30">
                                <p>' . $lang['PUNISHMENTS_PUNISHED'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-70 text-right">
                                <p><a player="' . $row['uuid'] . '"><img src="https://crafatar.com/avatars/' . $row['uuid'] . '"> ' . getName($row['uuid']) . '</a></p>
                            </div>
                        </div>';
        echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-30">
                                <p>' . $lang['PUNISHMENTS_PUNISHER'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-70 text-right">
                                <p><a player="' . $row['punisher'] . '"><img src="https://crafatar.com/avatars/' . $row['punisher'] . '"> ' . getName($row['punisher']) . '</a></p>
                            </div>
                        </div>';
        echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-50">
                                <p>' . $lang['PUNISHMENTS_TIME'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-50 text-right">
                                <p>' . date($dateformat, $row['time'] / 1000) . '</p>
                            </div>
                        </div>';
        if (!($row['type'] == 19 || $row['type'] == 18 || $row['type'] == 17)) {
            echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-30">
                                <p>' . $lang['PUNISHMENTS_END'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-70 text-right">
                                <p>' . ($row['end'] == '-1' || $row['end'] == '0' ? '-' : date($dateformat, $row['end'] / 1000)) . '</p>
                            </div>
                        </div>';
        }
        if ($row['server'] != '') {
            echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-50">
                                <p>' . $lang['VAR_SERVER'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-50 text-right">
                                <p>' . $row['server'] . '</p>
                            </div>
                        </div>';
        }

        echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-50">
                                <p>' . $lang['VAR_REASON'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-50 text-right">
                                <p>' . $colorCodes->convert($row['reason']) . '</p>
                            </div>
                        </div>';

        if (!($row['type'] == 20 || $row['type'] == 21)) {
            echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-50">
                                <p>' . $lang['PUNISHMENTS_SILENT'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-50 text-right">
                                <p>' . ($row['silent'] == 1 ? $lang['VAR_TRUE'] : $lang['VAR_FALSE']) . '</p>
                            </div>
                        </div>';
        }
        if (!($row['type'] == 19 || $row['type'] == 18 || $row['type'] == 17)) {
            echo($row['active'] == '1' ? ('<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-50">
                                <p>' . $lang['PUNISHMENTS_STATUS'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-50 text-right">
                                <p>' . $lang['VAR_ACTIVE'] . '</p>
                            </div>
                        </div>
                        ') : ('<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-50">
                                <p>' . $lang['PUNISHMENTS_STATUS'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-50 text-right">
                                <p>' . $lang['PUNISHMENTS_EXPIRED'] . '</p>
                            </div>
                        </div>'));
        }
        if ($row['unbanner'] != '') {
            echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-50">
                                <p>' . $row['type'] == 19 ? $lang['REPORTS_CLOSED_BY'] : $lang['PUNISHMENTS_UNBANNER'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-50 text-right">
                                <p><a href="#' . $row['unbanner'] . '" player="' . $row['punisher'] . '"><img src="https://crafatar.com/avatars/' . $row['unbanner'] . '"> ' . getName($row['unbanner']) . '</a></p>
                            </div>
                        </div>';
        }

        echo '</div>';

        echo '<div class="data-card-footer-pagination" style="width: 100% !important;">';

        echo '<button class="nm-button nm-raised nm-raised-delete" style="float: right;position: relative;top: 2px; margin-left: -3px !important;" delete-punishment="' . $row['id'] . '"type="submit" close="create-account">' . $lang['VAR_DELETE'] . '</button>';
        echo '<button class="nm-button nm-raised nm-raised-edit" style="float: right;position: relative; top: 2px; margin-left: -3px !important;" edit-punishment="' . $row['id'] . '" ptype="' . $row['type'] . '">' . $lang['VAR_EDIT'] . '</button>';
        if ($row['type'] != '20') {
            echo '<a class="nm-button nm-raised" nmbackpunishments="" style="position: relative;top: 2px;left: 55px; margin-left: -48px !important;" type="submit">' . $lang['VAR_BACK'] . '</a>';
        } else {
            echo '<a class="nm-button nm-raised" nmbackreports="" style="position: relative;top: 2px;left: 55px; margin-left: -48px !important;" type="submit">' . $lang['VAR_BACK'] . '</a>';
        }
        if (($row['type'] == 1 || $row['type'] == 2 || $row['type'] == 3 || $row['type'] == 4 || $row['type'] == 5 || $row['type'] == 6 || $row['type'] == 7 || $row['type'] == 8) && hasPermission("edit_punishments")) {
            if ($row['active'] == '1') {
                echo '<button class="nm-button nm-raised" undo-punishment="' . $row['id'] . '" style="position: relative;top: 2px;left: 55px; margin-left: -2px !important;" type="submit" close="create-account">' . $lang['VAR_UNBAN'] . '</button>';
            }
        } else if (($row['type'] == 9 || $row['type'] == 10 || $row['type'] == 11 || $row['type'] == 12 || $row['type'] == 13 || $row['type'] == 14 || $row['type'] == 15 || $row['type'] == 16) && hasPermission("edit_punishments")) {
            if ($row['active'] == '1') {
                echo '<button class="nm-button nm-raised" undo-punishment="' . $row['id'] . '" style="position: relative;top: 2px;left: 55px; margin-left: -2px !important;" type="submit" close="create-account">' . $lang['VAR_UNMUTE'] . '</button>';
            }
        } else if (($row['type'] == '17' || $row['type'] == '18') && hasPermission("edit_punishments")) {
        } else if ($row['type'] == '19' && hasPermission("edit_punishments")) {
            if ($row['active'] == '1') {
                echo '<button class="nm-button nm-raised" undo-punishment="' . $row['id'] . '" style="position: relative;top: 2px;left: 55px; margin-left: -2px !important;" type="submit" close="create-account">' . $lang['VAR_UNWARN'] . '</button>';
            }
        } else if ($row['type'] == '20' && hasPermission("edit_reports")) {
            if ($row['active'] == '1') {
                echo '<button class="nm-button nm-raised-delete" undo-punishment="' . $row['id'] . '" style="float: right;position: relative;top: 2px; margin-left: -3px !important;" type="submit" close="create-account">' . $lang['REPORTS_CLOSE'] . '</button>';
                if (isModuleEnabled('module_punishments')) {
                    echo '<button class="nm-button nm-raised" punishByReport="' . $row['id'] . '" style="float: right;position: relative; top: 2px; margin-left: -3px !important;" type="submit" close="create-account">PUNISH</button>';
                }
            }
        }
        echo '</div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
    }
} else if ($_GET['load'] == 'load_edit_punishment') {
    echo '<div id="nmedit-punishment" class="modal" punishmentid="' . $id . '">
            <div class="modal-content" style="width: 600px !important;">
            <h3 style="font-size: 15px;font-weight: 200">Edit Punishment ID: ' . $id . '</h3>
                <form id="nmedit-punishment-form" punishmentid="' . $id . '">';


    foreach ($pdo->query("SHOW COLUMNS FROM `nm_punishments`") as $item) {
        if ($item['Field'] == 'id' || $item['Field'] == 'ip') {
            continue;
        }
        $stmnt = $pdo->prepare('SELECT ' . $item['Field'] . ' FROM nm_punishments WHERE id=?');
        $stmnt->bindParam(1, $id, 1);
        $stmnt->execute();
        $row = $stmnt->fetch();
        if ($item['Field'] == 'type') {
            echo '<div class="nm-input-container">
                            <label for="' . $item['Field'] . '">' . ucfirst($item['Field']) . '</label>
                            <select id="edit_punishment_' . $item['Field'] . '" name="' . $item['Field'] . '" required>

                            </select>
                            </div>';
        } else if ($item['Field'] == 'time' || $item['Field'] == 'end') {
            echo '<div class="nm-input-container" id="root_' . $item['Field'] .'">
                            <label for="' . $item['Field'] . '">' . ucfirst($item['Field']) . '</label>
                            <input type="datetime-local" id="' . $item['Field'] . '" name="' . $item['Field'] . '" value="' . date('Y-m-d\TH:i', $row[$item['Field']] / 1000) . '">
                            </div>';
        } else if ($item['Field'] == 'silent' || $item['Field'] == 'active') {
            echo '<div class="nm-input-container" style="display: grid; position: relative; top: 0;padding: 0 !important;">
                            <label for="' . $item['Field'] . '">' . ucfirst($item['Field']) . '</label>
                            <input type="checkbox" id="' . $item['Field'] . '" name="' . $item['Field'] . '" class="switch-input" ' . isChecked($row[$item['Field']]) . '>
                            <label for="' . $item['Field'] . '" class="switch-label" style="margin-left: 0 !important;"><span style="position: relative !important;" class="toggle--on">' . $lang['VAR_TRUE'] . '</span><span style="position: relative !important;" class="toggle--off">' . $lang['VAR_FALSE'] . '</span></label>
                            </div>';
        } else {
            echo '<div class="nm-input-container" id="root_' . $item['Field'] .'">
                            <label for="' . $item['Field'] . '">' . ucfirst($item['Field']) . '</label>
                            <input type="text" id="' . $item['Field'] . '" name="' . $item['Field'] . '" value="' . $row[$item['Field']] . '">
                            </div>';
        }
    }
    echo '          <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="nmedit-punishment">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="nmedit-punishment">Save</button>
                    </div>
                </form>
            </div>
        </div>';
    die();
} else if ($_GET['load'] == 'edit_punishment') {
    $id = $_GET['id'];
    $type = $_GET['type'];
    $player = filter_input(INPUT_GET, 'uuid', FILTER_SANITIZE_STRING);
    $punisher = filter_input(INPUT_GET, 'punisher', FILTER_SANITIZE_STRING);
    $time = $_GET['time'] != "" ? strtotime($_GET['time']) * 1000 : -1;
    $end = $_GET['end'] != "" ? strtotime($_GET['end']) * 1000 : -1;
    $server = $_GET['server'] != "" ? filter_input(INPUT_GET, 'server', FILTER_SANITIZE_STRING) : null;
    $reason = html_entity_decode($purifier->purify($_GET['reason']));
    $unbanner = filter_input(INPUT_GET, 'unbanner', FILTER_SANITIZE_STRING);
    $active = $_GET['active'] == 'on' ? true : false;

    if ($player == null) {
        die('false');
    }
    if ($punisher == null) {
        die('false');
    }

    $player = getUuid($player);

    $punisher = getUuid($punisher);

    $unbanner = getUuid($unbanner);

    if ($uuid == $punisher) {
        die('same uuid');
    }

    try {
        $stmnt = $pdo->prepare('UPDATE nm_punishments SET type=?, uuid=?, punisher=?, time=?, end=?, reason=?, server=?, unbanner=?, active=? WHERE id=?;');
        $stmnt->bindParam(1, $type, 1);
        $stmnt->bindParam(2, $player, 2);
        $stmnt->bindParam(3, $punisher, 2);
        $stmnt->bindParam(4, $time, 1);
        $stmnt->bindParam(5, $end, 1);
        $stmnt->bindParam(6, $reason, 2);
        $stmnt->bindParam(7, $server, 2);
        $stmnt->bindParam(8, $unbanner, 2);
        $stmnt->bindParam(9, $active, PDO::PARAM_BOOL);
        $stmnt->bindParam(10, $id, 1);
        $stmnt->execute();
        die('true');
    } catch (PDOException $ex) {
        die($ex->getTraceAsString());
    }
}

function getPunishmentType($type)
{
    switch ($type) {
        case '1':
            return 'Ban';
        case '2':
            return 'Global Ban';
        case '3':
            return 'Temporary Ban';
        case '4':
            return 'Global Temporary Ban';
        case '5':
            return 'IP Ban';
        case '6':
            return 'Global IP Ban';
        case '7':
            return 'Temporary IP Ban';
        case '8':
            return 'Global Temporary IP Ban';
        case '9':
            return 'Mute';
        case '10':
            return 'Global Mute';
        case '11':
            return 'Temporary Mute';
        case '12':
            return 'Global Temporary Mute';
        case '13':
            return 'IP Mute';
        case '14':
            return 'Global IP Mute';
        case '15':
            return 'Temporary IP Mute';
        case '16':
            return 'Global Temporary IP Mute';
        case '17':
            return 'Kick';
        case '18':
            return 'Global Kick';
        case '19':
            return 'Warn';
        case '20':
            return 'Report';
        case '21':
            return 'Note';
        default:
            return 'Unknown';
    }
}