<?php
namespace PHPFusion\Points;

class PointsDiary extends PointsModel {
    private static $instance = NULL;
    private static $locale = [];
    private $info = [];
    public $settings = [];
    public $nplink = '';
    public $diary_filter = '';

    public function __construct() {
        include_once POINT_CLASS."templates.php";
        $this->settings = self::CurrentSetup();
        self::$locale = fusion_get_locale("", POINT_LOCALE);
	    $this->nplink = "?np=".iNP;
		$this->diary_filter = (isset($_POST['diary_filter']) && isnum($_POST['diary_filter']) ? $_POST['diary_filter'] : 0);
    }

    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new static();
        }
       return self::$instance;
    }

	public function DisplayList() {
		if (isset($_GET['deleteall']) && ($_GET['np'] == iNP) && $_GET['deleteall']=="all") {
			$result = dbquery("SELECT * FROM ".DB_POINT_LOG." WHERE log_user_id='".fusion_get_userdata('user_id')."' ");
			if (dbrows($result)) {
				$result = dbquery("UPDATE ".DB_POINT_LOG." SET log_active='1' WHERE log_user_id='".fusion_get_userdata('user_id')."'");
				addNotice('sucess', "<i class='fa fa-remove fa-lg fa-fw'></i>".self::$locale['PONT_201']);
			} else {
				addNotice('warning', "<i class='fa fa-remove fa-lg fa-fw'></i>".self::$locale['PONT_202']);
			}

		}

		if (isset($_GET['del']) && ($_GET['np'] == iNP) && isnum($_GET['log_id']) && ($_GET['del'] == "delete") && ($_GET['log_id'] > 0)) {
			$id = stripinput($_GET['log_id']);
			$result = dbquery("SELECT * FROM ".DB_POINT_LOG." WHERE log_user_id='".fusion_get_userdata('user_id')."' AND log_id='".$id."' ");
			if (dbrows($result)) {
				$result = dbquery("UPDATE ".DB_POINT_LOG." SET log_active='1' WHERE log_user_id='".fusion_get_userdata('user_id')."' AND log_id='".$id."'");
				addNotice('sucess', "<i class='fa fa-remove fa-lg fa-fw'></i>".self::$locale['PONT_203']);
			} else {
				addNotice('warning', "<i class='fa fa-remove fa-lg fa-fw'></i>".self::$locale['PONT_202']);
			}
		}

		$info = [
		    'diaryfilter' => self::Diaryfilter(),
		    'ittem' => self::DiaryData(),
		];
    //print_p($info);
        Display_Diary($info);
	}

	private function Diaryfilter() {

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
		$sql_condition = !empty($this->diary_filter) ? " AND log_pmod='".$this->diary_filter."'" : "";
        $max_rows = dbcount("(log_id)", DB_POINT_LOG, "log_user_id='".fusion_get_userdata('user_id')."' AND log_active=0".$sql_condition);
        $_GET['rowstart'] = (isset($_GET['rowstart']) && isnum($_GET['rowstart']) && $_GET['rowstart'] <= $max_rows) ? $_GET['rowstart'] : 0;
        $page_nav = makepagenav($_GET['rowstart'], $this->settings['ps_page'], $max_rows, 3, POINT_CLASS."points_diary.php".$this->nplink."&diary_filter=".$this->diary_filter."&");

        $bind = [
            ':active'   => 0,
            ':rowstart' => $_GET['rowstart'],
            ':limit'    => $this->settings['ps_page']
        ];
	    $result = dbquery("SELECT pu.user_id, pu.user_name, pu.user_status, pu.user_avatar, pu.user_joined, pu.user_level, pl.*
	        FROM ".DB_POINT_LOG." pl
	        LEFT JOIN ".DB_USERS." pu ON pu.user_id= pl.log_user_id
	        WHERE log_user_id='".fusion_get_userdata('user_id')."' AND log_active=:active".$sql_condition."
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
            'pagenav' => $page_nav
	    ];
        return $info;

	}

}


