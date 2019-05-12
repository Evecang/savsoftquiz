<?php
Class Qbank_model extends CI_Model
{
 
  function question_list($limit,$cid='0',$lid='0'){	//
	if($this->input->post('search')){
		$search=$this->input->post('search');
		$this->db->or_where('savsoft_qbank.qid',$search);
		$this->db->or_like('savsoft_qbank.question',$search);
		$this->db->or_like('savsoft_qbank.description',$search);	//WHERE savsoft_qbank.qid = $search OR savsoft_qbank.question LIKE $search OR ...

	}
	if($cid!='0'){
		$this->db->where('savsoft_qbank.cid',$cid);
	}
	if($lid!='0'){
		$this->db->where('savsoft_qbank.lid',$lid);
	}
	$this->db->join('savsoft_category','savsoft_category.cid=savsoft_qbank.cid');	//SELECT savsoft_category.* JOIN savsoft_qbank ON savsoft_category.cid=savsoft_qbank.cid
	$this->db->join('savsoft_level','savsoft_level.lid=savsoft_qbank.lid');
	$this->db->limit($this->config->item('number_of_rows'),$limit);		//limit(n,m)->??m+1?????n????arg1->?????arg2->?????
	$this->db->order_by('savsoft_qbank.qid','desc');
	$query=$this->db->get('savsoft_qbank');		//FROM savsoft_qbank
	return $query->result_array();
		
 }
 
 
 function num_qbank(){
	 
	 $query=$this->db->get('savsoft_qbank');
		return $query->num_rows();
 }
 
 
 
 function get_question($qid){	//根据qid得到相应的问题
	 $this->db->where('qid',$qid);
	 $query=$this->db->get('savsoft_qbank');
	 return $query->row_array();
	 
	 
 }
 function get_option($qid){
	 $this->db->where('qid',$qid);
	 $query=$this->db->get('savsoft_options');
	 return $query->result_array();
	 
	 
 }
 
 function remove_question($qid){	//for all, to remove the question
	 
	 $this->db->where('qid',$qid);
	 if($this->db->delete('savsoft_qbank')){
		$this->db->where('qid',$qid);		//WHERE qid=$qid
		$this->db->delete('savsoft_options');	//DELETE FROM savsoft_options
		

		//
		$qr=$this->db->query("select * from savsoft_quiz where FIND_IN_SET($qid, qids) ");
	 
		foreach($qr->result_array() as $k =>$val){
		
			$quid=$val['quid'];		//
			$qids=explode(',',$val['qids']);	//explode=> split()
			$nqids=array();
			foreach($qids as $qk => $qv){
				if($qv != $qid){
					$nqids[]=$qv;	//$nqids剩余题目的id
				}
			}
			$noq=count($nqids);		
			$nqids=implode(',',$nqids);		//join()
			$this->db->query(" update savsoft_quiz set qids='$nqids', noq='$noq' where quid='$quid' ");	
		}		
		return true;
	 }else{
		return false;
	 }
	
 }
 
 function insert_question_1(){
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('multiple_choice_single_answer'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid'),
	 'analyses'=>$this->input->post('analyses')
	 );
	 $this->db->insert('savsoft_qbank',$userdata);
	 $qid=$this->db->insert_id();
	 foreach($this->input->post('option') as $key => $val){
		if($this->input->post('score')==$key){
			$score=1;
		}else{
			$score=0;
		}
		$userdata=array(
		'q_option'=>$val,
		'qid'=>$qid,
		'score'=>$score,
		);
		$this->db->insert('savsoft_options',$userdata);	 
		 
	 }
	 
	 return true;
	 
 }

 function insert_question_2(){
	 
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('multiple_choice_multiple_answer'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid'),
	 'analyses'=>$this->input->post('analyses')	 
	 );
	 $this->db->insert('savsoft_qbank',$userdata);
	 $qid=$this->db->insert_id();
	 foreach($this->input->post('option') as $key => $val){
		 if(in_array($key,$this->input->post('score'))){
			 $score=(1/count($this->input->post('score')));
		 }else{
			 $score=0;
		 }
	$userdata=array(
	 'q_option'=>$val,
	 'qid'=>$qid,
	 'score'=>$score,
	 );
	 $this->db->insert('savsoft_options',$userdata);	 
		 
	 }
	 
	 return true;
	 
 }
 
 
 function insert_question_3(){	//娣诲姞鍖归厤棰樼洰鐨勬暟鎹�
	 
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('match_the_column'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid'),
	 'analyses'=>$this->input->post('analyses')	 
	 );
	 $this->db->insert('savsoft_qbank',$userdata);
	 $qid=$this->db->insert_id();	//返回插入的id
	 foreach($this->input->post('option') as $key => $val){	//left match
	  $score=(1/count($this->input->post('option')));
	$userdata=array(
	 'q_option'=>$val,
	 'q_option_match'=>$_POST['option2'][$key],	//right match
	 'qid'=>$qid,
	 'score'=>$score,
	 );
	 $this->db->insert('savsoft_options',$userdata);	 
		 
	 }
	 
	 return true;
	 
 }
 
 
 function insert_question_4(){
	 
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('short_answer'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid'),
	 'analyses'=>$this->input->post('analyses')	 
	 );
	 $this->db->insert('savsoft_qbank',$userdata);
	 $qid=$this->db->insert_id();
	 foreach($this->input->post('option') as $key => $val){
	  $score=1;
	$userdata=array(
	 'q_option'=>$val,
	 'qid'=>$qid,
	 'score'=>$score,
	 );
	 $this->db->insert('savsoft_options',$userdata);	 
		 
	 }
	 
	 return true;
	 
 }
 
 
 function insert_question_5(){
	 
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('long_answer'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid'),
	 'analyses'=>$this->input->post('analyses')	 
	 );
	 $this->db->insert('savsoft_qbank',$userdata);
	 $qid=$this->db->insert_id();
	 $userdata=array(
		'q_option'=>$this->input->post('option'),
		'qid'=>$qid,
		);
		$this->db->insert('savsoft_options',$userdata);	 
	 
	 
	 return true;
	 
 }


 function insert_question_6(){	//娣诲姞瀹屽舰濉┖鐨勬暟鎹�
	 
	 
	$userdata=array(
	'question'=>$this->input->post('question'),
	'description'=>$this->input->post('description'),
	'question_type'=>$this->lang->line('cloze_test'),
	'cid'=>$this->input->post('cid'),
	'lid'=>$this->input->post('lid'),
	'analyses'=>$this->input->post('analyses')	 
	);
	$this->db->insert('savsoft_qbank',$userdata);
	$qid=$this->db->insert_id();	//褰撴墽琛� INSERT 璇彞鏃讹紝杩欎釜鏂规硶杩斿洖鏂版彃鍏ヨ鐨処D銆�

	//sub_option1[]銆乻ub_option2[]銆乻ub_option3[]銆乻ub_option4[]鍒嗗埆瀛樺偍瀛愰鐩殑ABCD鐨勯€夐」銆�
	//score$i 鍒嗗埆瀛樺偍鐫€姣忛亾棰樼殑绛旀
	$option = array();	//浜岀淮鏁扮粍,瀛樺偍鎵€鏈夌瓟妗�
	$op1 = $this->input->post('sub_option1');
	$op2 = $this->input->post('sub_option2');
	$op3 = $this->input->post('sub_option3');
	$op4 = $this->input->post('sub_option4');
	$nop = count($this->input->post('sub_option1'));
	for($i=0;$i<$nop;$i++){
		$option[$i][0] = $op1[$i];
		$option[$i][1] = $op2[$i];
		$option[$i][2] = $op3[$i];
		$option[$i][3] = $op4[$i];
	}
	$score =  1/$nop;
	
	//闇€瑕佹彃鍏ュ埌options琛ㄤ腑锛岄渶瑕乹id銆乹_option銆乹_option_match銆乻core銆乹_option_match_option銆�
	
	foreach($option as $key => $val){

		$sub_answer = $this->input->post('score'.($key+1));
		$sub_option = implode($option[$key],',');	//A,B,C,D
		$userdata=array(
			'qid'=>$qid,
			'q_option'=>$key+1,	//瀛愰鍙� 1-nop
			'q_option_match'=>$sub_answer,	//瀛愰鐩殑绛旀 0-A 1-B 2-C 3-D
			'score'=>$score,
			'q_option_match_option'=>$sub_option
		);
		$this->db->insert('savsoft_options',$userdata);	 
		
	}

	
	return true;
	
}
 
 
 
  function update_question_1($qid){
	 
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('multiple_choice_single_answer'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid'),
	 'analyses'=>$this->input->post('analyses')
	 );
	 $this->db->where('qid',$qid);
	 $this->db->update('savsoft_qbank',$userdata);
	 $this->db->where('qid',$qid);
	$this->db->delete('savsoft_options');
	 foreach($this->input->post('option') as $key => $val){
		 
		 
		 if($this->input->post('score')==$key){
			 $score=1;
		 }else{
			 $score=0;
		 }
	$userdata=array(
	 'q_option'=>$val,
	 'qid'=>$qid,
	 'score'=>$score,
	 );
	 $this->db->insert('savsoft_options',$userdata);	 
		 
	 }
	 
	 return true;
	 
 }
 
 
  function update_question_2($qid){
	 
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('multiple_choice_multiple_answer'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid'),
	 'analyses'=>$this->input->post('analyses')	 
	 );
	 $this->db->where('qid',$qid);
	 $this->db->update('savsoft_qbank',$userdata);
	 $this->db->where('qid',$qid);
	$this->db->delete('savsoft_options');
	 foreach($this->input->post('option') as $key => $val){
		 if(in_array($key,$this->input->post('score'))){
			 $score=(1/count($this->input->post('score')));
		 }else{
			 $score=0;
		 }
	$userdata=array(
	 'q_option'=>$val,
	 'qid'=>$qid,
	 'score'=>$score,
	 );
	 $this->db->insert('savsoft_options',$userdata);	 
		 
	 }
	 
	 return true;
	 
 }
 
 
 function update_question_3($qid){
	 
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('match_the_column'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid'),
	 'analyses'=>$this->input->post('analyses')	 
	 );
	 	 $this->db->where('qid',$qid);
	 $this->db->update('savsoft_qbank',$userdata);
	 $this->db->where('qid',$qid);
	$this->db->delete('savsoft_options');
	foreach($this->input->post('option') as $key => $val){
	  $score=(1/count($this->input->post('option')));
	$userdata=array(
	 'q_option'=>$val,
	 'q_option_match'=>$_POST['option2'][$key],
	 'qid'=>$qid,
	 'score'=>$score,
	 );
	 $this->db->insert('savsoft_options',$userdata);	 
		 
	 }
	 
	 return true;
	 
 }

 
 function update_question_4($qid){
	 
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('short_answer'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid'),
	 'analyses'=>$this->input->post('analyses')	 
	 );
		 $this->db->where('qid',$qid);
	 $this->db->update('savsoft_qbank',$userdata);
	 $this->db->where('qid',$qid);
	$this->db->delete('savsoft_options');
  foreach($this->input->post('option') as $key => $val){
	  $score=1;
	$userdata=array(
	 'q_option'=>$val,
	 'qid'=>$qid,
	 'score'=>$score,
	 );
	 $this->db->insert('savsoft_options',$userdata);	 
		 
	 }
	 
	 return true;
	 
 }
 
 
 function update_question_5($qid){
	 
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('long_answer'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid'),
	 'analyses'=>$this->input->post('analyses')	 
	 );
		 $this->db->where('qid',$qid);
	 $this->db->update('savsoft_qbank',$userdata);
	 $this->db->where('qid',$qid);
	$this->db->delete('savsoft_options');
		$userdata=array(
		'q_option'=>$this->input->post('option'),
		'qid'=>$qid,
		);
		$this->db->insert('savsoft_options',$userdata);	 

	 
	 return true;
	 
 }
 

 function update_question_6($qid){
	 
	$userdata=array(
	'question'=>$this->input->post('question'),
	'description'=>$this->input->post('description'),
	'question_type'=>$this->lang->line('cloze_test'),
	'cid'=>$this->input->post('cid'),
	'lid'=>$this->input->post('lid'),
	'analyses'=>$this->input->post('analyses')	 
	);
	$this->db->where('qid',$qid);
	$this->db->update('savsoft_qbank',$userdata);

	$this->db->where('qid',$qid);
	$this->db->delete('savsoft_options');

	$option = array();	//所有的选项集合
	$op1 = $this->input->post('sub_option1');
	$op2 = $this->input->post('sub_option2');
	$op3 = $this->input->post('sub_option3');
	$op4 = $this->input->post('sub_option4');
	$nop = count($this->input->post('sub_option1'));
	
	for($i=0;$i<$nop;$i++){
		$option[$i][0] = $op1[$i];
		$option[$i][1] = $op2[$i];
		$option[$i][2] = $op3[$i];
		$option[$i][3] = $op4[$i];
	}
	$score =  1/$nop;
	
	foreach($option as $key => $val){

		$sub_answer = $this->input->post('score'.$key);
		$sub_option = implode($option[$key],',');	//A,B,C,D
		$userdata=array(
			'qid'=>$qid,
			'q_option'=>$key+1,	//子题号 1-nop
			'q_option_match'=>$sub_answer,	//正确的答案 0-A 1-B 2-C 3-D
			'score'=>$score,
			'q_option_match_option'=>$sub_option
		);
		$this->db->insert('savsoft_options',$userdata);	 
		
	}
	
	return true;
	
}

 
 
 
 // category function start 杩斿洖鎵€鏈夌殑鐩綍
 function category_list(){
	 $this->db->order_by('cid','desc');	//闄嶅簭
	 $query=$this->db->get('savsoft_category');	//SELECT * FROM savsoft_category
	 return $query->result_array();
	 
 }
 
 
 
 
 function update_category($cid){
	 
		$userdata=array(
		'category_name'=>$this->input->post('category_name'),
		 	
		);
	 
		 $this->db->where('cid',$cid);
		if($this->db->update('savsoft_category',$userdata)){
			
			return true;
		}else{
			
			return false;
		}
	 
 }
  
 
 
 function remove_category($cid){
	 
	 $this->db->where('cid',$cid);
	 if($this->db->delete('savsoft_category')){
		 return true;
	 }else{
		 
		 return false;
	 }
	 
	 
 }
 
  
 
 function insert_category(){
	 
	 	$userdata=array(
		'category_name'=>$this->input->post('category_name'),
			);
		
		if($this->db->insert('savsoft_category',$userdata)){
			
			return true;
		}else{
			
			return false;
		}
	 
 }
 
 // category function end
 
 
 

 
 
// level function start 杩斿洖鎵€鏈夌殑闅炬槗绋嬪害
 function level_list(){
	  $query=$this->db->get('savsoft_level');
	 return $query->result_array();
	 
 }
 
 
 
 
 function update_level($lid){
	 
		$userdata=array(
		'level_name'=>$this->input->post('level_name'),
		 	
		);
	 
		 $this->db->where('lid',$lid);
		if($this->db->update('savsoft_level',$userdata)){
			
			return true;
		}else{
			
			return false;
		}
	 
 }
  
 
 
 function remove_level($lid){
	 
	 $this->db->where('lid',$lid);
	 if($this->db->delete('savsoft_level')){
		 return true;
	 }else{
		 
		 return false;
	 }
	 
	 
 }
 
  
 
 function insert_level(){
	 
	 	$userdata=array(
		'level_name'=>$this->input->post('level_name'),
			);
		
		if($this->db->insert('savsoft_level',$userdata)){
			
			return true;
		}else{
			
			return false;
		}
	 
 }
 
 // level function end
 

 
 
 
 //閫氳繃xls鎵归噺鎻掑叆闂
 function import_question($question){	//$question闁哄嫷鍨遍弳鐔虹磼閸曞墎绀夐柣銏㈡附xcel閻炴稏鍔嶉悧鍛婄▔椤撶姵鐣辨慨锝呯箺椤㈡垿寮悧鍫濈ウ缂備礁瀚崹锟�
//echo "<pre>"; print_r($question);exit;
$questioncid=$this->input->post('cid');		//?? category_id
$questiondid=$this->input->post('did');		//lid select_level
foreach($question as $key => $singlequestion){
	//$ques_type= 
	
//echo $ques_type; 

if($key != 0){	//key=0???????
	echo "<pre>";print_r($singlequestion);		//key???????singlequestion???? ???????question?description?corrent_option_number?option1?option2...?
	//??$question?????????[1]?$description???????????[2]
	$question= str_replace('"','&#34;',$singlequestion['1']);

	$question= str_replace("`",'&#39;',$question);
	$question= str_replace("闂傚倷鑳堕崑銊╁磿婵犳碍鏅搁柨鐕傛嫹?",'&#39;',$question);
	$question= str_replace("闂傚倷鑳堕崑銊╁磿婵犳碍鏅搁柨鐕傛嫹?",'&#39;',$question);
	$question= str_replace("闂備浇鍋愰崕銈囨暜閿熺姴鏄ラ柍褜鍓熼弻銊╂偄鐏忎礁浜鹃柣鏇炲€圭€氾拷??",'&#34;',$question);
	$question= str_replace("闂備浇鍋愰崕銈囨暜閿熺姴鏄ラ柍褜鍓熼弻銊╂偄鐏忎礁浜鹃柣鏃堟交缁憋拷",'&#39;',$question);
	$question= str_replace("闂備浇鍋愰崕銈囨暜閿熺姴鏄ラ柍褜鍓熼弻銊╂偄閸撲胶鐓撻梺绯曟櫅鐎氫即寮幘缁樻櫢闁跨噦鎷�?",'&#39;',$question);
	$question= str_replace("闂備浇鍋愰崕銈囨暜閿熺姴鏄ラ柍褜鍓熼弻銊╂偄鐏忎礁浜鹃柣鏂垮鎼达拷",'&#34;',$question);
	$question= str_replace("'","&#39;",$question);
	$question= str_replace("\n","<br>",$question);

	$description= str_replace('"','&#34;',$singlequestion['2']);

	$description= str_replace("'","&#39;",$description);
	$description= str_replace("\n","<br>",$description);

	$ques_type= $singlequestion['0'];	//????

	if($ques_type=="0" || $ques_type==""){
	$question_type=$this->lang->line('multiple_choice_single_answer');	
	}
	if($ques_type=="1"){
	$question_type=$this->lang->line('multiple_choice_multiple_answer');	
	}
	if($ques_type=="2"){
	$question_type=$this->lang->line('match_the_column');	
	}
	if($ques_type=="3"){
	$question_type=$this->lang->line('short_answer');	
	}
	if($ques_type=="4"){
	$question_type=$this->lang->line('long_answer');	
	}


	$insert_data = array(
	'cid' => $questioncid,
	'lid' => $questiondid,
	'question' =>$question,
	'description' => $description,
	'question_type' => $question_type
	);
	
	if($this->db->insert('savsoft_qbank',$insert_data)){
		$qid=$this->db->insert_id();	//??? INSERT ???????????????ID?
		$optionkeycounter = 4;


		//??
		if($ques_type=="0" || $ques_type==""){	
			for($i=1;$i<=10;$i++){	//??10???
				if($singlequestion[$optionkeycounter] != ""){	//$singlequestion ??????$singlequestion[4]??????
					if($singlequestion['3'] == $i){ $correctoption ='1'; }	//???????
					else{ $correctoption = 0; }
					$insert_options = array(
					"qid" =>$qid,
					"q_option" => $singlequestion[$optionkeycounter],	//??????
					"score" => $correctoption
					);
					$this->db->insert("savsoft_options",$insert_options);
					$optionkeycounter++;
				}
				
			}
		}


		//multiple type
		if($ques_type=="1"){
			$correct_options=explode(",",$singlequestion['3']);		//split
			$no_correct=count($correct_options);	//正确答案的个数
			$correctoptionm=array();
			for($i=1;$i<=10;$i++){	//最多10项
				if($singlequestion[$optionkeycounter] != ""){	//答案不为空
					foreach($correct_options as $valueop){
						if($valueop == $i){ $correctoptionm[$i-1] =(1/$no_correct);		//?i????????? 1?/??????
							break;
						}
						else{ $correctoptionm[$i-1] = 0; }
					}
				}
			}
				
			//print_r($correctoptionm);
				
			for($i=1;$i<=10;$i++){
			
				if($singlequestion[$optionkeycounter] != ""){	//$optionkeycounter=4
				
					$insert_options = array(
						"qid" =>$qid,
						"q_option" => $singlequestion[$optionkeycounter],
						"score" => $correctoptionm[$i-1]
					);
					$this->db->insert("savsoft_options",$insert_options);
					$optionkeycounter++;
					
					
					}
				}
		}
		//multiple type end	
	

 		//match Answer
		if($ques_type=="2"){
			$qotion_match=0;	//match连接的个数
			for($j=1;$j<=10;$j++){
			
				if($singlequestion[$optionkeycounter] != ""){
				
					$qotion_match+=1;
					$optionkeycounter++;
				}
				
			}
			///h
			$optionkeycounter=4;	
			for($i=1;$i<=10;$i++){
			
				if($singlequestion[$optionkeycounter] != ""){
					$explode_match=explode('=',$singlequestion[$optionkeycounter]);		//A=B -> [A,B]
					$correctoption =1/$qotion_match;
					$insert_options = array(
						"qid" =>$qid,
						"q_option" =>$explode_match[0] ,
						"q_option_match" =>$explode_match[1] ,
						"score" => $correctoption
					);
					$this->db->insert("savsoft_options",$insert_options);
					$optionkeycounter++;
				}
				
			}
			
		}
		//end match answer
	

		//short Answer
		if($ques_type=="3"){
			for($i=1;$i<=1;$i++){
			
				if($singlequestion[$optionkeycounter] != ""){
					if($singlequestion['3'] == $i){ $correctoption ='1'; }
					$insert_options = array(
						"qid" =>$qid,
						"q_option" => $singlequestion[$optionkeycounter],
						"score" => $correctoption
					);
					$this->db->insert("savsoft_options",$insert_options);
					$optionkeycounter++;
				}
				
			}
			
		}
		//end Short answer


		//TODO 计算题没有答案
		if($ques_type=="4"){
			//optionkeycounter=4，即第一个option
				if($singlequestion[$optionkeycounter] != ""){
					$insert_options = array(
						"qid" =>$qid,
						"q_option" => $singlequestion[$optionkeycounter]
					);
					$this->db->insert("savsoft_options",$insert_options);
				}

		} 

		//TODO cloze_test
	
	
	
	}//END OF: if($this->db->insert('savsoft_qbank',$insert_data))... ???qbank???????????????options???
	

}//END OF:if($key!=0){


}//END OF:foreach($allxlsdata as $key => $singlequestion){



}//END OF:the function import_question($allxlsdata)






 
}











?>
