<?php

require_once 'config.php';
require_once 'auth.php';

require_role(['cashier']);

$id=(int)($_POST['id']??0);
$me=$_SESSION['user_id'];

if(($_POST['action']??'')==='claim'){
    $stmt=mysqli_prepare($conn,"UPDATE orders SET cashier_id=?, status='processing' WHERE id=? AND cashier_id IS NULL");
    mysqli_stmt_bind_param($stmt,"ii",$me,$id);
    mysqli_stmt_execute($stmt);
}
elseif(($_POST['action']??'')==='done'){
    $stmt=mysqli_prepare($conn,"UPDATE orders SET status='done' WHERE id=? AND cashier_id = ?");
    mysqli_stmt_bind_param($stmt,"ii",$id,$me);
    mysqli_stmt_execute($stmt);
}
header ("Location: cashier.php");
exit;