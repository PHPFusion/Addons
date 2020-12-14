<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: theme_db.php
| Author: PHP-Fusion Development Team
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

$theme_title = 'Darkcore';
$theme_description = 'Darkcore Theme for PHP-Fusion 9';
$theme_screenshot = 'screenshot.png';
$theme_author = 'PHP-Fusion Development Team';
$theme_web = 'https://phpfusion.com';
$theme_license = 'AGPL3';
$theme_version = '1.0.1';
$theme_folder = 'Darkcore';

// Optional for theme settings, Widget to add Phone in footer.
$theme_insertdbrow[] = DB_SETTINGS_THEME." (settings_name, settings_value, settings_theme) VALUES
    ('phone_number', '', '".$theme_folder."')
";

$theme_deldbrow[] = DB_SETTINGS_THEME." WHERE settings_theme='".$theme_folder."'";
