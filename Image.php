<?php

require_once './Viewer/Bootstrap.php';

$image_id = (int)getDatafromUri(1);
$getid = $_database->query("select * from ".DB_PREFIX."image where im_id=".$image_id);
$image = $getid->fetch(PDO::FETCH_ASSOC);
if (!$image || strlen($image['im_image']) == 1)
    {
    $getid = $_database->query("select * from ".DB_PREFIX."image where im_id=1");
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

$hashID = md5($image['im_image']);
$expireTime = time() + eval('return '.$_settings['cache_time'].';');
$wasUpdated = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) and 
    strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == strtotime($image['im_date']));
$IDsMatch = (isset($_SERVER['HTTP_IF_NONE_MATCH']) and 
    $hashID == $_SERVER['HTTP_IF_NONE_MATCH']);

header('Content-Type: '.$mimetype);
header('Cache-Control: max-age='.$expireTime);
header('Expires: '.gmdate('D, d M Y H:i:s', $expireTime).' GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s', strtotime($image['im_date'])).' GMT');
header('Etag: '.$hashID);
header_remove('Pragma');

if ($wasUpdated or $IDsMatch)
    {
    header('HTTP/1.1 304 Not Modified');
    header('Connection: close');
    }
else
    {
    header('HTTP/1.1 200 OK');
    header('Content-Length: '. strlen($image['im_image']));
    print $image['im_image'];
    }    

?>