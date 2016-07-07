<pre>
<?php

require_once './Viewer/Bootstrap.php';
if (ob_get_level() == 0) ob_start();





$data = file_get_contents('./Assets/common/img/404.jpg');
file_put_contents('404.txt', bin2hex($data));

/*
$doc = new Neko\htmlParser();
$doc->loadFile('http://www.kingsmanga.net/bokura-wa-minna-kawaisou-5/');
echo $doc->query('//h1[@class="entry-title"]')->getValue(0);
$url = $doc->query('//div[@class="post-content"]//img')->getAttribute('src');
var_dump(mtrim($url));


$start = '289';
$stop = '';

for ($i = $start; $i <= ($stop ? $stop : $start) ; $i++)
{
    $doc = new Neko\htmlParser();
    $doc->remove_javascripts = true;
    $doc->remove_stylesheet = true;
    $doc->loadFile('zip://'.realpath('../NekoNovel/Download/tdg/tdg-readtdg2.zip').'#tales-of-demons-gods-'.$i.'.html');
    $url = $doc->query('//link[@rel="canonical"]')->getAttribute('href', 0);
    $title = $doc->query('//h3[@class="post-title entry-title"]')->getValue(0);
    $novel = $doc->query('//div[@class="post-body entry-content"]/*')->getValue();
    preg_match('/(\d+).(.*)/', $title, $titles);
    $title = mtrim($titles[2]);
    $novel = mtrim($novel);
    $cont = '';
    foreach ($novel as $line)
        if ($line == '')
            $cont .= PHP_EOL.PHP_EOL;
        else
            $cont .= str_replace("\n", '', $line).' ';
    $novel = str_replace("คลิกเพื่อไปหน้าโฆษณาสนับสนุนเพจ", '', $cont);
    $novel = preg_replace("/\r\n\r\n/", PHP_EOL, $novel);
    echo 'Ch. '.$titles[1].' - '.$title.PHP_EOL;
    //echo $novel;
    $_database->query("update cv_name set na_last='".$i."' where cv_name.na_id='164'");
    $_database->query("insert into cv_chapter (ch_name_id, ch_number, ch_title, ch_uri, ch_content) values ('164', '".$i."', '".addslashes($title)."', '".addslashes($url)."', '".addslashes($novel)."')");
    ob_flush();
    flush();
    }



/*
function splitChapter ( $Chapters )
        {
        $SplitChapter = array();
        $ChapterExplode = toArray ( $Chapters );
        foreach ( $ChapterExplode as $Chapter )
            {
            if ( strpos ( $Chapter, '-' ) )
                {
                $CountChapter = toArray ( $Chapter , '-' );
                $SplitChapter = array_merge ( $SplitChapter, range( $CountChapter['0'], $CountChapter['1'] )
                }
            else
                {
                $SplitChapter[] = $Chapter;
                }
            }
        return $SplitChapter;
        }



$gr = $_database->query("

select * 
FROM `cv_chapter`
group by `ch_name_id`
ORDER BY ch_id DESC limit 0,10

");

$de = $gr->fetchAll(PDO::FETCH_ASSOC);
var_dump($de);
//foreach ($de as $be)
//echo '<pre>'.$be['ch_name_id'].'</pre>';

*/
ob_end_flush();
?>

</pre>