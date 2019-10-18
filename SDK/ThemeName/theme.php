<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: theme.php
| Author: Your Name
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

require_once INCLUDES.'theme_functions_include.php';

define('THEME_BULLET', '&middot;');
define('BOOTSTRAP', TRUE);
define('FONTAWESOME', TRUE);

function render_page() {
    $settings = fusion_get_settings();

    echo '<div class="container-fluid">';
        echo '<header>';
            $menu_config = [
                'container_fluid' => TRUE,
                'show_header'     => TRUE
            ];
            echo \PHPFusion\SiteLinks::setSubLinks($menu_config)->showSubLinks();
        echo '</header>';

        echo '<div class="notices">';
            echo renderNotices(getNotices(['all', FUSION_SELF]));
        echo '</div>';

        echo '<section class="main-content">';
            echo defined('AU_CENTER') && AU_CENTER ? AU_CENTER : '';
            echo showbanners(1);

            echo '<div class="row">';
            $content = ['sm' => 12, 'md' => 12, 'lg' => 12];
            $left    = ['sm' => 3,  'md' => 2,  'lg' => 2];
            $right   = ['sm' => 3,  'md' => 2,  'lg' => 2];

            if (defined('LEFT') && LEFT) {
                $content['sm'] = $content['sm'] - $left['sm'];
                $content['md'] = $content['md'] - $left['md'];
                $content['lg'] = $content['lg'] - $left['lg'];
            }

            if (defined('RIGHT') && RIGHT) {
                $content['sm'] = $content['sm'] - $right['sm'];
                $content['md'] = $content['md'] - $right['md'];
                $content['lg'] = $content['lg'] - $right['lg'];
            }

            if (defined('LEFT') && LEFT) {
                echo '<div class="col-xs-12 col-sm-'.$left['sm'].' col-md-'.$left['md'].' col-lg-'.$left['lg'].'">';
                    echo LEFT;
                echo '</div>';
            }

            echo '<div class="col-xs-12 col-sm-'.$content['sm'].' col-md-'.$content['md'].' col-lg-'.$content['lg'].'">';
                echo defined('U_CENTER') && U_CENTER ? U_CENTER : '';
                echo CONTENT;
                echo defined('L_CENTER') && L_CENTER ? L_CENTER : '';
                echo showbanners(2);
            echo '</div>';

            if (defined('RIGHT') && RIGHT) {
                echo '<div class="col-xs-12 col-sm-'.$right['sm'].' col-md-'.$right['md'].' col-lg-'.$right['lg'].'">';
                    echo RIGHT;
                echo '</div>';
            }

            echo '</div>';

            echo defined('BL_CENTER') && BL_CENTER ? BL_CENTER : '';
        echo '</section>';

        echo '<footer>';
            $theme_settings = get_theme_settings('ThemeName');

            if (!empty($theme_settings['facebook_url'])) {
                echo '<a href="'.$theme_settings['facebook_url'].'" target="_blank"><i class="fa fa-facebook"></i></a>';
            }

            echo '<div class="row m-t-10">';
                echo defined('USER1') && USER1 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER1.'</div>' : '';
                echo defined('USER2') && USER2 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER2.'</div>' : '';
                echo defined('USER3') && USER3 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER3.'</div>' : '';
                echo defined('USER4') && USER4 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER4.'</div>' : '';
            echo '</div>';

            echo showFooterErrors();
            echo showcopyright().showprivacypolicy();

            if ($settings['rendertime_enabled'] == 1 || $settings['rendertime_enabled'] == 2) {
                echo '<br/><small>'.showrendertime().showMemoryUsage().'</small>';
            }

            echo '<br/>'.showcounter();
            echo nl2br(parse_textarea($settings['footer'], FALSE, TRUE));
        echo '</footer>';
    echo '</div>';
}

function opentable($title = FALSE, $class = '') {
    echo '<div class="opentable">';
    echo $title ? '<div class="title">'.$title.'</div>' : '';
    echo '<div class="'.$class.'">';
}

function closetable() {
    echo '</div>';
    echo '</div>';
}

function openside($title = FALSE, $class = '') {
    echo '<div class="openside '.$class.'">';
    echo $title ? '<div class="title">'.$title.'</div>' : '';
}

function closeside() {
    echo '</div>';
}
