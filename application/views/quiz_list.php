 <div class="container">
<?php 
$logged_in=$this->session->userdata('logged_in');
			 
			
			?>
   
 <h3><?php echo $title;?></h3>
    <?php 
	if($logged_in['su']=='1'){  //如果是管理员（多显示以下部分）：
		?>
		<div class="row">
 
  <div class="col-lg-6">
    <!-- $list_view='grid',即访问quiz控制器的index(0,'grid') -->
    <form method="post" action="<?php echo site_url('quiz/index/0/'.$list_view);?>">
      <div class="input-group">
        <input type="text" class="form-control" name="search" placeholder="<?php echo $this->lang->line('search');?>...">
          <span class="input-group-btn">
            <button class="btn btn-default" type="submit"><?php echo $this->lang->line('search');?></button>
          </span>
      </div><!-- /input-group -->

   </form>
  </div><!-- /.col-lg-6搜索框 -->

  <div class="col-lg-6">
    <p style="float:right;">
  <?php 
  //这里选择展示考试列表的形式：格子/表格
  if($list_view=='grid'){
	  ?>
	  <a href="<?php echo site_url('quiz/index/'.$limit.'/table');?>"><?php echo $this->lang->line('table_view');?></a>
	  <?php 
  }else{
	  ?>
	   <a href="<?php echo site_url('quiz/index/'.$limit.'/grid');?>"><?php echo $this->lang->line('grid_view');?></a>
	  
	  <?php 
  }
  ?>
  </p>
  
  </div>
</div><!-- /.row -->

<?php 
	}
?>


  <div class="row">
 
<div class="col-md-12">
<br> 
			<?php 
		if($this->session->flashdata('message')){
			echo $this->session->flashdata('message');	
		}
		?>	
		<?php 
  if($list_view=='table'){
	  ?>
    <table class="table table-bordered">
      <tr>
        <th>#</th>
        <th><?php echo $this->lang->line('quiz_name');?></th>
        <th><?php echo $this->lang->line('noq');?></th>
        <th><?php echo $this->lang->line('action');?> </th>
      </tr>
<?php 
if(count($result)==0){  //没有数据时
	?>
      <tr>
        <td colspan="3"><?php echo $this->lang->line('no_record_found');?></td>
      </tr>	
	
	
	<?php
}
foreach($result as $key => $val){
?>
      <tr>
        <td><?php echo $val['quid'];?></td>
        <td><?php echo substr(strip_tags($val['quiz_name']),0,50);?></td>
        <td><?php echo $val['noq'];?></td>
        <td>
<a href="<?php echo site_url('quiz/quiz_detail/'.$val['quid']);?>" class="btn btn-success"  ><?php echo $this->lang->line('attempt');?> </a>

<?php 
if($logged_in['su']=='1'){
  //仅管理员才有编辑和删除功能
	?>
			
<a href="<?php echo site_url('quiz/edit_quiz/'.$val['quid']);?>"><img src="<?php echo base_url('images/edit.png');?>"></a>
<a href="javascript:remove_entry('quiz/remove_quiz/<?php echo $val['quid'];?>');"><img src="<?php echo base_url('images/cross.png');?>"></a>
<?php 
}
?>
</td>
</tr>

<?php 
}
?>
</table>

  <?php 
  }else{
    //当选择展示考试列表为格子形式时：
	  ?>
	  <?php 
      if(count($result)==0){  //没有数据
        ?>
          <?php echo $this->lang->line('no_record_found');?>
    <?php
      }
      //cc和colorcode主要设置表头的颜色，无实际作用
      $cc=0;
      $colorcode=array(
      'success',
      'warning',
      'info',
      'danger'
      );
      foreach($result as $key => $val){
    ?>
	  
	    <!-- item -->
      <div class="col-md-4 text-center">
        <div class="panel panel-<?php echo $colorcode[$cc];?> panel-pricing">
          <div class="panel-heading">
              <i class="fa fa-desktop"></i>
              <h3><?php echo substr(strip_tags($val['quiz_name']),0,50);?></h3>
          </div>
          <div class="panel-body text-center">
              <p><strong><?php echo $this->lang->line('duration');?> <?php echo $val['duration'];?></strong></p>
          </div>
          <ul class="list-group text-center">
              <li class="list-group-item"><i class="fa fa-check"></i> <?php echo $this->lang->line('noq');?>:  <?php echo $val['noq'];?></li>
              <li class="list-group-item"><i class="fa fa-check"></i> <?php echo $this->lang->line('maximum_attempts');?>: <?php echo $val['maximum_attempts'];?></li>
          </ul>
          <div class="panel-footer">
          <a href="<?php echo site_url('quiz/quiz_detail/'.$val['quid']);?>" class="btn btn-success"  ><?php echo $this->lang->line('attempt');?> </a>

    <?php 
    if($logged_in['su']=='1'){  //如果是管理员，添加编辑还有删除功能
    ?>
          <a href="<?php echo site_url('quiz/edit_quiz/'.$val['quid']);?>"><img src="<?php echo base_url('images/edit.png');?>"></a>
          <a href="javascript:remove_entry('quiz/remove_quiz/<?php echo $val['quid'];?>');"><img src="<?php echo base_url('images/cross.png');?>"></a>
    <?php 
    }
    ?>


          </div>
        </div>
      </div>
      <!-- /item --> 
	  
	  
	  <?php 
	  if($cc >= 4){
	  $cc=0;
	  }else{
	  $cc+=1;
	  }
	  
}

  }
  ?>

</div>

</div>
<br><br>

<?php
//number_of_rows=30
if( ( $limit-($this->config->item('number_of_rows')) ) >=0){ 
  $back=$limit-($this->config->item('number_of_rows')); 
}else{ 
  $back='0'; 
} 
?>

<a href="<?php echo site_url('quiz/index/'.$back.'/'.$list_view);?>"  class="btn btn-primary"><?php echo $this->lang->line('back');?></a>
&nbsp;&nbsp;
<?php
 $next=$limit+($this->config->item('number_of_rows'));  ?>

<a href="<?php echo site_url('quiz/index/'.$next.'/'.$list_view);?>"  class="btn btn-primary"><?php echo $this->lang->line('next');?></a>





</div>