<?php
namespace PHPFusion\Points;

class PointsDiaryAdmin extends PointsModel {
    private static $instance = NULL;
    private $locale = [];
    public $diary_filter = '';
    public $diary_user = '';

    public function __construct() {
        $this->settings = self::CurrentSetup();
        $this->locale = fusion_get_locale("", POINT_LOCALE);
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
        $this->plog_pmod = filter_input(INPUT_POST, 'log_pmod', FILTER_VALIDATE_INT);
        $this->glog_pmod = filter_input(INPUT_GET, 'log_pmod', FILTER_VALIDATE_INT);
        $this->ppoints_user = filter_input(INPUT_POST, 'points_user', FILTER_VALIDATE_INT);
        $this->gpoints_user = filter_input(INPUT_GET, 'points_user', FILTER_VALIDATE_INT);

		$this->diary_filter = (!empty($this->plog_pmod) && isnum($this->plog_pmod) ? $this->plog_pmod : 0);
		$this->diary_filter = empty($this->diary_filter) ? (!empty($this->glog_pmod) && isnum($this->glog_pmod) ? $this->glog_pmod : 0) : $this->diary_filter;
		$this->diary_user = (!empty($this->ppoints_user) && isnum($this->ppoints_user) ? $this->ppoints_user : 0);
		$this->diary_user = empty($this->diary_user) ? (!empty($this->gpoints_user) && isnum($this->gpoints_user) ? $this->gpoints_user : 0) : $this->diary_user;
    }

    private function Diaryform() {
        $this->diarydel = filter_input(INPUT_POST, 'diarydel', FILTER_DEFAULT);
		if (!empty($this->diarydel)) {
            $dellog = (isset($_POST['dellog_id'])) ? explode(",", form_sanitizer($_POST['dellog_id'], '', 'dellog_id')) : '';
            if (!empty($dellog)&& \defender::safe()) {
                foreach ($dellog as $dellog_id) {
                    dbquery("DELETE FROM ".DB_POINT_LOG." WHERE log_id=:log_id", [':log_id' => (int)$dellog_id]);
                }
                addNotice('success', $this->locale['PONT_306']);
                redirect(FUSION_REQUEST);
            }
            addNotice('warning', $this->locale['PONT_202']);
            redirect(FUSION_REQUEST);
		}

        $this->diaryrevoke = filter_input(INPUT_POST, 'diaryrevoke', FILTER_DEFAULT);
		if (!empty($this->diaryrevoke)) {
            $backlog = (isset($_POST['backlog_id'])) ? explode(",", form_sanitizer($_POST['backlog_id'], '', 'backlog_id')) : '';
            if (!empty($backlog) && \defender::safe()) {
            	$messages = $this->locale['PONT_210'];

                foreach ($backlog as $backlog_id) {
                	$data = dbarray(dbquery("SELECT * FROM ".DB_POINT_LOG." WHERE log_id=:log_id", [':log_id' => (int)$backlog_id]));
                    \PHPFusion\Points\UserPoint::getInstance()->setPoint($data['log_user_id'], ["mod" => $data['log_pmod'] == 1 ? 2 : 1, "point" => $data['log_point'], "messages" => $messages]);
                }
                addNotice('success', $this->locale['PONT_307']);
                redirect(FUSION_REQUEST);
            }
            addNotice('warning', $this->locale['PONT_211']);
            redirect(FUSION_REQUEST);
		}

        $this->deletemod = filter_input(INPUT_POST, 'deletemod', FILTER_DEFAULT);
		if (!empty($this->deletemod)) {
			$delmod = form_sanitizer($this->deletemod, 0, 'deletemod');
			$limit = (empty($delmod) ? time() : (time() - ($delmod * 86400)));
            $max_rows = dbcount("(log_id)", DB_POINT_LOG, "log_date<=:limit", [':limit' => $limit]);
            if ($max_rows && \defender::safe()) {
            	dbquery("DELETE FROM ".DB_POINT_LOG." WHERE log_date<=:log_date", [':log_date' => $limit]);
            	addNotice('success', sprintf($this->locale['PONT_308'], showdate("%Y.%m.%d - %H:%M", $limit)));
            	redirect(FUSION_REQUEST);
            }
		}
    }

    private function Diaryfilter() {
        $author_opts = [0 => $this->locale['PONT_212']];
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
            'options'    => $this->locale['adm038'],
            'onchange'   => 'document.diary_form.submit()'
        ])."</div></div>".
		closeform();
        return $info;
    }

	private function DiaryData() {
        $this->rowstart = filter_input(INPUT_GET, 'rowstart', FILTER_DEFAULT);
        $sql_condition = '';
        $search_string = [];
        $this->log_pmod = filter_input(INPUT_POST, 'log_pmod', FILTER_DEFAULT);
        if (!empty($this->log_pmod)) {
            $search_string['log_pmod'] = [
                'input' => form_sanitizer($this->log_pmod, '', 'log_pmod'), 'operator' => '='
            ];
        }

        $this->points_user = filter_input(INPUT_POST, 'points_user', FILTER_DEFAULT);
        if (!empty($this->points_user)) {
            $search_string['log_user_id'] = [
                'input' => form_sanitizer($this->points_user, '', 'points_user'), 'operator' => '='
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
    	opentable("<i class='fa fa-book fa-lg m-r-10'></i>".$this->locale['PONT_200']);
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
        	    <td></td>
        	    <td><?php echo $this->locale['PONT_213'] ?></td>
        	    <td><?php echo $this->locale['PONT_214'] ?></td>
        	    <td><?php echo $this->locale['PONT_215'] ?></td>
        	    <td><?php echo $this->locale['PONT_216'] ?></td>
        	    <td><?php echo $this->locale['PONT_217'] ?></td>
        	    <td><?php echo $this->locale['delete'] ?></td>
        	    <td><?php echo $this->locale['PONT_218'] ?></td>
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
        	    <div class='alert alert-danger text-center well'><?php echo $this->locale['PONT_303'] ?></div>
        	<?php
        }
        ?>
            <div class='text-center'><?php echo (dbcount("(log_id)", DB_POINT_LOG, "") ?
        form_button('diarydel', $this->locale['PONT_219'], $this->locale['PONT_219'])."&nbsp;&nbsp;".
        form_button('diaryrevoke', $this->locale['PONT_220'], $this->locale['PONT_220']) : ''); ?>
        </div>
        <?php echo closeform();

        $listadeletemod = [0 => '', 30 => 30, 20 => 20, 14 => 14, 7 => 7];
        foreach ($listadeletemod as $key => $data) {
        	$listadelete[$key] = ($key == 0 ? $this->locale['PONT_309'] : $data.$this->locale['PONT_310']);
        }
        echo openform('listadel_form', 'post', FUSION_REQUEST);
        echo form_select('deletemod', '', 0, [
            'allowclear' => TRUE,
            'options'    => $listadelete,
        ]);
        echo form_button('del_naplo', $this->locale['PONT_221'], $this->locale['PONT_221'], ['class' => 'btn-success']);
        echo closeform();
    	closetable();
    	add_to_jquery("$('#naplodeljel').bind('click', function() {
    		return confirm('".$this->locale['PONT_311']."');
    		});
    		$('#naplovissz').bind('click', function() {
    			return confirm('".$this->locale['PONT_312']."');
    			});
    		");
    }

}