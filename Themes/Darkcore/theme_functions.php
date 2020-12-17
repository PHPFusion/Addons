<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: theme_functions.php
| Author: PHPFusion Development Team
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
defined('IN_FUSION') || exit; // Prevent direct access to includes.

// Customized Register / Login menu, use it as default on concept pages where panels is disabled, it is called in the header
function user_menu() {
    $locale = fusion_get_locale();
    $settings = fusion_get_settings();
    $userdata = fusion_get_userdata();
    $languages = fusion_get_enabled_languages();

    if (iMEMBER) {
        $inbox_count = dbcount("(message_id)", DB_MESSAGES, "message_to='".$userdata['user_id']."' AND message_read='0'");
        $new_messages = $inbox_count > 0 ? '<span class="pm_label pm_label-danger">'.$inbox_count.'</span> ' : '';
        $name = "<div class='pull-left m-r-10' style='height:30px;'>$new_messages ".display_avatar( $userdata, '30px', FALSE, FALSE,'img-rounded')."</div>";
    } else {
        $name = $locale['login'].($settings['enable_registration'] ? '/'.$locale['register'] : '');
    }

    ob_start();
    echo '<ul class="nav navbar-nav navbar-right secondary m-r-0">';
        if (count($languages) > 1) {
            echo '<li class="nav-item dropdown">';
                echo '<a id="ddlangs" href="#" class="dropdown-toggle pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="'.LANGUAGE.'">';
                    echo '<i class="fa fa-globe"></i> ';
                    echo translate_lang_names(LANGUAGE);
                    echo '<span class="caret"></span>';
                echo '</a>';

                echo '<ul class="dropdown-menu" aria-labelledby="ddlangs">';
                    foreach ($languages as $language_folder => $language_name) {
                        echo '<li class="dropdown-item"><a class="display-block" href="'.clean_request('lang='.$language_folder, ['lang'], FALSE).'">';
                            echo '<img class="m-r-5" src="'.BASEDIR.'locale/'.$language_folder.'/'.$language_folder.'-s.png" alt="'.$language_folder.'"/> ';
                            echo $language_name;
                        echo '</a></li>';
                    }
                echo '</ul>';
            echo '</li>';
        }

        echo '<li id="user-info" class="dropdown">';
            echo '<a href="#" id="user-menu" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$name.' <span class="caret"></span></a>';

            if (iMEMBER) {

                echo '<ul class="dropdown-menu" aria-labelledby="user-menu" style="min-width: 180px;">';
                    echo '<li class="dropdown-item"><a href="'.BASEDIR.'profile.php?lookup='.$userdata['user_id'].'"><i class="m-r-5 fa fa-fw fa-user-circle-o"></i>'.$locale['view'].' '.$locale['profile'].'</a></li>';
                    echo '<li class="dropdown-item"><a href="'.BASEDIR.'messages.php"><i class="m-r-5 fa fa-fw fa-envelope-o"></i> '.$locale['message'].'</a></li>';
                    echo '<li class="dropdown-item"><a href="'.BASEDIR.'edit_profile.php"><i class="m-r-5 fa fa-fw fa-pencil"></i> '.$locale['UM080'].'</a></li>';
                    echo iADMIN ? '<li class="dropdown-item"><a href="'.ADMIN.'index.php'.fusion_get_aidlink().'&pagenum=0"><i class="m-r-5 fa fa-fw fa-dashboard"></i> '.$locale['global_123'].'</a></li>' : '';
                    echo '<li class="dropdown-item"><a href="'.BASEDIR.'index.php?logout=yes"><i class="m-r-5 fa fa-fw fa-sign-out"></i> '.$locale['logout'].'</a></li>';
                echo '</ul>';

            } else {

                echo '<ul class="dropdown-menu login-menu" aria-labelledby="user-menu">';
                    echo '<li class="p-5">';
                        $action_url = FUSION_SELF.(FUSION_QUERY ? '?'.FUSION_QUERY : '');
                        if (isset($_GET['redirect']) && strstr($_GET['redirect'], '/')) {
                            $action_url = cleanurl(urldecode($_GET['redirect']));
                        }

                        echo openform('loginform', 'post', $action_url, ['form_id' => 'login-form']);
                        switch ($settings['login_method']) {
                            case 2:
                                $placeholder = $locale['global_101c'];
                                break;
                            case 1:
                                $placeholder = $locale['global_101b'];
                                break;
                            default:
                                $placeholder = $locale['global_101a'];
                        }

                        echo form_text('user_name', '', '', ['placeholder' => $placeholder, 'required' => TRUE, 'input_id' => 'username']);
                        echo form_text('user_pass', '', '', ['placeholder' => $locale['global_102'], 'type' => 'password', 'required' => TRUE, 'input_id' => 'userpassword']);
                        echo form_checkbox('remember_me', $locale['global_103'], '', ['value' => 'y', 'class' => 'm-0', 'reverse_label' => TRUE, 'input_id' => 'rememberme']);
                        echo form_button('login', $locale['global_104'], '', ['class' => 'btn-primary btn-sm m-b-5', 'input_id' => 'loginbtn']);
                        echo closeform();
                    echo '</li>';
                    echo '<li>'.str_replace(['[LINK]', '[/LINK]'], ['<a href="'.BASEDIR.'lostpassword.php">', '</a>'], $locale['global_106']).'</li>';
                    if ($settings['enable_registration']) echo '<li><a href="'.BASEDIR.'register.php">'.$locale['register'].'</a></li>';
                echo '</ul>';

            }
        echo '</li>';
    echo '</ul>';

    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}

// Latest News
function render_latest_news() {
    $locale = fusion_get_locale();

    if (defined('NEWS_EXIST')) {
        $result = dbquery("SELECT * FROM ".DB_NEWS." ".(multilang_table("NS") ? "WHERE news_language='".LANGUAGE."' AND" : "WHERE")."
            ".groupaccess('news_visibility')." AND (news_start='0'||news_start<=".time().")
            AND (news_end='0'||news_end>=".time().") AND news_draft='0' ORDER BY news_id DESC, news_datestamp DESC LIMIT 0,5
        ");

        while ($data = dbarray($result)) {
            $comment_counter = dbcount("('comment_id')", DB_COMMENTS, "comment_type='N' AND comment_item_id='".$data['news_id']."'");

            echo "<p><i class='fa fa-book'></i> <a style='font-size:13px; color: #428bca' href='".INFUSIONS."news/news.php?readmore=".$data['news_id']."'>".trim_text($data['news_subject'],25)."</a><br /><small>";
            echo showdate('shortdate', $data['news_datestamp'])." ".THEME_BULLET;
            if ($data['news_allow_comments'] == '1') {
                if ($comment_counter < 1) {
                    echo "<a href='".INFUSIONS."news/news.php?readmore=".$data['news_id']."#comments'>Leave a comment</a>";
                } else {
                    echo "<a href='".INFUSIONS."news/news.php?readmore=".$data['news_id']."#comments'> $comment_counter ".(($comment_counter == 1) ? "Comment" : "Comments")."</a>";
                }
            } else {
                echo "Comments Disabled";
            }

            echo THEME_BULLET." ".$data['news_reads']." ".format_word($data['news_reads'], $locale['fmt_views']);
            echo "</small></p>";
        }
    }
}

// Latest Blogs
function render_latest_blogs() {
    $locale = fusion_get_locale();
    if (defined('BLOG_EXIST')) {
        $result = dbquery("SELECT * FROM ".DB_BLOG." ".(multilang_table("BL") ? "WHERE blog_language='".LANGUAGE."' AND" : "WHERE")."
            ".groupaccess('blog_visibility')." AND (blog_start='0'||blog_start<=".time().")
            AND (blog_end='0'||blog_end>=".time().") AND blog_draft='0' ORDER BY blog_id DESC, blog_datestamp DESC LIMIT 0,5
        ");

        while ($data = dbarray($result)) {
            $comment_counter = dbcount("('comment_id')", DB_COMMENTS, "comment_type='B' AND comment_item_id='".$data['blog_id']."'");

            echo "<p><i class='fa fa-book'></i> <a style='font-size:13px; color: #428bca' href='".INFUSIONS."blog/blog.php?readmore=".$data['blog_id']."'>".trim_text($data['blog_subject'],25)."</a><br /><small>";
            echo showdate('shortdate', $data['blog_datestamp'])." ".THEME_BULLET;
                if ($data['blog_allow_comments'] == '1') {
                    if ($comment_counter < 1) {
                        echo "<a href='".INFUSIONS."blog/blog.php?readmore=".$data['blog_id']."#comment'>Leave a comment</a>";
                    } else {
                        echo "<a href='".INFUSIONS."blog/blog.php?readmore=".$data['blog_id']."#comment'> $comment_counter ".(($comment_counter == 1) ? "Comment" : "Comments")."</a>";
                    }
                } else {
                    echo "Comments Disabled";
                }
            echo THEME_BULLET." ".$data['blog_reads']." ".format_word($data['blog_reads'], $locale['fmt_views']);
            echo "</small></p>";
        }
    }
}

// Latest Photos
function render_latest_photos() {
    if (defined('GALLERY_EXIST')) {
        $result = dbquery(
            "SELECT tp.photo_id, tp.photo_title, tp.photo_description, tp.photo_filename, tp.photo_thumb2, tp.photo_datestamp, tp.photo_views,
            tp.photo_order, tp.photo_allow_comments, tp.photo_allow_ratings, ta.album_id, ta.album_title, ta.album_access
            FROM ".DB_PHOTOS." tp
            LEFT JOIN ".DB_PHOTO_ALBUMS." ta USING (album_id)
            ".(multilang_table("PG") ? "WHERE album_language='".LANGUAGE."' AND" : "WHERE")." ".groupaccess('album_access')."
            GROUP BY tp.photo_id ORDER BY tp.photo_id DESC LIMIT 8"
        );

        while ($data = dbarray($result)) {
            echo "<a href='".INFUSIONS."gallery/gallery.php?photo_id=".$data['photo_id']."'><img src='".INFUSIONS."gallery/photos/thumbs/".$data['photo_thumb2']."' alt='".$data['photo_description']."' style='width:100px;' /></a>";
        }
    }
}
