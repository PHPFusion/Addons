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

if (!function_exists('DiaryItem')) {
    function DiaryItem($info) {
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



