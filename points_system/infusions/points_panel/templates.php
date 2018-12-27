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
            <th>
                <small><strong>".$info['item']['udate']['locale']."</strong></small>
            </th>
            <th>
                <small><strong>".$info['item']['upont']['locale']."</strong></small>
            </th>
            <th>
                <small><strong>".$info['item']['umod']['locale']."</strong></small>
            </th>
        </tr>
        </thead>
        <tbody class='text-smaller'>
        <tr id='point_{%id%}'>
        	<td>".$info['item']['udate']['data']."</td>
        	<td>".$info['item']['upont']['data']."</td>
        	<td>".$info['item']['umod']['data']."</td>
        </tr>
        </tbody>
    </table>";

    } else {
        echo "<div class='text-center'>".$info['message']."</div>";
    }
closetable();
    }
}

if (!function_exists('DiaryItem')) {
    function DiaryItem($info) {
        $html = \PHPFusion\Template::getInstance('diary');
        $html->set_template(POINT_CLASS.'templates/diary.html');
        $html->set_tag('opentable', fusion_get_function('opentable', $info['opentable']));
        $html->set_tag('closetable', fusion_get_function('closetable'));

        if (!empty($info['pagenav'])) {
            $html->set_block('pagenav_a', ['navigation' => $info['pagenav']]);
        }

        $html->set_tag('title1', $info['_th1']);
        $html->set_tag('title2', $info['_th2']);
        $html->set_tag('title3', $info['_th3']);
        $html->set_tag('title4', $info['_th4']);

        $html->set_tag('message', $info['message']);

        if (!empty($info['item'])) {
            	$i = 0;
                foreach ($info['item'] as $cdata) {
                    $i++;
                    $html->set_block('items', [
                        'point_id'  => $cdata['point_id'],
                        'helyezes'  => UserPoint::PointPlace(fusion_get_userdata('user_id')) != ($info['helyezes'] + $i) ? $info['helyezes'] + $i : "<span style='color:#FF0000'>".($info['helyezes'] + $i)."</span>",
                        'avatar'    => $cdata['avatar'],
                        'profile'   => $cdata['profile'],
                        'point'     => $cdata['point'],
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



