<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: points_panel/classes/admin/points_settings.php
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
namespace PHPFusion\Points;

class PointsSettingsAdmin extends PointsModel {
    private static $instance = NULL;
    private $locale = [];
    private $points_settings = [];

    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function displayPointsAdmin() {

        $locale = fusion_get_locale("", POINT_LOCALE);
        $points_settings = self::CurrentSetup();
        if (isset($_POST['savesettings'])) {
        	$datead = (isset($_POST['ps_dateadd']) ? form_sanitizer($_POST['ps_dateadd'], 0, "ps_dateadd") : $points_settings['ps_dateadd']);

            $points_settings = [
                'ps_id'         => $points_settings['ps_id'],
                'ps_activ'      => form_sanitizer($_POST['ps_activ'], 0, 'ps_activ'),
                'ps_default'    => form_sanitizer($_POST['ps_default'], 0, 'ps_default'),
                'ps_dateadd'    => $datead * 86400,
                'ps_day'        => form_sanitizer($_POST['ps_day'], 0, 'ps_day'),
                'ps_page'       => form_sanitizer($_POST['ps_page'], 0, 'ps_page')
            ];

            if (\defender::safe()) {
            	dbquery_insert(DB_POINT_ST, $points_settings, 'update');
            	addNotice('success', $locale['PONT_300']);
            }
        }

        $opts = ['1' => $locale['on'], '0' => $locale['off']];
        echo openform("settingsform", "post", FUSION_REQUEST, ['class' => 'spacer-sm']);
        echo form_select('ps_activ', $locale['PONT_110'], $points_settings['ps_activ'], [
            'options' => $opts,
            'inline'  => TRUE,
            'width'   => '100%',
            'ext_tip' => $locale['PONT_111']
        ]);
        echo form_text('ps_default', $locale['PONT_112'], $points_settings['ps_default'], [
            'inline'      => TRUE,
            'type'        => 'number',
            'inner_width' => '150px',
            'number_min'  => 1,
            'max_length'  => 4,
            'ext_tip'     => $locale['PONT_113']
        ]);
        echo form_text('ps_dateadd', $locale['PONT_114'], $points_settings['ps_dateadd'] / 86400, [
            'inline'      => TRUE,
            'type'        => 'number',
            'inner_width' => '150px',
            'number_min'  => 1,
            'max_length'  => 4,
            'ext_tip'     => $locale['PONT_115']
        ]);
        echo form_text('ps_day', $locale['PONT_116'], $points_settings['ps_day'], [
            'inline'      => TRUE,
            'type'        => 'number',
            'inner_width' => '150px',
            'number_min'  => 1,
            'max_length'  => 4,
            'ext_tip'     => $locale['PONT_117']
        ]);
        echo form_text('ps_page', $locale['PONT_118'], $points_settings['ps_page'], [
            'inline'      => TRUE,
            'type'        => 'number',
            'inner_width' => '150px',
            'max_length'  => 4
        ]);
        echo form_button('savesettings', $locale['save'], $locale['save'], ['class' => 'btn-success']);
        echo closeform();
    }
}
