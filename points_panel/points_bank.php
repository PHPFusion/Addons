<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: points_panel/points_bank.php
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
require_once file_exists('maincore.php') ? 'maincore.php' : __DIR__."/../../maincore.php";

if (!defined('POINTS_PANEL_EXIST')) {
    redirect(BASEDIR."error.php?code=404");
}

require_once THEMES."templates/header.php";
use \PHPFusion\BreadCrumbs;

class PointsBankSystem extends PointsModel {
    private $locale = [];
    public $settings = [];

    public function __construct() {
        $this->settings = self::CurrentSetup();
        $this->locale = fusion_get_locale('', POINT_LOCALE);
    }

    public function DisplayBank() {
        set_title($this->locale['PSP_M08']);

        opentable($this->locale['PSP_M08']);

        if (iMEMBER && !empty($this->settings['ps_bank'])) {
            $section = filter_input(INPUT_GET, 'section', FILTER_DEFAULT);
            $allowed_section = ['bank', 'deposit', 'loan', 'borrowers', 'depositors'];
            $tab_active = isset($section) && in_array($section, $allowed_section) ? $section : 'bank';

            $master_title['title'][] = $this->locale['PSP_B00'];
            $master_title['id'][] = 'bank';
            $master_title['icon'][] = '';

            if (iMEMBER && !empty($this->settings['ps_interest'])) {
                $master_title['title'][] = $this->locale['PSP_B01'];
                $master_title['id'][] = 'deposit';
                $master_title['icon'][] = '';
            }
            if (iMEMBER && !empty($this->settings['ps_loan'])) {
                $master_title['title'][] = $this->locale['PSP_B02'];
                $master_title['id'][] = 'loan';
                $master_title['icon'][] = '';
            }

            if (iADMIN) {
                $master_title['title'][] = $this->locale['PSP_B03'];
                $master_title['id'][] = 'depositors';
                $master_title['icon'][] = '';

                $master_title['title'][] = $this->locale['PSP_B04'];
                $master_title['id'][] = 'borrowers';
                $master_title['icon'][] = '';
            }

            echo opentab($master_title, $tab_active, 'bank_system', TRUE, '', 'section', ['points_user', 'rowstart', 'log_pmod']);
            switch ($section) {
	            case "deposit":
                    PointsBankDeposit::getInstance()->displayDeposit();
		            break;
	            case "loan":
                    PointsBankDeposit::getInstance()->displayLoan();
		            break;
	            case "borrowers":
                    PointsBankDeposit::getInstance()->BankLoan();
		            break;
	            case "depositors":
                    PointsBankDeposit::getInstance()->BankDeposit();
		            break;
	            default:
                    PointsBankDeposit::getInstance()->displayInfo();
            }
            echo closetab();
        } else {
        	echo "<div class='text-center well'>".$this->locale['PSP_E00']."</div>\n";
        }
        closetable();
    }
}
$pbank = new PointsBankSystem();
$pbank->DisplayBank();

require_once THEMES."templates/footer.php";
