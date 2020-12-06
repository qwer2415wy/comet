<?php
include '../permissions.php';
handlePermission('view_analytics');
$type = $_GET['type'];

switch ($type) {
    case 'map':
        $stmnt = $pdo->query('SELECT DISTINCT(country) as country, count(*) AS count FROM nm_players GROUP BY country;');
        $res = array();
        $result = $stmnt->fetchAll();
        foreach ($result as $row) {
            array_push($res, ["code" => $row['country'], "z" => $row['count']]);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
        break;
    case 'countrynames':
        $stmnt = $pdo->query('SELECT DISTINCT(country) as country, count(*) AS count FROM nm_players GROUP BY country ORDER BY count DESC LIMIT 15;');
        $data = $stmnt->fetchAll(PDO::FETCH_ASSOC);
        $res = array();
        foreach ($data as $item) {
            array_push($res, countryCodeToCountry($item['country']));
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        break;
    case 'countrydata':
        $stmnt = $pdo->query('SELECT DISTINCT(country) as country, count(*) AS count FROM nm_players GROUP BY country ORDER BY count DESC LIMIT 15;');
        $data = $stmnt->fetchAll(PDO::FETCH_ASSOC);
        $result = '[';
        $amount = 0;
        foreach ($data as $item) {
            if ($stmnt->rowCount() == ($amount + 1)) {
                $result = $result . $item['count'];
            } else {
                $result = $result . $item['count'] . ', ';
            }
            $amount++;
        }
        $result = $result . ']';
        echo $result;
        break;
    case 'future':
        $stmnt = $pdo->query('SELECT cast(from_unixtime(firstlogin/1000) as date) as day, count(*) as amount from nm_players where firstlogin > unix_timestamp(date_sub(cast(now() as date), interval 600 day))*1000 group by day;');

        $totalplayers = 0;
        $before = 0;
        $count = 0;
        $datebefore = round(microtime(true));
        $dayamount = array();
        $values = array();
        foreach ($stmnt->fetchAll() as $item) {
            if ($count > 0) {
                $now = strtotime($item['day']);
                $your_date = $datebefore;
                $datediff = $now - $your_date;
                $diffdays = floor($datediff / (60 * 60 * 24));
                if ($diffdays <= 0) {
                    $diffdays = 1;
                }
                $dayamount[] = $diffdays;
                $calc = (((($item['amount'] + $totalplayers) - $totalplayers) / $totalplayers) / $diffdays) * 100;
                $values[] = $calc;
            }
            $datebefore = strtotime($item['day']);
            $before = $item['amount'];
            $totalplayers += $item['amount'];
            $count++;
        }
        $multiplicator = (array_sum($values) / count($values));
        $daytiffamount = (array_sum($dayamount) / count($dayamount));
        $data = array();
        $date = new DateTime();
        $res = $totalplayers;
        for ($i = 0; $i < 5; $i++) {
            if ($i == 0) {
                array_push($data, [($date->getTimestamp() * 1000), $totalplayers]);
            } else {
                $res = $res * (($multiplicator / 100) + 1);
                $date->modify('+' . intval($daytiffamount) . ' days');
                array_push($data, [($date->getTimestamp() * 1000), round($res)]);
            }
        }
        echo json_encode($data, JSON_NUMERIC_CHECK);
        break;
    /*case 'retention':
        $stmnt = $pdo->query('SELECT days_active, COUNT(*) as players
    FROM (SELECT uuid, MAX(timestampdiff(DAY, from_unixtime(firstlogin/1000), from_unixtime(start/1000))) as days_active
    FROM nm_players INNER JOIN nm_sessions USING (uuid) GROUP BY uuid) as retention
GROUP BY days_active;');
        if ($stmnt->rowCount() == 0) {
            die('[0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]');
        }
        $res = array();
        foreach ($stmnt->fetchAll() as $row) {
            array_push($res, [$row['players']]);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
        break;*/
    case 'retention':
        $stmnt = $pdo->query('SELECT from_unixtime(firstlogin/1000) as day, uuid FROM nm_players WHERE playtime > 100000;');
        if ($stmnt->rowCount() == 0) {
            die('[0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]');
        }
        $players = $stmnt->fetchAll();

        $stmnt = $pdo->query('SELECT from_unixtime(start/1000) as day, uuid FROM nm_sessions WHERE start > unix_timestamp(date_sub(now(), interval 4 month))*1000;');
        if ($stmnt->rowCount() == 0) {
            die('[0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]');
        }
        $sessions = $stmnt->fetchAll();

        $playersssions = array();
        $session_count = count($sessions);
        $player_count = count($players);

        foreach ($players as $player) {
            $data = array();
            $data[] = 1;
            $date = new DateTime($player['day']);
            for ($i = 0; $i < 7; $i++) {
                $date->modify('+1 days');
                $amount = 0;
                foreach ($sessions as $session) {
                    if ($amount == ($session_count - 1)) {
                        $data[] = 0;
                        break 2;
                    }
                    if ($session['uuid'] === $player['uuid']) {
                        $sessiondate = new DateTime($session['day']);

                        if ($sessiondate->getTimestamp() == $date->getTimestamp()) {
                            $data[] = 1;
                            break 2;
                        }
                    }
                    $amount++;
                }
            }

            $playersssions[] = $data;
        }

        $result = array();
        foreach ($playersssions as $session) {
            $amount = 0;
            for ($i = 0; $i < count($session); $i++) {
                //echo 'Session: ' . $session[$i] . "\n";
                $amount +=
                    $session[$i];
            }
            $result[] = $amount;
        }

        $retention = array();
        for ($i = 0; $i < 7; $i++) {
            //echo 'result: ' . $result[$i] . "\n";
            $val = array_key_exists($i, $result) ? $result[$i] : 0;
            $retention[] = floatval(($val / $player_count) * 100);
        }

        echo json_encode($retention, JSON_NUMERIC_CHECK);
        break;
    case "premium":
        $stmnt = $pdo->query('SELECT DISTINCT(premium) as premium, COUNT(*) as count FROM nm_players GROUP by premium ORDER BY count DESC;');

        $pie_data = '[';
        foreach ($stmnt->fetchAll() as $row) {
            $pie_data = $pie_data . '{"name": "' . ($row['premium'] == 1 ? 'Premium' : 'Cracked') . '", "y": ' . $row['count'] . '}, ';
        }
        $pie_data = substr_replace($pie_data, "", -1);
        $pie_data = substr_replace($pie_data, "", -1);
        $pie_data = $pie_data . ']';
        echo $pie_data;
        break;
}