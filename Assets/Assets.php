<?php

$_assetmodule = array();

########### Jquery ############
$_module = array();
$_module['path'] = 'jquery';
$_module['js'][] = 'jquery-2.2.1.js';
$_assetmodule['jquery'] = $_module;

########### Font-Awesome ############
$_module = array();
$_module['path'] = 'font-awesome';
$_module['css'][] = 'css/font-awesome.css';
$_assetmodule['font-awesome'] = $_module;

########### tapmodo Jcrop ############
$_module = array();
$_module['path'] = 'jcrop';
$_module['css'][] = 'css/jquery.jcrop.css';
$_module['js'][] = 'js/jquery.jcrop.js';
$_module['js'][] = 'js/jquery.color.js';
$_module['script'] = 
    '
    $("#crop").Jcrop({
        setSelect: [0, 3000, 3000, 0],
        onChange: function (c){
            $("#status").html("X:"+c.x+", Y:"+c.y+" X2:"+c.x2+" Y2:"+c.y2+", W:"+c.w+", H:"+c.h);
            $("#status").delay(500).fadeIn( "slow" );
        },
        onSelect: function (c){
            $("#x").val(c.x);
            $("#y").val(c.y);
            $("#w").val(c.w);
            $("#h").val(c.h);
        }
    });
    ';
$_assetmodule['jcrop'] = $_module;

########### tablEdit ############
$_module = array();
$_module['path'] = 'tabledit';
$_module['js'][] = 'jquery.tabledit.js';
$_module['script'] = 
    '
    $("#chapter").Tabledit({
        url: "/api",
        editButton: false,
        deleteButton: false,
        hideIdentifier: true,
        columns: {
            identifier: [0, "ch_id"],
            editable: [[1, "ch_number"], [3, "ch_title"],[4, "ch_uri"], [5, "ch_date"]]
        },
        onAjax: function(action, serialize) {
            $("#status").html("กำลังส่งข้อมูล...");
            $("#status").delay(500).fadeIn( "slow" );
        },
        onSuccess: function(data, textStatus, jqXHR) {
            $("#status").html(data);
            $("#status").delay(2000).fadeOut( "slow" );
        },
        onFail: function(jqXHR, textStatus, errorThrown) {
            $("#status").html("ไม่สามารถส่งข้อมูลได้");
            $("#status").delay(2000).fadeOut( "slow" );
        }
    });
    ';
$_assetmodule['tabledit'] = $_module;

########### Pace ############
$_module = array();
$_module['path'] = 'pace';
$_module['css'][] = 'pace-theme-flash.css';
$_module['js'][] = 'pace.js';
$_assetmodule['pace'] = $_module;

########### prettyPhoto ############
$_module = array();
$_module['path'] = 'prettyPhoto';
$_module['css'][] = 'css/prettyPhoto.css';
$_module['js'][] = 'js/jquery.prettyPhoto.js';
$_module['script'] = 
    '
    $("a[rel^=\"prettyPhoto\"]").prettyPhoto({
        animation_speed: "fast",
        slideshow: 5000,
        autoplay_slideshow: false,
        opacity: 0.80, 
        show_title: true,
        allow_resize: false,
        overlay_gallery: false, 
        counter_separator_label: "/",
        theme: "pp_default",
        horizontal_padding: 20,
        social_tools: ""
    });
    ';
$_assetmodule['prettyPhoto'] = $_module;

########### bpgImage ############
$_module = array();
$_module['path'] = 'bpgImage';
$_module['js'][] = 'bpgdec8a.js';
$_assetmodule['bpg'] = $_module;

########### flifImage ############
$_module = array();
$_module['path'] = 'flifImage';
$_module['js'][] = 'flif.js';
$_assetmodule['flif'] = $_module;

########### Common Asset and Script ############
$_module = array();
$_module['path'] = 'common';
$_module['css'][] = 'css/front.css';
$_module['script'] = 
    '
    $(".delete").click(function(event){
         if(!confirm("คุณแน่ใจว่าจะลบ?")){
             event.preventDefault();
         }
    });
    ';
$_assetmodule['common'] = $_module;


###########   ############
$_module = array();
$_module['path'] = 'colortip';
$_module['css'][] = 'colortip.css';
$_module['js'][] = 'colortip.js';
$_module['script'] = 
    '
    $("[title]").colorTip({color:"blue"});
    ';
$_assetmodule['colortip'] = $_module;


###########   ############
$_module = array();
$_module['path'] = 'lazyload';
$_module['js'][] = 'jquery.lazyload.js';
$_module['script'] = 
    '
    $("img.lazy").lazyload({
         placeholder: "/Assets/common/img/preloader.gif",
         effect : "fadeIn",
    });
    ';
$_assetmodule['lazyload'] = $_module;


/*
###########   ############
$_module = array();
$_module['path'] = '';
$_module['css'][] = '';
$_module['js'][] = '';
$_module['script'] = 
    '
    ';
$_assetmodule[''] = $_module;
*/
?>