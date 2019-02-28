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

    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function displayPointsAdmin() {

        $locale = fusion_get_locale("", POINT_LOCALE);
        $points_settings = self::CurrentSetup();
        $this->savesettings = filter_input(INPUT_POST, 'savesettings', FILTER_DEFAULT);
        if (!empty($this->savesettings)) {
            $this->ps_dateadd = filter_input(INPUT_POST, 'ps_dateadd', FILTER_DEFAULT);
            $this->ps_activ = filter_input(INPUT_POST, 'ps_activ', FILTER_DEFAULT);
            $this->ps_default = filter_input(INPUT_POST, 'ps_default', FILTER_DEFAULT);
            $this->ps_day = filter_input(INPUT_POST, 'ps_day', FILTER_DEFAULT);
            $this->ps_page = filter_input(INPUT_POST, 'ps_page', FILTER_DEFAULT);
            $this->ps_pricetype = filter_input(INPUT_POST, 'ps_pricetype', FILTER_DEFAULT);
            $this->ps_unitprice = filter_input(INPUT_POST, 'ps_unitprice', FILTER_DEFAULT);
            $this->ps_autogroup = filter_input(INPUT_POST, 'ps_autogroup', FILTER_DEFAULT);
            $datead = (!empty($this->ps_dateadd) ? form_sanitizer($this->ps_dateadd, 0, "ps_dateadd") : $points_settings['ps_dateadd']);

            $points_settings = [
                'ps_id'         => $points_settings['ps_id'],
                'ps_activ'      => form_sanitizer($this->ps_activ, 0, 'ps_activ'),
                'ps_pricetype'  => form_sanitizer($this->ps_pricetype, 0, 'ps_pricetype'),
                'ps_unitprice'  => form_sanitizer($this->ps_unitprice, 0, 'ps_unitprice'),
                'ps_default'    => form_sanitizer($this->ps_default, 0, 'ps_default'),
                'ps_dateadd'    => $datead * 86400,
                'ps_day'        => form_sanitizer($this->ps_day, 0, 'ps_day'),
                'ps_autogroup'  => form_sanitizer($this->ps_autogroup, 0, 'ps_autogroup'),
                'ps_page'       => form_sanitizer($this->ps_page, 0, 'ps_page')
            ];

            if (\defender::safe()) {
            	dbquery_insert(DB_POINT_ST, $points_settings, 'update');
            	addNotice('success', $locale['PONT_300']);
            }
        }

        $opts = ['1' => $locale['on'], '0' => $locale['off']];
        $options = [0 => $locale['PONT_137'], 1 => $locale['PONT_138']];
        echo openform("settingsform", "post", FUSION_REQUEST, ['class' => 'spacer-sm']).
            form_select('ps_activ', $locale['PONT_110'], $points_settings['ps_activ'], [
                'options' => $opts,
                'inline'  => TRUE,
                'width'   => '100%',
                'ext_tip' => $locale['PONT_111']
            ]).
            form_select('ps_pricetype', $locale['PONT_135'], $points_settings['ps_pricetype'], [
                'options' => $options,
                'inline' => TRUE
            ]).
            form_text('ps_unitprice', $locale['PONT_136'], $points_settings['ps_unitprice'], [
                'inline'      => TRUE,
                'type'        => 'number',
                'inner_width' => '150px',
                'number_min'  => 1,
                'max_length'  => 4
            ]).
            form_text('ps_default', $locale['PONT_112'], $points_settings['ps_default'], [
                'inline'      => TRUE,
                'type'        => 'number',
                'inner_width' => '150px',
                'number_min'  => 1,
                'max_length'  => 4,
                'ext_tip'     => $locale['PONT_113']
            ]).
            form_text('ps_dateadd', $locale['PONT_114'], ($points_settings['ps_dateadd'] / 86400), [
                'inline'      => TRUE,
                'type'        => 'number',
                'inner_width' => '150px',
                'number_min'  => 1,
                'max_length'  => 4,
                'ext_tip'     => $locale['PONT_115']
            ]).
            form_text('ps_day', $locale['PONT_116'], $points_settings['ps_day'], [
                'inline'      => TRUE,
                'type'        => 'number',
                'inner_width' => '150px',
                'number_min'  => 1,
                'max_length'  => 4,
                'ext_tip'     => $locale['PONT_117']
            ]).
            form_select('ps_autogroup', $locale['PONT_139'], $points_settings['ps_autogroup'], [
                'options' => $opts,
                'inline'  => TRUE,
                'width'   => '100%',
                'ext_tip' => $locale['PONT_140']
            ]).
            form_text('ps_page', $locale['PONT_118'], $points_settings['ps_page'], [
                'inline'      => TRUE,
                'type'        => 'number',
                'inner_width' => '150px',
                'max_length'  => 4
            ]).
            form_button('savesettings', $locale['save'], $locale['save'], ['class' => 'btn-success']).
        closeform();
    }
}
