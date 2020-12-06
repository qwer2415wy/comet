<?php
include '../permissions.php';
include '../MinecraftColorcodes.php';
handlePermission('view_players');
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

echo '<style>
    /* Labels */
    .label-danger {
        color: white;
        background-color: #d9534f;
    }

    .label-info {
        color: white;
        background-color: #00C0EF;
    }


    .label-warning {
        color: white;
        background-color: #f0ad4e;
    }

    .label-success {
        color: white;
        background-color: #5cb85c;
    }
    
    .label{display:inline;padding:.4em .9em .5em;font-size:75%;font-weight:bold;line-height:1;color:#ffffff;max-width:100%;vertical-align: baseline; }
    
    span:after {
        content: none;
        clear: both;
    }
</style>';

$uuid = $_GET['uuid'];

$stmt = $pdo->prepare('SELECT `username`, `nickname`, `ip`, `country`, `version`, `firstlogin`, `lastlogin`, `lastlogout`, `online`, `playtime` FROM nm_players where `uuid`=?;');
$stmt->bindParam(1, $uuid, 2);
$stmt->execute();
if ($stmt->rowCount() > 0) {

    $row = $stmt->fetchAll()[0];

    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $iterator = new IteratorIterator($stmt);

    if ($row['online'] == '0') {
        $online = '<span class="label label-danger">False</span>';
    } else {
        $online = '<span class="label label-success">True</span>';
    }

    if ($row['premium'] == '0') {
        $premium = '<span class="label label-danger">False</span>';
    } else {
        $premium = '<span class="label label-success">True</span>';
    }

    echo '<div class="data-card-body">';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-50">
                                <p>' . $lang['PLAYER_USERNAME'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-50 text-right">
                                <p><img src="https://crafatar.com/avatars/' . $uuid . '"> ' . $row['username'] . '</p>
                            </div>
                        </div>';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-50">
                                <p>' . $lang['PLAYER_NICKNAME'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-50 text-right">
                                <p>' . $colorCodes->convert($row['nickname'], true) . '</p>
                            </div>
                        </div>';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-30">
                                <p>' . $lang['PLAYER_UUID'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-70 text-right">
                                <p>' . $uuid . '</p>
                            </div>
                        </div>';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-30">
                                <p>' . $lang['PLAYER_COUNTRY'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-70 text-right">
                                <p>' . countryCodeToCountry($row['country']) . '</p>
                            </div>
                        </div>';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-50">
                                <p>' . $lang['PLAYER_LATESTMINECRAFTVERSION'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-50 text-right">
                                <p>' . getVersion($row['version']) . '</p>
                            </div>
                        </div>';
    if (hasPermission('show_ip')) {
        echo '<div class="data-card-row">
								<div class="data-card-row-value nm-data-card-30">
									<p>' . $lang['PLAYER_IPADDRESS'] . '</p>
								</div>
								<div class="data-card-row-value nm-data-card-70 text-right">
									<p>' . $row['ip'] . '</p>
								</div>
							</div>';
    }
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-30">
                                <p>' . $lang['PLAYER_JOINED'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-70 text-right">
                                <p>' . date($dateformat, $row['firstlogin'] / 1000) . '</p>
                            </div>
                        </div>';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-30">
                                <p>' . $lang['PLAYER_LASTLOGIN'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-70 text-right">
                                <p>' . date($dateformat, $row['lastlogin'] / 1000) . '</p>
                            </div>
                        </div>';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-30">
                                <p>' . $lang['PLAYER_LASTLOGOUT'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-70 text-right">
                                <p>' . date($dateformat, $row['lastlogout'] / 1000) . '</p>
                            </div>
                        </div>';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-30">
                                <p>' . $lang['PLAYER_ONLINE'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-70 text-right">
                                <p>' . $online . '</p>
                            </div>
                        </div>';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-30">
                                <p>' . $lang['PLAYER_PLAYTIME'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-70 text-right">
                                <p>' . formatMilliseconds($row['playtime']) . '</p>
                            </div>
                        </div>';
    if (strcasecmp(getServerMode(), 'mixed') == 0) {
        echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-30">
                                <p>' . $lang['PLAYER_PREMIUM'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-70 text-right">
                                <p>' . $premium . '</p>
                            </div>
                        </div>';
    }
    echo '</div>';

} else {
    echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
}

function formatMilliseconds($milliseconds)
{
    $seconds = floor($milliseconds / 1000);
    $minutes = floor($seconds / 60);
    $hours = floor($minutes / 60);
    $milliseconds = $milliseconds % 1000;
    $seconds = $seconds % 60;
    $minutes = $minutes % 60;

    $format = '%u:%02u:%02u';
    $time = sprintf($format, $hours, $minutes, $seconds, $milliseconds);
    return rtrim($time, '0');
}

function getServerMode()
{
    global $pdo;
    $stmnt = $pdo->prepare('SELECT `value` FROM nm_values WHERE `variable`=?;');
    $stmnt->bindValue(1, 'setting_servermode', 2);
    $stmnt->execute();
    $result = $stmnt->fetch();
    return $result['value'];
}

function isPremium($uuid)
{
    global $pdo;
    try {
        $stmnt = $pdo->prepare('SELECT `premium` FROM nm_players WHERE `uuid`=?;');
        $stmnt->bindParam(1, $uuid, 2);
        $result = $stmnt->fetch();
        return $result['premium'];
    } catch (PDOException $ex) {
        return false;
    }
}