<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

	 function __construct()
	 {
	   parent::__construct();
	   $this->load->database();
	   $this->load->helper('url');
	   $this->load->helper('form');
	   $this->load->model("user_model");
	   $this->lang->load('basic', $this->config->item('language'));
		
			// 打印日志 方便查看
			// $this->load->helper('file');
			// write_file('./application/logs/log.txt','User 登陆态'.var_export($this->session->userdata('logged_in'),true)."\n",'a+');
			// write_file('./application/logs/log.txt',var_export(base_url(),true)."\n",'a+');

		// redirect if not loggedin
		if(!$this->session->userdata('logged_in')){
			// echo json_encode("redirect to login");
			// write_file('./application/logs/log.txt',"redirect if not loggedin\n",'a+');
			// return ;
			redirect('login');
			
		}
		$logged_in=$this->session->userdata('logged_in');

		if($logged_in['base_url'] != base_url()){
			$this->session->unset_userdata('logged_in');		
			redirect('login');
		}
		
	 }

	public function index($limit='0')
	{
		
		$logged_in=$this->session->userdata('logged_in');
		 
			if($logged_in['su']!='1'){
			exit($this->lang->line('permission_denied'));
			}
			
			
		$data['limit']=$limit;
		$data['title']=$this->lang->line('userlist');
		// fetching user list
		$data['result']=$this->user_model->user_list($limit);
		$this->load->view('header',$data);
		$this->load->view('user_list',$data);
		$this->load->view('footer',$data);
	}
	
	public function new_user()	//添加新用户
	{
		
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
			exit($this->lang->line('permission_denied'));
			}
			
			
		$data['title']=$this->lang->line('add_new').' '.$this->lang->line('user');
		// fetching group list
		$data['group_list']=$this->user_model->group_list();	//返回所有的（gid升序）班级
		$this->load->view('header',$data);
		$this->load->view('new_user',$data);
		$this->load->view('footer',$data);
	}
	
	public function insert_user()	//添加新用户
	{
	 	
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['su']!='1'){
			exit($this->lang->line('permission_denied'));
		}
		//设置表单验证规则
		$this->load->library('form_validation');
		$this->form_validation->set_rules('email', 'Email', 'required|is_unique[savsoft_users.email]');	//表单域名，表单域的人性化名字将插入到错误信息中，验证规则
		$this->form_validation->set_rules('password', 'Password', 'required');
		
		//run()它默认返回 FALSE， ``run()`` 方法只在全部成功匹配了你的规则后才会返回 TRUE 
		if ($this->form_validation->run() == FALSE)	//验证规则会被自动加载，当用户触发 run() 方法时被调用
		{
			$this->session->set_flashdata('message', "<div class='alert alert-danger'>".validation_errors()." </div>");
			redirect('user/new_user/');
		}
		else
		{
			if($this->user_model->insert_user()){
				$this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_added_successfully')." </div>");
			}else{
				$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_add_data')." </div>");
			}
			redirect('user/new_user/');
		}       

	}


	//批量添加用户，作用将excel文档里的每行内容传入model中
	public function import()
	{	
		$logged_in=$this->session->userdata('logged_in');
		if($logged_in['su']!='1'){
			exit($this->lang->line('permission_denied'));
		} 	

		//引入库文件，可以到ReadMe中查看一些关于spreadsheetreader的用法。
		$this->load->helper('xlsimport/php-excel-reader/excel_reader2');
		$this->load->helper('xlsimport/spreadsheetreader.php');

		if(isset($_FILES['xlsfile'])){	//如果上传文件成功

			$config['upload_path']          = './xls/';	//根目录里的xls，存放上传文件的目录
			$config['allowed_types']        = 'xls|xlsx';	//允许的文件类型
			$config['max_size']             = 10000;	//允许上传文件大小KB
			$this->load->library('upload', $config);	//初始化文件上传类 CI框架------->初始化之后，文件上传类的对象就可以这样访问:$this->upload
			// $this->upload->initialize($config);

			if ( ! $this->upload->do_upload('xlsfile'))	//xlsfile为前端上传文件input的name
			{
				$error = array('error' => $this->upload->display_errors());	//display_errors:如果 do_upload()方法返回 FALSE,可以使用该方法来获取错误信息。
				$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$error['error']." </div>");
				redirect('user/new_user/');				
				exit;
			}else{

				//TODO:文件重名怎么办
				$data = array('upload_data' => $this->upload->data());	//data():该方法返回一个数组，包含你上传的文件的所有信息.
				$targets = 'xls/';
				$targets = $targets . basename($data['upload_data']['file_name']);	//basename()返回路径中的文件名部分
				$Filepath = $targets;	//存放文件的路径：xls/文件名
			 
				$allxlsdata = array();
				date_default_timezone_set('UTC');	//函数设置脚本中所有日期/时间函数使用的默认时区。

				try
				{
					$Spreadsheet = new SpreadsheetReader($Filepath);

					$Sheets = $Spreadsheet -> Sheets();

					foreach ($Sheets as $Index => $Name)
					{

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
					
						}
					
					}
					
				}
				catch (Exception $E)
				{
					echo $E -> getMessage();
				}

				$this->user_model->import_user($allxlsdata);   
		
			}
			
		}else{
			echo "Error: " . $_FILES["file"]["error"];
		}	
  		$this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_imported_successfully')." </div>");
  		redirect('user/new_user/');
	}



		public function remove_user($uid){

			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
				exit($this->lang->line('permission_denied'));
			}
			if($uid=='1'){
					exit($this->lang->line('permission_denied'));
			}
			
			if($this->user_model->remove_user($uid)){
                        $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('removed_successfully')." </div>");
					}else{
						    $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_remove')." </div>");
						
					}
					redirect('user');
                     
			
		}

	public function edit_user($uid)
	{
		
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
			 $uid=$logged_in['uid'];
			}
			
			$data['uid']=$uid;
		 $data['title']=$this->lang->line('edit').' '.$this->lang->line('user');
		// fetching user
		$data['result']=$this->user_model->get_user($uid);
		$this->load->model("payment_model");
		$data['payment_history']=$this->payment_model->get_payment_history($uid);
		// fetching group list
		$data['group_list']=$this->user_model->group_list();
		 $this->load->view('header',$data);
			if($logged_in['su']=='1'){
		$this->load->view('edit_user',$data);
			}else{
		$this->load->view('myaccount',$data);
				
			}
		$this->load->view('footer',$data);
	}

		public function update_user($uid)
	{
		
		
			$logged_in=$this->session->userdata('logged_in');
						 
			if($logged_in['su']!='1'){
			 $uid=$logged_in['uid'];
			}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('email', 'Email', 'required');
           if ($this->form_validation->run() == FALSE)
                {
                     $this->session->set_flashdata('message', "<div class='alert alert-danger'>".validation_errors()." </div>");
					redirect('user/edit_user/'.$uid);
                }
                else
                {
					if($this->user_model->update_user($uid)){
                        $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_updated_successfully')." </div>");
					}else{
						    $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_update_data')." </div>");
						
					}
					redirect('user/edit_user/'.$uid);
                }       

	}

	public function wx_update_user($uid)
	{

		// 打印日志 方便查看
		// $this->load->helper('file');
		// write_file('./application/logs/log.txt',"uid---------------------\n".var_export($uid,true)."\n",'a+');
		
		
		$logged_in=$this->session->userdata('logged_in');
						
		if($logged_in['su']!='1'){
			$uid=$logged_in['uid'];
		}

		if($this->user_model->wx_update_user($uid)){

			echo json_encode( array(true,$this->lang->line('data_updated_successfully')) );
		}else{
			echo json_encode( array(false,$this->lang->line('error_to_update_data')) );
			
		}

	}
	
	
	public function group_list(){
		
		// fetching group list
		$data['group_list']=$this->user_model->group_list();
		$data['title']=$this->lang->line('group_list');
		$this->load->view('header',$data);
		$this->load->view('group_list',$data);
		$this->load->view('footer',$data);

		
		
		
	}
	
	public function add_new_group(){
	                $logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
			exit($this->lang->line('permission_denied'));
			}
			
			
			
		if($this->input->post('group_name')){
		if($this->user_model->insert_group()){
                        $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_added_successfully')." </div>");
					}else{
						    $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_update_data')." </div>");
						
					}
					redirect('user/group_list');
		}
		// fetching group list
		$data['title']=$this->lang->line('add_group');
		$this->load->view('header',$data);
		$this->load->view('add_group',$data);
		$this->load->view('footer',$data);

		
		
		
	}



	public function edit_group($gid){
	                $logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
			exit($this->lang->line('permission_denied'));
			}

		if($this->input->post('group_name')){
		if($this->user_model->update_group($gid)){
                        $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_updated_successfully')." </div>");
					}else{
						    $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_update_data')." </div>");
						
					}
					redirect('user/group_list');
		}
		// fetching group list
		$data['group']=$this->user_model->get_group($gid);
		$data['gid']=$gid;
		$data['title']=$this->lang->line('edit_group');
		$this->load->view('header',$data);
		$this->load->view('edit_group',$data);
		$this->load->view('footer',$data);

		
		
		
	}

        public function upgid($gid){
        $logged_in=$this->session->userdata('logged_in');
			$uid=$logged_in['uid'];
			$group=$this->user_model->get_group($gid);
		if($group['price'] != '0'){
		redirect('payment_gateway_2/subscribe/'.$gid.'/'.$logged_in['uid']);
		 }else{
		$subscription_expired=time()+(365*20*24*60*60);
		}
			$userdata=array(
			'gid'=>$gid,
			'subscription_expired'=>$subscription_expired
			);
			
			$this->db->where('uid',$uid);
			$this->db->update('savsoft_users',$userdata);
			 $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('group_updated_successfully')." </div>");
			redirect('user/edit_user/'.$logged_in['uid']);
        
        
        }
		public function switch_group()
	{
		
		$logged_in=$this->session->userdata('logged_in');
		if(!$this->config->item('allow_switch_group')){
		redirect('user/edit_user/'.$logged_in['uid']);
		}
			$data['title']=$this->lang->line('select_package');
		// fetching group list
		$data['group_list']=$this->user_model->group_list();
		$this->load->view('header',$data);
		$this->load->view('change_group',$data);
		$this->load->view('footer',$data);
	}
	
	public function pre_remove_group($gid){
		$data['gid']=$gid;
		// fetching group list
		$data['group_list']=$this->user_model->group_list();
		$data['title']=$this->lang->line('remove_group');
		$this->load->view('header',$data);
		$this->load->view('pre_remove_group',$data);
		$this->load->view('footer',$data);

		
		
		
	}
	
		public function insert_group()
	{
		
		
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
				exit($this->lang->line('permission_denied'));
			}
	
				if($this->user_model->insert_group()){
                $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('data_added_successfully')." </div>");
				}else{
				 $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_add_data')." </div>");
						
				}
				redirect('user/group_list/');
	
	}
	
			public function update_group($gid)
	{
		
		
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
				exit($this->lang->line('permission_denied'));
			}
	
				if($this->user_model->update_group($gid)){
                echo "<div class='alert alert-success'>".$this->lang->line('data_updated_successfully')." </div>";
				}else{
				 echo "<div class='alert alert-danger'>".$this->lang->line('error_to_update_data')." </div>";
						
				}
				 
	
	}
	
	
	function get_expiry($gid){
		
		echo $this->user_model->get_expiry($gid);
		
	}
	
	
	
	
			public function remove_group($gid){
                        $mgid=$this->input->post('mgid');
                        $this->db->query(" update savsoft_users set gid='$mgid' where gid='$gid' ");
                        
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']!='1'){
				exit($this->lang->line('permission_denied'));
			} 
			
			if($this->user_model->remove_group($gid)){
                        $this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('removed_successfully')." </div>");
					}else{
						    $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_remove')." </div>");
						
					}
					redirect('user/group_list');
                     
			
		}

	function logout(){
		
		$this->session->unset_userdata('logged_in');		
			if($this->session->userdata('logged_in_raw')){
				$this->session->unset_userdata('logged_in_raw');	
			}		
 redirect('login');
		
	}
}
