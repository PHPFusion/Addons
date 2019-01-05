<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: autoloader.php
| Author: PHP-Fusion Development Team
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
spl_autoload_register(function ($className) {

    $autoload_register_paths = [
        'PHPFusion\\Points\\UserPoint'           => POINT_CLASS."classes/points.php",
        'PHPFusion\\Points\\PointsSettingsAdmin' => POINT_CLASS."classes/admin/points_settings.php",
        'PHPFusion\\Points\\PointBanAdmin'       => POINT_CLASS."classes/admin/points_ban.php",
        'PHPFusion\\Points\\PointsModel'         => POINT_CLASS."classes/points_model.php",
        'PHPFusion\\Points\\PointDiary'          => POINT_CLASS."classes/diary.php",
    ];

    if (isset($autoload_register_paths[$className])) {
        $fullPath = $autoload_register_paths[$className];
        if (is_file($fullPath)) {
            require $fullPath;
        }
    }
});