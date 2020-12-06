<?php
include '../dep/permissions.php';
handlePermission('view_servers');
$defaultlang = 'English';
foreach ($webSettings as $variable => $value) {
    if ($variable == 'default-language') {
        $defaultlang = $value;
    }
}
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
?>

<style>
    /* Labels */
    .label-danger {
        background-color: #d9534f;
    }

    .label-warning {
        background-color: #f0ad4e;
    }

    .label-success {
        background-color: #5cb85c;
    }

    .label {
        display: inline-block;
        padding: .2em .6em .3em;
        font-size: 80%;
        line-height: 18px;
        cursor: pointer;
        min-height: 18px;
        min-width: 44px;
        font-weight: 700;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
        border-radius: .25em;
    }

    .tdmessage {
        max-width: 250px;
        word-wrap: break-word;
    }
</style>

<div class="container">
    <div class="content">
        <div class="row">
            <div class="col-12">
                <div class="data-card" style="margin-top: 20px">
                    <div id="data-card-content">
                        <div class="data-card-body">
                            <div class="nm-search-results-empty">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="data-card" style="margin-top: 20px">
                    <div id="data-servergroups-content">
                        <div class="data-card-body">
                            <div class="nm-search-results-empty">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="create-server" class="modal">
    <div class="modal-content" style="width: 600px !important;">
        <form id="create-server-form">
            <div class="nm-input-container">
                <label>Servername *</label>
                <label>
                    <input type="text" name="servername" id="servername" maxlength="36">
                </label>
            </div>
            <div class="nm-input-container">
                <label>Displayname</label>
                <label>
                    <input type="text" name="displayname" id="displayname" maxlength="36">
                </label>
            </div>
            <div class="nm-input-container">
                <label>IP</label>
                <label>
                    <input type="text" value="localhost" name="ip" maxlength="39">
                </label>
            </div>
            <div class="nm-input-container">
                <label>Port</label>
                <label>
                    <input type="text" value="25565" name="port" maxlength="65535">
                </label>
            </div>
            <div class="nm-input-container">
                <label>MOTD</label>
                <label>
                    <textarea name="motd" rows="2" maxlength="65535"></textarea>
                    <!--<input type="text" name="motd">-->
                </label>
            </div>
            <div class="nm-input-container">
                <label>Allowed Versions</label>
                <label>
                    <input type="text" name="allowed_versions" maxlength="128">
                </label>
            </div>
            <div class="nm-input-container">
                <label>Values</label>
            </div>
            <div class="row">
                <?php
                foreach ($pdo->query("SHOW COLUMNS FROM `nm_servers`") as $item) {
                    if ($item['Field'] == 'id' || $item['Field'] == 'servername' || $item['Field'] == 'displayname' || $item['Field'] == 'ip' || $item['Field'] == 'port' || $item['Field'] == 'motd' || $item['Field'] == 'online' || $item['Field'] == 'allowed_versions') {
                        continue;
                    }
                    echo '<div class="nm-input-container" style="position: relative; top: -10px; right: -16px; padding: 0 !important;">
                            <input type="checkbox" id="' . $item['Field'] . '" name="' . $item['Field'] . '" class="switch-input" ' . $item['Field'] . '>
	                        <label for="' . $item['Field'] . '" class="switch-label"><span style="position: relative !important; top: -4px !important;" class="toggle--on">' . $item['Field'] . '</span><span style="position: relative !important; top: -4px !important;" class="toggle--off">' . $item['Field'] . '</span></label>
	                        </div>';
                }
                ?>
            </div>
            <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                <button class="nm-button" type="button" style="position: relative;right: -24px;" close="create-server">
                    Cancel
                </button>
                <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit"
                        close="create-server">Create
                </button>
            </div>
        </form>
    </div>
</div>
<div id="create-servergroup" class="modal">
    <div class="modal-content" style="width: 600px !important;">
        <form id="create-servergroup-form">
            <div class="nm-input-container">
                <label>Group name *</label>
                <label>
                    <input type="text" name="name" maxlength="32" required>
                </label>
            </div>
            <div class="nm-input-container">
                <label>ServerIds *</label>
                <label>
                    <input type="text" name="serverIds" required>
                </label>
            </div>
            <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                <button class="nm-button" type="button" style="position: relative;right: -24px;"
                        close="create-servergroup">Cancel
                </button>
                <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit"
                        close="create-servergroup">Create
                </button>
            </div>
        </form>
    </div>
</div>
<div id="cached-modal"></div>

<script>
    $(document).ready(function () {
        $("#data-card-content").load('../inc/php/dep/pages/servers.php?p=1', function () {
            console.log('loaded');
        });
    });

    $(document).ready(function () {
        $("#data-servergroups-content").load('../inc/php/dep/pages/servers.php?load=groups&p=1', function () {
            console.log('loaded');
        });
    });

    $(document).on('click', 'a', function () {
        if (this.hasAttribute('nmservers')) {
            let attr = $(this).attr('page');
            if (typeof attr !== typeof undefined && attr !== false) {
                $("#data-card-content").load('../inc/php/dep/pages/servers.php?p=' + attr, function () {
                    console.log('load performed');
                });
            }
        } else if (this.hasAttribute('nmservergroupss')) {
            let attr = $(this).attr('page');
            if (typeof attr !== typeof undefined && attr !== false) {
                $("#data-servergroups-content").load('../inc/php/dep/pages/servers.php?load=groups&p=' + attr, function () {
                    console.log('punishmentsearch load performed');
                });
            }
        }
    });

    if (typeof isServersLoaded === 'undefined') {
        $.getScript("../inc/js/pages/servers.js", function (data, textStatus, jqxhr) {
            console.log(textStatus); //success
            console.log(jqxhr.status); //200
            console.log('Load was performed.');
        });
    }
</script>