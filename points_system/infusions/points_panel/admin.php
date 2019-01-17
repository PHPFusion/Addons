<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: admin.php
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
require_once "../../maincore.php";
require_once THEMES."templates/admin_header.php";
pageAccess('PSP');

use \PHPFusion\BreadCrumbs;

class PointsAdmin {
    private $locale = [];

    public function __construct() {
        $this->locale = fusion_get_locale('', POINT_LOCALE);
    }

    public function DisplayAdmin() {

        BreadCrumbs::getInstance()->addBreadCrumb(['link' => INFUSIONS.'points_panel/admin.php'.fusion_get_aidlink(), 'title' => $this->locale['PONT_100']]);
        add_to_title($this->locale['PONT_100']);
        opentable($this->locale['PONT_100']);

    	$section = filter_input(INPUT_GET, 'section', FILTER_DEFAULT);
        $allowed_section = ['diary', 'settings', 'pointst', 'bann', 'autogroup'];
        $section = isset($section) && in_array($section, $allowed_section) ? $section : 'settings';

        $tab['title'][] = $this->locale['PONT_101'];
        $tab['id'][]    = 'settings';
        $tab['icon'][]  = 'fa fa-fw fa-cogs';
        //if ($points_settings['ps_autogroup']) {
            $tab['title'][] = $this->locale['PONT_105'];
            $tab['id'][]    = 'autogroup';
            $tab['icon'][]  = 'fa fa-fw fa-cogs';
        //}
        $tab['title'][] = $this->locale['PONT_102'];
        $tab['id'][]    = 'diary';
        $tab['icon'][]  = 'fa fa-fw fa-book';
        $tab['title'][] = $this->locale['PONT_104'];
        $tab['id'][]    = 'pointst';
        $tab['icon'][]  = 'fa fa-fw fa-plus-circle';
        $tab['title'][] = $this->locale['PONT_103'];
        $tab['id'][]    = 'bann';
        $tab['icon'][]  = 'fa fa-fw fa-ban';

        echo opentab($tab, $section, 'points_admin', TRUE, '', 'section', ['points_user', 'rowstart', 'log_pmod']);
        switch ($section) {
            case "diary":
                PHPFusion\Points\PointsDiaryAdmin::getInstance()->displayDiaryAdmin();
                break;
            case "autogroup":
                PHPFusion\Points\PointsAutogroupAdmin::getInstance()->displayAdmin();
                break;
            case "pointst":
                PHPFusion\Points\PointsPointsAdmin::getInstance()->displayPointsAdmin();
                break;
            case "bann":
                PHPFusion\Points\PointsBanAdmin::getInstance()->CurrentList();
                break;
            default:
                PHPFusion\Points\PointsSettingsAdmin::getInstance()->displayPointsAdmin();
        }
        echo closetab();
        closetable();
    }
}
$vid = new PointsAdmin();
$vid->DisplayAdmin();

require_once THEMES."templates/footer.php";
