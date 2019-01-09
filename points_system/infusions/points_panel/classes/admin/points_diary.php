<?php
namespace PHPFusion\Points;

class PointsDiaryAdmin extends PointsModel {
    private static $instance = NULL;
    private static $locale = [];
    private $points_settings = [];
    public $diary_filter = '';
    public $diary_user = '';

    public function __construct() {
        $this->settings = self::CurrentSetup();
        self::$locale = fusion_get_locale("", POINT_LOCALE);
		$this->diary_filter = (isset($_POST['log_pmod']) && isnum($_POST['log_pmod']) ? $_POST['log_pmod'] : 0);
		$this->diary_filter = empty($this->diary_filter) ? (isset($_GET['log_pmod']) && isnum($_GET['log_pmod']) ? $_GET['log_pmod'] : 0) : $this->diary_filter;
		$this->diary_user = (isset($_POST['points_user']) && isnum($_POST['points_user']) ? $_POST['points_user'] : 0);
		$this->diary_user = empty($this->diary_user) ? (isset($_GET['points_user']) && isnum($_GET['points_user']) ? $_GET['points_user'] : 0) : $this->diary_user;
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

    private function Diaryform() {
		if (isset($_POST['diarydel'])) {
            $dellog = (isset($_POST['dellog_id'])) ? explode(",", form_sanitizer($_POST['dellog_id'], '', 'dellog_id')) : '';
            if (!empty($dellog)&& \defender::safe()) {
                foreach ($dellog as $dellog_id) {
                    dbquery("DELETE FROM ".DB_POINT_LOG." WHERE log_id=:log_id", [':log_id' => intval($dellog_id)]);
                }
                addNotice('success', self::$locale['PONT_306']);
                redirect(FUSION_REQUEST);
            }
            addNotice('warning', self::$locale['PONT_202']);
            redirect(FUSION_REQUEST);
		}

		if (isset($_POST['diaryrevoke'])) {
            $backlog = (isset($_POST['backlog_id'])) ? explode(",", form_sanitizer($_POST['backlog_id'], '', 'backlog_id')) : '';
            if (!empty($backlog) && \defender::safe()) {
            	$messages = self::$locale['PONT_210'];

                foreach ($backlog as $backlog_id) {
                	$data = dbarray(dbquery("SELECT * FROM ".DB_POINT_LOG." WHERE log_id=:log_id", [':log_id' => intval($backlog_id)]));
                    \PHPFusion\Points\UserPoint::getInstance()->setPoint($data['log_user_id'], ["mod" => $data['log_pmod'] == 1 ? 2 : 1, "point" => $data['log_point'], "messages" => $messages]);
                }
                addNotice('success', self::$locale['PONT_307']);
                redirect(FUSION_REQUEST);
            }
            addNotice('warning', self::$locale['PONT_211']);
            redirect(FUSION_REQUEST);
		}

		if (isset($_POST['deletemod'])) {
			$delmod = form_sanitizer($_POST['deletemod'], 0, 'deletemod');
			$limit = (empty($delmod) ? time() : (time() - ($delmod * 86400)));
            $max_rows = dbcount("(log_id)", DB_POINT_LOG, "log_date<='".$limit."'");
            if ($max_rows && \defender::safe()) {
            	$result = dbquery("DELETE FROM ".DB_POINT_LOG." WHERE log_date<=:log_date", [':log_date' => intval($limit)]);
            	addNotice('success', sprintf(self::$locale['PONT_308'], showdate("%Y.%m.%d - %H:%M",$limit)));
            	redirect(FUSION_REQUEST);
            }
		}
    }

    private function Diaryfilter() {
        $author_opts = [0 => self::$locale['PONT_212']];
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
            'options'    => self::$locale['adm038'],
            'onchange'   => 'document.diary_form.submit()'
        ])."</div></div>".
		closeform();
        return $info;

    }

	private function DiaryData() {
        $sql_condition = '';
        $search_string = array();
        if (!empty($_POST['log_pmod'])) {
            $search_string['log_pmod'] = array(
                "input" => form_sanitizer($_POST['log_pmod'], "", "log_pmod"), "operator" => "="
            );
        }

        if (!empty($_POST['points_user'])) {
            $search_string['log_user_id'] = array(
                "input" => form_sanitizer($_POST['points_user'], "", "points_user"), "operator" => "="
            );
        }

        if (!empty($search_string)) {
            foreach ($search_string as $key => $values) {
                if ($sql_condition) $sql_condition .= " AND ";
                $sql_condition .= "`$key` ".$values['operator'].$values['input'];
            }
        }

        $max_rows = dbcount("(log_id)", DB_POINT_LOG, $sql_condition);
        $_GET['rowstart'] = (isset($_GET['rowstart']) && isnum($_GET['rowstart']) && $_GET['rowstart'] <= $max_rows) ? $_GET['rowstart'] : 0;
        $page_nav = makepagenav($_GET['rowstart'], $this->settings['ps_page'], $max_rows, 3, FUSION_SELF.fusion_get_aidlink()."&section=diary&log_pmod=".$this->diary_filter."&points_user=".$this->diary_user."&");

        $bind = [
            ':rowstart' => $_GET['rowstart'],
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
            'pagenav' => $page_nav
	    ];
        return $info;

	}

    private function displayData($info) {

    	opentable("<i class='fa fa-book fa-lg m-r-10'></i>".self::$locale['PONT_200']);
        echo "<div class='display-inline-block pull-left'>".$info['fdata']['pagenav']."</div>";
        if ($info['filter']) {
        	echo $info['filter'];
        }
        if (!empty($info['fdata']['diary'])) {
        	echo "<div class='table-responsive m-t-20'><table class='table table-responsive table-striped'>";
        	echo "<thead>";
        	echo "<tr>";
        	echo "<td></td>";
        	echo "<td>".self::$locale['PONT_213']."</td>";
        	echo "<td>".self::$locale['PONT_214']."</td>";
        	echo "<td>".self::$locale['PONT_215']."</td>";
        	echo "<td>".self::$locale['PONT_216']."</td>";
        	echo "<td>".self::$locale['PONT_217']."</td>";
        	echo "<td>".self::$locale['delete']."</td>";
        	echo "<td>".self::$locale['PONT_218']."</td>";
        	echo "</tr>";
            echo "</thead>";
            echo "<tbody class='text-smaller'>";
            echo openform('diarycheck_form', 'post', FUSION_REQUEST);
            $i = 0;
            foreach ($info['fdata']['diary'] as $st) {
            	$i++;
            	$emotikum = "<span style='color:".($st['log_pmod'] == 1 ? '#5CB85C' : '#FF0000')."'><i class='".($st['log_pmod'] == 1 ? "fa fa-plus-square" : "fa fa-minus-square")."'></i></span>";
            	echo "<tr>";
            	echo "<td>".($_GET['rowstart'] + $i)."</td>";
            	echo "<td>".showdate("%Y.%m.%d - %H:%M",$st['log_date'])."</td>";
            	echo "<td>".trimlink($st['user_name'],20)."</td>";
            	echo "<td>".number_format($st['log_point'])."</td>\n";
            	echo "<td>".$emotikum."</td>\n";
            	echo "<td>".nl2br(parseubb(parsesmileys($st['log_descript'])))."</td>";
            	echo "<td>".form_checkbox('dellog_id[]', '', '', ['value' => $st['log_id'], 'class' => 'm-0'])."</td>\n";
            	echo "<td>".form_checkbox('backlog_id[]', '', '', ['value' => $st['log_id'], 'class' => 'm-0'])."</td>\n";
            	echo "</tr>";
            }
            echo "</tbody>";
            echo "</table></div>";
        } else {
        	echo "<div class='alert alert-danger text-center well'>".self::$locale['PONT_303']."</div>\n";
        }
        echo "<div class='text-center'>".(dbcount("(log_id)", DB_POINT_LOG, "") ?
        form_button('diarydel', self::$locale['PONT_219'], self::$locale['PONT_219'])."&nbsp;&nbsp;".
        form_button('diaryrevoke', self::$locale['PONT_220'], self::$locale['PONT_220']) : '');
        echo "</div>";
        echo closeform();

        $listadeletemod = [0 => '', 30 => 30, 20 => 20, 14 => 14, 7 => 7];
        foreach ($listadeletemod as $key => $data) {
        	$listadelete[$key] = ($key == 0 ? self::$locale['PONT_309'] : $data.self::$locale['PONT_310']);
        }

        echo openform('listadel_form', 'post', FUSION_REQUEST);
        echo form_select('deletemod', '', 0, [
            'allowclear' => TRUE,
            'options'    => $listadelete,
        ]);
        echo form_button('del_naplo', self::$locale['PONT_221'], self::$locale['PONT_221'], ['class' => 'btn-success']);
        echo closeform();
    	closetable();
    	add_to_jquery("$('#naplodeljel').bind('click', function() {
    		return confirm('".self::$locale['PONT_311']."');
    		});
    		$('#naplovissz').bind('click', function() {
    			return confirm('".self::$locale['PONT_312']."');
    			});
    		");
    }

}