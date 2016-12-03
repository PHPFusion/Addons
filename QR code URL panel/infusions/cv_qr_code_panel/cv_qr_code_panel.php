<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: cv_qr_code_panel.php
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

require_once __DIR__ . "/includes/qrlib.php";

if (file_exists(INFUSIONS . "cv_qr_code_panel/locale/" . $settings['locale'] . ".php")) {
	include INFUSIONS . "cv_qr_code_panel/locale/" . $settings['locale'] . ".php";
} else {
	include INFUSIONS . "cv_qr_code_panel/locale/English.php";
}

require_once INCLUDES . "infusions_include.php";
$opts = get_settings("cv_qr_code_panel");

openside($locale['cvqr_title']);

$target = base64_encode($settings['siteurl'] . substr(FUSION_REQUEST, 1));

echo "<img src='" . INFUSIONS . "cv_qr_code_panel/outimg.php?target=" . $target . "' />";
closeside();
