<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: user_send_points_include.php
| Author: karrak
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at http://www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined("IN_FUSION")) {
    die("Access Denied");
}

if ($profile_method == "input") {
    $user_fields = '';
    if (defined('ADMIN_PANEL')) {
        $user_fields = "<div class='well m-t-5 text-center'>".$locale['uf_sendpoints']."</div>";
    }
} elseif ($profile_method == "display") {

    if (iMEMBER && isset($_POST['send_point']) && fusion_get_userdata('user_id') != $_GET['lookup']) {
        $error = '';
        $sendpoints = form_sanitizer($_POST['point_point'], 0, 'point_point');
        $frompointinf = \PHPFusion\Points\UserPoint::getInstance()->PointInfo(fusion_get_userdata('user_id'), $sendpoints); //küldõ
        $error .= \PHPFusion\Points\UserPoint::getInstance()->PointInfo(fusion_get_userdata('user_id'), $sendpoints) < 0 ? $locale['uf_sendpoints_002'] : '';
        $error .= empty(\PHPFusion\Points\UserPoint::getInstance()->PointInfo($_GET['lookup'], '')) ? $locale['uf_sendpoints_003'] : '';//kapo
        $error .= (fusion_get_userdata('user_ip') == $user_data['user_ip']) ? $locale['uf_sendpoints_004'] : '';
        $error .= $sendpoints < 1 ? $locale['uf_sendpoints_005'] : '';
        if (\defender::safe() && $error == '') {
        	\PHPFusion\Points\UserPoint::getInstance()->setPoint(fusion_get_userdata('user_id'), ["mod" => 2, "point" => $sendpoints, "messages" => $locale['uf_sendpoints_006']]);
        	\PHPFusion\Points\UserPoint::getInstance()->setPoint($_GET['lookup'], ["mod" => 1, "point" => $sendpoints, "messages" => $locale['uf_sendpoints_007']]);
        	$messages = sprintf($locale['uf_sendpoints_009'], fusion_get_userdata('user_name'));
	    	send_pm($_GET['lookup'], fusion_get_userdata('user_id'), $locale['uf_sendpoints_008'], $messages, $smileys = "y");
	    	addNotice('success', $locale['uf_sendpoints_010']);
        } else {
	    	addNotice('danger', $error);
        }
    }
    if (iMEMBER && fusion_get_userdata('user_id') != $_GET['lookup']) {
        $action_url = FUSION_SELF.(FUSION_QUERY ? "?".FUSION_QUERY : "");
        $sendpoints = openform('sendpoint_form', 'post', $action_url).
            form_text('point_point', '', 0, [
                'required'    => TRUE,
                'type'        => 'number',
                'max_length'  => 5,
                'number_min'  => 1,
                'inner_width' => '100px',
                'inline'      => TRUE
            ]).
            form_button('send_point', $locale['uf_sendpoints_001'], 'sendpoint').
            closeform();
        $user_fields = ['title' => $locale['uf_sendpoints_001'], 'value' => $sendpoints];
    }
} elseif ($profile_method == "validate_insert") {
	//Nothing here
}