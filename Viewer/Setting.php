<?php

$action = getDatafromUri(1);
$_mainactive = '3';

switch ($action)
    {

    case 'manage' :
        $name_uri = NametoUri(getDatafromUri(2));
        if ($name_uri)
            {
            $_assets[] = 'tabledit';
            $getname = $_database->query("select * from ".DB_PREFIX."name where na_name_uri='".$name_uri."'");
            $name = $getname->fetch(PDO::FETCH_ASSOC);
            echo '<h1 class="title"><a href="'.URI_PATH.'/setting" title="การตั้งค่า"><i class="fa fa-cog"></i></a> <a href="'.URI_PATH.'/setting/manage" title="จัดการและแก้ใข"><i class="fa fa-pencil"></i> </a> : '.$name['na_name'].'</h1>', EOL;
            $_title = 'การตั้งค่า - การจัดการและการแก้ไข '.$name['na_name'];
            echo '<div class="contents">', EOL;
            echo '<div class="index">', EOL;
            echo '<ul class="article-list">', EOL;
            echo '<li><div id="debug" contenteditable data-name="custom-text">'.$name['na_detail'].'</div> <a href="'.$name['na_uri'].'" target="_blank" title="ไปยังเว็ปที่มา"><i class="fa fa-globe"></i></a></li>', EOL;
            $getname = $_database->query("select * from ".DB_PREFIX."name where na_sub_id=".$name['na_id']." order by na_name asc");
            foreach ($getname->fetchAll(PDO::FETCH_ASSOC) as $subname)
                {
                echo '<li><a href="/api/exportname/'.$subname['na_id'].'" title="บันทึกเป็นไฟล์เรื่อง"><i class="fa fa-floppy-o"></i> </a>';
                echo '<a href="/setting/name/edit/'.$subname['na_id'].'" title="แก้ใขเรื่อง"><i class="fa fa-pencil"></i> </a>';
                echo '<a class="delete" href="/api/name/delete/'.$subname['na_id'].'" title="ลบเรื่อง"><i class="fa fa-times"></i> </a>';
                echo '<a href="'.URI_PATH.'/setting/manage/'.$subname['na_name_uri'].'">'.$subname['na_name'].'</a>';
                echo '<time>'.DateFormat($subname['na_date']).'</time></li>', EOL;
                }
            echo '</ul>', EOL;
            echo '<table id="chapter">';
            echo '<thead><tr>';
            echo '<th style="display: none;">#</th>';
            echo '<th width="40">ตอน</th>';
            echo '<th width="90">#</th>';
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
                echo '<a class="delete" href="/api/deletechapter/'.$chapter['ch_id'].'" title="ลบตอน"><i class="fa fa-times"></i> </a>';
                if ($chapter['ch_readed'])
                    echo ' <a href="/api/unread/'.$chapter['ch_id'].'" title="อ่านแล้ว คลิกเพื่อทำว่ายังไม่ได้อ่าน"><i class="fa fa-check-circle-o"></i></a></td>';
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
            echo '<li><i class="fa fa-plus"></i> <a href="'.URI_PATH.'/setting/name/add">เพิ่มเรื่องใหม่</a></li>', EOL;
            echo '<li><i class="fa fa-plus"></i> <a href="'.URI_PATH.'/setting/chapter/add">เพิ่มตอนใหม่</a></li>', EOL;
            $getname = $_database->query("select * from ".DB_PREFIX."name where na_sub_id=0 order by na_name asc");
            foreach ($getname->fetchAll(PDO::FETCH_ASSOC) as $name)
                {
                echo '<li><a href="/api/exportname/'.$name['na_id'].'" title="บันทึกเป็นไฟล์เรื่อง"><i class="fa fa-floppy-o"></i> </a>';
                echo '<a href="/setting/name/edit/'.$name['na_id'].'" title="แก้ใขเรื่อง"><i class="fa fa-pencil"></i> </a>';
                echo '<a class="delete" href="/api/name/delete/'.$name['na_id'].'" title="ลบเรื่อง"><i class="fa fa-times"></i> </a>';
                echo '<a href="'.URI_PATH.'/setting/manage/'.$name['na_name_uri'].'">'.$name['na_name'].'</a>';
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
        echo '<th style="text-align: center;width:100px">จำนวน</th>';
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
        echo '<td>'.FileSizeConvert($data_length_total+$Index_length_total+$data_free_total).'</td>';
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

    case 'name' :
        $_assets[] = 'uploadPreview';
        $mode = NametoUri(getDatafromUri(2));
        $name_id = (int)getDatafromUri(3);
        $image_cover = URI_PATH.'/image/2';
        if($mode=='edit' && isset($name_id))
            {
            $_title = 'การตั้งค่า - แก้ใขชื่อเรื่อง';
            $titlemenu = 'แก้ใขชื่อเรื่อง';
            $getname = $_database->query("select * from ".DB_PREFIX."name where na_id='".$name_id ."'");
            $namedata = $getname->fetch(PDO::FETCH_ASSOC);
            if(!$namedata)
                {
                $_title = 'ไม่พบเรื่องที่ระบุมาในระบบ';
                $_error_message = 'ไม่พบเรื่องที่ระบุมาในระบบ อาจถูกลบไปแล้วหรือคุณไม่มีสิทธิ์เข้าถึงหน้าดังกล่าว';
                require_once 'Viewer/Error.php';
                }
            if($namedata['na_image_id'] != '0' )
                $image_cover  = URI_PATH.'/image/'.$namedata['na_image_id'];
            }
        elseif($mode=='add')
            {
            $_title = 'การตั้งค่า - เพิ่มเรื่องใหม่';
            $titlemenu = 'เพิ่มเรื่องใหม่';
            $namedata = array('na_id'=>'0','na_type'=>'M', 'na_sub_id'=>'0', 'na_visible'=>'true','na_name'=>'', 'na_name_uri'=>'',
                'na_detail'=>'', 'na_image_id'=>'0', 'na_uri'=>'', 'na_uri_template'=>'', 'na_last'=>'','na_end'=>'false'); 
            }
        else
            {
            $_title = 'ไม่พบหน้าที่ร้องขอมา';
            $_error_message = 'ไม่พบการกระทำหรือคุณไม่มีสิทธิ์เข้าถึงหน้าดังกล่าว';
            require_once 'Viewer/Error.php';
            }
        $type = checked($namedata['na_type'], 'M,N');
        $end = checked($namedata['na_end']);
        $visible = checked($namedata['na_visible']);
        echo '<h1 class="title"><a href="'.URI_PATH.'/setting" title="การตั้งค่า"><i class="fa fa-cog"></i></a> <a href="'.URI_PATH.'/setting/manage" title="จัดการและแก้ใข"><i class="fa fa-pencil"></i> </a> : '.$titlemenu.'</h1>', EOL;
        echo '<div class="contents">', EOL;
        echo '<div class="index">', EOL;
        echo '<form method="post" action="/api/name/'.$mode.'" enctype="multipart/form-data">';
        echo '<ul class="article-list">', EOL;
        if($mode=='edit')
            echo '<input type="hidden" name="namedata" value="'.base64_encode(json_encode($namedata)).'">', EOL;
        echo '<li><span class="left w200">กลุ่ม : </span><select name="sub_id" class="w400">', EOL;
        echo '<option value="0">เป็นกลุ่มหลัก</option>', EOL;
        $getname = $_database->query("select * from ".DB_PREFIX."name where na_sub_id=0 order by na_name asc");
        foreach ($getname as $name)
            {
            if($name['na_id'] != $name_id)
                {
                $select = ($namedata['na_sub_id'] == $name['na_id']) ? ' selected' : '';
                echo '<option value="'.$name['na_id'].'" '.$select .'>'.$name['na_name'].'</option>', EOL;
                }
            }
        echo '</select></li>', EOL;
        echo '<li><span class="left w200">ประเภท : </span><span  class="left w100"><input type="radio" name="type" value="M" '.$type['M'].'> การ์ตูน</span><span class="w100"><input type="radio" name="type" value="N"" '.$type['N'].'> นิยาย</span></li>', EOL;
        echo '<li><span class="left w200">สถานะ : </span><span  class="left w100"><input type="radio" name="end" value="true" '.$end['true'].'> จบแล้ว</span><span class="w100"><input type="radio" name="end" value="false"" '.$end['false'].'> ยังไม่จบ</span></li>', EOL;
        echo '<li><span class="left w200">การแสดงผล : </span><span  class="left w100"><input type="radio" name="visible" value="true" '.$visible['true'].'> แสดง</span><span class="w100"><input type="radio" name="visible" value="false"" '.$visible['false'].'> ซ่อน</span></li>', EOL;
        echo '<li><span class="left w200">ชื่อ : </span><input type="text" name="name" value="'.$namedata['na_name'].'" class="w400"></li>', EOL;
        if($mode=='edit')
            echo '<li><span class="left w200">ชื่อแบบย่อ : </span><input type="text" name="name_uri" value="'.$namedata['na_name_uri'].'" class="w400"></li>', EOL;
        echo '<li><span class="left w200">รูปปก : </span><label for="cover"><img id="preview" src="'.$image_cover .'" alt="Cover" /></label><input type="file" name="cover" id="cover" class="w400"></li>', EOL;
        echo '<li><span class="left w200">รายละเอียด : </span><textarea  name="details" style="width:400px;height:200px">'.htmlentities($namedata['na_detail']).'</textarea></li>', EOL;
        echo '<li><span class="left w200">เว็ปไซต์ : </span><input type="text" name="uri" value="'.$namedata['na_uri'].'" class="w400"></li>', EOL;
        echo '<li><span class="left w200">แม่แบบลิ้ง : </span><input type="text" name="uri_template" value="'.$namedata['na_uri_template'].'" class="w400"></li>', EOL;
        if($mode=='edit')
            echo '<li><span class="left w200">ตอนล่าสุด : </span><input type="text" name="last" value="'.floatval($namedata['na_last']).'" class="w400"></li>', EOL;
        echo '<li><span class="left w200">&nbsp;</span><input type="submit" value="บันทึก" class="ui" class="w100"></li>', EOL;
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
        echo '<li><span class="left w200">เรื่อง : </span><select name="sub_id" class="w400">', EOL;
        echo '<option value="0" selected="selected">ยังไม่ได้เลือก</option>', EOL;
        $getname = $_database->query("select * from ".DB_PREFIX."name where na_end!=1 order by na_name asc");
        foreach ($getname as $name)
            echo '<option value="'.$name['na_id'].'">'.$name['na_name'].'</option>', EOL;
        echo '</select></li>', EOL;
        echo '<li><span class="left w200">ตอนที่ : </span><input type="text" name="chapter" class="w400"></li>', EOL;
        echo '<li><span class="left w200">ชื่อตอน : </span><input type="text" name="title" class="w400"></li>', EOL;
        echo '<li><span class="left w200">เว็ปไซต์ : </span><input type="text" name="uri" class="w400"></li>', EOL;
        echo '<li><span class="left w200">&nbsp;</span><input type="submit" value="เพิ่ม" class="ui w100"></li>', EOL;
        echo '</ul>', EOL;
        echo '</form>';
        echo '</div>', EOL;
        echo '<div class="clear"></div>', EOL;
        echo '</div>', EOL;
        break;

    case 'crop' :
        $image_id = (int)getDatafromUri(2);
        $_title = 'การตั้งค่า - ตัดรูป หมายเลข '.$image_id;
        $_assets[] = 'jcrop';
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
        echo '<li><input type="submit" value="ตัดรูปภาพ" class="ui w100" /></li>', EOL;
        echo '</form>', EOL;
        echo '</ul>', EOL;
        echo '</form>', EOL;
        echo '</div>', EOL;
        echo '<div class="clear"></div>', EOL;
        echo '</div>', EOL;
        break;

    case 'general' :
        $_title = 'การตั้งค่า - การตั้งค่าทั่วไป';
        $cache_download = checked($_settings['cache_download_file']);
        $recompassed = checked($_settings['recompassed']);
        $output_extension = checked($_settings['output_extension'], 'jpg,png');
        $png_optimizer = checked($_settings['png_optimizer']);
        $episode_prefix = checked($_settings['episode_prefix']);
        echo '<h1 class="title"><a href="'.URI_PATH.'/setting" title="การตั้งค่า"><i class="fa fa-cog"></i></a> : การตั้งค่าทั่วไป</h1>', EOL;
        echo '<div class="contents">', EOL;
        echo '<div class="index">', EOL;
        echo '<ul class="article-list">', EOL;
        echo '<form method="post" action="/api" enctype="multipart/form-data">';
        echo '<input type="hidden" name="action" value="savesetting">', EOL;
        echo '<li><h2>ตั้งค่าการแสดงผล</h2></li>', EOL;
        echo '<li><span class="left w200">ชื่อหัวเว็ปหลัก : </span><input type="text" name="setting[title]" class="w400" value="'.$_settings['title'].'"></li>', EOL;
        echo '<li><span class="left w200">Facebook : </span><input type="text" name="setting[facebook]" class="w400" value="'.$_settings['facebook'].'"> ถ้าเว้นว่างจะไม่แสดงผล</li>', EOL;
        echo '<li><span class="left w200">Twitter : </span><input type="text" name="setting[twitter]" class="w400" value="'.$_settings['twitter'].'"> ถ้าเว้นว่างจะไม่แสดงผล</li>', EOL;
        echo '<li><span class="left w200">Github : </span><input type="text" name="setting[github]" class="w400" value="'.$_settings['github'].'"> ถ้าเว้นว่างจะไม่แสดงผล</li>', EOL;
        echo '<li><span class="left w200">Copy Right : </span><input type="text" name="setting[copy_right]" class="w400" value="'.htmlentities($_settings['copy_right']).'"></li>', EOL;
        echo '<li><span class="left w200">ความกว้างรูปย่อ : </span><input type="text" name="setting[thumbnail_width]" class="w400" value="'.$_settings['thumbnail_width'].'"> พิกเซลล์</li>', EOL;
        echo '<li><span class="left w200">ความสูงรูปย่อ : </span><input type="text" name="setting[thumbnail_height]" class="w400" value="'.$_settings['thumbnail_height'].'"> พิกเซลล์</li>', EOL;
        echo '<li><span class="left w200">ระยะเวลาแคช : </span><input type="text" name="setting[cache_time]" class="w400" value="'.$_settings['cache_time'].'"> วินาที</li>', EOL;
        //echo '<li><span class="left w200">ภาพสำหรับข้อผิดพลาด : </span><input type="text" name="setting[image_error]" class="w400" value="'.$_settings['image_error'].'"> เว้นว่างใช้ค่าเริ่มต้น</li>', EOL;
        //echo '<li><span class="left w200">ภาพเริ่มต้นของปก : </span><input type="text" name="setting[image_cover]" class="w400" value="'.$_settings['image_cover'].'"> เว้นว่างใช้ค่าเริ่มต้น</li>', EOL;
        echo '<li><h2>ตั้งค่าดาวน์โหลด</h2></li>', EOL;
        echo '<li><span class="left w200">ที่อยู่ดาวน์โหลด : </span><input type="text" name="setting[download_directory]" class="w400" value="'.$_settings['download_directory'].'"></li>', EOL;
        echo '<li><span class="left w200">ที่อยู่แคช : </span><input type="text" name="setting[cache_directory]" class="w400" value="'.$_settings['cache_directory'].'"></li>', EOL;
        echo '<li><span class="left w200">เปิดใช้งานแคช : </span><span class="left w100"><input type="radio" name="setting[cache_download_file]" value="true"'.$cache_download['true'].'> เปิด</span><span class="w100"><input type="radio" name="setting[cache_download_file]" value="false"'.$cache_download['false'].'> ปิด</span></li>', EOL;
        echo '<li><span class="left w200">ที่อยู่คุกกี้ : </span><input type="text" name="setting[cookie_directory]" class="w400" value="'.$_settings['cookie_directory'].'"></li>', EOL;
        echo '<li><span class="left w200">ที่อยู่แฟ้มCA : </span><input type="text" name="setting[ca_cert_filename]" class="w400" value="'.$_settings['ca_cert_filename'].'"></li>', EOL;
        echo '<li><span class="left w200">คำยกเว้น : </span><input type="text" name="setting[skip_word]" class="w400" value="'.$_settings['skip_word'].'"> ใช้ , คั่น</li>', EOL;
        echo '<li><span class="left w200">โดเมนพิเศษ : </span><input type="text" name="setting[special_domains]" class="w400" value="'.$_settings['special_domains'].'"> ใช้ , คั่น</li>', EOL;
        echo '<li><span class="left w200">URL แพทเทิร์น : </span><input type="text" name="setting[url_pattern]" class="w400" value="'.htmlentities($_settings['url_pattern']).'"></li>', EOL;
        echo '<li><span class="left w200">Java Script แพทเทิร์น : </span><input type="text" name="setting[javascript_pattern]" class="w400" value="'.htmlentities($_settings['javascript_pattern']).'"></li>', EOL;
        echo '<li><span class="left w200">User Agent : </span><input type="text" name="setting[browser_useragent]" class="w400" value="'.$_settings['browser_useragent'].'"></li>', EOL;
        echo '<li><span class="left w200">เข้ารหัสไฟล์ใหม่ : </span><span class="left w100"><input type="radio" name="setting[recompassed]" value="true"'.$recompassed['true'].'> เปิด</span><span class="w100"><input type="radio" name="setting[recompassed]" value="false"'.$recompassed['false'].'> ปิด</span></li>', EOL;
        echo '<li><span class="left w200">เข้ารหัสไฟล์ใหม่เป็น : </span><span class="left w100"><input type="radio" name="setting[output_extension]" value="jpg"'.$output_extension['jpg'].'> JPEG</span><span class="w100"><input type="radio" name="setting[output_extension]" value="png"'.$output_extension['png'].'> PNG</span></li>', EOL;
        echo '<li><span class="left w200">คุณภาพของ PNG : </span><input type="text" name="setting[png_compression]" class="w400" value="'.$_settings['png_compression'].'"> 0-9</li>', EOL;
        echo '<li><span class="left w200">คุณภาพของ JPEG : </span><input type="text" name="setting[jpeg_quality]" class="w400" value="'.$_settings['jpeg_quality'].'"> 0-100</li>', EOL;
        echo '<li><span class="left w200">ที่อยู่ Image Magick : </span><input type="text" name="setting[ImageMagick_filename]" class="w400" value="'.$_settings['ImageMagick_filename'].'"></li>', EOL;
        echo '<li><span class="left w200">ที่อยู่ PDFConvert : </span><input type="text" name="setting[PDFConvert_filename]" class="w400" value="'.$_settings['PDFConvert_filename'].'"></li>', EOL;
        echo '<li><span class="left w200">ที่อยู่ PNG Optimizer : </span><input type="text" name="setting[png_optimizer_filename]" class="w400" value="'.$_settings['png_optimizer_filename'].'"></li>', EOL;
        echo '<li><span class="left w200">เปิดใช้งาน PNG Optimizer : </span><span class="left w100"><input type="radio" name="setting[png_optimizer]" value="true"'.$png_optimizer['true'].'> เปิด</span><span class="w100"><input type="radio" name="setting[png_optimizer]" value="false"'.$png_optimizer['false'].'> ปิด</span></li>', EOL;
        echo '<li><span class="left w200">ขนาดรูปที่ยอมรับ : </span><input type="text" name="setting[accept_dimension]" class="w400" value="'.$_settings['accept_dimension'].'"> พิกเซลล์</li>', EOL;
        echo '<li><span class="left w200">ขนาดไฟล์รูปที่ยอมรับ : </span><input type="text" name="setting[accept_bytes]" class="w400" value="'.$_settings['accept_bytes'].'"> ไบต์</li>', EOL;
        echo '<li><span class="left w200">เวลาเชื่อมต่อ CURL : </span><input type="text" name="setting[curl_timeout]" class="w400" value="'.$_settings['curl_timeout'].'"> วินาที</li>', EOL;
        echo '<li><span class="left w200">ดาวน์โหลดซ้ำ : </span><input type="text" name="setting[resume_download]" class="w400" value="'.$_settings['resume_download'].'"> ครั้ง</li>', EOL;
        echo '<li><h2>ตั้งค่าส่งออก</h2></li>', EOL;
        echo '<li><span class="left w200">ที่อยู่ส่งออก : </span><input type="text" name="setting[export_directory]" class="w400" value="'.$_settings['export_directory'].'"></li>', EOL;
        echo '<li><span class="left w200">จำนวนหลักชื่อไฟล์ : </span><input type="text" name="setting[page_digit]" class="w400" value="'.$_settings['page_digit'].'"> หลัก</li>', EOL;
        echo '<li><span class="left w200">ขึ้นต้นไฟล์ด้วยตอนที่: </span><span class="left w100"><input type="radio" name="setting[episode_prefix]" value="true"'.$episode_prefix['true'].'> เปิด</span><span class="w100"><input type="radio" name="setting[episode_prefix]" value="false"'.$episode_prefix['false'].'> ปิด</span></li>', EOL;
        echo '<li><span class="left w200">&nbsp;</span><input type="submit" value="บันทึก" class="ui w100"></li>', EOL;
        echo '</form>', EOL;
        echo '</ul>', EOL;
        echo '</div>', EOL;
        echo '<div class="clear"></div>', EOL;
        echo '</div>', EOL;
        break;

    case 'import' :
        $_title = 'การตั้งค่า - จัดการการนำเข้า';
        echo '<h1 class="title"><a href="'.URI_PATH.'/setting" title="การตั้งค่า"><i class="fa fa-cog"></i></a> : จัดการการนำเข้า</h1>', EOL;
        echo '<div class="contents">', EOL;
        echo '<div class="index">', EOL;
        echo '<ul class="article-list">', EOL;
        echo '<li><i class="fa fa-plus"></i> <a href="'.URI_PATH.'/setting/import/add">เพิ่มการนำเข้า</a></li>', EOL;
        $getimport = $_database->query("select * from ".DB_PREFIX."import order by im_domain asc");
        foreach($getimport as $import)
            echo '<li><i class="fa fa-caret-right"></i> <a href="'.URI_PATH.'/setting/import/edit/'.$import['im_id'].'">'.$import['im_domain'].'</a><time>'.DateFormat($import['im_date']).'</time></li>', EOL;
        echo '</ul>', EOL;
        echo '</div>', EOL;
        echo '<div class="clear"></div>', EOL;
        echo '</div>', EOL;
        break;

    case 'about' :
        $_title = 'การตั้งค่า - เกี่ยวกับ';
        $_mainactive = '4';
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
        if(!$action)
            {
            $_title = 'การตั้งค่า';
            echo '<h1 class="title"><i class="fa fa-cog"></i> การตั้งค่า</h1>', EOL;
            echo '<div class="contents">', EOL;
            echo '<div class="index">', EOL;
            echo '<ul class="article-list">', EOL;
            echo '<li><span class="icon"><i class="fa fa-plus"></i> </span><a href="'.URI_PATH.'/setting/name/add">เพิ่มเรื่องใหม่</a></li>', EOL;
            echo '<li><span class="icon"><i class="fa fa-plus"></i> </span><a href="'.URI_PATH.'/setting/addchapter">เพิ่มตอนใหม่</a></li>', EOL;
            echo '<li><span class="icon"><i class="fa fa-pencil"></i> </span><a href="'.URI_PATH.'/setting/manage">การจัดการและการแก้ไข</a></li>', EOL;
            echo '<li><span class="icon"><i class="fa fa-cloud-download"></i> </span><a href="'.URI_PATH.'/setting/import">จัดการการนำเข้า</a></li>', EOL;
            echo '<li><span class="icon"><i class="fa fa-cog"></i> </span><a href="'.URI_PATH.'/setting/general">การตั้งค่าทั่วไป</a></li>', EOL;
            echo '<li><span class="icon"><i class="fa fa-database"></i> </span><a href="'.URI_PATH.'/setting/dbstatus">สถานะฐานข้อมูล</a></li>', EOL;
            echo '<li><span class="icon"><i class="fa fa-info"></i> </span><a href="'.URI_PATH.'/setting/about">เกี่ยวกับ Neko Viewer</a></li>', EOL;
            echo '</ul>', EOL;
            echo '</div>', EOL;
            echo '<div class="clear"></div>', EOL;
            echo '</div>', EOL;
            break;
            }
        else
            {
            $_title = 'ไม่พบหน้าที่ร้องขอมา';
            $_error_message = 'ไม่พบหน้าที่ร้องขอมาหรือคุณไม่มีสิทธิ์เข้าถึงหน้าดังกล่าว';
            require_once 'Viewer/Error.php';
            }

    }

?>