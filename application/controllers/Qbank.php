<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Qbank extends CI_Controller {

	 function __construct()
	 {
	   parent::__construct();
	   $this->load->database();
	   $this->load->helper('url');
	   $this->load->model("qbank_model");
	   $this->lang->load('basic', $this->config->item('language'));
		// redirect if not loggedin
		if(!$this->session->userdata('logged_in')){
			redirect('login');
			
		}
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['base_url'] != base_url()){
		$this->session->unset_userdata('logged_in');		
		redirect('login');
		}
	 }

	public function index($limit='0',$cid='0',$lid='0')
	{
		$this->load->helper('form');
		$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){	//如果不是管理员，没有权限
			exit($this->lang->line('permission_denied'));
			}
			
		$data['category_list']=$this->qbank_model->category_list();	//返回所有的科目
		$data['level_list']=$this->qbank_model->level_list();	//返回所有的难易程度
		
		$data['limit']=$limit;
		$data['cid']=$cid;	//科目的id
		$data['lid']=$lid;	//难易程度的id
		 
		
		$data['title']=$this->lang->line('qbank');	//Question Bank
		// fetching user list
		$data['result']=$this->qbank_model->question_list($limit,$cid,$lid);	//返回符合筛选后的问题列表
		$this->load->view('header',$data);
		$this->load->view('question_list',$data);
		$this->load->view('footer',$data);
	}
	
	public function remove_question($qid){	//删除题目

			//管理员才有权限
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
				exit($this->lang->line('permission_denied'));
			} 
			
			if($this->qbank_model->remove_question($qid)){
                        $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('removed_successfully')." </div>");
					}else{
						    $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_remove')." </div>");
						
					}
					redirect('qbank');
                     
			
		}
	
	
	
	function pre_question_list($limit='0',$cid='0',$lid='0'){	//这是用户选择了category/level 筛选条件之后执行的函数，实际上又是调用了index()
		$cid=$this->input->post('cid');
		$lid=$this->input->post('lid');
		redirect('qbank/index/'.$limit.'/'.$cid.'/'.$lid);
	}
	
	
	public function pre_new_question()	//点击Add new首先进来的界面
	{
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['su']!='1'){
		exit($this->lang->line('permission_denied'));
		}
			
		if($this->input->post('question_type')){
			if($this->input->post('question_type')=='1'){	//单选
				$nop=$this->input->post('nop');
				if(!is_numeric($this->input->post('nop'))){
					$nop=4;
				}
				redirect('qbank/new_question_1/'.$nop);
			}
			if($this->input->post('question_type')=='2'){	//多选
				$nop=$this->input->post('nop');
				if(!is_numeric($this->input->post('nop'))){
					$nop=4;
				}
				redirect('qbank/new_question_2/'.$nop);
			}
			if($this->input->post('question_type')=='3'){	//匹配
				$nop=$this->input->post('nop');
				if(!is_numeric($this->input->post('nop'))){
					$nop=4;
				}
				redirect('qbank/new_question_3/'.$nop);
			}
			if($this->input->post('question_type')=='4'){	//填空
				$nop=$this->input->post('nop');
				if(!is_numeric($this->input->post('nop'))){
					$nop=4;
				}
				redirect('qbank/new_question_4/'.$nop);
			}
			if($this->input->post('question_type')=='5'){	//long answer
				$nop=$this->input->post('nop');
				if(!is_numeric($this->input->post('nop'))){
					$nop=4;
				}
				redirect('qbank/new_question_5/'.$nop);
			}
			if($this->input->post('question_type')=='6'){	//完形填空 Cloze Test
				$nop=$this->input->post('nop');
				if(!is_numeric($this->input->post('nop'))){
					$nop=10;
				}
				redirect('qbank/new_question_6/'.$nop);
			}
		}
		
		$data['title']=$this->lang->line('add_new').' '.$this->lang->line('question');
		$this->load->view('header',$data);
		$this->load->view('pre_new_question',$data);
		$this->load->view('footer',$data);
	}
	
	public function new_question_1($nop='4')
	{
		
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
			exit($this->lang->line('permission_denied'));
			}
			
			if($this->input->post('question')){
				if($this->qbank_model->insert_question_1()){
                $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_added_successfully')." </div>");
				}else{
				 $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_add_data')." </div>");
				}
				redirect('qbank/pre_new_question/');
			}			
			
		 $data['nop']=$nop;
		 $data['title']=$this->lang->line('add_new');
		// fetching category list
		$data['category_list']=$this->qbank_model->category_list();
		// fetching level list
		$data['level_list']=$this->qbank_model->level_list();
		 $this->load->view('header',$data);
		$this->load->view('new_question_1',$data);
		$this->load->view('footer',$data);
	}
	
	
	public function new_question_2($nop='4')
	{
		
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
			exit($this->lang->line('permission_denied'));
			}
			if($this->input->post('question')){
				if($this->qbank_model->insert_question_2()){
                $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_added_successfully')." </div>");
				}else{
				 $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_add_data')." </div>");
				}
				redirect('qbank/pre_new_question/');
			}			
			
		 $data['nop']=$nop;
		 $data['title']=$this->lang->line('add_new');
		// fetching category list
		$data['category_list']=$this->qbank_model->category_list();
		// fetching level list
		$data['level_list']=$this->qbank_model->level_list();
		 $this->load->view('header',$data);
		$this->load->view('new_question_2',$data);
		$this->load->view('footer',$data);
	}
	
	
	public function new_question_3($nop='4')	//匹配
	{
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['su']!='1'){
			exit($this->lang->line('permission_denied'));
		}


		if($this->input->post('question')){	//这里是再次进入添加问题的第二个页面发送的数据（二）
			if($this->qbank_model->insert_question_3()){
				$this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_added_successfully')." </div>");
			}else{
				$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_add_data')." </div>");
			}
			redirect('qbank/pre_new_question/');
		}			
		
		//首次进入添加问题的页面 发送过来的数据（一）
		 $data['nop']=$nop;
		 $data['title']=$this->lang->line('add_new');
		// fetching category list
		$data['category_list']=$this->qbank_model->category_list();	//返回所有的科目信息
		// fetching level list
		$data['level_list']=$this->qbank_model->level_list();	//返回所有的level级别

		$this->load->view('header',$data);
		$this->load->view('new_question_3',$data);
		$this->load->view('footer',$data);
	}
	
	
	public function new_question_4($nop='4')
	{
		
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
			exit($this->lang->line('permission_denied'));
			}
			if($this->input->post('question')){
				if($this->qbank_model->insert_question_4()){
                $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_added_successfully')." </div>");
				}else{
				 $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_add_data')." </div>");
				}
				redirect('qbank/pre_new_question/');
			}			
			
		 $data['nop']=$nop;
		 $data['title']=$this->lang->line('add_new');
		// fetching category list
		$data['category_list']=$this->qbank_model->category_list();
		// fetching level list
		$data['level_list']=$this->qbank_model->level_list();
		 $this->load->view('header',$data);
		$this->load->view('new_question_4',$data);
		$this->load->view('footer',$data);
	}
	
	
	public function new_question_5($nop='4')
	{
		
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
			exit($this->lang->line('permission_denied'));
			}
			if($this->input->post('question')){	//这里是再次进入添加问题的第二个页面发送的数据（二）
				if($this->qbank_model->insert_question_5()){
                $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_added_successfully')." </div>");
				}else{
				 $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_add_data')." </div>");
				}
				redirect('qbank/pre_new_question/');
			}			
		
			//首次进入添加问题的页面 发送过来的数据（一）
		 $data['nop']=$nop;
		 $data['title']=$this->lang->line('add_new');
		// fetching category list
		$data['category_list']=$this->qbank_model->category_list();
		// fetching level list
		$data['level_list']=$this->qbank_model->level_list();
		 $this->load->view('header',$data);
		$this->load->view('new_question_5',$data);
		$this->load->view('footer',$data);
	}


	public function new_question_6($nop='10')	//完形填空
	{

		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['su']!='1'){
			exit($this->lang->line('permission_denied'));
		}


		if($this->input->post('question')){	//这里是再次进入添加问题的第二个页面发送的数据（二）
			if($this->qbank_model->insert_question_6()){
				$this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_added_successfully')." </div>");
			}else{
				$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_add_data')." </div>");
			}
			redirect('qbank/pre_new_question/');
		}			
		
		//首次进入添加问题的页面 发送过来的数据（一）
		 $data['nop']=$nop;
		 $data['title']=$this->lang->line('add_new');
		// fetching category list
		$data['category_list']=$this->qbank_model->category_list();	//返回所有的科目信息
		// fetching level list
		$data['level_list']=$this->qbank_model->level_list();	//返回所有的level级别

		$this->load->view('header',$data);
		$this->load->view('new_question_6',$data);
		$this->load->view('footer',$data);
	}
	

	
	
	
	//修改题型：1->单选题  2->多选题  3->匹配  4->简答题  5->长答题 6->完形填空
	
	public function edit_question_1($qid)
	{
		
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
			exit($this->lang->line('permission_denied'));
			}
			if($this->input->post('question')){
				if($this->qbank_model->update_question_1($qid)){
                $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_updated_successfully')." </div>");
				}else{
				 $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_update_data')." </div>");
				}
				redirect('qbank/edit_question_1/'.$qid);
			}			
			
		 
		 $data['title']=$this->lang->line('edit');
		// fetching question
		$data['question']=$this->qbank_model->get_question($qid);
		$data['options']=$this->qbank_model->get_option($qid);
		// fetching category list
		$data['category_list']=$this->qbank_model->category_list();
		// fetching level list
		$data['level_list']=$this->qbank_model->level_list();
		 $this->load->view('header',$data);
		$this->load->view('edit_question_1',$data);
		$this->load->view('footer',$data);
	}
	
	
	public function edit_question_2($qid)
	{
		
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
			exit($this->lang->line('permission_denied'));
			}
			if($this->input->post('question')){
				// // 打印日志 方便查看
				// $this->load->helper('file');
				// write_file('./application/logs/log.txt',var_export($this->input->post('question'),true)."\n",'a+');
				if($this->qbank_model->update_question_2($qid)){
                $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_updated_successfully')." </div>");
				}else{
				 $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_update_data')." </div>");
				}
				redirect('qbank/edit_question_2/'.$qid);
			}			
			
		 
		 $data['title']=$this->lang->line('edit');
		// fetching question
		$data['question']=$this->qbank_model->get_question($qid);
		$data['options']=$this->qbank_model->get_option($qid);
		// fetching category list
		$data['category_list']=$this->qbank_model->category_list();
		// fetching level list
		$data['level_list']=$this->qbank_model->level_list();
		 $this->load->view('header',$data);
		$this->load->view('edit_question_2',$data);
		$this->load->view('footer',$data);
	}
	
	
	public function edit_question_3($qid)
	{
		
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
			exit($this->lang->line('permission_denied'));
			}
			if($this->input->post('question')){
				if($this->qbank_model->update_question_3($qid)){
                $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_updated_successfully')." </div>");
				}else{
				 $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_update_data')." </div>");
				}
				redirect('qbank/edit_question_3/'.$qid);
			}			
			
		  
		 $data['title']=$this->lang->line('edit');
		// fetching question
		$data['question']=$this->qbank_model->get_question($qid);
		$data['options']=$this->qbank_model->get_option($qid);
		// fetching category list
		$data['category_list']=$this->qbank_model->category_list();
		// fetching level list
		$data['level_list']=$this->qbank_model->level_list();
		 $this->load->view('header',$data);
		$this->load->view('edit_question_3',$data);
		$this->load->view('footer',$data);
	}
	
	
	public function edit_question_4($qid)
	{
		
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
			exit($this->lang->line('permission_denied'));
			}
			if($this->input->post('question')){
				if($this->qbank_model->update_question_4($qid)){
                $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_updated_successfully')." </div>");
				}else{
				 $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_update_data')." </div>");
				}
				redirect('qbank/edit_question_4/'.$qid);
			}			
			
		 
		 $data['title']=$this->lang->line('edit');
		// fetching question
		$data['question']=$this->qbank_model->get_question($qid);
		$data['options']=$this->qbank_model->get_option($qid);
		// fetching category list
		$data['category_list']=$this->qbank_model->category_list();
		// fetching level list
		$data['level_list']=$this->qbank_model->level_list();
		 $this->load->view('header',$data);
		$this->load->view('edit_question_4',$data);
		$this->load->view('footer',$data);
	}
	
	
	public function edit_question_5($qid)
	{
		
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
			exit($this->lang->line('permission_denied'));
			}
			if($this->input->post('question')){
				if($this->qbank_model->update_question_5($qid)){
                $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_updated_successfully')." </div>");
				}else{
				 $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_update_data')." </div>");
				}
				redirect('qbank/edit_question_5/'.$qid);
			}			
			
		 
		 $data['title']=$this->lang->line('edit');
		// fetching question
		$data['question']=$this->qbank_model->get_question($qid);
		$data['options']=$this->qbank_model->get_option($qid)[0];
		// $myfile = fopen("D:\XAMPP\htdocs\bug.txt", "w") or die("Unable to open file!".$data['options']['q_option']);
		// foreach($data['options'] as $k =>$v){
		// 	fwrite($myfile, $k.' '.$v);
		// }
		// fclose($myfile);
		// fetching category list
		$data['category_list']=$this->qbank_model->category_list();
		// fetching level list
		$data['level_list']=$this->qbank_model->level_list();
		 $this->load->view('header',$data);
		$this->load->view('edit_question_5',$data);
		$this->load->view('footer',$data);
	}


	public function edit_question_6($qid)	//修改完形填空
	{
		
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['su']!='1'){
		exit($this->lang->line('permission_denied'));
		}
		if($this->input->post('question')){		//在修改页面 提交数据Edit
			if($this->qbank_model->update_question_6($qid)){
			$this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_updated_successfully')." </div>");
			}else{
				$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_update_data')." </div>");
			}
			redirect('qbank/edit_question_6/'.$qid);
		}

		//从question list 点击qid进来
		 $data['title']=$this->lang->line('edit');
		// fetching question
		$data['question']=$this->qbank_model->get_question($qid);	//得到题目Id
		$data['options']=$this->qbank_model->get_option($qid);		//得到所有的选项
		foreach($data['options'] as $key => $val){
			$data['options'][$key]['q_option_match_option'] = explode(",",$val['q_option_match_option']);	//转换为数组
		}
		// fetching category list
		$data['category_list']=$this->qbank_model->category_list();
		// fetching level list
		$data['level_list']=$this->qbank_model->level_list();
		 $this->load->view('header',$data);
		$this->load->view('edit_question_6',$data);
		$this->load->view('footer',$data);
	}
	

	// category functions start
	public function category_list(){
		
		// fetching group list
		$data['category_list']=$this->qbank_model->category_list();
		$data['title']=$this->lang->line('category_list');
		$this->load->view('header',$data);
		$this->load->view('category_list',$data);
		$this->load->view('footer',$data);

		
		
		
	}
	
	
	public function insert_category()
	{
		
		
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
				exit($this->lang->line('permission_denied'));
			}
	
				if($this->qbank_model->insert_category()){
                $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_added_successfully')." </div>");
				}else{
				 $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_add_data')." </div>");
						
				}
				redirect('qbank/category_list/');
	
	}
	
	public function update_category($cid)
	{
		
		
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
				exit($this->lang->line('permission_denied'));
			}
	
				if($this->qbank_model->update_category($cid)){
                echo "<div class='alert alert-success'>".$this->lang->line('data_updated_successfully')." </div>";
				}else{
				 echo "<div class='alert alert-danger'>".$this->lang->line('error_to_update_data')." </div>";
						
				}
				 
	
	}
	
	
	
	
			public function remove_category($cid){

			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
				exit($this->lang->line('permission_denied'));
			} 
			
			$mcid=$this->input->post('mcid');
$this->db->query(" update savsoft_qbank set cid='$mcid' where cid='$cid' ");


			if($this->qbank_model->remove_category($cid)){
                        $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('removed_successfully')." </div>");
					}else{
						    $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_remove')." </div>");
						
					}
					redirect('qbank/category_list');
                     
			
		}
	// category functions end
	
	
	
	
		public function pre_remove_category($cid){
		$data['cid']=$cid;
		// fetching group list
		$data['category_list']=$this->qbank_model->category_list();
		$data['title']=$this->lang->line('remove_category');
		$this->load->view('header',$data);
		$this->load->view('pre_remove_category',$data);
		$this->load->view('footer',$data);

		
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		
	
	// level functions start
	public function level_list(){
		
		// fetching group list
		$data['level_list']=$this->qbank_model->level_list();
		$data['title']=$this->lang->line('level_list');
		$this->load->view('header',$data);
		$this->load->view('level_list',$data);
		$this->load->view('footer',$data);

		
		
		
	}
	
	
		public function insert_level()
	{
		
		
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
				exit($this->lang->line('permission_denied'));
			}
	
				if($this->qbank_model->insert_level()){
                $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_added_successfully')." </div>");
				}else{
				 $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_add_data')." </div>");
						
				}
				redirect('qbank/level_list/');
	
	}
	
			public function update_level($lid)
	{
		
		
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
				exit($this->lang->line('permission_denied'));
			}
	
				if($this->qbank_model->update_level($lid)){
                echo "<div class='alert alert-success'>".$this->lang->line('data_updated_successfully')." </div>";
				}else{
				 echo "<div class='alert alert-danger'>".$this->lang->line('error_to_update_data')." </div>";
						
				}
				 
	
	}
	
	
	
	
			public function remove_level($lid){
                       
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
				exit($this->lang->line('permission_denied'));
			} 
$mlid=$this->input->post('mlid');
$this->db->query(" update savsoft_qbank set lid='$mlid' where lid='$lid' ");
 			
			if($this->qbank_model->remove_level($lid)){
                        $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('removed_successfully')." </div>");
					}else{
						    $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_remove')." </div>");
						
					}
					redirect('qbank/level_list');
                     
			
		}
	// level functions end
	
	
	
		public function pre_remove_level($lid){
		$data['lid']=$lid;
		// fetching group list
		$data['level_list']=$this->qbank_model->level_list();
		$data['title']=$this->lang->line('remove_level');
		$this->load->view('header',$data);
		$this->load->view('pre_remove_level',$data);
		$this->load->view('footer',$data);

		
		
		
	}
	
	
	
	
	
	function import()	//xls批量引入问题
	{	
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['su']!='1'){
			exit($this->lang->line('permission_denied'));
		} 	

		//引入库文件，可以到ReadMe中查看一些关于spreadsheetreader的用法。
		$this->load->helper('xlsimport/php-excel-reader/excel_reader2');
		$this->load->helper('xlsimport/spreadsheetreader.php');

		if(isset($_FILES['xlsfile'])){	//如果上传文件成功

			$config['upload_path']          = './xls/';	//根目录里的xls
			$config['allowed_types']        = 'xls';	//允许的文件类型
			$config['max_size']             = 10000;	//允许上传文件大小KB
			$this->load->library('upload', $config);	//初始化文件上传类 CI框架------->初始化之后，文件上传类的对象就可以这样访问:$this->upload
			// $this->upload->initialize($config);

			if ( ! $this->upload->do_upload('xlsfile'))	//xlsfile为前端上传文件input的name
			{
				$error = array('error' => $this->upload->display_errors());	//display_errors:如果 do_upload()方法返回 FALSE,可以使用该方法来获取错误信息。
				$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$error['error']." </div>");
				redirect('qbank');				
				exit;
			}else{

				//TODO:文件重名怎么办
				$data = array('upload_data' => $this->upload->data());	//data():该方法返回一个数组，包含你上传的文件的所有信息.[file_name、file_type、file_path、full_path、raw_name、orig_name、client_name、file_ext、file_size、is_image...]
				$targets = 'xls/';
				$targets = $targets . basename($data['upload_data']['file_name']);	//basename()返回路径中的文件名部分
				$Filepath = $targets;	//存放文件的路径：xls/文件名
			 
				$allxlsdata = array();
				date_default_timezone_set('UTC');	//函数设置脚本中所有日期/时间函数使用的默认时区。

				$StartMem = memory_get_usage();		//返回当前分配给你的 PHP 脚本的内存量，单位是字节（byte）
				//echo '---------------------------------'.PHP_EOL;
				//echo 'Starting memory: '.$StartMem.PHP_EOL;
				//echo '---------------------------------'.PHP_EOL;

				try
				{
					$Spreadsheet = new SpreadsheetReader($Filepath);
					$BaseMem = memory_get_usage();

					$Sheets = $Spreadsheet -> Sheets();

					//echo '---------------------------------'.PHP_EOL;
					//echo 'Spreadsheets:'.PHP_EOL;
					//print_r($Sheets);
					//echo '---------------------------------'.PHP_EOL;
					//echo '---------------------------------'.PHP_EOL;

					foreach ($Sheets as $Index => $Name)
					{
						//echo '---------------------------------'.PHP_EOL;
						//echo '*** Sheet '.$Name.' ***'.PHP_EOL;
						//echo '---------------------------------'.PHP_EOL;

						$Time = microtime(true);	//返回当前 Unix 时间戳和微秒数

						$Spreadsheet -> ChangeSheet($Index);

						foreach ($Spreadsheet as $Key => $Row)
						{
							//echo $Key.': ';
							if ($Row)
							{
								//print_r($Row);
								$allxlsdata[] = $Row;
							}
							else
							{
								var_dump($Row);		//返回变量的数据类型和值
							}
							$CurrentMem = memory_get_usage();	//返回当前分配给你的 PHP 脚本的内存量，单位是字节（byte）
					
							//echo 'Memory: '.($CurrentMem - $BaseMem).' current, '.$CurrentMem.' base'.PHP_EOL;
							//echo '---------------------------------'.PHP_EOL;
					
							if ($Key && ($Key % 500 == 0))
							{
								//echo '---------------------------------'.PHP_EOL;
								//echo 'Time: '.(microtime(true) - $Time);
								//echo '---------------------------------'.PHP_EOL;
							}
						}
					
					//	echo PHP_EOL.'---------------------------------'.PHP_EOL;
						//echo 'Time: '.(microtime(true) - $Time);
						//echo PHP_EOL;

						//echo '---------------------------------'.PHP_EOL;
						//echo '*** End of sheet '.$Name.' ***'.PHP_EOL;
						//echo '---------------------------------'.PHP_EOL;
					}
					
				}
				catch (Exception $E)
				{
					echo $E -> getMessage();
				}


				$this->qbank_model->import_question($allxlsdata);   
		
			}
			
		}else{
			echo "Error: " . $_FILES["file"]["error"];
		}	
  		$this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_imported_successfully')." </div>");
  		redirect('qbank');
	}

	
	
	
}
