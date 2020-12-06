<?php
include '../permissions.php';
$type = $_GET['type'];

switch ($type) {
    case 'newplayers':
        $res = array();
        $stmnt = $pdo->query('SELECT cast(from_unixtime(firstlogin/1000) as date) as day, count(*) as amount from nm_players where firstlogin > unix_timestamp(date_sub(cast(now() as date), interval 60 day))*1000 group by day;');
        $data_result = $stmnt->fetchAll();
        foreach ($data_result as $row) {
            array_push($res, [(float)strtotime($row['day']) * 1000, (int)$row['amount']]);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
        break;
    case 'sessions':
        $res = array();
        $stmnt = $pdo->query('SELECT cast(from_unixtime(start/1000) as date) as day, count(*) as amount from nm_sessions where start > unix_timestamp(date_sub(cast(now() as date), interval 60 day))*1000 group by day;');
        $data_result = $stmnt->fetchAll();
        foreach ($data_result as $row) {
            array_push($res, [(float)strtotime($row['day']) * 1000, (int)$row['amount']]);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
        break;
    case 'peak':
        $res = array();
        $stmnt = $pdo->query('SELECT cast(from_unixtime(time/1000) as date) as day, MAX(online) as amount from nm_serverAnalytics where time > unix_timestamp(date_sub(cast(now() as date), interval 60 day))*1000 group by day;');
        $data_result = $stmnt->fetchAll();
        foreach ($data_result as $row) {
            array_push($res, [(float)strtotime($row['day']) * 1000, (int)$row['amount']]);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
        break;
    case 'map':
        $stmnt = $pdo->query('SELECT DISTINCT(country) as country, count(*) AS count FROM nm_players WHERE firstlogin >=((UNIX_TIMESTAMP(CURDATE())*1000)-5184000000) GROUP BY country');
        $res = array();
        $result = $stmnt->fetchAll();
        foreach ($result as $row) {
            array_push($res, ["code" => $row['country'], "z" => $row['count']]);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
        break;
}