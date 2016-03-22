<?php

require_once './Viewer/Bootstrap.php';

$input = filter_input_array(INPUT_POST);
$action = isset($input['action']) ? $input['action'] : getDatafromUri(1);

switch ($action)
    {
    case 'edit':
        if(isset($input['ch_number']))
            {
            $input['ch_number'] = addslashes(trim(($input['ch_number'])));
            $_database->query("update ".DB_PREFIX."chapter set ch_number='".$input['ch_number']."' where ch_id=".$input['ch_id']);
           echo json_encode(['บันทึกตอน "'.$input['ch_number'].'" สำเร็จ']);
            }
        elseif(isset($input['ch_title']))
            {
            $input['ch_title'] = addslashes(trim(($input['ch_title'])));
            $_database->query("update ".DB_PREFIX."chapter set ch_title='".$input['ch_title']."' where ch_id=".$input['ch_id']);
           echo json_encode(['บันทึกชื่อตอน "'.$input['ch_title'].'" สำเร็จ']);
            }
        elseif(isset($input['ch_uri']))
            {
            $input['ch_uri'] = addslashes(trim(($input['ch_uri'])));
            $_database->query("update ".DB_PREFIX."chapter set ch_uri='".$input['ch_uri']."' where ch_id=".$input['ch_id']);
           echo json_encode(['บันทึกที่อยู่ "'.$input['ch_uri'].'" สำเร็จ']);
            }
        elseif(isset($input['ch_date']))
            {
            $input['ch_date'] = addslashes(trim(($input['ch_date'])));
            $_database->query("update ".DB_PREFIX."chapter set ch_date='".$input['ch_date']."' where ch_id=".$input['ch_id']);
           echo json_encode(['บันทึกสำเร็จ']);
            }
        else
            {
            echo json_encode(['บันทึกล้มเหลว']);
            }
        break;

    case 'deleteimg':
        $chapter_id = getDatafromUri(2);
        $image_id = getDatafromUri(3);
        if($chapter_id && $image_id)
            {
            $getchapter = $_database->query("select * from ".DB_PREFIX."chapter where ch_id=".$chapter_id);
            $chapter = $getchapter->fetch(PDO::FETCH_ASSOC);
            $blob_id = array_diff(toArray($chapter['ch_image_id']), toArray($image_id));
            $_database->query("update ".DB_PREFIX."chapter set ch_image_id ='".implode(',', $blob_id)."' where ".DB_PREFIX."chapter.ch_id=".$chapter_id);
            $_database->query("delete from ".DB_PREFIX."image where ".DB_PREFIX."image.im_id=".$image_id);
            header('Location:'.$_SERVER['HTTP_REFERER']);
            }
        break;

    case 'deletechapter':
        $chapter_id = getDatafromUri(2);
        if($chapter_id)
            {
            $getchapter = $_database->query("select * from ".DB_PREFIX."chapter where ch_id=".$chapter_id);
            $chapter = $getchapter->fetch(PDO::FETCH_ASSOC);
            foreach (toArray($chapter['ch_image_id']) as $image_id)
                $_database->query("delete from ".DB_PREFIX."image where ".DB_PREFIX."image.im_id=".$image_id);
            $_database->query("delete from ".DB_PREFIX."chapter where ".DB_PREFIX."chapter.ch_id=".$chapter_id);
            header('Location:'.$_SERVER['HTTP_REFERER']);
            }
        break;

    case 'deletename':
        $name_id = getDatafromUri(2);
        if($name_id)
            {
            $getchapter = $_database->query("select * from ".DB_PREFIX."chapter where ch_name_id=".$name_id);
            $chapters = $getchapter->fetchAll(PDO::FETCH_ASSOC);
            foreach ($chapters as $chapter)
                {
                foreach (toArray($chapter['ch_image_id']) as $image_id)
                    $_database->query("delete from ".DB_PREFIX."image where ".DB_PREFIX."image.im_id=".$image_id);
                $_database->query("delete from ".DB_PREFIX."chapter where ".DB_PREFIX."chapter.ch_id=".$name_id);
                }
            $getname = $_database->query("select * from ".DB_PREFIX."name where na_id=".$name_id);
            $name = $getname->fetch(PDO::FETCH_ASSOC);
            $_database->query("delete from ".DB_PREFIX."image where ".DB_PREFIX."image.im_id=".$name['na_image_id']);
            $_database->query("delete from ".DB_PREFIX."name where ".DB_PREFIX."name.na_id=".$name_id);
            header('Location:'.$_SERVER['HTTP_REFERER']);
            }
        break;

    case 'addname':
        try
            {
            CreateDirectory($_settings['cache_directory']);
            $filename = $_settings['cache_directory'].'/temp_cover.jpg';
            move_uploaded_file($_FILES['cover']['tmp_name'], $filename);
            $cover = file_get_contents($filename);
            $sql = "insert into cv_image (im_id, im_image, im_type) values (null, ?, ?)";
            $img = $_database->prepare($sql);
            $img->bindParam(1, $cover, PDO::PARAM_LOB);
            $img->bindValue(2, 'jpg', PDO::PARAM_STR);
            $img->execute();
            $img_id = $_database->lastInsertId();
            $input['name'] = trim($input['name']);
            $name_uri = strtolower(str_replace([' - ', '  ', ' '], '-', (preg_replace('/[^A-Za-z0-9 \-]/', '', $input['name']))));
            $input['details']= trim($input['details']);
            $sql = "insert into ".DB_PREFIX."name (na_id, na_sub_id, na_name, na_name_uri, na_detail, na_image_id, na_uri, na_uri_template, na_last, na_end, na_date) values (null, ?, ?, ?, ?,?, ?, ?, null, '0', CURRENT_TIMESTAMP)";
            $name = $_database->prepare($sql);
            $name->bindParam(1, $input['sub_id'], PDO::PARAM_INT);
            $name->bindParam(2, $input['name'], PDO::PARAM_STR);
            $name->bindParam(3, $name_uri, PDO::PARAM_STR);
            $name->bindParam(4, $input['details'], PDO::PARAM_STR);
            $name->bindParam(5, $img_id, PDO::PARAM_STR);
            $name->bindParam(6, $input['uri'], PDO::PARAM_STR);
            $name->bindParam(7, $input['uri_template'], PDO::PARAM_STR);
            $name->execute();
            $_database->lastInsertId();
            header('Location:/'.$name_uri);
        }
        catch (PDOException $e)
        {
            echo 'เพิ่มข้อมูลไม่ได้ จากข้อผิดพลาด: ' . $e->getMessage();
        }
        break;

    case 'crop':
        //var_dump($input);
        $getid = $_database->query("select * from ".DB_PREFIX."image where im_id=".$input['image_id']);
        $imagesource = $getid->fetch(PDO::FETCH_ASSOC);
        $img = imagecreatefromstring($imagesource['im_image']);
        $image = new PHPImage();
        $image->setResource($img);
        $image->crop($input['x'], $input['y'], $input['w'], $input['h']);
        if ($imagesource['im_type']== 'jpg')
            $image->setOutput('jpg', $_setting['jpeg_quality']);
        else
            $image->setOutput('png', $_setting['png_compression']);
        ob_start();
        $image->show();
        $imagedata = ob_get_contents();
        ob_end_clean();
        $sql = "update ".DB_PREFIX."image set im_image =? where im_id=?";
        $img = $_database->prepare($sql);
        $img->bindParam(1, $imagedata, PDO::PARAM_LOB);
        $img->bindParam(2, $input['image_id'], PDO::PARAM_INT);
        $img->execute();
        header('Location:'.$_SERVER['HTTP_REFERER']);
        break;

    case 'exportchapter' :
        $chapter_id = getDatafromUri(2);
        CreateDirectory($_settings['export_directory']);
        if($chapter_id)
            {
            $getchapter = $_database->query("
            select ".DB_PREFIX."name.na_id, ".DB_PREFIX."name.na_name, ".DB_PREFIX."chapter.ch_name_id,
            ".DB_PREFIX."chapter.ch_number, ".DB_PREFIX."chapter.ch_id, ".DB_PREFIX."chapter.ch_image_id
            from ".DB_PREFIX."name inner join ".DB_PREFIX."chapter
            on ".DB_PREFIX."name.na_id=".DB_PREFIX."chapter.ch_name_id
            where ".DB_PREFIX."chapter.ch_id=".$chapter_id."");
            $chapter = $getchapter->fetch(PDO::FETCH_ASSOC);
            $filename = $_settings['export_directory'].'/'.$chapter['na_name'].' - '.stringPad(floatval($chapter['ch_number'])).'.zip';
            $zipData = new ZipArchive;
            $zipData->open ( $filename, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE );
            $zipData->setArchiveComment ( $chapter['na_name'].' - '.stringPad(floatval($chapter['ch_number'])).EOL.'Created by NekoViewer');
            foreach ( toArray($chapter['ch_image_id']) as $id=>$image_id )
                {
                $getid = $_database->query("select * from ".DB_PREFIX."image where im_id=".$image_id);
                $image = $getid->fetch(PDO::FETCH_ASSOC);
                $filename = stringPad(floatval($chapter['ch_number'])).'_'.stringPad($id+1).'.'.$image['im_type'];
                $zipData->addFromString($filename, $image['im_image']);
                }
            $zipData->close();
            header('Location:'.$_SERVER['HTTP_REFERER']);
            }
        else
            {
            echo 'ตอนไม่ถูกต้อง';
            }
        break;

    case 'exportname' :
        $name_id = getDatafromUri(2);
        CreateDirectory($_settings['export_directory']);
        if($name_id)
            {
            $getchapter = $_database->query("
            select ".DB_PREFIX."name.na_id, ".DB_PREFIX."name.na_name, ".DB_PREFIX."chapter.ch_name_id,
            ".DB_PREFIX."chapter.ch_number, ".DB_PREFIX."chapter.ch_id, ".DB_PREFIX."chapter.ch_image_id
            from ".DB_PREFIX."name inner join ".DB_PREFIX."chapter
            on ".DB_PREFIX."name.na_id=".DB_PREFIX."chapter.ch_name_id
            where ".DB_PREFIX."name.na_id=".$name_id."");
            $names = $getchapter->fetchAll(PDO::FETCH_ASSOC);
            foreach ($names as $chapter)
                {
                $filename = $_settings['export_directory'].'/'.$chapter['na_name'].' - '.stringPad(floatval($chapter['ch_number'])).'.zip';
                $zipData = new ZipArchive;
                $zipData->open ( $filename, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE );
                $zipData->setArchiveComment ( $chapter['na_name'].' - '.stringPad(floatval($chapter['ch_number'])).EOL.'Created by NekoViewer');
                foreach ( toArray($chapter['ch_image_id']) as $id=>$image_id )
                    {
                    $getid = $_database->query("select * from ".DB_PREFIX."image where im_id=".$image_id);
                    $image = $getid->fetch(PDO::FETCH_ASSOC);
                    $filename = stringPad(floatval($chapter['ch_number'])).'_'.stringPad($id+1).'.'.$image['im_type'];
                    $zipData->addFromString($filename, $image['im_image']);
                    }
                $zipData->close();
                }
            header('Location:'.$_SERVER['HTTP_REFERER']);
            }
        else
            {
            echo 'ชื่อเรื่องไม่ถูกต้อง';
            }
        break;

    case 'merge' :
        $chapter_id = toArray(getDatafromUri(2));
        if (count($chapter_id) > 1)
            {
            $getchapter = $_database->query("select * from ".DB_PREFIX."chapter where ch_id in (".implode(',', $chapter_id).") order by ch_number asc");
            $chapters = $getchapter->fetchAll(PDO::FETCH_ASSOC);
            $ch_image_id = array();
            $ch_image_uri = array();
            foreach ($chapters as $chapter)
                {
                $ch_image_id = array_merge($ch_image_id , toArray($chapter['ch_image_id']));
                $ch_image_uri = array_merge($ch_image_uri, unserialize($chapter['ch_image_uri']));
                }
           $_database->query("update ".DB_PREFIX."chapter set ch_image_id='".implode(',', $ch_image_id)."', ch_image_uri='".serialize($ch_image_uri)."' where ".DB_PREFIX."chapter.ch_id=".$chapter_id[0]);
            for($i=1;$i<count($chapter_id);$i++)
                $_database->query("delete from ".DB_PREFIX."chapter where ".DB_PREFIX."chapter.ch_id=".$chapter_id[$i]);
            echo 'รวมตอน '.getDatafromUri(2).' สำเร็จ';
            }
        else
            {
            echo 'จำนวนตอนที่รวมไม่ถูกต้อง';
            }
        break;

    default:
        echo 'ไม่ได้เลือกการกระทำ';
        break;

    }

?>