<?php
namespace PHPFusion\Points;

class PointsBanAdmin extends PointsModel {
    private static $instance = NULL;
    private static $locale = [];
    private $info = [];
    public $settings = [];

    public function __construct() {
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
        set_title(self::$locale['PONT_103']);
		if (isset($_GET['ban_id']) && isnum($_GET['ban_id'])) {
		    $unban = dbarray(dbquery("SELECT ban_user_id FROM ".DB_POINT_BAN." WHERE ban_id=:banid", [':banid' => intval($_GET['ban_id'])]));
		    if (!empty($unban)) {
                \PHPFusion\Points\UserPoint::getInstance()->SetPointBan($unban['ban_user_id'], ['ban_mod' => 2, 'ban_stop' => (time() - 10)]);
		    }
		}
		if (isset($_POST['ban_users'])) {
			$ban_user = form_sanitizer($_POST['ban_user'], 0, 'ban_user');
			$ban_log = form_sanitizer($_POST['ban_log'], '', 'ban_log');
			$ban_end = form_sanitizer($_POST['ban_end'], 0, 'ban_end');
			if (\defender::safe() && !empty($ban_user) && $ban_end > time()) {
                \PHPFusion\Points\UserPoint::getInstance()->SetPointBan($ban_user, ['ban_mod' => 1, 'ban_start' => time(), 'ban_stop' => $ban_end, 'ban_text' => $ban_log]);
                addNotice('success', self::$locale['PONT_301']);
                redirect(FUSION_REQUEST);
			}

		}

		iADMIN ? self::BanDisplay() : '';
	}

	private function Currentdata($condition = FALSE) {
		$rwstart = empty($condition) ? "banstart" : "defstart";
		$sql_condition = empty($condition) ? "(ban_time_start<='".time()."' AND ban_time_stop>='".time()."') || (ban_time_start<='".time()."' AND ban_time_stop='0')" : "ban_time_stop!=0";
        $max_rows = dbcount("(ban_id)", DB_POINT_BAN, $sql_condition.(multilang_table("PSP") ? " AND ban_language='".LANGUAGE."'" : ''));
        $_GET[$rwstart] = (isset($_GET[$rwstart]) && isnum($_GET[$rwstart]) && $_GET[$rwstart] <= $max_rows) ? $_GET[$rwstart] : 0;
        $page_nav = makepagenav($_GET[$rwstart], $this->settings['ps_page'], $max_rows, 3, POINT_CLASS."points_ban.php".fusion_get_aidlink()."&", $rwstart);

        $bind = [
            ':language'      => LANGUAGE,
            ':rowstart'      => $_GET[$rwstart],
            ':limit'         => $this->settings['ps_page']
        ];
	    $result = dbquery("SELECT pbu.*, pb.*
	        FROM ".DB_POINT_BAN." AS pb
	        LEFT JOIN ".DB_USERS." AS pbu ON pbu.user_id= pb.ban_user_id
	        WHERE $sql_condition
            ".(multilang_table("PSP") ? " AND pb.ban_language=:language" : "")."
            LIMIT :rowstart, :limit", $bind);
            $inf = [];

        while ($data = dbarray($result)){
            $inf[] = $data;
	    }
	    $info = [
	        'ittem'   => $inf,
            'max_row' => $max_rows,
            'pagenav' => $page_nav
	    ];
        //print_p($info);
        return $info;
	}

	private function Banuserform() {
        $info = openform('bansearchform', 'post', FUSION_REQUEST).
        form_user_select('ban_user', self::$locale['PONT_175'], '', [
            'required'    => TRUE,
            'max_select'  => 1,
            'class'       => 'center-block',
            'inner_width' => '50%',
            'width'       => '50%',
            'allow_self'  => TRUE
        ]).
        form_text('ban_log', self::$locale['PONT_167'], '', [
            'required'   => TRUE,
            'max_length' => 200,
            'inline'     => TRUE
        ]).
        form_datepicker('ban_end', self::$locale['PONT_176'], 0, [
            'inline'          => TRUE,
            'type'            => 'time',
            'date_format_js'  => 'YYYY-M-DD H',
            'date_format_php' => 'Y-m-d H'
        ]).
        form_button('ban_users', self::$locale['PONT_177'], self::$locale['PONT_177']).
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