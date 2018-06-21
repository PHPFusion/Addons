<?php

class PHPSecure {
    private static $instance = NULL;
    private static $locale = [];
    private static $setup = [];
    protected static $cod = [];

    public function __construct() {
        require_once INCLUDES."infusions_include.php";
        require_once INFUSIONS.'secure_panel/templates/secure_panel.php';
        self::$locale = fusion_get_locale("", SCR_LOCALE);
    }

    public static function getInstance($key = TRUE) {
        if (self::$instance === NULL) {
            self::$instance = new static();
            self::$setup = get_settings("secure_panel");
            self::$instance->set_Secure();
            self::$instance->securCod();
        }

        return self::$instance;
    }

    protected function securCod() {
        srand((double)microtime() * 1000000);
        $a = RAND(1, 99);
        $b = RAND(1, 99);
        $muv = [" ", self::$locale['SEC_0005'], self::$locale['SEC_0006'], self::$locale['SEC_0007']];
        $el = (($a < 10 && $b < 10) ? 3 : (($a > $b) ? 2 : 1));

        $mvrnd = rand(1, 2);

        self::$cod = [
            'cod'  => $a.$muv[$el][$mvrnd].$b." = ? ",
            'ckod' => $a.",".$b.",".$el,
        ];

        return self::$cod;
    }

    private function set_Secure() {
        if (!empty(self::$setup['secure_aktiv'])) {
            if (isset($_POST['secure_submit'])) {
                $error = "";

                $secure_answer = form_sanitizer($_POST['secure_answer'], '', 'secure_answer');
                $scarr = form_sanitizer($_POST['secure_question'], '', 'secure_question');
                if (\defender::safe()) {
                    $secur = explode(',', $scarr);
                    $abc = ['', ($secur[0] + $secur[1]), ($secur[0] - $secur[1]), ($secur[0] * $secur[1])];
                    $error .= empty($secure_answer) ? self::$locale['error1'] : "";
                    $error .= $abc[$secur[2]] != $secure_answer ? self::$locale['error2'] : "";

                    if ($error != "") {
                        \defender::stop();
                        setcookie("secure", "secure_submit", time() - 600);
                        addNotice('warning', "<i class='fa fa-remove fa-lg fa-fw'></i>".$error);
                        redirect(FUSION_SELF);
                    }
                    setcookie("secure", "secure_submit", time() + 600);
                    redirect((fusion_get_settings("site_seo") ? FUSION_ROOT.BASEDIR."register.php" : BASEDIR."register.php"));
                }
            }
        }
        if (isset($_POST['secure_btn'])) {
            $inputArray = [
                'secure_aktiv' => form_sanitizer($_POST['stsecure'], 0, "stsecure"),
                'secure_time'  => form_sanitizer($_POST['stsecure_time'], 0, "stsecure_time") * 60,
            ];

            if (\defender::safe()) {
                foreach ($inputArray as $settings_name => $settings_value) {
                    $inputSettings = [
                        "settings_name" => $settings_name, "settings_value" => $settings_value, "settings_inf" => "secure_panel",
                    ];
                    dbquery_insert(DB_SETTINGS_INF, $inputSettings, "update", ["primary_key" => "settings_name"]);
                }
                addNotice("success", self::$locale['SEC_0025']);
                redirect(clean_request("", ["section=secure", "aid"], TRUE));
            }
        }

    }

    public static function display_members($akt = FALSE) {
        if (preg_match('/register.php/i', $_SERVER['PHP_SELF']) || preg_match('/registration/i', $_SERVER['REQUEST_URI']) AND !isset($_COOKIE['secure']) AND !empty(self::$setup['secure_aktiv'])) {

            if (fusion_get_settings("site_seo")) {
                redirect(FUSION_ROOT.BASEDIR."secure.php");
            } else {
                redirect(BASEDIR."secure.php");
            }
        }
    }

    public function display_admin() {

        $allowed_section = ["secure"];
        $_GET['section'] = isset($_GET['section']) && in_array($_GET['section'], $allowed_section) ? $_GET['section'] : 'secure';
        add_to_title(self::$locale['SEC_020']);
        \PHPFusion\BreadCrumbs::getInstance()->addBreadCrumb(['link' => INFUSIONS.'secure_panel/admin.php'.fusion_get_aidlink(), 'title' => self::$locale['SEC_020']]);

        opentable(self::$locale['SEC_0020']);
        $master_tab_title['title'][] = self::$locale['SEC_0021'];
        $master_tab_title['id'][] = "secure";
        $master_tab_title['icon'][] = "fa fa-fw fa-cogs";

        echo opentab($master_tab_title, $_GET['section'], "secure", FALSE);
        switch ($_GET['section']) {
            case "secure":
                $this->settings_Form();
                break;
            default:
                break;
        }
        echo closetab();
        closetable();
    }

    public function settings_Form() {

        openside('');
        echo openform('settingsform', 'post', FUSION_REQUEST);
        $opts = ['1' => self::$locale['yes'], '0' => self::$locale['no'],];
        echo form_select('stsecure', self::$locale['SEC_0022'], self::$setup['secure_aktiv'], ['inline' => TRUE, 'inner_width' => '100px', 'options' => $opts]);
        echo form_text('stsecure_time', self::$locale['SEC_0023'], (self::$setup['secure_time'] / 60), ['required' => TRUE, 'inline' => TRUE, 'inner_width' => '100px', 'type' => 'number', 'ext_tip' => self::$locale['SEC_0024']]);
        echo form_button('secure_btn', self::$locale['save'], self::$locale['save'], ['class' => 'btn-primary']);
        echo closeform();
        closeside();
    }

    public static function display_form() {
        add_to_title(self::$locale['SEC_0000']);
        if (iMEMBER || !fusion_get_settings("enable_registration")) {
            redirect(fusion_get_settings('opening_page'));
        }

        if (isset($_COOKIE['secure'])) {
            redirect(BASEDIR."register.php");
        }

        add_to_head("<link href='http://fonts.googleapis.com/css?family=Over+the+Rainbow' rel='stylesheet' type='text/css'>");
        add_to_head("<link rel='stylesheet' href='".INFUSIONS."secure_panel/secure.css' type='text/css' media='all' />");
        $info = [
            'tablename'    => "<i class='fa fa-shield fa-fw'></i>".self::$locale['SEC_0020'],
            'intro'        => "<div class='secque-intro'>".self::$locale['SEC_0010']." </div>\n",
            'prmessages'   => "<div class='panel-header'>".self::$locale['SEC_0020']."</div>\n",
            'prmessages_1' => "<div class='m-t-15 text-center'>".self::$locale['SEC_0011']."</div>\n",
            'seccode'      => "<div class='secure-question m-t-15 text-center'>".self::$cod['cod']."</div>",
            'openform'     => openform('secure_answer', 'post', FUSION_REQUEST, ['max_tokens' => 1]),
            'mail_name'    => form_text('secure_answer', '', '', ['required' => TRUE, 'number' => TRUE, 'max_length' => 4, 'error_text' => self::$locale['error3'], 'autocomplete_off' => TRUE]),
            'question'     => form_hidden('secure_question', '', self::$cod['ckod']),
            'button'       => form_button('secure_submit', self::$locale['submit'], self::$locale['submit'], ['class' => 'btn-primary', 'icon' => 'fa fa-send']),
            'closeform'    => closeform()
        ];
        ob_start();

        echo render_secure_panel($info);

        echo strtr(ob_get_clean(), [
            '{%tablename%}'    => $info['tablename'],
            '{%intro%}'        => $info['intro'],
            '{%prmessages%}'   => $info['prmessages'],
            '{%prmessages_1%}' => $info['prmessages_1'],
            '{%open_form%}'    => $info['openform'],
            '{%mail_name%}'    => $info['mail_name'],
            '{%question%}'     => $info['question'],
            '{%seccode%}'      => $info['seccode'],
            '{%send_button%}'  => $info['button'],
            '{%close_form%}'   => $info['closeform']
        ]);
    }

}
