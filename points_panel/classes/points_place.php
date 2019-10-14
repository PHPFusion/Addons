<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: classes/points_place.php
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

class PointsPlace extends PointsModel {
    private static $instance = NULL;
    public $settings = [];
    public $place_filter = '';

    public function __construct() {
        include_once POINT_CLASS."templates.php";
        $this->settings = self::CurrentSetup();
        self::$locale = fusion_get_locale("", POINT_LOCALE);
        $this->place_filter = filter_input(INPUT_POST, 'place_filter', FILTER_VALIDATE_INT);
        $this->place_filter = empty($this->place_filter) ? filter_input(INPUT_GET, 'place_filter', FILTER_VALIDATE_INT) : $this->place_filter;
    }

    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    private function checkPlaceFilter() {
        $catfilter = "p.point_point DESC";

        if (!empty($this->place_filter)) {
            switch ($this->place_filter) {
                case 1:
                    $catfilter = "pu.user_name ASC";
                    break;
                case 2:
                    $catfilter = "p.point_point DESC";
                    break;
                default:
                    $catfilter = "p.point_point DESC";
            }
        }
        return (string)$catfilter;
    }

    private function Placefilter() {
        $placeinf = [0 => self::$locale['PSP_PL0'], 1 => self::$locale['PSP_PL1'], 2 => self::$locale['PSP_PL2']];
        $info = openform('place_form', 'post', FUSION_SELF).
        form_select('place_filter', '', $this->place_filter, [
            'allowclear' => TRUE,
            'options'    => $placeinf,
            'onchange'   => 'document.place_form.submit()'
        ]).
        closeform();

        return $info;
    }

    public function CurrentList() {

        set_title(self::$locale['PSP_P00']);
        $rowstart = filter_input(INPUT_GET, 'rowstart', FILTER_VALIDATE_INT);
        $max_rows = dbcount("(point_id)", DB_POINT, (multilang_table("PSP") ? "point_language='".LANGUAGE."'" : ''));
        $rowstart = (!empty($rowstart) && isnum($rowstart) && $rowstart <= $max_rows) ? $rowstart : 0;

        $bind = [
            ':rowstart' => $rowstart,
            ':limit'    => $this->settings['ps_page']
        ];

        $result = dbquery("SELECT p.*, pu.user_id, pu.user_name, pu.user_status, pu.user_avatar, pu.user_joined, pu.user_level
            FROM ".DB_POINT." AS p
            LEFT JOIN ".DB_USERS." AS pu ON pu.user_id = p.point_user
            ".(multilang_table("PSP") ? "WHERE p.point_language = '".LANGUAGE."'" : "")."
            ORDER BY ".self::checkPlaceFilter()."
            LIMIT :rowstart, :limit", $bind);
        $inf = [];
        while ($data = dbarray($result)){
            $inf[] = [
                'point_id'   => $data['point_id'],
                'point_user' => $data['point_user'],
                'avatar'     => display_avatar($data, '50px', '', TRUE, 'img-rounded'),
                'profile'    => profile_link($data['user_id'], $data['user_name'], $data['user_status']),
                'point'      => number_format($data['point_point'])
            ];
	    }

        $info = [
            'opentable'   => "<i class='fa fa-pie-chart fa-lg m-r-10'></i>".self::$locale['PSP_P00'],
		    'placefilter' => self::Placefilter(),
            'message'     => sprintf(self::$locale['PSP_P01'], $this->settings['ps_page']),
            'max_row'     => $max_rows,
            'stat_rows'   => dbrows($result),
            'pagenav'     => makepagenav($rowstart, $this->settings['ps_page'], $max_rows, 3, POINT_CLASS."points_place.php?place_filter=".$this->place_filter."&"),
            'place'       => $rowstart,
            'item'        => $inf
        ];

        PlaceItem($info);
    }
}