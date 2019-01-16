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
        'mod'       => 1, //1 = add point, 2 = remov point
        'point'     => 0,
        'messages'  => '',
        'addtime'   => '',
        'pricetype' => 0  //0 = pricetype , 1 = more price
    ];

    protected $default_ban = [
        'ban_mod'   => 1, //1 = add ban, 2 = remov bann
        'ban_start' => 0,
        'ban_stop'  => 0,
        'ban_text'  => ''
    ];

    public function globinf() {
        //$this->diary_filter = 0;
        //$this->save = (string)filter_input(INPUT_POST, 'savesettings', FILTER_DEFAULT);
        $this->diary_filter = filter_input(INPUT_POST, 'diary_filter', FILTER_DEFAULT);
        $this->deleteall = filter_input(INPUT_GET, 'deleteall', FILTER_DEFAULT);
        $this->del = filter_input(INPUT_GET, 'del', FILTER_DEFAULT);
        $this->np = filter_input(INPUT_GET, 'np', FILTER_DEFAULT);
        $this->logid = filter_input(INPUT_GET, 'log_id', FILTER_VALIDATE_INT);
        $this->rowstart = filter_input(INPUT_GET, 'rowstart', FILTER_VALIDATE_INT);
    }

    public static function CurrentSetup() {

        $result = dbquery("SELECT *
            FROM ".DB_POINT_ST."
            ".(multilang_table("PSP") ? " WHERE ps_language=:language" : ''), [':language' => LANGUAGE]);

        $settings = dbarray($result);

        return $settings;
    }
}
