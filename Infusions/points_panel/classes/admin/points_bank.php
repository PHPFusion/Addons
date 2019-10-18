<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: classes/admin/points_bank.php
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

class PointsBankAdmin extends PointsModel {
    private static $instance = NULL;
    public $settings = [];

    public function __construct() {
        $this->settings = self::CurrentSetup();
        self::$locale = fusion_get_locale("", POINT_LOCALE);
    }

    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new static();
        }
       return self::$instance;
    }

    public function displayAdmin() {
        self::BankSettings();
    }

    private function BankSettings() {
        $savesettings = filter_input(INPUT_POST, 'savesettings', FILTER_DEFAULT);
        if (!empty($savesettings)) {

            $inputdata = [
                'ps_id'               => form_sanitizer(filter_input(INPUT_POST, 'ps_id', FILTER_DEFAULT), '', 'ps_id'),
                'ps_bank'             => form_sanitizer(filter_input(INPUT_POST, 'ps_bank', FILTER_DEFAULT), 0, 'ps_bank'),
                'ps_loan'             => form_sanitizer(filter_input(INPUT_POST, 'ps_loan', FILTER_DEFAULT), 0, 'ps_loan'),
                'ps_loan_max'         => form_sanitizer(filter_input(INPUT_POST, 'ps_loan_max', FILTER_DEFAULT), 0, 'ps_loan_max'),
                'ps_loan_day'         => form_sanitizer(filter_input(INPUT_POST, 'ps_loan_day', FILTER_DEFAULT), 0, 'ps_loan_day'),
                'ps_loan_interest'    => form_sanitizer(filter_input(INPUT_POST, 'ps_loan_interest', FILTER_DEFAULT), 0, 'ps_loan_interest'),
                'ps_interest'         => form_sanitizer(filter_input(INPUT_POST, 'ps_interest', FILTER_DEFAULT), 0, 'ps_interest'),
                'ps_deposit_max'      => form_sanitizer(filter_input(INPUT_POST, 'ps_deposit_max', FILTER_DEFAULT), 0, 'ps_deposit_max'),
                'ps_deposit_day'      => form_sanitizer(filter_input(INPUT_POST, 'ps_deposit_day', FILTER_DEFAULT), 0, 'ps_deposit_day'),
                'ps_deposit_interest' => form_sanitizer(filter_input(INPUT_POST, 'ps_deposit_interest', FILTER_DEFAULT), 0, 'ps_deposit_interest')
            ];

            if (\defender::safe()) {
            	dbquery_insert(DB_POINT_ST, $inputdata, 'update');
            	addNotice('success', self::$locale['PSP_E14']);
            	redirect(FUSION_REQUEST);
            }
        }

        $opts = ['1' => self::$locale['on'], '0' => self::$locale['off']];
    	opentable(self::$locale['PSP_M08']);
        echo openform('loanform', 'post', FUSION_REQUEST, ['class' => 'm-t-20']).
        form_hidden('ps_id', '', $this->settings['ps_id']).
        form_select('ps_bank', self::$locale['PSP_B50'], $this->settings['ps_bank'], [
            'options' => $opts,
            'inline'  => TRUE,
            'width'   => '100%'
        ]).
        form_select('ps_loan', self::$locale['PSP_B51'], $this->settings['ps_loan'], [
            'options' => $opts,
            'inline'  => TRUE,
            'width'   => '100%'
        ]).
        form_text('ps_loan_max', self::$locale['PSP_B52'], $this->settings['ps_loan_max'], [
            'inline'      => TRUE,
            'type'        => 'number',
            'inner_width' => '150px',
            'number_min'  => 1,
            'max_length'  => 4
        ]).
        form_text('ps_loan_day', self::$locale['PSP_B53'], $this->settings['ps_loan_day'], [
            'inline'      => TRUE,
            'type'        => 'number',
            'inner_width' => '150px',
            'number_min'  => 1,
            'max_length'  => 4
        ]).
        form_text('ps_loan_interest', self::$locale['PSP_B54'], $this->settings['ps_loan_interest'], [
            'inline'      => TRUE,
            'type'        => 'number',
            'inner_width' => '150px',
            'number_min'  => 1,
            'max_length'  => 4
        ]).
        form_select('ps_interest', self::$locale['PSP_B55'], $this->settings['ps_interest'], [
            'options' => $opts,
            'inline'  => TRUE,
            'width'   => '100%'
        ]).
        form_text('ps_deposit_max', self::$locale['PSP_B56'], $this->settings['ps_deposit_max'], [
            'inline'      => TRUE,
            'type'        => 'number',
            'inner_width' => '150px',
            'number_min'  => 1,
            'max_length'  => 4
        ]).
        form_text('ps_deposit_day', self::$locale['PSP_B57'], $this->settings['ps_deposit_day'], [
            'inline'      => TRUE,
            'type'        => 'number',
            'inner_width' => '150px',
            'number_min'  => 1,
            'max_length'  => 4
        ]).
        form_text('ps_deposit_interest', self::$locale['PSP_B58'], $this->settings['ps_deposit_interest'], [
            'inline'      => TRUE,
            'type'        => 'number',
            'inner_width' => '150px',
            'number_min'  => 1,
            'max_length'  => 4
        ]).
        form_button('savesettings', self::$locale['save'], self::$locale['save'], ['class' => 'btn-success']).
        closeform();
    	closetable();
    }
}