 
<div class="row" style="margin-top:20px;">
<div class="container" >   
 
 
 



<div class="col-md-8">
 
</div>
<!-- 这里才是登录的框框 -->
<div class="col-md-4">

	<div class="login-panel panel panel-default">
		<div class="panel-body"> 
		<center>
		<a href="<?php echo base_url();?>"><img src="<?php echo base_url('images/logo.png');?>"></a><br>
<?php echo $this->lang->line('login_tagline');?>
		</center>

		<form class="form-signin" method="post" action="<?php echo site_url('login/verifylogin');?>">
			<h4 class="form-signin-heading"><?php echo $this->lang->line('login');?></h4>
		<?php 
		// TODO
		// 如果 session->flashdatamessage的 message 字段存在
		// 则在 session->flashdata的 message 字段存储 的值中查找 ‘{resend_url}’并将其替换为 当前文件夹+login/resend
		// 如果不是账号激活而且账号密码都正确（如果存在flashdata，则说明不正确），则重定向到login/resend？
		if($this->session->flashdata('message')){
			
			?>
			<div class="alert alert-danger">
				<?php echo str_replace('{resend_url}',site_url('login/resend'),$this->session->flashdata('message'));?>
			</div>
		<?php	
		}
		?>	
		
		<label for="inputEmail" class="sr-only"><?php echo $this->lang->line('email_address');?></label> 
		<fieldset class="page-signin-form-group form-group form-group-lg">
                  <div class="page-signin-icon text-muted"><i class="fa fa-user"></i></div>
                  <input class="page-signin-form-control form-control" name="email"  placeholder="<?php echo $this->lang->line('email_address');?>" type="text" required autofocus>
                </fieldset>
                
                <label for="inputPassword" class="sr-only"><?php echo $this->lang->line('password');?></label>
  		<fieldset class="page-signin-form-group form-group form-group-lg">
                  <div class="page-signin-icon text-muted"><i class="fa fa-star"></i></div>
                  <input class="page-signin-form-control form-control" name="password"  id="inputPassword" placeholder="<?php echo $this->lang->line('password');?>" type="password" required  >
                </fieldset>
                			  
			 
			<div class="form-group">	  
					 
					<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo $this->lang->line('login');?></button>
			</div>
<?php 
// 在 config.php 中可以配置这个参数，默认为true，显示注册按钮，允许用户自主注册
if($this->config->item('user_registration')){
	?>
	<a href="<?php echo site_url('login/pre_registration');?>"><?php echo $this->lang->line('register_new_account');?></a>
	&nbsp;&nbsp;&nbsp;&nbsp;
<?php
}
?>
			<a href="<?php echo site_url('login/forgot');?>"><?php echo $this->lang->line('forgot_password');?></a>

		</form>
			
<?php 
// 直接查看所有试卷的按钮
// 在 config.php 中可配置，默认为true
if($this->config->item('open_quiz')){
	?>	
		<p>
			<a href="<?php echo site_url('quiz/open_quiz/0');?>"  ><?php echo $this->lang->line('open_quizzes');?></a>
		</p>
<?php 
	}
?>
			
		</div>
	</div>

</div>
 

</div>

</div>
