<?php
include("db.php");


$q = "select hostname, ipv6, httpsv6, http2v6 from status_last";
$stmt = $mysqli->prepare($q);
$stmt->execute();
$stmt->bind_result($hostname, $ipv6, $httpsv6, $http2v6);
$stmt->store_result();
while ($stmt->fetch()) {
    $output = array();
    $loss = "";
    $min = "";
    $avg = "";
    $max = "";
    if (($ipv6==1) || ($httpsv6==1) || ($http2v6==1)) {
        print("check $hostname speed \n");
        exec("python3 ./tping/tping.py -d $hostname -p 80 -6", $output);
        //var_dump($output);
        $pattern = "/avg_ms:[\s]+([0-9]+\.[0-9][0-9][\s]ms)/";
        $match = preg_match($pattern,$output[10],$matches);
        $avg = $matches[1];
        $pattern = "/f_rate:[\s]+([0-9]\.[0-9][0-9][\s])/";
        $match = preg_match($pattern,$output[10],$matches);
        $loss = $matches[1];

        array_pop($output);
        $min = min($output);
        $max = max($output);
        print("loss=".$loss."; max=".$max."; min=".$min."; avg=".$avg."\n");
    }
    global $mysqli;
    $q = "replace into speed values(?, now(),?,?,?,?)";
    $speed = $mysqli->prepare($q);
    $speed->bind_param("sssss", $hostname, $loss, $min, $avg, $max);
    $speed->execute();
    $speed->close();
}
$stmt->close();

