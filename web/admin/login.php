<?php
include "../db.php";

$username = $_POST['username'];
$password = $_POST['password'];

global $mysqli;
$q = "select uname from user where uname=? and passwd=?";
$stmt = $mysqli->prepare($q);
$stmt->bind_param("ss", $username,$password);
$stmt->execute();
sleep( rand( 2, 4 ) );
if ($stmt->fetch()){
    echo "sucess";
    setcookie("session",$username,time()+604800);
}else {
    return false;
}





//$cont = 0;
//$lockout_time = 15;
//$account_locked = false;
//if($username == 'admin' && $password == 'root'){
//   echo "sucess";
//   $cont = 0;
//}else {
//    $cont++;
//    sleep( rand( 2, 4 ) );
//    if ($cont == 5){
//
//    }
//    return false;
//}
