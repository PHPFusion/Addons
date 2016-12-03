<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: outimg.php
| Author: Chubatyj Vitalij (Rizado)
| web: http://chubatyj.ru/
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/

require_once "../../maincore.php";
require_once __DIR__ . "/includes/qrlib.php";
require_once INCLUDES . "infusions_include.php";

$target = isset($_GET['target']) ? base64_decode($_GET['target']) : $settings['siteurl'];

$opts = get_settings("cv_qr_code_panel");

QRcode::png($target, false, $opts['corr_level'], $opts['square_size'], $opts['margin_size'], false, hexdec(substr($opts['bg_color'], 1)), hexdec(substr($opts['main_color'], 1)));