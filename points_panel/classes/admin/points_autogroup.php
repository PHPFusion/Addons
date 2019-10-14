<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: classes/admin/points_autogroup.php
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

class PointsAutogroupAdmin extends PointsModel {
    private static $instance = NULL;
    public $settings = [];
    public $group_cache = [];
    private $link;


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
        set_title(self::$locale['PSP_M07']);

        self::SaveGroupForm();
        echo self::groupForm();
    }

    private function groupForm() {
        $html = openform('editform', 'post', $this->link, ['class' => 'spacer-xs']);
        $user_groups_opts = [0 => self::$locale['PSP_G00']];
        foreach ($this->group_cache as $key => $group) {
            if($group) {
                $user_groups_opts[$key] = $group;
            }
        }
        if (count($user_groups_opts) > 0) {
            $ref = filter_input(INPUT_GET, 'ref', FILTER_DEFAULT);
            $html .= form_select('group_name', self::$locale['PSP_G01'], $this->autogroup['pg_group_id'], [
                'required' => TRUE,
                'inline'   => TRUE,
                'options'  => $user_groups_opts
            ]);
            if (!empty($ref)) {
                $html .= form_text('pg_group_points', self::$locale['PSP_G02'], $this->autogroup['pg_group_points'], [
                    'required'    => TRUE,
                    'type'        => 'number',
                    'max_length'  => 5,
                    'number_min'  => 1,
                    'inner_width' => '100px',
                    'inline'      => TRUE
                ]);
                $html .= form_button('save_group', self::$locale['save'], self::$locale['save'], ['class' => 'btn-primary']);
            }
            empty($ref) ? $html .= form_button('edit_group', self::$locale['edit'], self::$locale['edit'], ['class' => 'btn-primary']) : '';
            $html .= closeform();
            return $html;
        }
        return "<div class='well text-center'>".self::$locale['PSP_G03']."</div>";
    }

    private function SaveGroupForm() {
        $save_group = filter_input(INPUT_POST, 'save_group', FILTER_DEFAULT);
        $edit_group = filter_input(INPUT_POST, 'edit_group', FILTER_DEFAULT);
        if (!empty($save_group)) {

            $group = form_sanitizer(filter_input(INPUT_POST, 'group_name', FILTER_VALIDATE_INT), 0, 'group_name');
            $pg_group_points = form_sanitizer(filter_input(INPUT_POST, 'pg_group_points', FILTER_VALIDATE_INT), 0, 'pg_group_points');
            if (\defender::safe()) {
        	    $savegroup = [
                    'pg_id'           => $this->autogroup['pg_id'],
                    'pg_group_id'     => $group,
                    'pg_group_points' => $pg_group_points
        	    ];
                dbquery_insert(DB_POINT_GROUP, $savegroup, empty($this->autogroup['pg_id']) ? 'save' : 'update');
                addNotice('success', empty($this->autogroup['pg_id']) ? self::$locale['PSP_G04'] : self::$locale['PSP_G05']);
                redirect(clean_request('', ['ref'], FALSE));
            }
        }

        if (!empty($edit_group)) {
            $group_name = filter_input(INPUT_POST, 'group_name', FILTER_VALIDATE_INT);

            $this->autogroup['group_name'] = $this->group_cache[$group_name];
            $this->autogroup['pg_group_id'] = $group_name;
        }
    }
}