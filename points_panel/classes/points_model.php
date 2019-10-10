<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: classes/points_model.php
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

    protected $autogroup = [
        'pg_id'           => '',
        'pg_group_id'     => 0,
        'pg_group_points' => 0,
        'group_name'      => ''
    ];

    protected $default_options = [
        'mod'       => 1, //1 = add point, 2 = remov point
        'point'     => 0,
        'messages'  => '',
        'addtime'   => 0,
        'pricetype' => 0,  //0 = pricetype , 1 = more price
        'hollyday'  => FALSE  //TRUE = hollyday not multiplier , FALSE = hollyday multiplier
    ];

    protected $default_ban = [
        'ban_mod'   => 1, //1 = add ban, 2 = remov bann
        'ban_start' => 0,
        'ban_stop'  => 0,
        'ban_text'  => ''
    ];

	public static function PointsBank($userid) {
	    $userid = (isnum($userid) ? $userid : 0);
	    $result = dbquery("SELECT *
	        FROM ".DB_POINT_BANK."
	        WHERE pb_user_id = :userid
			".(multilang_table("PSP") ? " AND pb_language = '".LANGUAGE."'" : ''), [':userid' => $userid]
        );
        if (dbrows($result)) {
            $data = [];
            while ($bank = dbarray($result)) {
                if (!empty($bank['pb_loan_activ'])) {
                	$data['loan'][] = $bank;
                }
                if (!empty($bank['pb_interest_activ'])) {
               	    $data['interest'][] = $bank;
                }
            }
            return $data;
        }
        return FALSE;
    }

    public function Pointscachegroups() {
        static $groups_cache = NULL;
        if ($groups_cache === NULL) {
            $groups_cache = [];
            $result = dbquery("SELECT group_id, group_name FROM ".DB_USER_GROUPS." ORDER BY group_id ASC");
            while ($data = dbarray($result)) {
                $groups_cache[$data['group_id']] = $data['group_name'];
            }
        }
        return $groups_cache;
    }

    public function PointsGroups() {

        $groups_cache = [];
        $result = dbquery("SELECT group_id, group_name FROM ".DB_USER_GROUPS." ORDER BY group_id ASC");

        while ($data = dbarray($result)) {
            $groups_cache[$data['group_id']] = $data['group_name'];
        }
        return $groups_cache;
    }

    public function PointsGroupsform(array $user = [], $groups, $point) {
        foreach ($groups as $group) {
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
            ];
        	$autgroupuser = fusion_get_userdata();
            $userbind = [
                ':group' => $autgroupuser['user_groups'].".".$groupid,
                ':user'  => $autgroupuser['user_id']
            ];
            dbquery("UPDATE ".DB_POINT." SET point_group = :groups WHERE point_user = :users".(multilang_table("PSP") ? " AND point_language = '".LANGUAGE."'" : ''), $bind);
            dbquery("UPDATE ".DB_USERS." SET user_groups = :group WHERE user_id = :user", $userbind);
            $messages = sprintf(fusion_get_locale('PONT_313', ''), $groups[$groupid]['group_name']);
            addNotice('success', $messages);
        }
    }

    public static function CurrentSetup() {

        $settings = dbarray(dbquery("SELECT * FROM ".DB_POINT_ST."
            ".(multilang_table("PSP") ? "WHERE ps_language = '".LANGUAGE."'" : '')
        ));
        return $settings;
    }

    public static function GetCurrentUser($uid = NULL) {

	    $def_point = [
	        'point_id'        => '',
	        'point_user'      => $uid,
	        'point_point'     => 0,
	        'point_increase'  => 0,
	        'point_group'     => '',
	        'point_language'  => LANGUAGE
        ];

        $result = dbquery("SELECT *
            FROM ".DB_POINT."
            WHERE point_user = :userid
            ".(multilang_table("PSP") ? " AND point_language = '".LANGUAGE."'" : '')."
            LIMIT 0,1", [':userid' => $uid]
        );

        $point = [];
        if (dbrows($result)) {
            $point = dbarray($result);
        }

        $points = array_merge($def_point, $point);

        return $points;
    }


}
