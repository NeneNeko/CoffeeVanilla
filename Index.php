<?php
/**
 * Project name : Neko Viewer
 * Code name : Coffee & Vanilla
 * Begin : Saturday, ‎January ‎30, ‎2016, ‏‎12:45:25
 * Author : NeneNeko
 * (c) Copyright : nene.neko@msn.com
 */

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

require_once 'Viewer/Theme.php';

?>