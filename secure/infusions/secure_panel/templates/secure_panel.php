<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: templates/secure_panel.php
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
if (!defined("IN_FUSION")) {
    die("Access Denied");
}

if (!function_exists('render_secure_panel')) {
    function render_secure_panel(array $info = array()) {
        echo opentable('{%tablename%}');
            echo '{%intro%}';
            echo '<div class="text-center well">{%prmessages%}</div>';
            echo '{%open_form%}';
            echo '{%prmessages_1%}';
            echo '{%seccode%}';
            echo '{%mail_name%}';
            echo '{%question%}';
            echo '<div class="text-center">{%send_button%}</div>';
            echo '{%close_form%}';
        echo closetable();
    }
}
