<div class="row"  style="border-bottom:1px solid #dddddd;">
<div class="container"  >
<div class="col-md-1">
</div>
<div class="col-md-10">
<a href="<?php echo base_url();?>"><img src="<?php echo base_url('images/logo.png');?>"></a>
<?php echo $this->lang->line('login_tagline');?>
</div>
<div class="col-md-1">
</div>

</div>

</div>

 <div class="container">
<a href="<?php echo site_url('login');?>">Login</a>
 
   
 <h3><?php echo $title;?></h3>
   
     

  <div class="row">
     
    <?php 
    $cc=0;
    //bottstrap自带颜色样式
$colorcode=array(
'success',
'warning',
'info',
'danger'
);
    //TODO：
    //$group_list 由 group_list 表获得
    //用户注册分为三个组别，这里可以通过更改 group_list 表
    //将此处设置为不同班级的选择
    //其中 group_name 可更改为班级名
    //注意下处注释
    foreach($group_list as $k => $val){
    
   ?>
	                <!-- item -->
                <div class="col-md-4 text-center">
                    <div class="panel panel-<?php echo $colorcode[$cc];?> panel-pricing">
                        <div class="panel-heading">
                            <i class="fa fa-desktop"></i>
                            <h3><?php echo $val['group_name'];?></h3>
                        </div>
                        <div class="panel-body text-center">
                          
                          <?php 
                          echo $val['description'];?>
                          <hr>
                          Price: 
<?php 
//TODO:
//此处通过配置 $this->config->item()来配置价格类型，比如美元$或者人名币￥，可删除大部分
if($val['price']==0){
echo "0";
}else{
echo $this->config->item('base_currency_prefix').' '.$val['price'].' '.$this->config->item('base_currency_sufix'); 
}
//此上可删除大部分
?>
                           
                        </div>
                        
                        <div class="panel-footer">
                         
						 
<a href="<?php echo site_url('login/registration/'.$val['gid']);?>" class="btn btn-success"  ><?php echo $this->lang->line('register');?> </a>
 

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
    ?>
  
</div>

 



</div>
<script>
 
</script>
