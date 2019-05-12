<?php
Class Quiz_model extends CI_Model
{
 
  function quiz_list($limit){	//根据$limit数目进行筛选，返回含有关键词的考试列表
	  
	$logged_in=$this->session->userdata('logged_in');
	if($logged_in['su']=='0'){	//学生
		$gid=$logged_in['gid'];
		$where="FIND_IN_SET('".$gid."', gids)";  
		$this->db->where($where);
	}
			
	if($this->input->post('search') && $logged_in['su']=='1'){	//管理员，并且有搜索关键词
		$search=$this->input->post('search');
		$this->db->or_where('quid',$search);
		$this->db->or_like('quiz_name',$search);
		$this->db->or_like('description',$search);

	}

	$this->db->limit($this->config->item('number_of_rows'),$limit);	//限制你的查询返回结果的数量(数量，偏移量)
	$this->db->order_by('quid','desc');
	$query=$this->db->get('savsoft_quiz');
	return $query->result_array();
		
 }
 
 
   function recent_quiz($limit){
	  
	 
		$this->db->limit($limit);
		$this->db->order_by('quid','desc');
		$query=$this->db->get('savsoft_quiz');
		return $query->result_array();
   }
 
   function open_quiz($limit){
	  
	 
		$this->db->limit($this->config->item('number_of_rows'),$limit);
		$this->db->order_by('quid','desc');
		$query=$this->db->get('savsoft_quiz');
		return $query->result_array();
   }
 
 
 function num_quiz(){
	 
	 $query=$this->db->get('savsoft_quiz');
		return $query->num_rows();
 }
 
 function insert_quiz(){
	 
	 $userdata=array(
	 'quiz_name'=>$this->input->post('quiz_name'),
	 'description'=>$this->input->post('description'),
	 'start_date'=>strtotime($this->input->post('start_date')),
	 'end_date'=>strtotime($this->input->post('end_date')),
	 'duration'=>$this->input->post('duration'),
	 'maximum_attempts'=>$this->input->post('maximum_attempts'),
	 'pass_percentage'=>$this->input->post('pass_percentage'),
	 'correct_score'=>$this->input->post('correct_score'),
	 'incorrect_score'=>$this->input->post('incorrect_score'),
	 'ip_address'=>$this->input->post('ip_address'),
	 'view_answer'=>$this->input->post('view_answer'),
	 'camera_req'=>$this->input->post('camera_req'),
	 'quiz_template'=>$this->input->post('quiz_template'),
	 'with_login'=>$this->input->post('with_login'),
	 'gids'=>implode(',',$this->input->post('gids')),
	 'question_selection'=>$this->input->post('question_selection')
	 );
	 	$userdata['gen_certificate']=$this->input->post('gen_certificate'); 
	 
	 if($this->input->post('certificate_text')){
		$userdata['certificate_text']=$this->input->post('certificate_text'); 
	 }
	  $this->db->insert('savsoft_quiz',$userdata);
	 $quid=$this->db->insert_id();
	return $quid;
	 
 }
 
 
 function update_quiz($quid){
	 
	 $userdata=array(
	 'quiz_name'=>$this->input->post('quiz_name'),
	 'description'=>$this->input->post('description'),
	 'start_date'=>strtotime($this->input->post('start_date')),
	 'end_date'=>strtotime($this->input->post('end_date')),
	 'duration'=>$this->input->post('duration'),
	 'maximum_attempts'=>$this->input->post('maximum_attempts'),
	 'pass_percentage'=>$this->input->post('pass_percentage'),
	 'correct_score'=>$this->input->post('correct_score'),
	 'incorrect_score'=>$this->input->post('incorrect_score'),
	 'ip_address'=>$this->input->post('ip_address'),
	 'view_answer'=>$this->input->post('view_answer'),
	 'camera_req'=>$this->input->post('camera_req'),
	  'quiz_template'=>$this->input->post('quiz_template'),
	'with_login'=>$this->input->post('with_login'),
	 'gids'=>implode(',',$this->input->post('gids'))
	 );
	  	 	 
		$userdata['gen_certificate']=$this->input->post('gen_certificate'); 
	  
	 if($this->input->post('certificate_text')){
		$userdata['certificate_text']=$this->input->post('certificate_text'); 
	 }
 
	  $this->db->where('quid',$quid);
	  $this->db->update('savsoft_quiz',$userdata);
	  
	  $this->db->where('quid',$quid);
	  $query=$this->db->get('savsoft_quiz',$userdata);
	 $quiz=$query->row_array();
	 if($quiz['question_selection']=='1'){
		 
	  $this->db->where('quid',$quid);
	  $this->db->delete('savsoft_qcl');
                $correct_i=array();
        	 $incorrect_i=array();	 
	 foreach($_POST['cid'] as $ck => $val){
		 if(isset($_POST['noq'][$ck])){
			 if($_POST['noq'][$ck] >= '1'){
		 $userdata=array(
		 'quid'=>$quid,
		 'cid'=>$val,
		 'lid'=>$_POST['lid'][$ck],
		 'i_correct'=>$_POST['i_correct'][$ck],
		 'i_incorrect'=>$_POST['i_incorrect'][$ck],
		 'noq'=>$_POST['noq'][$ck]
		 );
		 $this->db->insert('savsoft_qcl',$userdata);
		for($i=1; $i<=$_POST['noq'][$ck]; $i++){
			$correct_i[]=$_POST['i_correct'][$ck];
			$incorrect_i[]=$_POST['i_incorrect'][$ck];
		}
		 }
		 }
	 }
		 $userdata=array(
		 'noq'=>array_sum($_POST['noq']),
		 'correct_score'=>implode(',',$correct_i),
		 'incorrect_score'=>implode(',',$incorrect_i)
	);
	 $this->db->where('quid',$quid);
	  $this->db->update('savsoft_quiz',$userdata);
	 }else{
			$correct_i=array();
			 $incorrect_i=array();
		foreach($_POST['i_correct'] as $ck =>$cv){
		$correct_i[]=$_POST['i_correct'][$ck];
		$incorrect_i[]=$_POST['i_incorrect'][$ck];
		}

	 $userdata=array(
		 'correct_score'=>implode(',',$correct_i),
		  'incorrect_score'=>implode(',',$incorrect_i)
		 
			);
	  $this->db->where('quid',$quid);
	  $this->db->update('savsoft_quiz',$userdata);


	}
	return $quid;
	 
 }


 function wx_update_quiz($quid){
	 
	$userdata=array(
	'quiz_name'=>$this->input->post('quiz_name'),
	'start_date'=>strtotime($this->input->post('start_date')),
	'end_date'=>strtotime($this->input->post('end_date')),
	'duration'=>$this->input->post('duration'),
	'maximum_attempts'=>$this->input->post('maximum_attempts'),
	'pass_percentage'=>$this->input->post('pass_percentage'),
	// 'gids'=>implode(',',$this->input->post('gids'))
	'gids'=>$this->input->post('gids')
	);

	$this->db->where('quid',$quid);
	$this->db->update('savsoft_quiz',$userdata);
	 
	$this->db->where('quid',$quid);
	$query=$this->db->get('savsoft_quiz',$userdata);
	$quiz=$query->row_array();

   	return $quid;
	
}
 
 function get_questions($qids){	//根据qids在qbank、category、level表中查
	 if($qids == ''){
		$qids=0; 
	 }else{
		 $qids=$qids;
	 }
/*
	 if($cid!='0'){
		 $this->db->where('savsoft_qbank.cid',$cid);
	 }
	 if($lid!='0'){
		 $this->db->where('savsoft_qbank.lid',$lid);
	 }
*/
	  
	 $query=$this->db->query("select * from savsoft_qbank join savsoft_category on savsoft_category.cid=savsoft_qbank.cid join savsoft_level on savsoft_level.lid=savsoft_qbank.lid 
	 where savsoft_qbank.qid in ($qids) order by FIELD(savsoft_qbank.qid,$qids) 
	 ");
	 return $query->result_array();
	 
	 
 }
 
 function get_options($qids){	//获取答案
	 
	 
	 $query=$this->db->query("select * from savsoft_options where qid in ($qids) order by FIELD(savsoft_options.qid,$qids)");
	 return $query->result_array();
	 
 }
 
 
 
 function up_question($quid,$qid){
  	$this->db->where('quid',$quid);
 	$query=$this->db->get('savsoft_quiz');
 	$result=$query->row_array();
 	$qids=$result['qids'];
 	if($qids==""){
 	$qids=array();
 	}else{
 	$qids=explode(",",$qids);
 	}
 	$qids_new=array();
 	foreach($qids as $k => $qval){
 	if($qval == $qid){

 	$qids_new[$k-1]=$qval;
	$qids_new[$k]=$qids[$k-1];
	
 	}else{
	$qids_new[$k]=$qval;
 	
	}
 	}
 	
 	$qids=array_filter(array_unique($qids_new));
 	$qids=implode(",",$qids);
 	$userdata=array(
 	'qids'=>$qids
 	);
 		$this->db->where('quid',$quid);
	$this->db->update('savsoft_quiz',$userdata);

}



function down_question($quid,$qid){
  	$this->db->where('quid',$quid);
 	$query=$this->db->get('savsoft_quiz');
 	$result=$query->row_array();
 	$qids=$result['qids'];
 	if($qids==""){
 	$qids=array();
 	}else{
 	$qids=explode(",",$qids);
 	}
 	$qids_new=array();
 	foreach($qids as $k => $qval){
 	if($qval == $qid){

 	$qids_new[$k]=$qids[$k+1];
$kk=$k+1;
	$kv=$qval;
 	}else{
	$qids_new[$k]=$qval;
 	
	}

 	}
 	$qids_new[$kk]=$kv;
	
 	$qids=array_filter(array_unique($qids_new));
 	$qids=implode(",",$qids);
 	$userdata=array(
 	'qids'=>$qids
 	);
 		$this->db->where('quid',$quid);
	$this->db->update('savsoft_quiz',$userdata);

}




function get_qcl($quid){
	
	 $this->db->where('quid',$quid);
	 $query=$this->db->get('savsoft_qcl');
	 return $query->result_array();
	
}

 function remove_qid($quid,$qid){
	 
	 $this->db->where('quid',$quid);
	 $query=$this->db->get('savsoft_quiz');
	 $quiz=$query->row_array();
	 $new_qid=array();
	 foreach(explode(',',$quiz['qids']) as $key => $oqid){
		 
		 if($oqid != $qid){
			$new_qid[]=$oqid; 
			 
		 }
		 
	 }
	 $noq=count($new_qid);
	 $userdata=array(
	 'qids'=>implode(',',$new_qid),
	 'noq'=>$noq
	 
	 );
	 $this->db->where('quid',$quid);
	 $this->db->update('savsoft_quiz',$userdata);
	 return true;
 }
 
  function add_qid($quid,$qid){
	 
	 $this->db->where('quid',$quid);
	 $query=$this->db->get('savsoft_quiz');
	 $quiz=$query->row_array();
	 $new_qid=array();
	 $new_qid[]=$qid;
	 foreach(explode(',',$quiz['qids']) as $key => $oqid){
		 
		 if($oqid != $qid){
			$new_qid[]=$oqid; 
			 
		 }
		 
	 }
	 $new_qid=array_filter(array_unique($new_qid));
	 $noq=count($new_qid);
	 $userdata=array(
	 'qids'=>implode(',',$new_qid),
	 'noq'=>$noq
	 
	 );
	 $this->db->where('quid',$quid);
	 $this->db->update('savsoft_quiz',$userdata);
	 return true;
 }
 

 
 function get_quiz($quid){
	 $this->db->where('quid',$quid);
	 $query=$this->db->get('savsoft_quiz');
	 return $query->row_array();
	 
	 
 } 
 
 function remove_quiz($quid){
	 
	 $this->db->where('quid',$quid);
	 if($this->db->delete('savsoft_quiz')){
		 
		 return true;
	 }else{
		 
		 return false;
	 }
	 
	 
 }
 
  
 
 function count_result($quid,$uid){
	 
	 $this->db->where('quid',$quid);
	 $this->db->where('uid',$uid);
	$query=$this->db->get('savsoft_result');
	return $query->num_rows();
	 
 }
 
 
 function insert_result($quid,$uid){	//创建结果
	 
	 // get quiz info
	$this->db->where('quid',$quid);
	$query=$this->db->get('savsoft_quiz');
	$quiz=$query->row_array();
	 
	if($quiz['question_selection']=='0'){
		 
		// get questions	
		$noq=$quiz['noq'];	
		$qids=explode(',',$quiz['qids']);
		$categories=array();
		$category_range=array();

		$i=0;
		$wqids=implode(',',$qids);
		$noq=array();
		$query=$this->db->query("select * from savsoft_qbank join savsoft_category on savsoft_category.cid=savsoft_qbank.cid where qid in ($wqids) ORDER BY FIELD(qid,$wqids)  ");	
		$questions=$query->result_array();
		foreach($questions as $qk => $question){
			if(!in_array($question['category_name'],$categories)){
				if(count($categories)!=0){
					$i+=1;
				}
				$categories[]=$question['category_name'];
				$noq[$i]+=1;
			}else{
				$noq[$i]+=1;
			}
		}
		
		$categories=array();
		$category_range=array();

		$i=-1;
		foreach($questions as $qk => $question){
			if(!in_array($question['category_name'],$categories)){
				$categories[]=$question['category_name'];
				$i+=1;	
				$category_range[]=$noq[$i];
			} 
		}
	
	}else{
		// randomaly select qids
		$this->db->where('quid',$quid);
		$query=$this->db->get('savsoft_qcl');
		$qcl=$query->result_array();
		$qids=array();
		$categories=array();
		$category_range=array();
		
		foreach($qcl as $k => $val){
			$cid=$val['cid'];
			$lid=$val['lid'];
			$noq=$val['noq'];
			
			$i=0;
			$query=$this->db->query("select * from savsoft_qbank join savsoft_category on savsoft_category.cid=savsoft_qbank.cid where savsoft_qbank.cid='$cid' and lid='$lid' ORDER BY RAND() limit $noq ");	
			$questions=$query->result_array();
			foreach($questions as $qk => $question){
				$qids[]=$question['qid'];
				if(!in_array($question['category_name'],$categories)){
					$categories[]=$question['category_name'];
					$category_range[]=$i+$noq;
				}
			}
		}
	}
	$zeros=array();
	foreach($qids as $qidval){
		$zeros[]=0;
	}
	
	
	$userdata=array(
	'quid'=>$quid,
	'uid'=>$uid,
	'r_qids'=>implode(',',$qids),
	'categories'=>implode(',',$categories),
	'category_range'=>implode(',',$category_range),
	'start_time'=>time(),
	'individual_time'=>implode(',',$zeros),
	'score_individual'=>implode(',',$zeros),
	'attempted_ip'=>$_SERVER['REMOTE_ADDR'] 
	);
	
	if($this->session->userdata('photoname')){
		$photoname=$this->session->userdata('photoname');
		$userdata['photo']=$photoname;
	}
	$this->db->insert('savsoft_result',$userdata);
	$rid=$this->db->insert_id();
	return $rid;
 }
 
 
 
 function open_result($quid,$uid){	//根据uid在result中 得到怎么测试的用户（考试了考试 但未提交）
	 $result_open=$this->lang->line('open');	//'Open'
		$query=$this->db->query("select * from savsoft_result  where savsoft_result.result_status='$result_open'  and savsoft_result.uid='$uid'  "); 
	if($query->num_rows() >= '1'){	//正常情况下，只有一个符合条件
		$result=$query->row_array();	//返回一行数据，是数组形式
return $result['rid'];		//result的id
	}else{	//没有结果
		return '0';
	}
	
	 
 }
 
 function quiz_result($rid){	//根据rid在result表中查询，同时保证试卷在quid表中存在（也把quid的表字段导出），返回得到的第一行数据
	 
	 
	$query=$this->db->query("select * from savsoft_result join savsoft_quiz on savsoft_result.quid=savsoft_quiz.quid where savsoft_result.rid='$rid' "); 
	return $query->row_array(); 
	 
 }
 
function saved_answers($rid){	//作答的结果
	 
	 
	$query=$this->db->query("select * from savsoft_answers  where savsoft_answers.rid='$rid' "); 
	return $query->result_array(); 
	 
 }
 
 
 function assign_score($rid,$qno,$score){	//提交 long answer的分数 score-1正确 -2错误，qno为题目在试卷中的顺序
	 $qp_score=$score;
	 $query=$this->db->query("select * from savsoft_result join savsoft_quiz on savsoft_result.quid=savsoft_quiz.quid where savsoft_result.rid='$rid' "); 
	$quiz=$query->row_array(); 	//result+quiz表
	$score_ind=explode(',',$quiz['score_individual']);
	$score_ind[$qno]=$score;	//得分标志
	$r_qids=explode(',',$quiz['r_qids']);
	$marks = 0;
	$correct_score=explode(',',$quiz['correct_score']);
	$incorrect_score=explode(',',$quiz['incorrect_score']);
		$manual_valuation=0;
	foreach($score_ind as $mk => $score){
		
		if($score == 1){
			
			$marks+=$correct_score[$mk];
		}
		if($score == 2){
			
			$marks+=$incorrect_score[$mk];
		}
		if($score == 3){
			
			$manual_valuation=1;
		}
		else{	//4 -> cloze test
			// $cloze_options = $this->db->query("select * from savsoft_options where qid='$qids_perf[1]' ");
			$s = explode('-',$score);	//4-$marks
			$marks += floatval($s[1])*$correct_score[$mk];
		}
		
	}
	$percentage_obtained = ( $marks / array_sum($correct_score) ) * 100;
	if($percentage_obtained >= $quiz['pass_percentage']){
		$qr=$this->lang->line('pass');
	}else{
		$qr=$this->lang->line('fail');
		
	}
	 $userdata=array(
	  'score_individual'=>implode(',',$score_ind),
	  'score_obtained'=>$marks,
	 'percentage_obtained'=>$percentage_obtained,
	 'manual_valuation'=>$manual_valuation
	 );
	 if($manual_valuation == 1){
		 $userdata['result_status']=$this->lang->line('pending');
	}else{
		$userdata['result_status']=$qr;
	}
	 $this->db->where('rid',$rid);
	 $this->db->update('savsoft_result',$userdata);
	 
	 // question performance
	 $qp=$r_qids[$qno];
	 		 $crin="";
		if($qp_score=='1'){
			$crin="no_time_corrected=(no_time_corrected +1)"; 	 
		 }else if($qp_score=='2'){
			$crin="no_time_incorrected=(no_time_incorrected +1)"; 	 
		 }
		 $query_qp="update savsoft_qbank set  $crin  where qid='$qp'  ";
	 $this->db->query($query_qp);
 }
 
 
 
 function submit_result(){	//提交结果，有一个参数$rid
	 if(!$this->session->userdata('logged_in')){
		$logged_in=$this->session->userdata('logged_in_raw');
	 }else{
	 $logged_in=$this->session->userdata('logged_in');
	 }
//TODO：marks的计算逻辑有误
	 $email=$logged_in['email'];	//账号
	 $rid=$this->session->userdata('rid');	//结果id
	$query=$this->db->query("select * from savsoft_result join savsoft_quiz on savsoft_result.quid=savsoft_quiz.quid where savsoft_result.rid='$rid' "); 
	$quiz=$query->row_array(); 	//第一行数据，根据rid结合了quiz和result表
	$score_ind=explode(',',$quiz['score_individual']);	//在result表中 ->可能是一种解答结果的标志，1正确 2错误 3未完成 4-$marks为完形填空的得分
	$r_qids=explode(',',$quiz['r_qids']);	//result表中 记录试卷中包含的题目id序列
	$qids_perf=array();		//索引为题目qid,值为用户作答的正确错误标记
	$marks=0;	//得分的标记
	// $correct_score=$quiz['correct_score'];	//每题正确时的得分（序列）from quiz table   1,1,1,1
	$correct_score = explode(',',$quiz['correct_score']);
	// $incorrect_score=$quiz['incorrect_score'];	//每道题错误时的得分（序列） from quiz table    0,0,0,0
	$incorrect_score = explode(',',$quiz['incorrect_score']);
	$total_time=array_sum(explode(',',$quiz['individual_time']));	//result表 每题用时  array_sum()返回数组中所有值的和-->一共用时
	$manual_valuation=0;	//
	foreach($score_ind as $mk => $score){	//mk题索引 score每题正确，错误，未完成的标记
		$qids_perf[$r_qids[$mk]]=$score;
		
		if($score == 1){
			
			// $marks+=$correct_score;
			$marks+=$correct_score[$mk];
			
		}
		if($score == 2){
			
			// $marks+=$incorrect_score;
			$marks+=$incorrect_score[$mk];

		}
		if($score == 3){
			
			$manual_valuation=1;
		}
		else{	//4 -> cloze test
			// $cloze_options = $this->db->query("select * from savsoft_options where qid='$qids_perf[1]' ");
			$s = explode('-',$score);	//4-$marks
			$marks += floatval($s[1])*$correct_score[$mk];
		}
		
	}
	// $percentage_obtained=($marks/($quiz['noq']*$correct_score))*100;	//总分为100？
	$percentage_obtained = ( $marks / array_sum($correct_score) ) * 100;
	if($percentage_obtained >= $quiz['pass_percentage']){
		$qr=$this->lang->line('pass');
	}else{
		$qr=$this->lang->line('fail');
		
	}
	 $userdata=array(
	  'total_time'=>$total_time,
	   'end_time'=>time(),
	  'score_obtained'=>$marks,
	 'percentage_obtained'=>$percentage_obtained,
	 'manual_valuation'=>$manual_valuation
	 );
	 if($manual_valuation == 1){
		 $userdata['result_status']=$this->lang->line('pending');
	}else{
		$userdata['result_status']=$qr;
	}
	 $this->db->where('rid',$rid);
	 $this->db->update('savsoft_result',$userdata);
	 
	 
	 foreach($qids_perf as $qp => $qpval){
		 $crin="";
		 if($qpval=='0'){
			$crin=", no_time_unattempted=(no_time_unattempted +1) "; 
		 }else if($qpval=='1'){
			$crin=", no_time_corrected=(no_time_corrected +1)"; 	 
		 }else if($qpval=='2'){
			$crin=", no_time_incorrected=(no_time_incorrected +1)"; 	 
		 }
		  $query_qp="update savsoft_qbank set no_time_served=(no_time_served +1)  $crin  where qid='$qp'  ";
	 $this->db->query($query_qp);
		 
	 }
	 
if($this->config->item('allow_result_email')){
	$this->load->library('email');
	$query = $this -> db -> query("select savsoft_result.*,savsoft_users.*,savsoft_quiz.* from savsoft_result, savsoft_users, savsoft_quiz where savsoft_users.uid=savsoft_result.uid and savsoft_quiz.quid=savsoft_result.quid and savsoft_result.rid='$rid'");
	$qrr=$query->row_array();
  		if($this->config->item('protocol')=="smtp"){
			$config['protocol'] = 'smtp';
			$config['smtp_host'] = $this->config->item('smtp_hostname');
			$config['smtp_user'] = $this->config->item('smtp_username');
			$config['smtp_pass'] = $this->config->item('smtp_password');
			$config['smtp_port'] = $this->config->item('smtp_port');
			$config['smtp_timeout'] = $this->config->item('smtp_timeout');
			$config['mailtype'] = $this->config->item('smtp_mailtype');
			$config['starttls']  = $this->config->item('starttls');
			$config['newline']  = $this->config->item('newline');

			$this->email->initialize($config);
		}
			$toemail=$qrr['email'];
			$fromemail=$this->config->item('fromemail');
			$fromname=$this->config->item('fromname');
			$subject=$this->config->item('result_subject');
			$message=$this->config->item('result_message');
			
			$subject=str_replace('[email]',$qrr['email'],$subject);
			$subject=str_replace('[first_name]',$qrr['first_name'],$subject);
			$subject=str_replace('[last_name]',$qrr['last_name'],$subject);
			$subject=str_replace('[quiz_name]',$qrr['quiz_name'],$subject);
			$subject=str_replace('[score_obtained]',$qrr['score_obtained'],$subject);
			$subject=str_replace('[percentage_obtained]',$qrr['percentage_obtained'],$subject);
			$subject=str_replace('[current_date]',date('Y-m-d H:i:s',time()),$subject);
			$subject=str_replace('[result_status]',$qrr['result_status'],$subject);
			
			$message=str_replace('[email]',$qrr['email'],$message);
			$message=str_replace('[first_name]',$qrr['first_name'],$message);
			$message=str_replace('[last_name]',$qrr['last_name'],$message);
			$message=str_replace('[quiz_name]',$qrr['quiz_name'],$message);
			$message=str_replace('[score_obtained]',$qrr['score_obtained'],$message);
			$message=str_replace('[percentage_obtained]',$qrr['percentage_obtained'],$message);
			$message=str_replace('[current_date]',date('Y-m-d H:i:s',time()),$message);
			$message=str_replace('[result_status]',$qrr['result_status'],$message);
			 
			
			$this->email->to($toemail);
			$this->email->from($fromemail, $fromname);
			$this->email->subject($subject);
			$this->email->message($message);
			if(!$this->email->send()){
			 //print_r($this->email->print_debugger());
			
			}
	}
	

	return true;
 }
 
 
 
 
 
 function insert_answer(){	//保存答案 并且 自动计算得分
	 $rid=$_POST['rid'];
	$srid=$this->session->userdata('rid');
	if(!$this->session->userdata('logged_in')){
		$logged_in=$this->session->userdata('logged_in_raw');
	}else{
		$logged_in=$this->session->userdata('logged_in');
	}
	$uid=$logged_in['uid'];
	if($srid != $rid){

	return "Something wrong";
	}

	$query=$this->db->query("select * from savsoft_result join savsoft_quiz on savsoft_result.quid=savsoft_quiz.quid where savsoft_result.rid='$rid' "); 
	$quiz=$query->row_array(); 
	$correct_score=$quiz['correct_score'];
	$incorrect_score=$quiz['incorrect_score'];
	$qids=explode(',',$quiz['r_qids']);
	$vqids=$quiz['r_qids'];
	$correct_incorrect=explode(',',$quiz['score_individual']);
	
	
	//删除原来的图片 有重新上传的才删
	$this->db->where('rid',$rid);
	$this->db->where('img_src !=',null);	//找到img_src不为空的项
	$q1 = $this->db->get('savsoft_answers');
	$img_not_null = $q1->result_array();

	// remove existing answers
	$this->db->where('rid',$rid);	
	$this->db->delete('savsoft_answers');	//不断在answer表中更新数据


	
	 foreach($_POST['answer'] as $ak => $answer){	//$answer = 前端的answer[qk],qk在试卷中对应的题目顺序，从0开始
		//$_POST['question_type'][$ak]->value: 1-单选 2-多选 3-short 4-long 5-match 6-cloze  

		 // multiple choice single answer
		 if($_POST['question_type'][$ak] == '1' || $_POST['question_type'][$ak] == '2'){
			 
			 $qid=$qids[$ak];	//$ak在试卷中对应的题目顺序，从0开始，得到对应的qid
			 $query=$this->db->query(" select * from savsoft_options where qid='$qid' ");
			 $options_data=$query->result_array();	//多个结果
			 $options=array();
			 foreach($options_data as $ok => $option){
				 $options[$option['oid']]=$option['score'];	//oid  options表中的oid
			 }
			 $attempted=0;
			 $marks=0;


			foreach($answer as $sk => $ansval){//多选时要foreach TODO:多选时，若正确个数比错误的多1个则有分？

				if($options[$ansval] <= 0 ){	//$ansval=前端的radio/checkbox的value为oid
					$marks+=-1;	
				}else{
					$marks+=$options[$ansval];
				}
				$userdata=array(
				'rid'=>$rid,
				'qid'=>$qid,
				'uid'=>$uid,
				'q_option'=>$ansval,
				'score_u'=>$options[$ansval]
				);

				$this->db->insert('savsoft_answers',$userdata);
				$attempted=1;	
			}
			if($attempted==1){
				if($marks >= '0.99' ){
				$correct_incorrect[$ak]=1;	//正确
				}else{
				$correct_incorrect[$ak]=2;	//错误					
				}
			}else{
				$correct_incorrect[$ak]=0;
			}
		 }
		 // short answer
		 if($_POST['question_type'][$ak] == '3'){
			 
			 $qid=$qids[$ak];
			 $query=$this->db->query(" select * from savsoft_options where qid='$qid' ");
			 $options_data=$query->row_array();
			 $options_data=explode(',',$options_data['q_option']);
			 $noptions=array();
			 foreach($options_data as $op){
				 $noptions[]=strtoupper(trim($op));	//大写
			 }
			 
			 $attempted=0;
			 $marks=0;
				foreach($answer as $sk => $ansval){
					if($ansval != ''){
						if(in_array(strtoupper(trim($ansval)),$noptions)){
						$marks=1;	
					}else{
						$marks=0;
					}
					
				$attempted=1;

					$userdata=array(
					'rid'=>$rid,
					'qid'=>$qid,
					'uid'=>$uid,
					'q_option'=>$ansval,
					'score_u'=>$marks
					);
					$this->db->insert('savsoft_answers',$userdata);

				}
				}
				if($attempted==1){
					if($marks==1){
					$correct_incorrect[$ak]=1;	
					}else{
					$correct_incorrect[$ak]=2;							
					}
				}else{
					$correct_incorrect[$ak]=0;
				}
		 }
		 
		 // long answer
		 if($_POST['question_type'][$ak] == '4'){
			//answer + qk + file => qp=ak
			$data_name = 'answer'.$ak.'file';
			$file = $_FILES[$data_name];
			$file_path = null;
			if($file['error']>0){	//sth wrong 4-空	没有上传图片（已经上传 或者 用户的答案写在了textarea中） 或者 上传为空
				//echo $file['error'];	
				//数据库中已经有图片了，但是没上传新的图片
				foreach($img_not_null as $q_index => $q_item){
					if($q_item['qid']==$qids[$ak]){
						$file_path = $q_item['img_src'];
						break;
					}
				}
			}
			else	//上传了新的图片，需要先把原来的图片删除掉。
			{
				//上传文件名: ["name"]	文件类型:["type"]	文件大小:["size"]/1024(kb)	文件临时存储的位置: ["tmp_name"]
				$allowedExts = array("gif", "jpeg", "jpg", "png");
				$temp = explode(".", $file["name"]);
				$extension = end($temp);        // 获取文件后缀名
				if (( ($file["type"] == "image/gif")
				|| ($file["type"] == "image/jpeg")
				|| ($file["type"] == "image/jpg")
				|| ($file["type"] == "image/pjpeg")
				|| ($file["type"] == "image/x-png")
				|| ($file["type"] == "image/png"))
				// && ($file["size"] < 204800)    // 小于 200 kb
				&& in_array($extension, $allowedExts))
				{
					//删除原来的照片	$img_not_null在answers中取出img_src不为空的项
					foreach($img_not_null as $q_index => $q_item){
						if($q_item['qid']==$qids[$ak]){	//只删除改题目对应的那一项
							unlink($q_item['img_src']);	//文件=$q_item['img_src'] 绝对路径
							break;
						}
					}
					$extension_len = strlen($extension) + 1;
					$filename_len = strlen($file["name"]);
					$fix_name = substr($file["name"],0,$filename_len-$extension_len);
					$file_name = $file["name"];
					$num = 1;
					// 判断当期目录下的 upload 目录是否存在该文件
					while(file_exists("./upload/".$file_name)){
						$file_name = $fix_name.$num.'.'.$extension;
						$num++;
					}
					// upload 目录不存在该文件（命名后）则将文件上传到 upload 目录下
					move_uploaded_file($file["tmp_name"], "./upload/".$file_name);
					$file_path = "./upload/".$file_name;

				}
				else
				{
					write_file('./application/logs/log.txt',"非法的文件格式\n",'a+');
				}	
			}

			$attempted=0;
			$marks=0;
			$qid=$qids[$ak];
			foreach($answer as $sk => $ansval){
				//如果用户的答案只写在了图片中
				if($ansval == '' && $file_path!=null){
					$ansval = 'The answer is in the picture';
				}

				if($ansval != ''){
					$userdata=array(
					'rid'=>$rid,
					'qid'=>$qid,
					'uid'=>$uid,
					'q_option'=>$ansval,
					'score_u'=>0,
					'img_src'=>$file_path
					);
					$this->db->insert('savsoft_answers',$userdata);
					$attempted=1;
				}
			}
			if($attempted==1){
				
				$correct_incorrect[$ak]=3;							
				
			}else{
				$correct_incorrect[$ak]=0;
			}
		 }
		 
		 // match
		if($_POST['question_type'][$ak] == '5'){
			$qid=$qids[$ak];
			$query=$this->db->query(" select * from savsoft_options where qid='$qid' ");
			$options_data=$query->result_array();
			$noptions=array();
			foreach($options_data as $op => $option){
				$noptions[]=$option['q_option'].'___'.$option['q_option_match'];				
			}
			$marks=0;
			$attempted=0;
			foreach($answer as $sk => $ansval){
				if($ansval != '0'){
					$mc=0;
					if(in_array($ansval,$noptions)){
						$marks+=1/count($options_data);
						$mc=1/count($options_data);
					}else{
						$marks+=0;
						$mc=0;
					}
					$userdata=array(
					'rid'=>$rid,
					'qid'=>$qid,
					'uid'=>$uid,
					'q_option'=>$ansval,
					'score_u'=>$mc
					);
					$this->db->insert('savsoft_answers',$userdata);
					$attempted=1;
				}
			}
			if($attempted==1){
				if($marks==1){
					$correct_incorrect[$ak]=1;	
				}else{
					$correct_incorrect[$ak]=2;							
				}
			}else{
				$correct_incorrect[$ak]=0;
			}
		}
		 

		// cloze
		if($_POST['question_type'][$ak] == '6'){
			$qid=$qids[$ak];
			$query=$this->db->query(" select * from savsoft_options where qid='$qid' ");
			$options_data=$query->result_array();
			$noptions=array();	//每道题的正确答案
			foreach($options_data as $op => $option){
				$all_options = explode(',',$option['q_option_match_option']);
				$noptions[]=$option['q_option'].'___'.$all_options[$option['q_option_match']];		//1___deer,2___dog,3___goes,4___dark...	
			}
			// echo $noptions;
			
			$marks=0;
			$attempted=0;
			for($n=1;$n<=count($options_data);$n++){
				//$answer[$n]自选项的值
				if($answer[$n] != '0'){
					if(in_array($answer[$n],$noptions)){	//正确
						$marks += 1/count($options_data);
						$userdata=array(
							'rid'=>$rid,
							'qid'=>$qid,
							'uid'=>$uid,
							'q_option'=>$answer[$n],
							'score_u'=>1/count($options_data)
						);
					}else{	//错误
						$marks += 0;
						$userdata=array(
							'rid'=>$rid,
							'qid'=>$qid,
							'uid'=>$uid,
							'q_option'=>$answer[$n],
							'score_u'=>0
						);
					}
					$this->db->insert('savsoft_answers',$userdata);
					$attempted=1;
				}
			}

			$marks = $marks.'';
			if($attempted==1){
				if($marks=='1'){
					$correct_incorrect[$ak]=1;
				}elseif($marks=='0'){
					$correct_incorrect[$ak]=2;
				}else{
					// $correct_incorrect[$ak]=4;		//注意！！4的话代表是完形填空的不完全对的情况
					$correct_incorrect[$ak] = '4-'.$marks;
					// if(explode('-',$correct_incorrect[$ak])[1]=='1'){
					// 	$correct_incorrect[$ak]=1;
					// }
				}
			}else{
				$correct_incorrect[$ak]=0;
			}
		}
		 
		 
		 
		 
		 
		 
		 
	 }
	 
	 $userdata=array(
	 'score_individual'=>implode(',',$correct_incorrect),
	 'individual_time'=>$_POST['individual_time'],
	 
	 );
	 $this->db->where('rid',$rid);
	 $this->db->update('savsoft_result',$userdata);
	 
	 return true;
	 
 }
 
 

 function wx_insert_answer(){	//保存答案 并且 自动计算得分
	$rid=$_POST['rid'];
   $srid=$this->session->userdata('rid');
   if(!$this->session->userdata('logged_in')){
	   $logged_in=$this->session->userdata('logged_in_raw');
   }else{
	   $logged_in=$this->session->userdata('logged_in');
   }
   $uid=$logged_in['uid'];
   if($srid != $rid){

   return "Something wrong";
   }

   $query=$this->db->query("select * from savsoft_result join savsoft_quiz on savsoft_result.quid=savsoft_quiz.quid where savsoft_result.rid='$rid' "); 
   $quiz=$query->row_array(); 
   $correct_score=$quiz['correct_score'];
   $incorrect_score=$quiz['incorrect_score'];
   $qids=explode(',',$quiz['r_qids']);
   $vqids=$quiz['r_qids'];
   $correct_incorrect=explode(',',$quiz['score_individual']);
   
	//获取在数据库中原有的图片 不覆盖
	$this->db->where('rid',$rid);
	$this->db->where('img_src !=',null);	//找到img_src不为空的项
	$q1 = $this->db->get('savsoft_answers');
	$img_not_null = $q1->result_array();

   // remove existing answers
   $this->db->where('rid',$rid);
   $this->db->delete('savsoft_answers');	//不断在answer表中更新数据
   
	foreach($_POST['answer'] as $ak => $answer){	//$answer = 前端的answer[qk],qk在试卷中对应的题目顺序，从0开始
	   //$_POST['question_type'][$ak]->value: 1-单选 2-多选 3-short 4-long 5-match 6-cloze  

		// multiple choice single answer
		if($_POST['question_type'][$ak] == '1'){
			
			$qid=$qids[$ak];	//$ak在试卷中对应的题目顺序，从0开始，得到对应的qid
			$query=$this->db->query(" select * from savsoft_options where qid='$qid' ");
			$options_data=$query->result_array();	//多个结果
			$options=array();
			foreach($options_data as $ok => $option){
				$options[$option['oid']]=$option['score'];	//oid  options表中的oid
			}
			$attempted=0;
			$marks=0;

		   foreach($answer as $sk => $ansval){//多选时要foreach TODO:多选时，若正确个数比错误的多1个则有分？？

			   if($options[$ansval] <= 0 ){	//$ansval=前端的radio/checkbox的value为oid
				   $marks+=-1;	
			   }else{
				   $marks+=$options[$ansval];
			   }
			   $userdata=array(
			   'rid'=>$rid,
			   'qid'=>$qid,
			   'uid'=>$uid,
			   'q_option'=>$ansval,
			   'score_u'=>$options[$ansval]
			   );

			   $this->db->insert('savsoft_answers',$userdata);
			   $attempted=1;	
		   }
		   if($attempted==1){
			   if($marks >= '0.99' ){
			   $correct_incorrect[$ak]=1;	//正确
			   }else{
			   $correct_incorrect[$ak]=2;	//错误					
			   }
		   }else{
			   $correct_incorrect[$ak]=0;
		   }
		}

		// multiple choice multiple answer
		if($_POST['question_type'][$ak] == '2'){
			
			$qid=$qids[$ak];	//$ak在试卷中对应的题目顺序，从0开始，得到对应的qid
			$query=$this->db->query(" select * from savsoft_options where qid='$qid' ");
			$options_data=$query->result_array();	//多个结果
			$options=array();
			foreach($options_data as $ok => $option){
				$options[$option['oid']]=$option['score'];	//oid  options表中的oid
			}
			$attempted=0;
			$marks=0;


		   $ans_str = $answer[0];	//'86,87'
		   $ans_arr = explode(',',$ans_str);	//[86,87,99]
		   
		   foreach($ans_arr as $sk => $ansval){//多选时要foreach TODO:多选时，若正确个数比错误的多1个则有分？？

			   if($options[$ansval] <= 0 ){	//$ansval=前端的radio/checkbox的value为oid
				   $marks+=-1;	
			   }else{
				   $marks+=$options[$ansval];
			   }
			   $userdata=array(
			   'rid'=>$rid,
			   'qid'=>$qid,
			   'uid'=>$uid,
			   'q_option'=>$ansval,
			   'score_u'=>$options[$ansval]
			   );

			//    write_file('./application/logs/log.txt','answer（表）信息"\n"'.var_export($userdata,true)."\n\n",'a+');

			   $this->db->insert('savsoft_answers',$userdata);
			   $attempted=1;	
		   }
		   if($attempted==1){
			   if($marks >= '0.99' ){
			   $correct_incorrect[$ak]=1;	//正确
			   }else{
			   $correct_incorrect[$ak]=2;	//错误					
			   }
		   }else{
			   $correct_incorrect[$ak]=0;
		   }
		}

		// short answer
		if($_POST['question_type'][$ak] == '3'){
			
			$qid=$qids[$ak];
			$query=$this->db->query(" select * from savsoft_options where qid='$qid' ");
			$options_data=$query->row_array();
			$options_data=explode(',',$options_data['q_option']);
			$noptions=array();
			foreach($options_data as $op){
				$noptions[]=strtoupper(trim($op));	//大写
			}
			
			$attempted=0;
			$marks=0;
			   foreach($answer as $sk => $ansval){
				   if($ansval != ''){
					   if(in_array(strtoupper(trim($ansval)),$noptions)){
					   $marks=1;	
				   }else{
					   $marks=0;
				   }
				   
			   $attempted=1;

				   $userdata=array(
				   'rid'=>$rid,
				   'qid'=>$qid,
				   'uid'=>$uid,
				   'q_option'=>$ansval,
				   'score_u'=>$marks
				   );
				   $this->db->insert('savsoft_answers',$userdata);

			   }
			   }
			   if($attempted==1){
				   if($marks==1){
				   $correct_incorrect[$ak]=1;	
				   }else{
				   $correct_incorrect[$ak]=2;							
				   }
			   }else{
				   $correct_incorrect[$ak]=0;
			   }
		}
		
		// long answer
		if($_POST['question_type'][$ak] == '4'){
			$qid=$qids[$ak];
			// img_not_null
			$file_path = null;
			foreach($img_not_null as $ik => $iv){
				if($iv['qid']==$qid){
					$file_path = $iv['img_src'];
					break;
				}
			}
			$attempted=0;
			$marks=0;
			foreach($answer as $sk => $ansval){
				//如果用户的答案只写在了图片中
				if($ansval == '' && $file_path!=null){
					$ansval = 'The answer is in the picture';
				}

				if($ansval != ''){
					$userdata=array(
					'rid'=>$rid,
					'qid'=>$qid,
					'uid'=>$uid,
					'q_option'=>$ansval,
					'score_u'=>0,
					'img_src'=>$file_path
					);
					$this->db->insert('savsoft_answers',$userdata);
					$attempted=1;
				}
			}
			if($attempted==1){
				
				$correct_incorrect[$ak]=3;							
				
			}else{
				$correct_incorrect[$ak]=0;
			}
		}
		
		// match
	   if($_POST['question_type'][$ak] == '5'){
		   $qid=$qids[$ak];
		   $query=$this->db->query(" select * from savsoft_options where qid='$qid' ");
		   $options_data=$query->result_array();
		   $noptions=array();
		   foreach($options_data as $op => $option){
			   $noptions[]=$option['q_option'].'___'.$option['q_option_match'];				
		   }
		   $marks=0;
		   $attempted=0;
		   foreach($answer as $sk => $ansval){
			   if($ansval != '0'){
				   $mc=0;
				   if(in_array($ansval,$noptions)){
					   $marks+=1/count($options_data);
					   $mc=1/count($options_data);
				   }else{
					   $marks+=0;
					   $mc=0;
				   }
				   $userdata=array(
				   'rid'=>$rid,
				   'qid'=>$qid,
				   'uid'=>$uid,
				   'q_option'=>$ansval,
				   'score_u'=>$mc
				   );
				   $this->db->insert('savsoft_answers',$userdata);
				   $attempted=1;
			   }
		   }
		   if($attempted==1){
			   if($marks==1){
				   $correct_incorrect[$ak]=1;	
			   }else{
				   $correct_incorrect[$ak]=2;							
			   }
		   }else{
			   $correct_incorrect[$ak]=0;
		   }
	   }
		

	   // cloze
	   if($_POST['question_type'][$ak] == '6'){
		   $qid=$qids[$ak];
		   $query=$this->db->query(" select * from savsoft_options where qid='$qid' ");
		   $options_data=$query->result_array();
		   $noptions=array();	//每道题的正确答案
		   foreach($options_data as $op => $option){
			   $all_options = explode(',',$option['q_option_match_option']);
			   $noptions[]=$option['q_option'].'___'.$all_options[$option['q_option_match']];		//1___deer,2___dog,3___goes,4___dark...	
		   }
		   // echo $noptions;
		   
		   $marks=0;
		   $attempted=0;
		   for($n=1;$n<=count($options_data);$n++){
			   //$answer[$n]自选项的值
			   if($answer[$n] != '0'){
				   if(in_array($answer[$n],$noptions)){	//正确
					   $marks += 1/count($options_data);
					   $userdata=array(
						   'rid'=>$rid,
						   'qid'=>$qid,
						   'uid'=>$uid,
						   'q_option'=>$answer[$n],
						   'score_u'=>1/count($options_data)
					   );
				   }else{	//错误
					   $marks += 0;
					   $userdata=array(
						   'rid'=>$rid,
						   'qid'=>$qid,
						   'uid'=>$uid,
						   'q_option'=>$answer[$n],
						   'score_u'=>0
					   );
				   }
				   $this->db->insert('savsoft_answers',$userdata);
				   $attempted=1;
			   }
		   }

		   $marks = $marks.'';
		   if($attempted==1){
			   if($marks=='1'){
				   $correct_incorrect[$ak]=1;
			   }elseif($marks=='0'){
				   $correct_incorrect[$ak]=2;
			   }else{
				   // $correct_incorrect[$ak]=4;		//注意！！4的话代表是完形填空的不完全对的情况
				   $correct_incorrect[$ak] = '4-'.$marks;
				   // if(explode('-',$correct_incorrect[$ak])[1]=='1'){
				   // 	$correct_incorrect[$ak]=1;
				   // }
			   }
		   }else{
			   $correct_incorrect[$ak]=0;
		   }
	   }
		
		
		
		
		
		
		
	}
	
	$userdata=array(
	'score_individual'=>implode(',',$correct_incorrect),
	'individual_time'=>$_POST['individual_time'],
	
	);
	$this->db->where('rid',$rid);
	$this->db->update('savsoft_result',$userdata);
	
	return true;
	
}



function wx_upload_img(){
	if(!$this->session->userdata('logged_in')){
		$logged_in=$this->session->userdata('logged_in_raw');
	}else{
		$logged_in=$this->session->userdata('logged_in');
	}
	$uid=$logged_in['uid'];
	$rid = intval($_POST['rid']);
	$qid = intval($_POST['qid']);
	$qk = intval($_POST['qk']);

	//删除原来的图片  有重新上传的才删
	$this->db->where('rid',$rid);
	$this->db->where('qid',$qid);
	$q1 = $this->db->get('savsoft_answers');
	$db_num = $q1->num_rows();	//0无数据 1-有数据
	$db_data = $q1->row_array();

	$file = $_FILES['file'];
	$file_path = null;
	if($file['error']>0 || count($_FILES) == 0){	//sth wrong 4-空
		write_file('./application/logs/log.txt',"没有上传文件\n",'a+');
		return false;
	}
	else	//上传了新的图片，需要先把原来的图片删除掉。
	{
		//上传文件名: ["name"]	文件类型:["type"]	文件大小:["size"]/1024(kb)	文件临时存储的位置: ["tmp_name"]
		$allowedExts = array("gif", "jpeg", "jpg", "png");
		$temp = explode(".", $file["name"]);
		$extension = end($temp);        // 获取文件后缀名
		if (( ($file["type"] == "image/gif")
		|| ($file["type"] == "image/jpeg")
		|| ($file["type"] == "image/jpg")
		|| ($file["type"] == "image/pjpeg")
		|| ($file["type"] == "image/x-png")
		|| ($file["type"] == "image/png"))
		// && ($file["size"] < 204800)    // 小于 200 kb
		&& in_array($extension, $allowedExts))
		{

			if($db_num == 0){//学生暂未作答 需要插入一条新的数据

				$extension_len = strlen($extension) + 1;
				$filename_len = strlen($file["name"]);
				$fix_name = substr($file["name"],0,$filename_len-$extension_len);
				$file_name = $file["name"];
				$num = 1;
				// 判断当期目录下的 upload 目录是否存在该文件
				while(file_exists("./upload/".$file_name)){
					$file_name = $fix_name.$num.'.'.$extension;
					$num++;
				}
				// upload 目录不存在该文件（命名后）则将文件上传到 upload 目录下
				move_uploaded_file($file["tmp_name"], "./upload/".$file_name);
				$file_path = "./upload/".$file_name;
				
				$userdata=array(
					'rid'=>$rid,
					'qid'=>$qid,
					'uid'=>$uid,
					'q_option'=>'The answer is in the picture',
					'score_u'=>0,
					'img_src'=>$file_path
				);
				$this->db->insert('savsoft_answers',$userdata);
				//做题状态更改为3
				$this->db->where('rid',$rid);
				$q2 = $this->db->get('savsoft_result');
				$new_score_ind = explode(',',$q2->row_array()['score_individual']);
				$new_score_ind[$qk] = 3;
				$scoredata=array(
					'score_individual'=>implode(',',$new_score_ind)
				);
				$this->db->where('rid',$rid);
				$this->db->update('savsoft_result',$scoredata);


			}else{
			
				//删除原来的照片
				unlink($db_data['img_src']);	//文件=['img_src'] 绝对路径

				$extension_len = strlen($extension) + 1;
				$filename_len = strlen($file["name"]);
				$fix_name = substr($file["name"],0,$filename_len-$extension_len);
				$file_name = $file["name"];
				$num = 1;
				// 判断当期目录下的 upload 目录是否存在该文件
				while(file_exists("./upload/".$file_name)){
					$file_name = $fix_name.$num.'.'.$extension;
					$num++;
				}
				// upload 目录不存在该文件（命名后）则将文件上传到 upload 目录下
				move_uploaded_file($file["tmp_name"], "./upload/".$file_name);
				$file_path = "./upload/".$file_name;

				$userdata=array(
					'score_u'=>0,
					'img_src'=>$file_path
				);
				$this->db->where('rid',$rid);
				$this->db->where('qid',$qid);
				// $this->db->replace('savsoft_answers',$userdata);
				$this->db->update('savsoft_answers',$userdata);

			}
			return true;

		}
		else
		{
			write_file('./application/logs/log.txt',"非法的文件格式\n",'a+');
			return false;
		}	
	}

}

 
 
 function set_ind_time(){
	$rid=$this->session->userdata('rid');

	 $userdata=array(
	 'individual_time'=>$_POST['individual_time'],
	 
	 );
	 //TODO：网页与手机不能共存啊
	 // 打印日志 方便查看
	// $this->load->helper('file');
	// write_file('./application/logs/log.txt',var_export($this->input->user_agent(),true)."\n",'a+');
	// write_file('./application/logs/log.txt',var_export($this->input->ip_address(),true)."\n",'a+');
	// write_file('./application/logs/log.txt',var_export($_POST['individual_time'],true)."\n\n",'a+');

	 $this->db->where('rid',$rid);
	 $this->db->update('savsoft_result',$userdata);
	 
	 return true;
 }


 function view_uploaded(){
	$rid = $_POST['rid'];
	$qid = $_POST['qid'];

	$this->db->where('rid',$rid);
	$this->db->where('qid',$qid);
	$query = $this->db->get('savsoft_answers');
	$data = $query->row_array();

	if($data['img_src']){
		return $data['img_src'];
	}else{
		return '';
	}
 }
 
 
}
?>
