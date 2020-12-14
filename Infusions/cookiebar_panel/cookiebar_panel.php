<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: cookiebar_panel.php
| Author: Core Development Team (coredevs@phpfusion.com)
| Co-Author: Joakim Falk (Domi)
| Version: 1.0.1
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
defined('IN_FUSION') || exit;

if (!isset($_COOKIE[COOKIE_PREFIX.'cookieconsent'])) {
    $settings = fusion_get_settings();
    $locale = fusion_get_locale("", COOKIE_LOCALE);

    add_to_head("<link rel='stylesheet' type='text/css' href='".INFUSIONS."cookiebar_panel/cookiebar_panel.css' />");

    add_to_footer("<script type='text/javascript' src='".INCLUDES."jquery/colorbox/jquery.colorbox.js'></script>");

    add_to_jquery("
    $('#consentcookies').bind('click', function(evt) {
        console.log('clicked');
        $.ajax({
            type:'POST',
            url:'".INFUSIONS."cookiebar_panel/consentcookies.php',
            data: $('#consentcookieform').serialize(),
            dataType:'html',
            success:function(data) {
                $('#cookiebar').slideUp();
            }
        });
        evt.preventDefault();
    });
    $('.cookieoverlay').colorbox({height:'100%',width:'100%',maxWidth:'800px',maxHeight:'700px',scrolling:true,overlayClose:false,transition:'elastic'});
    ");

    echo "<div id='cookiebar'>\n";
    echo "<div class='row'>\n";
    echo "<div class='col-xs-12 col-sm-8 col-md-10 m-t-15 m-b-15'>";
    echo $locale['CBP101']."<br/>\n".$locale['CBP103'];
    echo "<a class='cookieoverlay' href='".INFUSIONS."cookiebar_panel/cookiesinfo.php'>".$locale['CBP102']."</a>\n";
    echo "</div>\n";
    echo "<div class='col-xs-12 col-sm-4 col-md-2 m-t-10 m-b-10'>\n";
    echo openform('consentcookieform', 'post', FUSION_REQUEST, ['remote_url' => fusion_get_settings('site_path').'infusions/cookiebar_panel/consentcookies.php', 'class' => 'pull-right m-l-15']);
    echo form_button('consentcookies', $locale['CBP100'], 'consentcookies', ['class' => 'btn-primary', 'icon' => 'fa fa-check-circle']);
    echo closeform();
    echo "</div>\n";
    echo "</div>\n";
    echo "</div>\n";
}
