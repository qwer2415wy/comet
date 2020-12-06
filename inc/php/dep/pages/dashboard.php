<?php
include '../permissions.php';

$type = $_GET['dataload'];

switch ($type) {
    case 'totalplayers':
        echo $pdo->query('SELECT COUNT(*) FROM nm_players')->fetchColumn();
        break;
    case 'todayplayers':
        echo $pdo->query('SELECT COUNT(*) FROM nm_players WHERE `lastlogin` > (UNIX_TIMESTAMP(CURDATE())*1000)')->fetchColumn();
        break;
    case 'newplayers':
        echo $pdo->query('SELECT COUNT(*) FROM nm_players WHERE `firstlogin` > (UNIX_TIMESTAMP(CURDATE())*1000)')->fetchColumn();
        break;
    case 'todayplaytime':
        $time = 0;
        $stmnt = $pdo->query('SELECT `time` FROM nm_sessions WHERE `start` > (UNIX_TIMESTAMP(CURDATE())*1000)');
        foreach ($stmnt->fetchAll() as $item) {
            $time += $item['time'];
        }
		echo formatMilliseconds($time);
        //echo gmdate('H:i:s', ($time) / 1000);
        break;
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
    return sprintf($format, $hours, $minutes, $seconds, $milliseconds);
}