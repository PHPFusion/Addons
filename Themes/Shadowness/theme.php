<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: theme.php
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
require_once INCLUDES."theme_functions_include.php";

/**
 * Class Producer
 */
class Producer {
    /* Theme properties */
    public $display_mode = 'full-grid'; // canvas for photo albums
    public $max_width = FALSE;
    public $show_menu = TRUE;
    public $sub_width_blaster = TRUE;
    public $xs_width = 12;
    public $sm_width = 3;
    public $md_width = 3;
    public $lg_width = 2;
    public $title_array = []; // set title array collection
    public $left_off = FALSE;
    public $right = TRUE;
    public $grid = '';

    /** How to embed template ?
     * 1. add same filename in templates/ folder, add your filename in $crossover array, and use render_template(); in the custom template.
     * 2. add same filename in templates/ folder, add your filename in $supported_template, and redeclare function used in the original file to output and recieve $info.
     */

    /* Supported template documentation - MVC
     * Function : to bypass core output and replace with theme's own template output
     * override_page is multidimensional array format.
     * Example: $crossover = array('login.php', 'register.php');
     */
    private $supported_template = ['home.php', 'login.php'];

    /* Cross Over documentation - NON MVC
     * Function : to bypass core output and replace with theme's own template output
     * override_page is multidimensional array format.
     * Example: $crossover = array('login.php', 'register.php');
     */

    public $crossover = [];

    /**
     * Bootstrap main content width calculation.
     * Required to run theme settings injections.
     *
     * @return string
     */
    private function get_main_span() {
        $this->lg_width = $this->max_width ? 2 : 3;

        $count = 0;
        $count = ((defined('LEFT') && LEFT !== '') && ($this->left_off == FALSE)) ? $count + 1 : $count;
        $count = ((defined('RIGHT') && RIGHT !== '') && ($this->right == TRUE)) ? $count + 1 : $count;

        if ($count > 0) {
            $this->sub_width_blaster = FALSE;
        }

        $xs_width = 12;
        $sm_width = 12;
        $md_width = 12;
        $lg_width = 12;

        for ($i = 0; $i < $count; $i++) {
            $xs_width = 12 - $this->xs_width;
            $sm_width = 12 - $this->sm_width;
            $md_width = 12 - $this->md_width;
            $lg_width = 12 - $this->lg_width;
        }

        $xs_width = ($xs_width <= 0) ? 12 : $xs_width;
        $sm_width = ($sm_width <= 0) ? 12 : $sm_width;
        $md_width = ($md_width <= 0) ? 12 : $md_width;
        $lg_width = ($lg_width <= 0) ? 12 : $lg_width;

        if (!empty($this->grid)) {
            $xs_width = 12;
            $sm_width = 12;
            $md_width = 12;
            $lg_width = 12;
        }

        return "col-xs-".$xs_width." col-sm-".$sm_width." col-md-".$md_width." col-lg-".$lg_width;
    }

    public function __construct() {
        define('THEME_BULLET', "&middot;");
        define('THEME_LOCALE', THEME.'templates/locale/'.LOCALESET);
    }

    /**
     * Toggler switches. 5 styles.
     * Set before call opentable(), or openside();
     *
     * @param $mode
     */
    public function set_display_mode($mode) {
        $this->display_mode = 'canvas';
        $available_modes = [
            'canvas', // for photography albums
            'full-grid',
            'single',
            'view',
            'comment',
        ];

        if (in_array($mode, $available_modes)) {
            $this->display_mode = $mode;
        }
    }

    /**
     * You can include a 2nd template using this.
     */
    public function template_loader() {
        foreach ($this->supported_template as $template_name) {
            if (file_exists(THEME.'/templates/'.$template_name)) {
                include THEME.'/templates/'.$template_name;
            }
        }
    }

    /**
     * Shadowness main menu bar.
     * The flash and dancing lights.
     */
    private function display_header() {
        $userdata = fusion_get_userdata();
        $settings = fusion_get_settings();
        $locale = fusion_get_locale();

        add_to_jquery("
            $('#header .dropdown').hover(
                function() { $(this).addClass('open'); },
                function() { $(this).removeClass('open'); }
            );
        ");
        ?>
        <div id='header'>
            <div class='menu'>
                <span class='menu-light' style='left:15px;'></span>
                <ul>
                    <li><a class='logo' href='<?php echo BASEDIR.'index.php'; ?>'><img src='<?php echo BASEDIR.$settings['sitebanner'] ?>' alt='<?php echo $settings['sitename'] ?>'></a></li>
                    <li class='search'><?php
                        echo openform('searchform', 'post', BASEDIR.'search.php?stype=all', [
                            'class'      => '',
                            'remote_url' => $settings['site_path']."search.php"
                        ]);
                        echo form_text('search_field', '', '', ['width' => '100px', 'class' => 'm-t-0', 'placeholder' => $locale['search']]);
                        echo closeform(); ?></li>

                    <?php

                    if (iMEMBER) {
                        echo "<li class='menu-2'><a href='".BASEDIR."profile.php?lookup=".$userdata['user_id']."'>".$locale['profile']."</a></li>";
                        ?>
                        <li class='dropdown menu-3'><a class='dropdown-toggle' data-toggle='dropdown'><i class='fa fa-star text-smaller'></i></a>
                            <ul class='dropdown-menu'>
                                <?php if (iMEMBER) {
                                    echo "<li><a href='".BASEDIR."edit_profile.php'>".$locale['UM080']."</a></li>";
                                } ?>
                                <?php if (iADMIN) {
                                    global $aidlink;
                                    echo "<li><a href='".ADMIN.$aidlink."'>".$locale['global_123']."</a></li>";
                                } ?>

                            </ul>
                        </li>
                    <?php }
                    $languages = fusion_get_enabled_languages();

                    if (count($languages) > 1) {
                        echo '<li class="dropdown language-switcher">';
                            echo '<a href="#" class="dropdown-toggle pointer" data-toggle="dropdown" title="'.LANGUAGE.'">';
                                echo '<i class="fa fa-globe"></i> ';
                                echo '<img src="'.BASEDIR.'locale/'.LANGUAGE.'/'.LANGUAGE.'-s.png" alt="'.translate_lang_names(LANGUAGE).'"/>';
                                echo '<span class="caret"></span>';
                            echo '</a>';

                            echo '<ul class="dropdown-menu">';
                                foreach ($languages as $language_folder => $language_name) {
                                    echo '<li><a class="display-block" href="'.clean_request('lang='.$language_folder, ['lang'], FALSE).'">';
                                        echo '<img class="m-r-5" src="'.BASEDIR.'locale/'.$language_folder.'/'.$language_folder.'-s.png" alt="'.$language_folder.'"/> ';
                                        echo $language_name;
                                    echo '</a></li>';
                                }
                            echo '</ul>';
                        echo '</li>';
                    }
                ?>
                </ul>
                <ul style="left: inherit;right: 0;" class="pull-right">
                    <?php
                    if (!iMEMBER) {
                        echo "<li class='usermenu'><a class='login' href='".BASEDIR."login.php'>".$locale['UM060']."</a></li>";
                        echo "<li class='usermenu'><a class='signup' href='".BASEDIR."register.php'>".$locale['global_107']."</a></li>";
                    } else {
                        echo "<li class='usermenu'><a class='signup' href='".BASEDIR."index.php?logout=yes'>".$locale['global_124']."</a></li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
        <?php
        add_to_footer("<script src='".THEME."jquery/sn.dancinglight.min.js'></script>");
    }

    public function sub_horizontal_nav($title, array $options = []) {
        $settings = fusion_get_settings();

        $options += [
            'get' => !empty($options['get']) ? $options['get'] : 'section',
        ];
        ?>
        <div class='subalbums'></div>
        <div class='subalbums'>
            <ul class='subalbum'>
                <?php
                if (is_array($title) && !empty($title)) {
                    $i = 0;
                    foreach ($title as $info) {
                        // if no request default is active
                        //$url = str_replace('../', '', $info['url']);
                        $url = str_replace($settings['site_path'], '', $info['url']);
                        $default_active = !isset($_GET[$options['get']]) && $i == 0;
                        // if request matches current url
                        $secondary_active = $_SERVER['REQUEST_URI'] == $settings['site_path'].$url;
                        // has get but belogns to other set, set default active.
                        $tertiary_active = isset($_GET[$options['get']]) && !in_array($_GET[$options['get']], $this->title_array) && $i == 0;
                        echo "<li><a ".($default_active || $secondary_active || $tertiary_active ? " class='active'" : "")." href='".$info['url']."' title='".$info['title']."'>".$info['title']."</a></li>\n";
                        $i++;
                    }
                }
                ?>
            </ul>
        </div> <!-- is tags -->
        <?php
    }

    /*
     * Main
     */
    public function display_content() {
        $settings = fusion_get_settings();

        if (in_array(FUSION_SELF, $this->crossover)) {
            include THEME.'templates/'.FUSION_SELF;
        }

        switch ($this->display_mode) {
            case 'canvas':
                $main_container_css = 'main full';
                break;
            case 'full-grid':
                $main_container_css = 'view home';
                break;
            case 'single':
                $main_container_css = 'single';
                break;
            case 'view':
                $main_container_css = 'view';
                break;
            default:
                $main_container_css = 'main full';
        }
        $main_container_css .= " ".self::get_main_span();

        echo ($this->max_width ? "" : $this->display_mode == 'single') ? "" : '<div class="container">'; ?>
        <div class='wrapper row equalize' <?php echo ($this->max_width ? "style='width:95%'" : $this->display_mode == 'single') ? "style='max-width:640px'" : '' ?>>
            <?php
            echo defined('AU_CENTER') && AU_CENTER ? AU_CENTER : '';
            echo showbanners(1);
            ?>
            <div class='<?php echo $main_container_css ?>'<?php echo $this->sub_width_blaster ? " style='width:100%'" : "" ?>>
                <?php if ($this->show_menu) {
                    echo showsublinks('', 'head menu navbar-default', ['logo' => $settings['sitename'], 'show_header' => TRUE])."\n";
                }
                ?>
                <div class='overflow-hide'>
                    <?php
                        echo CONTENT;
                    ?>
                </div>
            </div>
            <?php
            if ((defined('RIGHT') && RIGHT !== '') && $this->right == TRUE) {
                echo "<div class='sidebar home col-xs-".$this->xs_width." col-sm-".$this->sm_width." col-md-".$this->md_width." col-lg-".$this->lg_width."'>";
                    echo RIGHT;
                    echo defined('LEFT') && LEFT ? LEFT : '';
                echo "</div>";
            }

            echo '<div class="clear"></div>';
            echo defined('BL_CENTER') && BL_CENTER ? BL_CENTER : '';
            echo showbanners(2);

            if ((defined('USER1') && USER1) ||(defined('USER2') && USER2) || (defined('USER3') && USER3) || (defined('USER4') && USER4)) {
                echo '<div class="row">';
                echo defined('USER1') && USER1 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER1.'</div>' : '';
                echo defined('USER2') && USER2 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER2.'</div>' : '';
                echo defined('USER3') && USER3 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER3.'</div>' : '';
                echo defined('USER4') && USER4 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER4.'</div>' : '';
                echo '</div>';
            }
            ?>
        </div>

        <?php echo ($this->max_width ? "" : $this->display_mode == 'single') ? "" : '</div>';

        echo '<div class="text-center">';
        echo showFooterErrors();

        if ($settings['rendertime_enabled'] == 1 || $settings['rendertime_enabled'] == 2) {
            echo '<br/><span class="small">'.showrendertime().showMemoryUsage().'</span>';
        }
        echo '</div>';
    }

    /**
     * Static footer for the theme
     */
    public function display_footer() {
        $locale = fusion_get_locale();
        $settings = fusion_get_settings();
        ?>
        <div id="footer" style="bottom: 0;">
            <div class="copyright display-inline-block">
                <ul class="display-inline-block">
                    <li><a href="<?php echo THEME."about.php" ?>"><?php echo $locale['about']; ?></a></li>

                    <?php
                    $faq = function_exists('infusion_exists') ? infusion_exists('faq') : db_exists(DB_PREFIX.'faqs');
                    if ($faq) {
                        echo '<li><a href="'.INFUSIONS.'faq/faq.php">'.fusion_get_locale('faq_0000', FAQ_LOCALE).'</a></li>';
                    }

                    $contact = !empty(fusion_get_locale('CT_400', LOCALE.LOCALESET.'contact.php')) ? fusion_get_locale('CT_400', LOCALE.LOCALESET.'contact.php') : fusion_get_locale('400', LOCALE.LOCALESET.'contact.php');
                    ?>
                    <li><a href="<?php echo BASEDIR."contact.php" ?>"><?php echo $contact; ?></a></li>
                </ul>
                <small class="display-inline-block">
                    <span>PHP-Fusion Inc & Shadowness &copy; 2013 - <?php echo date('Y'); ?></span> |
                    <?php echo showcopyright('', TRUE).showprivacypolicy(); ?>
                </small>
            </div>
            <?php
            if ($settings['visitorcounter_enabled']) {
                echo '<span class="count m-t-5">';
                echo showcounter();
                echo '</span>';
            }
            ?>
        </div>
        <?php
    }

    /**
     * Classical openside() component
     *
     * @param bool $title
     */
    public function openside($title = FALSE) {
        if ($title) {
            echo "<div class='head'>".$title."</div>\n";
        }
        echo "<div class='side-wrapper'>\n";
    }

    public function closeside() {
        echo "</div>\n";
    }

    public function opensidex() {
        echo "<ul class='albums' style='margin-right:-15px;'>\n";
        echo "<li>\n";
    }

    public function closesidex() {
        echo "</li></ul>\n";
    }

    public function opentable($title = FALSE, $class = '') {
        switch ($this->display_mode) {
            case 'canvas':
                $sub_container_css = 'medium';
                break;
            case 'full-grid':
                $sub_container_css = 'topic';
                break;
            case 'single':
            case 'view':
                $sub_container_css = '';
                break;
            case 'comment':
                $sub_container_css = 'comments';
                break;
            default:
                $sub_container_css = 'medium';
        }
        if ($this->display_mode !== 'single' && $this->display_mode !== 'view') {
            if ($title)
                self::title($title);
            echo "<ul class='".$sub_container_css." ".$class."'>\n<li>\n";
        } else {
            echo "<div class='title'>\n<h1>".$title."</h1>\n";
        }
    }

    // need to know full array.
    public function title($title, array $options = []) {
        $settings = fusion_get_settings();

        $header_type = [
            'menu',
            'news',
        ];
        $options += [
            'get'  => !empty($options['get']) ? $options['get'] : 'section',
            'type' => !empty($options['type']) && in_array($options['type'], $header_type) ? $options['type'] : 'menu',
        ];
        echo "<ul class='head ".$options['type']."'>\n";
        if (is_array($title) && !empty($title)) {
            $i = 0;
            foreach ($title as $info) {
                // if no request default is active
                // $url = str_replace('../', '', $info['url']);
                $url = str_replace($settings['site_path'], '', $info['url']);
                $default_active = !isset($_GET[$options['get']]) && $i == 0;
                // if request matches current url
                $secondary_active = $_SERVER['REQUEST_URI'] == $settings['site_path'].$url;
                // has get but belogns to other set, set default active.
                $tertiary_active = isset($_GET[$options['get']]) && !in_array($_GET[$options['get']], $this->title_array) && $i == 0;
                echo "<li><a ".($default_active || $secondary_active || $tertiary_active ? " class='active'" : "")." href='".$info['url']."' title='".$info['title']."'>".$info['title']."</a></li>\n";
                $i++;
            }
        } else {
            echo "<li>\n";
            if ($options['type'] == 'menu') {
                echo "<h1>".$title."</h1>\n";
            } else {
                echo $title;
            }
            echo "</li>\n";
        }
        echo "</ul>\n";
    }

    /**
     * Shadowness photo container
     *
     * @param $img_src
     * @param $img_link_url
     * @param $image_title
     * @param $author
     */
    public function photo_thumbnail($img_src, $img_link_url, $image_title, $author) {
        global $locale;
        add_to_jquery("$('[data-trim-text]').trim_text();");
        echo "<a href='".$img_link_url."' class='removeParent' title='".$image_title." ".$locale['by']." ".$author."'>\n";
        echo '<img src="'.$img_src.'" alt="'.$image_title.'" style="height: 120px;" class="img img-responsive"/>';
        echo "<span class='title'><span data-trim-text='20'>".$image_title."</span> <small>".$locale['by']." ".$author."</small></span>\n";
        echo "</a>\n";
    }

    /*
     * List breaker <li>
     */
    public function tablebreak() {
        if ($this->display_mode !== 'single')
            echo "</li><li>\n";
    }

    /*
     * Closing of opentable
     */
    public function closetable() {
        if ($this->display_mode !== 'single') {
            echo "</ul>\n";
        } else {
            echo "</div>\n";
        }
    }

    public function render_page() {
        self::display_header();
        self::display_content();
        self::display_footer();
    }
}

$theme = new Producer;
$theme->template_loader();

function render_page() {
    global $theme;
    $theme->render_page();
}

function openside($title = NULL) {
    global $theme;
    $theme->openside($title);
}

function closeside() {
    global $theme;
    $theme->closeside();
}

function opensidex() {
    global $theme;
    $theme->opensidex();
}

function closesidex() {
    global $theme;
    $theme->closesidex();
}


function opentable($title, $class = '') {
    global $theme;
    $theme->opentable($title, $class);
}

function tablebreak() {
    global $theme;
    $theme->tablebreak();
}

function closetable() {
    global $theme;
    $theme->closetable();
}

function display_avatar(array $userdata, $size, $class = '', $link = TRUE, $img_class = 'img-thumbnail', $custom_avatar = '') {
    $settings = fusion_get_settings();

    if (empty($userdata)) {
        $userdata = [];
    }
    $userdata += [
        'user_id'     => 0,
        'user_name'   => '',
        'user_avatar' => '',
        'user_status' => ''
    ];

    if (!$userdata['user_id']) {
        $userdata['user_id'] = 1;
    }
    $link = $settings['hide_userprofiles'] == TRUE ? (iMEMBER ? $link : FALSE) : $link;
    $class = ($class) ? "class='$class'" : '';
    // Need a full path - or else Jquery script cannot use this function.
    $default_avatar = !empty($custom_avatar) ? $custom_avatar : $settings['siteurl']."images/avatars/no-avatar.jpg";
    $user_avatar = $settings['siteurl']."images/avatars/".$userdata['user_avatar'];
    $hasAvatar = $userdata['user_avatar'] && file_exists(IMAGES."avatars/".$userdata['user_avatar']) && $userdata['user_status'] != '5' && $userdata['user_status'] != '6';
    $imgTpl = "<img class='avatar img-responsive $img_class' alt='".(!empty($userdata['user_name']) ? $userdata['user_name'] : 'Guest')."' data-pin-nopin='true' style='display:inline; width:$size;' src='%s'>";
    $img = sprintf($imgTpl, $hasAvatar ? $user_avatar : $default_avatar);
    return $link ? sprintf("<a $class title='".$userdata['user_name']."' href='".BASEDIR."profile.php?lookup=".$userdata['user_id']."'>%s</a>", $img) : $img;
}
