<?php
namespace PHPFusion\Points;

class PointsPointsAdmin extends PointsModel {
    private static $instance = NULL;
    private static $locale = [];
    private $points_settings = [];
    public $diary_filter = '';
    public $diary_user = '';

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
        set_title(self::$locale['PONT_104']);
        self::Pointsform();
        self::PointsDisplay();

	}

    private function Pointsform() {
		if (isset($_POST['send_plus']) or isset($_POST['send_minus'])) {
			$point_user = form_sanitizer($_POST['point_user'], 0, 'point_user');
			$point_point = form_sanitizer($_POST['point_point'], 0, 'point_point');
			$log_descript = form_sanitizer($_POST['log_descript'], '', 'log_descript');
			$mod = (isset($_POST['send_plus']) ? 1 : (isset($_POST['send_minus']) ? 2 : 0));
            $pointinfo = $mod == '2' ? \PHPFusion\Points\UserPoint::getInstance()->PointInfo($point_user, $point_point) : 10;

            $max_rows = dbcount("(user_id)", DB_USERS, "user_id='".$point_user."'");
            if ($max_rows && $mod > 0 && $pointinfo > 0 && \defender::safe()) {
                \PHPFusion\Points\UserPoint::getInstance()->setPoint($point_user, ["mod" => $mod, "point" => $point_point, "messages" => $log_descript]);
            	addNotice('success', $log_descript);
            	redirect(FUSION_REQUEST);
            }
		}

		if (isset($_POST['all_plus']) or isset($_POST['all_minus'])) {
			$point_point = form_sanitizer($_POST['point_point'], 0, 'point_point');
			$log_descript = form_sanitizer($_POST['log_descript'], '', 'log_descript');
			$mod = (isset($_POST['all_plus']) ? 1 : (isset($_POST['all_minus']) ? 2 : 0));
			if (\defender::safe()) {
			    $messages = $mod == 1 ? self::$locale['PONT_231'] : self::$locale['PONT_230'];
				$result = dbquery("SELECT p.*, pu.*
				    FROM ".DB_POINT." p
				    LEFT JOIN ".DB_USERS." pu ON pu.user_id=p.point_user
				    WHERE user_status='0' && user_lastvisit!=''
				    ORDER BY user_name ASC
				");
				if (dbrows($result)){
					while($data = dbarray($result)){
						$pointinfo = $mod == '2' ? \PHPFusion\Points\UserPoint::getInstance()->PointInfo($data['point_user'], $point_point) : 10;
						if ($mod > 0 && $pointinfo > 0) {
							\PHPFusion\Points\UserPoint::getInstance()->setPoint($data['point_user'], ["mod" => $mod, "point" => $point_point, "messages" => $log_descript]);
						}
					}
					addNotice('success', $messages);
					redirect(FUSION_REQUEST);
				}
			}

        }

		if (isset($_POST['group_plus']) or isset($_POST['group_minus'])) {
			$group = form_sanitizer($_POST['group'], 0, 'group');
			$point_point = form_sanitizer($_POST['point_point'], 0, 'point_point');
			$log_descript = form_sanitizer($_POST['log_descript'], '', 'log_descript');
			$mod = (isset($_POST['group_plus']) ? 1 : (isset($_POST['group_minus']) ? 2 : 0));
			if (\defender::safe()) {
			    $messages = $mod == 1 ? self::$locale['PONT_233'] : self::$locale['PONT_232'];
				$result = dbquery("SELECT p.*, pu.*
				    FROM ".DB_POINT." p
				    LEFT JOIN ".DB_USERS." pu ON pu.user_id=p.point_user
				    WHERE user_groups REGEXP('^\\\.{$group}$|\\\.{$group}\\\.|\\\.{$group}$')
				    ORDER BY user_name ASC
				");
				if (dbrows($result)){
					while($data = dbarray($result)){
						$pointinfo = $mod == '2' ? \PHPFusion\Points\UserPoint::getInstance()->PointInfo($data['point_user'], $point_point) : 10;
						if ($mod > 0 && $pointinfo > 0) {
							\PHPFusion\Points\UserPoint::getInstance()->setPoint($data['point_user'], ["mod" => $mod, "point" => $point_point, "messages" => $log_descript]);
						}
					}
					addNotice('success', $messages);
					redirect(FUSION_REQUEST);
				}
			}

        }

    }

    private function Pointsfilter() {
        $result = dbquery("SELECT p.*, pu.user_id, pu.user_name, pu.user_status
            FROM ".DB_POINT." p
            LEFT JOIN ".DB_USERS." pu on p.point_user = pu.user_id
            WHERE user_status = '0' && user_lastvisit != ''
            GROUP BY pu.user_id
            ORDER BY user_name ASC
        ");
        $opts = [];
        if (dbrows($result) > 0) {
        	while ($data = dbarray($result)) {
        		$opts[$data['user_id']] = $data['user_name']." ( ".number_format($data['point_point'])." )";
        	}
        }

        return $opts;
    }

    private function PointsDisplay() {

    	opentable(self::$locale['PONT_104']);
    	openside(self::$locale['PONT_234']);
    	echo openform('point_form', 'post', FUSION_REQUEST);
    	echo form_select('point_user', self::$locale['PONT_235'], 0, [
            'required'    => TRUE,
    	    'inline'      => TRUE,
    	    'allowclear'  => TRUE,
    	    'options'     => self::Pointsfilter()
    	]);

    	echo form_text('point_point', self::$locale['PONT_236'], 0, [
            'required'    => TRUE,
    	    'type'        => 'number',
    	    'max_length'  => 5,
    	    'number_min'  => 1,
    	    'inner_width' => '100px',
    	    'inline'      => TRUE
    	]);

        echo form_text('log_descript', self::$locale['PONT_237'], '', [
            'required'   => TRUE,
            'max_length' => 200,
            'inline'     => TRUE
        ]);

    	echo "<div class='text-center'>".(form_button('send_plus', self::$locale['PONT_238'], self::$locale['PONT_238'])."&nbsp;&nbsp;".
        form_button('send_minus', self::$locale['PONT_239'], self::$locale['PONT_239']));
        echo "</div>";

        echo closeform();
        closeside();

        openside(self::$locale['PONT_240']);
        echo openform('alluser_form', 'post', FUSION_REQUEST);
    	echo form_text('point_point', self::$locale['PONT_236'], 0, [
            'required'    => TRUE,
    	    'type'        => 'number',
    	    'max_length'  => 5,
    	    'number_min'  => 1,
    	    'inner_width' => '100px',
    	    'inline'      => TRUE
            ]);

        echo form_text('log_descript', self::$locale['PONT_237'], '', [
            'required'   => TRUE,
            'max_length' => 200,
            'inline'     => TRUE
        ]);

        echo "<div class='text-center'>".(form_button('all_plus', self::$locale['PONT_238'], self::$locale['PONT_238'])."&nbsp;&nbsp;".
        form_button('all_minus', self::$locale['PONT_239'], self::$locale['PONT_239']));
        echo "</div>";
        echo closeform();
        closeside();

        $group = [0 => self::$locale['PONT_241']];
        $result = dbquery("SELECT group_id, group_name FROM ".DB_USER_GROUPS." ORDER BY group_name ASC ");
        if (dbrows($result)){
        	while ($l = dbarray($result)){
        		$group[$l['group_id']] = $l['group_name'];
        	}
        }

        if (count($csop) > 1) {
        	openside(self::$locale['PONT_242']);
        	echo openform('group_form', 'post', FUSION_REQUEST);
        	echo form_select('group', self::$locale['PONT_243'], 0, [
                'required'    => TRUE,
                'inline'      => TRUE,
                'allowclear'  => TRUE,
                'options'     => $group
            ]);
            echo form_text('point_point', self::$locale['PONT_236'], 0, [
                'required'    => TRUE,
                'type'        => 'number',
                'max_length'  => 5,
                'number_min'  => 1,
                'inner_width' => '100px',
                'inline'      => TRUE
            ]);

        echo form_text('log_descript', self::$locale['PONT_237'], '', [
            'required'   => TRUE,
            'max_length' => 200,
            'inline'     => TRUE
        ]);
            echo "<div class='text-center'>".(form_button('group_plus', self::$locale['PONT_238'], self::$locale['PONT_238'])."&nbsp;&nbsp;".
            form_button('group_minus', self::$locale['PONT_239'], self::$locale['PONT_239']));
            echo "</div>";
            echo closeform();
            closeside();
        }

    	closetable();
    }



}