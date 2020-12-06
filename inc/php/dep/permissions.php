<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

$root = dirname(__DIR__, 3);;
include $root . '/protected/config.php';
include_once 'Group.php';
include_once 'User.php';
include_once 'lib/HTMLPurifier.standalone.php';

$config = HTMLPurifier_Config::createDefault();
$config->set('Cache.DefinitionImpl', null);
$config->set('Core.Encoding', 'UTF-8');
$config->set('Core.EscapeInvalidTags', true);
//$config->set('Core.EscapeNonASCIICharacters', true);
$purifier = new HTMLPurifier($config);

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] == "") {
    die('denied');
}

$jsonData = file_get_contents(ROOT_DIR . '/protected/settings.json');
$webSettings = json_decode($jsonData, true);

include 'utils.php';

function hasPermission($permission)
{
    return $_SESSION['user']->getGroup()->hasPermission($permission);
}

function handlePermission($permission)
{
    if (!hasPermission($permission)) {
        die('no permission');
    }
}

if (isset($_GET['haspermission'])) {
    die(hasPermission($_GET['haspermission']));
}

if (isset($_GET['getSettings'])) {
    die($jsonData);
}

function errorAlert($message)
{
    echo '<script type="text/javascript">
            swal({
                title: "Error",
                text: "' . $message . '",
                type: "warning",
                closeOnConfirm: true
                });
    </script>';
}