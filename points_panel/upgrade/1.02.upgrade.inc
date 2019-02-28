<?php
$inf_version = '1.02';

if (!db_exists(DB_POINT_BAN)) {
    $create_table = DB_POINT_BAN." (
	ban_id         INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
	ban_user_id	   MEDIUMINT(8)          NOT NULL DEFAULT '0',
	ban_time_start INT(10)               NOT NULL DEFAULT '0',
	ban_time_stop  INT(10)               NOT NULL DEFAULT '0',
	ban_text       TEXT                  NOT NULL,
	ban_language   VARCHAR(50)           NOT NULL DEFAULT '".LANGUAGE."',
	PRIMARY KEY (ban_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";
    dbquery("CREATE TABLE ".$create_table);
}

if (!db_exists(DB_POINT_INF)) {
    $create_table = DB_POINT_INF." (
	pi_id          INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
	pi_user_id     INT(11)               NOT NULL DEFAULT '0',
	pi_user_access TINYINT(4)            NOT NULL DEFAULT '0',
	pi_link        VARCHAR(255),
	pi_title       VARCHAR(255),
	pi_language    VARCHAR(50)           NOT NULL DEFAULT '".LANGUAGE."',
PRIMARY KEY (pi_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";
    dbquery("CREATE TABLE ".$create_table);
}
$enabled_languages = makefilelist(LOCALE, ".|..", TRUE, "folders");
if (!empty($enabled_languages)) {
    foreach($enabled_languages as $language) {
        $locale = fusion_get_locale("", INFUSIONS.$inf_folder."/locale/".$language.".php");
		$mlt_insertdbrow[$language][] = DB_POINT_INF." (pi_user_id, pi_user_access, pi_link, pi_title, pi_language) VALUES
			('0', ".USER_LEVEL_MEMBER.", '".fusion_get_settings('site_path')."infusions/points_panel/points_bestof.php', '".$locale['PNT_I04']."', '".$language."')";

		$mlt_deldbrow[$language][] = DB_POINT_INF." WHERE pi_language='".$language."'";
    }
} else {
	$inf_insertdbrow[] = DB_POINT_INF." (pi_user_id, pi_user_access, pi_link, pi_title, pi_language) VALUES
	('0', ".USER_LEVEL_MEMBER.", '".fusion_get_settings('site_path')."infusions/points_panel/points_bestof.php', '".$locale['krd_217']."', '".LANGUAGE."')";

}
