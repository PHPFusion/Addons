<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: downloads.php
| Author: PHP-Fusion Development Team
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

use \PHPFusion\Panels;

function render_downloads($info) {
    $locale = fusion_get_locale();

    echo render_breadcrumbs();

    Panels::addPanel('menu_panel', menu($info), Panels::PANEL_RIGHT, iGUEST, 1);

    if (isset($_GET['download_id']) && !empty($info['download_item'])) {
        opentable($info['download_title']);
        display_download_item($info);
        closetable();
    } else {
        opentable($locale['download_1000']);
        display_download_index($info);
        closetable();
    }

}

function display_download_index($info) {
    $locale = fusion_get_locale();
    $dl_settings = get_settings('downloads');

    if (!empty($info['download_cat_description'])) {
        echo '<div class="display-block">'.$info['download_cat_description'].'</div>';
    }

    if (!empty($info['download_item'])) {
        echo '<div class="row equal-height m-b-10">';
            foreach ($info['download_item'] as $download_id => $data) {
                echo '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">';
                    echo '<div class="well item">';
                        $link = DOWNLOADS.'downloads.php?cat_id='.$data['download_cat_id'].'&download_id='.$data['download_id'];

                        echo '<h4 class="m-t-5 text-center"><a href="'.$link.'">'.trimlink($data['download_title'], 40).'</a></h4>';

                        if ($dl_settings['download_screenshot'] == 1) {
                            echo '<div class="preview display-inline-block image-wrap thumb text-center overflow-hide m-2">';
                                if (!empty($data['download_thumb']) && file_exists($data['download_thumb'])) {
                                    echo "<div class='center-all' style='height: 180px;'>";
                                    echo '<a href="'.$link.'"><img style="width: 180px;" class="img-responsive" src="'.$data['download_thumb'].'" alt="'.$data['download_title'].'"/></a>';
                                    echo "</div>";
                                } else {
                                    echo '<a href="'.$link.'">'.get_image('imagenotfound', $data['download_title'], 'width: 140px;height: 140px;').'</a>';
                                }
                            echo '</div>';
                        }

                        echo '<div class="text-center"><time>'.$data['download_post_time'].'</time></div>';
                        echo '<a href="'.$link.'" class="btn btn-link btn-sm btn-block">'.$locale['download_1007'].'</a>';
                    echo '</div>';
                echo '</div>';
            }
        echo '</div>';

        echo !empty($info['download_nav']) ? '<div class="text-center m-b-20">'.$info['download_nav'].'</div>' : '';
    } else {
        echo '<div class="text-center">'.$locale['download_3000'].'</div>';
    }
}

function display_download_item($info) {
    $locale = fusion_get_locale();
    $dl_settings = get_settings('downloads');
    $data = $info['download_item'];

    echo '<h3 class="m-t-0 m-b-0">'.$data['download_title'].'</h3>';
    //echo $data['download_description_short'];
    echo '<hr/>';

    if ($data['admin_link']) {
        $admin_actions = $data['admin_link'];
        echo '<div class="btn-group m-b-20">';
        echo '<a class="btn btn-primary btn-sm" href="'.$admin_actions['edit'].'">'.$locale['edit'].'</a>';
        echo '<a class="btn btn-danger btn-sm" href="'.$admin_actions['delete'].'">'.$locale['delete'].'</a>';
        echo '</div>';
    }

    echo '<div class="row m-b-20">';
        if ($dl_settings['download_screenshot'] == 1) {
            echo '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">';
                echo '<div class="text-center">';
                    if ($data['download_image'] && file_exists(DOWNLOADS.'images/'.$data['download_image'])) {
                        echo thumbnail(DOWNLOADS.'images/'.$data['download_image'], '200px');
                    } else {
                        echo get_image('imagenotfound', $data['download_title'], 'width: 200px;height: 200px;');
                    }
                echo '</div>';
            echo '</div>';

            $grid = 9;
        } else {
            $grid = 12;
        }

        echo '<div class="col-xs-12 col-sm-'.$grid.' col-md-'.$grid.' col-lg-'.$grid.'">';
            $profile = profile_link($data['user_id'], $data['user_name'], $data['user_status']);
            echo '<strong>'.$locale['global_050'].'</strong>: '.$profile.'<br />';
            echo '<strong>'.$locale['download_1017'].'</strong>: '.$data['download_homepage'].'<br/>';
            echo '<strong>Category: </strong> ';
            $link = DOWNLOADS.'downloads.php?cat_id='.$data['download_cat_id'];
            echo '<a href="'.$link.'">'.$data['download_cat_name'].'</a>';
        echo '</div>';
        echo '<div class="col-xs-12 col-sm-3 col-md-9 col-lg-12 center-all">';
            echo '<a href="'.$data['download_file_link'].'" target="blank" class="btn btn-success btn-md">'.$locale['drk_002'].' <i class="fa fa-download"></i> '.setLocale('09','DOWNLOADS_LOCALE').($data['download_filesize'] ? ' ('.$data['download_filesize'].')' : '').'</a>';
        echo '</div>';

    echo '</div>';

    echo '<div class="row">';
    echo '<div class="col-xs-12 col-sm-9">';

    if ($data['download_description']) {
        echo '<div class="p-15 m-b-20" style="border: 1px solid #000;">';
            echo $data['download_description'];
        echo '</div>';
    }

    if ($dl_settings['download_screenshot'] == 1 && $data['download_image'] && file_exists(DOWNLOADS.'images/'.$data['download_image'])) {
        echo '<div class="center-all p-10 m-b-20" style="border: 1px solid #000;">';
            $link = DOWNLOADS.'images/'.$data['download_image'];
            echo '<img src="'.$link.'" alt="'.$data['download_title'].'" class="img-responsive"/>';
        echo '</div>';
    }

    echo '</div>';

    echo '<div class="col-xs-12 col-sm-3">';
        echo '<div class="m-b-10">';
            echo '<span class="strong text-lighter">'.$locale['download_1011'].'</span><br>';
            echo $data['download_version'];
        echo '</div>';

        echo '<div class="m-b-10">';
            echo '<span class="strong text-lighter">'.$locale['download_1012'].'</span><br>';
            echo $data['download_count'];
        echo '</div>';

        echo '<div class="m-b-10">';
            echo '<span class="strong text-lighter">'.$locale['download_1021'].'</span><br>';
            echo $data['download_post_time'];
        echo '</div>';
        echo '<div class="m-b-10">';
            echo '<span class="strong text-lighter">'.$locale['download_1013'].'</span><br>';
            echo $data['download_license'];
        echo '</div>';

        echo '<div class="m-b-10">';
            echo '<span class="strong text-lighter">'.$locale['download_1014'].'</span><br>';
            echo $data['download_os'];
        echo '</div>';
        echo '<div>';
            echo '<span class="strong text-lighter">'.$locale['download_1015'].'</span><br>';
            echo $data['download_copyright'];
        echo '</div>';
    echo '</div>';

    echo '</div>'; // .row

    if ($data['download_allow_comments'] && fusion_get_settings('comments_enabled') == 1) {
        echo "<div class='m-t-20'></div>";
        opentable();
            echo '<div class="well text-left">';
                echo '<div id="comment">';
                    echo $data['download_show_comments'];
                echo '</div>';
            echo '</div>';
        closetable();
    }

    if ($data['download_allow_ratings'] && fusion_get_settings('ratings_enabled') == 1) {
        opentable();
            echo '<div class="well">';
                echo '<div class="ratings-box">';
                    echo $data['download_show_ratings'];
                echo '</div>';
            echo '</div>';
        closetable();
    }
}

function displayCatMenu($info, $cat_id = 0, $level = 0) {
    $html = '';

    if (!empty($info[$cat_id])) {
        foreach ($info[$cat_id] as $download_cat_id => $cdata) {
            $active = (!empty($_GET['cat_id']) && $_GET['cat_id'] == $download_cat_id) ? ' active' : '';
            $link = DOWNLOADS.'downloads.php?cat_id='.$download_cat_id;
            $html .= str_repeat('&nbsp;', $level);
            $html .= '<a href="'.$link.'" class="list-group-item p-5 p-l-15'.$active.'">'.$cdata['download_cat_name'].'</a>';

            if (!empty($info[$download_cat_id])) {
                $html .= displayCatMenu($info, $download_cat_id, $level + 1);
            }
        }
    }

    return $html;
}

function menu($info) {
    $locale = fusion_get_locale();

    ob_start();
    openside();
    echo '<ul class="list-style-none m-t-5 m-b-10">';
    echo '<li><a title="'.$locale['download_1001'].'" href="'.DOWNLOADS.'downloads.php"><span>'.$locale['download_1001'].'</span></a></li>';

    $filter_ = $info['download_filter'];

    foreach ($filter_ as $filter_key => $filter) {
        $active = isset($_GET['type']) && $_GET['type'] === $filter_key ? ' class="active strong"' : '';
        echo '<li'.$active.'><a href="'.$filter['link'].'">'.$filter['title'].'</a></li>';
    }
    echo '</ul>';
    closeside();
    openside('<i class="fa fa-list"></i> '.$locale['download_1003']);
    echo '<div class="list-group">';
        $download_cat_menu = displayCatMenu($info['download_categories']);
        echo !empty($download_cat_menu) ? $download_cat_menu : '<p>'.$locale['download_3001'].'</p>';
    echo '</div>';
    closeside();

    openside('<i class="fa fa-users"></i> '.$locale['download_1004']);
    echo '<ul class="list-style-none">';
        if (!empty($info['download_author'])) {
            foreach ($info['download_author'] as $author_id => $author_info) {
                echo '<li'.($author_info['active'] ? ' class="active strong"' : '').'>';
                    echo '<a href="'.$author_info['link'].'">'.$author_info['title'].'</a> ';
                    echo '<span class="badge m-l-10">'.$author_info['count'].'</span>';
                echo '</li>';
            }
        } else {
            echo '<li>'.$locale['download_3002'].'</li>';
        }
    echo '</ul>';
    closeside();

    $html = ob_get_contents();
    ob_end_clean();

    return $html;
}
