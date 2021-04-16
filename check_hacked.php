<?php
include('db.php');

$hacked = 0;

if ($_SERVER['argc'] < 2) {
    echo("php check_hacked.php www.site.com\n");
    exit(0);
}
$hostname = $_SERVER['argv'][1];
//$hostname = 'www.szu.edu.cn';
$url = "http://".$hostname;
$html = file_get_contents($url);
if(!$html) {
    $url = "https://".$hostname;
    $html =  file_get_contents($url);
}

$file = fopen("./data/hacked_word2.txt", "r") or exit("无法打开文件!");
// 读取文件每一行，直到文件结尾
while(!feof($file))
{
    $word = fgets($file);
    $word = trim($word);
    if(stristr($html,$word)){
        $hacked = 1;
        $keyword = $word;
        break;
    }
}
fclose($file);

//将数据填入数据库
global $mysqli;
$q = "replace into hacked_site values(?, now(),?,?)";
$stmt = $mysqli->prepare($q);
$stmt->bind_param("sis", $hostname, $hacked, $keyword);
$stmt->execute();
$stmt->close();






