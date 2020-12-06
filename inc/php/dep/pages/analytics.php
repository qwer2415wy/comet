<?php
include '../permissions.php';
handlePermission('view_analytics');
header('Content-type: application/json');
ini_set('memory_limit', '256M');

$type = $_GET['dataload'];

switch ($type) {
    case 'mnp':
        //echo $pdo->query('SELECT COUNT(id) FROM nm_players WHERE firstlogin >= UNIX_TIMESTAMP((LAST_DAY(NOW())+INTERVAL 1 DAY)-INTERVAL 1 MONTH)*1000')->fetchColumn();
        echo $pdo->query('SELECT COUNT(*) FROM nm_players WHERE firstlogin >= UNIX_TIMESTAMP(CURDATE()- INTERVAL 1 MONTH)*1000;')->fetchColumn();
        break;
    case 'wnp':
        echo $pdo->query('SELECT COUNT(*) FROM nm_players WHERE firstlogin >= UNIX_TIMESTAMP(CURDATE()- INTERVAL 1 WEEK)*1000;')->fetchColumn();
        break;
    case 'dnp':
        echo $pdo->query('SELECT COUNT(*) FROM nm_players WHERE firstlogin >= UNIX_TIMESTAMP(CURDATE())*1000;')->fetchColumn();
        break;
    case 'mrp':
        //$stmnt = $pdo->query('SELECT COUNT(DISTINCT(uuid)) FROM nm_sessions WHERE start >= UNIX_TIMESTAMP((LAST_DAY(NOW())+INTERVAL 1 DAY)-INTERVAL 1 MONTH)*1000');
        $stmnt = $pdo->query('SELECT COUNT(DISTINCT(uuid)) FROM nm_sessions WHERE start >= UNIX_TIMESTAMP(CURDATE()- INTERVAL 1 MONTH)*1000;');
        $res = $stmnt->fetchAll()[0];
        echo $res[0];
        break;
    case 'wrp':
        $stmnt = $pdo->query('SELECT COUNT(DISTINCT(uuid)) FROM nm_sessions WHERE start >= UNIX_TIMESTAMP(CURDATE()- INTERVAL 1 WEEK)*1000');
        $res = $stmnt->fetchAll()[0];
        echo $res[0];
        break;
    case 'drp':
        $stmnt = $pdo->query('SELECT COUNT(DISTINCT(uuid)) FROM nm_sessions WHERE start >= UNIX_TIMESTAMP(CURDATE())*1000');
        $res = $stmnt->fetchAll()[0];
        echo $res[0];
        break;
    case 'dpt':
        $stmnt = $pdo->query('SELECT SUM(time) FROM nm_sessions WHERE start >= (UNIX_TIMESTAMP(CURDATE())*1000)');
        echo formatMilliseconds($stmnt->fetchAll()[0][0]);
        break;
    case 'wpt':
        $stmnt = $pdo->query('SELECT SUM(time) FROM nm_sessions WHERE start >= UNIX_TIMESTAMP(CURDATE()- INTERVAL 1 WEEK)*1000');
        echo formatMilliseconds($stmnt->fetchAll()[0][0]);
        break;
    case 'tpt':
        $stmnt = $pdo->query('SELECT SUM(playtime) FROM nm_players');
        echo formatMilliseconds($stmnt->fetchAll()[0][0]);
        break;
    case 'versions':
        $stmnt2 = $pdo->query('SELECT DISTINCT COUNT(*) FROM nm_players;');
        $total = $stmnt2->fetchAll()[0][0];

        echo '<table class="data-table"><thead><tr class="header"><th>Version</th><th>Players</th><th>Population</th></tr></thead><tbody>';
        $stmnt = $pdo->query('SELECT DISTINCT(version) as version, count(*) AS count FROM nm_players GROUP BY version ORDER BY count DESC');
        foreach ($stmnt->fetchAll() as $item) {
            echo '<tr class="row"">
            <td>' . getVersion($item['version']) . '</td>
            <td>' . $item['count'] . '</td>
            <td>' . number_format((($item['count'] / $total) * 100), 2, '.', ' ') . '%</td>
            </tr>';
        }
        echo '</tbody></table>';
        break;
    case 'virtualhosts':
        $stmnt = $pdo->query('SELECT DISTINCT COUNT(*) FROM nm_logins;');
        $total = $stmnt->fetchAll()[0][0];

        echo '<table class="data-table"><thead><tr class="header"><th>Virtual Host</th><th>Players</th><th>Population</th></tr></thead><tbody>';
        $stmnt = $pdo->query('SELECT DISTINCT(vhost) as vhost, count(*) AS count FROM nm_logins GROUP BY vhost ORDER BY count DESC');
        foreach ($stmnt->fetchAll() as $item) {
            echo '<tr class="row"">
            <td>' . ($item['vhost'] == null ? "Unknown" : $item['vhost']) . '</td>
            <td>' . $item['count'] . '</td>
            <td>' . number_format((($item['count'] / $total) * 100), 2, '.', ' ') . '%</td>
            </tr>';
        }
        echo '</tbody></table>';
        break;
    case 'onlineplayers':
        $res = array();
        $stmnt = $pdo->prepare('SELECT TIME, ONLINE FROM nm_serverAnalytics WHERE `TIME` > ? ORDER BY `TIME`;');
        $stmnt->bindValue(1, round(microtime(true) * 1000) - 30 * 86400000, PDO::PARAM_INT);
        $stmnt->execute();
        foreach ($stmnt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            array_push($res, [(float)$row['TIME'], (int)$row['ONLINE']]);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
        break;
    case "is_enabled":
        echo isEnabled($_GET['variable']);
        break;
}

function isEnabled($variable)
{
    global $pdo;
    $stmnt = $pdo->prepare('SELECT value FROM nm_values WHERE variable=?;');
    $stmnt->bindParam(1, $variable, 2);
    $stmnt->execute();
    $result = $stmnt->fetch();
    return $result['value'];
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