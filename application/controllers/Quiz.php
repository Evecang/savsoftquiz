<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quiz extends CI_Controller {

	 function __construct()
	 {
	   parent::__construct();
	   $this->load->database();
	   $this->load->helper('url');
	   $this->load->model("quiz_model");
	   $this->load->model("user_model");
	   $this->lang->load('basic', $this->config->item('language'));

	 }

	public function index($limit='0',$list_view='grid')
	{//第二个参数的含义是，展示考试信息的形式：grid->格子，table->表格
		
		// redirect if not loggedin
		if(!$this->session->userdata('logged_in')){
			redirect('login');
			
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
		$this->session->unset_userdata('logged_in');		
		redirect('login');
		}
		
		
		
		$logged_in=$this->session->userdata('logged_in');
			 
			
			
		$data['list_view']=$list_view;	//展示考试列表的形式
		$data['limit']=$limit;	//搜索的关键词
		$data['title']=$this->lang->line('quiz');
		// fetching quiz list
		$data['result']=$this->quiz_model->quiz_list($limit);	//返回含有$limit关键词的考试列表
		$this->load->view('header',$data);
		$this->load->view('quiz_list',$data);
		$this->load->view('footer',$data);
	}


	public function wx_index($limit='0')
	{
		
		// redirect if not loggedin
		if(!$this->session->userdata('logged_in')){
			echo json_encode(array(
				'status'=>'0',
				'message'=>'without login'
			));
			return ;
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
			$this->session->unset_userdata('logged_in');		
			echo json_encode(array(
				'status'=>'0',
				'message'=>'Base url error, please redirect to login page!'
			));
			return ;
		}
		
		$logged_in=$this->session->userdata('logged_in');

		$limit='0';
		$data['limit']=$limit;	//条数

		
		// fetching quiz list
		$data['result']=$this->quiz_model->quiz_list($limit);	//返回含有$limit关键词的考试列表

		// 打印日志 方便查看
		// $this->load->helper('file');
		// write_file('./application/logs/log.txt',var_export($data['result'],true)."\n",'a+');

		echo json_encode(array(
			'status'=>'1',
			'message'=>'Fetching quiz list success!',
			'result'=>$data['result'],
			'user'=>$logged_in
		));
	}
	
	
function open_quiz($limit='0'){
	if(!$this->config->item('open_quiz')){
		exit();
	}
		$data['limit']=$limit;
		$data['title']=$this->lang->line('quiz');
		$data['open_quiz']=$this->quiz_model->open_quiz($limit);
		
		$this->load->view('header',$data);
		$this->load->view('open_quiz',$data);
		$this->load->view('footer',$data);
	
}



	public function add_new()
	{
				// redirect if not loggedin
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
			
			
	 
		$data['title']=$this->lang->line('add_new').' '.$this->lang->line('quiz');
		// fetching group list
		$data['group_list']=$this->user_model->group_list();	//返回所有的（gid升序）班级/组别
		$this->load->view('header',$data);
		$this->load->view('new_quiz',$data);
		$this->load->view('footer',$data);
	}
	
		
		
	
	
	
	
	
	
	
		public function edit_quiz($quid)
	{
				// redirect if not loggedin
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
			
			
	 
		$data['title']=$this->lang->line('edit').' '.$this->lang->line('quiz');
		// fetching group list
		$data['group_list']=$this->user_model->group_list();
		$data['quiz']=$this->quiz_model->get_quiz($quid);
		if($data['quiz']['question_selection']=='0'){
		$data['questions']=$this->quiz_model->get_questions($data['quiz']['qids']);
			 
		}else{
			$this->load->model("qbank_model");
	   $data['qcl']=$this->quiz_model->get_qcl($data['quiz']['quid']);
		
			 $data['category_list']=$this->qbank_model->category_list();
		 $data['level_list']=$this->qbank_model->level_list();
		
		}
		$this->load->view('header',$data);
		$this->load->view('edit_quiz',$data);
		$this->load->view('footer',$data);
	}
	
	public function wx_edit_quiz($quid)	//微信端 返回编辑quiz
	{
		$result['code'] = 0;
		// redirect if not loggedin
		if(!$this->session->userdata('logged_in')){
			$result['message']='Login is expired';
			echo json_encode($result); return ;
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
			$this->session->unset_userdata('logged_in');
			$result['message']='Login is expired';
			echo json_encode($result); return ;
		}
		
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['su']!='1'){
			$result['message']='Permission Denied!'; $result['code'] = 2;
			echo json_encode($result); return ;
		}
			
		// fetching group list
		$data['group_list']=$this->user_model->group_list();
		$data['quiz']=$this->quiz_model->get_quiz($quid);
		if($data['quiz']['question_selection']=='0'){
			$data['questions']=$this->quiz_model->get_questions($data['quiz']['qids']);
			 
		}else{
			$this->load->model("qbank_model");
	   		$data['qcl']=$this->quiz_model->get_qcl($data['quiz']['quid']);
			$data['category_list']=$this->qbank_model->category_list();
		 	$data['level_list']=$this->qbank_model->level_list();
		}

		$result['message']='Ready to edit'; $result['code'] = 1; $result['data']=$data;
		echo json_encode($result); return ;

	}
	
	
	
	function no_q_available($cid,$lid){
		$val="<select name='noq[]'>";
		$query=$this->db->query(" select * from savsoft_qbank where cid='$cid' and lid='$lid' ");
		$nor=$query->num_rows();
		for($i=0; $i<= $nor; $i++){
			$val.="<option value='".$i."' >".$i."</option>";
			
			
		}
		$val.="</select>";
		echo $val;
		
	}
	
	
	
	
	function remove_qid($quid,$qid){
				// redirect if not loggedin
		if(!$this->session->userdata('logged_in')){
			redirect('login');
			
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
		$this->session->unset_userdata('logged_in');		
		redirect('login');
		}
		
		if($this->quiz_model->remove_qid($quid,$qid)){
                        $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('removed_successfully')." </div>");
		}
		redirect('quiz/edit_quiz/'.$quid);
	}
	
	function add_qid($quid,$qid){
				// redirect if not loggedin
		if(!$this->session->userdata('logged_in')){
			redirect('login');
			
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
		$this->session->unset_userdata('logged_in');		
		redirect('login');
		}
		
		 $this->quiz_model->add_qid($quid,$qid);
          echo 'added';              
	}
	
	
	
	function pre_add_question($quid,$limit='0',$cid='0',$lid='0'){
				// redirect if not loggedin
		if(!$this->session->userdata('logged_in')){
			redirect('login');
			
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
		$this->session->unset_userdata('logged_in');		
		redirect('login');
		}
		$cid=$this->input->post('cid');
		$lid=$this->input->post('lid');
		redirect('quiz/add_question/'.$quid.'/'.$limit.'/'.$cid.'/'.$lid);
	}
	
	
	
		public function add_question($quid,$limit='0',$cid='0',$lid='0')
	{
				// redirect if not loggedin
		if(!$this->session->userdata('logged_in')){
			redirect('login');
			
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
		$this->session->unset_userdata('logged_in');		
		redirect('login');
		}

		$this->load->model("qbank_model");
	   
		
		$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
			exit($this->lang->line('permission_denied'));
			}
			
			
	 
		 $data['quiz']=$this->quiz_model->get_quiz($quid);
		$data['title']=$this->lang->line('add_question_into_quiz').': '.$data['quiz']['quiz_name'];
		if($data['quiz']['question_selection']=='0'){
		
		$data['result']=$this->qbank_model->question_list($limit,$cid,$lid);
		 $data['category_list']=$this->qbank_model->category_list();
		 $data['level_list']=$this->qbank_model->level_list();
			 
		}else{
			
			exit($this->lang->line('permission_denied'));
		}
		$data['limit']=$limit;
		$data['cid']=$cid;
		$data['lid']=$lid;
		$data['quid']=$quid;
		
		$this->load->view('header',$data);
		$this->load->view('add_question_into_quiz',$data);
		$this->load->view('footer',$data);
	}
	
	
	
	
	function up_question($quid,$qid,$not='1'){
				// redirect if not loggedin
		if(!$this->session->userdata('logged_in')){
			redirect('login');
			
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
		$this->session->unset_userdata('logged_in');		
		redirect('login');
		}


		$logged_in=$this->session->userdata('logged_in');
	if($logged_in['su']!="1"){
	exit($this->lang->line('permission_denied'));
	return;
	}		
	for($i=1; $i <= $not; $i++){
	$this->quiz_model->up_question($quid,$qid);
	}
	redirect('quiz/edit_quiz/'.$quid, 'refresh');
	}
	
	
	
	
	
	
	function down_question($quid,$qid,$not='1'){
				// redirect if not loggedin
		if(!$this->session->userdata('logged_in')){
			redirect('login');
			
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
		$this->session->unset_userdata('logged_in');		
		redirect('login');
		}


		$logged_in=$this->session->userdata('logged_in');
	if($logged_in['su']!="1"){
	exit($this->lang->line('permission_denied'));
	return;
	}	
			for($i=1; $i <= $not; $i++){
	$this->quiz_model->down_question($quid,$qid);
	}
	redirect('quiz/edit_quiz/'.$quid, 'refresh');
	}
	
	
	
	
		public function insert_quiz()
	{
				// redirect if not loggedin
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


		//
		$this->load->library('form_validation');
		$this->form_validation->set_rules('quiz_name', 'quiz_name', 'required');
           if ($this->form_validation->run() == FALSE)
                {
                     $this->session->set_flashdata('message', "<div class='alert alert-danger'>".validation_errors()." </div>");
					redirect('quiz/add_new/');
                }
                else
                {
					$quid=$this->quiz_model->insert_quiz();
                   
					redirect('quiz/edit_quiz/'.$quid);
                }       

	}
	
		public function update_quiz($quid)
	{
				// redirect if not loggedin
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
		$this->load->library('form_validation');
		$this->form_validation->set_rules('quiz_name', 'quiz_name', 'required');
           if ($this->form_validation->run() == FALSE)
                {
                     $this->session->set_flashdata('message', "<div class='alert alert-danger'>".validation_errors()." </div>");
					redirect('quiz/edit_quiz/'.$quid);
                }
                else
                {
					$quid=$this->quiz_model->update_quiz($quid);
                   
					redirect('quiz/edit_quiz/'.$quid);
                }       

	}
	

	public function wx_update_quiz($quid)
	{
		$result['code'] = 0;
		if(!$this->session->userdata('logged_in')){
			// redirect('login');
			$result['message'] = 'Login failed';
			echo json_encode($result); return ;
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
			$this->session->unset_userdata('logged_in');		
			// redirect('login');
			$result['message'] = 'Login failed';
			echo json_encode($result); return ;
		}
		
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['su']!='1'){
			// exit($this->lang->line('permission_denied'));
			$result['code']=2; $result['message']="Permission Denied!";
			echo json_encode($result); return ;
		}

		$quid=$this->quiz_model->wx_update_quiz($quid);
		// redirect('quiz/edit_quiz/'.$quid);
		$result['code']=1; $result['quid']=$quid;
		echo json_encode($result); return ;
      
	}
	
	
	
	
	public function remove_quiz($quid){
				// redirect if not loggedin
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
			
		if($this->quiz_model->remove_quiz($quid)){
			$this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('removed_successfully')." </div>");
		}else{
			$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_remove')." </div>");
			
		}
		redirect('quiz');
			
	}

	public function wx_remove_quiz($quid){
		$result['code']=0;
		// redirect if not loggedin
		if(!$this->session->userdata('logged_in')){
			// redirect('login');
			$result['message']='Login is expired';
			echo json_encode($result); return ;
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
			$this->session->unset_userdata('logged_in');		
			// redirect('login');
			$result['message']='Login is expired';
			echo json_encode($result); return ;
		}

		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['su']!='1'){
			// exit($this->lang->line('permission_denied'));
			$result['message']='Permission Denied!'; $result['code']=2;
			echo json_encode($result); return ;
		} 
			
		$result['code'] = 1;
		if($this->quiz_model->remove_quiz($quid)){
			$result['message'] = 'Removed Successfully';
		}else{
			$result['message'] = 'Error to remove';
		}
		// redirect('quiz');
		echo json_encode($result); return ;
			
	}
	




	public function quiz_detail($quid){	//用户在考试列表点击 Attempt 按钮应用的函数，参数为quid试卷的id
				// redirect if not loggedin
 	
		$logged_in=$this->session->userdata('logged_in');
		$gid=$logged_in['gid'];
		$data['title']=$this->lang->line('attempt').' '.$this->lang->line('quiz');
		
		$data['quiz']=$this->quiz_model->get_quiz($quid);	//得到指定试卷的所有信息，从savsoft_quiz表中查询
		$this->load->view('header',$data);
		$this->load->view('quiz_detail',$data);
		$this->load->view('footer',$data);
		
	}

	public function wx_quiz_detail($quid){	//用户在考试列表点击 Attempt 按钮应用的函数，参数为quid试卷的id

		$logged_in=$this->session->userdata('logged_in');
		$gid=$logged_in['gid'];

		$data['quiz']=$this->quiz_model->get_quiz($quid);	//得到指定试卷的所有信息，从savsoft_quiz表中查询

		echo json_encode($data['quiz']);
		return ;

	}
	
	public function validate_quiz($quid){	//正式进入测试
		$data['quiz']=$this->quiz_model->get_quiz($quid);
		// if it is without login quiz. 游客测试
		if($data['quiz']['with_login']==0 && !$this->session->userdata('logged_in')){	//用户可以不用登录进行测试
		if($this->session->userdata('logged_in_raw')){
			$logged_in=$this->session->userdata('logged_in_raw');
		}else{
			
		$userdata=array(
		'email'=>time(),
		'password'=>md5(rand(11111,99999)),
		'first_name'=>'Guest User',
		'last_name'=>time(),
		'contact_no'=>'',
		'gid'=>$this->config->item('default_gid'),
		'su'=>'0'		
		);
		$this->db->insert('savsoft_users',$userdata);	//插入游客账号
		$uid=$this->db->insert_id();
		$query=$this->db->query("select * from savsoft_users where uid='$uid' ");
		$user=$query->row_array();
		// creating login cookie
		$user['base_url']=base_url();
		$this->session->set_userdata('logged_in_raw', $user);
		$logged_in=$user;
		}		
		
		
		$gid=$logged_in['gid'];
		$uid=$logged_in['uid'];
		 
		 // if this quiz already opened by user then resume it
		 $open_result=$this->quiz_model->open_result($quid,$uid);
		 if($open_result != '0'){
		// $this->session->set_userdata('rid', $open_result);
		redirect('quiz/resume_pending/'.$open_result);
		 	
		}
		$data['quiz']=$this->quiz_model->get_quiz($quid);

		// validate start end date/time
		if($data['quiz']['start_date'] > time()){
		$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('quiz_not_available')." </div>");
		redirect('quiz/quiz_detail/'.$quid);
		 }
		// validate start end date/time
		if($data['quiz']['end_date'] < time()){
		$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('quiz_ended')." </div>");
		redirect('quiz/quiz_detail/'.$quid);
		 }


		
		// insert result row and get rid (result id)
		$rid=$this->quiz_model->insert_result($quid,$uid);
		
		$this->session->set_userdata('rid', $rid);
		redirect('quiz/attempt/'.$rid);	














		
		// without login ends

		
		}else{	//正式用户测试
		// with login starts
				// redirect if not loggedin
		if(!$this->session->userdata('logged_in')){
			$this->session->set_flashdata('message', $this->lang->line('login_required2'));
			
			redirect('login');
			
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
		$this->session->unset_userdata('logged_in');		
		redirect('login');
		}
		 
		
		
		$logged_in=$this->session->userdata('logged_in');
		
		
		$gid=$logged_in['gid'];
		$uid=$logged_in['uid'];
		 
		 // if this quiz already opened by user then resume it
		 $open_result=$this->quiz_model->open_result($quid,$uid);	//在result表中，根据uid查询正在测试的用户，返回rid(result的id)
		 if($open_result != '0'){	//有数据
		// $this->session->set_userdata('rid', $open_result);
		redirect('quiz/resume_pending/'.$open_result);	//继续作答
		 	
		}
		$data['quiz']=$this->quiz_model->get_quiz($quid);
		// validate assigned group
		if(!in_array($gid,explode(',',$data['quiz']['gids']))){
		$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('quiz_not_assigned_to_your_group')." </div>");
		redirect('quiz/quiz_detail/'.$quid);
		 }
		// validate start end date/time
		if($data['quiz']['start_date'] > time()){
		$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('quiz_not_available')." </div>");
		redirect('quiz/quiz_detail/'.$quid);
		 }
		// validate start end date/time
		if($data['quiz']['end_date'] < time()){
		$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('quiz_ended')." </div>");
		redirect('quiz/quiz_detail/'.$quid);
		 }

		// validate ip address
		if($data['quiz']['ip_address'] !=''){
		$ip_address=explode(",",$data['quiz']['ip_address']);
		$myip=$_SERVER['REMOTE_ADDR'];
		if(!in_array($myip,$ip_address)){
		$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('ip_declined')." </div>");
		redirect('quiz/quiz_detail/'.$quid);
		 }
		}
		 // validate maximum attempts
		$maximum_attempt=$this->quiz_model->count_result($quid,$uid);
		if($data['quiz']['maximum_attempts'] <= $maximum_attempt){
		$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('reached_maximum_attempt')." </div>");
		redirect('quiz/quiz_detail/'.$quid);
		 }
		
		// insert result row and get rid (result id)
		$rid=$this->quiz_model->insert_result($quid,$uid);
		
		$this->session->set_userdata('rid', $rid);
		redirect('quiz/attempt/'.$rid);	
		}
		
	}

	public function wx_validate_quiz($quid){	//正式进入测试	code: 0-login 1-success 2-resume 3-detail 
		$data['quiz']=$this->quiz_model->get_quiz($quid);
		$result['code']=0;


		// redirect if not loggedin
		if(!$this->session->userdata('logged_in')){
			$result['message']='please redirect to login';
			echo json_encode($result);
			return ;
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
			$result['message']='please redirect to login';
			echo json_encode($result);
			return ;
		}
		 
		$logged_in=$this->session->userdata('logged_in');
		
		$gid=$logged_in['gid'];
		$uid=$logged_in['uid'];
		 
		 // 该试卷已经被用户打开了
		 $open_result=$this->quiz_model->open_result($quid,$uid);	//在result表中，根据uid查询正在测试的用户，返回rid(result的id)
		if($open_result != '0'){
			redirect('quiz/wx_resume_pending/'.$open_result);	//继续作答
			// $result['code']=2;
			// $result['message']='redirect to resume_pending';
			// echo json_encode($result);
			return ;
		}
		$data['quiz']=$this->quiz_model->get_quiz($quid);
		// 班级不匹配
		if(!in_array($gid,explode(',',$data['quiz']['gids']))){
			// redirect('quiz/quiz_detail/'.$quid);
			$result['code']=3;
			$result['message']='Quiz not assigned to your group';
			echo json_encode($result);return ;
		 }
		// 未开始
		if($data['quiz']['start_date'] > time()){
		// redirect('quiz/quiz_detail/'.$quid);
			$result['code']=3;
			$result['message']='Quiz not start';
			echo json_encode($result);return ;
			
		 }
		// 已结束
		if($data['quiz']['end_date'] < time()){
		// redirect('quiz/quiz_detail/'.$quid);
			$result['code']=3;
			$result['message']='Quiz ended';
			echo json_encode($result);return ;
		 }

		// 不合法的ip地址
		if($data['quiz']['ip_address'] !=''){
			$ip_address=explode(",",$data['quiz']['ip_address']);
			$myip=$_SERVER['REMOTE_ADDR'];
			if(!in_array($myip,$ip_address)){
				// redirect('quiz/quiz_detail/'.$quid);
				$result['code']=3;
				$result['message']='Invalid IP Address';
				echo json_encode($result);return ;
		 	}
		}
		 // 最大参与次数
		$maximum_attempt=$this->quiz_model->count_result($quid,$uid);
		if($data['quiz']['maximum_attempts'] <= $maximum_attempt){
			// redirect('quiz/quiz_detail/'.$quid);
			$result['code']=3;
			$result['message']='You have reached maximum attempt';
			echo json_encode($result);return ;
		 }
		
		
		// insert result row and get rid (result id)
		$rid=$this->quiz_model->insert_result($quid,$uid);
		$this->session->set_userdata('rid', $rid);
		
		// $this->load->helper('file');
		// write_file('./application/logs/log.txt','wx_validate_quiz中的 $rid：'.var_export($this->session->userdata('rid'),true)."\n",'a+');

		// redirect('quiz/attempt/'.$rid);	
		$result['code']=1;
		$result['message']='Ready to attempt quiz';
		$result['url']='quiz_attempt/quiz_attempt?rid='.$rid;
		echo json_encode($result); return ;
		

		
	}
	
	function resume_pending($open_result){	//继续作答，$open_result为正在作答的用户的result表中的rid
		$data['title']=$this->lang->line('pending_quiz');
		$this->session->set_userdata('rid', $open_result);
		$data['openquizurl']='quiz/attempt/'.$open_result;
			 		
		//$data['openquizurl']就是要跳转的地址
		$this->load->view('header',$data);
		 $this->load->view('pending_quiz_message',$data);
		$this->load->view('footer',$data);
	
	}

	function wx_resume_pending($open_result){	//继续作答，$open_result为正在作答的用户的result表中的rid
		$this->session->set_userdata('rid', $open_result);

		// $this->load->helper('file');
		// write_file('./application/logs/log.txt','wx_resume_pending $rid：'.var_export($this->session->userdata('rid'),true)."\n",'a+');

		$data['openquizurl']='quiz_attempt/quiz_attempt?rid='.$open_result;
		//要给pending_quiz_message传$data['openquizurl']
		$result['code']=2;
		$result['message']='You have a pending quiz to submit!
		Click "Yes" redirecting to resume that quiz...';
		$result['url']=$data['openquizurl'];
		echo json_encode($result);
		return ;
	}
	
	function attempt($rid){	//测试界面
		// redirect if not loggedin
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


		$srid=$this->session->userdata('rid');
						// if linked and session rid is not matched then something wrong.
		if($rid != $srid){
		 
			$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('quiz_ended')." </div>");
			redirect('quiz/');

		}
		/*
		if(!$this->session->userdata('logged_in')){
			exit($this->lang->line('permission_denied'));
		}
		*/
		
		// get result and quiz info and validate time period
		$data['quiz']=$this->quiz_model->quiz_result($rid);	//根据rid得到考试结果信息result表+quiz表
		$data['saved_answers']=$this->quiz_model->saved_answers($rid);	//在answer表中查询作答信息
		

			
			
		// end date/time
		if($data['quiz']['end_date'] < time()){	//end_date考试结束的时间
		$this->quiz_model->submit_result($rid);	//更新结果。。
		$this->session->unset_userdata('rid');
		$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('quiz_ended')." </div>");
		redirect('quiz/quiz_detail/'.$data['quiz']['quid']);
		 }

		
		// end date/time
		if(($data['quiz']['start_time']+($data['quiz']['duration']*60)) < time()){
		$this->quiz_model->submit_result($rid);
		$this->session->unset_userdata('rid');
		$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('time_over')." </div>");
		redirect('quiz/quiz_detail/'.$data['quiz']['quid']);
		 }
		// remaining time in seconds 
		$data['seconds']=($data['quiz']['duration']*60) - (time()- $data['quiz']['start_time']);
		// get questions
		$data['questions']=$this->quiz_model->get_questions($data['quiz']['r_qids']);	//根据qid在qbank、category、level表中查
		// get options
		$data['options']=$this->quiz_model->get_options($data['quiz']['r_qids']);	//option
		$data['title']=$data['quiz']['quiz_name'];
		$this->load->view('header',$data);
		
		$this->load->view('quiz_attempt_'.$data['quiz']['quiz_template'],$data);
		$this->load->view('footer',$data);
			
	}

	function wx_attempt($rid){	//微信测试界面
		$result['code']=0;

		// redirect if not loggedin
		if(!$this->session->userdata('logged_in')){
			if(!$this->session->userdata('logged_in_raw')){
				// redirect('login');
				$result['message']='please redirect to login';
				echo json_encode($result); return ;
			}
		}
		
		if(!$this->session->userdata('logged_in')){
			$logged_in=$this->session->userdata('logged_in_raw');
		}else{
			$logged_in=$this->session->userdata('logged_in');
		}
		if($logged_in['base_url'] != base_url()){
			$this->session->unset_userdata('logged_in');		
			// redirect('login');
			$result['message']='please redirect to login';
			echo json_encode($result); return ;
		}


		$srid=$this->session->userdata('rid');
		// // 打印日志 方便查看
		// $this->load->helper('file');
		// write_file('./application/logs/log.txt',"wx_attempt中的数据——————————————————————————\n",'a+');

		// if linked and session rid is not matched then something wrong.
		if($rid != $srid){
			// redirect('quiz/');
			$result['code']=2;
			$result['message']='Try again';
			echo json_encode($result); return ;
		}
		
		
		// get result and quiz info and validate time period
		$data['quiz']=$this->quiz_model->quiz_result($rid);	//根据rid得到考试结果信息result表+quiz表
		$data['saved_answers']=$this->quiz_model->saved_answers($rid);	//在answer表中查询作答信息
			
			
		// end date/time
		if($data['quiz']['end_date'] < time()){	//end_date考试结束的时间
			$this->quiz_model->submit_result($rid);	//更新结果。。
			$this->session->unset_userdata('rid');
			// redirect('quiz/quiz_detail/'.$data['quiz']['quid']);
			$result['message']='Quiz ended';
			$result['code']=3;
			// $result['quid']=$data['quiz']['quid'];
			$result['url']='quiz/quiz_list';
			echo json_encode($result); return ;
		}
		
		
		// end date/time
		if(($data['quiz']['start_time']+($data['quiz']['duration']*60)) < time()){
			$this->quiz_model->submit_result($rid);
			$this->session->unset_userdata('rid');
			// redirect('quiz/quiz_detail/'.$data['quiz']['quid']);
			$result['code']=3;
			$result['message']='Time over and quiz submitted successfully';
			$result['url']='quiz/quiz_list';
			echo json_encode($result); return ;
		}
		// remaining time in seconds 
		$data['seconds']=($data['quiz']['duration']*60) - (time()- $data['quiz']['start_time']);
		// get questions
		$data['questions']=$this->quiz_model->get_questions($data['quiz']['r_qids']);	//根据qid在qbank、category、level表中查
		// get options
		$data['options']=$this->quiz_model->get_options($data['quiz']['r_qids']);	//option
		
		// $this->load->view('quiz_attempt_'.$data['quiz']['quiz_template'],$data);
		
		$result['code']=1;
		$result['message']='exam continue..';
		$result['data']=$data;

		echo json_encode($result); return ;

	}
		
		
	
	
	function save_answer(){	//自动保存答案
				// redirect if not loggedin
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

		echo "<pre>";
		print_r($_POST);


		$this->load->helper('file');
		// write_file('./application/logs/log.txt',"前端的数据————————————————————————————————————\n",'a+');
		// write_file('./application/logs/log.txt',var_export($_POST,true)."\n\n",'a+');
		// write_file('./application/logs/log.txt',var_export/($_FILES,true)."\n\n",'a+');


		// insert user response and calculate scroe
		echo $this->quiz_model->insert_answer();	//保存答案并 自动计算用户得分
		
		
	}

	function view_uploaded_img(){
		echo $this->quiz_model->view_uploaded();
	 }

	function wx_save_answer(){	//自动保存答案
		$result['code'] = 0;
		// redirect if not loggedin
		if(!$this->session->userdata('logged_in')){
			if(!$this->session->userdata('logged_in_raw')){
				// redirect('login');
				$result['message'] = 'Login again';
				echo json_encode($result); return ;
			}	
		}
		if(!$this->session->userdata('logged_in')){
			$logged_in=$this->session->userdata('logged_in_raw');
		}else{
			$logged_in=$this->session->userdata('logged_in');
		}
		if($logged_in['base_url'] != base_url()){
			$this->session->unset_userdata('logged_in');		
			// redirect('login');
			$result['message'] = 'Login again';
			echo json_encode($result); return ;
		}
		
		// 打印日志 方便查看
		$this->load->helper('file');
		
		// insert user response and calculate scroe
		if($this->quiz_model->wx_insert_answer()){	//保存答案并 自动计算用户得分
			$result['code']=1;
			$result['message']='save answer success';
			echo json_encode($result); return ;
		}else{
			$result['message']='save answer failed';
			echo json_encode($result); return ;
		}
	}


	function wx_upload_img(){
		
		// 打印日志 方便查看
		$this->load->helper('file');

		$result['code']=0;
		if($this->quiz_model->wx_upload_img()){
			$result['code']=1;
			$result['message']='upload success';
			echo json_encode($result); return ;
		}else{
			$result['message']='upload fail';
			echo json_encode($result); return ;
		}
	}


 function set_ind_time(){

		  // update questions time spent
		$this->quiz_model->set_ind_time();

	}
 
 
 
 function upload_photo(){
				// redirect if not loggedin
		if(!$this->session->userdata('logged_in')){
			redirect('login');
			
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
		$this->session->unset_userdata('logged_in');		
		redirect('login');
		}

		
		
if(isset($_FILES['webcam'])){
			$targets = 'photo/';
			$filename=time().'.jpg';
			$targets = $targets.''.$filename;
			if(move_uploaded_file($_FILES['webcam']['tmp_name'], $targets)){
			
				$this->session->set_flashdata('photoname', $filename);
				}
				}
}



 function submit_quiz(){	//用户提交测试数据
	 				// redirect if not loggedin
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

	 $rid=$this->session->userdata('rid');
		
		if($this->quiz_model->submit_result()){	//submit_result()函数才是关键
				
				$this->session->set_flashdata('message', "<div class='alert alert-success'>".str_replace("{result_url}",site_url('result/view_result/'.$rid),$this->lang->line('quiz_submit_successfully'))." </div>");
				
		}else{
				$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_submit')." </div>");
			
		}

		$this->session->unset_userdata('rid');

	if($this->session->userdata('logged_in')){				
 	 redirect('quiz');
	}else{
	 redirect('quiz/open_quiz/0');	
	}
 }



 function wx_submit_quiz(){	//用户提交测试后
	$result['code']=0;
	// redirect if not loggedin
	if(!$this->session->userdata('logged_in')){
		if(!$this->session->userdata('logged_in_raw')){
			// redirect('login');
			$result['message'] = 'Login'; echo json_encode($result); return ;
		}	
	}
	if(!$this->session->userdata('logged_in')){
		$logged_in=$this->session->userdata('logged_in_raw');
	}else{
		$logged_in=$this->session->userdata('logged_in');
	}
	if($logged_in['base_url'] != base_url()){
		$this->session->unset_userdata('logged_in');		
		// redirect('login');
		$result['message'] = 'Login'; echo json_encode($result); return ;
	}

	$rid=$this->session->userdata('rid');

	if($this->quiz_model->submit_result()){	//submit_result()函数才是关键
		//Quiz submitted successfully! <a href='{result_url}'>Click here</a> to view result
		//site_url('result/view_result/'.$rid),
		$result['code']=1; $result['message']='Quiz submitted successfully!';
		echo json_encode($result);

	}else{
		$result['code']=2; $result['message']='Unable to submit quiz';
		echo json_encode($result);
	}

	$this->session->unset_userdata('rid'); 
	// 打印日志 方便查看
	$this->load->helper('file');
	write_file('./application/logs/log.txt',var_export($this->session->userdata,true)."\n",'a+');	
	
	return ;
}
 
 
 
 function assign_score($rid,$qno,$score){	//提交 long answer的分数 score-1正确 -2错误
	 
	 				// redirect if not loggedin
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
			$this->quiz_model->assign_score($rid,$qno,$score);
			
			echo '1';
	 
 }



 
 
	
}
