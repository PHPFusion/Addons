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
    private static $locale = [];
    private $points_settings;

    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function displayPointsAdmin() {

        $locale = fusion_get_locale("", POINT_LOCALE);
        $points_settings = self::CurrentSetup();
        $savesettings = filter_input(INPUT_POST, 'savesettings', FILTER_DEFAULT);
        if (!empty($savesettings)) {
            $ps_dateadd = filter_input(INPUT_POST, 'ps_dateadd', FILTER_DEFAULT);
            $datead = (!empty($ps_dateadd) ? form_sanitizer($ps_dateadd, 0, "ps_dateadd") : $points_settings['ps_dateadd']);

            $points_settings = [
                'ps_id'         => $points_settings['ps_id'],
                'ps_activ'      => form_sanitizer(filter_input(INPUT_POST, 'ps_activ', FILTER_DEFAULT), 0, 'ps_activ'),
                'ps_pricetype'  => form_sanitizer(filter_input(INPUT_POST, 'ps_pricetype', FILTER_DEFAULT), 0, 'ps_pricetype'),
                'ps_holiday'    => form_sanitizer(filter_input(INPUT_POST, 'ps_holiday', FILTER_DEFAULT), 0, 'ps_holiday'),
                'ps_unitprice'  => form_sanitizer(filter_input(INPUT_POST, 'ps_unitprice', FILTER_DEFAULT), 0, 'ps_unitprice'),
                'ps_default'    => form_sanitizer(filter_input(INPUT_POST, 'ps_default', FILTER_DEFAULT), 0, 'ps_default'),
                'ps_dateadd'    => $datead * 86400,
                'ps_day'        => form_sanitizer(filter_input(INPUT_POST, 'ps_day', FILTER_DEFAULT), 0, 'ps_day'),
                'ps_autogroup'  => form_sanitizer(filter_input(INPUT_POST, 'ps_autogroup', FILTER_DEFAULT), 0, 'ps_autogroup'),
                'ps_holidays'   => form_sanitizer(filter_input(INPUT_POST, 'ps_holidays', FILTER_DEFAULT), '', 'ps_holidays'),
                'ps_page'       => form_sanitizer(filter_input(INPUT_POST, 'ps_page', FILTER_DEFAULT), 0, 'ps_page')
            ];

            if (\defender::safe()) {
            	dbquery_insert(DB_POINT_ST, $points_settings, 'update');
            	addNotice('success', $locale['PSP_E14']);
            }
        }

        $opts = ['1' => $locale['on'], '0' => $locale['off']];
        $options = [0 => $locale['PSP_S00'], 1 => $locale['PSP_S01']];
        echo openform("settingsform", "post", FUSION_REQUEST, ['class' => 'spacer-sm']).
            form_select('ps_activ', $locale['PSP_S02'], $points_settings['ps_activ'], [
                'options' => $opts,
                'inline'  => TRUE,
                'width'   => '100%',
                'ext_tip' => $locale['PSP_S03']
            ]).
            form_text('ps_holiday', $locale['PSP_S15'], $points_settings['ps_holiday'], [
                'inline'      => TRUE,
                'type'        => 'number',
                'inner_width' => '150px',
                'number_min'  => 1,
                'max_length'  => 4,
                'ext_tip'     => $locale['PSP_S16']
            ]).
            form_select('ps_pricetype', $locale['PSP_S04'], $points_settings['ps_pricetype'], [
                'options' => $options,
                'inline' => TRUE
            ]).
            form_text('ps_unitprice', $locale['PSP_S05'], $points_settings['ps_unitprice'], [
                'inline'      => TRUE,
                'type'        => 'number',
                'inner_width' => '150px',
                'number_min'  => 1,
                'max_length'  => 4
            ]).
            form_text('ps_default', $locale['PSP_S06'], $points_settings['ps_default'], [
                'inline'      => TRUE,
                'type'        => 'number',
                'inner_width' => '150px',
                'number_min'  => 1,
                'max_length'  => 4,
                'ext_tip'     => $locale['PSP_S07']
            ]).
            form_text('ps_dateadd', $locale['PSP_S08'], ($points_settings['ps_dateadd'] / 86400), [
                'inline'      => TRUE,
                'type'        => 'number',
                'inner_width' => '150px',
                'number_min'  => 1,
                'max_length'  => 4,
                'ext_tip'     => $locale['PSP_S09']
            ]).
            form_text('ps_day', $locale['PSP_S10'], $points_settings['ps_day'], [
                'inline'      => TRUE,
                'type'        => 'number',
                'inner_width' => '150px',
                'number_min'  => 1,
                'max_length'  => 4,
                'ext_tip'     => $locale['PSP_S11']
            ]).
            form_select('ps_autogroup', $locale['PSP_S12'], $points_settings['ps_autogroup'], [
                'options' => $opts,
                'inline'  => TRUE,
                'width'   => '100%',
                'ext_tip' => $locale['PSP_S13']
            ]).
            form_textarea('ps_holidays', $locale['PSP_S17'], $points_settings['ps_holidays'], [
                'inline'   => TRUE,
                'autosize' => TRUE,
                'tinymce'  => 'simple',
                'ext_tip'  => $locale['PSP_S18']
            ]).
            form_text('ps_page', $locale['PSP_S14'], $points_settings['ps_page'], [
                'inline'      => TRUE,
                'type'        => 'number',
                'inner_width' => '150px',
                'max_length'  => 4
            ]).
            form_button('savesettings', $locale['save'], $locale['save'], ['class' => 'btn-success']).
        closeform();
    }
}
