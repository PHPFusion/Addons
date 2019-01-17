<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: points_panel/classes/points_model.php
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

class PointsModel {

    protected $default_options = [
        'mod'       => 1, //1 = add point, 2 = remov point
        'point'     => 0,
        'messages'  => '',
        'addtime'   => '',
        'pricetype' => 0  //0 = pricetype , 1 = more price
    ];

    protected $default_ban = [
        'ban_mod'   => 1, //1 = add ban, 2 = remov bann
        'ban_start' => 0,
        'ban_stop'  => 0,
        'ban_text'  => ''
    ];

    public function globinf() {
        //$this->diary_filter = 0;
        //$this->save = (string)filter_input(INPUT_POST, 'savesettings', FILTER_DEFAULT);
        $this->diary_filter = filter_input(INPUT_POST, 'diary_filter', FILTER_DEFAULT);
        $this->deleteall = filter_input(INPUT_GET, 'deleteall', FILTER_DEFAULT);
        $this->del = filter_input(INPUT_GET, 'del', FILTER_DEFAULT);
        $this->np = filter_input(INPUT_GET, 'np', FILTER_DEFAULT);
        $this->logid = filter_input(INPUT_GET, 'log_id', FILTER_VALIDATE_INT);
        $this->rowstart = filter_input(INPUT_GET, 'rowstart', FILTER_VALIDATE_INT);
    }

    public function PointsGroups() {
        $groups_cache = [];
        $result = dbquery("SELECT pg.*, g.group_name
            FROM ".DB_USER_GROUPS." AS g
            LEFT JOIN ".DB_POINT_GROUP." AS pg ON pg.pg_group_id = g.group_id
            ORDER BY group_id ASC");
        while ($data = dbarray($result)) {
            $groups_cache[$data['pg_group_id']] = $data;
        }
        return $groups_cache;
    }

    public function PointsGroupsform(array $user = [], $groups, $point) {
        foreach ($groups as $key => $group) {
            if (!preg_match("(^\.{$group['pg_group_id']}$|\.{$group['pg_group_id']}\.|\.{$group['pg_group_id']}$)", $user['point_group'])) {
                if ($point >= $group['pg_group_points']) {
                    self::addautogroup($user, $group['pg_group_id'], $groups);
                    return TRUE;
                }
            }
        }
        return false;
    }

    private static function addautogroup(array $user = [], $groupid, $groups) {
        if (!in_array($groupid, explode(".", $user['point_group']))) {
            $bind = [
                ':groups'   => $user['point_group'].".".$groupid,
                ':users'    => $user['point_user'],
                ':language' => LANGUAGE
            ];
        	$autgroupuser = fusion_get_userdata();
            $userbind = [
                ':group' => $autgroupuser['user_groups'].".".$groupid,
                ':user'  => $autgroupuser['user_id']
            ];
            dbquery("UPDATE ".DB_POINT." SET point_group=:groups WHERE point_user=:users".(multilang_table("PSP") ? " AND point_language=:language" : ''), $bind);
            dbquery("UPDATE ".DB_USERS." SET user_groups=:group WHERE user_id=:user", $userbind);
            $messages = sprintf(fusion_get_locale('PONT_313', ''), $groups[$groupid]['group_name']);
            addNotice('success', $messages);
        }
    }

    public static function CurrentSetup() {

        $result = dbquery("SELECT *
            FROM ".DB_POINT_ST."
            ".(multilang_table("PSP") ? " WHERE ps_language=:language" : ''), [':language' => LANGUAGE]);

        $settings = dbarray($result);

        return $settings;
    }
}
