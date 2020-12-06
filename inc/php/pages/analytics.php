<?php
include '../dep/permissions.php';
$defaultlang = 'English';
foreach ($webSettings as $variable => $value) {
    if ($variable == 'default-language') {
        $defaultlang = $value;
    }
}
handlePermission('view_analytics');
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

<div id="analytics">
    <div class="container">
        <div class="content">
            <div class="row">
                <div class="col-9">
                    <div class="box">
                        <div class="box-header">
                            <?php echo $lang['ANALYTICS_ONLINE']; ?>
                            <div class="tooltip"><i class="material-icons help-icon">help_outline</i><span
                                        class="tooltiptext"><?php echo $lang['ANALYTICS_ONLINE_TOOLTOP']; ?></span>
                            </div>
                        </div>
                        <div class="box-body">
                            <div id="container"
                                 style="width: 100%; height: auto"><?php echo $lang['VAR_LOADING']; ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="box" style="height: 256px">
                        <div class="box-header">
                            <?php echo $lang['ANALYTICS_AVERAGESTATS']; ?>
                            <div class="tooltip"><i class="material-icons help-icon">help_outline</i><span
                                        class="tooltiptext tooltip-left"><?php echo $lang['ANALYTICS_AVERAGESTATS_TOOLTOP']; ?></span>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="col-6 no-padding">
                                <p class="aps-header">ANPPD</p>
                                <div class="col-12 no-padding">
                                    <div class="aps">
                                        <p><?php echo $lang['VAR_MONTHLY']; ?></p>
                                        <h3 id="mnp"><?php echo $lang['VAR_LOADING']; ?></h3>
                                    </div>
                                </div>
                                <div class="col-12 no-padding">
                                    <div class="aps">
                                        <p><?php echo $lang['VAR_WEEKLY']; ?></p>
                                        <h3 id="wnp"><?php echo $lang['VAR_LOADING']; ?></h3>
                                    </div>
                                </div>
                                <div class="col-12 no-padding">
                                    <div class="aps">
                                        <p><?php echo $lang['VAR_DAILY']; ?></p>
                                        <h3 id="dnp"><?php echo $lang['VAR_LOADING']; ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 no-padding" style="padding-left: 12% !important;">
                                <p class="aps-header">ARPPD</p>
                                <div class="col-12 no-padding">
                                    <div class="aps">
                                        <p><?php echo $lang['VAR_MONTHLY']; ?></p>
                                        <h3 id="mrp"><?php echo $lang['VAR_LOADING']; ?></h3>
                                    </div>
                                </div>
                                <div class="col-12 no-padding">
                                    <div class="aps">
                                        <p><?php echo $lang['VAR_WEEKLY']; ?></p>
                                        <h3 id="wrp"><?php echo $lang['VAR_LOADING']; ?></h3>
                                    </div>
                                </div>
                                <div class="col-12 no-padding">
                                    <div class="aps">
                                        <p><?php echo $lang['VAR_DAILY']; ?></p>
                                        <h3 id="drp"><?php echo $lang['VAR_LOADING']; ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="box" style="height: auto" id="versions">
                        <div class="box-header">
                            <?php echo $lang['ANALYTICS_MOSTPLAYEDVERSIONS']; ?>
                            <div class="tooltip"><i class="material-icons help-icon">help_outline</i><span
                                        class="tooltiptext"><?php echo $lang['ANALYTICS_MOSTPLAYEDVERSIONS_TOOLTIP']; ?></span>
                            </div>
                        </div>
                        <div class="box-body" id="version-analytics">
                            <div class="nm-search-results-empty" style="margin: 50px !important;">
                                <?php echo $lang['VAR_LOADING']; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="box">
                        <div class="box-header">
                            <?php echo $lang['ANALYTICS_PLAYERREGIONS']; ?>
                            <div class="tooltip"><i class="material-icons help-icon">help_outline</i><span
                                        class="tooltiptext"><?php echo $lang['ANALYTICS_PLAYERREGIONS_TOOLTIP']; ?></span>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="col-4 no-padding">
                                <div id="countries" style="width: 100%"></div>
                            </div>
                            <div class="col-8 no-padding">
                                <div id="map" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="box" style="height: auto" id="vhosts">
                        <div class="box-header">
                            Most used Virtual Hosts
                            <div class="tooltip"><i class="material-icons help-icon">help_outline</i><span
                                        class="tooltiptext">Gein idee</span>
                            </div>
                        </div>
                        <div class="box-body" id="vhosts-analytics">
                            <div class="nm-search-results-empty" style="margin: 50px !important;">
                                <?php echo $lang['VAR_LOADING']; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-2">
                    <div class="box" style="height: 216px;">
                        <div class="box-header">
                            <?php echo $lang['ANALYTICS_PLAYTIME']; ?>
                            <div class="tooltip"><i class="material-icons help-icon">help_outline</i><span
                                        class="tooltiptext tooltip-veryleft"><?php echo $lang['ANALYTICS_PLAYTIME_TOOLTIP']; ?></span>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="col-12 no-padding">
                                <div class="aps">
                                    <p><?php echo $lang['VAR_TODAY']; ?></p>
                                    <h3 id="dpt"><?php echo $lang['VAR_LOADING']; ?></h3>
                                </div>
                            </div>
                            <div class="col-12 no-padding">
                                <div class="aps">
                                    <p><?php echo $lang['VAR_THISWEEK']; ?></p>
                                    <h3 id="wpt"><?php echo $lang['VAR_LOADING']; ?></h3>
                                </div>
                            </div>
                            <div class="col-12 no-padding">
                                <div class="aps">
                                    <p><?php echo $lang['VAR_TOTAL']; ?></p>
                                    <h3 id="tpt"><?php echo $lang['VAR_LOADING']; ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-10">
                    <div class="box">
                        <div class="box-header">
                            <?php echo $lang['ANALYTICS_USERRETENTION']; ?>
                            <div class="tooltip"><i class="material-icons help-icon">help_outline</i><span
                                        class="tooltiptext"><?php echo $lang['ANALYTICS_USERRETENTION_TOOLTIP']; ?></span>
                            </div>
                        </div>
                        <div class="box-body">
                            <div id="retention" style="width: 100%"><?php echo $lang['VAR_LOADING']; ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="box">
                        <div class="box-header">
                            <?php echo $lang['ANALYTICS_FUTUREPORJECTIONS']; ?>
                            <div class="tooltip"><i class="material-icons help-icon">help_outline</i><span
                                        class="tooltiptext"><?php echo $lang['ANALYTICS_FUTUREPORJECTIONS_TOOLTIP']; ?></span>
                            </div>
                        </div>
                        <div class="box-body">
                            <div id="future" style="width: 100%"><?php echo $lang['VAR_LOADING']; ?></div>
                        </div>
                    </div>
                </div>
                <?php
                if (strcasecmp(getServerMode(), 'mixed') == 0) {
                    echo '
                <div class="col-8">
                    <div class="box">
                        <div class="box-header">
                            Premium <div class="tooltip"><i class="material-icons help-icon">help_outline</i><span class="tooltiptext">This shows which mode is most used</span></div>
                        </div>
                        <div class="box-body">
                            <div id="premium" style="width: 100%">'.  $lang['VAR_LOADING'] . '</div>
                        </div>
                    </div>
                </div>
                ';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php
function getServerMode()
{
    global $pdo;
    $stmnt = $pdo->prepare('SELECT value FROM nm_values WHERE variable=?;');
    $stmnt->bindValue(1, 'setting_servermode', 2);
    $stmnt->execute();
    $result = $stmnt->fetch();
    return $result['value'];
}

?>

<script>
    $(document).ready(function () {
        $("#version-analytics").load('../inc/php/dep/pages/analytics.php?dataload=versions');
        $("#vhosts-analytics").load('../inc/php/dep/pages/analytics.php?dataload=virtualhosts');
        $("h3").each(function () {
            if (this.hasAttribute('id')) {
                let attr = this.getAttribute('id');
                $('#' + attr).load('../inc/php/dep/pages/analytics.php?dataload=' + attr);
            }
        });
    });
    $.getScript("../inc/js/pages/analytics.js", function (data, textStatus, jqxhr) {
        console.log(textStatus); //success
        console.log(jqxhr.status); //200
        console.log('Load was performed.');
    });
    /*if (typeof isAnalyticsLoaded === 'undefined') {
        $.getScript("../inc/js/pages/analytics.js", function (data, textStatus, jqxhr) {
            console.log(textStatus); //success
            console.log(jqxhr.status); //200
            console.log('Load was performed.');
        });
    }*/
</script>
<!--<script src="../inc/js/pages/analytics.js"></script>-->