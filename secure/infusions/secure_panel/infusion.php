<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion.php
| Author: karrak
| verzió: 1.01
| web: http://fusionjatek.hu
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined("IN_FUSION")) { die("Access Denied"); }
//Nyelv betöltése
$locale = fusion_get_locale("", SCR_LOCALE);
//Alap adatok megadása
$inf_title = $locale['SEC_0000'];
$inf_description = $locale['SEC_0001'];
$inf_version = "1.0";
$inf_developer = "karrak";
$inf_email = "admin@fusionjatek.hu";
$inf_weburl = "http://www.fusionjatek.hu";
$inf_folder = "secure_panel";
$inf_image = "secure.png";
// Adminisztrációs oldal adatai
$inf_adminpanel[] = array(
	"title" => $locale['SEC_0000'],
	"image" => $inf_image,
	"panel" => "admin.php",
	"rights" => "SCR",
	"page" => 5
);
//Többnyelvû tábla adata
$inf_mlt[] = array(
	"title" => $locale['SEC_0000'],
	"rights" => "SCR"
);


$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('secure_aktiv', '1', '".$inf_folder."')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('secure_time', '600', '".$inf_folder."')";
$inf_insertdbrow[] = DB_PANELS." (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_restriction, panel_languages) VALUES('".$inf_title."', '".$inf_folder."', '', '2', '3', 'file', '0', '1', '1', '3', '".fusion_get_settings('enabled_languages')."')";

$inf_deldbrow[] = DB_PANELS." WHERE panel_filename='".$inf_folder."'";
$inf_deldbrow[] = DB_SETTINGS_INF." WHERE settings_inf='".$inf_folder."'";
$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='SCR'";
$inf_deldbrow[] = DB_LANGUAGE_TABLES." WHERE mlt_rights='SCR'";?>