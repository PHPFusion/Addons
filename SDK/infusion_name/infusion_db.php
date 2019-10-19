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
    if (file_exists(INFUSIONS.'infusion_name/locale/'.LOCALESET.'.php')) {
        define('XXX_LOCALE', INFUSIONS.'infusion_name/locale/'.LOCALESET.'.php');
    } else {
        define('XXX_LOCALE', INFUSIONS.'infusion_name/locale/English.php');
    }
}

if (!defined('DB_INFUSION_TABLE')) {
    define('DB_INFUSION_TABLE', DB_PREFIX.'infusion_table');
}

// Admin Settings
\PHPFusion\Admins::getInstance()->setAdminPageIcons('XXX', '<i class="admin-ico fa fa-fw fa-play"></i>'); // FontAwesomwe icon
// \PHPFusion\Admins::getInstance()->setCommentType('XXX', fusion_get_locale('xxx_title', XXX_LOCALE)); // Comments
// \PHPFusion\Admins::getInstance()->setLinkType('XXX', fusion_get_settings('siteurl').'infusions/infusion_name/infusion_name.php?item_id=%s'); // Ratings


// Submissions
/*$inf_settings = get_settings('infusion_name');
if (!empty($inf_settings['infusion_allow_submission']) && $inf_settings['infusion_allow_submission']) {
    \PHPFusion\Admins::getInstance()->setSubmitData('x', [
        'infusion_name' => 'infusion_name',
        'link'          => INFUSIONS.'infusion_name/infusion_submit.php',
        'submit_link'   => 'submit.php?stype=x',
        'submit_locale' => fusion_get_locale('xxx_title', XXX_LOCALE),
        'title'         => fusion_get_locale('Submit', XXX_LOCALE),
        'admin_link'    => INFUSIONS.'infusion_name/admin.php'.fusion_get_aidlink().'&amp;section=submissions&amp;submit_id=%s'
    ]);
}*/


// Shows CHMOD in Admin Dashboard > System Admin > PHP Info: Folder Permissions
/*\PHPFusion\Admins::getInstance()->setFolderPermissions('infusion_name', [
    'infusions/infusion_name/images/'      => TRUE,
    'infusions/infusion_name/submissions/' => TRUE
]);*/
