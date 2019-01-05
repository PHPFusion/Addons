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

        $allowed_section = ['diary', 'settings', 'bann'];
        $_GET['section'] = isset($_GET['section']) && in_array($_GET['section'], $allowed_section) ? $_GET['section'] : 'settings';

        $tab['title'][] = $this->locale['PONT_101'];
        $tab['id'][]    = 'settings';
        $tab['icon'][]  = 'fa fa-fw fa-cogs';
        $tab['title'][] = $this->locale['PONT_102'];
        $tab['id'][]    = 'diary';
        $tab['icon'][]  = 'fa fa-fw fa-book';
        $tab['title'][] = $this->locale['PONT_103'];
        $tab['id'][]    = 'bann';
        $tab['icon'][]  = 'fa fa-fw fa-book';

        echo opentab($tab, $_GET['section'], "points_admin", TRUE, "", "section");
        switch ($_GET['section']) {
            case "diary":
                //PHPFusion\Points\PointsDiaryAdmin::getInstance()->displayDiaryAdmin();
                break;
            case "bann":
                PHPFusion\Points\PointBanAdmin::getInstance()->CurrentList();
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

//require_once ARTICLE_CLASS."autoloader.php";
//PHPFusion\Articles\ArticlesServer::ArticlesAdmin()->display_admin();
require_once THEMES."templates/footer.php";
