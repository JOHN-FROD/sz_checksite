<?php

$db_host  =  "localhost";
$db_user  =  "root";
$db_passwd  =  "";
$db_dbname  =  "checksite";

$mysqli  =  new mysqli($db_host, $db_user, $db_passwd, $db_dbname);
if (mysqli_connect_error()){
    echo mysqli_connect_error();
}

#检查域名是否有非法字符
function checkvalue($str) {
    for ($i = 0; $i < strlen($str); $i++) {
        if (ctype_alnum($str[$i]))      //检查是否为字母或数字
            continue;
        if (strchr(".", $str[$i]))      //检查是否为.
            continue;
        if (strchr("-", $str[$i]))      //检查是否为-
            continue;
        if (strchr("_", $str[$i]))      //检查是否为_
            continue;
        echo "$str 中第 $i 非法字符 $str[$i]";       //假若全都不是则输出报错信息，并退出
        exit(1);
    }
}


$file_group  =  fopen("sz.txt", "r");
if (!$file_group) {
    echo "file sz.txt open error\n";
    exit(1);
}



// first delete all

$q = "delete from `site`";
$stmt = $mysqli->prepare($q);
$stmt->execute();
$stmt->close();

// insert into 'site'
$file_site = fopen('sz.txt', "r");
$cnt = 0;
while (($buf = fgets($file_site, 4096)) !== false) {
    echo $buf;
    $s = preg_split("/[\s,]+/", $buf);
    if ($s[0][0] == "#")
        continue;
    checkvalue($s[1]);

    $cnt++;

    // insert into site
    $q = "replace into site values(?,?)";
    $stmt = $mysqli->prepare($q);
    $stmt->bind_param("ss", $s[1], $s[0]);
    $stmt->execute();
    $stmt->close();
}
fclose($file_site);

?>
