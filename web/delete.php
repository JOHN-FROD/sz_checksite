<?php

include "db.php";

function delete_site($hostname)
{
    global $mysqli;
    echo "delete_site\n";
    $q = "delete from site where hostname = ?";
    $stmt = $mysqli->prepare($q);
    $stmt->bind_param("s", $hostname );
    $stmt->execute();
    $stmt->close();
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

$hostname = $_POST['hostname'];
checkhostname($hostname);
delete_site($hostname);
exit(0);