<?php
$db = mysqli_connect ('127.0.0.1','root', '', 'tutorial');

if(mysqli_connect_errno()){
    echo 'Database connect failed with following errors : '. mysqli_connect_error();
    die();
}
require_once $_SERVER['DOCUMENT_ROOT'].'/ecommerce/config.php';
require_once BASEURL. 'helpers/helpers.php';
