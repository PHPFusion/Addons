<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: resource.php
| Author: Core Development Team (coredevs@phpfusion.com)
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
namespace Artemis\Model;

use PHPFusion\Admins;

abstract class resource extends Admins {

    public static $page_title = "";
    protected static $locale = [];
    private static $user_drop = [];
    private static $aidlink = "";
    private static $userdata = [];

    public function __construct() {
        parent::__construct();
        self::$page_title = $this->set_page_title();
    }

    public function set_page_title() {

        $locale = self::$locale;

        $sections = Admins::getInstance()->getAdminSections();

        $pages = Admins::getInstance()->getAdminPages();

        $is_current_page = Admins::getInstance()->getCurrentPage();

        if (!empty($sections) && !empty($pages)) {

            $pages = flatten_array($pages);

            if (!empty($is_current_page)) {

                foreach ($pages as $page_data) {

                    if ($page_data['admin_link'] == $is_current_page) {

                        $page_title = $page_data['admin_title'];

                        $page_section = $sections[$page_data['admin_page']];

                        $page_rights = $page_data['admin_rights'];

                        return [
                            "title" => $page_section.$locale['global_201'].$page_title,
                            "icon"  => "<img class='img-responsive' alt='$page_title' src='".get_image("ac_".$page_rights)."'/>",
                        ];
                    }
                }
            }
        }

        return [
            "title" => self::get_locale('artemis_admin'),
            "icon"  => "<img class='img-responsive' alt='PHPFusion 9' src='".IMAGES."phpfusion-icon.png'/>",
        ];
    }

    public static function get_locale($key = NULL) {
        return $key === NULL ? self::$locale : (isset(self::$locale[$key]) ? self::$locale[$key] : NULL);
    }

    public static function add_css($file) {
        if (file_exists(THEMES."admin_themes/Artemis/css/".$file)) {
            add_to_head("<link rel='stylesheet' href='".THEMES."admin_themes/Artemis/css/".$file."' type='text/css' />");
        }
    }

    public static function get_title() {
        return self::$page_title;
    }

    public static function admin_language_switcher() {

        $locale = self::get_locale();

        $language_opts = '';
        if (count(fusion_get_enabled_languages()) > 1) {
            $language_opts = "<li class='dropdown'>\n";
            $language_opts .= "<a id='ddlangs' class='dropdown-toggle pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='".$locale['UM101']."'><i class='fa fa-globe'></i> <span class='hidden-xs hidden-sm'>".translate_lang_names(LANGUAGE)."</span> <span class='caret'></span></a>\n";
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

        return $language_opts;
    }

    public static function set_static_variables() {

        $aidlink = fusion_get_aidlink();
        $userdata = fusion_get_userdata();

        if (file_exists(THEMES."admin_themes/Artemis/locale/".LANGUAGE.".php")) {
            $locale = fusion_get_locale('', THEMES."admin_themes/Artemis/locale/".LANGUAGE.".php");
        } else {
            $locale = fusion_get_locale('', THEMES."admin_themes/Artemis/locale/English.php");
        }

        if (!empty($locale) && empty(self::$locale)) {
            foreach ($locale as $locale_key => $locale_value) {
                self::$locale[$locale_key] = $locale_value;
            }
        }

        self::$userdata = $userdata;

        self::$aidlink = $aidlink;

        self::$user_drop = [
            BASEDIR."edit_profile.php"                               => self::$locale['edit']." ".self::$locale['profile'],
            BASEDIR."profile.php?lookup=".self::$userdata['user_id'] => self::$locale['view']." ".self::$locale['profile'],
            "---"                                                    => "---",
            FUSION_REQUEST."&logout"                             => self::$locale['admin-logout'],
            BASEDIR."index.php?logout=yes"                           => self::$locale['logout']
        ];
    }

    public static function get_udrop() {
        return self::$user_drop;
    }

    public static function get_aidlink() {
        return self::$aidlink;
    }

    public static function get_userdata() {
        return self::$userdata;
    }

}
