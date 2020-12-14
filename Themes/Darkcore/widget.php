<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: widget.php
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
$settings = get_theme_settings('Darkcore');

if (!defined('DARKCORE_LOCALE')) {
    if (file_exists(THEMES.'Darkcore/locale/'.LANGUAGE.'.php')) {
        define('DARKCORE_LOCALE', THEMES.'Darkcore/locale/'.LANGUAGE.'.php');
    } else {
        define('DARKCORE_LOCALE', THEMES.'Darkcore/locale/English.php');
    }
}

$locale = fusion_get_locale('', DARKCORE_LOCALE);

if (isset($_POST['save_settings'])) {
    $settings = [
        'phone_number' => form_sanitizer($_POST['phone_number'], '', 'phone_number')
    ];

    if (\defender::safe()) {
        foreach ($settings as $settings_name => $settings_value) {
            $db = [
                'settings_name'  => $settings_name,
                'settings_value' => $settings_value,
                'settings_theme' => 'Darkcore'
            ];

            dbquery_insert(DB_SETTINGS_THEME, $db, 'update');
        }

        addNotice('success', $locale['drk_003']);
        redirect(FUSION_REQUEST);
    }
}

echo openform('main_settings', 'post', FUSION_REQUEST);
openside('');
echo form_text('phone_number', $locale['drk_004'], $settings['phone_number'], ['inline' => TRUE]);
closeside();

echo form_button('save_settings', $locale['save_changes'], 'save', ['class' => 'btn-primary']);
echo closeform();
