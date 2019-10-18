<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: classes/points_bank.php
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

class PointsBankDeposit extends PointsModel {
    private static $instance = NULL;
    private $pointmod = [];
    private $bank = [];
    public $settings = [];
    private $amount;
    private $depositday;
    private $getinterest;
    private $getpoint;
    private $loanday;
    private $repay;
    private $rowstart;

    public function __construct() {
        include_once POINT_CLASS."templates.php";
        $this->settings = self::CurrentSetup();
        self::$locale = fusion_get_locale("", POINT_LOCALE);
		$this->pointmod = self::GetCurrentUser(fusion_get_userdata('user_id'));
        $this->bank = self::PointsBank(fusion_get_userdata('user_id'));
    }

    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new static();
        }
       return self::$instance;
    }

    public function displayDeposit() {
        if (iMEMBER && !empty($this->settings['ps_interest'])) {
            self::BankDepositForm();
        }
    }

    public function displayLoan() {
        if (iMEMBER && !empty($this->settings['ps_loan'])) {
            self::BankLoanForm();
        }
    }

    public function displayInfo() {
        if (iMEMBER && !empty($this->settings['ps_bank'])) {
            self::BankInfoForm();
        }
    }

    private function BankDepositForm() {
        $save_deposit = filter_input(INPUT_POST, 'save_deposit', FILTER_DEFAULT);
        if(!empty($save_deposit)) {
            $error = self::checkDeposit();

            if ($error == '') {
                self::modifyDeposit();
            } else {
                addNotice('warning', "<i class='fa fa-remove fa-lg fa-fw'></i>".$error);
            }
        }

        openside('');

        $be_max = ($this->settings['ps_deposit_max'] > $this->pointmod['point_point'] ? $this->pointmod['point_point'] : $this->settings['ps_deposit_max']);
        echo openform('betetform', 'post', FUSION_REQUEST, ["class" => "m-t-20"]);

        echo "<div class='display-block overflow-hide'>
        ".form_text('amount', self::$locale['PSP_B05'], $be_max, [
            'required'    => TRUE,
            'inline'      => TRUE,
            'type'        => 'number',
            'width'       => '150px',
            'inner_width' => '150px',
            'number_max'  => $be_max,
            'max_length'  => 6,
            'ext_tip'     => sprintf(self::$locale['PSP_B06'], number_format($be_max)),
        ])."</div>";

        echo "<div class='display-block overflow-hide'>
        ".form_text('depositday', self::$locale['PSP_B07'], $this->settings['ps_deposit_day'], [
            'required'    => TRUE,
            'inline'      => TRUE,
            'type'        => 'number',
            'width'       => '150px',
            'inner_width' => '150px',
            'number_max'  => $this->settings['ps_deposit_day'],
            'max_length'  => 6,
            'ext_tip'     => sprintf(self::$locale['PSP_B69'], $this->settings['ps_deposit_day']),
        ])."</div>";

        echo "<div class='col-xs-12 col-sm-3'>".self::$locale['PSP_B09']."</div>\n";
        echo "<div id='interestrate' class='col-xs-12 col-sm-9'></div>";

        echo "<div class='col-xs-12 col-sm-3'>".self::$locale['PSP_B10']."</div>\n";
        echo "<div id='outcome' class='col-xs-12 col-sm-9'></div>";

        echo form_hidden('getinterest', '', $this->settings['ps_deposit_interest']);
        echo form_hidden('getpoint', '', '');

        echo form_button('save_deposit', self::$locale['PSP_B11'], self::$locale['PSP_B11'], ['class' => 'btn-success', 'icon' => 'fa fa-hdd-o']);
        echo closeform();
        closeside();

        add_to_jquery("
            var depositinterest = ".$this->settings['ps_deposit_interest'].";
            var plocale = '".self::$locale['PSP_PNT']."';
            var calculate = 0;
            a = parseInt(document.getElementById('amount').value)
            b = parseInt((document.getElementById('amount').value*depositinterest)/100)
            calculate = (a+b);
            document.getElementById('getinterest').value = depositinterest;
            document.getElementById('interestrate').innerHTML = depositinterest + ' %';
            document.getElementById('getpoint').value = Math.round(calculate, 0);
            document.getElementById('outcome').innerHTML = Math.round(calculate, 0) + plocale;
        ");

        echo "<script language='JavaScript' type='text/javascript'>
        jQuery(function(){
            jQuery('#amount').change(function () {
                var depositmax = ".$this->settings['ps_deposit_max'].";
                var depositinterest = ".$this->settings['ps_deposit_interest'].";
                var depositmaxday = ".$this->settings['ps_deposit_day'].";
                var pont = ".$this->pointmod['point_point'].";
                var plocale = '".self::$locale['PSP_PNT']."';
                var amount = jQuery('#amount').val();
                var depositday = jQuery('#depositday').val();
                var calculate = 0;
                if (amount > depositmax) {
                    amount = depositmax
                }
                if (amount > pont) {
                    amount = pont
                }
                if (depositday <= depositmaxday) {
                    percent = parseInt(depositday/(depositmaxday/100));
                    newinterest = parseInt((depositinterest/100)*percent);
                    document.getElementById('getinterest').value = Math.round(newinterest, 0);
                    document.getElementById('interestrate').innerHTML = Math.round(newinterest, 0) + ' %';
                    depositinterest = Math.round(newinterest, 0);
                }
                if (amount!= 0 && depositday!= 0) {
                    a = parseInt(amount);
                    b = parseInt((amount/100)*depositinterest);
                    calculate = a+b;
                    document.getElementById('getpoint').value = Math.round(calculate, 0);
                    document.getElementById('outcome').innerHTML = Math.round(calculate, 0) + plocale;
                } else {
                    document.getElementById('getpoint').value = '0';
                    document.getElementById('outcome').innerHTML = 0 + plocale;
                }
                document.getElementById('amount').value = amount;
            });

            jQuery('#depositday').change(function () {
                var depositmax = ".$this->settings['ps_deposit_max'].";
                var depositinterest = ".$this->settings['ps_deposit_interest'].";
                var depositmaxday = ".$this->settings['ps_deposit_day'].";
                var pont = ".$this->pointmod['point_point'].";
                var plocale = '".self::$locale['PSP_PNT']."';
                var amount = jQuery('#amount').val();
                var depositday_val = jQuery('#depositday').val();
                var calculate = 0;

                if (depositday_val > depositmaxday) {
                    depositday_val = depositmaxday
                }
                if (amount > depositmax) {
                    amount = depositmax
                }
                if (amount > pont) {
                    amount = pont
                }

                percent = parseInt(depositday_val/(depositmaxday/100));
                newinterest = parseInt((depositinterest/100)*percent);
                document.getElementById('getinterest').value = Math.round(newinterest, 0);
                document.getElementById('interestrate').innerHTML = Math.round(newinterest, 0) + ' %';
                depositinterest = Math.round(newinterest, 0);

                if (amount!= 0 && depositday_val!= 0) {
                    a = parseInt(amount);
                    b = parseInt((amount/100)*depositinterest);
                    calculate = a+b;
                    document.getElementById('getpoint').value = Math.round(calculate, 0);
                    document.getElementById('outcome').innerHTML = Math.round(calculate, 0) + plocale;
                } else {
                    document.getElementById('getpoint').value = '0';
                    document.getElementById('outcome').innerHTML = 0 + plocale;
                }
                document.getElementById('depositday').value = depositday_val;
            });
        });
        </script>";
    }

    /**
     * @return string
     */
    private function checkDeposit(){
        $error = '';

        $this->amount = form_sanitizer(filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_INT), 0, 'amount');
        $this->depositday = form_sanitizer(filter_input(INPUT_POST, 'depositday', FILTER_VALIDATE_INT), 0, 'depositday');
        $this->getinterest = form_sanitizer(filter_input(INPUT_POST, 'getinterest', FILTER_VALIDATE_INT), 0, 'getinterest');
        $this->getpoint = form_sanitizer(filter_input(INPUT_POST, 'getpoint', FILTER_VALIDATE_INT), 0, 'getpoint');

        $error .= UserPoint::getInstance()->PointBan(fusion_get_userdata('user_id')) ? self::$locale['PSP_E01'] : '';
        $error .= UserPoint::getInstance()->PointInfo(fusion_get_userdata('user_id'), $this->amount) < 0 ? self::$locale['PSP_E02'] : '';
    	$error .= empty($this->settings['ps_activ']) ? self::$locale['PSP_E03'] : '';
    	$error .= empty($this->settings['ps_bank']) ? self::$locale['PSP_E04'] : '';
    	$error .= empty($this->settings['ps_interest']) ? self::$locale['PSP_E05'] : '';
    	$error .= ($this->amount <= 0 || $this->amount == "") ? self::$locale['PSP_E06'] : '';
    	$error .= ($this->depositday <= 0 || $this->depositday == "") ? self::$locale['PSP_E07'] : '';
    	$error .= ($this->getinterest <= 0 || $this->getinterest == "") ? self::$locale['PSP_E08'] : '';
    	$error .= ($this->getpoint <= 0 || $this->getpoint == "") ? self::$locale['PSP_E09'] : '';
    	$error .= (iGUEST) ? self::$locale['PSP_E10'] : '';
        $error .= (\defender::safe() ? "" : self::$locale['PSP_E11']);

        return $error;
    }

    private function modifyDeposit(){
        $deposit = [
            'pb_id'              => '',
            'pb_user_id'         => fusion_get_userdata('user_id'),
            'pb_interest_activ'  => 1,
            'pb_interest_start'  => time(),
            'pb_interest_end'    => (time() + ($this->depositday * 86400)),
            'pb_interest_amount' => $this->amount,
            'pb_interest_szaz'   => $this->getinterest,
            'pb_interest_get'    => $this->getpoint
        ];

        if (\defender::safe()) {
            dbquery_insert(DB_POINT_BANK, $deposit, 'save');
            $messages = self::$locale['PSP_B11'];
            UserPoint::getInstance()->setPoint(fusion_get_userdata('user_id'), ["mod" => 2, "point" => $this->amount, "messages" => $messages]);
            addNotice('success', self::$locale['PSP_B12']);
        }
        !empty($this->bank) ? $this->bank = array_merge($this->bank, $deposit) : '';
    }

    private function BankLoanForm() {

        $save_loan = filter_input(INPUT_POST, 'save_loan', FILTER_DEFAULT);
        if(!empty($save_loan)) {
            $error = self::checkLoan();

            if ($error == '') {
                self::modifyLoan();
            } else {
                addNotice('warning', "<i class='fa fa-remove fa-lg fa-fw'></i>".$error);
            }
        }

        echo openform('loanform', 'post', FUSION_REQUEST, ['class' => 'm-t-20']);

        echo form_text('amount', self::$locale['PSP_B13'], $this->settings['ps_loan_max'], [
            'required'    => TRUE,
            'inline'      => TRUE,
            'type'        => 'number',
            'width'       => '150px',
            'inner_width' => '150px',
            'number_max'  => $this->settings['ps_loan_max'],
            'max_length'  => 6,
            'ext_tip'     => sprintf(self::$locale['PSP_B14'], number_format($this->settings['ps_loan_max']))
        ]);

        echo form_text('loanday', self::$locale['PSP_B15'], $this->settings['ps_loan_day'], [
            'required'    => TRUE,
            'inline'      => TRUE,
            'type'        => 'number',
            'width'       => '150px',
            'inner_width' => '150px',
            'number_max'  => $this->settings['ps_loan_day'],
            'max_length'  => 6,
            'ext_tip'     => sprintf(self::$locale['PSP_B16'], $this->settings['ps_loan_day'])
        ]);

        echo "<div class='col-xs-12 col-sm-3'>".self::$locale['PSP_B17']."</div>\n";
        echo "<div class='col-xs-12 col-sm-9'>".$this->settings['ps_loan_interest']." %</div>";

        echo "<div class='col-xs-12 col-sm-3'>".self::$locale['PSP_B18']."</div>\n";
        echo "<div id='outcome' class='col-xs-12 col-sm-9'></div>";


        echo form_hidden('repay', '', '');
        echo form_button('save_loan', self::$locale['PSP_B19'], self::$locale['PSP_B19'], ['class' => 'btn-success', 'icon' => 'fa fa-hdd-o']);
        echo closeform();

        add_to_jquery("
            var loanmax = ".$this->settings['ps_loan_max'].";
            var loaninterest = ".$this->settings['ps_loan_interest'].";
            var loanday = ".$this->settings['ps_loan_day'].";
            var plocale = '".self::$locale['PSP_PNT']."';
            var calculate = 0;
            a = parseInt(document.getElementById('amount').value)
            b = parseInt((document.getElementById('amount').value*loaninterest)/100)
            calculate = (a+b)/document.getElementById('loanday').value;
            document.getElementById('repay').value = Math.round(calculate, 0);
            document.getElementById('outcome').innerHTML = Math.round(calculate, 0) + plocale;
        ");

        echo "<script language='JavaScript' type='text/javascript'>
            jQuery(function(){
                jQuery('#amount').change(function () {
                    var loanmax = ".$this->settings['ps_loan_max'].";
                    var loaninterest = ".$this->settings['ps_loan_interest'].";
                    var loanmaxday = ".$this->settings['ps_loan_day'].";
                    var plocale = '".self::$locale['PSP_PNT']."';
                    var amount = jQuery('#amount').val();
                    var loanday = jQuery('#loanday').val();
                    var calculate = 0;

                    if (amount > loanmax) {
                        amount = loanmax
                    }
                    if (loanday > loanmaxday) {
                        loanday = loanmaxday
                    }
                    document.getElementById('amount').value = amount;

                    if (amount!= 0 && loanday!= 0) {
                        a = parseInt(amount);
                        b = parseInt((amount*loaninterest)/100);
                        calculate = (a+b)/loanday;

                        document.getElementById('repay').value = Math.round(calculate, 0);
                        document.getElementById('outcome').innerHTML = Math.round(calculate, 0) + plocale;
                    } else {
                        document.getElementById('repay').value = '0';
                        document.getElementById('outcome').innerHTML = '0' + plocale;
                    }
                });

                jQuery('#loanday').change(function () {
                    var loanmax = ".$this->settings['ps_loan_max'].";
                    var loaninterest = ".$this->settings['ps_loan_interest'].";
                    var loanmaxday = ".$this->settings['ps_loan_day'].";
                    var plocale = '".self::$locale['PSP_PNT']."';
                    var amount = jQuery('#amount').val();
                    var loanday = jQuery('#loanday').val();
                    var calculate = 0;

                    if (amount > loanmax) {
                        amount = loanmax
                    }
                    if (loanday > loanmaxday) {
                        loanday = loanmaxday
                    }
                    document.getElementById('loanday').value = loanday;

                    if (amount!= 0 && loanday!= 0) {
                        a = parseInt(amount);
                        b = parseInt((amount*loaninterest)/100);
                        calculate = (a+b)/loanday;
                        document.getElementById('repay').value = Math.round(calculate, 0);
                        document.getElementById('outcome').innerHTML = Math.round(calculate, 0) + plocale;
                    } else {
                        document.getElementById('repay').value = '0';
                        document.getElementById('outcome').innerHTML = '0' + plocale;
                    }
                });
            });
        </script>";
    }

    private function checkLoan() {
        $error = '';

        $this->amount = form_sanitizer(filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_INT), 0, 'amount');
        $this->loanday = form_sanitizer(filter_input(INPUT_POST, 'loanday', FILTER_VALIDATE_INT), 0, 'loanday');
        $this->repay = form_sanitizer(filter_input(INPUT_POST, 'repay', FILTER_VALIDATE_INT), 0, 'repay');

        $error .= UserPoint::getInstance()->PointBan(fusion_get_userdata('user_id')) ? self::$locale['PSP_E01'] : '';
        $error .= empty($this->settings['ps_activ']) ? self::$locale['PSP_E03'] : '';
    	$error .= empty($this->settings['ps_bank']) ? self::$locale['PSP_E04'] : '';
    	$error .= empty($this->settings['ps_interest']) ? self::$locale['PSP_E12'] : '';
        $error .= ($this->amount <= 0 || $this->amount == "") ? self::$locale['PSP_E06'] : '';
        $error .= ($this->loanday <= 0 || $this->loanday == "") ? self::$locale['PSP_E07'] : '';
        $error .= ($this->repay <= 0 || $this->repay == "") ? self::$locale['PSP_E08'] : '';
        $error .= (iGUEST) ? self::$locale['PSP_E13'] : '';
        $error .= (\defender::safe() ? "" : self::$locale['PSP_E11']);

        return $error;
    }

    /**
     *
     */
    private function modifyLoan(){
        $loan = [
            'pb_id'           => '',
            'pb_user_id'      => fusion_get_userdata('user_id'),
            'pb_loan_activ'   => 1,
            'pb_loan_start'   => time(),
            'pb_loan_end'     => $this->loanday,
            'pb_loan_amount'  => $this->amount,
            'pb_loan_reszlet' => $this->repay,
            'pb_loan_levont'  => (time()+86400),
            'pb_loan_day'     => $this->loanday
        ];

        if (\defender::safe()) {
            dbquery_insert(DB_POINT_BANK, $loan, 'save');
            $messages = self::$locale['PSP_B20'];
            UserPoint::getInstance()->setPoint(fusion_get_userdata('user_id'), ["mod" => 1, "point" => $this->amount, "messages" => $messages]);
            addNotice('success', self::$locale['PSP_B21']);
        }

        !empty($this->bank) ? $this->bank = array_merge($this->bank, $loan) : '';
    }

    private function BankInfoForm() {

        openside(self::$locale['PSP_B22']);
        if (!empty($this->bank['loan'])) {
            echo "<div class='table-responsive m-t-20'><table class='table table-responsive table-striped'>";
        	echo "<thead>";
        	echo "<tr>";
        	echo "<th>".self::$locale['PSP_B23']."</th>";
        	echo "<th>".self::$locale['PSP_B24']."</th>";
        	echo "<th>".self::$locale['PSP_B25']."</th>";
        	echo "<th>".self::$locale['PSP_B26']."</th>";
        	echo "<th>".self::$locale['PSP_B27']."</th>";
        	echo "<th>".self::$locale['PSP_B28']."</th>";
        	echo "<th>".self::$locale['PSP_B29']."</th>";
        	echo "<th>".self::$locale['PSP_B30']."</th>";
        	echo "</tr>";
            echo "</thead>";
            echo "<tbody class='text-smaller'>";
            $ii = 0;
            foreach ($this->bank['loan'] as $st) {
                $next_loan[$ii] = $st['pb_loan_levont'] - time();
                if ($next_loan[$ii] > 0) {
                    ?>
                    <script type="text/javascript">
                        display_cr(<?php echo $next_loan[$ii] ?>, "bankle"+<?php echo $ii ?>, 1, "points_bank.php");
                    </script>
                    <?php
                }
                echo "<tr>";
                echo "<td>".number_format($st['pb_loan_amount'])."</td>";
                echo "<td>".showdate("%Y.%m.%d - %H:%M", $st['pb_loan_start'])."</td>";
                echo "<td>".showdate("%Y.%m.%d - %H:%M",($st['pb_loan_start']+($st['pb_loan_end']*86400)))."</td>";
                echo "<td>".$this->settings['ps_loan_interest']." %</td>";
                echo "<td>".$st['pb_loan_reszlet']."</td>";
                echo "<td>".($st['pb_loan_day'] - $st['pb_loan_end'])."</td>";
                echo "<td>".($st['pb_loan_day'] == $st['pb_loan_end'] ? self::$locale['PSP_B32'] : showdate("%Y.%m.%d - %H:%M", ($st['pb_loan_levont'] - 86400)))."</td>";
                echo "<td><div class='text-right required' id='bankle".$ii."'> --- </div></td>\n";
                echo "</tr>";
                $ii++;
            }
            echo "</tbody>";
            echo "</table></div>";
        } else {
            echo "<div class='well text-center'>".self::$locale['PSP_B31']."</div>\n";
        }
        closeside();

        openside(self::$locale['PSP_B33']);
        if (!empty($this->bank['interest'])) {
            echo "<div class='table-responsive m-t-20'><table class='table table-responsive table-striped'>";
        	echo "<thead>";
        	echo "<tr>";
        	echo "<th>".self::$locale['PSP_B34']."</th>";
        	echo "<th>".self::$locale['PSP_B35']."</th>";
        	echo "<th>".self::$locale['PSP_B36']."</th>";
        	echo "<th>".self::$locale['PSP_B37']."</th>";
        	echo "<th>".self::$locale['PSP_B38']."</th>";
        	echo "</tr>";
            echo "</thead>";
            echo "<tbody class='text-smaller'>";
            $ii = 0;
            foreach ($this->bank['interest'] as $st) {
                $next_interest[$ii] = $st['pb_interest_end'] - time();
                if ($next_interest[$ii] > 0) {
                    ?>
                    <script type="text/javascript">
                        display_cr(<?php echo $next_interest[$ii] ?>, "bankkam"+<?php echo $ii ?>, 1, "points_bank.php");
                    </script>
                    <?php
                }
                echo "<tr>";
                echo "<td>".number_format($st['pb_interest_amount'])."</td>";
                echo "<td>".showdate("%Y.%m.%d - %H:%M",$st['pb_interest_start'])."</td>";
                echo "<td><div class='text-right required' id='bankkam".$ii."'> --- </div></td>\n";
                echo "<td>".$st['pb_interest_szaz']." %</td>";
                echo "<td>".number_format($st['pb_interest_get'])."</td>";
                echo "</tr>";
                $ii++;
            }
            echo "</tbody>";
            echo "</table></div>";
        } else {
            echo "<div class='well text-center'>".self::$locale['PSP_B39']."</div>\n";
        }
        closeside();

    }

    public function BankLoan() {

        $max_rows = dbcount("(pb_id)", DB_POINT_BANK, "pb_loan_start != '0'".(multilang_table("PSP") ? " AND pb_language='".LANGUAGE."'" : ''));
        $this->rowstart = filter_input(INPUT_GET, 'rowstart', FILTER_VALIDATE_INT);
        $this->rowstart = (!empty($this->rowstart) && $this->rowstart <= $max_rows) ? $this->rowstart : 0;

        $bind = [
            ':start'    => '0',
            ':language' => LANGUAGE,
            ':rowstart' => $this->rowstart,
            ':limit'    => $this->settings['ps_page']
        ];
        $result = dbquery("SELECT pb.*, pu.user_id, pu.user_name, pu.user_status, pu.user_avatar, pu.user_joined, pu.user_level
            FROM ".DB_POINT_BANK." AS pb
            LEFT JOIN ".DB_USERS." AS pu ON pu.user_id = pb.pb_user_id
            WHERE pb_loan_start != :start
            ".(multilang_table("PSP") ? " AND pb_language=:language" : '')."
            ORDER BY pb_user_id ASC, pb_loan_start DESC
            LIMIT :rowstart, :limit", $bind
        );
        $inf = [];
        while ($data = dbarray($result)) {
            $inf[] = $data;
        }
	    $info = [
	        'ittem'   => $inf,
            'max_row' => $max_rows,
            'pagenav' => makepagenav($this->rowstart, $this->settings['ps_page'], $max_rows, 3, POINT_CLASS."points_bank.php&")
	    ];

        displayBankLoan($info);
    }

    public function BankDeposit() {
        $max_rows = dbcount("(pb_id)", DB_POINT_BANK, "pb_interest_start != '0'".(multilang_table("PSP") ? " AND pb_language='".LANGUAGE."'" : ''));
        $this->rowstart = filter_input(INPUT_GET, 'rowstart', FILTER_VALIDATE_INT);
        $this->rowstart = (!empty($this->rowstart) && $this->rowstart <= $max_rows) ? $this->rowstart : 0;

        $bind = [
            ':start'    => '0',
            ':language' => LANGUAGE,
            ':rowstart' => $this->rowstart,
            ':limit'    => $this->settings['ps_page']
        ];
        $result = dbquery("SELECT pb.*, pu.user_id, pu.user_name, pu.user_status, pu.user_avatar, pu.user_joined, pu.user_level
            FROM ".DB_POINT_BANK." AS pb
            LEFT JOIN ".DB_USERS." AS pu ON pu.user_id = pb.pb_user_id
            WHERE pb_interest_start != :start
            ".(multilang_table("PSP") ? " AND pb_language=:language" : '')."
            ORDER BY pb_user_id ASC, pb_interest_start DESC
            LIMIT :rowstart, :limit", $bind
        );
        $inf = [];
        while ($data = dbarray($result)) {
            $inf[] = $data;
        }
	    $info = [
	        'ittem'   => $inf,
            'max_row' => $max_rows,
            'pagenav' => makepagenav($this->rowstart, $this->settings['ps_page'], $max_rows, 3, POINT_CLASS."points_bank.php&")
	    ];

        displayBankDeposit($info);
    }

}
