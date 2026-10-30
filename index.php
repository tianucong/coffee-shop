<?php

if (session_status()===PHP_SESSION_NONE){
    session_start();
}

function is_logged(){
    return isset($_SESSION['user_id']);
}

function current_role(){
    return $_SESSION['role']??'guest';
}

function require_role(array $roles){
    if(!is_logged()){
        header("Location: login.php");exit;
    }
    if(!in_array($_SESSION['role'],$roles)){
        http_response_code(403);
        die("403 - akses ditolak");
    }
}
function get_discount_percent(){
    return (is_logged()&&current_role()==='customer')?10 :0;
}

