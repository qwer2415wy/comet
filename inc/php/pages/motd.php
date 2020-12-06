<?php
include '../dep/permissions.php';
$defaultlang = 'English';
foreach ($webSettings as $variable => $value) {
    if ($variable == 'default-language') {
        $defaultlang = $value;
    }
}
handlePermission('view_network');
if (isset($_COOKIE['language'])) {
    $language = $_COOKIE['language'];
    $langdir = "../dep/languages/";
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
    include '../dep/languages/' . $defaultlang . '.php';
}

if (isset($_GET['load'])) {
    switch ($_GET['load']) {
        case 'load_motd':
            $id = $_GET['id'];

            echo loadMotd($id);
            return;
        case 'create_motd':

            echo createMotd();
            return;
        case 'delete_motd':
            $id = $_GET['id'];

            deleteMotd($id);
            return;
        case 'set_motd_text':
            $id = $_GET['id'];
            $text = $_GET['text'];

            setMotdText($id, $text);
            return;
        case 'set_motd_description':
            $id = $_GET['id'];
            $description = $_GET['description'];

            setMotdDescription($id, $description);
            return;
        case 'set_motd_customversion':
            $id = $_GET['id'];
            $customversion = $_GET['customversion'];

            echo setMotdCustomVersion($id, $customversion);
            return;
        case 'set_motd_faviconurl':
            $id = $_GET['id'];
            $url = $_GET['url'];

            setMotdFaviconUrl($id, $url);
            return;
        case 'reload_motd_dropdown':
            $id = $_GET['id'];
            echo loadMotdSelect($id);
            return;
    }
}

$stmnt = $pdo->query('SELECT value FROM nm_values WHERE variable="maintenance_text" OR variable="maintenance_description" OR variable="maintenance_customversion" ORDER BY variable DESC');
$res = $stmnt->fetchAll();

$customVersion = $res[3]['value'] == '' ? '143/200' : $res[3]['value'];

function loadMotdSelect($id)
{
    global $pdo;

    $sql = "SELECT id FROM nm_motds;";
    $stmnt = $pdo->query($sql);
    $res = $stmnt->fetchAll();
    if ($id == 0) {
        $id = $res[0]['id'];
    }
    $res1 = '<select style="max-width: 75%" id="motdIds" motd="' . $id . '">';
    //$res1 = $res1 . '<option disabled selected value>' . $lang['TEXT_SELECTOPTION'] . '</option>';
    foreach ($res as $row) {
        $select = '';
        if ($row['id'] == $id) {
            $select = 'selected';
        }
        $res1 = $res1 . '<option ' . $select . ' value="' . $row['id'] . '">' . $row['id'] . '</option>';
    }
    $res1 = $res1 . '</select>';
    return $res1;
}

function loadMotd($id)
{
    global $pdo;
    global $lang;

    $sql = "SELECT * FROM nm_motds WHERE id=?";
    if ($id == 0) {
        $sql = "SELECT * FROM nm_motds LIMIT 1;";
    }

    $stmnt = $pdo->prepare($sql);
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();
    $res = $stmnt->fetch();

    $id = $res['id'];

    return '<div class="data-card-row-value" style="padding: 20px">
                            <div class="nm-input-container">
                                <label>' . $lang['VAR_MESSAGE'] . '</label>
                                <textarea placeholder="Message" id="message" mid="' . $id . '" rows="2">' . htmlspecialchars($res['text'], ENT_QUOTES) . '</textarea>
                                <!--<input type="text" placeholder="Message" id="message" mid="' . $id . '"
                                       value="' . htmlspecialchars($res['text'], ENT_QUOTES) . '">-->
                            </div>
                            <div class="nm-input-container">
                                <label>' . $lang['VAR_HOVERMESSAGE'] . '</label>
                                <textarea placeholder="Description" id="description" mid="' . $id . '"
                                       variable="motd_description">' . htmlspecialchars($res['description'], ENT_QUOTES) . '</textarea>
                                <!--<input type="text" placeholder="Description" id="description" mid="' . $id . '"
                                       variable="motd_description" value="' . htmlspecialchars($res['description'], ENT_QUOTES) . '">-->
                            </div>
                            <div class="nm-input-container">
                                <label>Custom Version</label>
                                <input type="text" id="customversion" placeholder="Custom Version" mid="' . $id . '"
                                       value="' . htmlspecialchars($res['customversion'], ENT_QUOTES) . '">
                            </div>
                            <div class="nm-input-container">
                                <label>' . $lang['VAR_ICON'] . '</label>
                                <input type="text" id="icon" placeholder="Icon" mid="' . $id . '"
                                       value="' . htmlspecialchars($res['faviconUrl'], ENT_QUOTES) . '">
                            </div>
                            <div class="nm-input-container">
                                <label>' . $lang['MOTD_PREVIEW'] . '</label>
                                <div class="preview_zone" id="preview_zone">
                                    <div class="server-name">Minecraft Server <span id="ping" class="ping">143/200</div>
                                    <span class="preview_motd" id="motd">Your MOTD should be here</span>
                                </div>
                            </div>
                        </div>
                        <button style="color: grey;" class="nm-button" id="create-motd"><i class="material-icons" style="bottom: -6px;">add</i> ADD MOTD </button>
                        <button class="nm-button nm-raised nm-raised-delete" style="float: right;position: relative;top: 2px; margin-left: -3px !important;" id="delete-motd" mid="' . $id . '" type="submit">DELETE</button>
                        ';
}

function createMotd()
{
    global $pdo;

    $sql = "INSERT INTO nm_motds(text, description, customversion, faviconurl) VALUES(?,?,?,?)";
    $stmnt = $pdo->prepare($sql);
    $stmnt->bindValue(1, "&aThis is a MOTD &4test%newline%&aThis is the second line!", 2);
    $stmnt->bindValue(2, "& 6This server is running &c&lNetworkManager&r%newline%&6Join to take a look :P", 2);
    $stmnt->bindValue(3, "", 2);
    $stmnt->bindValue(4, "https://raw.githubusercontent.com/ChimpGamer/NetworkManager/master/Webbie/logo.png", 2);
    $stmnt->execute();

    return $pdo->lastInsertId();
}

function deleteMotd($id)
{
    global $pdo;

    $sql = "DELETE FROM nm_motds WHERE id=?";
    $stmnt = $pdo->prepare($sql);
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();
}

function setMotdText($id, $text = '')
{
    global $pdo;
    global $purifier;

    $text = $purifier->purify($text);

    $text = str_replace(';v1', ' ', $text);
    $text = str_replace(';v2', '&', $text);
    $text = str_replace(';v3', '%', $text);
    $text = str_replace(';v4', '#', $text);
    $text = str_replace(';v5', '<', $text);
    $text = str_replace(';v6', '>', $text);
    $text = str_replace(';v7', '+', $text);

    $sql = "UPDATE nm_motds SET text=? WHERE id=?;";
    $stmnt = $pdo->prepare($sql);
    $stmnt->bindParam(1, $text, 2);
    $stmnt->bindParam(2, $id, 1);
    $stmnt->execute();
}

function setMotdDescription($id, $description = '')
{
    global $pdo;
    global $purifier;

    $description = $purifier->purify($description);

    $description = str_replace(';v1', ' ', $description);
    $description = str_replace(';v2', '&', $description);
    $description = str_replace(';v3', '%', $description);
    $description = str_replace(';v4', '#', $description);
    $description = str_replace(';v5', '<', $description);
    $description = str_replace(';v6', '>', $description);
    $description = str_replace(';v7', '+', $description);

    $sql = "UPDATE nm_motds SET description=? WHERE id=?;";
    $stmnt = $pdo->prepare($sql);
    $stmnt->bindParam(1, $description, 2);
    $stmnt->bindParam(2, $id, 1);
    $stmnt->execute();
}

function setMotdCustomVersion($id, $customVersion = '')
{
    global $pdo;
    global $purifier;

    $customVersion = $purifier->purify($customVersion);

    $customVersion = str_replace(';v1', ' ', $customVersion);
    $customVersion = str_replace(';v2', '&', $customVersion);
    $customVersion = str_replace(';v3', '%', $customVersion);
    $customVersion = str_replace(';v4', '#', $customVersion);
    $customVersion = str_replace(';v5', '<', $customVersion);
    $customVersion = str_replace(';v7', '+', $customVersion);

    try {
        $sql = "UPDATE nm_motds SET customversion=? WHERE id=?;";
        $stmnt = $pdo->prepare($sql);
        $stmnt->bindParam(1, $customVersion, 2);
        $stmnt->bindParam(2, $id, 1);
        if ($stmnt->execute()) {
            return 'Successfully changed custom version of Motd ' . $id . ' to ' . $customVersion;
        }
    } catch (PDOException $ex) {
        return $ex->getMessage();
    }
}

function setMotdFaviconUrl($id, $url = '')
{
    global $pdo;
    global $purifier;

    $url = $purifier->purify($url);

    $url = str_replace(';v1', ' ', $url);
    $url = str_replace(';v2', '&', $url);
    $url = str_replace(';v3', '%', $url);
    $url = str_replace(';v4', '#', $url);
    $url = str_replace(';v5', '<', $url);
    $url = str_replace(';v7', '+', $url);

    $sql = "UPDATE nm_motds SET faviconUrl=? WHERE id=?;";
    $stmnt = $pdo->prepare($sql);
    $stmnt->bindParam(1, $url, 2);
    $stmnt->bindParam(2, $id, 1);
    $stmnt->execute();
}

?>

<style>
    span:after {
        content: "";
        display: none;
        clear: both;
    }
</style>

<div class="container">
    <div class="content">
        <div class="row">
            <div class="col-6">
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="data-card-header-title">
                            <?php echo $lang['MOTD_MOTDGENERATOR']; ?>
                        </div>
                    </div>
                    <div class="nm-input-container" style="padding-bottom: 0;">
                        <button class="nm-button nm-raised disabled" style="position: relative;">Select ID <i
                                    class="material-icons" style="color: white;bottom: -7px;">keyboard_arrow_right</i>
                        </button>
                        <span id="motd_select_zone"><?php echo loadMotdSelect(0); ?></span>
                    </div>
                    <div class="data-card-body" id="motd_zone">
                        <?php echo loadMotd(0); ?>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="data-card-header-title">
                            <?php echo $lang['MOTD_MAINTENANCEMODE']; ?>
                        </div>
                    </div>
                    <div class="data-card-body">
                        <div class="data-card-row-value" style="padding: 20px">
                            <div class="nm-input-container">
                                <label><?php echo $lang['TITLE_MOTD']; ?></label>
                                <textarea placeholder="MOTD" id="maintenance_message"
                                          variable="maintenance_text" rows="2"><?php echo htmlspecialchars($res[0]['value'], ENT_QUOTES); ?></textarea>
                            </div>
                            <div class="nm-input-container">
                                <label><?php echo $lang['VAR_HOVERMESSAGE']; ?></label>
                                <textarea placeholder="Description" id="maintenance_description"
                                          variable="maintenance_description"><?php echo htmlspecialchars($res[1]['value'], ENT_QUOTES); ?></textarea>
                            </div>
                            <div class="nm-input-container">
                                <label>Custom Version</label>
                                <input type="text" placeholder="Custom Version" id="maintenance_customversion"
                                       variable="maintenance_customversion"
                                       value="<?php echo htmlspecialchars($res[2]['value'], ENT_QUOTES); ?>">
                            </div>
                            <div class="nm-input-container">
                                <label><?php echo $lang['MOTD_PREVIEW']; ?></label>
                                <div class="preview_zone" id="preview_zone_maintenance">
                                    <div class="server-name">Minecraft Server <span id="maintenance_ping" class="ping">143/200
                                    </div>
                                    <span class="preview_motd" id="motd_maintenance">Your MOTD should be here</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    if (typeof isMotdLoaded === 'undefined') {
        $.getScript("../inc/js/pages/colorcodes.js", function (data, textStatus, jqxhr) {
            console.log(textStatus); //success
            console.log(jqxhr.status); //200
            console.log('Load was performed. (colorcodes)');
        });
        $.getScript("../inc/js/pages/motd.js", function (data, textStatus, jqxhr) {
            console.log(textStatus); //success
            console.log(jqxhr.status); //200
            console.log('Load was performed. (motd)');
        });
    }
    $(document).ready(function () {
        setTimeout(function () {
            let yourMOTD = document.getElementById('message').value.replace(new RegExp('%newline%|{newline}', 'g'), '\n');
            let newMOTD = yourMOTD.replaceColorCodes();
            $('#motd').text('').append(newMOTD).html();

            let version = document.getElementById('customversion').value;
            let newVersion = version.length === 0 ? '143/200' : version.replaceColorCodes();
            $('#ping').text('').append(newVersion).html();

            let url = document.getElementById('icon').value;
            if (url.endsWith('.png') || url.endsWith('.jpg')) {
                console.log('proceed please');
                $('#preview_zone').css("background-image", "url(" + url + "), url(../inc/img/motd.png)");
                $('#preview_zone_maintenance').css("background-image", "url(" + url + "), url(../inc/img/motd.png)");
            }

            yourMOTD = document.getElementById('maintenance_message').value.replace(new RegExp('%newline%|{newline}', 'g'), '\n');
            newMOTD = yourMOTD.replaceColorCodes();
            $('#motd_maintenance').text('').append(newMOTD).html();

            version = document.getElementById('maintenance_customversion').value;
            newVersion = version.length === 0 ? '143/200' : version.replaceColorCodes();
            $('#maintenance_ping').text('').append(newVersion).html();
        }, 80);
    });
</script>