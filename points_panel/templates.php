<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: points_panel/templates.php
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
defined('IN_FUSION') || exit;

if (!function_exists('pointPanelItem')) {
    function pointPanelItem($info) {

        $html = \PHPFusion\Template::getInstance('point_panel');
        $html->set_template(__DIR__.'/templates/point_panel.html');
        $locale = fusion_get_locale();
        $html->set_locale($locale);

        $html->set_tag('opentable', fusion_get_function('opentable', $info['opentable']));
        $html->set_tag('closetable', fusion_get_function('closetable'));

        if ($info['activ'] == 1) {
            if (!empty($info['holiday'])) {
                $html->set_block('holiday', ['holiday' => $info['holiday']]);
            }
            if (!empty($info['pricetype'])) {
                $html->set_block('pricetype', ['pricetype' => $info['pricetype']]);
            }
            if (!empty($info['item']['listmenu'])) {
                $html->set_block('listmenu', ['listmenu' => $info['item']['listmenu']]);
            }

            $html->set_block('pointpanel', [
                'id'        => $info['id'],
                'allpoint'  => $info['item']['UserPont'],
                'place'     => $info['item']['UserHely'],
                'increase'  => $info['item']['increase'],
                'udate'     => $info['item']['udate'],
                'upont'     => $info['item']['upont'],
                'umod'      => $info['item']['umod'],
            ]);

        } else {
            $html->set_block('no_item', ['message' => $info['message']]);
        }

        echo $html->get_output();
    }
}

if (!function_exists('PlaceItem')) {
    function PlaceItem($info) {
        $html = \PHPFusion\Template::getInstance('place');
        $html->set_template(POINT_CLASS.'templates/place.html');
        $html->set_tag('opentable', fusion_get_function('opentable', $info['opentable']));
        $html->set_tag('closetable', fusion_get_function('closetable'));
        $html->set_locale(fusion_get_locale());

        if (!empty($info['placefilter'])) {
            $html->set_block('pagenav_a', ['navigation' => $info['pagenav']]);
            $html->set_tag('placefilter', $info['placefilter']);
        }

        $html->set_tag('message', $info['message']);

        if (!empty($info['item'])) {
            $pli = 0;
            foreach ($info['item'] as $cdata) {
                $pli++;
                $html->set_block('items', [
                    'point_id' => $cdata['point_id'],
                    'place'    => \PHPFusion\Points\UserPoint::PointPlace(fusion_get_userdata('user_id')) != ($info['place'] + $pli) ? $info['place'] + $pli : "<span style='color:#FF0000'>".($info['place'] + $pli)."</span>",
                    'avatar'   => $cdata['avatar'],
                    'profile'  => $cdata['profile'],
                    'point'    => $cdata['point']
                ]);
            }
        } else {
            $html->set_block('no_item', ['message' => $info['nostat']]);
        }
        if (!empty($info['pagenav'])) {
            $html->set_block('pagenav_b', ['navigation_2' => $info['pagenav']]);
        }

        echo $html->get_output();
    }
}

if (!function_exists('BanItem')) {
    function BanItem($info) {
    	$locale = fusion_get_locale();
        if (!empty($info['banuser'])) {
    	opentable($locale['PSP_A10']);
        echo $info['banuser'];
        closetable();
        }

    	opentable($locale['PSP_A10']);
        echo "<div class='well text-center'>".$locale['PSP_A11']."</div>";
        if (!empty($info['aktivban']['ittem'])) {
            if ($info['aktivban']['pagenav']) {
        	    echo "<div class='clearfix'>";
        	    echo "<div class='display-inline-block pull-right'>".$info['aktivban']['pagenav']."</div>";
                echo "</div>";
            }
            echo "<div class='table-responsive m-t-20'><table class='table table-bordered clear'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th><small><strong>".$locale['PSP_A12']."</strong></small></th>";
            echo "<th><small><strong>".$locale['PSP_A13']."</strong></small></th>";
            echo "<th><small><strong>".$locale['PSP_A14']."</strong></small></th>";
            echo "<th><small><strong>".$locale['PSP_A15']."</strong></small></th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody class='text-smaller'>";
            foreach ($info['aktivban']['ittem'] as $data) {
                echo "<tr>";
                echo "<td><div class='clearfix'>
                    <div class='pull-left m-r-10'>".display_avatar($data, '50px', '', TRUE, 'img-rounded')."</div>
                    <div class='overflow-hide'>
                        <span class='m-l-10 m-r-10'>".profile_link($data['user_id'], $data['user_name'], $data['user_status'])."</span>
                    </div>
                    </div>
                </td>";
                echo "<td>".showdate("%Y.%m.%d - %H:%M", $data['ban_time_start'])." - ".showdate("%Y.%m.%d - %H:%M", $data['ban_time_stop'])."</td>";
                echo "<td>".$data['ban_text']."</td>";
                echo "<td><a href='".FUSION_SELF.fusion_get_aidlink()."&ban_id=".$data['ban_id']."' onclick=\"return confirm('".$locale['PONT_169']."' );\"><i class='fa fa-trash-o fa-lg m-r-10'></i></a></td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table></div>";

        } else {
            echo "<div class='well text-center'>".$locale['PSP_A16']."</div>";
        }
    	closetable();

    	opentable($locale['PSP_A17']);
        echo "<div class='well text-center'>".$locale['PSP_A18']."</div>";
        if (!empty($info['allban']['ittem'])) {
            if ($info['allban']['pagenav']) {
        	    echo "<div class='clearfix'>";
        	    echo "<div class='display-inline-block pull-right'>".$info['allban']['pagenav']."</div>";
                echo "</div>";
            }
            echo "<div class='table-responsive m-t-20'><table class='table table-bordered clear'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th><small><strong>".$locale['PSP_A12']."</strong></small></th>";
            echo "<th><small><strong>".$locale['PSP_A13']."</strong></small></th>";
            echo "<th><small><strong>".$locale['PSP_A19']."</strong></small></th>";
            echo "<th><small><strong>".$locale['PSP_A14']."</strong></small></th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody class='text-smaller'>";
            foreach ($info['allban']['ittem'] as $data) {
                echo "<tr>";
                echo "<td><div class='clearfix'>
                    <div class='pull-left m-r-10'>".display_avatar($data, '50px', '', TRUE, 'img-rounded')."</div>
                    <div class='overflow-hide'>
                        <span class='m-l-10 m-r-10'>".profile_link($data['user_id'], $data['user_name'], $data['user_status'])."</span>
                    </div>
                    </div>
                </td>";
                echo "<td>".showdate("%Y.%m.%d - %H:%M", $data['ban_time_start'])."</td>";
                echo "<td>".showdate("%Y.%m.%d - %H:%M", $data['ban_time_stop'])."</td>";
                echo "<td>".$data['ban_text']."</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table></div>";

        } else {
            echo "<div class='well text-center'>".$locale['PSP_A20']."</div>";
        }
    	closetable();
    }

}

if (!function_exists('Display_Diary')) {
    function Display_Diary($info) {
    	$locale = fusion_get_locale();
    	opentable("<i class='fa fa-globe fa-lg m-r-10'></i>".$locale['PSP_D00']);
        if ($info['diaryfilter']) {
        	echo "<div class='clearfix'>";
        	echo "<div class='display-inline-block pull-right'>".$info['diaryfilter']."</div>";
        	echo "<div class='display-inline-block pull-left'>".$info['ittem']['pagenav']."</div>";
        	echo "</div>";
        }

        if (!empty($info['ittem']['diary'])) {
        	echo "<div class='table-responsive m-t-20'><table class='table table-responsive table-striped'>";
        	echo "<thead>";
        	echo "<tr>";
        	echo "<th>".$locale['PSP_D06']."</th>";
        	echo "<th>".$locale['PSP_D07']."</th>";
        	echo "<th>".$locale['PSP_D08']."</th>";
        	echo "<th>".$locale['PSP_D09']."</th>";
        	echo "<th>".$locale['PSP_D10']."</th>";
        	echo "</tr>";
            echo "</thead>";
            echo "<tbody class='text-smaller'>";
            foreach ($info['ittem']['diary'] as $st) {
            	$emotikum = "<span style='color:".($st['log_pmod'] == 1 ? '#5CB85C' : '#FF0000')."'><i class='".($st['log_pmod'] == 1 ? "fa fa-plus-square" : "fa fa-minus-square")."'></i></span>";
            	echo "<tr>";
            	echo "<td>".showdate("%Y.%m.%d - %H:%M",$st['log_date'])."</td>";
            	echo "<td>".number_format($st['log_point'])."</td>";
            	echo "<td>".$emotikum."</td>\n";
            	echo "<td>".nl2br(parseubb(parsesmileys($st['log_descript'])))."</td>";
            	echo "<td><a href='".FUSION_SELF.$info['ittem']['link']."&del=delete&amp;log_id=".$st['log_id']."' onclick=\"return confirm('".$locale['PSP_D11']."');\"><i class='fa fa-trash'></i></a></td>";
            	echo "</tr>";
            }
            echo "</tbody>";
            echo "</table></div>";
        } else {
        	echo "<div class='text-center well'>".$locale['PSP_D12']."</div>\n";
        }
        echo "<div class='text-center'>".$info['ittem']['delall']."</div>\n";
        if ($info['ittem']['pagenav']) {
        	echo "<div class='clearfix'>";
        	echo "<div class='display-inline-block pull-left'>".$info['ittem']['pagenav']."</div>";
        	echo "</div>";
        }

        closetable();
    }
}

if (!function_exists('displayBankDeposit')) {
    function displayBankDeposit($info) {
    	$locale = fusion_get_locale();
    	opentable("<i class='fa fa-globe fa-lg m-r-10'></i>".$locale['PSP_M08']);
        if ($info['pagenav']) {
        	echo "<div class='clearfix'>";
        	echo "<div class='display-inline-block pull-left'>".$info['pagenav']."</div>";
        	echo "</div>";
        }

        if (!empty($info['ittem'])) {
        	echo "<div class='table-responsive m-t-20'><table class='table table-responsive table-striped'>";
        	echo "<thead>";
        	echo "<tr>";
        	echo "<th>".$locale['PSP_B61']."</th>";
        	echo "<th>".$locale['PSP_B35']."</th>";
        	echo "<th>".$locale['PSP_B62']."</th>";
        	echo "<th>".$locale['PSP_B37']."</th>";
        	echo "</tr>";
            echo "</thead>";
            echo "<tbody class='text-smaller'>";
            foreach ($info['ittem'] as $st) {
            	echo "<tr>";
            	echo "<td><div class='clearfix'><div class='pull-left m-r-10'>".display_avatar($st, '30px', '', TRUE, 'img-rounded')."</div><div class='overflow-hide'>".profile_link($st['user_id'], $st['user_name'], $st['user_status'])."</div></td>\n";
            	echo "<td>".showdate("%Y.%m.%d - %H:%M", $st['pb_interest_start'])."</td>";
            	echo "<td>".showdate("%Y.%m.%d - %H:%M", $st['pb_interest_end'])."</td>";
            	echo "<td>".number_format($st['pb_interest_get'])."</td>";
            	echo "</tr>";
            }
            echo "</tbody>";
            echo "</table></div>";
        } else {
        	echo "<div class='text-center well'>".$locale['PSP_B63']."</div>\n";
        }
        if ($info['pagenav']) {
        	echo "<div class='clearfix'>";
        	echo "<div class='display-inline-block pull-left'>".$info['pagenav']."</div>";
        	echo "</div>";
        }

        closetable();
    }
}

if (!function_exists('displayBankLoan')) {
    function displayBankLoan($info) {
    	$locale = fusion_get_locale();
    	opentable("<i class='fa fa-globe fa-lg m-r-10'></i>".$locale['PSP_M08']);
        if ($info['pagenav']) {
        	echo "<div class='clearfix'>";
        	echo "<div class='display-inline-block pull-left'>".$info['pagenav']."</div>";
        	echo "</div>";
        }

        if (!empty($info['ittem'])) {
        	echo "<div class='table-responsive m-t-20'><table class='table table-responsive table-striped'>";
        	echo "<thead>";
        	echo "<tr>";
        	echo "<th>".$locale['PSP_B61']."</th>";
        	echo "<th>".$locale['PSP_B24']."</th>";
        	echo "<th>".$locale['PSP_B25']."</th>";
        	echo "<th>".$locale['PSP_B64']."</th>";
        	echo "<th>".$locale['PSP_B65']."</th>";
        	echo "<th>".$locale['PSP_B66']."</th>";
        	echo "<th>".$locale['PSP_B67']."</th>";
        	echo "</tr>";
            echo "</thead>";
            echo "<tbody class='text-smaller'>";
            foreach ($info['ittem'] as $st) {
            	echo "<tr>";
            	echo "<td><div class='clearfix'><div class='pull-left m-r-10'>".display_avatar($st, '30px', '', TRUE, 'img-rounded')."</div><div class='overflow-hide'>".profile_link($st['user_id'], $st['user_name'], $st['user_status'])."</div></td>\n";
            	echo "<td>".showdate("%Y.%m.%d - %H:%M", $st['pb_loan_start'])."</td>";
            	echo "<td>".$st['pb_loan_end']."</td>";
            	echo "<td>".$st['pb_loan_day']."</td>";
            	echo "<td>".number_format($st['pb_loan_amount'])."</td>";
            	echo "<td>".$st['pb_loan_reszlet']."</td>";
            	echo "<td>".showdate("%Y.%m.%d - %H:%M", $st['pb_loan_levont'])."</td>";
            	echo "</tr>";
            }
            echo "</tbody>";
            echo "</table></div>";
        } else {
        	echo "<div class='text-center well'>".$locale['PSP_B68']."</div>\n";
        }
        if ($info['pagenav']) {
        	echo "<div class='clearfix'>";
        	echo "<div class='display-inline-block pull-left'>".$info['pagenav']."</div>";
        	echo "</div>";
        }

        closetable();
    }
}
