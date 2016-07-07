<?php

ob_start();
session_start();
error_reporting ( E_ALL );
set_time_limit ( 0 );
ini_set ( 'memory_limit', '-1' );
date_default_timezone_set ( 'Asia/Bangkok' );

$_assets = array();
$_mainmenu = array();
$_fatalerror = false;

require_once 'Viewer/Config.php';
require_once 'Viewer/Function.php';
require_once 'Viewer/Library/php-image/PHPImage.php';
require_once 'Viewer/Library/htmlparser/htmlParser.php';
require_once 'Viewer/Library/bbcodeparser/bbcodeparser.php';

require_once 'Viewer/Assets.php';
$_assets[] = 'font-awesome';
$_assets[] = 'jquery';
$_assets[] = 'colortip';
$_assets[] = 'common';
$_assets[] = 'pace';

# เมนูหลักเริ่มต้นที่ลำดับ 1
$_mainmenu[] = ['หน้าหลัก', URI_PATH.'/'];
$_mainmenu[] = ['รายชื่อทั้งหมด', URI_PATH.'/all'];
$_mainmenu[] = ['การตั้งค่า', URI_PATH.'/setting'];
$_mainmenu[] = ['เกี่ยวกับ', URI_PATH.'/setting/about'];
$_mainactive = '1';

if(!in_array('mod_rewrite', apache_get_modules()))
    {
    $_title = 'ยังไม่เปิดการใช้งาน mod_rewrite';
    $_error_message = 'ยังไม่เปิดการใช้งาน mod_rewrite เช็คการเปิดใช้งาน webservice';
    $_fatalerror = true;
    require_once 'Viewer/Error.php';
    }

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
    $_title = 'เชื่อมต่อกับฐานข้อมูลไม่ได้';
    $_error_message = 'เชื่อมต่อกับฐานข้อมูลไม่ได้ จากข้อผิดพลาด: ' . $e->getMessage();
    $_fatalerror = true;
    require_once 'Viewer/Error.php';
    }

