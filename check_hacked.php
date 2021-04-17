<?php
include("db.php");

ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
$hacked = 0;

if ($_SERVER['argc'] < 2) {
    echo("php check_hacked.php www.site.com\n");
    exit(0);
}
$hostname = $_SERVER['argv'][1];
//$hostname = 'www.szu.edu.cn';
$url = "http://".$hostname;
// echo $url;
$html = file_get_contents($url);
if(!$html) {
    $url = "https://".$hostname;
    $html =  file_get_contents($url);
}
//echo $html;

$file = fopen("./data/keyword.txt", "r") or exit("无法打开文件!");
// 读取文件每一行，直到文件结尾
while(!feof($file))
{
    $word = fgets($file);
    $word = trim($word);
    //echo $word;
    if(stristr($html,$word)){
        $hacked = 1;
        $keyword = $word;
        break;
    }
}
fclose($file);

if($hacked===1){
    echo 'hacked:';
    echo $keyword;
    echo("\n");
}else{
    echo("normal\n");
}

// 将数据填入数据库
global $mysqli;
$q = "replace into hacked_site values(?, now(),?,?)";
$stmt = $mysqli->prepare($q);
$stmt->bind_param("sis", $hostname, $hacked, $keyword);
$stmt->execute();
$stmt->close();






