<?php

if (!function_exists('pointPanelItem')) {
    function pointPanelItem($info) {

        opentable($info['opentable']);
        if ($info['aktiv'] == 1) {
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

        if ($info['pagenav']) {
        	echo "<div class='clearfix'>";
        	echo "<div class='display-inline-block pull-right'>".$info['pagenav']."</div>";
            echo "</div>";
        }
        echo "<div class='table-responsive m-t-20'><table class='table table-bordered clear'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th><small><strong>".$locale['PONT_131']."</strong></small></th>";
        echo "<th><small><strong>".$locale['PONT_132']."</strong></small></th>";
        echo "<th><small><strong>".$locale['PONT_133']."</strong></small></th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody class='text-smaller'>";

        if (!empty($info['max_row'])) {
            $i = 0;
            foreach ($info['item'] as $key => $data) {
                $i++;
                echo "<tr id='diary_".$data['point_id']."' class='warning'>";
                echo "<td><strong>".(PHPFusion\Points\UserPoint::PointPlace(fusion_get_userdata('user_id')) != ($info['helyezes'] + $i) ? $info['helyezes'] + $i : "<span style='color:#FF0000'>".($info['helyezes'] + $i))."</span>"."</strong></td>";
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
    	opentable($locale['PONT_163']);
        echo "<div class='well text-center'>".$locale['PONT_164']."</div>";
        if (!empty($info['aktivban']['ittem'])) {
            if ($info['aktivban']['pagenav']) {
        	    echo "<div class='clearfix'>";
        	    echo "<div class='display-inline-block pull-right'>".$info['aktivban']['pagenav']."</div>";
                echo "</div>";
            }
            echo "<div class='table-responsive m-t-20'><table class='table table-bordered clear'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th><small><strong>".$locale['PONT_165']."</strong></small></th>";
            echo "<th><small><strong>".$locale['PONT_166']."</strong></small></th>";
            echo "<th><small><strong>".$locale['PONT_167']."</strong></small></th>";
            echo "<th><small><strong>".$locale['PONT_168']."</strong></small></th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody class='text-smaller'>";
            foreach ($info['aktivban']['ittem'] as $key => $data) {
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
                echo "<td><a href='".FUSION_SELF.fusion_get_aidlink()."&amp;ban_id=".$data['ban_id']."' onclick=\"return confirm('".$locale['PONT_169']."' );\"><i class='fa fa-trash-o fa-lg m-r-10'></i></a></td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table></div>";

        } else {
            echo "<div class='well text-center'>".$locale['PONT_170']."</div>";
        }
    	closetable();

    	opentable($locale['PONT_171']);
        echo "<div class='well text-center'>".$locale['PONT_172']."</div>";
        if (!empty($info['allban']['ittem'])) {
            if ($info['allban']['pagenav']) {
        	    echo "<div class='clearfix'>";
        	    echo "<div class='display-inline-block pull-right'>".$info['allban']['pagenav']."</div>";
                echo "</div>";
            }
            echo "<div class='table-responsive m-t-20'><table class='table table-bordered clear'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th><small><strong>".$locale['PONT_165']."</strong></small></th>";
            echo "<th><small><strong>".$locale['PONT_166']."</strong></small></th>";
            echo "<th><small><strong>".$locale['PONT_173']."</strong></small></th>";
            echo "<th><small><strong>".$locale['PONT_167']."</strong></small></th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody class='text-smaller'>";
            foreach ($info['allban']['ittem'] as $key => $data) {
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
            echo "<div class='well text-center'>".$locale['PONT_174']."</div>";
        }
    	closetable();
    }

}

if (!function_exists('Display_Diary')) {
    function Display_Diary($info) {
    	$locale = fusion_get_locale();
    	opentable("<i class='fa fa-globe fa-lg m-r-10'></i>".$locale['PONT_200']);
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
        	echo "<td>".$locale['PONT_205']."</td>";
        	echo "<td>".$locale['PONT_206']."</td>";
        	echo "<td>".$locale['PONT_207']."</td>";
        	echo "<td>".$locale['PONT_208']."</td>";
        	echo "<td>".$locale['PONT_209']."</td>";
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
            	echo "<td><a href='".FUSION_SELF.$info['ittem']['link']."&amp;del=delete&amp;log_id=".$st['log_id']."' onclick=\"return confirm('".$locale['PONT_305']."');\"><i class='fa fa-trash'></i></a></td>";
            	echo "</tr>";
            }
            echo "</tbody>";
            echo "</table></div>";
        } else {
        	echo "<div class='alert alert-danger text-center well'>".$locale['PONT_303']."</div>\n";
        }
        echo "<div class='text-center'>".$info['ittem']['delall']."</div>\n";
        echo !empty($info['ittem']['pagenav']) ? "<div class='pull-right m-r-10'>".$info['ittem']['pagenav']."</div>\n" : '';

        closetable();
    }
}
