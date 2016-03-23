<?php

$_assets[] = 'font-awesome';
$_assets[] = 'jquery';
$_assets[] = 'pace';
$_assets[] = 'flif';
$_assets[] = 'bpg';
$_assets[] = 'prettyPhoto';
$_assets[] = 'colortip';
$_assets[] = 'lazyload';
$_assets[] = 'common';

$_mainmenu[] = ['หน้าหลัก', URI_PATH.'/', 1];
$_mainmenu[] = ['รายชื่อทั้งหมด', URI_PATH.'/all', 0];
$_mainmenu[] = ['การตั้งค่า', URI_PATH.'/setting', 0];
$_mainmenu[] = ['สถานะฐานข้อมูล', URI_PATH.'/setting/dbstatus', 0];
$_mainmenu[] = ['เกี่ยวกับ', URI_PATH.'/setting/about', 0];

echo '<section class="main container_12">', EOL;
echo '<div class="row">', EOL;
echo '<div class="area-header"></div>', EOL;
echo '<div class="block-contents">', EOL;
echo '<div class="bg-contents"></div>', EOL;

$read_name = getDatafromUri(0);
$read_chapter = getDatafromUri(1);

if($read_name && ($read_chapter !== False))
    {
    $getchapter = $_database->query("
    select ".DB_PREFIX."name.na_id, ".DB_PREFIX."name.na_name, ".DB_PREFIX."chapter.ch_name_id, ".DB_PREFIX."chapter.ch_number,
    ".DB_PREFIX."chapter.ch_title, ".DB_PREFIX."chapter.ch_id, ".DB_PREFIX."chapter.ch_image_id
    from ".DB_PREFIX."name inner join ".DB_PREFIX."chapter
    on ".DB_PREFIX."name.na_id=".DB_PREFIX."chapter.ch_name_id
    where ".DB_PREFIX."name.na_name_uri='".$read_name."'
    and (".DB_PREFIX."chapter.ch_number>=".($read_chapter-1)." and ".DB_PREFIX."chapter.ch_number<=".($read_chapter+1).")
    order by ".DB_PREFIX."chapter.ch_number asc");
    $chapter= $getchapter->fetchAll(PDO::FETCH_ASSOC);
    $key = array_search($read_chapter, array_column($chapter, 'ch_number'));
    $_title = $chapter[$key]['na_name'].' ตอนที่ '.$read_chapter;
    echo '<h1 class="title"><a href="'.URI_PATH.'/" title="กลับหน้าแรก"><i class="fa fa-home"></i></a> : <a href="'.URI_PATH.'/'.$read_name.'">'.$chapter[$key]['na_name'].'</a></h1>', EOL;
    echo '<h2 class="title">ตอนที่ '.$read_chapter.' : '.$chapter[$key]['ch_title'].'</h2>', EOL;
    echo '<div class="contents">', EOL;
    if(isset($chapter[$key+1]['ch_number']))
        echo '<div class="archive-link"><a href="'.URI_PATH.'/'.$read_name.'/'.floatval($chapter[$key+1]['ch_number']).'">Next <i class="fa fa-share"></i></a></div>', EOL;
    if(isset($chapter[$key-1]['ch_number']))
        echo '<div class="archive-link"><a href="'.URI_PATH.'/'.$read_name.'/'.floatval($chapter[$key-1]['ch_number']).'"><i class="fa fa-reply"></i>Back</a></div>', EOL;
    echo '<div class="clear"></div>', EOL;
    echo '<div class="index">', EOL;
    echo '<ul class="article-list">', EOL;
    $_database->query("update cv_chapter set ch_readed=1 where ch_id=".$chapter[$key]['ch_id']);
    foreach ( toArray($chapter[$key]['ch_image_id']) as $page=>$image)
        {
        echo '<li>'.($page+1).' <a class="delete" href="/api/deleteimg/'.$chapter[$key]['ch_id'].'/'.$image.'" title="ลบรูป"> <i class="fa fa-times"></i></a>';
        echo '<a href="/setting/crop/'.$image.'" title="ตัดรูป" target="_blank"> <i class="fa fa-crop"></i></a>';
        echo '<br><a rel="prettyPhoto[pp_gal]" title="" href="'.URI_PATH.'/image/'.$image.'">';
        echo '<img class="lazy" data-original="'.URI_PATH.'/image/'.$image.'" width="100%" border="0"></a></il>', EOL;
        }
    echo '</ul>', EOL;
    echo '</div>', EOL;
    if(isset($chapter[$key+1]['ch_number']))
        echo '<div class="archive-link"><a href="'.URI_PATH.'/'.$read_name.'/'.floatval($chapter[$key+1]['ch_number']).'">Next <i class="fa fa-share"></i></a></div>', EOL;
    if(isset($chapter[$key-1]['ch_number']))
        echo '<div class="archive-link"><a href="'.URI_PATH.'/'.$read_name.'/'.floatval($chapter[$key-1]['ch_number']).'"><i class="fa fa-reply"></i>Back</a></div>', EOL;
    echo '<div class="clear"></div>', EOL;
    echo '</div>', EOL;
    }
elseif($read_name == 'all')
    {
    $_title = 'รายชื่อทั้งหมด';
    $getname = $_database->query("select na_name, na_name_uri, na_end, substring(na_name, 1, 1) as letter from ".DB_PREFIX."name order by na_name asc");
    $names = array();
    foreach ($getname->fetchAll(PDO::FETCH_ASSOC) as $name)
        $names[$name['letter']][] = $name;
    echo '<h1 class="title"><a href="'.URI_PATH.'/" title="กลับหน้าแรก"><i class="fa fa-home"></i></a> : รายชื่อทั้งหมด</h1>', EOL;
    echo '<div class="contents">', EOL;
    echo '<div class="index">', EOL;
    foreach($names as $letter=>$name)
    {
        echo '<div><h1>'.$letter.'</h1>', EOL;
        foreach($name as $detail)
            {
            $end = $detail['na_end'] ? '<span style="color:green">(End)</span>' : '';
            echo '<div> <a href="'.URI_PATH.'/'.$detail['na_name_uri'].'">'.$detail['na_name'].'</a> '.$end.'</div>', EOL;
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
        echo '<li>'.$name['na_detail'].' <a href="'.$name['na_uri'].'" target="_blank" title="ไปยังเว็ปที่มา"><i class="fa fa-globe"></i></a></li>';
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
                echo ' <a href="/api/unread/'.$chapter['ch_id'].'" title="อ่านแล้ว คลิกเพื่อทำว่ายังไม่ได้อ่าน"><i class="fa fa-check-circle-o"></i></a>', EOL;
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
        echo '<h1 class="title"><a href="'.URI_PATH.'/" title="กลับหน้าแรก"><i class="fa fa-home"></i></a> : เกิดข้อผิดพลาด</h1>', EOL;
        echo '<div class="contents">', EOL;
        echo '<div class="index">', EOL;
        echo '<h2 class="center"><i class="fa fa-exclamation-triangle"></i> ไม่พบหน้าที่ร้องขอมา</h2>', EOL;
        echo '</div>', EOL;
        echo '<div class="clear"></div>', EOL;
        echo '</div>', EOL;
        }
    }
else
    {
    $_title = 'หน้าหลัก';
    echo '<h1 class="title"><i class="fa fa-home"></i> รายการมังงะ</h1>', EOL;
    echo '<div class="contents">', EOL;
    echo '<div class="index center">', EOL;
    echo '<div class="left">', EOL;
    $getname = $_database->query("select * from ".DB_PREFIX."name where na_sub_id=0 order by na_name asc");
    foreach ($getname->fetchAll(PDO::FETCH_ASSOC) as $name)
        {
        $block = $name['na_end'] ? 'name-block ended' : 'name-block';
        echo '<div class="'.$block .'"> <a href="'.URI_PATH.'/'.$name['na_name_uri'].'">';
        echo '<img src="'.URI_PATH.'/image/'.$name['na_image_id'].'" width="200" height="285" border="0">'.limitText($name['na_name']).'</a></div>', EOL;
        }
    echo '</div>', EOL;
    echo '</div>', EOL;
    echo '<div class="clear"></div>', EOL;
    echo '</div>', EOL;
    }

echo '</div>', EOL;
echo '</div>', EOL;
echo '</section>', EOL;

