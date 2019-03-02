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

        opentable($info['opentable']);
        if ($info['aktiv'] == 1) {
            echo (!empty($info['pricetype']) ? "<div class='m-t-5 text-center'><small>".$info['pricetype']."</small></div>\n" : "");
        	echo "<table class='table table-responsive table-striped'>
        	    <tbody class='text-smaller'>
        	    <tr id='point_{%id%}'>
        	        <td>".$info['item']['UserPont']['locale']."</td>
        	        <td>".$info['item']['UserPont']['data']."</td>
        	     </tr>
        	     <tr id='place_{%id%}'>
        	         <td>".$info['item']['UserHely']['locale']."</td>
        	         <td>".$info['item']['UserHely']['data']."</td>
        	      </tr>
        	      <tr id='increase_{%id%}' class='success'>
        	          <td colspan='2' class='text-center'>".$info['item']['increase']."</td>
        	      </tr>
        	      </tbody>
        	      </table>
        	      <table class='table table-responsive table-striped'>
        	      <thead>
        	      <tr>
        	          <th><small><strong>".$info['item']['udate']['locale']."</strong></small></th>
        	          <th><small><strong>".$info['item']['upont']['locale']."</strong></small></th>
        	          <th><small><strong>".$info['item']['umod']['locale']."</strong></small></th>
        	      </tr>
        	      </thead>
        	      <tbody class='text-smaller'>
        	      <tr id='point_".$info['id']."'>
        	          <td>".$info['item']['udate']['data']."</td>
        	          <td>".$info['item']['upont']['data']."</td>
        	          <td>".$info['item']['umod']['data']."</td>
        	      </tr>
        	      </tbody>
        	</table>";

        	if (!empty($info['item']['listmenu'])) {
        		echo "<div id='point_' class='text-center'>".$info['item']['listmenu']."</div>";
            }

        } else {
            echo "<div class='text-center'>".$info['message']."</div>";
        }
        closetable();
    }
}

if (!function_exists('PlaceItem')) {
    function PlaceItem($info) {
        $locale = fusion_get_locale("", POINT_LOCALE);
        echo opentable($info['opentable']);
        if (!empty($info['message'])) {
            echo "<div class='well text-center'><strong>".$info['message']."</strong></div>";
        }

        if ($info['placefilter']) {
        	echo "<div class='clearfix'>";
        	echo "<div class='display-inline-block pull-right'>".$info['placefilter']."</div>";
        	echo "<div class='display-inline-block pull-left'>".$info['pagenav']."</div>";
        	echo "</div>";
        }
        echo "<div class='table-responsive m-t-20'><table class='table table-bordered clear'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th><small><strong>".$locale['PSP_P02']."</strong></small></th>";
        echo "<th><small><strong>".$locale['PSP_P03']."</strong></small></th>";
        echo "<th><small><strong>".$locale['PSP_P04']."</strong></small></th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody class='text-smaller'>";

        if (!empty($info['max_row'])) {
            $pli = 0;
            foreach ($info['item'] as $data) {
                $pli++;
                echo "<tr id='diary_".$data['point_id']."'>";
                echo "<td><strong>".(\PHPFusion\Points\UserPoint::PointPlace(fusion_get_userdata('user_id')) != ($info['helyezes'] + $pli) ? $info['helyezes'] + $pli : "<span style='color:#FF0000'>".($info['helyezes'] + $pli))."</span>"."</strong></td>";
                echo "<td><div class='clearfix'>
                    <div class='pull-left m-r-10'>".$data['avatar']."</div>
                    <div class='overflow-hide'>
                        <span class='m-l-10 m-r-10'>".$data['profile']."</span>
                    </div>
                    </div>
                </td>";
                echo "<td>".$data['point']."</td>";
                echo "</tr>";
            }
        }
        echo "</tbody>";
        echo "</table></div>";

        if ($info['pagenav']) {
        	echo "<div class='clearfix'>";
        	echo "<div class='display-inline-block pull-right'>".$info['pagenav']."</div>";
            echo "</div>";
        }

        echo closetable();
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
