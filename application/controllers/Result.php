<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Result extends CI_Controller {

	 function __construct()
	 {
	   parent::__construct();
	   $this->load->database();
	   $this->load->helper('url');
	   $this->load->model("result_model");
	   $this->lang->load('basic', $this->config->item('language'));
		// redirect if not loggedin

	 }

	public function index($limit='0',$status='0')
	{
		
	 	if(!$this->session->userdata('logged_in')){
			redirect('login');
			
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
		$this->session->unset_userdata('logged_in');		
		redirect('login');
		}
			
			
		$data['limit']=$limit;
		$data['status']=$status;
		$data['title']=$this->lang->line('resultlist');
		// fetching result list
		$data['result']=$this->result_model->result_list($limit,$status);	//根据筛选条件limit（搜索的关键字）,status（测试的状态）从result、quiz、user表中得到结果
		// fetching quiz list
		$data['quiz_list']=$this->result_model->quiz_list();	//在quid表中，返回所有的考试
		// group list
		 $this->load->model("user_model");
		$data['group_list']=$this->user_model->group_list();	//返回所有的（gid升序）班级
		
		$this->load->view('header',$data);
		$this->load->view('result_list',$data);
		$this->load->view('footer',$data);
	}


	public function wx_index($limit='0',$status='0')
	{
		
	 	if(!$this->session->userdata('logged_in')){
	 		echo json_encode(array(
				'status'=>'0',
				'message'=>'without login'
			));
			return;
			
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
		$this->session->unset_userdata('logged_in');		
		echo json_encode(array(
				'status'=>'0',
				'message'=>'Base url error, please redirect to login page!'
			));
		return;
		}
		// $this->load->helper('file');
		// write_file('./application/logs/log.txt',var_export($logged_in).'\n','a+');

		$data['limit']=$limit;
		$data['status']=$status;
		$data['title']=$this->lang->line('resultlist');
		// fetching result list
		$data['result']=$this->result_model->result_list($limit,$status);	//根据筛选条件limit（搜索的关键字）,status（测试的状态）从result、quiz、user表中得到结果
		// fetching quiz list
		$data['quiz_list']=$this->result_model->quiz_list();	//在quid表中，返回所有的考试
		// group list
		 $this->load->model("user_model");
		$data['group_list']=$this->user_model->group_list();	//返回所有的（gid升序）班级

		$data['attempt'] = array();
		// foreach($data['result'] as $key=>$val){

		// 	$data['result'][$key]['attempt'] = $this->result_model->no_attempt($val['quid'],$val['uid']);
		// }
		// write_file('./application/logs/log.txt',var_export($data).'\n','a+');
		echo json_encode(array(
			'status'=>'1',
			'message'=>'Fetching result list success!',
			'result'=>$data['result'],
			'quiz_list'=>$data['quiz_list']
		));
	}

	public function view_total_score(){
		
		if(!$this->session->userdata('logged_in')){
			redirect('login');
			
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
		$this->session->unset_userdata('logged_in');		
		redirect('login');
		}
			
		$data['title'] = $this->lang->line('view_total_score');
		if($this->input->post('per')) { 
			$data['per'] = intval($this->input->post('per')); 
		}else{
			$data['per'] = 100;
		}

		$uid = $logged_in['uid'];
		$gid = $logged_in['gid'];
		// fetching total score
		$quizs = $this->result_model->get_quizs($gid);	//获得了 包含班级为gid的所有测试列表
		// fetching user list
		$allUser = $this->result_model->get_group_user($gid);		//获得了一个班级里的所有学生
		$data['group_name'] = $this->result_model->get_group_name($gid)['group_name'];		//获得班级名称

		$data['grades_average'] = $this->result_model->get_grades_average($quizs,$allUser);		//uid、user_name(名字)、attempt_number(作业提交次数)、total_score(作业总分)、average_score平时成绩。

		// // 打印日志 方便查看
		// $this->load->helper('file');
		// write_file('./application/logs/log.txt',var_export($data,true)."\n\n",'a+');

		$this->load->view('header',$data);
		$this->load->view('view_total_score',$data);
		$this->load->view('footer',$data);

	}

	
	public function wx_view_total_score(){
		$result['code'] = 0;
		if(!$this->session->userdata('logged_in')){
			// redirect('login');
			$result['message'] = 'Login Failed';
			echo json_encode($result); return ;
		}
		$logged_in=$this->session->userdata('logged_in');
			if($logged_in['base_url'] != base_url()){
			$this->session->unset_userdata('logged_in');		
			// redirect('login');
			$result['message'] = 'Login Failed';
			echo json_encode($result); return ;
		}
		
		// 打印日志 方便查看
		// $this->load->helper('file');
		// write_file('./application/logs/log.txt',var_export($this->input->post('per'),true)."\n\n",'a+');


		if($this->input->post('per')) { 
			$data['per'] = intval($this->input->post('per')); 
		}else{
			$data['per'] = 100;
		}

		$uid = $logged_in['uid'];
		$gid = $logged_in['gid'];
		// fetching total score
		$quizs = $this->result_model->get_quizs($gid);	//获得了 包含班级为gid的所有测试列表
		// fetching user list
		$allUser = $this->result_model->get_group_user($gid);		//获得了一个班级里的所有学生
		$data['group_name'] = $this->result_model->get_group_name($gid)['group_name'];		//获得班级名称

		$data['grades_average'] = $this->result_model->get_grades_average($quizs,$allUser);		//uid、user_name(名字)、attempt_number(作业提交次数)、total_score(作业总分)、average_score平时成绩。

		$result['code']=1; $result['data']=$data;
		echo json_encode($result); return ;
	}

	
	public function remove_result($rid){	//删除结果
		if(!$this->session->userdata('logged_in')){
			redirect('login');
			
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
		$this->session->unset_userdata('logged_in');		
		redirect('login');
		}
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
				exit($this->lang->line('permission_denied'));
			} 
			
			if($this->result_model->remove_result($rid)){
                        $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('removed_successfully')." </div>");
					}else{
						    $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_remove')." </div>");
						
					}
					redirect('result');
                     
			
		}
	

	
	function generate_report(){	//仅限管理员，在result中提交查询条件（quiz,group,date）
		if(!$this->session->userdata('logged_in')){
			redirect('login');
			
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
		$this->session->unset_userdata('logged_in');		
		redirect('login');
		}
		
		$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
				exit($this->lang->line('permission_denied'));
			} 
			
		$this->load->helper('download');
		
		$quid=$this->input->post('quid');
		$gid=$this->input->post('gid');
		$result=$this->result_model->generate_report($quid,$gid);	//在查询结果过程中，根据筛选条件从user、group、result、quiz表中得出结果
		$csvdata=$this->lang->line('result_id').",".$this->lang->line('email').",".$this->lang->line('first_name').",".$this->lang->line('last_name').",".$this->lang->line('group_name').",".$this->lang->line('quiz_name').",".$this->lang->line('score_obtained').",".$this->lang->line('percentage_obtained').",".$this->lang->line('status')."\r\n";
		foreach($result as $rk => $val){
		$csvdata.=$val['rid'].",".$val['email'].",".$val['first_name'].",".$val['last_name'].",".$val['group_name'].",".$val['quiz_name'].",".$val['score_obtained'].",".$val['percentage_obtained'].",".$val['result_status']."\r\n";
		}
		$filename=time().'.csv';	//time()返回当前时间的 Unix 时间戳
		force_download($filename, $csvdata);	//filename,file contents,Whether to try to send the actual MIME type -> return void下载服务器的文件，不知道有什么作用？？

	}
	
	
	function view_result($rid){	//查看详细结果
		
		if(!$this->session->userdata('logged_in')){
		if(!$this->session->userdata('logged_in_raw')){
			redirect('login');
		}	
		}
		if(!$this->session->userdata('logged_in')){
		$logged_in=$this->session->userdata('logged_in_raw');	
		}else{
		$logged_in=$this->session->userdata('logged_in');
		}
		if($logged_in['base_url'] != base_url()){
		$this->session->unset_userdata('logged_in');		
		redirect('login');
		}
		
		
		 	
		$data['result']=$this->result_model->get_result($rid);	//根据rid(result_id)从user、result、group、quiz表中得到结果
		$data['attempt']=$this->result_model->no_attempt($data['result']['quid'],$data['result']['uid']);	//根据quid、uid在result表中得到的数据行数，从而判断参与了几次考试
		$data['title']=$this->lang->line('result_id').' '.$data['result']['rid'];
		if($data['result']['view_answer']=='1' || $logged_in['su']=='1'){	//考试完毕后可以查看答案，并且为管理员
		 	$this->load->model("quiz_model");
			$data['saved_answers']=$this->quiz_model->saved_answers($rid);	//select * from savsoft_answers  where savsoft_answers.rid='$rid' 
			$data['questions']=$this->quiz_model->get_questions($data['result']['r_qids']);	////根据qids在qbank、category、level表中查
			$data['options']=$this->quiz_model->get_options($data['result']['r_qids']);	//select * from savsoft_options where qid in ($qids) order by FIELD(savsoft_options.qid,$qids)

		}
		// top 10 results of selected quiz
		$last_ten_result = $this->result_model->last_ten_result($data['result']['quid']);	//根据quid在user、result、quiz表中得到结果，限制10条数据
		$value=array();	//得分
		$value[]=array('Quiz Name','Percentage (%)');
		foreach($last_ten_result as $val){
			$value[]=array($val['email'].' ('.$val['first_name']." ".$val['last_name'].')',intval($val['percentage_obtained']));
		}
		$data['value']=json_encode($value);
	 
	// time spent on individual questions
		$correct_incorrect=explode(',',$data['result']['score_individual']);
		$qtime[]=array($this->lang->line('question_no'),$this->lang->line('time_in_sec'));	//Question No.? Time in Seconds
		foreach(explode(",",$data['result']['individual_time']) as $key => $val){
			if($val=='0'){
				$val=1;
			}
			if($correct_incorrect[$key]=="1"){
				$qtime[]=array($this->lang->line('q')." ".($key+1).") - ".$this->lang->line('correct')." ",intval($val));
			}else if($correct_incorrect[$key]=='2' ){
				$qtime[]=array($this->lang->line('q')." ".($key+1).") - ".$this->lang->line('incorrect')."",intval($val));
			}else if($correct_incorrect[$key]=='0' ){
				$qtime[]=array($this->lang->line('q')." ".($key+1).") -".$this->lang->line('unattempted')." ",intval($val));
			}else if($correct_incorrect[$key]=='3' ){
				$qtime[]=array($this->lang->line('q')." ".($key+1).") - ".$this->lang->line('pending_evaluation')." ",intval($val));
			}
		}
		$data['qtime']=json_encode($qtime);
		$data['percentile'] = $this->result_model->get_percentile($data['result']['quid'], $data['result']['uid'], $data['result']['score_obtained']);//返回res：res[0]该试卷的有多少个用户进行了测试，res[1]分数比该用户还<=的人数

	  
	  $uid=$data['result']['uid'];
	  $quid=$data['result']['quid'];
	  $score=$data['result']['score_obtained'];
	  $query=$this->db->query(" select * from savsoft_result where score_obtained > '$score' and quid ='$quid' group by score_obtained ");
	  $data['rank']=$query->num_rows() + 1;	//在某场考试中，分数比该用户高的个数（按照分数相比，不是按人头个数）
	  $query=$this->db->query(" select * from savsoft_result where quid ='$quid'  group by score_obtained  ");
	  $data['last_rank']=$query->num_rows();
	  $query=$this->db->query(" select * from savsoft_result where quid ='$quid'  group by score_obtained  order by score_obtained desc limit 3 ");	//降序
	  $data['toppers']=$query->result_array();
	  $query=$this->db->query(" select * from savsoft_result where quid ='$quid'  group by score_obtained  order by score_obtained asc limit 1 ");	//升序
	  $data['looser']=$query->row_array();
	
		$this->load->view('header',$data);
		if($this->session->userdata('logged_in')){
			$this->load->view('view_result',$data);
		}else{
			$this->load->view('view_result_without_login',$data);
			
		}
		$this->load->view('footer',$data);	
		
		
	}
	
	function wx_view_result($rid){	//查看详细结果
	
	if(!$this->session->userdata('logged_in')){
	if(!$this->session->userdata('logged_in_raw')){
		echo json_encode(array(
			'status'=>'0',
			'message'=>'without login'
		));
		return;
	}	
	}
	if(!$this->session->userdata('logged_in')){
	$logged_in=$this->session->userdata('logged_in_raw');	
	}else{
	$logged_in=$this->session->userdata('logged_in');
	}
	if($logged_in['base_url'] != base_url()){
	$this->session->unset_userdata('logged_in');		
	echo json_encode(array(
			'status'=>'0',
			'message'=>'Base url error, please redirect to login page!'
		));
		return;
	}
	
	
	 	
	$data['result']=$this->result_model->get_result($rid);	//根据rid(result_id)从user、result、group、quiz表中得到结果
	$data['attempt']=$this->result_model->no_attempt($data['result']['quid'],$data['result']['uid']);	//根据quid、uid在result表中得到的数据行数，从而判断参与了几次考试
	$data['title']=$this->lang->line('result_id').' '.$data['result']['rid'];
	if($data['result']['view_answer']=='1' || $logged_in['su']=='1'){	//考试完毕后可以查看答案，并且为管理员
	 	$this->load->model("quiz_model");
		$data['saved_answers']=$this->quiz_model->saved_answers($rid);	//select * from savsoft_answers  where savsoft_answers.rid='$rid' 
		$data['questions']=$this->quiz_model->get_questions($data['result']['r_qids']);	////根据qids在qbank、category、level表中查
		$data['options']=$this->quiz_model->get_options($data['result']['r_qids']);	//select * from savsoft_options where qid in ($qids) order by FIELD(savsoft_options.qid,$qids)

	}
	// top 10 results of selected quiz
	$last_ten_result = $this->result_model->last_ten_result($data['result']['quid']);	//根据quid在user、result、quiz表中得到结果，限制10条数据
	$value=array();	//得分
	$value[]=array('Quiz Name','Percentage (%)');
	foreach($last_ten_result as $val){
		$value[]=array($val['email'].' ('.$val['first_name']." ".$val['last_name'].')',intval($val['percentage_obtained']));
	}
	$data['value']=json_encode($value);
 
// time spent on individual questions
	$correct_incorrect=explode(',',$data['result']['score_individual']);
	$qtime[]=array($this->lang->line('question_no'),$this->lang->line('time_in_sec'));	//Question No.? Time in Seconds
	foreach(explode(",",$data['result']['individual_time']) as $key => $val){
		if($val=='0'){
			$val=1;
		}
		if($correct_incorrect[$key]=="1"){
			$qtime[]=array($this->lang->line('q')." ".($key+1).") - ".$this->lang->line('correct')." ",intval($val));
		}else if($correct_incorrect[$key]=='2' ){
			$qtime[]=array($this->lang->line('q')." ".($key+1).") - ".$this->lang->line('incorrect')."",intval($val));
		}else if($correct_incorrect[$key]=='0' ){
			$qtime[]=array($this->lang->line('q')." ".($key+1).") -".$this->lang->line('unattempted')." ",intval($val));
		}else if($correct_incorrect[$key]=='3' ){
			$qtime[]=array($this->lang->line('q')." ".($key+1).") - ".$this->lang->line('pending_evaluation')." ",intval($val));
		}
	}
	$data['qtime']=json_encode($qtime);
	$data['percentile'] = $this->result_model->get_percentile($data['result']['quid'], $data['result']['uid'], $data['result']['score_obtained']);//返回res：res[0]该试卷的有多少个用户进行了测试，res[1]分数比该用户还<=的人数

	  
	  $uid=$data['result']['uid'];
	  $quid=$data['result']['quid'];
	  $score=$data['result']['score_obtained'];
	  $query=$this->db->query(" select * from savsoft_result where score_obtained > '$score' and quid ='$quid' group by score_obtained ");
	  $data['rank']=$query->num_rows() + 1;	//在某场考试中，分数比该用户高的个数（按照分数相比，不是按人头个数）
	  $query=$this->db->query(" select * from savsoft_result where quid ='$quid'  group by score_obtained  ");
	  $data['last_rank']=$query->num_rows();
	  $query=$this->db->query(" select * from savsoft_result where quid ='$quid'  group by score_obtained  order by score_obtained desc limit 3 ");	//降序
	  $data['toppers']=$query->result_array();
	  $query=$this->db->query(" select * from savsoft_result where quid ='$quid'  group by score_obtained  order by score_obtained asc limit 1 ");	//升序
	  $data['looser']=$query->row_array();

	  echo json_encode(array(
				'status'=>'1',
				'message'=>'success get view_result data!',
				'result'=>$data
			));

	  return;


		// $this->load->view('header',$data);
		// if($this->session->userdata('logged_in')){
		// 	$this->load->view('view_result',$data);
		// }else{
		// 	$this->load->view('view_result_without_login',$data);
		// }
		// $this->load->view('footer',$data);
	}
	
	
	function generate_certificate($rid){
				if(!$this->session->userdata('logged_in')){
			redirect('login');
			
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
		$this->session->unset_userdata('logged_in');		
		redirect('login');
		}
		if(!$this->config->item('dompdf')){
		exit('DOMPDF library disabled in config.php file');
		
		}
	$data['result']=$this->result_model->get_result($rid);
	if($data['result']['gen_certificate']=='0'){
		exit();
	}
		// save qr 
	$enu=urlencode(site_url('login/verify_result/'.$rid));

	$qrname="./upload/".time().'.jpg';
	$durl="https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl=".$enu."&choe=UTF-8";
	copy($durl,$qrname);
	 
	
	$certificate_text=$data['result']['certificate_text'];
	$certificate_text=str_replace('{qr_code}',"<img src='".$qrname."'>",$certificate_text);
	$certificate_text=str_replace('{email}',$data['result']['email'],$certificate_text);
	$certificate_text=str_replace('{first_name}',$data['result']['first_name'],$certificate_text);
	$certificate_text=str_replace('{last_name}',$data['result']['last_name'],$certificate_text);
	$certificate_text=str_replace('{percentage_obtained}',$data['result']['percentage_obtained'],$certificate_text);
	$certificate_text=str_replace('{score_obtained}',$data['result']['score_obtained'],$certificate_text);
	$certificate_text=str_replace('{quiz_name}',$data['result']['quiz_name'],$certificate_text);
	$certificate_text=str_replace('{status}',$data['result']['result_status'],$certificate_text);
	$certificate_text=str_replace('{result_id}',$data['result']['rid'],$certificate_text);
	$certificate_text=str_replace('{generated_date}',date('Y-m-d H:i:s',$data['result']['end_time']),$certificate_text);
	
	$data['certificate_text']=$certificate_text;
	// $this->load->view('view_certificate',$data);
	$this->load->library('pdf');
	$this->pdf->load_view('view_certificate',$data);
	$this->pdf->render();
	$filename=date('Y-M-d_H:i:s',time()).".pdf";
	$this->pdf->stream($filename);

	
	}
	
	
	function preview_certificate($quid){
		if(!$this->session->userdata('logged_in')){
			redirect('login');
			
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
		$this->session->unset_userdata('logged_in');		
		redirect('login');
		}

		$this->load->model("quiz_model");
	  
	$data['result']=$this->quiz_model->get_quiz($quid);
	if($data['result']['gen_certificate']=='0'){
		exit();
	}
		// save qr 
	$enu=urlencode(site_url('login/verify_result/0'));
$tm=time();
	$qrname="./upload/".$tm.'.jpg';
	$durl="https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl=".$enu."&choe=UTF-8";
	copy($durl,$qrname);
	 $qrname2=base_url('/upload/'.$tm.'.jpg');
	
	
	$certificate_text=$data['result']['certificate_text'];
	$certificate_text=str_replace('{qr_code}',"<img src='".$qrname2."'>",$certificate_text);
	$certificate_text=str_replace('{result_id}','1023',$certificate_text);
	$certificate_text=str_replace('{generated_date}',date('Y-m-d H:i:s',time()),$certificate_text);
	
	$data['certificate_text']=$certificate_text;
	  $this->load->view('view_certificate_2',$data);
	 
	
	}
	
}
