<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: infusion.php
| Author: Your Name
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

$locale = fusion_get_locale('', XXX_LOCALE);

// Infusion general information
$inf_title = $locale['xxx_title'];
$inf_description = $locale['xxx_desc'];
$inf_version = '1.0.0';
$inf_developer = 'YOUR NAME HERE';
$inf_email = 'YOUR EMAIL HERE (optional)';
$inf_weburl = 'YOUR WEBSITE HERE (optional)';
$inf_folder = "infusion_name"; // The folder in which the infusion resides.
$inf_image = 'icon.svg'; // (optional) Icon name, icon must be placed in infusion folder. Recommended size is 48x48px

// Create tables
$inf_newtable[] = DB_INFUSION_TABLE." (
    field1 SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
    field2 TINYINT(5) UNSIGNED DEFAULT '1' NOT NULL,
    field3 VARCHAR(200) DEFAULT '' NOT NULL,
    field4 VARCHAR(50) DEFAULT '' NOT NULL,
    PRIMARY KEY (field1)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

// Insert data
$inf_insertdbrow[] = DB_INFUSION_TABLE." (field1, field2, field3, field4) VALUES('', '', '', '')";

// Insert panel
//$inf_insertdbrow[] = DB_PANELS." (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list, panel_restriction, panel_languages) VALUES ('Panel Name', 'new_infusion_panel', '', '3', '1', 'file', '0', '1', '1', '', '3', '".fusion_get_settings('enabled_languages')."')";

// Insert settings
$settings = [
    'setting1' => 1,
    'setting2' => 43200
];

foreach ($settings as $name => $value) {
    $inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('".$name."', '".$value."', '".$inf_folder."')";
}

$inf_adminpanel[] = [
    'rights'   => 'XXX',
    'image'    => $inf_image,
    'title'    => $locale['xxx_admin1'],
    'panel'    => 'new_infusion_admin.php',
    'page'     => 5, // admin section
    'language' => LANGUAGE
];

// Multilanguage table
$inf_mlt[] = [
    'title'  => $locale['xxx_title'],
    'rights' => 'XX',
];

// Multilanguage links
$enabled_languages = makefilelist(LOCALE, '.|..', TRUE, 'folders');
if (!empty($enabled_languages)) {
    foreach ($enabled_languages as $language) {
        if (file_exists($inf_folder.'locale/'.$language.'.php')) {
            include $inf_folder.'locale/'.$language.'.php';
        } else {
            include $inf_folder.'locale/English.php';
        }

        // Add
        $mlt_insertdbrow[$language][] = DB_SITE_LINKS." (link_name, link_url, link_visibility, link_position, link_window, link_order, link_status, link_language) VALUES('".$locale['xxx_link1']."', 'infusions/infusion_name/file.php', '0', '2', '0', '2', '1', '".$language."')";

        // Delete
        $mlt_deldbrow[$language][] = DB_SITE_LINKS." WHERE link_url='infusions/infusion_name/file.php' AND link_language='".$language."'";
        $mlt_deldbrow[$language][] = DB_ADMIN." WHERE admin_rights='XXX' AND admin_language='".$language."'";
    }
} else {
    $inf_insertdbrow[] = DB_SITE_LINKS." (link_name, link_url, link_visibility, link_position, link_window, link_order, link_status, link_language) VALUES('".$locale['xxx_link1']."', 'infusions/infusion_name/file.php', '0', '2', '0', '2', '1', '".LANGUAGE."')";
}

// Uninstallation
$inf_droptable[] = DB_INFUSION_TABLE;
$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='XXX'";
// $inf_deldbrow[] = DB_COMMENTS." WHERE comment_type='XXX'"; // If the infusion has a enabled comments
// $inf_deldbrow[] = DB_RATINGS." WHERE rating_type='XXX'"; // If the infusion has a enabled ratings
//$inf_deldbrow[] = DB_PANELS." WHERE panel_filename='new_infusion_panel'";
$inf_deldbrow[] = DB_SITE_LINKS." WHERE link_url='infusions/infusion_name/file.php'";
$inf_deldbrow[] = DB_SETTINGS_INF." WHERE settings_inf='".$inf_folder."'";
