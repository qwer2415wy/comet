<?php
define('ROOT_DIR', dirname(__DIR__, 1));

$configFile = parse_ini_file('config.ini');

define('DB_SERVER', $configFile['db_host']);
define('DB_PORT', $configFile['db_port']);
define('DB_USERNAME', $configFile['db_username']);
define('DB_PASSWORD', $configFile['db_password']);
define('DB_DATABASE', $configFile['db_database']);
define('DB_ENCODING', 'utf8mb4');
try {
    $pdo = new PDO("mysql:host=" .
        DB_SERVER . ";port=" . DB_PORT . ";dbname=" .
        DB_DATABASE . ";charset=" . DB_ENCODING, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $ex) {
    getErrorPage('Error: ' . $ex->getCode() . '. MySQL Connection refused');
    die();
}

function getErrorPage($traceString)
{
    $loginvideo = '';
    $background = '';
    $defaultlang = 'English';
    $logo = ROOT_DIR . '/inc/img/logo.png';
    $full_logo = ROOT_DIR . '/inc/img/full_logo.png';
    $jsondata = file_get_contents(__DIR__ . '/settings.json');
    $websettings = json_decode($jsondata, true);
    foreach ($websettings as $variable => $value) {
        if ($variable == 'login-video') {
            $loginvideo = $value;
        }
        if ($variable == 'login-background') {
            $background = $value;
        }
        if ($variable == 'default-language') {
            $defaultlang = $value;
        }
        if ($variable == 'logo') {
            $logo = $value;
        }
        if ($variable == 'full-logo') {
            $full_logo = $value;
        }
    }
    if (isset($_COOKIE['language'])) {
        $language = $_COOKIE['language'];
        $langdir = ROOT_DIR . "/inc/php/dep/languages/";
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
        include ROOT_DIR . '/inc/php/dep/languages/' . $defaultlang . '.php';
    }
	echo "

    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>".$lang['PANEL_TITLE']."</title>
        <link rel='icon' href='" .  $logo . "'>
        <link rel='stylesheet' href='//fonts.googleapis.com/icon?family=Material+Icons'>
        <link rel='stylesheet' href='//code.getmdl.io/1.3.0/material.indigo-pink.min.css'>
        <script src='//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
        <script defer src='//code.getmdl.io/1.3.0/material.min.js'></script>
    </head>
    <style>
        #header {
            transition: all 0.5s ease;
        }

        body {
            background-repeat: no-repeat;
            background-size: cover !important;
            background-attachment: fixed;
            background-position: 50% 0;
            background-color: black;
            overflow: hidden;
        }

        #video {
            z-index: -99;
            position: fixed;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: 150%;
            height: auto;
            -webkit-transform: translateX(-50%) translateY(-50%);
            transform: translateX(-50%) translateY(-50%);
            filter: blur(30px);
        }

        .logo img {
            margin-top: -100px !important;
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 25%;
        }

        .mdl-layout {
            align-items: center;
            justify-content: center;
        }

        .mdl-layout__content {
            padding: 24px;
            flex: none;
        }

        .mdl-color--grey-100 {
            background-color: transparent !important;
        }
    </style>
    <body>
    <div class='mdl-grid'>
        <div class='mdl-layout mdl-js-layout mdl-color--grey-100'>
            <div class='logo'>
                <img src='" . $full_logo . "' draggable='false'>
            </div>
            <main class='mdl-layout__content'>
                <div class='mdl-card mdl-shadow--6dp'>
                    <form id='login'>
                        <div class='mdl-card__title mdl-color--primary mdl-color-text--white' id='header'>
                            <h2 class='mdl-card__title-text' id='title'>NetworkManager</h2>
                        </div>
                        <div class='mdl-card__supporting-text'>
                            <h4>" . $traceString . "</h4>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
    <div id='background'>

    </div>
    <script>
        var videosrc = '" . $loginvideo . "';
        var backgroundimagesrc = '" . $background . "';
        if (videosrc !== null && videosrc) {
            $(document).ready(function () {
                document.getElementById('background').innerHTML = '<video id=\"video\" autoplay=\"autoplay\" loop=\"loop\" preload=\"none\" muted><source src=' + videosrc + ' type=\"video/mp4\"/></video>';
            });
        } else if (backgroundimagesrc !== null && backgroundimagesrc) {
            $(document).ready(function () {
                document.body.style.backgroundImage = 'url(' + backgroundimagesrc + ')';
            });
        }
    </script>
    </body>
    </html>";
}