<?php
namespace PHPFusion\Points;

class PointsAutogroupAdmin extends PointsModel {
    private static $instance = NULL;
    private static $locale = [];
    private $autogroup = [];

    public function __construct() {
        $this->settings = self::CurrentSetup();
        $this->group_cache = self::PointsGroups();
        self::$locale = fusion_get_locale("", POINT_LOCALE);
        $this->link = FUSION_REQUEST."&ref=edit";

    }

    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new static();
        }
       return self::$instance;
    }

    public function displayAdmin() {
        $this->autogroup = [
            'pg_id'           => '',
            'pg_group_id'     => 0,
            'pg_group_points' => 0,
            'group_name' => ''
        ];
        self::SaveGroupForm();
        echo self::groupForm();
    }

    private function groupForm() {
        $html = openform('editform', 'post', $this->link, ['class' => 'spacer-xs']);
        $user_groups_opts = [0 => 'select'];
        foreach ($this->group_cache as $key => $group) {
            if($group['pg_group_id']) {
                $user_groups_opts[$key] = $group['group_name'].' ( '.$group['pg_group_points'].')';
            }
       }

        $html .= form_select('group_name', self::$locale['PONT_178'], $this->autogroup['pg_group_id'], [
            'required'    => TRUE,
    	    'inline'      => TRUE,
    	    'options'     => $user_groups_opts
    	]);
        $this->ref = filter_input(INPUT_GET, 'ref', FILTER_DEFAULT);
        if (!empty($this->ref)) {
    	$html .= form_text('pg_group_points', self::$locale['PONT_179'], $this->autogroup['pg_group_points'], [
            'required'    => TRUE,
    	    'type'        => 'number',
    	    'max_length'  => 5,
    	    'number_min'  => 1,
    	    'inner_width' => '100px',
    	    'inline'      => TRUE
    	]);
        $html .= form_button('save_group', self::$locale['save'], self::$locale['save'], ['class' => 'btn-primary']);

        }
        empty($this->ref) ? $html .= form_button('edit_group', self::$locale['edit'], self::$locale['edit'], ['class' => 'btn-primary']) : '';
        $html .= closeform();

        return $html;
    }

    private function SaveGroupForm() {
        $this->save_group = filter_input(INPUT_POST, 'save_group', FILTER_DEFAULT);
        $this->edit_group = filter_input(INPUT_POST, 'edit_group', FILTER_DEFAULT);
        if (!empty($this->save_group)) {
            $this->group_name = filter_input(INPUT_POST, 'group_name', FILTER_DEFAULT);
            $this->pg_group_points = filter_input(INPUT_POST, 'pg_group_points', FILTER_VALIDATE_INT);
            $this->autogroup = $this->group_cache[$this->group_name];

            $group = form_sanitizer($this->group_name, 0, 'group_name');
            $pg_group_points = form_sanitizer($this->pg_group_points, 0, 'pg_group_points');
            if (\defender::safe()) {
        	    $savegroup = [
        	        'pg_id'           => $this->autogroup['pg_id'],
        	        'pg_group_id'     => $group,
        	        'pg_group_points' => $pg_group_points
        	    ];
                dbquery_insert(DB_POINT_GROUP, $savegroup, empty($this->autogroup['pg_id']) ? 'save' : 'update');
                addNotice('success', empty($this->autogroup['pg_id']) ? self::$locale['PONT_314'] : self::$locale['PONT_315']);
                redirect(clean_request('', ['ref'], FALSE));
            }
        }

        if (!empty($this->edit_group)) {
            $this->group_name = filter_input(INPUT_POST, 'group_name', FILTER_VALIDATE_INT);
            $this->autogroup = $this->group_cache[$this->group_name];
        }
    }





}