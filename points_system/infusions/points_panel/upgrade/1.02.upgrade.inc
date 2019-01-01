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

