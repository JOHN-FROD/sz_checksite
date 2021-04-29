<?php

// php check.php [timeout]

include ("db.php");

$timeout = 4;
if ($_SERVER['argc'] > 1 )
	$timeout = $_SERVER['argv'][1];

$q = "select hostname from site";
$stmt = $mysqli->prepare($q);
$stmt->execute();
$stmt->bind_result($hostname);
$stmt->store_result();
while ($stmt->fetch()) {
    system("php checksite.php $hostname $timeout");
    system("php check_hacked.php $hostname");
}
$stmt->close();
system("php check_speed.php");

