<?php
Class Result_model extends CI_Model
{
	
 
 function result_list($limit,$status='0'){	//根据查询条件返回结果列表
	$result_open=$this->lang->line('open');
	$logged_in=$this->session->userdata('logged_in');
	$uid=$logged_in['uid'];
	  
		
	if($this->input->post('search')){	//查询条件
		 $search=$this->input->post('search');
		 $this->db->or_where('savsoft_users.email',$search);
		 $this->db->or_where('savsoft_users.first_name',$search);
		 $this->db->or_where('savsoft_users.last_name',$search);
		 $this->db->or_where('savsoft_users.contact_no',$search);
		 $this->db->or_where('savsoft_result.rid',$search);
		 $this->db->or_where('savsoft_quiz.quiz_name',$search);
 
	 }else{
		 $this->db->where('savsoft_result.result_status !=',$result_open);	//open是正在测试
	 }
	if($logged_in['su']=='0'){	//0-学生 学生只能看到自己的成绩 
		$this->db->where('savsoft_result.uid',$uid);
	}
	
	if($status !='0'){
		$this->db->where('savsoft_result.result_status',$status);	//pending,pass,fail...
	}
		
		
		
		$this->db->limit($this->config->item('number_of_rows'),$limit);	//数量，偏移量
		$this->db->order_by('rid','desc');
		$this->db->join('savsoft_users','savsoft_users.uid=savsoft_result.uid');
		$this->db->join('savsoft_quiz','savsoft_quiz.quid=savsoft_result.quid');
		$query=$this->db->get('savsoft_result');
		return $query->result_array();
		
	 
 }
 
 function quiz_list(){	//返回所有的考试，多行数据
	$this->db->order_by('quid','desc');
	$query=$this->db->get('savsoft_quiz');	
	return $query->result_array();	 
 }
 
 
 function no_attempt($quid,$uid){	//根据quid、uid在result表中得到的数据行数，从而判断参与了几次考试
	 
	$query=$this->db->query(" select * from savsoft_result where uid='$uid' and quid='$quid' ");
		return $query->num_rows(); 
 }
 
 
 function remove_result($rid){	//删除在answer、result表中结果
	 
	 $this->db->where('savsoft_result.rid',$rid);
	 if($this->db->delete('savsoft_result')){
		  $this->db->where('rid',$rid);
		  $this->db->delete('savsoft_answers');
		 return true;
	 }else{
		 
		 return false; 
	 }
	 
	 
	 
 }
 
 
 function generate_report($quid,$gid){	//在查询结果过程中，根据筛选条件从user、group、result、quiz表中得出结果
	$logged_in=$this->session->userdata('logged_in');
	$uid=$logged_in['uid'];
	$date1=$this->input->post('date1');
	 $date2=$this->input->post('date2');
		
		if($quid != '0'){
			$this->db->where('savsoft_result.quid',$quid);
		}
		if($gid != '0'){
			$this->db->where('savsoft_users.gid',$gid);
		}
		if($date1 != ''){
			$this->db->where('savsoft_result.start_time >=',strtotime($date1));
		}
		if($date2 != ''){
			$this->db->where('savsoft_result.start_time <=',strtotime($date2));
		}

	 	$this->db->order_by('rid','desc');
		$this->db->join('savsoft_users','savsoft_users.uid=savsoft_result.uid');
		$this->db->join('savsoft_group','savsoft_group.gid=savsoft_users.gid');
		$this->db->join('savsoft_quiz','savsoft_quiz.quid=savsoft_result.quid');
		$query=$this->db->get('savsoft_result');
		return $query->result_array();
 }
 
 
 
 
 
 function get_result($rid){	//根据rid(result_id)从user、result、group、quiz表中得到结果
	$logged_in=$this->session->userdata('logged_in');
	$uid=$logged_in['uid'];
		if($logged_in['su']=='0'){	//学生
			$this->db->where('savsoft_result.uid',$uid);
		}
		$this->db->where('savsoft_result.rid',$rid);
	 	$this->db->join('savsoft_users','savsoft_users.uid=savsoft_result.uid');
		$this->db->join('savsoft_group','savsoft_group.gid=savsoft_users.gid');
		$this->db->join('savsoft_quiz','savsoft_quiz.quid=savsoft_result.quid');
		$query=$this->db->get('savsoft_result');
		return $query->row_array();
	 
	 
 }
 
 
 function last_ten_result($quid){	//根据quid在user、result、quiz表中得到结果，限制10条数据
		$this->db->order_by('percentage_obtained','desc');
		$this->db->limit(10);		
	 	$this->db->where('savsoft_result.quid',$quid);
	 	$this->db->join('savsoft_users','savsoft_users.uid=savsoft_result.uid'); 
		$this->db->join('savsoft_quiz','savsoft_quiz.quid=savsoft_result.quid');
		$query=$this->db->get('savsoft_result');
		return $query->result_array();
 }
 
 
 
   function get_percentile($quid,$uid,$score){	//返回res：res[0]该试卷的有多少个用户进行了测试，res[1]分数比该用户还<=的人数
  $logged_in =$this->session->userdata('logged_in');
$gid= $logged_in['gid'];
$res=array();
	$this->db->where("savsoft_result.quid",$quid);
	 $this->db->group_by("savsoft_result.uid");
	 $this->db->order_by("savsoft_result.score_obtained",'DESC');
	$query = $this -> db -> get('savsoft_result');
	$res[0]=$query -> num_rows();	//根据quid在result表中查询，但是结果根据uid分类，最高分的在前面（得到每一个用户的最高分），返回行数

	
	$this->db->where("savsoft_result.quid",$quid);
	$this->db->where("savsoft_result.uid !=",$uid);	//其他用户
	$this->db->where("savsoft_result.score_obtained <=",$score);	//有谁的分数比本人还低
	$this->db->group_by("savsoft_result.uid");
	 $this->db->order_by("savsoft_result.score_obtained",'DESC');	//降序
	$querys = $this -> db -> get('savsoft_result');
	$res[1]=$querys -> num_rows();	//返回行数
		
   return $res;
  
  
 }
 
 
 
 
 
 
 
 
 
 
 
 
 

}












?>