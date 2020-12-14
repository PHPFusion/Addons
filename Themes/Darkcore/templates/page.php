<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: page.php
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
defined('IN_FUSION') || exit;

use PHPFusion\Panels;

function display_page($info) {
    Panels::getInstance(TRUE)->hide_panel('RIGHT');
    Panels::getInstance(TRUE)->hide_panel('LEFT');
    Panels::getInstance(TRUE)->hide_panel('AU_CENTER');
    Panels::getInstance(TRUE)->hide_panel('U_CENTER');
    Panels::getInstance(TRUE)->hide_panel('L_CENTER');
    Panels::getInstance(TRUE)->hide_panel('BL_CENTER');

    echo render_breadcrumbs();

    opentable($info['title']);
    echo "<!--cp_idx-->\n";
    if (!empty($info['error'])) {
        echo "<div class='well text-center'>\n";
        echo $info['error'];
        echo "</div>\n";
    } else {
        echo $info['body'];
    }
    closetable();
}

function display_page_content($info) {
    echo "<!--cp_idx-->\n";
    if (!empty($info['error'])) {
        echo "<div class='well text-center'>\n";
        echo $info['error'];
        echo "</div>\n";
    } else {
        echo "<div class='well'>\n";
        echo $info['body'][$info['rowstart']];
        echo $info['pagenav'];
        echo "</div>\n";
    }
}