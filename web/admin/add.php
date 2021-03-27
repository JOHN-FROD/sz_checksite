<?php

include "../db.php";


function valid($session){
    global $mysqli;
    $q = "select uname from user where uname=? ";
    $stmt = $mysqli->prepare($q);
    $stmt->bind_param("s", $session);
    $stmt->execute();
    if ($stmt->fetch()){
    }else {
        header("location: login.html");
        die("您无权访问,请先登陆");
    }
}


function insert_site($hostname,$name)
{
    global $mysqli;
    echo "insert_site\n";
    $q = "insert into site values(?, ?)";
    $stmt = $mysqli->prepare($q);
    $stmt->bind_param("ss", $hostname,$name );
    $stmt->execute();
    $stmt->close();
}


function checkname($str) {
    for ($i = 0; $i < strlen($str); $i++) {
//        if (ctype_alnum($str[$i]))      //检查是否为字母或数字
//            continue;
        if (strchr(".", $str[$i]))  {
            //检查是否为.
            echo "$str 中第 $i 非法字符 $str[$i]";
            exit(0);
        }
        if (strchr("-", $str[$i])) {
            //检查是否为-
            echo "$str 中第 $i 非法字符 $str[$i]";
            exit(0);
        }
        if (strchr("_", $str[$i])) {
            //检查是否为_
            echo "$str 中第 $i 非法字符 $str[$i]";
            exit(0);
        }
        if (strchr("/", $str[$i])) {
            //检查是否为/
            echo "$str 中第 $i 非法字符 $str[$i]";
            exit(0);
        }
        if (strchr("'", $str[$i])) {
            //检查是否为'
            echo "$str 中第 $i 非法字符 $str[$i]";
            exit(0);
        }
        if (strchr("\"", $str[$i])) {
            //检查是否为"
            echo "$str 中第 $i 非法字符 $str[$i]";
            exit(0);
        }
    }
}

function checkhostname($str) {
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
        exit(0);
    }
}

$session = $_COOKIE['session'];
valid($session);

$name = $_POST['name'];
$hostname = $_POST['hostname'];
checkname($name);
checkhostname($hostname);

global $mysqli;
$q = "select name from site where name=?";
$stmt = $mysqli->prepare($q);
$stmt->bind_param("s", $name);
$stmt->execute();
$stmt->bind_result( $oldname);
$stmt->store_result();
if ($stmt->fetch()) {
    $stmt->close();
    //已经存在记录
    echo "已经存在记录";
    exit(0);
}
$stmt->close();

insert_site($hostname,$name);
exit(0);