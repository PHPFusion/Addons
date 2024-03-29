<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: theme.php
| Author: PHP Fusion Inc
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require_once THEME."autoloader.php";

if (!defined('IS_V910')) {
    define('IS_V910', (bool)version_compare(fusion_get_settings('version'), '9.03', (strpos(fusion_get_settings('version'), '9.10') === 0 ? '>' : '<')));
}

$theme_settings = get_theme_settings('FusionTheme');
$theme_package = !empty($theme_settings['theme_pack']) ? $theme_settings['theme_pack'] : 'nebula';
define("THEME_PACK", THEME."themepack/".$theme_package."/");
themefactory\Core::getInstance()->get_ThemePack($theme_package);
