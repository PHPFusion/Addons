<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: points_panel/classes/points_diary.php
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

class PointsDiary extends PointsModel {
    private static $instance = NULL;
    private static $locale = [];
    public $settings = [];
    public $diary_filter = '';
    public $nplink = '';
    private $rowstart;

    public function __construct() {
        include_once POINT_CLASS."templates.php";
        $this->settings = self::CurrentSetup();
        self::$locale = fusion_get_locale("", POINT_LOCALE);
	    $this->nplink = "?np=".iNP;
    }

    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new static();
        }
       return self::$instance;
    }

	public function DisplayList() {
        set_title(self::$locale['PSP_D00']);
        $del = filter_input(INPUT_GET, 'del', FILTER_DEFAULT);
        $np = filter_input(INPUT_GET, 'np', FILTER_DEFAULT);
        if (!empty($del)) {
            $logid = filter_input(INPUT_GET, 'log_id', FILTER_VALIDATE_INT);
            if (($np == iNP) && isnum($logid) && ($logid > 0) && ($del == "delete")) {
                self::DelDiary($logid);
            }
        }
        $deleteall = filter_input(INPUT_GET, 'deleteall', FILTER_DEFAULT);
        if (!empty($deleteall)) {
            if (($np == iNP) && ($deleteall == "all")) {
                self::DelallDiary();
            }
        }

		$info = [
	        'diaryfilter' => self::Diaryfilter(),
            'ittem'       => self::DiaryData()
		];
        Display_Diary($info);
    }

    /**
     * @param $logid
     * @return mixed
     */
    private function DelDiary($logid) {
        $userid = fusion_get_userdata('user_id');
        $result = dbquery("SELECT * FROM ".DB_POINT_LOG." WHERE log_user_id = :userid AND log_id = :logid", [':userid' => $userid, ':logid' => $logid]);
        if (dbrows($result)) {
            dbquery("UPDATE ".DB_POINT_LOG." SET log_active = :active WHERE log_user_id = :userid AND log_id = :logid", [':active' => '1', ':userid' => $userid, ':logid' => $logid]);
            return addNotice('sucess', "<i class='fa fa-remove fa-lg fa-fw'></i>".self::$locale['PSP_D02']);
        }
        return addNotice('warning', "<i class='fa fa-remove fa-lg fa-fw'></i>".self::$locale['PSP_D01']);
    }
    /**
     * @return mixed
     */
    private function DelallDiary() {
        $userid = fusion_get_userdata('user_id');
        $result = dbquery("SELECT * FROM ".DB_POINT_LOG." WHERE log_user_id = :userid", [':userid' => $userid]);
        if (dbrows($result)) {
            dbquery("UPDATE ".DB_POINT_LOG." SET log_active = :active WHERE log_user_id = :userid", [':active' => '1', ':userid' => $userid]);
            return addNotice('sucess', "<i class='fa fa-remove fa-lg fa-fw'></i>".self::$locale['PSP_D03']);
        }
        return addNotice('warning', "<i class='fa fa-remove fa-lg fa-fw'></i>".self::$locale['PSP_D01']);
	}

	private function Diaryfilter() {
        $gdiary_filter = filter_input(INPUT_GET, 'filter', FILTER_DEFAULT);
        $pdiary_filter = filter_input(INPUT_POST, 'diary_filter', FILTER_DEFAULT);
		$this->diary_filter = (!empty($gdiary_filter) ? $gdiary_filter : (!empty($pdiary_filter) ? $pdiary_filter : 0));
		$info = openform('diary_form', 'post', FUSION_SELF).
        form_select('diary_filter', '', $this->diary_filter, [
            'allowclear' => TRUE,
            'options'    => self::$locale['PSP_LST'],
            'onchange'   => 'document.diary_form.submit()'
        ]).
		closeform();

        return $info;
	}

	private function DiaryData() {
		$userid = fusion_get_userdata('user_id');
		$sql_condition = !empty($this->diary_filter) ? " AND log_pmod = '".$this->diary_filter."'" : "";
        $max_rows = dbcount("(log_id)", DB_POINT_LOG, "log_user_id = '".$userid."' AND log_active = '0'".$sql_condition);
        $this->rowstart = filter_input(INPUT_GET, 'rowstart', FILTER_VALIDATE_INT);
        $this->rowstart = (!empty($this->rowstart) && isnum($this->rowstart) && $this->rowstart <= $max_rows) ? $this->rowstart : 0;

        $bind = [
            ':active'   => '0',
            ':userid'   => $userid,
            ':rowstart' => $this->rowstart,
            ':limit'    => $this->settings['ps_page']
        ];
	    $result = dbquery("SELECT pu.user_id, pu.user_name, pu.user_status, pu.user_avatar, pu.user_joined, pu.user_level, pl.*
	        FROM ".DB_POINT_LOG." AS pl
	        LEFT JOIN ".DB_USERS." AS pu ON pu.user_id = pl.log_user_id
	        WHERE log_user_id = :userid AND log_active = :active".$sql_condition."
	        ORDER BY log_date DESC
            LIMIT :rowstart, :limit", $bind);
        $inf = [];
        while ($data = dbarray($result)){
            $inf[] = $data;
	    }

	    $info = [
	        'diary'   => $inf,
            'max_row' => $max_rows,
            'link'    => $this->nplink,
            'delall'  => (!empty(dbrows($result)) ? "<a class='btn btn-default btn-sm' href='".FUSION_SELF.$this->nplink."&deleteall=all' onclick=\"return confirm('".self::$locale['PSP_D04']."' );\">".self::$locale['PSP_D05']."</a>" : ""),
            'pagenav' => makepagenav($this->rowstart, $this->settings['ps_page'], $max_rows, 3, POINT_CLASS."points_diary.php".$this->nplink."&filter=".$this->diary_filter."&")
	    ];
        return $info;
	}
}
