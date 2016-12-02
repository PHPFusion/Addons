<?php
/**
 * Created by PhpStorm.
 * User: rizado
 * Date: 23.11.16
 * Time: 8:10 PM
 */

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
