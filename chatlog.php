<?php
include 'protected/config.php';
include 'inc/php/dep/MinecraftColorcodes.php';

$colorCodes = new MinecraftColorcodes();

$uuid = $_GET['uuid'];
if (!isset($_GET['uuid'])) {
    die('no uuid');
}
if (empty($_GET)) {
    die('no uuid');
}

$stmnt = $pdo->prepare('SELECT `creator`, `tracked`, `time` FROM nm_chatlogs WHERE `uuid`=?;');
$stmnt->bindParam(1, $uuid, PDO::PARAM_STR);
$stmnt->execute();
if ($stmnt->rowCount() == 0) {
    die('invalid uuid');
}
$data = $stmnt->fetchAll()[0];
$creator = $data['creator'];
$tracked = $data['tracked'];
$time = $data['time'];

function getName($uuid)
{
    if ($uuid == '1a6b7d7c-f2a8-4763-a9a8-b762f309e84c') {
        return 'CONSOLE';
    }
    global $pdo;
    $sql = 'SELECT `username` FROM nm_players WHERE `uuid`=?;';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(1, $uuid, PDO::PARAM_STR);
    $statement->execute();
    $row = $statement->fetch();
    return $row['username'];
}

$name = getName($tracked);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="HandheldFriendly" content="true">
    <title>ChatLog - NetworkManager</title>
    <link rel="stylesheet" href="inc/css/style.css">
    <link rel="stylesheet" href="inc/css/grid.css">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="title">ChatLog of <?php echo $name; ?>
                <small style="color: lightgray"> by <?php echo getName($creator); ?>
                    on <?php echo date('l jS \of F Y h:i:s A', round($time / 1000)); ?></small>
            </h1>
        </div>
        <?php
        echo '<div class="col-12" style="font-size: 18px;">
            <table class="tbl" style="width: 100%;">
                <tbody>';

        $stmnt = $pdo->prepare('SELECT `message`, `server`, `time` FROM nm_chat WHERE `uuid`=? AND `time` <=? ORDER BY `id` DESC LIMIT 25;');
        $stmnt->bindParam(1, $tracked);
        $stmnt->bindParam(2, $time);
        $stmnt->execute();
        $res = $stmnt->fetchAll();
        echo '<tr>
        <td><b>Player</b></td>
        <td><b>Message</b></td>
        <td><b>Server</b></td>
        <td class="text-right"><b>Time</b></td>
        </tr>';
        foreach ($res as $item) {
            if (substr($item['message'], 0, 1) === "/") {
                echo '<tr>
                </tr>';
            } else {
                echo '<tr>
                <td><img src="https://crafatar.com/avatars/' . $tracked . '?size=20"> ' . $name . '</td>
                <td>' . $colorCodes->convert($item['message']) . '</td>
                <td>' . htmlspecialchars($item['server']) . '</td>
                <td class="text-right">' . date('l jS \of F Y h:i:s A', round($item['time'] / 1000)) . '</td>
                </tr>';
            }
        }
        echo '</tbody></table>
        </div>';
        ?>
    </div>
</div>
</body>
</html>