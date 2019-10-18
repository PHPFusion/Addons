<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion_db.php
| Author: Your Name
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
defined('IN_FUSION') || exit;

if (!defined('XXX_LOCALE')) {
    if (file_exists(INFUSIONS.'infusionname/locale/'.LOCALESET.'.php')) {
        define('XXX_LOCALE', INFUSIONS.'infusionname/locale/'.LOCALESET.'.php');
    } else {
        define('XXX_LOCALE', INFUSIONS.'infusionname/locale/English.php');
    }
}

if (!defined('DB_INFUSION_TABLE')) {
    define('DB_INFUSION_TABLE', DB_PREFIX.'infusion_table');
}

// Admin Settings
\PHPFusion\Admins::getInstance()->setAdminPageIcons('XXX', '<i class="admin-ico fa fa-fw fa-play"></i>'); // FontAwesomwe icon
