<?php

require_once './Viewer/Bootstrap.php';

$input = mtrim(filter_input_array(INPUT_POST));
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
        $chapter_id = (int)getDatafromUri(2);
        $image_id = (int)getDatafromUri(3);
        if($chapter_id && $image_id)
            {
            $getchapter = $_database->query("select * from ".DB_PREFIX."chapter where ch_id=".$chapter_id);
            $chapter = $getchapter->fetch(PDO::FETCH_ASSOC);
            $blob_id = array_diff(toArray($chapter['ch_content']), toArray($image_id));
            $_database->query("update ".DB_PREFIX."chapter set ch_content ='".implode(',', $blob_id)."' where ".DB_PREFIX."chapter.ch_id=".$chapter_id);
            $_database->query("delete from ".DB_PREFIX."image where ".DB_PREFIX."image.im_id=".$image_id);
            header('Location:'.$_SERVER['HTTP_REFERER']);
            }
        break;

    case 'deletechapter':
        $chapter_id = (int)getDatafromUri(2);
        if($chapter_id)
            {
            $getchapter = $_database->query("select * from ".DB_PREFIX."chapter where ch_id=".$chapter_id);
            $chapter = $getchapter->fetch(PDO::FETCH_ASSOC);
            foreach (toArray($chapter['ch_content']) as $image_id)
                $_database->query("delete from ".DB_PREFIX."image where ".DB_PREFIX."image.im_id=".$image_id);
            $_database->query("delete from ".DB_PREFIX."chapter where ".DB_PREFIX."chapter.ch_id=".$chapter_id);
            header('Location:'.$_SERVER['HTTP_REFERER']);
            }
        break;

    case 'name':
        $mode = NametoUri(getDatafromUri(2));
        CreateDirectory($_settings['cache_directory']);
        $filename = $_settings['cache_directory'].'/temp_cover.jpg';
        switch($mode)
            {
            case 'edit':
                $namedata = json_decode(base64_decode($input['namedata']), true);
                if($namedata['na_image_id'])
                    $image_id = $namedata['na_image_id'];
                 try
                    {
                    if($_FILES['cover']['tmp_name'])
                        {
                        move_uploaded_file($_FILES['cover']['tmp_name'], $filename);
                        $image = new PHPImage($filename);
                        $image->resize(300, 400, true, true);
                        $image->setOutput('jpg', 90);
                        $image->save($filename, false, false);
                        $cover = file_get_contents($filename);
                        if($namedata['na_image_id'] == '0')
                            {
                            $sql = "insert into ".DB_PREFIX."image (im_id, im_image, im_type) values (null, :image, :extension)";
                            $img = $_database->prepare($sql);
                            $img->bindParam(':image', $cover, PDO::PARAM_LOB);
                            $img->bindValue(':extension', 'jpg', PDO::PARAM_STR);
                            $img->execute();
                            $image_id = $_database->lastInsertId();
                            } 
                        else
                            {
                            $sql = "update ".DB_PREFIX."image set im_image=:image where im_id=:id";
                            $img = $_database->prepare($sql);
                            $img->bindParam(':image', $cover, PDO::PARAM_LOB);
                            $img->bindParam(':id', $namedata['na_image_id'], PDO::PARAM_INT);
                            $img->execute();
                            }
                            unlink($filename);
                        }
                    $sql = "update cv_name set na_type=:type, na_sub_id=:sub_id, na_visible=:visible, na_name=:name, na_name_uri=:name_uri, na_detail=:details, na_image_id=:image_id, na_uri=:uri, na_uri_template=:uri_template, na_last=:last, na_end=:end where na_id=:id";
                    $name = $_database->prepare($sql);
                    $name->bindParam(':type', $input['type'], PDO::PARAM_STR);
                    $name->bindParam(':sub_id', $input['sub_id'], PDO::PARAM_INT);
                    $name->bindParam(':visible', $input['visible'], PDO::PARAM_STR);   
                    $name->bindParam(':name', $input['name'], PDO::PARAM_STR);
                    $name->bindParam(':name_uri', $input['name_uri'], PDO::PARAM_STR);
                    $name->bindParam(':details', $input['details'], PDO::PARAM_STR);
                    $name->bindParam(':image_id', $image_id, PDO::PARAM_STR);
                    $name->bindParam(':uri', $input['uri'], PDO::PARAM_STR);
                    $name->bindParam(':uri_template', $input['uri_template'], PDO::PARAM_STR);
                    $name->bindParam(':last', $input['last'], PDO::PARAM_INT);
                    $name->bindParam(':end', $input['end'], PDO::PARAM_STR);
                    $name->bindParam(':id', $namedata['na_id'], PDO::PARAM_INT);
                    $name->execute();
                    header('Location:'.$_SERVER['HTTP_REFERER']);
                    }
                catch (PDOException $e)
                    {
                    $_title = 'เพิ่มข้อมูลไม่ได้';
                    $_error_message = 'เพิ่มข้อมูลไม่ได้ จากข้อผิดพลาด: ' . $e->getMessage();
                    require_once 'Viewer/Error.php';
                    }
                break;
            case 'add':
                try
                    {
                    if($_FILES['cover']['tmp_name'])
                        {
                        move_uploaded_file($_FILES['cover']['tmp_name'], $filename);
                        $image = new PHPImage($filename);
                        $image->resize(300, 400, true, true);
                        $image->setOutput('jpg', 90);
                        $image->save($filename, false, false);
                        $cover = file_get_contents($filename);
                        $sql = "insert into ".DB_PREFIX."image (im_id, im_image, im_type) values (null, :image, :extension)";
                        $img = $_database->prepare($sql);
                        $img->bindParam(':image', $cover, PDO::PARAM_LOB);
                        $img->bindValue(':extension', 'jpg', PDO::PARAM_STR);
                        $img->execute();
                        $image_id = $_database->lastInsertId();
                        unlink($filename);
                        }
                    else
                        {
                        $image_id = '0';
                        }
                    $input['name'] = trim($input['name']);
                    $name_uri = NametoUri($input['name']);
                    $input['details']= trim($input['details']);
                    $sql = "insert into ".DB_PREFIX."name (na_id, na_type, na_sub_id, na_visible, na_name, na_name_uri, na_detail, na_image_id, na_uri, na_uri_template, na_last, na_end, na_date, na_last_date) values (null, :type, :sub_id, :visible, :name, :name_uri, :details, :image_id, :uri, :uri_template, null, 'false', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
                    $name = $_database->prepare($sql);
                    $name->bindParam(':type', $input['type'], PDO::PARAM_STR);
                    $name->bindParam(':sub_id', $input['sub_id'], PDO::PARAM_INT);
                    $name->bindParam(':visible', $input['visible'], PDO::PARAM_STR);                   
                    $name->bindParam(':name', $input['name'], PDO::PARAM_STR);
                    $name->bindParam(':name_uri', $name_uri, PDO::PARAM_STR);
                    $name->bindParam(':details', $input['details'], PDO::PARAM_STR);
                    $name->bindParam(':image_id', $image_id, PDO::PARAM_STR);
                    $name->bindParam(':uri', $input['uri'], PDO::PARAM_STR);
                    $name->bindParam(':uri_template', $input['uri_template'], PDO::PARAM_STR);
                    $name->execute();
                    header('Location:/'.$name_uri);
                    }
                catch (PDOException $e)
                    {
                    $_title = 'เพิ่มข้อมูลไม่ได้';
                    $_error_message = 'เพิ่มข้อมูลไม่ได้ จากข้อผิดพลาด: ' . $e->getMessage();
                    require_once 'Viewer/Error.php';
                    }
                break;
            case 'delete':
                $name_id = (int)getDatafromUri(3);
                if($name_id)
                    {
                    $getchapter = $_database->query("select * from ".DB_PREFIX."chapter where ch_name_id=".$name_id);
                    $chapters = $getchapter->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($chapters as $chapter)
                        {
                        foreach (toArray($chapter['ch_content']) as $image_id)
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
            default :
                $_title = 'ไม่พบหน้าที่ร้องขอมา';
                $_error_message = 'ไม่พบการกระทำหรือคุณไม่มีสิทธิ์เข้าถึงหน้าดังกล่าว';
                require_once 'Viewer/Error.php';
                break;
            }
        break;

    case 'unread':
        $chapter_id = (int)getDatafromUri(2);
        $_database->query("update ".DB_PREFIX."chapter set ch_readed=0 where ch_id=".$chapter_id);
        header('Location:'.$_SERVER['HTTP_REFERER']);
        break;

    case 'crop':
        $getid = $_database->query("select * from ".DB_PREFIX."image where im_id=".(int)$input['image_id']);
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
        $chapter_id = (int)getDatafromUri(2);
        CreateDirectory($_settings['export_directory']);
        if($chapter_id)
            {
            $getchapter = $_database->query("
            select ".DB_PREFIX."name.na_id, ".DB_PREFIX."name.na_name, ".DB_PREFIX."chapter.ch_name_id,
            ".DB_PREFIX."chapter.ch_number, ".DB_PREFIX."chapter.ch_id, ".DB_PREFIX."chapter.ch_content
            from ".DB_PREFIX."name inner join ".DB_PREFIX."chapter
            on ".DB_PREFIX."name.na_id=".DB_PREFIX."chapter.ch_name_id
            where ".DB_PREFIX."chapter.ch_id=".$chapter_id."");
            $chapter = $getchapter->fetch(PDO::FETCH_ASSOC);
            $filename = $_settings['export_directory'].'/'.$chapter['na_name'].' - '.stringPad(floatval($chapter['ch_number'])).'.zip';
            $zipData = new ZipArchive;
            $zipData->open ( $filename, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE );
            $zipData->setArchiveComment ( $chapter['na_name'].' - '.stringPad(floatval($chapter['ch_number'])).EOL.'Created by NekoViewer');
            foreach ( toArray($chapter['ch_content']) as $id=>$image_id )
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
            $_title = 'ส่งออกไม่ได้';
            $_error_message = 'ส่งออกไม่ได้เนื่องจากตอนไม่ถูกต้อง';
            require_once 'Viewer/Error.php';
            }
        break;

    case 'exportname' :
        $name_id = (int)getDatafromUri(2);
        CreateDirectory($_settings['export_directory']);
        if($name_id)
            {
            $getchapter = $_database->query("
            select ".DB_PREFIX."name.na_id, ".DB_PREFIX."name.na_name, ".DB_PREFIX."chapter.ch_name_id,
            ".DB_PREFIX."chapter.ch_number, ".DB_PREFIX."chapter.ch_id, ".DB_PREFIX."chapter.ch_content
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
                foreach ( toArray($chapter['ch_content']) as $id=>$image_id )
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
            $_title = 'ส่งออกไม่ได้';
            $_error_message = 'ส่งออกไม่ได้เนื่องจากชื่อเรื่องไม่ถูกต้อง';
            require_once 'Viewer/Error.php';
            }
        break;

    case 'merge' :
        $chapter_id = toArray(getDatafromUri(2));
        if (count($chapter_id) > 1)
            {
            $getchapter = $_database->query("select * from ".DB_PREFIX."chapter where ch_id in (".implode(',', $chapter_id).") order by ch_number asc");
            $chapters = $getchapter->fetchAll(PDO::FETCH_ASSOC);
            $ch_content = array();
            $ch_image_uri = array();
            foreach ($chapters as $chapter)
                {
                $ch_content = array_merge($ch_content , toArray($chapter['ch_content']));
                $ch_image_uri = array_merge($ch_image_uri, unserialize($chapter['ch_image_uri']));
                }
           $_database->query("update ".DB_PREFIX."chapter set ch_content='".implode(',', $ch_content)."', ch_image_uri='".serialize($ch_image_uri)."' where ".DB_PREFIX."chapter.ch_id=".$chapter_id[0]);
            for($i=1;$i<count($chapter_id);$i++)
                $_database->query("delete from ".DB_PREFIX."chapter where ".DB_PREFIX."chapter.ch_id=".$chapter_id[$i]);
            echo 'รวมตอน '.getDatafromUri(2).' สำเร็จ';
            }
        else
            {
            echo 'จำนวนตอนที่รวมไม่ถูกต้อง';
            }
        break;

    case 'savesetting':
        foreach ($input['setting'] as $keyname=>$value)
            $_database->query("update cv_setting set se_variable= '".$value."' where cv_setting.se_name= '".$keyname."'");
        header('Location:'.$_SERVER['HTTP_REFERER']);
        break;

    default:
        $_title = 'ไม่พบหน้าที่ร้องขอมา';
        $_error_message = 'ไม่พบการกระทำหรือคุณไม่มีสิทธิ์เข้าถึงหน้าดังกล่าว';
        require_once 'Viewer/Error.php';
        break;

    }

?>