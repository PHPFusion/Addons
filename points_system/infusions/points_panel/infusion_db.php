<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion_db.php
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
//Admin icon megjelenítése
\PHPFusion\Admins::getInstance()->setAdminPageIcons("PSP", "<i class='admin-ico fa fa-fw fa-commenting'></i>");

if (!defined("DB_POINT")) {
	define("DB_POINT", DB_PREFIX."points");
}
if (!defined("DB_POINT_LOG")) {
	define("DB_POINT_LOG", DB_PREFIX."points_log");
}
if (!defined("DB_POINT_ST")) {
	define("DB_POINT_ST", DB_PREFIX."points_setup");
}

if (!defined("POINT_CLASS")) {
    define("POINT_CLASS", INFUSIONS."points_panel/");
}
//Nyelvi file betöltése, ez külön könyvtárból a felhasználóknak
if (!defined("POINT_LOCALE")) {
    if (file_exists(POINT_CLASS."locale/".LANGUAGE.".php")) {
        define("POINT_LOCALE", POINT_CLASS."locale/".LANGUAGE.".php");
    } else {
        define("POINT_LOCALE", POINT_CLASS."locale/Hungarian.php");
    }
}

if (db_exists(DB_POINT)) {
    include_once POINT_CLASS."autoload.php";
    PHPFusion\Points\UserPoint::getInstance()->GetPoint();
}
