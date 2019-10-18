<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: classes/admin/points_diary.php
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

class PointsDiaryAdmin extends PointsModel {
    private static $instance = NULL;
    public $diary_filter = '';
    public $diary_user = '';
    private $settings;
    private $rowstart;

    public function __construct() {
        $this->settings = self::CurrentSetup();
        self::$locale = fusion_get_locale("", POINT_LOCALE);
        self::InputFilter();
    }

    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new static();
        }
       return self::$instance;
    }

    public function displayDiaryAdmin() {
    	self::Diaryform();
    	$info = [
    	    'filter' => self::Diaryfilter(),
    	    'fdata'  => self::DiaryData()
    	];
    	self::displayData($info);
    }

    private function InputFilter() {
        $plog_pmod = filter_input(INPUT_POST, 'log_pmod', FILTER_VALIDATE_INT);
        $glog_pmod = filter_input(INPUT_GET, 'log_pmod', FILTER_VALIDATE_INT);
        $ppoints_user = filter_input(INPUT_POST, 'points_user', FILTER_VALIDATE_INT);
        $gpoints_user = filter_input(INPUT_GET, 'points_user', FILTER_VALIDATE_INT);

        $this->diary_filter = (!empty($plog_pmod) ? $plog_pmod : (!empty($glog_pmod) ? $glog_pmod : 0));
        $this->diary_user = (!empty($ppoints_user) ? $ppoints_user : (!empty($gpoints_user) ? $gpoints_user : 0));
    }

    private function Diaryform() {
        $diarydel = filter_input(INPUT_POST, 'diarydel', FILTER_DEFAULT);
		if (!empty($diarydel)) {
            $dellog = (isset($_POST['dellog_id'])) ? explode(",", form_sanitizer($_POST['dellog_id'], '', 'dellog_id')) : '';
            if (!empty($dellog)&& \defender::safe()) {
                foreach ($dellog as $dellog_id) {
                    dbquery("DELETE FROM ".DB_POINT_LOG." WHERE log_id=:log_id", [':log_id' => (int)$dellog_id]);
                }
                addNotice('success', self::$locale['PSP_D20']);
                redirect(FUSION_REQUEST);
            }
            addNotice('warning', self::$locale['PSP_D01']);
            redirect(FUSION_REQUEST);
		}

        $diaryrevoke = filter_input(INPUT_POST, 'diaryrevoke', FILTER_DEFAULT);
		if (!empty($diaryrevoke)) {
            $backlog = (isset($_POST['backlog_id'])) ? explode(",", form_sanitizer($_POST['backlog_id'], '', 'backlog_id')) : '';
            if (!empty($backlog) && \defender::safe()) {
            	$messages = self::$locale['PSP_D21'];

                foreach ($backlog as $backlog_id) {
                	$data = dbarray(dbquery("SELECT * FROM ".DB_POINT_LOG." WHERE log_id=:log_id", [':log_id' => (int)$backlog_id]));
                    UserPoint::getInstance()->setPoint($data['log_user_id'], ["mod" => $data['log_pmod'] == 1 ? 2 : 1, "point" => $data['log_point'], "messages" => $messages]);
                }
                addNotice('success', self::$locale['PSP_D22']);
                redirect(FUSION_REQUEST);
            }
            addNotice('warning', self::$locale['PSP_D23']);
            redirect(FUSION_REQUEST);
		}

        $deletemod = filter_input(INPUT_POST, 'deletemod', FILTER_DEFAULT);
		if (!empty($deletemod)) {
			$delmod = form_sanitizer($deletemod, 0, 'deletemod');
			$limit = (empty($delmod) ? time() : (time() - ($delmod * 86400)));
            $max_rows = dbcount("(log_id)", DB_POINT_LOG, "log_date<=:limit", [':limit' => $limit]);
            if ($max_rows && \defender::safe()) {
            	dbquery("DELETE FROM ".DB_POINT_LOG." WHERE log_date<=:log_date", [':log_date' => $limit]);
            	addNotice('success', sprintf(self::$locale['PSP_D24'], showdate("%Y.%m.%d - %H:%M", $limit)));
            	redirect(FUSION_REQUEST);
            }
		}
    }

    private function Diaryfilter() {
        $author_opts = [0 => self::$locale['PSP_D25']];
        $result = dbquery("SELECT pl.*, pu.user_id, pu.user_name, pu.user_status
            FROM ".DB_POINT_LOG." AS pl
            LEFT JOIN ".DB_USERS." AS pu ON pl.log_user_id = pu.user_id
            GROUP BY pu.user_id
            ORDER BY pu.user_name ASC
        ");

        if (dbrows($result) > 0) {
        	while ($data = dbarray($result)) {
        		$author_opts[$data['user_id']] = $data['user_name'];
        	}
        }

		$info = "<div class='clearfix'>".openform('diary_form', 'post', FUSION_REQUEST).
        "<div class='display-inline-block pull-right'>".form_select("points_user", "", $this->diary_user, [
            "allowclear"  => TRUE,
            "options"     => $author_opts,
            'onchange'   => 'document.diary_form.submit()'
        ])."</div>".
        "<div class='display-inline-block pull-right'>".form_select('log_pmod', '', $this->diary_filter, [
            'allowclear' => TRUE,
            'options'    => self::$locale['PSP_LST'],
            'onchange'   => 'document.diary_form.submit()'
        ])."</div></div>".
		closeform();
        return $info;
    }

	private function DiaryData() {
        $this->rowstart = filter_input(INPUT_GET, 'rowstart', FILTER_DEFAULT);
        $sql_condition = '';
        $search_string = [];
        $plog_pmod = filter_input(INPUT_POST, 'log_pmod', FILTER_DEFAULT);
        $glog_pmod = filter_input(INPUT_GET, 'log_pmod', FILTER_DEFAULT);
		$log_pmod = (!empty($glog_pmod) ? $glog_pmod : (!empty($plog_pmod) ? $plog_pmod : 0));

        if (!empty($log_pmod)) {
            $search_string['log_pmod'] = [
                'input' => form_sanitizer($log_pmod, '', 'log_pmod'), 'operator' => '='
            ];
        }

        $ppoints_user = filter_input(INPUT_POST, 'points_user', FILTER_DEFAULT);
        $gpoints_user = filter_input(INPUT_GET, 'points_user', FILTER_DEFAULT);
		$points_user = (!empty($gpoints_user) ? $gpoints_user : (!empty($ppoints_user) ? $ppoints_user : 0));

        if (!empty($points_user)) {
            $search_string['log_user_id'] = [
                'input' => form_sanitizer($points_user, '', 'points_user'), 'operator' => '='
            ];
        }

        if (!empty($search_string)) {
            foreach ($search_string as $key => $values) {
                if ($sql_condition) $sql_condition .= " AND ";
                $sql_condition .= "`$key` ".$values['operator'].$values['input'];
            }
        }

        $max_rows = dbcount("(log_id)", DB_POINT_LOG, $sql_condition);
        $this->rowstart = (!empty($this->rowstart) && isnum($this->rowstart) && $this->rowstart <= $max_rows) ? $this->rowstart : 0;

        $bind = [
            ':rowstart' => $this->rowstart,
            ':limit'    => $this->settings['ps_page']
        ];
	    $result = dbquery("SELECT pu.user_id, pu.user_name, pu.user_status, pu.user_avatar, pu.user_joined, pu.user_level, pl.*
	        FROM ".DB_POINT_LOG." AS pl
	        LEFT JOIN ".DB_USERS." AS pu ON pu.user_id = pl.log_user_id
	        ".($sql_condition ? "WHERE ".$sql_condition : "")."
	        ORDER BY pl.log_date DESC
            LIMIT :rowstart, :limit", $bind);

        $inf = [];
        while ($data = dbarray($result)){
            $inf[] = $data;
	    }

	    $info = [
	        'diary'   => $inf,
            'max_row' => $max_rows,
            'pagenav' => makepagenav($this->rowstart, $this->settings['ps_page'], $max_rows, 3, FUSION_SELF.fusion_get_aidlink()."&section=diary&log_pmod=".$this->diary_filter."&points_user=".$this->diary_user."&")
	    ];
        return $info;

	}

    private function displayData($dinfo) {
    	opentable("<i class='fa fa-book fa-lg m-r-10'></i>".self::$locale['PSP_D00']);
    	?>
            <div class='display-inline-block pull-left'><?php echo $dinfo['fdata']['pagenav'] ?></div>
    	<?php
        if ($dinfo['filter']) {

        	echo $dinfo['filter'];
        }

        if (!empty($dinfo['fdata']['diary'])) {
            ?>
            <div class='table-responsive m-t-20'><table class='table table-responsive table-striped'>
        	    <thead>
        	    <tr>
        	    <th></th>
        	    <th><?php echo self::$locale['PSP_D26'] ?></th>
        	    <th><?php echo self::$locale['PSP_D27'] ?></th>
        	    <th><?php echo self::$locale['PSP_D28'] ?></th>
        	    <th><?php echo self::$locale['PSP_D29'] ?></th>
        	    <th><?php echo self::$locale['PSP_D30'] ?></th>
        	    <th><?php echo self::$locale['delete'] ?></th>
        	    <th><?php echo self::$locale['PSP_D31'] ?></th>
        	    </tr>
        	    </thead>
        	    <tbody class='text-smaller'>
        	    <?php echo openform('diarycheck_form', 'post', FUSION_REQUEST);
        	    $pdi = 0;
        	    foreach ($dinfo['fdata']['diary'] as $std) {
        	        $pdi++;
        	        $emotikum = "<span style='color:".($std['log_pmod'] == 1 ? '#5CB85C' : '#FF0000')."'><i class='".($std['log_pmod'] == 1 ? "fa fa-plus-square" : "fa fa-minus-square")."'></i></span>";
        	        ?>
        	        <tr>
        	        <td><?php echo ($this->rowstart + $pdi) ?></td>
        	        <td><?php echo showdate("%Y.%m.%d - %H:%M", $std['log_date']) ?></td>
        	        <td><?php echo trimlink($std['user_name'],20) ?></td>
        	        <td><?php echo number_format($std['log_point']) ?></td>
        	        <td><?php echo $emotikum ?></td>
        	        <td><?php echo nl2br(parseubb(parsesmileys($std['log_descript']))) ?></td>
        	        <td><?php echo form_checkbox('dellog_id[]', '', '', ['value' => $std['log_id'], 'class' => 'm-0']) ?></td>
        	        <td><?php echo form_checkbox('backlog_id[]', '', '', ['value' => $std['log_id'], 'class' => 'm-0']) ?></td>
        	        </tr>
        	    <?php
        	    }
        	    ?>
        	    </tbody>
        	    </table></div>
        	    <?php
        } else {
        	?>
        	    <div class='text-center well'><?php echo self::$locale['PSP_D12'] ?></div>
        	<?php
        }
        ?>
            <div class='text-center'><?php echo (dbcount("(log_id)", DB_POINT_LOG, "") ?
        form_button('diarydel', self::$locale['PSP_D32'], self::$locale['PSP_D32'])."&nbsp;&nbsp;".
        form_button('diaryrevoke', self::$locale['PSP_D33'], self::$locale['PSP_D33']) : ''); ?>
        </div>
        <?php echo closeform();

        $listadeletemod = [0 => '', 30 => 30, 20 => 20, 14 => 14, 7 => 7];
        $listadelete = [];
        foreach ($listadeletemod as $key => $data) {
        	$listadelete[$key] = ($key == 0 ? self::$locale['PSP_D34'] : $data.self::$locale['PSP_D35']);
        }
        echo openform('listadel_form', 'post', FUSION_REQUEST);
        echo form_select('deletemod', '', 0, [
            'allowclear' => TRUE,
            'options'    => $listadelete
        ]);
        echo form_button('del_naplo', self::$locale['PSP_D36'], self::$locale['PSP_D36'], ['class' => 'btn-success']);
        echo closeform();
    	closetable();
    	add_to_jquery("$('#diarydel').bind('click', function() {
    		return confirm('".self::$locale['PSP_D37']."');
    		});
    		$('#diaryrevoke').bind('click', function() {
    			return confirm('".self::$locale['PSP_D38']."');
    			});
    		");
    }

}