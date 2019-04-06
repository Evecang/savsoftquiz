<div class="container">

   
<h3><?php echo $title;?></h3>
  


 <div class="row">
    <form method="post" action="<?php echo site_url('qbank/new_question_6/'.$nop);?>">
   
<div class="col-md-8">
<br> 
<div class="login-panel panel panel-default">
       <div class="panel-body"> 
   
   
   
        <?php 
       if($this->session->flashdata('message')){
           echo $this->session->flashdata('message');	
       }
       ?>	
       
       
       
        <div class="form-group">	 
            <?php echo $this->lang->line('cloze_test');?>
        </div>

           
           <div class="form-group">	 
                   <label   ><?php echo $this->lang->line('select_category');?></label> 
                   <select class="form-control" name="cid">
                   <?php 
                   foreach($category_list as $key => $val){
                       ?>
                       
                       <option value="<?php echo $val['cid'];?>"><?php echo $val['category_name'];?></option>
                       <?php 
                   }
                   ?>
                   </select>
           </div>
           
           
           <div class="form-group">	 
                   <label   ><?php echo $this->lang->line('select_level');?></label> 
                   <select class="form-control" name="lid">
                   <?php 
                   foreach($level_list as $key => $val){
                       ?>
                       
                       <option value="<?php echo $val['lid'];?>"><?php echo $val['level_name'];?></option>
                       <?php 
                   }
                   ?>
                   </select>
           </div>

           
           

           <div class="form-group">	 
                   <label for="inputEmail"  ><?php echo $this->lang->line('question');?></label> 
                   <textarea  name="question"  class="form-control"   ></textarea>
           </div>
           <div class="form-group">	 
                   <label for="inputEmail"  ><?php echo $this->lang->line('description');?></label> 
                   <textarea  name="description"  class="form-control"></textarea>
           </div>
           
           <!-- 选项设置 -->
       <?php 
       for($i=1; $i<=$nop; $i++){
           ?>
           <div class="form-group">	 
                   <label for="inputEmail"  ><?php echo $this->lang->line('subquestions');?> <?php echo $i;?>)</label> <br>
                    
                    <label>
                   <input type="radio" name="<?php echo "score".$i?>" value="0" checked>
                    A.&nbsp;<input type="text" name="sub_option1[]" value="" placeholder="sub option A">
                    </label>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                    <label>
                   <input type="radio" name="<?php echo "score".$i?>" value="1">
                    B.&nbsp;<input type="text" name="sub_option2[]" value="" placeholder="sub option B">
                    </label>
                   <br>

                    <label>
                   <input type="radio" name="<?php echo "score".$i?>" value="2">
                    C.&nbsp;<input type="text" name="sub_option3[]" value="" placeholder="sub option C">
                    </label>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                    <label>
                   <input type="radio" name="<?php echo "score".$i?>" value="3">
                    D.&nbsp;<input type="text" name="sub_option4[]" value="" placeholder="sub option D">
                    </label>

           </div><br>
       <?php 
       }
       ?>
        <div class="form-group">	 
                <label for="inputEmail"  ><?php echo $this->lang->line('analyses');?></label> 
                <textarea  name="analyses"  class="form-control"></textarea>
        </div>


   <button class="btn btn-default" type="submit"><?php echo $this->lang->line('submit');?></button>

       </div>
</div>




</div>
     </form>
</div>





</div>