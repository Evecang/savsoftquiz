<div class="container">

   
<h3><?php echo $title;?></h3>
  


 <div class="row">
    <form method="post" action="<?php echo site_url('qbank/edit_question_6/'.$question['qid']);?>">
   
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
                       
                       <option value="<?php echo $val['cid'];?>"  <?php if($question['cid']==$val['cid']){ echo 'selected'; } ?> ><?php echo $val['category_name'];?></option>
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
                       
                       <option value="<?php echo $val['lid'];?>" <?php if($question['lid']==$val['lid']){ echo 'selected'; } ?> ><?php echo $val['level_name'];?></option>
                       <?php 
                   }
                   ?>
                   </select>
           </div>

           
           

           <div class="form-group">	 
                   <label for="inputEmail"  ><?php echo $this->lang->line('question');?></label> 
                   <textarea  name="question"  class="form-control"   ><?php echo $question['question'];?></textarea>
           </div>
           <div class="form-group">	 
                   <label for="inputEmail"  ><?php echo $this->lang->line('description');?></label> 
                   <textarea  name="description"  class="form-control"><?php echo $question['description'];?></textarea>
           </div>

       <?php 
       foreach($options as $key => $val){
           ?>

           <div class="form-group">	 
                   <label for="inputEmail"  ><?php echo $this->lang->line('subquestions');?> <?php echo $key+1;?>)</label> <br>
                    
                    <label>
                   <input type="radio" name="<?php echo "score".$key?>" value="0" <?php if($val['q_option_match']==0){echo 'checked';} ?> >
                    A.&nbsp;<input type="text" name="sub_option1[]" value="<?php echo $val['q_option_match_option'][0];?>" placeholder="sub option A">
                    </label>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                    <label>
                   <input type="radio" name="<?php echo "score".$key?>" value="1" <?php if($val['q_option_match']==1){echo 'checked';} ?> >
                    B.&nbsp;<input type="text" name="sub_option2[]" value="<?php echo $val['q_option_match_option'][1];?>" placeholder="sub option B">
                    </label>
                   <br>

                    <label>
                   <input type="radio" name="<?php echo "score".$key?>" value="2" <?php if($val['q_option_match']==2){echo 'checked';} ?> >
                    C.&nbsp;<input type="text" name="sub_option3[]" value="<?php echo $val['q_option_match_option'][2];?>" placeholder="sub option C">
                    </label>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                    <label>
                   <input type="radio" name="<?php echo "score".$key?>" value="3" <?php if($val['q_option_match']==3){echo 'checked';} ?> >
                    D.&nbsp;<input type="text" name="sub_option4[]" value="<?php echo $val['q_option_match_option'][3];?>" placeholder="sub option D">
                    </label>

           </div><br>

       <?php 
       }
       ?>
       <div class="form-group">	 
               <label for="inputEmail"  ><?php echo $this->lang->line('analyses');?></label> 
               <textarea  name="analyses"  class="form-control"><?php echo $question['analyses'];?></textarea>
       </div>


   <button class="btn btn-default" type="submit"><?php echo $this->lang->line('submit');?></button>

       </div>
</div>




</div>
     </form>
     
     <div class="col-md-3">
       
       
           <div class="form-group">	 
           <table class="table table-bordered">
           <tr><td><?php echo $this->lang->line('no_times_corrected');?></td><td><?php echo $question['no_time_corrected'];?></td></tr>
           <tr><td><?php echo $this->lang->line('no_times_incorrected');?></td><td><?php echo $question['no_time_incorrected'];?></td></tr>
           <tr><td><?php echo $this->lang->line('no_times_unattempted');?></td><td><?php echo $question['no_time_unattempted'];?></td></tr>

           </table>

           </div>


     </div>
</div>





</div>