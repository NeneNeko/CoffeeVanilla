<?php

$contents_data = ob_get_contents();
ob_end_clean();

echo '<!DOCTYPE html>', EOL;
echo '<html lang="th-TH">', EOL;
echo '<head>', EOL;
echo '<meta http-equiv="content-type" content="text/html; charset=UTF-8">', EOL;
//echo '<meta charset="utf-8">', EOL;
echo '<title>'.$_settings['title'].' : '.$_title.'</title>', EOL;
echo '<meta name="author" content="Neko">', EOL;
echo '<meta content="width=320,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">', EOL;
echo '<link rel="icon" href="'.URI_PATH.'/Assets/common/img/favicon.png">', EOL;
$scripts = EOL.'jQuery(function($){'.EOL;
foreach ($_assets as $asset)
{
    $module = $_assetmodule[$asset];
    if (isset($module['css']))
        foreach ($module['css'] as $css)
            echo '<link rel="stylesheet" href="'.URI_PATH.'/Assets/'.$module['path'].'/'.$css.'" type="text/css" media="screen" />', EOL;
    if (isset($module['js']))
        foreach ($module['js'] as $js)
            echo '<script type="text/javascript" src="'.URI_PATH.'/Assets/'.$module['path'].'/'.$js.'"></script>', EOL;
    if (isset($module['script']))
        $scripts .= $module['script'];
}
$scripts .= EOL.'});'.EOL;
echo '<script type="text/javascript">'.$scripts.'</script>', EOL;
echo '</head>', EOL;
echo '<body>', EOL;
echo '<header class="header">', EOL;
echo '<div class="navigation container_12">', EOL;
echo '<div class="row">', EOL;
echo '<section class="grid_2 logo"> <a href="'.URI_PATH.'/">'.$_settings['title'].'</a>', EOL;
echo '</section>', EOL;
echo '<div class="grid_8">', EOL;
echo '<nav role="navigation">', EOL;
echo '<ul role="main-navigation">', EOL;
foreach (increaseIndex($_mainmenu) as $id=>$menu)
    {
    $current = ($_mainactive == $id) ? ' class="current"' : '';
    echo '<li><a href="'.$menu[1].'"'.$current.'>'.$menu[0].'</a></li>', EOL;
    }
echo '</ul>', EOL;
echo '</nav>', EOL;
echo '<div class="search"></div>', EOL;
echo '</div>', EOL;
echo '<div class="social grid_2"> ', EOL;
echo '<span> ', EOL;
if($_settings['facebook'])
    echo '<a href="'.$_settings['facebook'].'" target="_blank"><i class="fa fa-facebook"></i></a>', EOL;
if($_settings['twitter'])
    echo '<a href="'.$_settings['twitter'].'" target="_blank"><i class="fa fa-twitter"></i></a>', EOL;
if($_settings['github'])
    echo '<a href="'.$_settings['github'].'" target="_blank"><i class="fa fa-github"></i></a>', EOL;
 if(!$_fatalerror)
    echo '<a href="'.URI_PATH.'/feed" target="_blank"><i class="fa fa-rss"></i></a>', EOL;
echo '</span>', EOL;
echo '</div>', EOL;
echo '</div>', EOL;
echo '</div>', EOL;
echo '</header>', EOL;
echo '<div class="banner"></div>', EOL;
echo '<div id="status" class="status">&nbsp;</div>', EOL;
echo '<section class="main container_12">', EOL;
echo '<div class="row">', EOL;
echo '<div class="area-header"></div>', EOL;
echo '<div class="block-contents">', EOL;
echo '<div class="bg-contents"></div>', EOL;
echo $contents_data;
echo '</div>', EOL;
echo '</div>', EOL;
echo '</section>', EOL;
echo '<footer class="footer" role="contentinfo">', EOL;
echo '<div class="footer-content container_12">', EOL;
echo '<div class="row">', EOL;
echo '<div class="grid_5 left">', EOL;
echo '<span></span>', EOL;
echo '</div>', EOL;
echo '<div class="copyright grid_5 right">', EOL;
echo '<span>'.$_settings['copy_right'].'</span>', EOL;
echo '</div>', EOL;
echo '</div>', EOL;
echo '</div>', EOL;
echo '</footer>', EOL;
echo '</body>', EOL;
echo '</html>', EOL;
 if(!$_fatalerror)
 {
    echo '<!--', EOL;
    echo 'Memory use '.memory_get_usage(), EOL;
    echo 'Query count '.$_database->query("show session status like 'questions'")->fetchColumn(1), EOL;
    echo '-->', EOL;
 }

?>