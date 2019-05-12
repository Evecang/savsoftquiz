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

 	function get_quizs($gid){	//_by_gid
		$this->db->like('gids',$gid);
		$query = $this->db->get('savsoft_quiz');

		$like_data = $query->result_array();
		$res = array();
		
		foreach($like_data as $lk => $ldata){	//TODO:可以使用FIND_IN_SET 方法优化 $where="FIND_IN_SET('".$gid."', gids)"; 
			$gids = explode(',',$ldata['gids']);
			if(in_array($gid,$gids)){
				$res[] = $ldata;
			}
		}
		return $res;
	 }

	 function get_group_user($gid){
		 $this->db->where('gid',$gid);
		 $query = $this->db->get('savsoft_users');
		 return $query->result_array();
	 }

	 function get_group_name($gid){
		 $this->db->where('gid',$gid);
		 $query = $this->db->get('savsoft_group');
		 return $query->row_array();
	 }

	 function get_grades_average($quizs,$allUser){
		// 打印日志 方便查看
		// $this->load->helper('file');
		// write_file('./application/logs/log.txt',"get_grades_average\n\n",'a+');

		$grades_average = array();	//存储学期中成绩
		$topper = 0;	//最高分
		foreach($allUser as $uk => $user){
			$uid = $user['uid'];
			$this->db->group_by('quid');
			$this->db->where('uid',$uid);
			$q1 = $this->db->get('savsoft_result');

			
			// write_file('./application/logs/log.txt',"q1_______attempt_number________________________________\n",'a+');
			// write_file('./application/logs/log.txt',var_export($q1->num_rows(),true)."\n\n",'a+');

			$grades_average[$uk] = [];
			$grades_average[$uk]['uid'] = $uid;
			$grades_average[$uk]['user_name'] = $user['first_name']." ".$user['last_name'];
			$grades_average[$uk]['attempt_number'] = $q1->num_rows();	//参加了考试的种数
			$grades_average[$uk]['total_score'] = 0;
			// $grades_average[$uk]['average_score']

			// write_file('./application/logs/log.txt',"部分grades_average数据______________________________________\n",'a+');
			// write_file('./application/logs/log.txt',var_export($grades_average[$uk]['user_name'],true)."————这个学生的名字\n\n",'a+');
			// write_file('./application/logs/log.txt',var_export($grades_average[$uk],true)."————这个学生的部分grades_average数据\n\n",'a+');

			// write_file('./application/logs/log.txt',"***************************************开始便利试卷，得出总成绩***********************\n\n",'a+');
			// write_file('./application/logs/log.txt',var_export($quizs,true)."————这个学生的名字\n\n",'a+');


			foreach($quizs as $qk => $quiz){	//quizs得到了学生参加了哪几种考试
				$quid = $quiz['quid'];	//遍历 试卷的Id	
				$this->db->where('uid',$uid);
				$this->db->where('quid',$quid);
				$this->db->order_by('percentage_obtained','DESC');	//降序 只取第一行
				$q2 = ($this->db->get('savsoft_result'))->row_array();	//注意 有的学生未参与考试 则为NULL
				
				// write_file('./application/logs/log.txt',"查看该学生quid与最高分______________________________________\n",'a+');
				// write_file('./application/logs/log.txt',var_export($q2,true)."————这个学生的查询试卷结果\n\n",'a+');
				// write_file('./application/logs/log.txt',var_export($q2['quid'],true)."————这个学生的试卷quid\n\n",'a+');
				// write_file('./application/logs/log.txt',var_export($q2['percentage_obtained'],true)."————这个学生的最高扽\n\n",'a+');

				if($q2){	//没有数据时 为NULL
					$grades_average[$uk]['total_score'] += $q2['percentage_obtained'];	//累加总分数
				}
			}

			if($grades_average[$uk]['total_score']>$topper) {	//最高分
				$topper = $grades_average[$uk]['total_score'];
			}
			
		}
		

		$scale = $topper/100;
		// write_file('./application/logs/log.txt',var_export($topper,true)."————最高分数\n\n",'a+');
		// write_file('./application/logs/log.txt',var_export($scale,true)."————比例\n\n",'a+');

		// write_file('./application/logs/log.txt',var_export($grades_average,true)."_____________________________________前\n\n",'a+');

		
		foreach($grades_average as $aveK => $ave){	//按照比例折算成 百分数

			// write_file('./application/logs/log.txt',var_export($aveK,true)."                    ____aveK\n\n",'a+');
			// write_file('./application/logs/log.txt',var_export($ave,true)."                       _____ave\n\n",'a+');
			// write_file('./application/logs/log.txt',"________________________\n",'a+');
			// write_file('./application/logs/log.txt',var_export($grades_average[$aveK],true)."_____grades_average[aveK]\n\n",'a+');

			//保留四位小数？
			$ave['average_score'] = $ave['total_score']/$scale;

			$grades_average[$aveK] = $ave;
		}

		//根据字段average_score对数组$data进行降序排列
		$average_score = array_column($grades_average,'average_score');
		array_multisort($average_score,SORT_DESC,$grades_average);

		// write_file('./application/logs/log.txt',var_export($grades_average,true)."________________________________________后\n\n",'a+');

		return $grades_average;

	 }
 
 
 
 
 
 
 
 
 
 
 
 
 

}












?>