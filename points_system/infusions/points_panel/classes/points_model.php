<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: points_panel/classes/points_model.php
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

class PointsModel {

    protected $default_options = [
        'mod'      => 1, //1 = add point, 2 = remov point
        'point'    => 0,
        'messages' => '',
        'addtime'  => ''
    ];

	public static function CurrentSetup() {

        $result = dbquery("SELECT *
            FROM ".DB_POINT_ST."
            ".(multilang_table("PSP") ? " WHERE ps_language=:language" : ''), [':language' => LANGUAGE]);

        $settings = dbarray($result);

        return $settings;
	}
}