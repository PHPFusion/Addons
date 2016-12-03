<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: admin.php
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
pageAccess('CVQR');
require_once THEMES."templates/admin_header.php";
require_once INCLUDES."infusions_include.php";

if (file_exists(INFUSIONS . "cv_qr_code_panel/locale/".$settings['locale'].".php")) {
	include INFUSIONS . "cv_qr_code_panel/locale/".$settings['locale'].".php";
} else {
	INFUSIONS . "cv_qr_code_panel/locale/English.php";
}

if (isset($_POST['savesettings'])) {
	$inputData = array(
		"main_color" => form_sanitizer($_POST['main_color'], "#601117", "main_color"),
		"bg_color" => form_sanitizer($_POST['bg_color'], "#ffffff", "bg_color"),
		"square_size" => form_sanitizer($_POST['square_size'], 4, "square_size"),
		"margin_size" => form_sanitizer($_POST['margin_size'], 2, "margin_size"),
		"corr_level" => form_sanitizer($_POST['corr_level'], 2, "corr_level")
	);
	if (defender::safe()) {
		foreach ($inputData as $settings_name => $settings_value) {
			$data = array(
				"settings_name" => $settings_name,
				"settings_value" => $settings_value
			);
			dbquery_insert(DB_SETTINGS_INF, $data, "update", array("primary_key" => "settings_name"));
		}
		addNotice('success', $locale['cvqr_1101']);
		redirect(FUSION_REQUEST);
	} else {
		addNotice('danger', $locale['cvqr_1102']);
	}
}

$cvqr_settings = get_settings("cv_qr_code_panel");

opentable($locale['cvqr_1000']);
echo "<div class='well'>".$locale['cvqr_1001']."</div>";

echo openform('settingsform', 'post', FUSION_SELF.$aidlink, array('max_tokens' => 1));

echo "<div class='row'>\n";
echo "<div class='col-xs-12 col-sm-12 col-md-12'>\n";

openside($locale['cvqr_1002']);
echo "<div class='pull-right m-b-10'><span class='small2'>".$locale['663']."</span></div>\n";
echo form_colorpicker('main_color', $locale['cvqr_1011'], $cvqr_settings['main_color'], array('required' => true));
echo form_colorpicker('bg_color', $locale['cvqr_1012'], $cvqr_settings['bg_color'], array('required' => true));
closeside();

openside($locale['cvqr_1003']);
echo form_text('square_size', $locale['cvqr_1016'], $cvqr_settings['square_size'], array('type' => 'number', 'number_min' => 1, 'number_max' => 25, 'required' => true));
echo form_text('margin_size', $locale['cvqr_1017'], $cvqr_settings['margin_size'], array('type' => 'number', 'number_min' => 0, 'number_max' => 12, 'required' => true));
closeside();

openside($locale['cvqr_1004']);
$choice_arr = array(
	0 => $locale['cvqr_1022'],
	1 => $locale['cvqr_1023'],
	2 => $locale['cvqr_1024'],
	3 => $locale['cvqr_1025']
);
echo form_select('corr_level', $locale['cvqr_1021'], $cvqr_settings['corr_level'], array('options' => $choice_arr, 'inline' => TRUE, 'required' => true));
closeside();

echo "</div>\n</div>";
echo form_button('savesettings', $locale['cvqr_1100'], $locale['cvqr_1100'], array('class' => 'btn-success'));
echo closeform();
closetable();
require_once THEMES."templates/footer.php";