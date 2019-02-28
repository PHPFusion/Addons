<?php

namespace PHPFusion\Points;

class PointsDiary extends PointsModel {
    private static $instance = NULL;
    private static $locale = [];
    public $settings = [];
    public $nplink = '';

    public function __construct() {
        self::globinf();
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
        set_title(self::$locale['PONT_200']);
        if (!empty($this->del)) {
        	self::DelDiary();
        }
        if (!empty($this->deleteall)) {
            self::DelallDiary();
        }

		$info = [
		    'diaryfilter' => self::Diaryfilter(),
		    'ittem'       => self::DiaryData()
		];
        Display_Diary($info);
	}

	private function DelDiary() {
		if (!empty($this->del) && ($this->np == iNP) && isnum($this->logid) && ($this->del == "delete") && ($this->logid > 0)) {
			$userid = fusion_get_userdata('user_id');
			$result = dbquery("SELECT * FROM ".DB_POINT_LOG." WHERE log_user_id=:userid AND log_id=:logid", [':userid' => $userid, ':logid' => $this->logid]);
			if (dbrows($result)) {
				dbquery("UPDATE ".DB_POINT_LOG." SET log_active=:active WHERE log_user_id=:userid AND log_id=:logid", [':active' => '1', ':userid' => $userid, ':logid' => $this->logid]);
				return addNotice('sucess', "<i class='fa fa-remove fa-lg fa-fw'></i>".self::$locale['PONT_203']);
			}
			return addNotice('warning', "<i class='fa fa-remove fa-lg fa-fw'></i>".self::$locale['PONT_202']);
		}
	}

	private function DelallDiary() {
		if (!empty($this->deleteall) && ($this->np == iNP) && $this->deleteall == "all") {
			$userid = fusion_get_userdata('user_id');
			$result = dbquery("SELECT * FROM ".DB_POINT_LOG." WHERE log_user_id=:userid", [':userid' => $userid]);
			if (dbrows($result)) {
				$result = dbquery("UPDATE ".DB_POINT_LOG." SET log_active=:active WHERE log_user_id=:userid", [':active' => '1', ':userid' => $userid]);
				return addNotice('sucess', "<i class='fa fa-remove fa-lg fa-fw'></i>".self::$locale['PONT_201']);
			}
			return addNotice('warning', "<i class='fa fa-remove fa-lg fa-fw'></i>".self::$locale['PONT_202']);
		}
	}

	private function Diaryfilter() {
        $this->pdiary_filter = $this->diary_filter;
        $this->gdiary_filter = filter_input(INPUT_GET, 'diary_filter', FILTER_DEFAULT);
		$this->diary_filter = (!empty($this->pdiary_filter) && isnum($this->pdiary_filter) ? $this->pdiary_filter : 0);
		$this->diary_filter = empty($this->diary_filter) ? (!empty($this->gdiary_filter) && isnum($this->gdiary_filter) ? $this->gdiary_filter : 0) : $this->diary_filter;

		$info = openform('diary_form', 'post', FUSION_SELF).
        form_select('diary_filter', '', $this->diary_filter, [
            'allowclear' => TRUE,
            'options'    => self::$locale['adm038'],
            'onchange'   => 'document.diary_form.submit()'
        ]).
		closeform();

        return $info;
	}

	private function DiaryData() {
		$userid = fusion_get_userdata('user_id');
		$sql_condition = !empty($this->diary_filter) ? " AND log_pmod='".$this->diary_filter."'" : "";
        $max_rows = dbcount("(log_id)", DB_POINT_LOG, "log_user_id='".$userid."' AND log_active='0'".$sql_condition);
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
	        WHERE log_user_id=:userid AND log_active=:active".$sql_condition."
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
            'delall'  => (!empty(dbrows($result)) ? "<a class='btn btn-default btn-sm' href='".FUSION_SELF.$this->nplink."&amp;deleteall=all' onclick=\"return confirm('".self::$locale['PONT_304']."' );\">".self::$locale['PONT_204']."</a>" : ""),
            'pagenav' => makepagenav($this->rowstart, $this->settings['ps_page'], $max_rows, 3, POINT_CLASS."points_diary.php".$this->nplink."&diary_filter=".$this->diary_filter."&")
	    ];
        return $info;
	}
}
