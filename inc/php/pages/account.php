<?php
include_once "../dep/permissions.php";
global $webSettings;
global $lang;
$defaultTheme = $webSettings['default-theme'];
if (!isset($defaultTheme)) {
    $defaultTheme = "Default";
}
$defaultLanguage = $webSettings['default-language'];
if (!isset($defaultLanguage)) {
    $defaultLanguage = "English";
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
    include '../dep/languages/' . $defaultLanguage . '.php';
}
?>

<div class="container">
    <div class="content">
        <div class="row">
            <div class="col-6">
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="data-card-header-title">
                            <?php echo $lang['VAR_PASSWORD'] ?>
                        </div>
                    </div>
                    <div class="data-card-body">
                        <form id="change-password">
                            <div class="data-card-row-value" style="padding: 20px">
                                <div class="nm-input-container">
                                    <input type="password" placeholder="<?php echo $lang['ACCOUNTSETTINGS_OLDPASS']; ?>"
                                           name="old">
                                </div>
                                <div class="nm-input-container">
                                    <input type="password" placeholder="<?php echo $lang['ACCOUNTSETTINGS_NEWPASS']; ?>"
                                           name="new">
                                </div>
                                <div class="col-12 no-padding text-right" style="width: 100% !important;">
                                    <button class="nm-button" type="submit"
                                            style="margin: 0 !important;"><?php echo $lang['VAR_SAVE']; ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="data-card-header-title">
                            <?php echo $lang['VAR_LANGUAGES']; ?>
                        </div>
                    </div>
                    <div class="data-card-body">
                        <form id="change-language">
                            <div class="data-card-row-value" style="padding: 20px">
                                <p class="text-center"> <?php echo $lang['ACCOUNTSETTINGS_SELECTED_LANG'] . ' ' . $lang['LANGUAGE'] . ' ' . $lang['VAR_BY'] . ' ' . $lang['AUTHOR']; ?></p>
                                <div class="nm-input-container">
                                    <select id="language" class="form-control" name="language">

                                    </select>
                                </div>
                                <div class="col-12 no-padding text-right" style="width: 100% !important;">
                                    <button class="nm-button" type="submit"
                                            style="margin: 0 !important;"><?php echo $lang['VAR_SAVE']; ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="data-card-header-title">
                            <?php echo $lang['VAR_THEMES']; ?>
                        </div>
                    </div>
                    <div class="data-card-body">
                        <form id="change-theme">
                            <div class="data-card-row-value" style="padding: 20px">
                                <p class="text-center"> <?php echo $lang['ACCOUNTSETTINGS_SELECTED_THEME'] . ' ' . isset($_COOKIE['theme']) ? $_COOKIE['theme'] : $defaultTheme; ?></p>
                                <div class="nm-input-container">
                                    <select id="theme" class="form-control" name="theme">

                                    </select>
                                </div>
                                <div class="col-12 no-padding text-right" style="width: 100% !important;">
                                    <button class="nm-button" type="submit"
                                            style="margin: 0 !important;"><?php echo $lang['VAR_SAVE']; ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    if (typeof isAccountLoaded === 'undefined') {
        $.getScript("../inc/js/pages/account.js", function (data, textStatus, jqxhr) {
            console.log(textStatus); //success
            console.log(jqxhr.status); //200
            console.log('Load was performed.');
        });
    }
</script>