<?php

require_once './Viewer/Bootstrap.php';

$image_id = getDatafromUri(1);
$getid = $_database->query("select * from ".DB_PREFIX."image where im_id=".$image_id);
$image = $getid->fetch(PDO::FETCH_ASSOC);
if (!$image || strlen($image['im_image']) == 1)
    {
    $getid = $_database->query("select * from ".DB_PREFIX."image where im_id=0");
    $image = $getid->fetch(PDO::FETCH_ASSOC);
    }

switch ($image['im_type'])
    {
    case 'png' :
        $mimetype = 'image/png';
        break;
    case 'gif' :
        $mimetype = 'image/gif';
        break;
    case 'webp' :
        $mimetype = 'image/webp';
        break;
    case 'bpg' :
        $mimetype = 'image/bpg';
        break;
    case 'flif' :
        $mimetype = 'image/flif';
        break;
    case 'jpg' :
    default:
        $mimetype = 'image/jpeg';
        break;
    }

header('Cache-Control: max-age='.(3600*24));
header('Content-Type: '.$mimetype);
print $image['im_image'];

?>