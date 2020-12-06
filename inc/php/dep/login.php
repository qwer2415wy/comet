<?php
session_start();
include 'User.php';
include 'Group.php';
//include_once 'protected/config.php';
ob_start();

$root =  dirname(__DIR__, 3);;
include $root . '/protected/config.php';
include_once 'lib/BruteForceBlock.php';

use ejfrancis\BruteForceBlock;

try {
    $BFBresponse = BruteForceBlock::getLoginStatus();
    switch ($BFBresponse['status']) {
        case 'safe':
            //safe to login
            if (isset($_POST['username'])) {
                $username = $_POST['username'];
                $password = $_POST['password'];
                $salt = 'HM$75Dh(r^#A22j@_';
                //$pwhash = md5($salt . $password);
                $statement = $pdo->prepare('SELECT `id`, `username`, `password`, `usergroup` FROM nm_accounts WHERE `username`=?;');
                $statement->bindParam(1, $username, PDO::PARAM_STR);
                $statement->execute();
                $result = $statement->fetchAll();
                if (count($result) != 0 && password_verify($password, $result[0][2])) {
                    $permissions = array();
                    foreach ($pdo->query('SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME`="nm_accountgroups";') as $item) {
                        $columnName = $item['COLUMN_NAME'];
                        if ($columnName == 'id' || $columnName == 'name') {
                            continue;
                        }
                        $statement2 = $pdo->prepare('SELECT ' . $columnName . ' FROM nm_accountgroups WHERE name=?;');
                        $statement2->bindParam(1, $result[0][3], PDO::PARAM_STR);
                        $statement2->execute();
                        $res = $statement2->fetchAll();
                        if ($res[0][0] == '1') {
                            $permissions[] = $columnName;
                        }
                    }
                    error_log((string)$permissions);
                    $_SESSION['user'] = new User($result[0][1], $result[0][2], new Group($result[0][3], $permissions));
                    if (isset($_POST['remember'])) {
                        setcookie('nm_username', md5($salt . $username), time() + (86400 * 7), '/');
                        setcookie('nm_password', $result[0][2], time() + (86400 * 7), '/');
                    }
                    echo 'true';
                } else {
                    echo 'false';
                    $BFBresponse = BruteForceBlock::addFailedLoginAttempt($result[0][0], GetRealUserIp());
                }
            }
            break;
        case 'error':
            //error occured. get message
            $error_message = $BFBresponse['message'];
            throw new Exception($error_message);
            break;
        case 'delay':
            //time delay required before next login
            $remaining_delay_in_seconds = $BFBresponse['message'];

            throw new LoginDelayException($remaining_delay_in_seconds);
            break;
        case 'captcha':
            die('lockedout');
            break;
    }
} catch (LoginDelayException $e) {
    die('delay ' . $e->getDelay());
} catch (Exception $e) {
    die($e->getTraceAsString());
}

/**
 * Get real user ip
 *
 * Usage sample:
 * GetRealUserIp();
 * GetRealUserIp('ERROR',FILTER_FLAG_NO_RES_RANGE);
 *
 * @param string $default default return value if no valid ip found
 * @param int $filter_options filter options. default is FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
 *
 * @return string real user ip
 */

function GetRealUserIp($default = NULL, $filter_options = 12582912)
{
    $HTTP_X_FORWARDED_FOR = isset($_SERVER) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : getenv('HTTP_X_FORWARDED_FOR');
    $HTTP_CLIENT_IP = isset($_SERVER) ? $_SERVER["HTTP_CLIENT_IP"] : getenv('HTTP_CLIENT_IP');
    $HTTP_CF_CONNECTING_IP = isset($_SERVER) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : getenv('HTTP_CF_CONNECTING_IP');
    $REMOTE_ADDR = isset($_SERVER) ? $_SERVER["REMOTE_ADDR"] : getenv('REMOTE_ADDR');

    $all_ips = explode(",", "$HTTP_X_FORWARDED_FOR,$HTTP_CLIENT_IP,$HTTP_CF_CONNECTING_IP,$REMOTE_ADDR");
    foreach ($all_ips as $ip) {
        if ($ip = filter_var($ip, FILTER_VALIDATE_IP, $filter_options))
            break;
    }
    return $ip ? $ip : $default;
}