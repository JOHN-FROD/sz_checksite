<?php

include "db.php";

function delete_site($oldhostname)
{
    global $mysqli;
    echo "delete_site\n";
    $q = "delete from site where hostname = ?";
    $stmt = $mysqli->prepare($q);
    $stmt->bind_param("s", $oldhostname );
    $stmt->execute();
    $stmt->close();
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

$oldhostname = $_POST['oldhostname'];
$name = $_POST['name'];
$hostname = $_POST['hostname'];
checkname($name);
checkhostname($hostname);

delete_site($oldhostname);
insert_site($hostname,$name);
exit(0);


