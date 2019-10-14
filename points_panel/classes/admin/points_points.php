<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: classes/admin/points_points.php
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
namespace PHPFusion\Points;

class PointsPointsAdmin extends PointsModel {
    private static $instance = NULL;
    public $settings = [];

    public function __construct() {
        $this->settings = self::CurrentSetup();
        self::$locale = fusion_get_locale("", POINT_LOCALE);
    }

    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new static();
        }
       return self::$instance;
    }

	public function displayPointsAdmin() {
        set_title(self::$locale['PSP_M10']);
        self::PointsForm();
        self::PointsDisplay();
	}

    private function PointsForm() {
        $send_plus = filter_input(INPUT_POST, 'send_plus', FILTER_DEFAULT);
        $send_minus = filter_input(INPUT_POST, 'send_minus', FILTER_DEFAULT);

        if (!empty($send_plus) || !empty($send_minus)) {
		    $mod = (!empty($send_plus) ? 1 : (!empty($send_minus) ? 2 : 0));
            self::PointsFormSend($mod);
        }

        $all_plus = filter_input(INPUT_POST, 'all_plus', FILTER_DEFAULT);
        $all_minus = filter_input(INPUT_POST, 'all_minus', FILTER_DEFAULT);
        if (!empty($all_plus) || !empty($all_minus)) {
		    $mod = (!empty($all_plus) ? 1 : (!empty($all_minus) ? 2 : 0));
            self::PointsFormAllSend($mod);
        }

        $group_plus = filter_input(INPUT_POST, 'group_plus', FILTER_DEFAULT);
        $group_minus = filter_input(INPUT_POST, 'group_minus', FILTER_DEFAULT);
        if (!empty($group_plus) || !empty($group_minus)) {
		    $mod = (!empty($group_plus) ? 1 : (!empty($group_minus) ? 2 : 0));
            self::PointsFormGroupSend($mod);
        }
    }

    private function PointsFormSend($mod = NULL) {

        $point_user = form_sanitizer(filter_input(INPUT_POST, 'point_user', FILTER_VALIDATE_INT), 0, 'point_user');
        $point_point = form_sanitizer(filter_input(INPUT_POST, 'point_point', FILTER_VALIDATE_INT), 0, 'point_point');
        $log_descript = form_sanitizer(filter_input(INPUT_POST, 'log_descript', FILTER_DEFAULT), '', 'log_descript');
        $pointinfo = ($mod == '2' ? UserPoint::getInstance()->PointInfo($point_user, $point_point) : 10);

        $max_rows = dbcount("(user_id)", DB_USERS, "user_id = :userid", [':userid' => $point_user]);
        if ($max_rows && $mod > 0 && $pointinfo > 0 && \defender::safe()) {
            UserPoint::getInstance()->setPoint($point_user, ["mod" => $mod, "point" => $point_point, "messages" => $log_descript]);
            addNotice('success', $log_descript);
            redirect(FUSION_REQUEST);
        }
    }

    private function PointsFormAllSend($mod = NULL) {

        $point_point = form_sanitizer(filter_input(INPUT_POST, 'point_point', FILTER_VALIDATE_INT), 0, 'point_point');
        $log_descript = form_sanitizer(filter_input(INPUT_POST, 'log_descript', FILTER_DEFAULT), '', 'log_descript');

        if (\defender::safe()) {
            $messages = $mod == 1 ? self::$locale['PSP_021'] : self::$locale['PSP_020'];
            $result = dbquery("SELECT p.*, pu.*
                FROM ".DB_POINT." AS p
                LEFT JOIN ".DB_USERS." AS pu ON pu.user_id = p.point_user
                WHERE user_status = :status AND user_lastvisit != :lastvisit
                ORDER BY user_name ASC
            ", [':status' => '0', ':lastvisit' => '']);

            if (dbrows($result)){
                while($data = dbarray($result)){
                	$pointinfo = $mod == '2' ? UserPoint::getInstance()->PointInfo($data['point_user'], $point_point) : 10;
                    if ($mod > 0 && $pointinfo > 0) {
                        UserPoint::getInstance()->setPoint($data['point_user'], ["mod" => $mod, "point" => $point_point, "messages" => $log_descript]);
                    }
                }
                addNotice('success', $messages);
                redirect(FUSION_REQUEST);
            }
        }
    }

    private function PointsFormGroupSend($mod = NULL) {

        $group = form_sanitizer(filter_input(INPUT_POST, 'group', FILTER_VALIDATE_INT), 0, 'group');
        $point_point = form_sanitizer(filter_input(INPUT_POST, 'point_point', FILTER_VALIDATE_INT), 0, 'point_point');
        $log_descript = form_sanitizer(filter_input(INPUT_POST, 'log_descript', FILTER_DEFAULT), '', 'log_descript');

        if (\defender::safe()) {
            $messages = $mod == 1 ? self::$locale['PONT_233'] : self::$locale['PONT_232'];
            $result = dbquery("SELECT p.*, pu.*
                FROM ".DB_POINT." AS p
                LEFT JOIN ".DB_USERS." AS pu ON pu.user_id = p.point_user
                WHERE user_groups REGEXP('^\\\.{$group}$|\\\.{$group}\\\.|\\\.{$group}$')
                ORDER BY user_name ASC
            ");
            if (dbrows($result)) {
                while($data = dbarray($result)) {
                    $pointinfo = $mod == '2' ? UserPoint::getInstance()->PointInfo($data['point_user'], $point_point) : 10;
                    if ($mod > 0 && $pointinfo > 0) {
                        UserPoint::getInstance()->setPoint($data['point_user'], ["mod" => $mod, "point" => $point_point, "messages" => $log_descript]);
                    }
                }
                addNotice('success', $messages);
                redirect(FUSION_REQUEST);
            }
        }
    }

    private function Pointsfilter() {
        $result = dbquery("SELECT p.*, pu.user_id, pu.user_name, pu.user_status
            FROM ".DB_POINT." AS p
            LEFT JOIN ".DB_USERS." AS pu ON p.point_user = pu.user_id
            WHERE user_status = :status AND user_lastvisit != :lastvisit
            GROUP BY pu.user_id
            ORDER BY user_name ASC
        ", [':status' => '0', ':lastvisit' => '']);
        $opts = [];
        if (dbrows($result) > 0) {
            while ($data = dbarray($result)) {
                $opts[$data['user_id']] = $data['user_name']." ( ".number_format($data['point_point'])." )";
            }
        }
        return $opts;
    }

    private function PointsDisplay() {

    	opentable(self::$locale['PSP_M10']);
    	openside(self::$locale['PSP_022']);
    	echo openform('point_form', 'post', FUSION_REQUEST);
    	echo form_select('point_user', self::$locale['PSP_023'], 0, [
            'required'    => TRUE,
    	    'inline'      => TRUE,
    	    'allowclear'  => TRUE,
    	    'options'     => self::Pointsfilter()
    	]);

    	echo form_text('point_point', self::$locale['PSP_024'], 0, [
            'required'    => TRUE,
    	    'type'        => 'number',
    	    'max_length'  => 6,
    	    'number_min'  => 1,
    	    'inner_width' => '100px',
    	    'inline'      => TRUE
    	]);

        echo form_text('log_descript', self::$locale['PSP_025'], '', [
            'required'   => TRUE,
            'max_length' => 200,
            'inline'     => TRUE
        ]);

    	echo "<div class='text-center'>".(form_button('send_plus', self::$locale['PSP_026'], self::$locale['PSP_026'])."&nbsp;&nbsp;".
        form_button('send_minus', self::$locale['PSP_027'], self::$locale['PSP_027']));
        echo "</div>";

        echo closeform();
        closeside();

        openside(self::$locale['PSP_028']);
        echo openform('alluser_form', 'post', FUSION_REQUEST);
    	echo form_text('point_point', self::$locale['PSP_024'], 0, [
            'required'    => TRUE,
    	    'type'        => 'number',
    	    'max_length'  => 6,
    	    'number_min'  => 1,
    	    'inner_width' => '100px',
    	    'inline'      => TRUE
            ]);

        echo form_text('log_descript', self::$locale['PSP_025'], '', [
            'required'   => TRUE,
            'max_length' => 200,
            'inline'     => TRUE
        ]);

        echo "<div class='text-center'>".(form_button('all_plus', self::$locale['PSP_026'], self::$locale['PSP_026'])."&nbsp;&nbsp;".
        form_button('all_minus', self::$locale['PSP_027'], self::$locale['PSP_027']));
        echo "</div>";
        echo closeform();
        closeside();

        $group = [0 => self::$locale['PSP_029']];
        $result = dbquery("SELECT group_id, group_name FROM ".DB_USER_GROUPS." ORDER BY group_name ASC ");
        if (dbrows($result)){
        	while ($groupdata = dbarray($result)){
        		$group[$groupdata['group_id']] = $groupdata['group_name'];
        	}
        }

        if (count($group) > 1) {
        	openside(self::$locale['PSP_030']);
        	echo openform('group_form', 'post', FUSION_REQUEST);
        	echo form_select('group', self::$locale['PSP_031'], 0, [
                'required'    => TRUE,
                'inline'      => TRUE,
                'allowclear'  => TRUE,
                'options'     => $group
            ]);
            echo form_text('point_point', self::$locale['PSP_024'], 0, [
                'required'    => TRUE,
                'type'        => 'number',
                'max_length'  => 6,
                'number_min'  => 1,
                'inner_width' => '100px',
                'inline'      => TRUE
            ]);

            echo form_text('log_descript', self::$locale['PSP_025'], '', [
                'required'   => TRUE,
                'max_length' => 200,
                'inline'     => TRUE
            ]);
            echo "<div class='text-center'>".(form_button('group_plus', self::$locale['PSP_026'], self::$locale['PSP_026'])."&nbsp;&nbsp;".
            form_button('group_minus', self::$locale['PSP_027'], self::$locale['PSP_027']));
            echo "</div>";
            echo closeform();
            closeside();
        }

    	closetable();
    }
}