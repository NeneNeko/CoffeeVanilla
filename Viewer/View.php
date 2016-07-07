<?php

$read_name = NametoUri(getDatafromUri(0));
$read_chapter = getDatafromUri(1);

if($read_name && ($read_chapter !== false))
    {
    $read_chapter = floatval($read_chapter);
    $getchapter = $_database->query("
    select ".DB_PREFIX."name.na_id, ".DB_PREFIX."name.na_type, ".DB_PREFIX."name.na_name, ".DB_PREFIX."chapter.ch_name_id, 
    ".DB_PREFIX."chapter.ch_number, ".DB_PREFIX."chapter.ch_title, ".DB_PREFIX."chapter.ch_id, ".DB_PREFIX."chapter.ch_content
    from ".DB_PREFIX."name inner join ".DB_PREFIX."chapter on ".DB_PREFIX."name.na_id=".DB_PREFIX."chapter.ch_name_id
    where ".DB_PREFIX."name.na_name_uri='".$read_name."'
    order by ".DB_PREFIX."chapter.ch_number asc");
    /* and (".DB_PREFIX."chapter.ch_number>=".($read_chapter-1)." and ".DB_PREFIX."chapter.ch_number<=".($read_chapter+1).") */
    $chapter = $getchapter->fetchAll(PDO::FETCH_ASSOC);
    $search_key = array_search($read_chapter, increaseIndex(array_column($chapter, 'ch_number')));
    $current_key = $search_key-1;
    $current_chapter = $chapter[$current_key];
    $next_chapter = isset($chapter[$current_key+1]['ch_number']) ? floatval($chapter[$current_key+1]['ch_number']) : false;
    $back_chapter = isset($chapter[$current_key-1]['ch_number']) ? floatval($chapter[$current_key-1]['ch_number']) : false;
    if ($search_key)
        {
        $_title = $current_chapter['na_name'].' ตอนที่ '.$read_chapter;
        echo '<h1 class="title"><a href="'.URI_PATH.'/" title="กลับหน้าแรก"><i class="fa fa-home"></i></a> : <a href="'.URI_PATH.'/'.$read_name.'">'.$current_chapter['na_name'].'</a></h1>', EOL;
        echo '<h2 class="title">ตอนที่ '.$read_chapter.' : '.$current_chapter['ch_title'].'</h2>', EOL;
        echo '<div class="contents">', EOL;
        if($current_chapter['na_type'] == 'N')
            {
            echo '<div class="left"><a id="increaseFont" style=" cursor: pointer;cursor: hand;" title="เพิ่มขนาดตัวอักษร"><i class="fa fa-arrow-up"></i></a>  ', EOL;
            echo '<a id="decreaseFont" style=" cursor: pointer;cursor: hand;" title="ลดขนาดตัวอักษร"><i class="fa fa-arrow-down"></i> ', EOL;
            echo '</a> <a id="largeFont" style=" cursor: pointer;cursor: hand;" title="ขนาดตัวอักษรใหญ่สุด"><i class="fa fa-bold"></i></a>', EOL;
            echo '<a id="smallFont" style=" cursor: pointer;cursor: hand;" title="ขนาดตัวอักษรเล็กสุด"><i class="fa fa-font"></i> </a> ', EOL;
            echo '<a id="resetFont" style=" cursor: pointer;cursor: hand;" title="ขนาดตัวอักษรเริ่มต้น"><i class="fa fa-undo"></i> </a></div>', EOL;
            }
        if($next_chapter)
            echo '<div class="archive-link"><a href="'.URI_PATH.'/'.$read_name.'/'.$next_chapter.'">Next <i class="fa fa-share"></i></a></div>', EOL;
        if($back_chapter)
            echo '<div class="archive-link"><a href="'.URI_PATH.'/'.$read_name.'/'.$back_chapter.'"><i class="fa fa-reply"></i>Back</a></div>', EOL;
        echo '<div class="clear"></div>', EOL;
        echo '<div class="index">', EOL;
        $_database->query("update cv_chapter set ch_readed=1 where ch_id=".$current_chapter['ch_id']);
        if($current_chapter['na_type'] == 'M')
            {
            $_assets[] = 'prettyPhoto';
            $_assets[] = 'lazyload';    
            foreach(toArray($current_chapter['ch_content']) as $page=>$image)
                {
                echo '<ul class="article-list">', EOL;
                echo '<li>'.($page+1).' <a class="delete" href="'.URI_PATH.'/api/deleteimg/'.$current_chapter['ch_id'].'/'.$image.'" title="ลบรูป"> <i class="fa fa-times"></i></a>';
                echo '<a href="/setting/crop/'.$image.'" title="ตัดรูป" target="_blank"> <i class="fa fa-crop"></i></a>';
                echo '<br><a rel="prettyPhoto[pp_gal]" title="" href="'.URI_PATH.'/image/'.$image.'">';
                echo '<img class="lazy" data-original="'.URI_PATH.'/image/'.$image.'" width="100%" border="0"></a></il>', EOL;
                echo '</ul>', EOL;
                }
            }
        elseif($current_chapter['na_type'] == 'N')       
            {
            $_assets[] = 'fontresize';
            echo '<div id="article-contents">', EOL;
            //$Parsedown = new ParsedownExtra();
            //$Parsedown->setBreaksEnabled(true);
            //echo $Parsedown->text($current_chapter['ch_content']);
            $bbcode = new Golonka\BBCode\BBCodeParser;
            echo $bbcode->parse($current_chapter['ch_content']);
            echo '</div>', EOL;
            }
        else
            {
            $_title = 'ประเภทไม่ถูกต้อง';
            $_error_message = 'ประเภทไม่ถูกต้อง';
            require_once 'Viewer/Error.php';
            }  
        echo '</div>', EOL;
        if($next_chapter)
            echo '<div class="archive-link"><a href="'.URI_PATH.'/'.$read_name.'/'.$next_chapter.'">Next <i class="fa fa-share"></i></a></div>', EOL;
        if($back_chapter)
            echo '<div class="archive-link"><a href="'.URI_PATH.'/'.$read_name.'/'.$back_chapter.'"><i class="fa fa-reply"></i>Back</a></div>', EOL;
        echo '<div class="clear"></div>', EOL;
        echo '</div>', EOL;
        }
     else
        {
        $_title = 'ไม่พบหน้าที่ร้องขอมา';
        $_error_message = 'ไม่พบหน้าที่ร้องขอมาหรือหน้านี้ถูกลบไปแล้ว';
        require_once 'Viewer/Error.php';
        }
    }
elseif($read_name == 'all')
    {
    $_title = 'รายชื่อทั้งหมด';
    $_mainactive = '2';
    $getname = $_database->query("select na_name, na_name_uri, na_last, na_end, substring(na_name, 1, 1) as letter from ".DB_PREFIX."name where na_visible='true' order by na_name asc");
    $names = array();
    foreach ($getname->fetchAll(PDO::FETCH_ASSOC) as $name)
        if(ord(strtoupper($name['letter'])) <= 90 && ord(strtoupper($name['letter'])) >= 65)
            $names[$name['letter']][] = $name;
        else
            $names['#'][] = $name;
    ksort($names);
    echo '<h1 class="title"><a href="'.URI_PATH.'/" title="กลับหน้าแรก"><i class="fa fa-home"></i></a> : รายชื่อทั้งหมด</h1>', EOL;
    echo '<div class="contents">', EOL;
    echo '<div class="index">', EOL;
    foreach($names as $letter=>$name)
    {
        echo '<div><h1>'.$letter.'</h1>', EOL;
        foreach($name as $detail)
            {
            $end = selfBool($detail['na_end']) ? '<span style="color:green">(End)</span>' : '';
            echo '<div> <a href="'.URI_PATH.'/'.$detail['na_name_uri'].'">'.$detail['na_name'].'</a> '.floatval($detail['na_last']).' '.$end.'</div>', EOL;
            }
        echo '</div><br/>', EOL;
    }
    echo '</div>', EOL;
    echo '<div class="clear"></div>', EOL;
    echo '</div>', EOL;
    }
elseif($read_name)
    {
    $getname = $_database->query("select * from ".DB_PREFIX."name where na_name_uri='".$read_name."'");
    $name = $getname->fetch(PDO::FETCH_ASSOC);
    if($name)
        {
        $_title = $name['na_name'];
        echo '<h1 class="title"><a href="'.URI_PATH.'/" title="กลับหน้าแรก"><i class="fa fa-home"></i></a> : '.$name['na_name'].'</h1>', EOL;
        echo '<div class="contents">', EOL;
        echo '<div class="index">', EOL;
        echo '<ul class="article-list">', EOL;
        $bbcode = new Golonka\BBCode\BBCodeParser;
        $detail = $bbcode->parse($name['na_detail']);
        echo '<li>'.$detail.' <a href="'.$name['na_uri'].'" target="_blank" title="ไปยังเว็ปที่มา"><i class="fa fa-globe"></i></a></li>';
        echo '<div>', EOL;
        $getsubname = $_database->query("select * from ".DB_PREFIX."name where na_sub_id=".$name['na_id']." order by na_name asc");
        foreach ($getsubname->fetchAll(PDO::FETCH_ASSOC) as $subname)
            {
            echo '<div class="name-subblock"> <a href="'.URI_PATH.'/'.$subname['na_name_uri'].'">';
            echo '<img src="'.URI_PATH.'/image/'.$subname['na_image_id'].'" width="120" height="155" border="0">'.limitText($subname['na_name']).'</a></div>', EOL;
            }
        echo '</div>', EOL;
        $getchapter = $_database->query("select * from ".DB_PREFIX."chapter where ch_name_id=".$name['na_id']." order by ch_number desc");
        foreach ($getchapter->fetchAll(PDO::FETCH_ASSOC) as $chapter)
            {
            $chapter['ch_number'] = floatval($chapter['ch_number']);
            if ($chapter['ch_title'] == null)
                $chapter['ch_title'] = 'Chapter '.$chapter['ch_number'];
            echo '<li> <a href="'.URI_PATH.'/'.$read_name.'/'.$chapter['ch_number'].'">ตอนที่ '.$chapter['ch_number'].' - '.$chapter['ch_title'].'</a>';
            if ($chapter['ch_readed'])
                echo ' <a href="'.URI_PATH.'/api/unread/'.$chapter['ch_id'].'" title="อ่านแล้ว คลิกเพื่อทำว่ายังไม่ได้อ่าน"><i class="fa fa-check-circle-o"></i></a>', EOL;
            echo '<time>'.DateFormat($chapter['ch_date']).'</time></il>', EOL;
            }
        echo '</ul>', EOL;
        echo '</div>', EOL;
        echo '<div class="clear"></div>', EOL;
        echo '</div>', EOL;
        }
    else
        {
        $_title = 'ไม่พบหน้าที่ร้องขอมา';
        $_error_message = 'ไม่พบหน้าที่ร้องขอมาหรือหน้านี้ถูกลบไปแล้ว';
        require_once 'Viewer/Error.php';
        }
    }
else
    {
    $_title = 'หน้าหลัก';
    echo '<h1 class="title"><i class="fa fa-home"></i> รายการมังงะ</h1>', EOL;
    echo '<div class="contents">', EOL;
    echo '<div class="index center">', EOL;
    echo '<div class="left">', EOL;
    $getname = $_database->query("select * from ".DB_PREFIX."name where na_sub_id=0 and na_visible='true' order by na_name asc");
    if($names = $getname->fetchAll(PDO::FETCH_ASSOC))
        {
        foreach ($names as $name)
            {
            $block = selfBool($name['na_end']) ? 'name-block ended' : 'name-block';
            if($name['na_image_id'] == '0' )
                $image_cover = URI_PATH.'/image/2';
            else
                $image_cover = URI_PATH.'/image/'.$name['na_image_id'];
            echo '<div class="'.$block .' "> <a href="'.URI_PATH.'/'.$name['na_name_uri'].'">';
            echo '<img src="'.$image_cover.'" width="200" height="285" border="0">'.limitText($name['na_name']).'</a></div>', EOL;
            }
        }
    else
        {
        echo '<div><i class="fa fa-plus"></i> <a href="'.URI_PATH.'/setting/name/add">เพิ่มเรื่องใหม่</a></div>', EOL;
        }
    echo '</div>', EOL;
    echo '</div>', EOL;
    echo '<div class="clear"></div>', EOL;
    echo '</div>', EOL;
    }