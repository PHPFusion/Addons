<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2010 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: cv_birthdays_today_panel.php
| Author: Chubatyj Vitalij (Rizado)
| Web: http://chubatyj.ru/
| E-mail: v.chubatyj@yandex.ru
| XMPP: v.chubatyj@yandex.ru
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined("IN_FUSION")) {
    die("Access Denied");
}

if (file_exists(INFUSIONS."cv_birthdays_today_panel/locale/".$settings['locale'].".php")) {
    include INFUSIONS."cv_birthdays_today_panel/locale/".$settings['locale'].".php";
} else {
    include INFUSIONS."cv_birthdays_today_panel/locale/English.php";
}

openside($locale['btp_001']);
$result = dbquery("SELECT user_id, user_name, user_birthdate FROM ".DB_USERS." WHERE user_birthdate LIKE '%-".showdate("%m-%d", time())."' ORDER BY user_level DESC, user_id");
if (dbrows($result)) {
    $birthdays = "";
    while ($data = dbarray($result)) {
        $birthdays .= $birthdays ? ", " : "";
        $bdate = explode("-", $data['user_birthdate']);
        $birthdays .= "<a href='".BASEDIR."profile.php?lookup=".$data['user_id']."'>".$data['user_name']." (".(showdate("%Y", time()) - $bdate[0]).")</a>";
    }
    echo $birthdays;
} else {
    echo $locale['btp_002'];
}
closeside();
?>
