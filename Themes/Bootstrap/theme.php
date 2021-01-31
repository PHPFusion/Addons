<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: theme.php
| Author: Frederick MC Chan (Chan)
| Version: 1.4.1
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

define("THEME_BULLET", "&middot;");

define('BOOTSTRAP', TRUE);
define('FONTAWESOME', TRUE);

function render_page() {
    $locale = fusion_get_locale();
    $userdata = fusion_get_userdata();
    $aidlink = fusion_get_aidlink();
    $settings = fusion_get_settings();

    // set variables
    $brand = "<a href='".BASEDIR.fusion_get_settings('opening_page')."'>\n";
    $brand .= $settings['sitebanner'] ? "<img title='".$settings['sitename']."' style='margin-left:-20px; width:100%; margin-top:-35px;' src='".BASEDIR.$settings['sitebanner']."' />" : $settings['sitename'];
    $brand .= "</a>\n";

    // set size - max of 12 min of 0
    $side_grid_settings = [
        'desktop_size' => 2,
        'laptop_size'  => 3,
        'tablet_size'  => 3,
        'phone_size'   => 12,
    ];

    // Render Theme
    echo "<div class='container p-t-20 p-b-20'>\n";
    ?>
    <div class="row">
        <div class="col-xs-12 col-sm-4">
            <?php
            echo "<div class='display-inline-block m-t-20 m-l-20' style='max-width: 280px;'>";
            echo $brand;
            echo "</div>\n";
            ?>
        </div>
        <div class="col-xs-12 col-sm-8 text-right">
            <?php
            echo "<div class='display-inline-block pull-right m-l-10' style='width:30%;'>\n";
            echo openform('searchform', 'post', BASEDIR.'search.php?stype=all',
                [
                    'class'      => 'm-b-10',
                    'remote_url' => fusion_get_settings('site_path')."search.php"
                ]
            );
            echo form_text('stext', '', '', [
                'placeholder'        => $locale['search'],
                'append_button'      => TRUE,
                'append_type'        => "submit",
                "append_form_value"  => 'search',
                "append_value"       => "<i class='fa fa-search'></i> ".$locale['search'],
                "append_button_name" => "search",
                'class'              => 'no-border m-b-0',
            ]);
            echo closeform();
            echo "</div>\n";
            echo "<ul class='display-inline-block m-t-10'>\n";
            $language_opts = '';
            if (count(fusion_get_enabled_languages()) > 1) {
                $language_opts = "<li class='dropdown display-inline-block p-r-5'>\n";
                $language_opts .= "<a id='ddlangs' class='dropdown-toggle pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='".fusion_get_locale('UM101')."'><i class='fa fa-globe fa-lg'></i> ".translate_lang_names(LANGUAGE)." <span class='caret'></span></a>\n";
                $language_opts .= "<ul class='dropdown-menu' aria-labelledby='ddlangs' role='menu'>\n";
                $language_switch = fusion_get_language_switch();
                if (!empty($language_switch)) {
                    foreach ($language_switch as $folder => $langData) {
                        $language_opts .= "<li class='text-left'><a href='".$langData['language_link']."'>\n";
                        $language_opts .= "<img alt='".$langData['language_name']."' class='m-r-5' src='".$langData['language_icon_s']."'/>\n";
                        $language_opts .= $langData['language_name'];
                        $language_opts .= "</a></li>\n";
                    }
                }
                $language_opts .= "</ul>\n";
                $language_opts .= "</li>\n";
            }
            if (!iMEMBER) {
                echo "<li class='display-inline-block p-l-5 p-r-5'><a href='".BASEDIR."login.php'>".$locale['login']."</a></li>\n";
                if (fusion_get_settings("enable_registration")) {
                    echo "<li class='display-inline-block p-l-5 p-r-5'><a href='".BASEDIR."register.php'>".$locale['register']."</a></li>\n";
                }
                echo $language_opts;
            } else {
                echo iADMIN ? "<li class='display-inline-block p-l-5 p-r-5'><a href='".ADMIN.$aidlink."&pagenum=0'>".$locale['global_123']."</a></li>" : '';
                echo "<li class='display-inline-block p-l-5 p-r-5'>\n<a href='".BASEDIR."profile.php?lookup=".$userdata['user_id']."'>".$locale['profile']."</a>\n</li>\n";
                echo $language_opts;
                echo session_get('login_as') ? '<li><a href="'.BASEDIR.'index.php?logoff='.$userdata['user_id'].'">'.$locale['UM103'].'</a></li>' : '';
                echo "<li class='display-inline-block p-l-5 p-r-5'>\n<a href='".BASEDIR."index.php?logout=yes'>".$locale['logout']."</a></li>\n";
            }

            echo "</ul>\n";
            ?>
        </div>
    </div>
    <?php

    echo showsublinks('', 'navbar-default', ['logo' => $brand, 'show_header' => TRUE])."\n";
    echo showbanners(1);
    // row 1 - go for max width
    if (defined('AU_CENTER') && AU_CENTER) {
        echo "<div class='row'>\n<div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>".AU_CENTER."</div>\n</div>";
    }
    // row 2 - fluid setitngs depending on panel appearances
    echo "<div class='row main-body'>\n";
    if (defined('LEFT') && LEFT) {
        echo "<div class='".html_prefix($side_grid_settings)." hidden-xs'>\n".LEFT."</div>\n";
    } // column left
    echo "<div class='".html_prefix(center_grid_settings($side_grid_settings))."'>\n";
    echo renderNotices(getNotices(['all', FUSION_SELF]));
    echo defined("U_CENTER") && U_CENTER ? U_CENTER : '';
    echo CONTENT; // column center
    echo defined("L_CENTER") && L_CENTER ? L_CENTER : '';
    echo "</div>\n";
    if (defined('RIGHT') && RIGHT) {
        echo "<div class='".html_prefix($side_grid_settings)."'>\n".RIGHT."</div>\n";
    } // column right
    if (defined('LEFT') && LEFT) {
        echo "<div class='".html_prefix($side_grid_settings)." hidden-sm hidden-md hidden-lg'>\n".LEFT."</div>\n";
    } // column left
    echo "</div>\n";
    // row 3
    if (defined('BL_CENTER') && BL_CENTER) {
        echo "<div class='row'>\n<div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>".BL_CENTER."</div>\n</div>";
    }

    echo "<div class='row'>\n";
    echo "<div class='col-xs-12 col-sm-3 col-md-3 col-lg-3'>\n";
    echo defined('USER1') && USER1 ? USER1 : '';
    echo "</div>\n";

    echo "<div class='col-xs-12 col-sm-3 col-md-3 col-lg-3'>\n";
    echo defined('USER2') && USER2 ? USER2 : '';
    echo "</div>\n";

    echo "<div class='col-xs-12 col-sm-3 col-md-3 col-lg-3'>\n";
    echo defined('USER3') && USER3 ? USER3 : '';
    echo "</div>\n";

    echo "<div class='col-xs-12 col-sm-3 col-md-3 col-lg-3'>\n";
    echo defined('USER4') && USER4 ? USER4 : '';
    echo "</div>\n";
    echo "</div>\n";

    // footer
    echo "<hr>\n";
    echo showbanners(2);
    echo "<div class='row'>\n<div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>";
    echo "<span>".nl2br(parse_textarea($settings['footer'], FALSE, TRUE))."</span><br/>\n";
    echo "<span>".showcopyright().showprivacypolicy()."</span><br/>\n";
    echo "<span>Theme by <a href='http://www.phpfusion.com' target='_blank'>PHP Fusion Inc</a></span><br/>\n";
    echo "<span>";
    echo showcounter();
    if ($settings['rendertime_enabled'] == '1' || $settings['rendertime_enabled'] == '2') {
        if ($settings['visitorcounter_enabled']) {
            echo " | ";
        }
        echo showrendertime();
        echo showMemoryUsage();
    }
    echo showFooterErrors();
    echo "</span>\n";
    echo "</div>\n</div>\n";
    echo "</div>\n";
}

function openside($title) {
    echo "<h4>$title</h4>\n";
    echo "<div class='list-group-item'>\n";
}

function closeside() {
    echo "</div>\n";
}

function opentable($title) {
    echo "<h3>$title</h3>\n";
}

function closetable() {
    echo " ";
}

function html_prefix(array $array) {
    $array['phone_size'] = ($array['phone_size'] == 0) ? 'hidden-xs' : 'col-xs-'.$array['phone_size'];
    $array['tablet_size'] = ($array['tablet_size'] == 0) ? 'hidden-sm' : 'col-sm-'.$array['tablet_size'];
    $array['laptop_size'] = ($array['laptop_size'] == 0) ? 'hidden-md' : 'col-md-'.$array['laptop_size'];
    $array['desktop_size'] = ($array['desktop_size'] == 0) ? 'hidden-lg' : 'col-lg-'.$array['desktop_size'];

    return "".$array['phone_size']." ".$array['tablet_size']." ".$array['laptop_size']." ".$array['desktop_size']."";
}

function total_side_span($value) {
    $count = 0;
    if (defined('LEFT') && LEFT) {
        $count = $count + $value;
    }
    if (defined('RIGHT') && RIGHT) {
        $count = $count + $value;
    }
    if ($count > 12) {
        $count = 12;
    }

    return $count;
}

// Step 2 - get the balance out of max 12 for center settings after deduction of total side_length
function center_grid_settings($side_grid_settings) {
    return [
        'desktop_size' => (12 - total_side_span($side_grid_settings['desktop_size'])) > 0 ? 12 - total_side_span($side_grid_settings['desktop_size']) : 12,
        'laptop_size'  => (12 - total_side_span($side_grid_settings['laptop_size'])) > 0 ? 12 - total_side_span($side_grid_settings['laptop_size']) : 12,
        'tablet_size'  => (12 - total_side_span($side_grid_settings['tablet_size'])) > 0 ? 12 - total_side_span($side_grid_settings['tablet_size']) : 12,
        'phone_size'   => (12 - total_side_span($side_grid_settings['phone_size'])) > 0 ? 12 - total_side_span($side_grid_settings['phone_size']) : 12,
    ];
}
