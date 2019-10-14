<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: classes/admin/points_ban.php
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

class PointsBanAdmin extends PointsModel {
    private static $instance = NULL;
    public $settings = [];

    public function __construct() {
        pageAccess('PSP');
        include_once POINT_CLASS."templates.php";
        $this->settings = self::CurrentSetup();
        self::$locale = fusion_get_locale("", POINT_LOCALE);
    }

    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new static();
        }
       return self::$instance;
    }

	public function CurrentList() {
        set_title(self::$locale['PSP_M11']);
        $ban_buton = filter_input(INPUT_POST, 'ban_buton', FILTER_DEFAULT);

		if (!empty($ban_buton)) {
            $ban_id = filter_input(INPUT_GET, 'ban_id', FILTER_VALIDATE_INT);
            $ban_users = filter_input(INPUT_POST, 'ban_user', FILTER_VALIDATE_INT);
		    if (!empty($ban_id) && isnum($ban_id)) {
		        $unban = dbarray(dbquery("SELECT ban_user_id FROM ".DB_POINT_BAN." WHERE ban_id=:banid", [':banid' => (int)$ban_id]));
		        if (!empty($unban)) {
		            UserPoint::getInstance()->SetPointBan($unban['ban_user_id'], ['ban_mod' => 2, 'ban_stop' => (time() - 10)]);
		        }
		    }

		    if (!empty($ban_users)) {
		        $ban_user = form_sanitizer($ban_users, 0, 'ban_user');
		        $ban_log = form_sanitizer(filter_input(INPUT_POST, 'ban_log', FILTER_DEFAULT), '', 'ban_log');
		        $ban_end = form_sanitizer(filter_input(INPUT_POST, 'ban_end', FILTER_DEFAULT), 0, 'ban_end');
		        if (\defender::safe() && $ban_end > time()) {
		            UserPoint::getInstance()->SetPointBan($ban_user, ['ban_mod' => 1, 'ban_start' => time(), 'ban_stop' => $ban_end, 'ban_text' => $ban_log]);
		            addNotice('success', self::$locale['PSP_A01']);
		            redirect(FUSION_REQUEST);
		        }
		    }
		}
        iADMIN ? self::BanDisplay() : '';
    }

	private function Currentdata($condition = FALSE) {
		$sql_condition = empty($condition) ? "(ban_time_start<='".time()."' AND ban_time_stop>='".time()."') || (ban_time_start<='".time()."' AND ban_time_stop='0')" : "ban_time_stop!=0";
        $max_rows = dbcount("(ban_id)", DB_POINT_BAN, $sql_condition.(multilang_table("PSP") ? " AND ban_language='".LANGUAGE."'" : ''));
		$rwstart = empty($condition) ? "banstart" : "defstart";
        $rowstart = filter_input(INPUT_GET, $rwstart, FILTER_DEFAULT);
        $rowstart = (!empty($rowstart) && isnum($rowstart) && $rowstart <= $max_rows) ? $rowstart : 0;

        $bind = [
            ':rowstart' => $rowstart,
            ':limit'    => $this->settings['ps_page']
        ];
	    $result = dbquery("SELECT pbu.*, pb.*
	        FROM ".DB_POINT_BAN." AS pb
	        LEFT JOIN ".DB_USERS." AS pbu ON pbu.user_id = pb.ban_user_id
	        WHERE $sql_condition
            ".(multilang_table("PSP") ? " AND pb.ban_language = '".LANGUAGE."'" : "")."
            LIMIT :rowstart, :limit", $bind);
        $inf = [];

        while ($data = dbarray($result)){
            $inf[] = $data;
	    }
	    $info = [
	        'ittem'   => $inf,
            'max_row' => $max_rows,
            'pagenav' => makepagenav($rowstart, $this->settings['ps_page'], $max_rows, 3, POINT_CLASS."points_ban.php".fusion_get_aidlink()."&", $rwstart)
	    ];
        return $info;
	}

	private function Banuserform() {
        $info = openform('bansearchform', 'post', FUSION_REQUEST).
        form_user_select('ban_user', self::$locale['PSP_A05'], '', [
            'required'    => TRUE,
            'max_select'  => 1,
            'class'       => 'center-block',
            'inner_width' => '50%',
            'width'       => '50%',
            'allow_self'  => TRUE
        ]).
        form_text('ban_log', self::$locale['PSP_A06'], '', [
            'required'   => TRUE,
            'max_length' => 200,
            'inline'     => TRUE
        ]).
        form_datepicker('ban_end', self::$locale['PSP_A07'], 0, [
            'inline'          => TRUE,
            'type'            => 'time',
            'date_format_js'  => 'YYYY-M-DD H',
            'date_format_php' => 'Y-m-d H'
        ]).
        form_button('ban_buton', self::$locale['PSP_A08'], self::$locale['PSP_A08']).
        closeform();
        return $info;
	}

	private function BanDisplay() {
		$info = [
		    'aktivban' => self::Currentdata(),
		    'allban'   => self::Currentdata(TRUE),
		    'banuser'  => self::Banuserform()
		];
		BanItem($info);
	}
}