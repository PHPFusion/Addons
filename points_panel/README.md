# PHP-Fusion Point System
PHP-Fusion 9.03 Addons

installation as an infusion

Point info:
---
- \PHPFusion\Points\UserPoint::PointInfo(user_id, remov point or "");<br />
- \PHPFusion\Points\UserPoint::PointInfo(fusion_get_userdata('user_id'), 100);<br />
- \PHPFusion\Points\UserPoint::PointInfo(fusion_get_userdata('user_id'), 100) < 0) ? 'error' : "";<br />


add point
---
- \PHPFusion\Points\UserPoint::getInstance()->setPoint(user id, ["mod" => 1 = add point, 2 remov point, "point" => number 200, "messages" => "message text"]);<br />
//I increase it 200 point<br />
- \PHPFusion\Points\UserPoint::getInstance()->setPoint("", ["mod" => 1, "point" => 200, "messages" => "message text"]);<br />
// 3 id user increase 200 point<br />
- \PHPFusion\Points\UserPoint::getInstance()->setPoint(3, ["mod" => 1, "point" => 200, "messages" => "message text"]);<br />
//if the point does not multiply by a multiplier<br />
//default hollyday multiplier<br />
- \PHPFusion\Points\UserPoint::getInstance()->setPoint("", ["mod" => 1, "point" => 200, "messages" => "message text", 'hollyday'  => TRUE]);<br />

remov point
---
// 3 id user remov 200 point<br />
- \PHPFusion\Points\UserPoint::getInstance()->setPoint(3, ["mod" => 2, "point" => 200, "messages" => "message text"]);<br />
// I remov 200 point<br />
- \PHPFusion\Points\UserPoint::getInstance()->setPoint("", ["mod" => 2, "point" => 200, "messages" => "message text"]);<br />

example:
---
infusions/shoutbox_panel/shoutbox.inc
 line 147

                if (\defender::safe()) {
                    dbquery_insert(DB_SHOUTBOX, $this->data, empty($this->data['shout_id']) ? "save" : "update");
                    //add point
                    \PHPFusion\Points\UserPoint::getInstance()->setPoint("", ["mod" => 1, "point" => 100, "messages" => "for sending a message"]);

                    addNotice("success", empty($this->data['shout_id']) ? self::$locale['SB_shout_added'] : self::$locale['SB_shout_updated']);
                }

Bann 1 id user
---
- \PHPFusion\Points\UserPoint::getInstance()->SetPointBan(user Id, ['ban_mod' => 1, 'ban_start' => Start Ban (time), 'ban_stop' => Stop Ban (time), 'ban_text' => 'messages']);<br />
- \PHPFusion\Points\UserPoint::getInstance()->SetPointBan(1, ['ban_mod' => 1, 'ban_start' => '1546421200', 'ban_stop' => '1546423200', 'ban_text' => 'messages']);<br />
// 3 id user Bann<br />
- \PHPFusion\Points\UserPoint::getInstance()->SetPointBan(3, ['ban_mod' => 1, 'ban_start' => '1546421200', 'ban_stop' => '1546423200', 'ban_text' => 'messages']);<br />


Remove or Stop Ban:
---
- \PHPFusion\Points\UserPoint::getInstance()->SetPointBan(1, ['ban_mod' => 2, 'ban_stop' => Stop Ban (time - 5)]);<br />
- \PHPFusion\Points\UserPoint::getInstance()->SetPointBan(1, ['ban_mod' => 2, 'ban_stop' => '1546423200']);<br />
// 3 id user Stop Bann
- \PHPFusion\Points\UserPoint::getInstance()->SetPointBan(3, ['ban_mod' => 2, 'ban_stop' => '1546423200']);<br />

Time lock
---
````
$game = savetime - time();
?>
    <script type="text/javascript">
        display_cr(<?php echo $game ?>, "redseven", 3, "redseven.php");
    </script>
<?php
 "<b><div class='text-center' id='redseven'>00:00</div></b>\n";
````
