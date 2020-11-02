<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: blog.php
| Author: Core Development Team
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

use PHPFusion\Panels;

function render_main_blog($info) {
    Panels::getInstance(TRUE)->hide_panel('RIGHT');
    Panels::getInstance(TRUE)->hide_panel('LEFT');
    Panels::getInstance(TRUE)->hide_panel('AU_CENTER');
    Panels::getInstance(TRUE)->hide_panel('U_CENTER');
    Panels::getInstance(TRUE)->hide_panel('L_CENTER');
    Panels::getInstance(TRUE)->hide_panel('BL_CENTER');

    add_to_head("<link rel='stylesheet' href='".THEME."custom_css/blog.css?v=".filemtime(THEME.'custom_css/blog.css')."'>");

    echo render_breadcrumbs();

    if (isset($_GET['readmore']) && !empty($info['blog_item'])) {
        display_blog_item($info);
    } else {
        display_blog_index($info);
    }
}

function display_blog_index($info) {
    $locale = fusion_get_locale();

    if (!empty($info['blog_item'])) {
        foreach ($info['blog_item'] as $blog_id => $data) {
            opentable();
                echo '<div class="well text-left">';
                    echo '<article class="archive-post"><div class="wrap-post">';
                        echo '<div class="entry-header">';
                            echo '<div class="entry-thumb"><div class="blog-box zoom-effect">';
                                if (!empty($data['blog_image_path']) || !empty($data['blog_cat_image'])) {
                                    if ($data['blog_image_path'] && file_exists($data['blog_image_path'])) {
                                        $image = $data['blog_image_path'];
                                    } else if ($data['blog_cat_image']) {
                                        $image = INFUSIONS."blog/blog_cats/".$data['blog_cat_image'];
                                    } else {
                                        $image = get_image('imagenotfound');
                                    }
                                } else {
                                    $image = get_image('imagenotfound');
                                }
                                echo '<a href="'.$data['blog_link'].'"><img class="img-responsive" src="'.$image.'" alt="'.$data['blog_subject'].'"></a>';
                            echo '</div></div>';
                        echo '</div>';

                        echo '<div class="entry-content clearfix">';
                            echo '<h2 class="entry-title text-center"><a href="'.$data['blog_link'].'">'.$data['blog_subject'].'</a></h2>';
                            echo '<div class="entry-meta text-center">';
                                echo '<span class="post-date"><i class="fa fa-calendar"></i> '.timer($data['blog_datestamp']).'</span>';

                                if (fusion_get_settings('comments_enabled') && $data['blog_allow_comments']) {
                                    echo '<a class="post-comment" href="'.INFUSIONS.'blog/blog.php?readmore='.$blog_id.'#comments"><i class="fa fa-comments"></i> '.$data['blog_comments'].'</a>';
                                }

                            echo '</div>';
                            echo "<p class='p-15'>";
                            echo $data['blog_blog'];
                            echo "</p>";
                            echo '<span class="tags-links">';
                                $cats = explode(', ', $data['blog_category_link']);
                                foreach ($cats as $cat) {
                                    echo str_replace('<a href=', '<a href=', $cat);
                                }
                            echo '</span>';

                            echo '<a class="btn btn-md btn-primary pull-right" href="'.$data['blog_link'].'">Read More</a>';
                        echo '</div>';
                    echo '</div></article>';
                echo '</div>';
            closetable();
        }

        echo !empty($info['blog_nav']) ? '<div class="text-center m-t-10">'.$info['blog_nav'].'</div>' : '';
    } else {
        echo '<div class="text-center">'.$locale['blog_3000'].'</div>';
    }
}

function display_blog_item($info) {
    $data = $info['blog_item'];

        echo '<article class="single-post"><div class="wrap-post">';

            echo '<div class="entry-header text-center">';
            opentable();
                echo '<div class="well text-left">';
                    echo '<h1 class="entry-title">'.$data['blog_subject'].'</h1>';
                    echo '<span class="entry-meta">';
                        echo '<ul class="list-inline link">';
                            echo '<li>By '.$data['blog_post_author'].'</li>';
                            echo '<li>'.$data['blog_post_time'].'</li>';
                        echo '</ul>';
                    echo '</span>';
                echo '</div>';

                echo '<div class="post-item text-left">';
                    if ($data['blog_image']) {
                        echo '<div class="post-thumbnail-wrap"><div class="blog-box">';
                            echo '<img class="img-responsive" src="'.$data['blog_image_link'].'" alt="'.$data['blog_subject'].'">';
                        echo '</div></div>';
                    }

                    echo '<span class="tags-links p-15">';
                        $cats = explode(', ', $data['blog_category_link']);
                        foreach ($cats as $cat) {
                            echo str_replace('<a href=', '<a href=', $cat);
                        }

                    echo '</span>';
                    echo '<div class="p-15">';
                        echo $data['blog_blog'];
                        echo '<br />';
                        echo $data['blog_extended'];
                    echo '</div>';
                    echo $data['blog_nav'] ? '<div class="clearfix m-b-20">'.$data['blog_nav'].'</div>' : '';
                echo '</div>';

                echo '<div class="pull-right clearfix p-15">';
                    $url = (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                    echo '<div class="m-r-5 display-inline">';
                        echo social_media_links($url);
                    echo '</div>';
                echo '</div>';

            closetable();

            if ($data['blog_allow_comments'] && fusion_get_settings('comments_enabled') == 1) {
                opentable();
                    echo '<div class="well text-left">';
                        echo '<div id="comment">';
                            echo $data['blog_show_comments'];
                        echo '</div>';
                    echo '</div>';
                closetable();
            }

            if ($data['blog_allow_ratings'] && fusion_get_settings('ratings_enabled') == 1) {
                opentable();
                    echo '<div class="well">';
                        echo $data['blog_show_ratings'];
                    echo '</div>';
                closetable();
            }
        echo '</div>';

    echo '</div></article>';
}
