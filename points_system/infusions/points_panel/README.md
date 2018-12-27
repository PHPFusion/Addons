# PHP-Fusion Point System
PHP-Fusion 9 Addons

installation as an infusion

Point info:
 PHPFusion\Points\UserPoint::PointInfo(user_id, remov point or "");
 PHPFusion\Points\UserPoint::PointInfo(fusion_get_userdata('user_id'), 100);
 PHPFusion\Points\UserPoint::PointInfo(fusion_get_userdata('user_id'), 100) < 0) ? error : "";


add point

 PHPFusion\Points\UserPoint::getInstance()->setPoint(user id, ["mod" => 1 = add point, 2 remov point, "point" => number 200, "messages" => "message text"]);<br />
 PHPFusion\Points\UserPoint::getInstance()->setPoint("", ["mod" => 1, "point" => 200, "messages" => "message text"]);//I increase it 200 point<br />
 PHPFusion\Points\UserPoint::getInstance()->setPoint(3, ["mod" => 1, "point" => 200, "messages" => "message text"]); // 3 id user increase 200 point<br />

remov point
 PHPFusion\Points\UserPoint::getInstance()->setPoint(3, ["mod" => 2, "point" => 200, "messages" => "message text"]); // 3 id user remov 200 point<br />
 PHPFusion\Points\UserPoint::getInstance()->setPoint("", ["mod" => 2, "point" => 200, "messages" => "message text"]); // I remov 200 point<br />

example:
infusions/shoutbox_panel/shoutbox.inc<br />
 line 147

                if (\defender::safe()) {
                    dbquery_insert(DB_SHOUTBOX, $this->data, empty($this->data['shout_id']) ? "save" : "update");
                    //add point
                    PHPFusion\Points\UserPoint::getInstance()->setPoint("", ["mod" => 1, "point" => 100, "messages" => "for sending a message"]);

                    addNotice("success", empty($this->data['shout_id']) ? self::$locale['SB_shout_added'] : self::$locale['SB_shout_updated']);
                }
