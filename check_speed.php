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
    $mdev = "";
    if (($ipv6==1) || ($httpsv6==1) || ($http2v6==1)) {
        exec("ping6 -c 10 -q $hostname", $output);
        print("check $hostname speed \n");
        //var_dump($output);
        // $output='5 packets transmitted, 5 received, 90% packet loss, time 4009ms';
        preg_match("/[0-9]+%/",$output[3],$match);
        $loss = $match[0];
        print('loss='.$loss."\n");
        // print($output[4]);
        if ($loss!= "100%" ) {
            preg_match_all("/[0-9]\.[0-9][0-9][0-9]/",$output[4],$matches);
            // var_dump($matches);
            $min = $matches[0][0];
            $avg = $matches[0][1];
            $max = $matches[0][2];
            $mdev = $matches[0][3];
    
            print('min='.$matches[0][0]);
            print('avg='.$matches[0][1]);
            print('max='.$matches[0][2]);
            print('mdev='.$matches[0][3]);
        }
    }
    global $mysqli;
    $q = "replace into speed values(?, now(),?,?,?,?,?)";
    $speed = $mysqli->prepare($q);
    $speed->bind_param("ssssss", $hostname, $loss, $min, $avg, $max, $mdev);
    $speed->execute();
    $speed->close();
}
$stmt->close();
