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
defined('IN_FUSION') || exit;

require_once(INFUSIONS.'points_panel/user_fields/user_send_points_include.php');

if ($profile_method == "input") {
    $user_fields = '';
    if (defined('ADMIN_PANEL')) {
        $user_fields = "<div class='well m-t-5 text-center'>".$locale['uf_sendpoints']."</div>";
    }
} elseif ($profile_method == "display") {
    $lookup = filter_input(INPUT_GET, 'lookup', FILTER_VALIDATE_INT);
    $send_point = filter_input(INPUT_POST, 'send_point', FILTER_DEFAULT);
    $userid = fusion_get_userdata('user_id');
    if (iMEMBER && !empty($send_point) && $userid != $lookup) {
        $point_point = ;
        $error = '';
        $sendpoints = form_sanitizer(filter_input(INPUT_POST, 'point_point', FILTER_VALIDATE_INT), 0, 'point_point');
        $frompointinf = \PHPFusion\Points\UserPoint::getInstance()->PointInfo($userid, $sendpoints); //k�ld�
        $error .= \PHPFusion\Points\UserPoint::getInstance()->PointInfo($userid, $sendpoints) < 0 ? $locale['uf_sendpoints_002'] : '';
        $error .= empty(\PHPFusion\Points\UserPoint::getInstance()->PointInfo($lookup, 0)) ? $locale['uf_sendpoints_003'] : '';//kapo
        $error .= (fusion_get_userdata('user_ip') == $user_data['user_ip']) ? $locale['uf_sendpoints_004'] : '';
        $error .= $sendpoints < 1 ? $locale['uf_sendpoints_005'] : '';
        if (\defender::safe() && $error == '') {
        	\PHPFusion\Points\UserPoint::getInstance()->setPoint($userid, ["mod" => 2, "point" => $sendpoints, "messages" => $locale['uf_sendpoints_006']]);
        	\PHPFusion\Points\UserPoint::getInstance()->setPoint($lookup, ["mod" => 1, "point" => $sendpoints, "messages" => $locale['uf_sendpoints_007']]);
        	$messages = sprintf($locale['uf_sendpoints_009'], fusion_get_userdata('user_name'));
	    	send_pm($lookup, $userid, $locale['uf_sendpoints_008'], $messages, $smileys = "y");
	    	addNotice('success', $locale['uf_sendpoints_010']);
        } else {
	    	addNotice('danger', $error);
        }
    }
    if (iMEMBER && $userid != $lookup) {
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
}
