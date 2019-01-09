<?php
namespace PHPFusion\Points;

class PointsPlace extends PointsModel {
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

        set_title(self::$locale['PONT_130']);
        $max_rows = dbcount("(point_id)", DB_POINT, (multilang_table("PSP") ? "point_language='".LANGUAGE."'" : ''));
        $_GET['rowstart'] = (isset($_GET['rowstart']) && isnum($_GET['rowstart']) && $_GET['rowstart'] <= $max_rows) ? $_GET['rowstart'] : 0;
        $page_nav = makepagenav($_GET['rowstart'], $this->settings['ps_page'], $max_rows, 3, POINT_CLASS."points_bestof.php&nbsp;");

        $bind = [
            ':language' => LANGUAGE,
            ':rowstart' => $_GET['rowstart'],
            ':limit'    => $this->settings['ps_page']
        ];

        $result = dbquery("SELECT p.*, pu.user_id, pu.user_name, pu.user_status, pu.user_avatar, pu.user_joined, pu.user_level
            FROM ".DB_POINT." AS p
            LEFT JOIN ".DB_USERS." AS pu ON pu.user_id = p.point_user
            ".(multilang_table("PSP") ? "WHERE p.point_language=:language" : "")."
            ORDER BY point_point DESC
            LIMIT :rowstart, :limit", $bind);


        while ($data = dbarray($result)){
            $inf[] = [
                'point_id'   => $data['point_id'],
                'point_user' => $data['point_user'],
                'avatar'     => display_avatar($data, '50px', '', TRUE, 'img-rounded'),
                'profile'    => profile_link($data['user_id'], $data['user_name'], $data['user_status']),
                'point'      => number_format($data['point_point'])
            ];
	    }

        $info = [
            'opentable' => "<i class='fa fa-pie-chart fa-lg m-r-10'></i>".self::$locale['PONT_130'],
            'message'   => sprintf(self::$locale['PONT_134'], $this->settings['ps_page']),
            'max_row'   => $max_rows,
            'stat_rows' => dbrows($result),
            'pagenav'   => $page_nav,
            'helyezes'  => $_GET['rowstart'],
            'item'      => $inf
        ];

        PlaceItem($info);
	}



}