<html lang="en">
  <head>
  <title><?php echo $title;?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  
	<title> </title>
	<!-- bootstrap css -->
	<link href="<?php echo base_url('bootstrap/css/bootstrap.min.css');?>" rel="stylesheet">
	
	<!-- custom css -->
	<link href="<?php echo base_url('css/style.css');?>" rel="stylesheet">
	
	<script>
	
	var base_url="<?php echo base_url();?>";
  console.log("base_url 为： ",base_url);

	</script>
	
	<!-- jquery -->
	<script src="<?php echo base_url('js/jquery.js');?>"></script>
	
	<!-- custom javascript -->
	  	<script src="<?php echo base_url('js/basic.js');?>"></script>
		
	<!-- bootstrap js -->
    <script src="<?php echo base_url('bootstrap/js/bootstrap.min.js');?>"></script>
	
	<!-- fontawesome css -->
	<link href="<?php echo base_url('font-awesome/css/font-awesome.css');?>" rel="stylesheet">
	
	
	
 </head>
  <body  class='login'  >

  <?php 
      // 如果用户是登陆态,才展示导航栏
      //而且 URI 不等于 ‘quiz/attempt’ (这步还不理解)，则将 session 中的数据缓存到 $logged_in 变量
			if($this->session->userdata('logged_in')){
        // $this->uri->segment(1)获取url上从域名开始的第一个参数（可能是控制器的名字）  （2）获取第二个参数，是控制器（类）的方法
				if(($this->uri->segment(1).'/'.$this->uri->segment(2))!='quiz/attempt'){
				$logged_in=$this->session->userdata('logged_in');
	?>

  <script>
    var session_info = "<?php echo $this->session; ?>";
    console.log("Session信息为：",session_info);
  </script>

	    <nav class="navbar navbar-default">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <!--这里我把href的地址更改成dashboard，但未成效果，所以该文件不是实际上的导航栏-->
            <a class="navbar-brand" href="<?php echo site_url('dashboard');?>"><?php echo $this->lang->line('savsoft_quiz');?></a>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <?php  
              //如果这里的用户是 su=1 ->管理员  则导航栏首先显示Dashboard、Users、Question Bank、（后续的显示应该跟学生一致，所以放到下边代码中）
                if($logged_in['su']==1){
              ?> 
			  
			  <li <?php if($this->uri->segment(1)=='dashboard'){ echo "class='active'"; } ?> ><a href="<?php echo site_url('dashboard');?>"><?php echo $this->lang->line('dashboard');?></a></li>
            
			 
			  <li class="dropdown" <?php if($this->uri->segment(1)=='user'){ echo "class='active'"; } ?> >
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $this->lang->line('users');?> <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="<?php echo site_url('user/new_user');?>"><?php echo $this->lang->line('add_new');?></a></li>
                  <li><a href="<?php echo site_url('user');?>"><?php echo $this->lang->line('users');?> <?php echo $this->lang->line('list');?></a></li>
                  
                </ul>
        </li>
			 
			 
			 
			  <li class="dropdown" <?php if($this->uri->segment(1)=='qbank'){ echo "class='active'"; } ?> >
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $this->lang->line('qbank');?> <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="<?php echo site_url('qbank/pre_new_question');?>"><?php echo $this->lang->line('add_new');?></a></li>
                  <li><a href="<?php echo site_url('qbank');?>"><?php echo $this->lang->line('question');?> <?php echo $this->lang->line('list');?></a></li>
                  
                </ul>
        </li>
			 
			 
			 
		    <?php 
          }else{
            //如果这里的用户是 su！=1 ->学生  则导航栏首先显示My Account（后续的显示应该跟学生一致，所以放到下边代码中）
        ?>
			 <li><a href="<?php echo site_url('user/edit_user/'.$logged_in['uid']);?>"><?php echo $this->lang->line('myaccount');?></a></li>
			<?php 
        }
        //之后导航栏显示的的内容 管理员与学生的一致：Quiz、Result、Live Classroom、Logout
			?>
     		  <li class="dropdown" <?php if($this->uri->segment(1)=='qbank'){ echo "class='active'"; } ?> >
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $this->lang->line('quiz');?> <span class="caret"></span></a>
                <ul class="dropdown-menu">
                 <?php  
				if($logged_in['su']==1){
          //管理员才能够 添加试卷
			?>     <li><a href="<?php echo site_url('quiz/add_new');?>"><?php echo $this->lang->line('add_new');?></a></li>
              <?php 
				}
?>				 <li><a href="<?php echo site_url('quiz');?>"><?php echo $this->lang->line('quiz');?> <?php echo $this->lang->line('list');?></a></li>
               
                </ul>
              </li>
	

	           <li><a href="<?php echo site_url('result');?>"><?php echo $this->lang->line('result');?></a></li>
			 
			 <li><a href="<?php echo site_url('liveclass');?>"><?php echo $this->lang->line('live_classroom');?></a></li>
			 
			  <?php  
				if($logged_in['su']==1){
          //管理员的导航栏 在Live Classroom后再添加两栏————Payment History、Setting
			?>
			 <li><a href="<?php echo site_url('payment_gateway');?>"><?php echo $this->lang->line('payment_history');?></a></li>
			 
			  <li class="dropdown" <?php if($this->uri->segment(1)=='user_group'){ echo "class='active'"; } ?> >
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $this->lang->line('setting');?> <span class="caret"></span></a>
            <ul class="dropdown-menu">
                 
              <li><a href="<?php echo site_url('user/group_list');?>"><?php echo $this->lang->line('group_list');?></a></li>
              <li><a href="<?php echo site_url('qbank/category_list');?>"><?php echo $this->lang->line('category_list');?></a></li>
              <li><a href="<?php echo site_url('qbank/level_list');?>"><?php echo $this->lang->line('level_list');?></a></li>
                  
					    <li><a href="<?php echo site_url('dashboard/config');?>"><?php echo $this->lang->line('config');?></a></li>
					 
					    <li><a href="<?php echo site_url('dashboard/css');?>"><?php echo $this->lang->line('custom_css');?></a></li>
						  
                  
            </ul>
            
         </li>
			
			<?php 
				}
				?>
             <li><a href="<?php echo site_url('user/logout');?>"><?php echo $this->lang->line('logout');?></a></li>
              <!--
			  <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Action</a></li>
                  <li><a href="#">Another action</a></li>
                  <li><a href="#">Something else here</a></li>
                 
                </ul>
              </li>
			  -->
			  
            </ul>
             
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>

	<?php 
			}
			}
	?>
	
