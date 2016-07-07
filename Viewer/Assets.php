<?php

$_assetmodule = array();

########### Jquery ############
    $_module = array();
    $_module['path'] = 'jquery-2.2.3';
    $_module['js'][] = 'jquery.min.js';
    $_assetmodule['jquery'] = $_module;

########### Font-Awesome ############
    $_module = array();
    $_module['path'] = 'font-awesome-4.6.3';
    $_module['css'][] = 'css/font-awesome.min.css';
    $_assetmodule['font-awesome'] = $_module;

########### tapmodo Jcrop ############
    $_module = array();
    $_module['path'] = 'jcrop-0.9.12';
    $_module['css'][] = 'css/jquery.jcrop.min.css';
    $_module['js'][] = 'js/jquery.jcrop.min.js';
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
    $_module['path'] = 'tabledit-1.2.3';
    $_module['js'][] = 'jquery.tabledit.min.js';
    $_module['script'] = 
        '
        $("#chapter").Tabledit({
            url: "'.URI_PATH.'/api",
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
    $_module['path'] = 'pace-1.0.0';
    $_module['css'][] = 'pace-theme-flash.css';
    $_module['js'][] = 'pace.min.js';
    $_assetmodule['pace'] = $_module;

########### prettyPhoto ############
    $_module = array();
    $_module['path'] = 'prettyPhoto-3.1.6';
    $_module['css'][] = 'css/prettyPhoto.min.css';
    $_module['js'][] = 'js/jquery.prettyPhoto.min.js';
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


########### colortip ############
    $_module = array();
    $_module['path'] = 'colortip-1.0';
    $_module['css'][] = 'jquery.colortip.css';
    $_module['js'][] = 'jquery.colortip.js';
    $_module['script'] = 
        '
        $("[title]").colorTip({color:"blue", timeout: 50});
        ';
    $_assetmodule['colortip'] = $_module;

########### lazyload ############
    $_module = array();
    $_module['path'] = 'lazyload-1.9.7';
    $_module['js'][] = 'jquery.lazyload.min.js';
    $_module['script'] = 
        '
        $("img.lazy").lazyload({
            placeholder: "'.URI_PATH.'/Assets/common/img/preloader.gif",
            effect : "fadeIn",
        });
        ';
    $_assetmodule['lazyload'] = $_module;

########### uploadPreview  ############
    $_module = array();
    $_module['script'] = 
        '
        function readURL(input) {
            var url = input.value;
            var ext = url.substring(url.lastIndexOf(".") + 1).toLowerCase();
            if (input.files && input.files[0]&& (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $("#preview").attr("src", e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }else{
                $("#preview").attr("src", "'.URI_PATH.'/image/2");
            }
        }
        $("#cover").change(function(){
            readURL(this);
        });
        ';
    $_assetmodule['uploadPreview'] = $_module;


########### Text font resize  ############
    $_module = array();
    $_module['script'] = 
        '
        var size = $("#article-contents").css("font-size"); 
        $("#largeFont").click(function(){ 
            $("#article-contents").css("font-size", "30px");
            return false; 
        });
        $("#resetFont").click(function(){ 
            $("#article-contents").css("font-size", size);
            return false; 
        });
        $("#increaseFont").click(function() { 
            var size = $("#article-contents").css("font-size");
            $("#article-contents").css("font-size", parseInt(size)+2); 
            return false;
        });
        $("#decreaseFont").click(function() { 
            var size = $("#article-contents").css("font-size");
            $("#article-contents").css("font-size", parseInt(size)-2); 
            return false;
        }); 
        $("#smallFont").click(function(){ 
            $("#article-contents").css("font-size", "10px");
            return false; 
        });
        ';
    $_assetmodule['fontresize'] = $_module;


########### template  ############
    /*
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