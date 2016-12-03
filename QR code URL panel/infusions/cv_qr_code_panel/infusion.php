<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion.php
| Author: Chubatyj Vitalij (Rizado)
| web: http://chubatyj.ru/
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

if (file_exists(INFUSIONS . "cv_qr_code_panel/locale/".$settings['locale'].".php")) {
	include INFUSIONS . "cv_qr_code_panel/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS . "cv_qr_code_panel/locale/English.php";
}

$inf_title = $locale['cvqr_title'];
$inf_description = $locale['cvqr_descr'];
$inf_version = "0.1";
$inf_developer = "Chubatyj Vitalij (Rizado)";
$inf_email = "v.chubatyj@yandex.ru";
$inf_weburl = "http://chubatyj.ru/";
$inf_folder = "cv_qr_code_panel";
$inf_image = "cv_qr_code.png";

// Automatic enable of the qr code panel
$inf_insertdbrow[] = DB_PANELS." (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list, panel_restriction) VALUES('".$locale['cvqr_title']."', 'cv_qr_code_panel', '', '1', '5', 'file', '0', '0', '1', '', '3')";

$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('main_color', '#601117', 'cv_qr_code_panel')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('bg_color', '#ffffff', 'cv_qr_code_panel')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('square_size', '4', 'cv_qr_code_panel')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('margin_size', '2', 'cv_qr_code_panel')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('corr_level', '1', 'cv_qr_code_panel')";

$inf_adminpanel[] = array(
	"image" => "cv_qr_code.png",
	"page" => 5,
	"rights" => "CVQR",
	"title" => $locale['cvqr_title'],
	"panel" => "admin.php",
);

$inf_deldbrow[] = DB_PANELS." WHERE panel_filename='cv_qr_code_panel'";
$inf_deldbrow[] = DB_SETTINGS_INF." WHERE settings_inf='cv_qr_code_panel'";
$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='CVQR'";