# PHP-Fusion Point System
PHP-Fusion 9 Addons

installation as an infusion

Point info:
 PHPFusion\Points\UserPoint::PointInfo(user_id, remov point or "");
 PHPFusion\Points\UserPoint::PointInfo(fusion_get_userdata('user_id'), 100);
 PHPFusion\Points\UserPoint::PointInfo(fusion_get_userdata('user_id'), 100) < 0) ? error : "";


add point

 PHPFusion\Points\UserPoint::getInstance()->setPoint(user id, ["mod" => 1 = add point, 2 remov point, "point" => number 200, "messages" => "message text"]);
 PHPFusion\Points\UserPoint::getInstance()->setPoint("", ["mod" => 1, "point" => 200, "messages" => "message text"]);//I increase it 200 point
 PHPFusion\Points\UserPoint::getInstance()->setPoint(3, ["mod" => 1, "point" => 200, "messages" => "message text"]); // 3 id user increase 200 point

remov point
 PHPFusion\Points\UserPoint::getInstance()->setPoint(3, ["mod" => 2, "point" => 200, "messages" => "message text"]); // 3 id user remov 200 point
 PHPFusion\Points\UserPoint::getInstance()->setPoint("", ["mod" => 2, "point" => 200, "messages" => "message text"]); // I remov 200 point

example:
infusions/shoutbox_panel/shoutbox.inc
 line 147

                if (\defender::safe()) {
                    dbquery_insert(DB_SHOUTBOX, $this->data, empty($this->data['shout_id']) ? "save" : "update");
                    //add point
                    PHPFusion\Points\UserPoint::getInstance()->setPoint("", ["mod" => 1, "point" => 100, "messages" => "for sending a message"]);

                    addNotice("success", empty($this->data['shout_id']) ? self::$locale['SB_shout_added'] : self::$locale['SB_shout_updated']);
                }

Bann 1 id user
 \PHPFusion\Points\UserPoint::getInstance()->SetPointBan(user Id, ['ban_mod' => 1, 'ban_start' => Start Ban (time), 'ban_stop' => Stop Ban (time), 'ban_text' => 'messages'])
 \PHPFusion\Points\UserPoint::getInstance()->SetPointBan(1, ['ban_mod' => 1, 'ban_start' => '1546421200', 'ban_stop' => '1546423200', 'ban_text' => 'messages'])
 \PHPFusion\Points\UserPoint::getInstance()->SetPointBan(3, ['ban_mod' => 1, 'ban_start' => '1546421200', 'ban_stop' => '1546423200', 'ban_text' => 'messages']); // 3 id user Bann


Remove or Stop Ban:
 \PHPFusion\Points\UserPoint::getInstance()->SetPointBan(1, ['ban_mod' => 2, 'ban_stop' => Stop Ban (time - 5)])
 \PHPFusion\Points\UserPoint::getInstance()->SetPointBan(1, ['ban_mod' => 2, 'ban_stop' => '1546423200'])
 \PHPFusion\Points\UserPoint::getInstance()->SetPointBan(3, ['ban_mod' => 2, 'ban_stop' => '1546423200']); // 3 id user Stop Bann
