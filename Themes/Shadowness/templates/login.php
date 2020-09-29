<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: login.php
| Author: Frederick MC Chan (Chan)
| Co-Author: RobiNN
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
function display_loginform($info = []) {
    global $theme;

    $theme->set_display_mode('single');
    $theme->right = FALSE;
    $theme->show_menu = FALSE;
    $theme->grid = 12;

    $locale = fusion_get_locale();
    $userdata = fusion_get_userdata();
    $settings = fusion_get_settings();
    $aidlink = fusion_get_aidlink();

    include THEME_LOCALE.'login.php';

    if (iMEMBER) {
        $msg_count = dbcount("(message_id)", DB_MESSAGES, "message_to='".$userdata['user_id']."' AND message_read='0' AND message_folder='0'");
        opentable('<div class="text-center">'.$userdata['user_name'].'</div>');
        echo "<div style='text-align:center'><br />\n";
        echo THEME_BULLET." <a href='".BASEDIR."edit_profile.php' class='side'>".$locale['global_120']."</a><br />\n";
        echo THEME_BULLET." <a href='".BASEDIR."messages.php' class='side'>".$locale['global_121']."</a><br />\n";
        echo THEME_BULLET." <a href='".BASEDIR."members.php' class='side'>".$locale['global_122']."</a><br />\n";
        if (iADMIN && (iUSER_RIGHTS != "" || iUSER_RIGHTS != "C")) {
            echo THEME_BULLET." <a href='".ADMIN."index.php".$aidlink."' class='side'>".$locale['global_123']."</a><br />\n";
        }
        echo THEME_BULLET." <a href='".BASEDIR."index.php?logout=yes' class='side'>".$locale['global_124']."</a>\n";
        if ($msg_count) {
            echo "<br /><br />\n";
            echo "<strong><a href='".BASEDIR."messages.php' class='side'>".sprintf($locale['global_125'], $msg_count);
            echo ($msg_count == 1 ? $locale['global_126'] : $locale['global_127'])."</a></strong>\n";
        }
        echo "<br /><br /></div>\n";
    } else {
        opentable($locale['sh_0100'].' '.$settings['sitename']);
        echo "<p>".$locale['sh_0101']."</p>\n";

        echo $info['open_form'];
        echo $info['user_name'];
        echo $info['user_pass'];
        echo $info['remember_me'];
        echo $info['login_button'];
        echo $info['registration_link']."<br/><br/>";
        echo $info['forgot_password_link']."<br/><br/>";
        echo $info['close_form'];

        if (!empty($info['connect_buttons'])) {
            foreach ($info['connect_buttons'] as $mhtml) {
                echo $mhtml;
            }
        }

        if ($settings['enable_registration']) {
            echo "<p>".strtr($locale['global_105'], ['[LINK]' => "<a href='".BASEDIR."register.php'>", '[/LINK]' => "</a>\n"])."</p>\n";
        }
        echo strtr($locale['global_106'], ['[LINK]' => "<a href='".BASEDIR."lostpassword.php'>", '[/LINK]' => "</a>"]);
        echo "</div></div>\n";
    }
    closetable();
}
