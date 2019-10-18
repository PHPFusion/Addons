<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: classes/points_panel.php
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

class PointsPanel extends PointsModel {
    private static $instance = NULL;
    private $points = [];
    public $settings = [];

    public function __construct() {
        include_once POINT_CLASS."templates.php";
        $this->settings = self::CurrentSetup();
        self::$locale = fusion_get_locale("", POINT_LOCALE);
        $this->points = self::GetCurrentUser(fusion_get_userdata('user_id'));
    }

    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function DisplayPoint() {
        $multiplier = UserPoint::getInstance()->pointHollyday();
        $diary = [
            'where' => 'log_user_id = :userid',
            'order' => ' ORDER BY log_date DESC',
            'limit' => ' LIMIT 0,1',
            'bind' => [
                ':userid' => fusion_get_userdata('user_id')
            ]
        ];

        $message = UserPoint::getInstance()->PontDiary($diary);

        $info = [
            'opentable' => "<i class='fa fa-star-o fa-lg m-r-10'></i>".self::$locale['PSP_M10'],
            'id'        => $this->points['point_user'],
            'activ'     => $this->settings['ps_activ'],
            'message'   => empty($this->settings['ps_activ']) ? self::$locale['PSP_009'] : '',
            'pricetype' => empty($this->settings['ps_pricetype']) ? sprintf(self::$locale['PSP_010'], ($this->settings['ps_unitprice'])) : '',
            'holiday'   => ($this->settings['ps_holiday'] > 1 && $multiplier > 1) ? sprintf(self::$locale['PSP_032'], $this->settings['ps_holiday']) : ''
        ];

    	$info['item'] = [
    	    'UserPont'  => number_format($this->points['point_point']),
    	    'UserHely'  => number_format(UserPoint::getInstance()->PointPlace($this->points['point_user'])),
    	    'increase'  => sprintf(self::$locale['PSP_005'], showdate("%Y.%m.%d - %H:%M", $this->points['point_increase'])),
    	    'udate'     => showdate("%d-%H:%M", $message['log_date']),
    	    'upont'     => "<abbr title='".$message['log_descript']."' class='initialism'>".number_format($message['log_point'])."</abbr>\n",
    	    'umod'      => "<span style='color:".($message['log_pmod'] == 1 ? '#5CB85C' : '#FF0000')."'><i class='".($message['log_pmod'] == 1 ? "fa fa-plus-square" : "fa fa-minus-square")."'></i></span>\n",
    	    'listmenu'  => UserPoint::getInstance()->pointListMenu()
    	];

        pointPanelItem($info);
    }
}