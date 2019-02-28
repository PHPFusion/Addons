<?php
namespace PHPFusion\Points;

class PointsPointsAdmin extends PointsModel {
    private static $instance = NULL;
    private static $locale = [];

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
        self::PointsForm();
        self::PointsDisplay();
	}

    private function PointsForm() {
        $this->send_plus = filter_input(INPUT_POST, 'send_plus', FILTER_DEFAULT);
        $this->send_minus = filter_input(INPUT_POST, 'send_minus', FILTER_DEFAULT);

        if (!empty($this->send_plus) or !empty($this->send_minus)) {
            self::PointsFormSend();
        }

        $this->all_plus = filter_input(INPUT_POST, 'all_plus', FILTER_DEFAULT);
        $this->all_minus = filter_input(INPUT_POST, 'all_minus', FILTER_DEFAULT);
        if (!empty($this->all_plus) or !empty($this->all_minus)) {
            self::PointsFormAllSend();
        }

        $this->group_plus = filter_input(INPUT_POST, 'group_plus', FILTER_DEFAULT);
        $this->group_minus = filter_input(INPUT_POST, 'group_minus', FILTER_DEFAULT);
        if (!empty($this->group_plus) or !empty($this->group_minus)) {
            self::PointsFormGroupSend();
        }
    }

    private function PointsFormSend() {
        $this->point_user = filter_input(INPUT_POST, 'point_user', FILTER_VALIDATE_INT);
        $this->point_point = filter_input(INPUT_POST, 'point_point', FILTER_VALIDATE_INT);
        $this->log_descript = filter_input(INPUT_POST, 'log_descript', FILTER_DEFAULT);

        $point_user = form_sanitizer($this->point_user, 0, 'point_user');
        $point_point = form_sanitizer($this->point_point, 0, 'point_point');
        $log_descript = form_sanitizer($this->log_descript, '', 'log_descript');
        $mod = (!empty($this->send_plus) ? 1 : (!empty($this->send_minus) ? 2 : 0));
        $pointinfo = $mod == '2' ? \PHPFusion\Points\UserPoint::getInstance()->PointInfo($point_user, $point_point) : 10;

        $max_rows = dbcount("(user_id)", DB_USERS, "user_id=:userid", [':userid' => $point_user]);
        if ($max_rows && $mod > 0 && $pointinfo > 0 && \defender::safe()) {
            \PHPFusion\Points\UserPoint::getInstance()->setPoint($point_user, ["mod" => $mod, "point" => $point_point, "messages" => $log_descript]);
            addNotice('success', $log_descript);
            redirect(FUSION_REQUEST);
        }
    }

    private function PointsFormAllSend() {
        $this->point_point = filter_input(INPUT_POST, 'point_point', FILTER_VALIDATE_INT);
        $this->log_descript = filter_input(INPUT_POST, 'log_descript', FILTER_DEFAULT);

        $point_point = form_sanitizer($this->point_point, 0, 'point_point');
        $log_descript = form_sanitizer($this->log_descript, '', 'log_descript');
        $mod = (!empty($this->all_plus) ? 1 : (!empty($this->all_minus) ? 2 : 0));

        if (\defender::safe()) {
            $messages = $mod == 1 ? self::$locale['PONT_231'] : self::$locale['PONT_230'];
            $result = dbquery("SELECT p.*, pu.*
                FROM ".DB_POINT." AS p
                LEFT JOIN ".DB_USERS." AS pu ON pu.user_id = p.point_user
                WHERE user_status = :status AND user_lastvisit != :lastvisit
                ORDER BY user_name ASC
            ", [':status' => '0', ':lastvisit' => '']);

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

    private function PointsFormGroupSend() {
        $this->group = filter_input(INPUT_POST, 'group', FILTER_VALIDATE_INT);
        $this->point_point = filter_input(INPUT_POST, 'point_point', FILTER_VALIDATE_INT);
        $this->log_descript = filter_input(INPUT_POST, 'log_descript', FILTER_DEFAULT);

        $group = form_sanitizer($this->group, 0, 'group');
        $point_point = form_sanitizer($this->point_point, 0, 'point_point');
        $log_descript = form_sanitizer($this->log_descript, '', 'log_descript');
        $mod = (!empty($this->group_plus) ? 1 : (!empty($this->group_minus) ? 2 : 0));

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
        	while ($groupdata = dbarray($result)){
        		$group[$groupdata['group_id']] = $groupdata['group_name'];
        	}
        }

        if (count($group) > 1) {
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