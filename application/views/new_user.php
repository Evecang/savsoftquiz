<div class="container">
	<h3><?php echo $title;?></h3>
   
    <div class="row">
    
	<div class="col-md-8">
		<?php 
			if($this->session->flashdata('message')){
				echo $this->session->flashdata('message');	
			}
		?>
		<div class="login-panel panel panel-default">
			<div class="panel-body"> 
			<form method="post" action="<?php echo site_url('user/insert_user/');?>">
				<div class="form-group">	 
						<label for="inputEmail" class="sr-only"><?php echo $this->lang->line('email_address');?></label> 
						<input type="email" id="inputEmail" name="email" class="form-control" placeholder="<?php echo $this->lang->line('email_address');?>" required autofocus>
				</div>
				<div class="form-group">	  
						<label for="inputPassword" class="sr-only"><?php echo $this->lang->line('password');?></label>
						<input type="password" id="inputPassword" name="password"  class="form-control" placeholder="<?php echo $this->lang->line('password');?>" required >
				</div>
				<div class="form-group">	 
						<label for="inputEmail" class="sr-only"><?php echo $this->lang->line('first_name');?></label> 
						<input type="text"  name="first_name"  class="form-control" placeholder="<?php echo $this->lang->line('first_name');?>"   autofocus>
				</div>
					<div class="form-group">	 
						<label for="inputEmail" class="sr-only"><?php echo $this->lang->line('last_name');?></label> 
						<input type="text"   name="last_name"  class="form-control" placeholder="<?php echo $this->lang->line('last_name');?>"   autofocus>
				</div>
					<div class="form-group">	 
						<label for="inputEmail" class="sr-only"><?php echo $this->lang->line('contact_no');?></label> 
						<input type="text" name="contact_no"  class="form-control" placeholder="<?php echo $this->lang->line('contact_no');?>"   autofocus>
				</div>
					<div class="form-group">	 
						<label   ><?php echo $this->lang->line('select_group');?></label> 
						<select class="form-control" name="gid" id="gid" onChange="getexpiry();">
						<?php 
						foreach($group_list as $key => $val){
							?>
							
							<option value="<?php echo $val['gid'];?>"><?php echo $val['group_name'];?> (<?php echo $this->lang->line('price_');?>: <?php echo $val['price'];?>)</option>
							<?php 
						}
						?>
						</select>
				</div>
				<div class="form-group">	 
						<label for="inputEmail"  ><?php echo $this->lang->line('subscription_expired');?></label> 
						<input type="text" name="subscription_expired"  id="subscription_expired" class="form-control" placeholder="<?php echo $this->lang->line('subscription_expired');?>"    autofocus>
				</div>

				<div class="form-group">	 
						<label   ><?php echo $this->lang->line('account_type');?></label> 
						<select class="form-control" name="su">
							<option value="0"><?php echo $this->lang->line('user');?></option>
							<option value="1"><?php echo $this->lang->line('administrator');?></option>
						</select>
				</div>

	
				<button class="btn btn-default" type="submit"><?php echo $this->lang->line('submit');?></button>
			
			</form>
			</div>
		</div>

		<!-- 批量地添加用户	 -->
		<br>
		<div class="login-panel panel panel-default">
			<div class="panel-heading">
				<h4><?php echo $this->lang->line('import_user');?></h4> 
			</div>

			<div class="panel-body"> 
				<!-- 通过xls文档批量引入用户 -->
				<?php echo form_open('user/import',array('enctype'=>'multipart/form-data')); ?>
				<!-- <form method="post" action="user/import" enctype='multipart/form-data'> -->
				
				<div class="form-group">
					<label><?php echo $this->lang->line('select_group');?></label>

					<select name="i_gid" id="gid1" class="form-control" required>
						<option value="" disabled selected><?php echo $this->lang->line('select_group');?></option>
						<?php 
						foreach($group_list as $key => $val){
						?>
							<option value="<?php echo $val['gid'];?>"><?php echo $val['group_name'];?></option>
						<?php 
						}
						?>
					</select>
				</div>

				<div class="form-group">
					<label><?php echo $this->lang->line('account_type');?></label> 

					<select name="i_su" class="form-control" required >
						<option value="" disabled selected><?php echo $this->lang->line('account_type');?></option>
						<option value="0"><?php echo $this->lang->line('user');?></option>
						<option value="1"><?php echo $this->lang->line('administrator');?></option>
					</select>
				</div>
				
				<div class="form-group">	 
					<label><?php echo $this->lang->line('subscription_expired');?></label> 
					<input type="text" name="i_subscription_expired" class="form-control" placeholder="yyyy-mm-dd or yyyy/mm/dd" autofocus required>
				</div>

				<?php echo $this->lang->line('upload_excel');?>

				<input type="hidden" name="size" value="3500000">
				<input type="file" name="xlsfile" style="width:150px;float:left;margin-left:10px;">
				<div style="clear:both;"></div>
				<input type="submit" value="Import" style="margin-top:5px;" class="btn btn-default">
				
				<a href="<?php echo base_url();?>sample/user.xlsx" target="new">Click here</a> <?php echo $this->lang->line('upload_excel_info');?> 

				
				</form><!-- 对应form_open的form -->

			</div>

		</div>

	</div><!--end of col-md-8 -->

	</div>

</div>
<script>
getexpiry();
</script>