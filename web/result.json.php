<?php

header('Content-Type: application/json');

include "db.php";

echo "{";


$q = "select tm from status_last order by tm desc limit 1";
$stmt = $mysqli->prepare($q);
$stmt->execute();
$stmt->bind_result($lasttm);
$stmt->store_result();
$stmt->fetch();
$stmt->close();

echo "\"endDatetime\": \"$lasttm\",\n";
echo "\"result\": [\n";

//$q = "select site.hostname, site.name, status_last.ipv4, status_last.httpsv4, status_last.http2v4, status_last.aaaa, status_last.ipv6, status_last.httpsv6, status_last.http2v6 from `site` left join status_last on site.hostname = status_last.hostname order by (status_last.ipv4 + status_last.httpsv4 + status_last.http2v4 + status_last.aaaa + status_last.ipv6 + status_last.httpsv6 + status_last.http2v6 ) desc limit 50";
$q = "select site.hostname, site.name, status_last.ipv4, status_last.httpsv4, status_last.http2v4, status_last.aaaa, status_last.ipv6, status_last.httpsv6, status_last.http2v6, hacked_site.hacked, hacked_site.keyword
from site left join status_last on site.hostname = status_last.hostname
left join hacked_site on site.hostname = hacked_site.hostname order by (status_last.ipv4 + status_last.httpsv4 + status_last.http2v4 + status_last.aaaa + status_last.ipv6 + status_last.httpsv6 + status_last.http2v6 ) desc limit 50";
$stmt = $mysqli->prepare($q);
$stmt->execute();
$cnt = 0;
$stmt->bind_result($hostname, $name, $ipv4, $httpsv4, $http2v4, $aaaa, $ipv6, $httpsv6, $http2v6,  $hacked, $keyword);
$stmt->store_result();
$isfirst = 1;
while ($stmt->fetch()) {
    if ($isfirst == 1)  {
        $isfirst = 0;
    } else
        echo ",\n";
    $cnt ++;
    echo "{ \"cnt\": ".$cnt.", ";
    echo "\"hostname\": \"$hostname\", ";
    echo "\"name\": \"$name\", ";
//    echo "\"dnssec\": "; echo intval($dnssec); echo ",";
    echo "\"ipv4\": "; echo intval($ipv4); echo ",";
    echo "\"httpsv4\": "; echo intval($httpsv4); echo ",";
    echo "\"http2v4\": "; echo intval($http2v4); echo ",";
    echo "\"aaaa\": "; echo intval($aaaa); echo ",";
    echo "\"ipv6\": "; echo intval($ipv6); echo ",";
    echo "\"httpsv6\": "; echo intval($httpsv6); echo ",";
    echo "\"http2v6\": "; echo intval($http2v6); echo ",";
    echo "\"hacked\": "; echo intval($hacked); echo ",";
    echo "\"keyword\": "; echo "\"".$keyword."\""; echo ",";
    echo "\"score\": ";
    $score = ( $ipv4 * 4 + $httpsv4 + $http2v4 + $aaaa + $ipv6 + $httpsv6 + $http2v6) * 10;
//    if ($score == 100)
//        $score += get_addon($hostname);
    echo $score;
    echo "}";
}
echo "\n";
$stmt->close();
echo "]\n";
echo "}\n";
?>
