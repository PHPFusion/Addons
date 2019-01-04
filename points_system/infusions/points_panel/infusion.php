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
if (!defined("IN_FUSION")) {
    die("Access Denied");
}

//Nyelv betöltése
$locale = fusion_get_locale("", POINT_LOCALE);
//Alap adatok megadása
$inf_title = $locale['PNT_I01'];
$inf_description = $locale['PNT_I02'];
$inf_version = "1.02";
$inf_developer = "karrak";
$inf_email = "admin@fusionjatek.hu";
$inf_weburl = "http://www.fusionjatek.hu";
$inf_folder = "points_panel";
$inf_image = "points.png";
// Adminisztrációs oldal adatai
$inf_adminpanel[] = [
    'title'  => $locale['PNT_I01'],
    'image'  => $inf_image,
    'panel'  => 'admin.php',
    'rights' => 'PSP',
    'page'   => 5
];
//Többnyelvû tábla adata
$inf_mlt[] = [
    'title'  => $inf_title,
    'rights' => 'PSP'
];
//Adatbázis létrehozása
$inf_newtable[] = DB_POINT." (
    point_id        INT(11)    UNSIGNED NOT NULL AUTO_INCREMENT,
    point_user      INT(11)             NOT NULL DEFAULT '0',
    point_point     BIGINT(11)          NOT NULL DEFAULT '0',
    point_increase  INT(11)             NOT NULL DEFAULT '0',
    point_language  VARCHAR(50)         NOT NULL DEFAULT '".LANGUAGE."',
	PRIMARY KEY (point_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_POINT_LOG." (
	log_id         INT(11)         UNSIGNED NOT NULL AUTO_INCREMENT,
	log_user_id    INT(11)                  NOT NULL DEFAULT '0',
	log_pmod       ENUM('1','2')                     DEFAULT '1',
	log_date       INT(11)                  NOT NULL DEFAULT '0',
	log_descript   VARCHAR(1000),
	log_point      INT(10)                  NOT NULL DEFAULT '0',
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
	pi_link        VARCHAR(255),
	pi_title       VARCHAR(255),
	pi_language    VARCHAR(50)           NOT NULL DEFAULT '".LANGUAGE."',
PRIMARY KEY (pi_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_POINT_ST." (
	ps_id INT (1) UNSIGNED NOT NULL AUTO_INCREMENT,
	ps_activ ENUM('0','1') DEFAULT '0',
	ps_naplodel ENUM('0','1') DEFAULT '0',
	ps_dateadd INT(11) NOT NULL default '0',
	ps_day DOUBLE,
	ps_default INT(11) NOT NULL default '0',
	ps_page INT(2) NOT NULL default '0',
	ps_dailycheck INT(11) NOT NULL default '0',
	ps_language VARCHAR(50) NOT NULL DEFAULT '".LANGUAGE."',
	PRIMARY KEY (ps_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_insertdbrow[] = DB_PANELS." SET panel_name='".$inf_title."', panel_filename='".$inf_folder."', panel_content='', panel_side=4, panel_order='99', panel_type='file', panel_access=".USER_LEVEL_MEMBER.", panel_display='1', panel_status='1', panel_restriction='3'";
$tomorrow = mktime(0, 0, 0, date("m"), date("d")+1, date("Y"));

$enabled_languages = makefilelist(LOCALE, ".|..", TRUE, "folders");
if (!empty($enabled_languages)) {
    foreach($enabled_languages as $language) {
        $locale = fusion_get_locale("", INFUSIONS.$inf_folder."/locale/".$language.".php");
		$mlt_insertdbrow[$language][] = DB_POINT_ST." (ps_activ, ps_naplodel, ps_dateadd, ps_day, ps_default, ps_page, ps_dailycheck, ps_language) VALUES ('1', '1', '86400', '500', '5000', '20', '".$tomorrow."', '".$language."')";
		$mlt_insertdbrow[$language][] = DB_POINT_INF." (pi_user_id, pi_user_access, pi_link, pi_title, pi_language) VALUES
			('0', ".USER_LEVEL_SUPER_ADMIN.", '".fusion_get_settings('site_path')."infusions/".$inf_folder."/admin.php', '".$locale['PONT_M01']."', '".$language."'),
			('0', ".USER_LEVEL_ADMIN.", '".fusion_get_settings('site_path')."infusions/".$inf_folder."/points_ban.php', '".$locale['PONT_M02']."', '".$language."'),
			('0', ".USER_LEVEL_MEMBER.", '".fusion_get_settings('site_path')."infusions/".$inf_folder."/points_bestof.php', '".$locale['PONT_M03']."', '".$language."')";

		$mlt_deldbrow[$language][] = DB_POINT." WHERE point_language='".$language."'";
		$mlt_deldbrow[$language][] = DB_POINT_BAN." WHERE ban_language='".$language."'";
		$mlt_deldbrow[$language][] = DB_POINT_ST." WHERE ps_language='".$language."'";
		$mlt_deldbrow[$language][] = DB_POINT_INF." WHERE pi_language='".$language."'";
    }
} else {
	$inf_insertdbrow[] = DB_POINT_ST." (ps_activ, ps_naplodel, ps_dateadd, ps_day, ps_default, ps_page, ps_dailycheck, ps_language) VALUES ('1', '1', '86400', '500', '5000', '20', '".$tomorrow."', '".LANGUAGE."')";
	$inf_insertdbrow[] = DB_POINT_INF." (pi_user_id, pi_user_access, pi_link, pi_title, pi_language) VALUES
	('0', ".USER_LEVEL_SUPER_ADMIN.", '".fusion_get_settings('site_path')."infusions/".$inf_folder."/admin.php', '".$locale['PONT_M01']."', '".LANGUAGE."'),
	('0', ".USER_LEVEL_SUPER_ADMIN.", '".fusion_get_settings('site_path')."infusions/".$inf_folder."/pont_ban.php', '".$locale['PONT_M02']."', '".LANGUAGE."'),
	('0', ".USER_LEVEL_MEMBER.", '".fusion_get_settings('site_path')."infusions/".$inf_folder."/points_bestof.php', '".$locale['krd_217']."', '".LANGUAGE."')";

}

$inf_droptable[] = DB_POINT;
$inf_droptable[] = DB_POINT_LOG;
$inf_droptable[] = DB_POINT_ST;
$inf_droptable[] = DB_POINT_BAN;
$inf_droptable[] = DB_POINT_INF;

$inf_deldbrow[] = DB_PANELS." WHERE panel_filename='".$inf_folder."'";
$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='PSP'";
$inf_deldbrow[] = DB_LANGUAGE_TABLES." WHERE mlt_rights='PSP'";
