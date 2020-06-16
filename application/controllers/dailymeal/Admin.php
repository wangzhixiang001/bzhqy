<?php
// 127.0.0.1/kfweixin/index.php/dailymeal/admin
class Admin extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('admin/auth_model');
		$this->user = $this->auth_model->checkPower(1);
		$this->load->database();
	}

	public function index() {
		$this->load->view('newadmin/dailymeal/list');
	}

	public function userlist() {
		$this->load->view('newadmin/dailymeal/userlist');
	}

	public function msglist() {
		$this->load->view('newadmin/dailymeal/msg');
	}

	public function ajaxlist() {
		$begin = $this->input->post('begin');
		$end = $this->input->post('end');
		$draw = $_POST['draw'];
		$start = $_POST['start'];
		$length = $_POST['length'];

		$all = $this->db->count_all_results('bzh_dailymeal_main');

		$this->db->start_cache();

		if (!empty($begin)) {
			$this->db->where('time >=', $begin);
		}

		if (!empty($end)) {
			$this->db->where('time <', date('Y-m-d', strtotime($end . ' +1 day')));
		}

		$this->db->stop_cache();

		$some = $this->db->count_all_results('bzh_dailymeal_main');
		$list = $this->db->select('qy_user.name,bzh_dailymeal_main.*')->join('qy_user', 'qy_user.userid=bzh_dailymeal_main.userid')->order_by('Id')->get('bzh_dailymeal_main', $length, $start)->result();

		$data = [
			'draw' => (int) $draw,
			'recordsTotal' => $all,
			'recordsFiltered' => $some,
			'data' => $list
		];
		echo json_encode($data);
	}

	public function ajaxuserlist() {
		$begin = $this->input->post('begin');
		$end = $this->input->post('end');
		$draw = $_POST['draw'];
		$start = $_POST['start'];
		$length = $_POST['length'];
		$query = 'select userid,count(*) as num from bzh_dailymeal_main where type=1';
		if (!empty($begin)) {
			$query .= " and time>='$begin'";
		}

		if (!empty($end)) {
			$query .= " and time<'" . date('Y-m-d', strtotime($end . ' +1 day')) . "'";
		}

		$query = "select u.name,m.* from ($query group by userid) m join qy_user u on u.userid=m.userid";

		$list = $this->db->query($query)->result();
		$all = count($list);

		$data = [
			'draw' => (int) $draw,
			'recordsTotal' => $all,
			'recordsFiltered' => $all,
			'data' => $list
		];
		echo json_encode($data);
	}

	public function out() {
		$begin = $this->input->get('begin');
		$end = $this->input->get('end');

		$this->db->start_cache();
		if (!empty($begin)) {
			$this->db->where('time >=', $begin);
		}

		if (!empty($end)) {
			$this->db->where('time <', date('Y-m-d', strtotime($end . ' +1 day')));
		}

		$this->db->stop_cache();
		$some = $this->db->count_all_results('bzh_dailymeal_main');
		$list = $this->db->select('qy_user.userid,qy_user.name,bzh_dailymeal_main.*')->join('qy_user', 'qy_user.userid=bzh_dailymeal_main.userid')->get('bzh_dailymeal_main')->result();
		$cs = [
			['强赞，不解释！', '味道超好！', '食材非常丰富！'],
			['还不错哦~', '味道好', '分量足'],
			['不喜欢不喜欢>_<', '味道不好', '类型不喜欢', '不够吃……']
		];
		$ev = ['好评', '中评', '差评'];
		if ($some > 3000) {
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="报饭记录-' . date('Y_m_d') . '.csv"');
			header('Cache-Control: max-age=0');
			$file = fopen('php://output', 'a');

			$titie = "员工编号,姓名,报饭状态,报饭时间,取消时间,就餐状态,就餐时间,点评,留言\n";
			fwrite($file, iconv('utf-8', 'gbk', $titie));
			$limit = 5000;
			foreach ($list as $data) {
				$limit--;
				$temp[0] = iconv('utf-8', 'gbk', $data->userid);
				$temp[1] = iconv('utf-8', 'gbk', $data->name);
				$temp[2] = iconv('utf-8', 'gbk', ($data->type == 1) ? '已报饭' : (($data->type == 9) ? '未报饭' : '已取消'));
				$temp[3] = iconv('utf-8', 'gbk', ($data->type == 1) ? $data->time : '');
				$temp[4] = iconv('utf-8', 'gbk', ($data->type == 0) ? $data->time : '');
				$temp[2] = iconv('utf-8', 'gbk', ($data->status == 0) ? '未就餐' : ($data->status == 1 ? '已就餐' : '已就餐(补签)'));
				$temp[3] = iconv('utf-8', 'gbk', ($data->status > 0) ? $data->eat_time : '');
				$temp[5] = iconv('utf-8', 'gbk', ($data->eval == 0) ? '' : ($ev[substr($data->eval, 1, 1)] . '(' . $cs[substr($data->eval, 1, 1)][substr($data->eval, 2, 1)] . ')'));
				$temp[6] = iconv('utf-8', 'gbk', ($data->msg == '') ? '' : ($data->msg . ' (' . $data->msgtime . ')'));
				fputcsv($file, $temp);
				if ($limit < 1) {
					ob_flush();
					flush();
					$limit = 5000;
				}
			}
			fclose($file);
		} else {
			set_time_limit(90);
			ini_set('memory_limit', '512M');
			$this->load->library('PHPExcel');
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator('admin')
				->setLastModifiedBy('admin')
				->setTitle('报饭记录')
				->setCategory('admin');
			$worksheet = $objPHPExcel->setActiveSheetIndex(0);

			$worksheet->setCellValue('A1', '员工编号')
				->setCellValue('B1', '姓名')
				->setCellValue('C1', '报饭状态')
				->setCellValue('D1', '报饭时间')
				->setCellValue('E1', '取消时间')
				->setCellValue('F1', '就餐状态')
				->setCellValue('G1', '就餐时间')
				->setCellValue('H1', '点评')
				->setCellValue('I1', '留言');

			$worksheet->getColumnDimension('A')->setWidth(15);
			$worksheet->getColumnDimension('B')->setWidth(15);
			$worksheet->getColumnDimension('C')->setWidth(15);
			$worksheet->getColumnDimension('D')->setWidth(20);
			$worksheet->getColumnDimension('E')->setWidth(20);
			$worksheet->getColumnDimension('F')->setWidth(15);
			$worksheet->getColumnDimension('G')->setWidth(20);
			$worksheet->getColumnDimension('H')->setWidth(50);
			$worksheet->getColumnDimension('I')->setWidth(50);

			$row = 2;

			foreach ($list as $data) {
				$lie = 0;
				$worksheet->setCellValueByColumnAndRow($lie++, $row, $data->userid);
				$worksheet->setCellValueByColumnAndRow($lie++, $row, $data->name);
				$worksheet->setCellValueByColumnAndRow($lie++, $row, ($data->type == 1) ? '已报饭' : (($data->type == 9) ? '未报饭' : '已取消'));
				$worksheet->setCellValueByColumnAndRow($lie++, $row, ($data->type == 1) ? $data->time : '');
				$worksheet->setCellValueByColumnAndRow($lie++, $row, ($data->type == 0) ? $data->time : '');
				$worksheet->setCellValueByColumnAndRow($lie++, $row, ($data->status == 0) ? '未就餐' : ($data->status == 1 ? '已就餐' : '已就餐(补签)'));
				$worksheet->setCellValueByColumnAndRow($lie++, $row, ($data->status > 0) ? $data->eat_time : '');
				$worksheet->setCellValueByColumnAndRow($lie++, $row, ($data->eval == 0) ? '' : ($ev[substr($data->eval, 1, 1)] . '(' . $cs[substr($data->eval, 1, 1)][substr($data->eval, 2, 1)] . ')'));
				$worksheet->setCellValueByColumnAndRow($lie++, $row, ($data->msg == '') ? '' : ($data->msg . ' (' . $data->msgtime . ')'));
				$row++;
			}
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="报饭记录_' . $begin . '_' . $end . '.xlsx"');
			header('Cache-Control: max-age=0');
			$objWriter->save('php://output');
		}
	}

	public function outnum() {
		$begin = $this->input->get('begin');
		$end = $this->input->get('end');

		$query = 'select userid,count(*) as num from bzh_dailymeal_main where type=1';
		if (!empty($begin)) {
			$query .= " and time>='$begin'";
		}

		if (!empty($end)) {
			$query .= " and time<'" . date('Y-m-d', strtotime($end . ' +1 day')) . "'";
		}

		$query = "select u.name,m.* from ($query group by userid) m join qy_user u on u.userid=m.userid";

		$list = $this->db->query($query)->result();

		$this->load->library('PHPExcel');
		$objPHPExcel = $this->phpexcel;
		$objPHPExcel->getProperties()->setCreator('admin')
			->setLastModifiedBy('admin')
			->setTitle('报饭次数统计')
			->setCategory('admin');
		$worksheet = $objPHPExcel->setActiveSheetIndex(0);

		$worksheet->setCellValue('A1', '员工编号')
			->setCellValue('B1', '姓名')
			->setCellValue('C1', '报饭次数');

		$worksheet->getColumnDimension('A')->setWidth(15);
		$worksheet->getColumnDimension('B')->setWidth(15);
		$worksheet->getColumnDimension('C')->setWidth(10);

		$row = 2;

		foreach ($list as $data) {
			$lie = 0;
			$worksheet->setCellValueByColumnAndRow($lie++, $row, $data->userid);
			$worksheet->setCellValueByColumnAndRow($lie++, $row, $data->name);
			$worksheet->setCellValueByColumnAndRow($lie++, $row, $data->num);
			$row++;
		}
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="报饭次数统计_' . $begin . '_' . $end . '.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	}
}