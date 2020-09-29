<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: home.php
| Author: Frederick MC Chan (Chan)
| Co-Author: RobiNN
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
function display_home($info) {
    $locale = fusion_get_locale();
    global $theme;

    include THEME.'templates/locale/'.LOCALESET.'/home.php';

    $_GET['section'] = !empty($_GET['section']) ? $_GET['section'] : 'news';

    if (db_exists(DB_PHOTOS) && db_exists(DB_PHOTO_ALBUMS)) {
        // go for recent, and go for most popular
        $title[] = ['url' => BASEDIR."home.php?section=recent", 'title' => $locale['sh_0100']];
        $title[] = ['url' => BASEDIR."home.php?section=popular", 'title' => $locale['sh_0101']];
        $theme->title_array = ['recent', 'popular'];
        $theme->title($title);
        // photo gallery category album
        $theme->title_array = [];
        $cat_title = [];
        $cat_result = dbquery("SELECT * FROM ".DB_PHOTO_ALBUMS."
        ".(multilang_table("PG") ? "WHERE album_language='".LANGUAGE."' AND" : "WHERE")."
        ".groupaccess('album_access')." order by album_id ASC
        ");
        if (dbrows($cat_result) > 0) {
            while ($data = dbarray($cat_result)) {
                $cat_title[] = ['url' => BASEDIR."home.php?section=".$data['album_id'], 'title' => $data['album_title']];
                $theme->title_array[] = $data['album_id'];
            }
            $theme->sub_horizontal_nav($cat_title);
        }

        $theme->set_display_mode('canvas');
        opentable('', 'm-0');
        // filter conditions
        $sql_cond = 'order by photo_datestamp desc';
        if (isset($_GET['section']) && $_GET['section'] == 'popular') {
            $sql_cond = 'order by comment_count desc';
        }
        $result = dbquery("SELECT p.*, u.user_name, count(comment_id) as comment_count
        FROM ".DB_PHOTOS." p
        INNER JOIN ".DB_PHOTO_ALBUMS." a on p.album_id = a.album_id
        INNER JOIN ".DB_USERS." u on u.user_id = p.photo_user
        LEFT JOIN ".DB_COMMENTS." c ON c.comment_item_id=p.photo_id AND comment_type='P'
        WHERE ".groupaccess('a.album_access')." ".(isset($_GET['section']) && intval($_GET['section']) ? "AND p.album_id = '".intval($_GET['section'])."'" : '')."
        GROUP BY p.photo_id
        ".$sql_cond." LIMIT 0, 20");

        if (dbrows($result) > 0) {
            echo '<div class="row">';
            while ($data = dbarray($result)) {
                echo '<div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">';
                $data['thumb_image'] = ($data['photo_thumb1'] && file_exists(INFUSIONS.'gallery/photos/thumbs/'.$data['photo_thumb1'])) ?
                    INFUSIONS.'gallery/photos/thumbs/'.$data['photo_thumb1'] : IMAGES.'imagenotfound.jpg';
                $theme->photo_thumbnail(
                    $data['thumb_image'],
                    INFUSIONS.'gallery/gallery.php?photo_id='.$data['photo_id'],
                    $data['photo_title'],
                    $data['user_name']
                );
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo $locale['sh_0102'];
        }
        closetable();
    }

    $theme->title_array = [];
    $theme->set_display_mode('full-grid');
    // home template
    $title = [];
    $id_array = [];
    foreach ($info as $db_prefix => $content) {
        $id = str_replace(DB_PREFIX, '', $db_prefix);
        $title[$id] = ['url' => BASEDIR.'home.php?section='.$id, 'title' => $content['blockTitle']];
        $id_array[] = $id;
    }
    $theme->title_array = $id_array;

    if (!empty($title)) {
        opentable($title);
        if (isset($_GET['section']) && isset($info[DB_PREFIX.$_GET['section']])) {
            $current_table = DB_PREFIX.$_GET['section'];
        } else {
            $title_keys = array_keys($title);
            $current_table = DB_PREFIX.$title_keys[0];
        }
        $data_array = isset($_GET['section']) && isset($info[$current_table]['data']) ? $info[$current_table]['data'] : [];
        if (!empty($data_array)) {
            foreach ($data_array as $data) {
                echo "<div class='col-sm-12'>";
                echo "<h3><a href='".$data['url']."'>".$data['title']."</a></h3>";
                echo "<div class='small m-b-10'>".$data['meta']."</div>";
                echo "<div>".$data['content']."</div>";
                echo "</div>";
            }
        } else {
            echo $info[$current_table]['norecord'];
        }
        closetable();
    }
}
