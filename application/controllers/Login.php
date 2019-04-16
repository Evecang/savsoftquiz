<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	 function __construct()
	 {
	   parent::__construct();
	   $this->load->database();
	   $this->load->model("user_model");
	    $this->load->model("quiz_model");
	   $this->lang->load('basic', $this->config->item('language'));
	   // 如果数据库为空，则重定向到 install 界面
		if($this->db->database ==''){
		redirect('install');	
		}
		 
		 
		 
		
		
	 }

	public function index()
	{
		//加载 url 辅助类函数
		$this->load->helper('url');
		//如果用户已经登录
		//则根据 $logged_in['su']=='1' （判断是管理人员）决定重定向到 dashboard 首页还是 quiz 测试页面
		if($this->session->userdata('logged_in')){
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']=='1'){
				//管理员
				redirect('dashboard');
			}else{
				//学生
				redirect('quiz');	
			}
			
		}
		
		
		
		$data['title']=$this->lang->line('login');
		$data['recent_quiz']=$this->quiz_model->recent_quiz('5');
		
		$this->load->view('header_login',$data);
		$this->load->view('login',$data);
		$this->load->view('footer',$data);
	}



	//设置重新发送邮箱的页面？
	public function resend()
	{
		
		
		 $this->load->helper('url');
		if($this->input->post('email')){
		$status=$this->user_model->resend($this->input->post('email'));
		$this->session->set_flashdata('message', $status);
		redirect('login/resend');
		}
		
		
		$data['title']=$this->lang->line('resend_link');
		 
		$this->load->view('header',$data);
		$this->load->view('resend',$data);
		$this->load->view('footer',$data);
	}
	
	
 
	
	
	
	
	//真正注册前一个页面的路由，即选择用户组别的页面，不是后台注册逻辑路由
		public function pre_registration()
	{
		$this->load->helper('url');
		//TODO：
		//这里配置要去选择组别/班级
		$data['title']=$this->lang->line('select_package');
		// fetching group list
		$data['group_list']=$this->user_model->group_list();
		$this->load->view('header',$data);
		$this->load->view('pre_register',$data);
		$this->load->view('footer',$data);
	}

		//注册页面的路由，不是注册逻辑后台路由
		public function registration($gid='0')
	{
	$this->load->helper('url');
		$data['gid']=$gid;
		$data['title']=$this->lang->line('register_new_account');
		// fetching group list
		$data['group_list']=$this->user_model->group_list();
		$this->load->view('header',$data);
		$this->load->view('register',$data);
		$this->load->view('footer',$data);
	}

	//登陆验证路由接口
	public function verifylogin($p1='',$p2=''){
		
		if($p1 == ''){
		$username=$this->input->post('email');
		$password=$this->input->post('password');
		}else{
		$username=urldecode($p1);
		$password=urldecode($p2);
		}
		 $status=$this->user_model->login($username,$password);
		 //除了验证正确设置 userdata，其他所有情况都设置 flashSession 
		if($status['status']=='1'){
			$this->load->helper('url');
			// row exist fetch userdata
			$user=$status['user'];
			
			
			// validate if user assigned to paid group
			// 如果是付费组的用户，检测是否过期，如果过期则重定向到充值页面，不过期则不做处理
			if($user['price'] > '0'){
				
				// user assigned to paid group now validate expiry date(到期时间).
				if($user['subscription_expired'] <= time()){
					// eubscription expired, redirect to payment page
					
					redirect('payment_gateway/subscription_expired/'.$user['uid']);
					
				}
				
			}
			$user['base_url']=base_url();
			// creating login cookie
			$this->session->set_userdata('logged_in', $user);
			// redirect to dashboard
			if($user['su']=='1'){
			 redirect('dashboard');
				 
			}else{
				$burl=$this->config->item('base_url').'index.php/quiz';
			 header("location:$burl");
			}
		}else if($status['status']=='0'){
			 
			// invalid login
			// try to auth wp
			if($this->config->item('wp-login')){
			 
		                if($this->authentication($username, $password)){
		               
		                 $this->verifylogin($username, $password);
		                }else{
		                 $this->load->helper('url');
		                 $this->session->set_flashdata('message', $status['message']);
			 $burl=$this->config->item('base_url');
			 header("location:$burl");
		                }
		        }else{
		        
		        $this->load->helper('url');
		        $this->session->set_flashdata('message', $status['message']);
			redirect('login');
		        }
		        
			
		}else if($status['status']=='2'){
                        $this->load->helper('url');

			 
			// email not verified
			$this->session->set_flashdata('message', $status['message']);
			redirect('login');
		}else if($status['status']=='3'){
                        $this->load->helper('url');

			 
			// email not verified
			$this->session->set_flashdata('message', $status['message']);
			redirect('login');
		}
		
		
		
	}
	

		//wx登录接口1
		public function wx_verifylogin(){
		
			$username=$this->input->post('email');	
			$password=$this->input->post('password');
			$code=$this->input->post('code');

			$status=$this->user_model->wx_login($username,$password);//成功情况：[status:1,user:user]
			
			 
			 //除了验证正确设置 userdata，其他所有情况都设置 flashSession 
			if($status['status']=='1'){
				$this->load->helper('url');
				$user=$status['user'];
				// TODO:微信端-如果是付费组的用户，检测是否过期，如果过期则重定向到充值页面，不过期则不做处理

				$user['base_url']=base_url();
				// creating login cookie
				$this->session->set_userdata('logged_in', $user);

				//将appid,appsecret,code发送至微信服务器,得到openid,session_key
				$appid='wxb650f284e0718ec2';
				$appsecret='317599492cbea8916b79918550211d3a';
				$openid='';
				$session_key='';
				$wx_url='https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$appsecret.'&js_code='.$code.'&grant_type=authorization_code';
				$info = file_get_contents($wx_url);//发送HTTPs请求并获取返回的数据，推荐使用curl
				$json = json_decode($info);//对json数据解码
				$arr = get_object_vars($json);
				$openid = $arr['openid'];
				$session_key = $arr['session_key'];

				$this->user_model->wx_binding($username,$password,$openid);

				echo json_encode($user);
			}else{
				// ['status','message'] => ['0','invalid login'],['2','email_not_verified'],['3','account_inactive']
				echo json_encode($status);
			}
			
		}

		//wx登录接口2(不用输入账号密码)
		public function wx_autologin(){

			$code=$this->input->post('code');

			//将appid,appsecret,code发送至微信服务器,得到openid,session_key
			$appid='wxb650f284e0718ec2';
			$appsecret='317599492cbea8916b79918550211d3a';
			$openid='';
			$session_key='';
			$wx_url='https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$appsecret.'&js_code='.$code.'&grant_type=authorization_code';
			$info = file_get_contents($wx_url);//发送HTTPs请求并获取返回的数据，推荐使用curl
			$json = json_decode($info);//对json数据解码

			$arr = get_object_vars($json);
			$openid = $arr['openid'];
			$session_key = $arr['session_key'];
			// 打印日志 方便查看
			// $this->load->helper('file');
			// write_file('./application/logs/log.txt',"微信服务器返回的openid与session_key\n".var_export($arr,true)."\n",'a+');

			$status=$this->user_model->wx_autologin($openid);//成功情况：[status:1,user:user] 失败：[status:0,message]
			
			if($status['status']=='1'){
				$this->load->helper('url');
				$user=$status['user'];
				$user['base_url']=base_url();
				// creating login cookie
				$this->session->set_userdata('logged_in', $user);

				$status['user'] = $user;

				echo json_encode($status);
			}else{
				echo json_encode($status);
			}
			
		}
	
	
	
		
	function verify($vcode){
		$this->load->helper('url');	 
		 if($this->user_model->verify_code($vcode)){
			 $data['title']=$this->lang->line('email_verified');
		   $this->load->view('header',$data);
			$this->load->view('verify_code',$data);
		  $this->load->view('footer',$data);

			}else{
			 $data['title']=$this->lang->line('invalid_link');
		   $this->load->view('header',$data);
			$this->load->view('verify_code',$data);
		  $this->load->view('footer',$data);

			}
	}
	
	
	
	
	function forgot(){
	$this->load->helper('url');
			if($this->input->post('email')){
			$user_email=$this->input->post('email');
			 if($this->user_model->reset_password($user_email)){
				$this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('password_updated')." </div>");
						
			}else{
				$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('email_doesnot_exist')." </div>");
						
			}
			redirect('login/forgot');
			}
			
  
			$data['title']=$this->lang->line('forgot_password');
		   $this->load->view('header',$data);
			$this->load->view('forgot_password',$data);
		  $this->load->view('footer',$data);

	
	}
	
	//用户注册的真正逻辑后台接口
		public function insert_user()
	{
		
		
		 $this->load->helper('url');
		$this->load->library('form_validation');
		//email 账号必须且唯一 ， password 必须，否则定向回注册界面
		$this->form_validation->set_rules('email', 'Email', 'required|is_unique[savsoft_users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required');
          if ($this->form_validation->run() == FALSE)
                {
                     $this->session->set_flashdata('message', "<div class='alert alert-danger'>".validation_errors()." </div>");
					redirect('login/registration/');
                }
                else
                {
					if($this->user_model->insert_user_2()){
                        if($this->config->item('verify_email')){
						$this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('account_registered_email_sent')." </div>");
						}else{
							$this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('account_registered')." </div>");
						}
						}else{
						    $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_add_data')." </div>");
						
					}
					redirect('login/registration/');
                }       

	}
	
	
	
	
	function verify_result($rid){
		$this->load->helper('url');
		$this->load->model("result_model");
		
			$data['result']=$this->result_model->get_result($rid);
	if($data['result']['gen_certificate']=='0'){
		exit();
	}
	
	
	$certificate_text=$data['result']['certificate_text'];
	$certificate_text=str_replace('{email}',$data['result']['email'],$certificate_text);
	$certificate_text=str_replace('{first_name}',$data['result']['first_name'],$certificate_text);
	$certificate_text=str_replace('{last_name}',$data['result']['last_name'],$certificate_text);
	$certificate_text=str_replace('{percentage_obtained}',$data['result']['percentage_obtained'],$certificate_text);
	$certificate_text=str_replace('{score_obtained}',$data['result']['score_obtained'],$certificate_text);
	$certificate_text=str_replace('{quiz_name}',$data['result']['quiz_name'],$certificate_text);
	$certificate_text=str_replace('{status}',$data['result']['result_status'],$certificate_text);
	$certificate_text=str_replace('{result_id}',$data['result']['rid'],$certificate_text);
	$certificate_text=str_replace('{generated_date}',date('Y-m-d',$data['result']['end_time']),$certificate_text);
	
	$data['certificate_text']=$certificate_text;
	  $this->load->view('view_certificate_2',$data);
	 

	}
	
	
	
	function authentication ($user, $pass){
                  global $wp, $wp_rewrite, $wp_the_query, $wp_query;

                  if(empty($user) || empty($pass)){
                    return false;
                  }else{
                    require_once($this->config->item('wp-path'));
                    $status = false;
                    $auth = wp_authenticate($user, $pass );
                    if( is_wp_error($auth) ) {      
                      $status = false;
                    } else {
                    
                    // if username already exist in savsoft_users
                    $this->db->where('wp_user',$user);
                    $query=$this->db->get('savsoft_users');
                    if($query->num_rows()==0){
                    $userdata=array(
                    'password'=>md5($pass),
                    'wp_user'=>$user,
                    'su'=>0,
                    'gid'=>$this->config->item('default_group')                  
                    
                    );
                    $this->db->insert('savsoft_users',$userdata);
                    
                    }
                    
                    
                      $status = true;
                    }
                    return $status;
                  } 
        }
        
        
        public function commercial(){
        $this->load->helper('url');
		
       $data['title']=$this->lang->line('files_missing');
		   $this->load->view('header',$data);
			$this->load->view('files_missing',$data);
		  $this->load->view('footer',$data);
        }



		 // super admin code login controller 
	public function superadminlogin(){
	$this->load->helper('url');
			$logged_in=$this->session->userdata('logged_in_super_admin');
			if($logged_in['su']!='3'){
				exit('permission denied');
				
			}
			
		$user=$this->user_model->admin_login();
		$user['base_url']=base_url();
		 $user['super']=3;
		$this->session->set_userdata('logged_in', $user);
		redirect('dashboard');
	}
	
	
}
