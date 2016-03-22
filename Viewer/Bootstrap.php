<?php

session_start();
error_reporting ( E_ALL );
set_time_limit ( 0 );
ini_set ( 'memory_limit', '-1' );
date_default_timezone_set ( 'Asia/Bangkok' );

$_assets = array();
$_mainmenu = array();

require_once 'Config.php';
require_once 'Assets/Assets.php';
require_once 'Viewer/Function.php';
require_once 'Viewer/PHPImage.php';

// เชื่อมต่อฐานข้อมูล
try
    {
    $dsn = 'mysql:dbname='.DB_NAME.';charset='.DB_CHARSET.';host='.DB_HOST.';port=3306';
    $_database = new PDO($dsn, DB_USER, DB_PASSWORD);
    // อ่านการตั้งค่าระบบ
    $getconfig = $_database->query("select * from ".DB_PREFIX."setting");
    foreach ($getconfig->fetchAll(PDO::FETCH_ASSOC) as $value)
        $_settings[$value['se_name']] = $value['se_variable'];
    }
catch (PDOException $e)
    {
    echo 'เชื่อมต่อกับฐานข้อมูลไม่ได้ จากข้อผิดพลาด: ' . $e->getMessage();
    }


