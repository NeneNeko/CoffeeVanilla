<?php
/**
 * Project name : Manga Viewer
 * Code name : Coffee & Vanilla
 * Begin : Saturday, ‎January ‎30, ‎2016, ‏‎12:45:25
 * Author : NeneNeko
 * (c) Copyright : nene.neko@msn.com
 */


if(!in_array('mod_rewrite', apache_get_modules()))
    {
    echo 'ยังไม่เปิดการใช้งาน mod_rewrite';
    }


ob_start();
require_once 'Viewer/Bootstrap.php';

$action = getDatafromUri(0);

switch ($action)
    {

    case 'setting' :
        require_once 'Viewer/Setting.php';
        break;

    default :
        require_once 'Viewer/View.php';
        break;

    }

$contents_data = ob_get_contents();
ob_end_clean();
require_once 'Viewer/Theme.php';