<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: andromeda_patch.php
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
require_once __DIR__.'/../../maincore.php';
require_once THEMES.'templates/header.php';

echo "<h1>Points System</h1>\n";
$changed = FALSE;

if (!db_exists(DB_POINT_GROUP)) {
    // Create tables
    dbquery("CREATE TABLE `".DB_POINT_GROUP."` (
    pg_id           INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
    pg_group_id     INT(11)               NOT NULL DEFAULT '0',
    pg_group_points BIGINT(11)            NOT NULL DEFAULT '0',
    PRIMARY KEY (pg_id)
    ) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci");
    $changed = TRUE;
}

$p_check = [
    // Option to use keywords in downloads
    'point_group'   => " ADD point_group TEXT AFTER point_increase"
];

foreach ($p_check as $key => $value) {
    if (!column_exists(DB_POINT, $key, FALSE)) {
        dbquery("ALTER TABLE ".DB_POINT.$value);
        $changed = TRUE;
    }
}

$array_check = [
    // Option to use keywords in downloads
    'ps_autogroup'   => " ADD ps_autogroup ENUM('0','1') DEFAULT '0' AFTER ps_page"
];

foreach ($array_check as $key => $value) {
    if (!column_exists(DB_POINT_ST, $key, FALSE)) {
        dbquery("ALTER TABLE ".DB_POINT_ST.$value);
        $changed = TRUE;
    }
}

if ($changed === TRUE) {
    addNotice("success", "You have successfully upgraded to latest Points System");
}

require_once THEMES.'templates/footer.php';
