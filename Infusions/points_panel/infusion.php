<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion.php
| Author: karrak
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

//load language
$locale = fusion_get_locale("", POINT_LOCALE);
//default data
$inf_title = $locale['PSP_I01'];
$inf_description = $locale['PSP_I02'];
$inf_version = "1.02";
$inf_developer = "karrak";
$inf_email = "admin@fusionjatek.hu";
$inf_weburl = "http://www.fusionjatek.hu";
$inf_folder = "points_panel";
$inf_image = "points.png";
//multilang table
$inf_mlt[] = [
    'title'  => $inf_title,
    'rights' => 'PSP'
];
//create database
$inf_newtable[] = DB_POINT." (
    point_id        INT(11)    UNSIGNED NOT NULL AUTO_INCREMENT,
    point_user      INT(11)             NOT NULL DEFAULT '0',
    point_point     BIGINT(11)          NOT NULL DEFAULT '0',
    point_increase  INT(11)             NOT NULL DEFAULT '0',
    point_group     TEXT                NOT NULL,
    point_language  VARCHAR(50)         NOT NULL DEFAULT '".LANGUAGE."',
	PRIMARY KEY (point_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_POINT_LOG." (
    log_id         INT(11)         UNSIGNED NOT NULL AUTO_INCREMENT,
    log_user_id    INT(11)                  NOT NULL DEFAULT '0',
    log_pmod       ENUM('1','2')                     DEFAULT '1',
    log_date       INT(11)                  NOT NULL DEFAULT '0',
    log_descript   VARCHAR(1000)            NOT NULL DEFAULT '',
    log_point      INT(10)                  NOT NULL DEFAULT '0',
    log_active     ENUM('0','1')                     DEFAULT '0',
    PRIMARY KEY (log_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_POINT_BAN." (
    ban_id         INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
    ban_user_id	   MEDIUMINT(8)          NOT NULL DEFAULT '0',
    ban_time_start INT(10)               NOT NULL DEFAULT '0',
    ban_time_stop  INT(10)               NOT NULL DEFAULT '0',
    ban_text       TEXT                  NOT NULL,
    ban_language   VARCHAR(50)           NOT NULL DEFAULT '".LANGUAGE."',
    PRIMARY KEY (ban_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_POINT_INF." (
    pi_id          INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
    pi_user_id     INT(11)               NOT NULL DEFAULT '0',
    pi_user_access TINYINT(4)            NOT NULL DEFAULT '0',
    pi_link        VARCHAR(255)          NOT NULL DEFAULT '',
    pi_title       VARCHAR(255)          NOT NULL DEFAULT '',
    pi_language    VARCHAR(50)           NOT NULL DEFAULT '".LANGUAGE."',
    PRIMARY KEY (pi_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_POINT_GROUP." (
    pg_id           INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
    pg_group_id     INT(11)               NOT NULL DEFAULT '0',
    pg_group_points BIGINT(11)            NOT NULL DEFAULT '0',
    PRIMARY KEY (pg_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_POINT_BANK." (
    pb_id              INT(10)       UNSIGNED NOT NULL AUTO_INCREMENT,
    pb_user_id         INT(11)                NOT NULL DEFAULT '0',
    pb_loan_activ      ENUM('0','1')                   DEFAULT '0',
    pb_interest_activ  ENUM('0','1')                   DEFAULT '0',
    pb_loan_start      INT(11)                NOT NULL DEFAULT '0',
    pb_loan_tor_start  INT(11)                NOT NULL DEFAULT '0',
    pb_loan_end        INT(3)                 NOT NULL DEFAULT '0',
    pb_loan_day        INT(3)                 NOT NULL DEFAULT '0',
    pb_loan_amount     INT(5)                 NOT NULL DEFAULT '0',
    pb_loan_reszlet    INT(5)                 NOT NULL DEFAULT '0',
    pb_loan_levont     INT(10)                NOT NULL DEFAULT '0',
    pb_interest_start  INT(11)                NOT NULL DEFAULT '0',
    pb_interest_end    INT(11)                NOT NULL DEFAULT '0',
    pb_interest_amount INT(5)                 NOT NULL DEFAULT '0',
    pb_interest_szaz   INT(3)                 NOT NULL DEFAULT '0',
    pb_interest_get    INT(5)                 NOT NULL DEFAULT '0',
	pb_language        VARCHAR(50)            NOT NULL DEFAULT '".LANGUAGE."',
	PRIMARY KEY (pb_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_POINT_ST." (
    ps_id               INT(1)        UNSIGNED NOT NULL AUTO_INCREMENT,
    ps_activ            ENUM('0','1')                   DEFAULT '0',
    ps_pricetype        ENUM('0','1')                   DEFAULT '1',
	ps_holiday          INT(5)                 NOT NULL DEFAULT '0',
    ps_unitprice        INT(5)                 NOT NULL DEFAULT '0',
    ps_naplodel         ENUM('0','1')                   DEFAULT '0',
    ps_dateadd          INT(11)                NOT NULL DEFAULT '0',
    ps_day              DOUBLE,
    ps_default          INT(11)                NOT NULL DEFAULT '0',
    ps_page             INT(2)                 NOT NULL DEFAULT '0',
    ps_autogroup        ENUM('0','1')                   DEFAULT '0',
    ps_bank             ENUM('0','1')                   DEFAULT '0',
    ps_loan             ENUM('0','1')                   DEFAULT '0',
    ps_interest         ENUM('0','1')                   DEFAULT '0',
    ps_loan_day         INT(3)                 NOT NULL DEFAULT '0',
    ps_loan_max         INT(5)                 NOT NULL DEFAULT '0',
    ps_loan_interest    INT(3)                 NOT NULL DEFAULT '0',
    ps_deposit_day      INT(3)                 NOT NULL DEFAULT '0',
    ps_deposit_max      INT(5)                 NOT NULL DEFAULT '0',
    ps_deposit_interest INT(3)                 NOT NULL DEFAULT '0',
    ps_dailycheck       INT(11)                NOT NULL DEFAULT '0',
	ps_holidays         TEXT                   NOT NULL,
    ps_language         VARCHAR(50)            NOT NULL DEFAULT '".LANGUAGE."',
    PRIMARY KEY (ps_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_insertdbrow[] = DB_PANELS." (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list, panel_restriction, panel_languages) VALUES('".$inf_title."', '".$inf_folder."', '', '4', '5', 'file', '".USER_LEVEL_MEMBER."', '1', '1', '', '3', '".fusion_get_settings('enabled_languages')."')";
$tomorrow = mktime(0, 0, 0, date("m"), date("d")+1, date("Y"));

$enabled_languages = makefilelist(LOCALE, ".|..", TRUE, "folders");
if (!empty($enabled_languages)) {
    foreach($enabled_languages as $language) {
        include INFUSIONS.$inf_folder."/locale/".$language."/points.php";

        $mlt_adminpanel[$language][] = [
            'title'    => $locale['PSP_I03'],
            'image'    => $inf_image,
            'panel'    => "admin.php",
            'rights'   => "PSP",
            'page'     => 5,
            'language' => $language
        ];

        $mlt_insertdbrow[$language][] = DB_POINT_ST." (ps_activ, ps_pricetype, ps_holiday, ps_unitprice, ps_naplodel, ps_dateadd, ps_day, ps_default, ps_page, ps_autogroup, ps_bank, ps_loan, ps_interest, ps_loan_day, ps_loan_max, ps_loan_interest, ps_deposit_day, ps_deposit_max, ps_deposit_interest, ps_dailycheck, ps_holidays, ps_language) VALUES ('1', '1', '1', '10', '1', '86400', '500', '5000', '20', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '".$tomorrow."', '0101,0315,0420,0421,0501,0608,0609,0920,1023,1101,1224,1225,1226', '".$language."')";
        $mlt_insertdbrow[$language][] = DB_POINT_INF." (pi_user_id, pi_user_access, pi_link, pi_title, pi_language) VALUES
            ('0', '".USER_LEVEL_SUPER_ADMIN."', '".fusion_get_settings('site_path')."infusions/".$inf_folder."/admin.php', '".$locale['PSP_M01']."', '".$language."'),
            ('0', '".USER_LEVEL_ADMIN."', '".fusion_get_settings('site_path')."infusions/".$inf_folder."/points_ban.php', '".$locale['PSP_M02']."', '".$language."'),
            ('0', '".USER_LEVEL_MEMBER."', '".fusion_get_settings('site_path')."infusions/".$inf_folder."/points_bank.php', '".$locale['PSP_M05']."', '".$language."'),
            ('0', '".USER_LEVEL_MEMBER."', '".fusion_get_settings('site_path')."infusions/".$inf_folder."/points_diary.php', '".$locale['PSP_M04']."', '".$language."'),
            ('0', '".USER_LEVEL_MEMBER."', '".fusion_get_settings('site_path')."infusions/".$inf_folder."/points_place.php', '".$locale['PSP_M03']."', '".$language."')";

        $mlt_deldbrow[$language][] = DB_POINT." WHERE point_language='".$language."'";
        $mlt_deldbrow[$language][] = DB_POINT_BAN." WHERE ban_language='".$language."'";
        $mlt_deldbrow[$language][] = DB_POINT_ST." WHERE ps_language='".$language."'";
        $mlt_deldbrow[$language][] = DB_POINT_INF." WHERE pi_language='".$language."'";
        $mlt_deldbrow[$language][] = DB_POINT_BANK." WHERE pb_language='".$language."'";
        $mlt_deldbrow[$language][] = DB_ADMIN." WHERE admin_rights='PSP' AND admin_language='".$language."'";
    }
} else {
    $inf_adminpanel[] = [
        'title'    => $locale['PSP_I03'],
        'image'    => $inf_image,
        'panel'    => "admin.php",
        'rights'   => "PSP",
        'page'     => 5,
        'language' => LANGUAGE
    ];

    $inf_insertdbrow[] = DB_POINT_ST." (ps_activ, ps_pricetype, ps_holiday, ps_unitprice, ps_naplodel, ps_dateadd, ps_day, ps_default, ps_page, ps_autogroup, ps_bank, ps_loan, ps_interest, ps_loan_day, ps_loan_max, ps_loan_interest, ps_deposit_day, ps_deposit_max, ps_deposit_interest, ps_dailycheck, ps_holidays, ps_language) VALUES ('1', '1', '1', '10', '1', '86400', '500', '5000', '20', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '".$tomorrow."', '0101,0315,0420,0421,0501,0608,0609,0920,1023,1101,1224,1225,1226', '".LANGUAGE."')";
    $inf_insertdbrow[] = DB_POINT_INF." (pi_user_id, pi_user_access, pi_link, pi_title, pi_language) VALUES
        ('0', ".USER_LEVEL_SUPER_ADMIN.", '".fusion_get_settings('site_path')."infusions/".$inf_folder."/admin.php', '".$locale['PSP_M01']."', '".LANGUAGE."'),
        ('0', ".USER_LEVEL_ADMIN.", '".fusion_get_settings('site_path')."infusions/".$inf_folder."/points_ban.php', '".$locale['PSP_M02']."', '".LANGUAGE."'),
        ('0', ".USER_LEVEL_MEMBER.", '".fusion_get_settings('site_path')."infusions/".$inf_folder."/points_bank.php', '".$locale['PSP_M05']."', '".LANGUAGE."'),
        ('0', ".USER_LEVEL_MEMBER.", '".fusion_get_settings('site_path')."infusions/".$inf_folder."/points_diary.php', '".$locale['PSP_M04']."', '".LANGUAGE."'),
        ('0', ".USER_LEVEL_MEMBER.", '".fusion_get_settings('site_path')."infusions/".$inf_folder."/points_place.php', '".$locale['PSP_M03']."', '".LANGUAGE."')";
}

$inf_droptable[] = DB_POINT;
$inf_droptable[] = DB_POINT_LOG;
$inf_droptable[] = DB_POINT_ST;
$inf_droptable[] = DB_POINT_BAN;
$inf_droptable[] = DB_POINT_INF;
$inf_droptable[] = DB_POINT_GROUP;
$inf_droptable[] = DB_POINT_BANK;

$inf_deldbrow[] = DB_PANELS." WHERE panel_filename='".$inf_folder."'";
$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='PSP'";
$inf_deldbrow[] = DB_LANGUAGE_TABLES." WHERE mlt_rights='PSP'";
