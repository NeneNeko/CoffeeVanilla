<?php

ob_end_clean();
ob_start();

if($_fatalerror)
    {
    $_assets[] = 'font-awesome';
    $_assets[] = 'jquery';
    $_assets[] = 'pace';
    $_assets[] = 'colortip';
    $_assets[] = 'common';
    $_mainmenu[] = ['หน้าหลัก', URI_PATH.'/', 0];
    $_settings['title'] = 'Neko Viewer';
    $_settings['facebook'] = false;
    $_settings['twitter'] = false;
    $_settings['github'] = 'https://github.com/NeneNeko';
    $_settings['copy_right'] = '';
    }

echo '<h1 class="title"><a href="'.URI_PATH.'/" title="กลับหน้าแรก"><i class="fa fa-home"></i></a> : เกิดข้อผิดพลาด</h1>', EOL;
echo '<div class="contents">', EOL;
echo '<div class="index">', EOL;
echo '<h2 class="center"><i class="fa fa-exclamation-triangle"></i> '.$_error_message.'</h2>', EOL;
echo '</div>', EOL;
echo '<div class="clear"></div>', EOL;
echo '</div>', EOL;

require_once 'Viewer/Theme.php';
exit();

?>