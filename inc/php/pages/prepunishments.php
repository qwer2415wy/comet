<?php
include '../dep/permissions.php';
$defaultlang = 'English';
foreach ($webSettings as $variable => $value) {
    if ($variable == 'default-language') {
        $defaultlang = $value;
    }
}
handlePermission('view_pre_punishments');
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
<div class="container">
    <div class="content">
        <div class="row">
            <div class="col-12">
                <div class="data-card" style="margin-top: 20px">
                    <!--<div class="data-card-header">
                        <div class="data-card-search-box">
                            <i class="material-icons">search</i>
                            <form>
                                <input type="text" id="search-input" class="data-card-input"
                                       placeholder="<?php echo $lang['PUNISHMENTS_SEARCH']; ?>" autocomplete="off">
                            </form>
                        </div>
                    </div>-->
                    <div id="data-card-content">
                        <div class="data-card-body">
                            <div class="nm-search-results-empty"><?php echo $lang['VAR_LOADING']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="create-pre-punishment" class="modal">
    <div class="modal-content" style="width: 600px !important;">
        <form id="create-pre-punishment-form">
            <div class="nm-input-container" id="name">
                <label>Name *</label>
                <label>
                    <input type="text" name="name" maxlength="24">
                </label>
            </div>
            <div class="nm-input-container">
                <label>Punishment Type *</label>
                <label>
                    <select name="ptype" id="prepunishment-type" required>
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
            <div class="nm-input-container" id="duration">
                <label>Duration *</label>
                <label>
                    <input type="text" name="duration">
                </label>
            </div>
            <div class="nm-input-container" id="server">
                <label>Server *</label>
                <label>
                    <input type="text" name="server" id="serverinput" maxlength="36" required>
                </label>
            </div>
            <div class="nm-input-container">
                <label>Reason</label>
                <label>
                    <input type="text" name="reason" maxlength="512" required>
                </label>
            </div>
            <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                <button class="nm-button" type="button" style="position: relative;right: -24px;"
                        close="create-pre-punishment">Cancel
                </button>
                <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit"
                        close="create-pre-punishment">Create
                </button>
            </div>
        </form>
    </div>
</div>
<div id="cached-modal"></div>
<script>
    $(document).ready(function () {
        $("#data-card-content").load('../inc/php/dep/pages/prepunishments.php?p=1', function () {
            console.log('loaded');
        });
    });

    $(document).on('click', 'a', function () {
        if (this.hasAttribute('pre-punishments')) {
            let attr = $(this).attr('page');
            if (typeof attr !== typeof undefined && attr !== false) {
                $("#data-card-content").load('../inc/php/dep/pages/prepunishments.php?p=' + attr, function () {
                    console.log('load performed');
                });
            }
        }
    });

    if (typeof isPrePunishmentsLoaded === 'undefined') {
        $.getScript("../inc/js/pages/prepunishments.js", function (data, textStatus, jqxhr) {
            console.log(textStatus); //success
            console.log(jqxhr.status); //200
            console.log('Load was performed.');
        });
    }
</script>