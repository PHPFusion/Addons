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
    private $ps_dateadd;
    private $ps_activ;
    private $ps_default;
    private $ps_day;
    private $ps_page;
    private $ps_pricetype;
    private $ps_unitprice;
    private $ps_autogroup;

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
