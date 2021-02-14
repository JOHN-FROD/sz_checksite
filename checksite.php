<?php

// php checksite.php www.site.com timeout
// 检查 www.site.com 的IPv4/IPv6 http/https/http2 支持

include ("db.php");

function update_last($hostname, $ipv4, $aaaa, $ipv6, $httpsv4, $httpsv6, $http2v4, $http2v6)
{
    global $mysqli;
    echo "update_last\n";
    $q = "replace into status_last values(?, now(),?,?,?,?,?,?,?)";
    $stmt = $mysqli->prepare($q);
    $stmt->bind_param("siiiiiii", $hostname, $ipv4, $aaaa, $ipv6, $httpsv4, $httpsv6, $http2v4, $http2v6);
    $stmt->execute();
    $stmt->close();
}

function insert_log($hostname, $ipv4, $aaaa, $ipv6, $httpsv4, $httpsv6, $http2v4, $http2v6)
{
    global $mysqli;
    echo "insert_log\n";
    $q = "insert into status_log values(?, now(), ?,?,?,?,?,?,?)";
    $stmt = $mysqli->prepare($q);
    $stmt->bind_param("siiiiiii", $hostname, $ipv4, $aaaa, $ipv6, $httpsv4, $httpsv6, $http2v4, $http2v6);
    $stmt->execute();
    $stmt->close();
}

#把域名加入allok_first表
function update_allok($hostname)
{
    #先判断该域名是否已经在allok_first中
    global $mysqli;
    echo "update_allok\n";
    $q = "select count(*) from allok_first where hostname=?";
    $stmt = $mysqli->prepare($q);
    $stmt->bind_param("s", $hostname);
    $stmt->execute();
    $stmt->bind_result($cnt);
    $stmt->store_result();
    $stmt->fetch();
    $stmt->close();
    if ($cnt == 1)
        return;
    
    #没有的话就把域名加入该表中
    $q = "insert into allok_first values(?, now())";
    $stmt = $mysqli->prepare($q);
    $stmt->bind_param("s", $hostname);
    $stmt->execute();
    $stmt->close();
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
        exit(0);
    }
}

//检查传入的参数个数，如果小于2，则输出正确格式
if ($_SERVER['argc'] < 2) {
    echo("php checksite.php www.site.com [ timeout ]\n");
    exit(0);
}

$hostname = $_SERVER['argv'][1];        //取出传入参数的第2个值（域名）
checkvalue($hostname);              //检查域名是否有非法字符

$timeout = 4;               //默认超时时间为4
if ($_SERVER['argc'] > 2)       //如果传入的参数超过2个
    $timeout = intval($_SERVER['argv'][2]);     //取出第3个参数并取整作为超时时间

echo "$hostname $timeout\n";        //输出域名和超时时间

//$dnssec = 0;
$ipv4 = 0;
$aaaa = 0;
$ipv6 = 0;
$httpsv4 = 0;
$httpsv6 = 0;
$http2v4 = 0;
$http2v6 = 0;

//检查DNSSEC
/*$retval = 1;
$msg = system("bash network-probes/100-dns-dnssec.sh $hostname", $retval);
if ($retval == 0)
    $dnssec = 1;*/

//检查httpv4
$retval = 1;
$msg = system("bash network-probes/200-http-ipv4.sh http://$hostname $timeout", $retval);
//如果返回值是0的话就证明IPv4有http连接
if ($retval == 0)
    $ipv4 = 1;

#检查是否有IPv6解析
#检查http IPv6是否可以访问
$msg = system("bash network-probes/100-dns-aaaa.sh $hostname", $retval);
//如果返回值是0的话就证明有IPv6解析
if ($retval == 0) {
    $aaaa = 1;
    #有IPv6解析就尝试IPv6连接，返回值为0证明有IPv6连接
    $msg = system("bash network-probes/200-http-ipv6.sh http://$hostname $timeout", $retval);

    if ($retval == 0)
        $ipv6 = 1;
}

#检查httpsv4/v6, http2 v4/v6
$msg = system("bash network-probes/200-http-ipv4.sh https://$hostname $timeout", $retval);
if ($retval == 0) {
    $httpsv4 = 1;
    $msg = system("bash network-probes/200-http2-ipv4.sh https://$hostname $timeout", $retval);
    if ($retval == 0)
        $http2v4 = 1;
}
if ($aaaa == 1) {
    $msg = system("bash network-probes/200-http-ipv6.sh https://$hostname $timeout", $retval);
    if ($retval == 0) {
        $httpsv6 = 1;
        $msg = system("bash network-probes/200-http2-ipv6.sh https://$hostname $timeout", $retval);
        if ($retval==0)
            $http2v6=1;
    }
}

//如果全部都是通过则把域名加入allok_first表（全部通过）
if ($aaaa + $ipv6 + $httpsv4 + $httpsv6 + $http2v4 + $http2v6 == 6)
    update_allok($hostname);

echo ("$ipv4 $httpsv4 $http2v4/$aaaa $ipv6 $httpsv6 $http2v6\n");


// 检查status_last 是否有记录
$q = "select ipv4, aaaa, ipv6, httpsv4, httpsv6, http2v4, http2v6 from status_last where hostname=?";
$stmt = $mysqli->prepare($q);
$stmt->bind_param("s", $hostname);
$stmt->execute();
$stmt->bind_result( $oldipv4, $oldaaaa, $oldipv6, $oldhttpsv4, $oldhttpsv6, $oldhttp2v4, $oldhttp2v6);
$stmt->store_result();
if (!$stmt->fetch()) {    // 第一次记录
    $stmt->close();

    update_last($hostname, $ipv4, $aaaa, $ipv6, $httpsv4, $httpsv6, $http2v4, $http2v6);

    insert_log($hostname, $ipv4, $aaaa, $ipv6, $httpsv4, $httpsv6, $http2v4, $http2v6);

    exit(0);
}
$stmt->close();

// 之前有过记录
update_last($hostname, $ipv4, $aaaa, $ipv6, $httpsv4, $httpsv6, $http2v4, $http2v6);

if (($ipv4 != $oldipv4) || ($aaaa != $oldaaaa) || ($ipv6 != $oldipv6)
    || ($httpsv4 != $oldhttpsv4) || ($httpsv6 != $oldhttpsv6)
    || ($http2v4 != $oldhttp2v4) || ($http2v6 != $oldhttp2v6))
    insert_log($hostname, $ipv4, $aaaa, $ipv6, $httpsv4, $httpsv6, $http2v4, $http2v6);

