<?php

$_assets[] = 'font-awesome';
$_assets[] = 'jquery';
$_assets[] = 'jcrop';
$_assets[] = 'colortip';
$_assets[] = 'common';
$_assets[] = 'tabledit';
$_assets[] = 'pace';

$_mainmenu[] = ['หน้าหลัก', URI_PATH.'/', 0];
$_mainmenu[] = ['รายชื่อทั้งหมด', URI_PATH.'/all', 0];
$_mainmenu[] = ['การตั้งค่า', URI_PATH.'/setting', 1];
$_mainmenu[] = ['สถานะฐานข้อมูล', URI_PATH.'/setting/dbstatus', 0];
$_mainmenu[] = ['เกี่ยวกับ', URI_PATH.'/setting/about', 0];

echo '<div id="status" class="status">&nbsp;</div>', EOL;
echo '<section class="main container_12">', EOL;
echo '<div class="row">', EOL;
echo '<div class="area-header"></div>', EOL;
echo '<div class="block-contents">', EOL;
echo '<div class="bg-contents"></div>', EOL;

$action = getDatafromUri(1);

switch ($action)
    {

    case 'edit' :
        $name_uri = getDatafromUri(2);
        if ($name_uri)
            {
            $getname = $_database->query("select * from ".DB_PREFIX."name where na_name_uri='".$name_uri."'");
            $name = $getname->fetch(PDO::FETCH_ASSOC);
            echo '<h1 class="title"><a href="'.URI_PATH.'/setting" title="การตั้งค่า"><i class="fa fa-cog"></i></a> <a href="'.URI_PATH.'/setting/edit" title="จัดการและแก้ใข"><i class="fa fa-pencil"></i> </a> : '.$name['na_name'].'</h1>', EOL;
            $_title = 'การตั้งค่า - การจัดการและการแก้ไข '.$name['na_name'];
            echo '<div class="contents">', EOL;
            echo '<div class="index">', EOL;
            echo '<ul class="article-list">', EOL;
            echo '<li><div id="debug" contenteditable data-name="custom-text">'.$name['na_detail'].'</div> <a href="'.$name['na_uri'].'" target="_blank" title="ไปยังเว็ปที่มา"><i class="fa fa-globe"></i></a></li>', EOL;
            $getname = $_database->query("select * from ".DB_PREFIX."name where na_sub_id=".$name['na_id']." order by na_name asc");
            foreach ($getname->fetchAll(PDO::FETCH_ASSOC) as $subname)
                {
                echo '<li> <a href="'.URI_PATH.'/setting/edit/'.$subname['na_name_uri'].'">'.$subname['na_name'].'</a>';
                echo '<time>'.DateFormat($subname['na_date']).'</time></li>', EOL;
                }
            echo '</ul>', EOL;
            echo '<table id="chapter">';
            echo '<thead><tr>';
            echo '<th style="display: none;">#</th>';
            echo '<th width="40">ตอน</th>';
            echo '<th width="70">#</th>';
            echo '<th>ชื่อตอน</th>';
            echo '<th width="250">ที่อยู่</th>';
            echo '<th width="180">วันที่</th>';
            echo '</tr></thead>', EOL;
            echo '<tbody>', EOL;
            $getchapter = $_database->query("select * from ".DB_PREFIX."chapter where ch_name_id=".$name['na_id']." order by ch_number desc");
            foreach ($getchapter->fetchAll(PDO::FETCH_ASSOC) as $id=>$chapter)
                {
                $chapter['ch_number'] = floatval($chapter['ch_number']);
                //if ($chapter['ch_title'] == null) $chapter['ch_title'] = 'Chapter '.$chapter['ch_number'];
                echo '<tr id="['.$id.']">';
                echo '<td> '.$chapter['ch_id'].'</td>';
                echo '<td> '.$chapter['ch_number'].'</td>';
                echo '<td><a href="'.URI_PATH.'/'.$name['na_name_uri'].'/'.$chapter['ch_number'].'" target="_blank" title="อ่านตอนนี้"><i class="fa fa-search"></i> </a>';
                echo '<a href="/api/exportchapter/'.$chapter['ch_id'].'" title="บันทึกเป็นไฟล์ตอนนี้"><i class="fa fa-floppy-o"></i> </a>';
                echo '<a class="delete" href="/api/deletechapter/'.$chapter['ch_id'].'" title="ลบตอน"><i class="fa fa-times"></i> </a></td>';
                echo '<td>'.$chapter['ch_title'].'</td>';
                echo '<td>'.$chapter['ch_uri'].'</td>';
                echo '<td>'.DateFormat($chapter['ch_date']).'</td>';
                echo '</tr>', EOL;
                }
            echo '</tbody></table>';
            echo '</div>', EOL;
            echo '<div class="clear"></div>', EOL;
            echo '</div>', EOL;
            }
        else
            {
            $_title = 'การตั้งค่า - การจัดการและการแก้ไข';
            echo '<h1 class="title"><a href="'.URI_PATH.'/setting" title="การตั้งค่า"><i class="fa fa-cog"></i></a> : การจัดการและการแก้ไข</h1>', EOL;
            echo '<div class="contents">', EOL;
            echo '<div class="index">', EOL;
            echo '<ul class="article-list">', EOL;
            echo '<li><a href="'.URI_PATH.'/setting/addname"><i class="fa fa-plus"></i> เพิ่มเรื่องใหม่</a></li>', EOL;
            $getname = $_database->query("select * from ".DB_PREFIX."name where na_sub_id=0 order by na_name asc");
            foreach ($getname->fetchAll(PDO::FETCH_ASSOC) as $name)
                {
                echo '<li><a href="/api/exportname/'.$name['na_id'].'" title="บันทึกเป็นไฟล์เรื่องนี้"><i class="fa fa-floppy-o"></i> </a>';
                echo '<a class="delete" href="/api/deletename/'.$name['na_id'].'" title="ลบเรื่อง"><i class="fa fa-times"></i> </a>';
                echo '<a href="'.URI_PATH.'/setting/edit/'.$name['na_name_uri'].'">'.$name['na_name'].'</a>';
                echo '<time>'.DateFormat($name['na_date']).'</time></li>', EOL;
                }
            echo '</ul>', EOL;
            echo '</div>', EOL;
            echo '<div class="clear"></div>', EOL;
            echo '</div>', EOL;
            }
        break;

    case 'dbstatus' :
        $_title = 'การตั้งค่า - สถานะฐานข้อมูล';
        echo '<h1 class="title"><a href="'.URI_PATH.'/setting" title="การตั้งค่า"><i class="fa fa-cog"></i></a> : สถานะฐานข้อมูล</h1>', EOL;
        echo '<div class="contents">', EOL;
        echo '<div class="index">', EOL;
        echo '<h2 class="title"><i class="fa fa-database"></i> ฐานข้อมูล '.strtoupper(str_replace('_', ' ', DB_NAME)).'</h2>', EOL;
        echo '<table id="dbinformation">';
        echo '<thead><tr>';
        echo '<th style="text-align: left;width:50px">#</th>';
        echo '<th>ตาราง</th>';
        //echo '<th style="text-align: center;width:80px">ประเภท</th>';
        echo '<th style="text-align: center;width:160px">เข้าถึงล่าสุด</th>';
        echo '<th style="text-align: center;width:100px">จำนวนข้อมูล</th>';
        echo '<th style="text-align: center;width:120px">ขนาด</th>';
        echo '<th style="text-align: center;width:100px">ดัชนี</th>';
        echo '<th style="text-align: center;width:100px">ส่วนเกิน</th>';
        echo '</tr></thead>', EOL;
        echo '<tbody>';
        $data_length_total = 0;
        $data_free_total = 0;
        $Index_length_total = 0;
        $row_total = 0;
        $get_db_infomation = $_database->query("show table status");
        foreach ($get_db_infomation->fetchAll(PDO::FETCH_ASSOC) as $id=>$db_infomation)
            {
            echo '<tr id="['.$id.']">';
            echo '<td style="text-align: left;">'.($id+1).'</td>';
            echo '<td><i class="fa fa-table"></i> '.strtoupper(str_replace([DB_PREFIX, '_'], ['',' '], $db_infomation['Name'])).'</td>';
            //echo '<td style="text-align: center">'.$db_infomation['Engine'].'</td>';
            echo '<td style="text-align: center">'.DateFormat($db_infomation['Update_time']).'</td>';
            echo '<td style="text-align: right;">'.number_format($db_infomation['Rows']).'</td>';
            echo '<td style="text-align: right;">'.FileSizeConvert($db_infomation['Data_length']).'</td>';
            echo '<td style="text-align: right;">'.FileSizeConvert($db_infomation['Index_length']).'</td>';
            echo '<td style="text-align: right;">'.FileSizeConvert($db_infomation['Data_free']).'</td>';
            echo '</tr>', EOL;
            $data_length_total += $db_infomation['Data_length'];
            $Index_length_total +=$db_infomation['Index_length'];
            $data_free_total +=$db_infomation['Data_free'];
            $row_total += $db_infomation['Rows'];
            }
        echo '<tr>';
        echo '<td>รวม</td>';
        echo '<td>ใช้หน่วยความจำ '.FileSizeConvert($data_length_total+$Index_length_total+$data_free_total).'</td>';
        //echo '<td></td>';
        echo '<td></td>';
        echo '<td style="text-align: right;">'.number_format($row_total).'</td>';
        echo '<td style="text-align: right;">'.FileSizeConvert($data_length_total).'</td>';
        echo '<td style="text-align: right;">'.FileSizeConvert($Index_length_total).'</td>';
        echo '<td style="text-align: right;">'.FileSizeConvert($data_free_total).'</td>';
        echo '</tr>', EOL;
        echo '</tbody></table>', EOL;
        echo '</div>', EOL;
        echo '<div class="clear"></div>', EOL;
        echo '</div>', EOL;
        break;

    case 'addname' :
        $_title = 'การตั้งค่า - เพิ่มเรื่องใหม่';
        echo '<h1 class="title"><a href="'.URI_PATH.'/setting" title="การตั้งค่า"><i class="fa fa-cog"></i></a> : เพิ่มเรื่องใหม่</h1>', EOL;
        echo '<div class="contents">', EOL;
        echo '<div class="index">', EOL;
        echo '<form method="post" action="/api" enctype="multipart/form-data">';
        echo '<ul class="article-list">', EOL;
        echo '<input type="hidden" name="action" value="addname">', EOL;
        echo '<li><span style="float: left;width:100px">กลุ่ม : </span><select name="sub_id" style="width:400px">', EOL;
        echo '<option value="0" selected="selected">เป็นกลุ่มหลัก</option>', EOL;
        $getname = $_database->query("select * from ".DB_PREFIX."name where na_sub_id=0 order by na_name asc");
        foreach ($getname as $name)
            echo '<option value="'.$name['na_id'].'">'.$name['na_name'].'</option>', EOL;
        echo '</select></li>', EOL;
        echo '<li><span style="float: left;width:100px;">ประเภท : </span><span style="float: left;width:100px;"><input type="radio" name="type" value="M"> การ์ตูน</span><span style="width:100px;"><input type="radio" name="type" value="N"> นิยาย</span></li>', EOL;
        echo '<li><span style="float: left;width:100px;">ชื่อ : </span><input type="text" name="name" style="width:400px"></li>', EOL;
        echo '<li><span style="float: left;width:100px">รูปปก : </span><input type="file" name="cover" style="width:400px"></li>', EOL;
        echo '<li><span style="float: left;width:100px">รายละเอียด : </span><textarea  name="details" style="width:400px;height:200px"></textarea></li>', EOL;
        echo '<li><span style="float: left;width:100px">เว็ปไซต์ : </span><input type="text" name="uri" style="width:400px"></li>', EOL;
        echo '<li><span style="float: left;width:100px">แม่แบบลิ้ง : </span><input type="text" name="uri_template" style="width:400px"></li>', EOL;
        echo '<li><span style="float: left;width:100px">&nbsp;</span><input type="submit" value="เพิ่ม" class="ui" style="width:100px"></li>', EOL;
        echo '</ul>', EOL;
        echo '</form>';
        echo '</div>', EOL;
        echo '<div class="clear"></div>', EOL;
        echo '</div>', EOL;
        break;

    case 'addchapter' :
        $_title = 'การตั้งค่า - เพิ่มตอนใหม่';
        echo '<h1 class="title"><a href="'.URI_PATH.'/setting" title="การตั้งค่า"><i class="fa fa-cog"></i></a> : เพิ่มตอนใหม่</h1>', EOL;
        echo '<div class="contents">', EOL;
        echo '<div class="index">', EOL;
        echo '<form method="post" action="/api" enctype="multipart/form-data">';
        echo '<ul class="article-list">', EOL;
        echo '<input type="hidden" name="action" value="addchapter">', EOL;
        echo '<li><span style="float: left;width:100px">เรื่อง : </span><select name="sub_id" style="width:400px">', EOL;
        echo '<option value="0" selected="selected">ยังไม่ได้เลือก</option>', EOL;
        $getname = $_database->query("select * from ".DB_PREFIX."name where na_end!=1 order by na_name asc");
        foreach ($getname as $name)
            echo '<option value="'.$name['na_id'].'">'.$name['na_name'].'</option>', EOL;
        echo '</select></li>', EOL;
        echo '<li><span style="float: left;width:100px;">ตอนที่ : </span><input type="text" name="chapter" style="width:400px"></li>', EOL;
        echo '<li><span style="float: left;width:100px">ชื่อตอน : </span><input type="text" name="title" style="width:400px"></li>', EOL;
        echo '<li><span style="float: left;width:100px">เว็ปไซต์ : </span><input type="text" name="uri" style="width:400px"></li>', EOL;
        echo '<li><span style="float: left;width:100px">&nbsp;</span><input type="submit" value="เพิ่ม" class="ui" style="width:100px"></li>', EOL;
        echo '</ul>', EOL;
        echo '</form>';
        echo '</div>', EOL;
        echo '<div class="clear"></div>', EOL;
        echo '</div>', EOL;
        break;

    case 'crop' :
        $image_id = getDatafromUri(2);
        $_title = 'การตั้งค่า - ตัดรูป หมายเลข '.$image_id;
        echo '<h1 class="title"><a href="'.URI_PATH.'/setting" title="การตั้งค่า"><i class="fa fa-cog"></i></a> : แก้ไขรูปภาพ</h1>', EOL;
        echo '<div class="contents">', EOL;
        echo '<div class="index">', EOL;
        echo '<ul class="article-list">', EOL;
        echo '<form action="/api" method="post">', EOL;
        echo '<input type="hidden" name="action" value="crop">', EOL;
        echo '<input type="hidden" name="image_id" value="'.$image_id.'">', EOL;
        echo '<input type="hidden" id="x" name="x" />', EOL;
        echo '<input type="hidden" id="y" name="y" />', EOL;
        echo '<input type="hidden" id="w" name="w" />', EOL;
        echo '<input type="hidden" id="h" name="h" />', EOL;
        echo '<li><img id="crop" src="/image/'.$image_id.'"></li>', EOL;
        echo '<li><input type="submit" value="ตัดรูปภาพ" class="ui" /></li>', EOL;
        echo '</form>', EOL;
        echo '</ul>', EOL;
        echo '</form>';
        echo '</div>', EOL;
        echo '<div class="clear"></div>', EOL;
        echo '</div>', EOL;
        break;

    case 'general' :
        $_title = 'การตั้งค่า - การตั้งค่าทั่วไป';
        echo '<h1 class="title"><a href="'.URI_PATH.'/setting" title="การตั้งค่า"><i class="fa fa-cog"></i></a> : การตั้งค่าทั่วไป</h1>', EOL;
        echo '<div class="contents">', EOL;
        echo '<div class="index">', EOL;
        echo '<form method="post" action="/api" enctype="multipart/form-data">';
        echo '<ul class="article-list">', EOL;
        echo '<input type="hidden" name="action" value="savesetting">', EOL;
        echo '<li><h2>ตั้งค่าระบบ</h2></li>', EOL;
        echo '<li><span style="float: left;width:200px;">ชื่อหัวเว็ปหลัก : </span><input type="text" name="setting[title]" style="width:400px" value="'.$_settings['title'].'"></li>', EOL;
        echo '<li><span style="float: left;width:200px;">Github : </span><input type="text" name="setting[github]" style="width:400px" value="'.$_settings['github'].'"></li>', EOL;
        echo '<li><span style="float: left;width:200px;">Twitter : </span><input type="text" name="setting[twitter]" style="width:400px" value="'.$_settings['twitter'].'"></li>', EOL;
        echo '<li><span style="float: left;width:200px;">Copy Right : </span><input type="text" name="setting[copy_right]" style="width:400px" value="'.htmlentities($_settings['copy_right']).'"></li>', EOL;
        echo '<li><h2>ตั้งค่าการแสดงผล</h2></li>', EOL;
        echo '<li><span style="float: left;width:200px;">ความกว้างรูปย่อ : </span><input type="text" name="setting[thumbnail_width]" style="width:400px" value="'.$_settings['thumbnail_width'].'"> ฟิกเซลล์</li>', EOL;
        echo '<li><span style="float: left;width:200px;">ความสูงรูปย่อ : </span><input type="text" name="setting[thumbnail_height]" style="width:400px" value="'.$_settings['thumbnail_height'].'"> ฟิกเซลล์</li>', EOL;
        echo '<li><h2>ตั้งค่าดาวน์โหลด</h2></li>', EOL;
        echo '<li><span style="float: left;width:200px;">ที่อยู่ดาวน์โหลด : </span><input type="text" name="setting[download_directory]" style="width:400px" value="'.$_settings['download_directory'].'"></li>', EOL;
        echo '<li><span style="float: left;width:200px;">ที่อยู่แคช : </span><input type="text" name="setting[cache_directory]" style="width:400px" value="'.$_settings['cache_directory'].'"></li>', EOL;
        $cache_download = checked($_settings['cache_download_file']);
        echo '<li><span style="float: left;width:200px;">เปิดใช้งานแคช : </span><span style="float: left;width:100px;"><input type="radio" name="setting[cache_download_file]" value="true"'.$cache_download['true'].'> เปิด</span><span style="width:100px;"><input type="radio" name="setting[cache_download_file]" value="false"'.$cache_download['false'].'> ปิด</span></li>', EOL;
        echo '<li><span style="float: left;width:200px;">ที่อยู่คุกกี้ : </span><input type="text" name="setting[cookie_directory]" style="width:400px" value="'.$_settings['cookie_directory'].'"></li>', EOL;
        echo '<li><span style="float: left;width:200px;">ที่อยู่แฟ้มCA : </span><input type="text" name="setting[ca_cert_filename]" style="width:400px" value="'.$_settings['ca_cert_filename'].'"></li>', EOL;
        echo '<li><span style="float: left;width:200px;">คำยกเว้น : </span><input type="text" name="setting[skip_word]" style="width:400px" value="'.$_settings['skip_word'].'"></li>', EOL;
        echo '<li><span style="float: left;width:200px;">โดเมนพิเศษ : </span><input type="text" name="setting[special_domains]" style="width:400px" value="'.$_settings['special_domains'].'"></li>', EOL;
        echo '<li><span style="float: left;width:200px;">URL แพทเทิร์น : </span><input type="text" name="setting[url_pattern]" style="width:400px" value="'.$_settings['url_pattern'].'"></li>', EOL;
        echo '<li><span style="float: left;width:200px;">User Agent : </span><input type="text" name="setting[browser_useragent]" style="width:400px" value="'.$_settings['browser_useragent'].'"></li>', EOL;
        $recompassed = checked($_settings['recompassed']);
        echo '<li><span style="float: left;width:200px;">เข้ารหัสไฟล์ใหม่ : </span><span style="float: left;width:100px;"><input type="radio" name="setting[recompassed]" value="true"'.$recompassed['true'].'> เปิด</span><span style="width:100px;"><input type="radio" name="setting[recompassed]" value="false"'.$recompassed['false'].'> ปิด</span></li>', EOL;
        $output_extension = checked($_settings['output_extension'], 'jpg,png');
        echo '<li><span style="float: left;width:200px;">เข้ารหัสไฟล์ใหม่เป็น : </span><span style="float: left;width:100px;"><input type="radio" name="setting[output_extension]" value="jpg"'.$output_extension['jpg'].'> JPEG</span><span style="width:100px;"><input type="radio" name="setting[output_extension]" value="png"'.$output_extension['png'].'> PNG</span></li>', EOL;
        echo '<li><span style="float: left;width:200px;">คุณภาพของ PNG : </span><input type="text" name="setting[png_compression]" style="width:400px" value="'.$_settings['png_compression'].'"> 0-9</li>', EOL;
        echo '<li><span style="float: left;width:200px;">คุณภาพของ JPEG : </span><input type="text" name="setting[jpeg_quality]" style="width:400px" value="'.$_settings['jpeg_quality'].'"> 0-100</li>', EOL;
        echo '<li><span style="float: left;width:200px;">ที่อยู่ ImageMagick : </span><input type="text" name="setting[ImageMagick_filename]" style="width:400px" value="'.$_settings['ImageMagick_filename'].'"></li>', EOL;
        echo '<li><span style="float: left;width:200px;">ที่อยู่ PDFConvert : </span><input type="text" name="setting[PDFConvert_filename]" style="width:400px" value="'.$_settings['PDFConvert_filename'].'"></li>', EOL;
        echo '<li><span style="float: left;width:200px;">ที่อยู่ PNG Optimizer : </span><input type="text" name="setting[png_optimizer_filename]" style="width:400px" value="'.$_settings['png_optimizer_filename'].'"></li>', EOL;
        $png_optimizer = checked($_settings['png_optimizer']);
        echo '<li><span style="float: left;width:200px;">เปิดใช้งาน PNG Optimizer : </span><span style="float: left;width:100px;"><input type="radio" name="setting[png_optimizer]" value="true"'.$png_optimizer['true'].'> เปิด</span><span style="width:100px;"><input type="radio" name="setting[png_optimizer]" value="false"'.$png_optimizer['false'].'> ปิด</span></li>', EOL;
        echo '<li><span style="float: left;width:200px;">ขนาดรูปที่ยอมรับ (Pixel) : </span><input type="text" name="setting[accept_dimension]" style="width:400px" value="'.$_settings['accept_dimension'].'"> ฟิกเซลล์</li>', EOL;
        echo '<li><span style="float: left;width:200px;">ขนาดรูปที่ยอมรับ (Byte) : </span><input type="text" name="setting[accept_bytes]" style="width:400px" value="'.$_settings['accept_bytes'].'"> ไปต์</li>', EOL;
        echo '<li><span style="float: left;width:200px;">เวลา CURL : </span><input type="text" name="setting[curl_timeout]" style="width:400px" value="'.$_settings['curl_timeout'].'"> วินาที</li>', EOL;
        echo '<li><span style="float: left;width:200px;">ดาวน์โหลดซ้ำ : </span><input type="text" name="setting[resume_download]" style="width:400px" value="'.$_settings['resume_download'].'"> ครั้ง</li>', EOL;
        /*episode_prefix page_digit max_width max_height*/
        echo '<li><span style="float: left;width:200px">&nbsp;</span><input type="submit" value="บันทึก" class="ui" style="width:100px"></li>', EOL;
        echo '</ul>', EOL;
        echo '</div>', EOL;
        echo '<div class="clear"></div>', EOL;
        echo '</div>', EOL;
        break;

    case 'about' :
        $_title = 'การตั้งค่า - เกี่ยวกับ';
        echo '<h1 class="title"><a href="'.URI_PATH.'/setting" title="การตั้งค่า"><i class="fa fa-cog"></i></a> : เกี่ยวกับ Neko Viewer</h1>', EOL;
        echo '<div class="contents">', EOL;
        echo '<div class="index">', EOL;
        echo '<ul class="article-list">', EOL;
        echo '<li><h2>Neko Viewer</h2></li>', EOL;
        echo '<li>Neko Viewer Core v.'.$_settings['view_version'].'</li>', EOL;
        echo '<li>Neko Downloader Core v.'.$_settings['down_version'].'</li>', EOL;
        echo '</ul>', EOL;
        echo '<div>&nbsp;</div>', EOL;
        echo '<ul class="article-list">', EOL;
        echo '<li><h2>Libraries</h2></li>', EOL;
        echo '<li>JQuery v2.2.3 - <a href="http://jquery.com" target="_blank">http://jquery.com</a></li>', EOL;
        echo '<li>Tabledit v1.2.3 - <a href="http://markcell.github.io/jquery-tabledit" target="_blank">http://markcell.github.io/jquery-tabledit</a></li>', EOL;
        echo '<li>prettyPhoto v3.1.6 - <a href="https://github.com/scaron/prettyphoto" target="_blank">https://github.com/scaron/prettyphoto</a></li>', EOL;
        echo '<li>Jcrop v0.9.12 - <a href="http://deepliquid.com/content/Jcrop.html" target="_blank">http://deepliquid.com/content/Jcrop.html</a></li>', EOL;
        echo '<li>Font Awesome v4.5.0 - <a href="http://fontawesome.io" target="_blank">http://fontawesome.io</a></li>', EOL;
        echo '<li>Pace v1.0.2 - <a href="http://github.hubspot.com/pace" target="_blank">http://github.hubspot.com/pace</a></li>', EOL;
        echo '<li>Lazy Load v1.9.5 - <a href="http://www.appelsiini.net/projects/lazyload" target="_blank">http://www.appelsiini.net/projects/lazyload</a></li>', EOL;
        echo '<li>Colortips v1.0.0 - <a href="http://tutorialzine.com/2010/07/colortips-jquery-tooltip-plugin" target="_blank">http://tutorialzine.com/2010/07/colortips-jquery-tooltip-plugin</a></li>', EOL;
        echo '<li>php-image v0.5.0 - <a href="https://github.com/kus/php-image" target="_blank">https://github.com/kus/php-image</a></li>', EOL;
        echo '</ul>', EOL;
        echo '</div>', EOL;
        echo '<div class="clear"></div>', EOL;
        echo '</div>', EOL;
        break;

    default :
        $_title = 'การตั้งค่า';
        echo '<h1 class="title"><i class="fa fa-cog"></i> การตั้งค่า</h1>', EOL;
        echo '<div class="contents">', EOL;
        echo '<div class="index">', EOL;
        echo '<ul class="article-list">', EOL;
        echo '<li><span class="icon"><i class="fa fa-plus"></i> </span><a href="'.URI_PATH.'/setting/addname">เพิ่มเรื่องใหม่</a></li>', EOL;
        echo '<li><span class="icon"><i class="fa fa-plus"></i> </span><a href="'.URI_PATH.'/setting/addchapter">เพิ่มตอนใหม่</a></li>', EOL;
        echo '<li><span class="icon"><i class="fa fa-pencil"></i> </span><a href="'.URI_PATH.'/setting/edit">การจัดการและการแก้ไข</a></li>', EOL;
        echo '<li><span class="icon"><i class="fa fa-cog"></i> </span><a href="'.URI_PATH.'/setting/general">การตั้งค่าทั่วไป</a></li>', EOL;
        echo '<li><span class="icon"><i class="fa fa-database"></i> </span><a href="'.URI_PATH.'/setting/dbstatus">สถานะฐานข้อมูล</a></li>', EOL;
        echo '<li><span class="icon"><i class="fa fa-info"></i> </span><a href="'.URI_PATH.'/setting/about">เกี่ยวกับ Neko Viewer</a></li>', EOL;
        echo '</ul>', EOL;
        echo '</div>', EOL;
        echo '<div class="clear"></div>', EOL;
        echo '</div>', EOL;
        break;
    }

echo '</div>', EOL;
echo '</div>', EOL;
echo '</section>', EOL;

?>