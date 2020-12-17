<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: theme.php
| Author: PHPFusion Development Team
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
defined('IN_FUSION') || exit;

require_once INCLUDES.'theme_functions_include.php';

if (!defined('DARKCORE_LOCALE')) {
    if (file_exists(THEMES.'Darkcore/locale/'.LANGUAGE.'.php')) {
        define('DARKCORE_LOCALE', THEMES.'Darkcore/locale/'.LANGUAGE.'.php');
    } else {
        define('DARKCORE_LOCALE', THEMES.'Darkcore/locale/English.php');
    }
}

// Definitions

define('BOOTSTRAP', TRUE); // Enable Bootstrap, to turn it off set FALSE
// define('BOOTSTRAP4', TRUE); // Uncomment if using a BS4 Based template

define('FONTAWESOME', TRUE); // Enabled Font Awesome, Comment to disable
// define('ENTYPO', TRUE); // Uncomment to enable Entypo

// Setting to enable only 2 columns layout ( 2 Panels ) // We set this TRUE for most theme ports for now
define('THEME_2COL', TRUE); // Set TRUE to enable
define('THEME_SIDE', 'RIGHT'); // Enable LEFT or RIGHT side when 2COL is set to TRUE
define('THEME_BULLET', '&middot;'); // Compability for old panels, used here and there.


// Ready to use functions for content injections in menu, footers and/or panels as seen in this theme
require_once THEME.'theme_functions.php';

// Init the content rendering engine
function render_page() {
    // Load Locales
    $locale = fusion_get_locale('', DARKCORE_LOCALE);
    // Load Fusion Core Settings
    $settings = fusion_get_settings();

    // Adjust for SEO
    $file_path = str_replace(ltrim($settings['site_path'], '/'), '', preg_replace('/^\//', '', FUSION_REQUEST));
    if ($settings['site_seo'] && defined('IN_PERMALINK')) {
        $file_path = \PHPFusion\Rewrite\Router::getRouterInstance()->getCurrentURL();
    }

    $is_home = 'home.php' == $file_path;

    // Fusions Menu & links function, top header hack
    echo '<header class="'.($is_home ? 'main-header' : 'sub-header').'">';
        $collapse_icon = '<center><button type="button" class="btn btn-navbar navbar-toggle visible-xs" data-toggle="collapse" data-target="#menu_menu" aria-expanded="false" aria-controls="menu_menu"><i class="fa fa-bars"></i></button></center>';
        echo showsublinks('', '', [
            'id'               => 'menu',
            'container'        => TRUE,
            'nav_class'        => 'nav navbar-nav navbar-right primary',
            'grouping'         => TRUE,
            'links_per_page'   => 6,
            'custom_header'    => '<div class="navbar-header"><div class="navbar-brand"><a class="navbar-brand-link" href="'.BASEDIR.$settings['opening_page'].'"><img src="'.BASEDIR.$settings['sitebanner'].'" alt="'.$settings['sitename'].'"></a></div>'.$collapse_icon.'</div>',
            'html_pre_content' => user_menu() // Function at bottom.
        ]);

        // Render page content with fusion default functions & Map out the grid
        echo '<div class="container">';
            echo renderNotices(getNotices(['all', FUSION_SELF]));
        echo '</div>';

    echo '</header>';

    echo '<div class="container p-t-10"><section>';

        echo defined('AU_CENTER') && AU_CENTER ? AU_CENTER : '';

        echo showbanners(1);  // Custom banners 1 ( From Admin )

        echo '<div class="row">';
            $content = ['sm' => 12, 'md' => 12, 'lg' => 12];
            $left    = ['sm' => 3,  'md' => 2,  'lg' => 2];
            $right   = ['sm' => 3,  'md' => 3,  'lg' => 3];

            $left_side = TRUE;
            $right_side = TRUE;

            if (THEME_2COL == TRUE) {
                $left_side = THEME_SIDE == 'LEFT';
                $right_side = THEME_SIDE == 'RIGHT';
            }

            if ((defined('LEFT') && LEFT) && $left_side == TRUE) {
                $content['sm'] = $content['sm'] - $left['sm'];
                $content['md'] = $content['md'] - $left['md'];
                $content['lg'] = $content['lg'] - $left['lg'];
            }

            if ((defined('RIGHT') && RIGHT) && $right_side == TRUE) {
                $content['sm'] = $content['sm'] - $right['sm'];
                $content['md'] = $content['md'] - $right['md'];
                $content['lg'] = $content['lg'] - $right['lg'];
            }

            if ((defined('LEFT') && LEFT) && $left_side == TRUE) {
                echo '<div id="left-side" class="col-xs-12 col-sm-'.$left['sm'].' col-md-'.$left['md'].' col-lg-'.$left['lg'].'">';
                    echo defined('RIGHT') && RIGHT && $right_side == FALSE ? RIGHT : '';
                    echo defined('LEFT') && LEFT ? LEFT : '';
                echo '</div>';
            }

            echo '<div id="main-content" class="col-xs-12 col-sm-'.$content['sm'].' col-md-'.$content['md'].' col-lg-'.$content['lg'].'">';
                echo defined('U_CENTER') && U_CENTER ? U_CENTER : '';
                echo CONTENT;
                echo defined('L_CENTER') && L_CENTER ? L_CENTER : '';
                echo showbanners(2); // Custom banners 2 ( From Admin )
            echo '</div>';

            if ((defined('RIGHT') && RIGHT) && $right_side == TRUE) {
                echo '<div id="right-side" class="col-xs-12 col-sm-'.$right['sm'].' col-md-'.$right['md'].' col-lg-'.$right['lg'].'">';
                    echo defined('RIGHT') && RIGHT ? RIGHT : '';
                    echo defined('LEFT') && LEFT && $left_side == FALSE ? LEFT : '';
                echo '</div>';
            }

            echo defined('BL_CENTER') && BL_CENTER ? BL_CENTER : '';
        echo '</div>';

    echo '</section></div>';

    // Footer Content starts here
    echo '<footer>';
        echo '<div class="container spacer-md">';

            // Load the widget setting ( We have Phone default for showcase, not required )
            $theme_settings = get_theme_settings('Darkcore');

            // User Defined Links
            echo '<div class="row m-t-10">';
                echo defined('USER1') && USER1 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER1.'</div>' : '';
                echo defined('USER2') && USER2 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER2.'</div>' : '';
                echo defined('USER3') && USER3 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER3.'</div>' : '';
                echo defined('USER4') && USER4 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER4.'</div>' : '';
            echo '</div>';

            echo '<div class="row">';
                echo '<div class="col-footer footer-1 col-md-3">';
                    echo '<h2 class="footer-title">'.$locale['drk_005'].'</h2>';
                    echo '<div class="footer-content">';

                        echo nl2br($settings['description']);
                        echo '<br /><br />';

                        // Check if the Phone number have been set. ( Admin > System Admnin > Theme Manager > THEME > Manage Theme > Widget )
                        if (!empty($theme_settings['phone_number'])) {
                            echo '<i class="fa fa-phone"></i> '.$theme_settings['phone_number'].'<br />';
                        }

                        // Display site email with hide_email function
                        echo '<i class="fa fa-envelope"></i> '.hide_email($settings['siteemail']).'<br />';
                    echo '</div>';
                echo '</div>';

                // List sites gallery instead of non site content
                echo '<div class="col-footer footer-3 col-md-3">';
                    echo '<h2 class="footer-title">'.$locale['drk_006'].'</h2>';
                        echo '<div class="footer-content">';
                                render_latest_photos();
                    echo '</div>';
                echo '</div>';

                // List blogs instead of non site content
                echo '<div class="col-footer footer-2 col-md-3">';
                    echo '<h2 class="footer-title">'.$locale['drk_007'].'</h2>';
                    echo '<div class="footer-content">';
                        render_latest_blogs();
                    echo '</div>';
                echo '</div>';

                echo '<div class="col-footer footer-2 col-md-3">';
                    echo '<h2 class="footer-title">'.$locale['drk_008'].'</h2>';
                        echo '<div class="footer-content">';
                            render_latest_news();
                        echo '</div>';
                echo '</div>';
            echo '</div>';
        echo '</div>';

        // Add PHPFusion´s Core functions to footer.
        echo '<div class="bottom-footer"><div class="container"><div class="copyright text-center">';
            echo nl2br(parse_textarea($settings['footer'], FALSE));
            echo showFooterErrors();
            echo showcopyright('', TRUE).showprivacypolicy();
            echo '<br /><span>Darkcore by <a href="https://www.phpfusion.com/" target="_blank">PHPFusion Development Team</a></span>';
            if ($settings['rendertime_enabled'] == 1 || $settings['rendertime_enabled'] == 2) {
                echo '<br /><small>'.showrendertime().showMemoryUsage().'</small>';
            }

            echo '<br />'.showcounter();

        echo '</div></div></div>';
    echo '</footer>';
}

// Predefined Theme wrap functions in all of the sites sections
function opentable($title = FALSE, $class = '') {
    echo '<div class="panel panel-default opentable">';
    echo $title ? '<div class="panel-heading"><h4>'.$title.'</h4></div>' : '';
    echo '<div class="panel-body '.$class.'">';
}

function closetable() {
    echo '</div>';
    echo '</div>';
}

function openside($title = FALSE, $class = '') {
    echo '<div class="panel panel-default openside '.$class.'">';
    echo $title ? '<div class="panel-heading">'.$title.'</div>' : '';
    echo '<div class="panel-body">';
}

function closeside() {
    echo '</div>';
    echo '</div>';
}

/** Overrides from Core Standard-Templates **/
// You can read sent data with print_p($info) inside each override file

// The theme concept index page that is included inside our display_home function that automatically overrides Core home modules
//require_once THEME.'templates/home.php';

// The blog have been ported here as well to automatically override Core Blog system and use a more simple readable html
require_once THEME.'templates/blog.php';

// Our news have been ported here as well to automatically override Core News system and use a more simple readable html
require_once THEME.'templates/news.php';

// Our downloads been ported here as well to automatically override Core Downloads system and use a more simple readable html
require_once THEME.'templates/downloads.php';

// Our articles been ported here as well to automatically override Core Articles system and use a more simple & readable html
require_once THEME.'templates/articles.php';

// Our gallery been ported here as well to automatically override Core Gallery system and use a more simple & readable html
require_once THEME.'templates/gallery.php';

// Our message system have been ported here as well to automatically override Core Private Messages system and use a more simple & readable html
require_once THEME.'templates/messages.php';

// Our custom pages system have been ported here as well to automatically override Core CP system
require_once THEME.'templates/page.php';

// Our page error reporting system have been ported here as well to automatically override Core error system and use a more simple & readable html
require_once THEME.'templates/error.php';

// Our registration, gateway,lost password and login system have been ported here as well to automatically override Core functions for these systems and use a more simple & readable html
require_once THEME.'templates/auth.php';
