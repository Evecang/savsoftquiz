 
function remove_entry(redir_cont){		//删除时，添加一个弹窗再次confirm
	
	if(confirm("Do you really want to remove entry?")){
		window.location=base_url+"index.php/"+redir_cont;
	}
	
}



function updategroup(vall,gid){
	 
	var formData = {group_name:vall};
	$.ajax({
		 type: "POST",
		 data : formData,
			url: base_url + "index.php/user/update_group/"+gid,
		success: function(data){
		$("#message").html(data);
			
			},
		error: function(xhr,status,strErr){
			//alert(status);
			}	
		});
	
}

function updategroupprice(vall,gid){
	 
	var formData = {price:vall};
	$.ajax({
		 type: "POST",
		 data : formData,
			url: base_url + "index.php/user/update_group/"+gid,
		success: function(data){
		$("#message").html(data);
			
			},
		error: function(xhr,status,strErr){
			//alert(status);
			}	
		});
	
}


function updategroupvalid(vall,gid){
	 
	var formData = {valid_day:vall};
	$.ajax({
		 type: "POST",
		 data : formData,
			url: base_url + "index.php/user/update_group/"+gid,
		success: function(data){
		$("#message").html(data);
			
			},
		error: function(xhr,status,strErr){
			//alert(status);
			}	
		});
	
}



function updatecategory(vall,cid){
	 
	var formData = {category_name:vall};
	$.ajax({
		 type: "POST",
		 data : formData,
			url: base_url + "index.php/qbank/update_category/"+cid,
		success: function(data){
		$("#message").html(data);
			
			},
		error: function(xhr,status,strErr){
			//alert(status);
			}	
		});
	
}


//在添加新用户时，选择组别时的onchange函数，gid为对应的输入框
function getexpiry(){
	var gid=document.getElementById('gid').value;	//选择组别的下拉框
	var formData = {gid:gid};
	$.ajax({
		 type: "POST",
		 data : formData,
		 url: base_url + "index.php/user/get_expiry/"+gid,
		success: function(data){
		$("#subscription_expired").val(data);	//更改过期时间
			
			},
		error: function(xhr,status,strErr){
			//alert(status);
			}	
		});
	
}


function updatelevel(vall,lid){
	 
	var formData = {level_name:vall};
	$.ajax({
		 type: "POST",
		 data : formData,
			url: base_url + "index.php/qbank/update_level/"+lid,
		success: function(data){
		$("#message").html(data);
			
			},
		error: function(xhr,status,strErr){
			//alert(status);
			}	
		});
	
}



function hidenop(vall){	//在添加问题时，如果是单选、多选、匹配题则可以让用户设置option个数 现在多添加一下6==完形填空
	if(vall == '1' || vall=='2' || vall=='3' || vall=='6'){
		$("#nop").css('display','block');
		//如果是完形填空 选项默认为10
		if(document.getElementsByName("question_type")[0].selectedIndex == 5){
			document.getElementsByName("nop")[0].value = "10";	
		}
	}else{
	$("#nop").css('display','none');
	}
}



function addquestion(quid,qid){
	 var did='#q'+qid;
	var formData = {quid:quid};
	$.ajax({
		 type: "POST",
		 data : formData,
			url: base_url + "index.php/quiz/add_qid/"+quid+'/'+qid,
		success: function(data){
		$(did).html(document.getElementById('added').value);
			
			},
		error: function(xhr,status,strErr){
			//alert(status);
			}	
		});
	
}





 
var position_type="Up";
var global_quid="0";
var global_qid="0";
var global_opos="0";

function cancelmove(position_t,quid,qid,opos){	//submit_quiz
save_answer(qn);	//保存答案并且 自动计算得分
position_type=position_t;
global_quid=quid;
global_qid=qid;
global_opos=opos;

if((document.getElementById('warning_div').style.display)=="block"){	//再次config的时候 取消显示
	document.getElementById('warning_div').style.display="none";
}else{
	document.getElementById('warning_div').style.display="block";	//显示提交提示框
	if(position_type=="Up"){
		var upos=parseInt(global_opos)-parseInt(1);	//-1
	}else{
		var upos=parseInt(global_opos)+parseInt(1);	//+1
	}
	document.getElementById('qposition').value=upos;

}

}


function movequestion(){

var pos=document.getElementById('qposition').value;

if(position_type=="Up"){
var npos=parseInt(global_opos)-parseInt(pos);
window.location=base_url+"index.php/quiz/up_question/"+global_quid+"/"+global_qid+"/"+npos;
}else{
var npos=parseInt(pos)-parseInt(global_opos);
window.location=base_url+"index.php/quiz/down_question/"+global_quid+"/"+global_qid+"/"+npos;
}
}



function no_q_available(lid){
	var cid=document.getElementById('cid').value;
	
		var formData = {cid:cid};
	$.ajax({
		 type: "POST",
		 data : formData,
			url: base_url + "index.php/quiz/no_q_available/"+cid+'/'+lid,
		success: function(data){
		$('#no_q_available').html(data);
			
			},
		error: function(xhr,status,strErr){
			//alert(status);
			}	
		});
}




// quiz attempt functions 测试中的参数

var noq=0;	//quiz中的noq
var qn=0;
var lqn=0;	//last qn

function fide_all_question(){
	
	for(var i=0; i < noq; i++){
		
		var did="#q"+i;	//包括每个问题的最外边的框
	$(did).css('display','none');
	}
}


function show_question(vqn){
	change_color(vqn);
	fide_all_question();
	var did="#q"+vqn;
	$(did).css('display','block');
	// hide show next back btn     vqn从0开始
	if(vqn >= 1){	//第一题之后 显示‘返回’
	$('#backbtn').css('visibility','visible');
	}
	
	if(vqn < noq){	//不是最后一题的话 显示‘下一题’
	$('#nextbtn').css('visibility','visible');
	}
	if((parseInt(vqn)+1) == noq){	//最后一题 隐藏‘下一题’
	  
	$('#nextbtn').css('visibility','hidden');
	}
	if(vqn == 0){	//第一题 隐藏‘返回’
	$('#backbtn').css('visibility','hidden');
	}
	
	// last qn
	qn=vqn;
lqn=vqn;
setIndividual_time(lqn);
save_answer(lqn);
	
}

function show_next_question(){

	if((parseInt(qn)+1) < noq){	//不为最后一题
		fide_all_question();
		qn=(parseInt(qn)+1);
		var did="#q"+qn;
		$(did).css('display','block');	//显示下一题
	}
	// hide show next back btn
	if(qn >= 1){
	$('#backbtn').css('visibility','visible');
	}
	if((parseInt(qn)+1) == noq){
	$('#nextbtn').css('visibility','hidden');
	}
	// console.log("lqn is:"+lqn); 	//比如我在第一题中点击了下一题（激发这个函数），lqn是0,但是此时qn变为1了
	change_color(lqn);
	setIndividual_time(lqn);
	save_answer(lqn);
	
	// last qn
	lqn=qn;	
		
}
function show_back_question(){
	
	if((parseInt(qn)-1) >= 0 ){	//不为第一题
		fide_all_question();	//所有的题目都display:none
		qn=(parseInt(qn)-1);	//返回上一题
		var did="#q"+qn;
		$(did).css('display','block');	//显示上一题
	}
	// hide show next back btn
	if(qn < noq){
	$('#nextbtn').css('visibility','visible');
	}
	if(qn == 0){
	$('#backbtn').css('visibility','hidden');
	}
	change_color(lqn);
	setIndividual_time(lqn);
	save_answer(lqn);	//保存答案，并自动计算某一些值
	
	// last qn
	lqn=qn;	
		
}


function change_color(qn){
	var did='#qbtn'+qn;	//右上角的题目号
	var q_type='#q_type'+lqn;	//代表是哪种题，隐藏
	
	// if not answered then make red
	// alert($(did).css('backgroundColor'));
	if($(did).css('backgroundColor') != 'rgb(68, 157, 68)' && $(did).css('backgroundColor') != 'rgb(236, 151, 31)'){	//绿色answered&橘黄色review later
	$(did).css('backgroundColor','#c9302c');	//红色 -> unanswered
	$(did).css('color','#ffffff');
	}
	
	// answered make green
	if(lqn >= '0' && $(did).css('backgroundColor') != 'rgb(236, 151, 31)'){	//！=橘黄
	var ldid='#qbtn'+lqn;
		
		if($(q_type).val()=='1' || $(q_type).val()=='2'){
			var green=0;
			for(var k=0; k<=10; k++){
				var answer_value="answer_value"+lqn+'-'+k;
				if(document.getElementById(answer_value)){
					if(document.getElementById(answer_value).checked == true){	
					green=1;
					}
				}
			}

			if(green==1){			
				$(ldid).css('backgroundColor','#449d44');	//绿色
				$(ldid).css('color','#ffffff');	
			}		
		}		
 		
		if($(q_type).val()=='3' || $(q_type).val()=='4'){
			var answer_value="#answer_value"+lqn;
			if($(answer_value).val()!=''){			
				$(ldid).css('backgroundColor','#449d44');
				$(ldid).css('color','#ffffff');	
			}
		}		
 		
		if($(q_type).val()=='5'){
			var green=0;
			for(var k=0; k<=10; k++){
				var answer_value="answer_value"+lqn+'-'+k;
				if(document.getElementById(answer_value)){
					if(document.getElementById(answer_value).value != '0'){	
					green=1;
					}
				}
			}
			if(green==1){			
			$(ldid).css('backgroundColor','#449d44');
			$(ldid).css('color','#ffffff');	
			}		
		}		

		if($(q_type).val()=='6'){	//cloze
			var green=0,n = 1;
			var answer_value = "answer_value"+lqn+'-'+n+'-';

			while(document.getElementById(answer_value+'0')){

				for(var j=0;j<4;j++){
					if(document.getElementById(answer_value+j).checked == true){	
						green++;
						break;
					}
				}
				if(green < n){
					break;
				}
				n++;
				answer_value = "answer_value"+lqn+'-'+n+'-';

			}
			if(!document.getElementById(answer_value+'0')){
				n--;
			}
			if(green==n){			
			$(ldid).css('backgroundColor','#449d44');
			$(ldid).css('color','#ffffff');	
			}		
		}		

		
		
	}
	
}


// clear radio btn response
function clear_response(){	//测试中 底部按钮：Clear  ->  主要清空答案
var q_type='#q_type'+qn;	//判断当前处于那种类型的题目
		
		if($(q_type).val()=='1' || $(q_type).val()=='2'){	//1-单选 2-多选
		 
		for(var k=0; k<=10; k++){
			var answer_value="answer_value"+lqn+'-'+k;
			
			if(document.getElementById(answer_value)){
				
				if(document.getElementById(answer_value).checked == true){
					
				document.getElementById(answer_value).checked=false;
				}
			}
		}
	 		
		}	
		
		if($(q_type).val()=='3' || $(q_type).val()=='4'){	//3-填空 4-计算
		var answer_value="answer_value"+qn;
		document.getElementById(answer_value).value='';	//清空
		}	
		
		
		
		if($(q_type).val()=='5'){	//5-匹配
			 
			for(var k=0; k<=10; k++){
				var answer_value="answer_value"+qn+'-'+k;
				if(document.getElementById(answer_value)){
					if(document.getElementById(answer_value).value != '0'){	
					document.getElementById(answer_value).value='0';
					}
				}
			}
		 		
		}		
		
		if($(q_type).val()=='6'){	//6-完形
			 
			for(var n=1; n<=20; n++){

				for(var j=0;j<4;j++){
					var answer_value="answer_value"+qn+'-'+n+'-'+j;
					// console.log(answer_value);
					if(document.getElementById(answer_value)){
						if(document.getElementById(answer_value).checked == true){	
							document.getElementById(answer_value).checked = false;
						}
					}
				}

			}
		}		

	var did='#qbtn'+qn;	//右边的题号
	$(did).css('backgroundColor','#c9302c');
	$(did).css('color','#ffffff');
}

var review_later;	//数组
function review_later(){	//在考试中 底部按钮：Review Later -> 主要是更改右边题号的颜色
	
 
	if(review_later[qn] && review_later[qn]){	//qn在232行，是测试中一个参数，题号，[0,noq)
	
		review_later[qn]=0;
		var did='#qbtn'+qn;	//did为右边col-ms-4的 题号
	$(did).css('backgroundColor','#c9302c');//红色 ->unanswered
			$(did).css('color','#ffffff');	
	}else{
		
		review_later[qn]=1;
	var did='#qbtn'+qn;
	$(did).css('backgroundColor','#ec971f');//橘黄色 -> review later
	$(did).css('color','#ffffff');
	}
	
}




function save_answer(qn){
	
								// signal 1
							$('#save_answer_signal1').css('backgroundColor','#00ff00');	//绿色
								setTimeout(function(){
							$('#save_answer_signal1').css('backgroundColor','#666666');		//黑色
								},5000);
										//serialize()对于file文件类型的input框并不适用
										// var str = $( "form" ).serialize();	//jq中的方法，serialize() 方法通过序列化表单值，创建 URL 编码文本字符串。
										
							var form = $("#quiz_form");
							var formData = new FormData(form[0]);
 
						// var formData = {user_answer:str};
						$.ajax({
							 type: "POST",
							 data : formData,
							 async: false,
							 cache: false,
							url: base_url + "index.php/quiz/save_answer/",
							contentType:false,
							processData: false,
							success: function(data){
							// signal 1
							$('#save_answer_signal2').css('backgroundColor','#00ff00');
								setTimeout(function(){
							$('#save_answer_signal2').css('backgroundColor','#666666');		
								},5000);
								
								},
							error: function(xhr,status,strErr){
								//alert(status);
								
							// signal 1
							$('#save_answer_signal2').css('backgroundColor','#ff0000');
								setTimeout(function(){
							$('#save_answer_signal2').css('backgroundColor','#666666');		
								},5500);

								}	
							});
	 		
		 
	
}

 
function setIndividual_time(cqn){
	if(cqn==undefined || cqn == null ){
		var cqn='0';
	}
		  if(cqn=='0'){
		ind_time[qn]=parseInt(ind_time[qn])+parseInt(ctime);	
		 
		  }else{
			  
			ind_time[cqn]=parseInt(ind_time[cqn])+parseInt(ctime);	
		  
		  }
	
	ctime=0;
	  
	 document.getElementById('individual_time').value=ind_time.toString();
	 
	 var iid=document.getElementById('individual_time').value;
	 
	 	 
	var formData = {individual_time:iid};
	$.ajax({
		 type: "POST",
		 data : formData,
			url: base_url + "index.php/quiz/set_ind_time",
		success: function(data){
	 	
			},
		error: function(xhr,status,strErr){
			//alert(status);
			}	
		});
		
}




function submit_quiz(){
	save_answer(qn);
	setIndividual_time(qn);
	$('#processing').html("Processing...<br>");
	setTimeout(function(){
	window.location=base_url+"index.php/quiz/submit_quiz/";
	},3000);
}



function switch_category(c_k){
	
	var did=document.getElementById(c_k).value;
	show_question(did);
	
}


function count_char(answer,span_id){
	var chcount=answer.split(' ').length;
	if(answer == ''){
		chcount=0;
	}
	document.getElementById(span_id).innerHTML=chcount; //统计字数
	
}



function sort_result(limit,val){	//在结果查询中 根据status筛选结果
	window.location=base_url+"index.php/result/index/"+limit+"/"+val;
	
}


function assign_score(rid,qno,score){	//提交 long answer的分数 score-1正确 -2错误
	 var evaluate_warning=	document.getElementById('evaluate_warning').value;
	 if(confirm(evaluate_warning)){
	var formData = {rid:rid};
	$.ajax({
		 type: "POST",
		 data : formData,
			url: base_url + "index.php/quiz/assign_score/"+rid+'/'+qno+'/'+score,
		success: function(data){
	 	var did="#assign_score"+qno;
		$(did).css('display','none');
			},
		error: function(xhr,status,strErr){
			//alert(status);
			}	
		});	
	 }
}


//展示问题的简要描述状态，如果被打开则关闭，如果关闭则打开
function show_question_stat(id){
	var did="#stat-"+id;
	 
	if($(did).css('display')=='none'){
		$(did).css('display','block');
	}else{
		$(did).css('display','none');
	}
	 
}
 

// end - quiz attempt functions 









// start classroom




function postclass_content(id){
var cont=document.getElementById('page').innerHTML;
var formData = {content:cont};
document.getElementById('page_res').innerHTML="Sending data...";
	$.ajax({
		type: "POST",
	    data : formData,
		url: base_url + "index.php/liveclass/insert_content/"+id,
		success: function(data){
				var d = new Date();
		var dt=d.toString();
		var gt=dt.replace("GMT+0530 (India Standard Time)","");
		document.getElementById('page_res').innerHTML="Sent : "+gt;

		},
		error: function(xhr,status,strErr){
			document.getElementById('page_res').innerHTML="Sending failed!";
			}	
		});

}


function get_liveclass_content(id){

	$.ajax({
		url: base_url + "index.php/liveclass/get_class_content/"+id,
		success: function(data){
		var d = new Date();
var dt=d.toString();
var gt=dt.replace("GMT+0530 (India Standard Time)","");
document.getElementById('page').innerHTML=data;
		document.getElementById('page_res').innerHTML="Last updated on "+gt;
setTimeout(function(){
get_liveclass_content(id);
},5000);
		},
		error: function(xhr,status,strErr){
setTimeout(function(){
get_liveclass_content(id);
},5000);
			}	
		});
		
	document.getElementById("page").scrollTop = document.getElementById("page").scrollHeight;
	
}



function get_liveclass_content_2(id){

	$.ajax({
		url: base_url + "index.php/liveclass/get_class_content/"+id,
		success: function(data){
		var d = new Date();
var dt=d.toString();
var gt=dt.replace("GMT+0530 (India Standard Time)","");
document.getElementById('page').innerHTML=data;
		document.getElementById('page_res').innerHTML="Last updated on "+gt;

		},
		error: function(xhr,status,strErr){
setTimeout(function(){
get_liveclass_content(id);
},5000);
			}	
		});
		
	document.getElementById("page").scrollTop = document.getElementById("page").scrollHeight;
	
}



var class_id;
function get_ques_content(id){
class_id=id;
	$.ajax({
		url: base_url + "index.php/liveclass/get_ques_content/"+id,
		success: function(data){
		//alert(data);
		document.getElementById('comment_box').innerHTML=data;
		
setTimeout(function(){
get_ques_content(id);
},5000);
		},
		error: function(xhr,status,strErr){
setTimeout(function(){
get_ques_content(id);
},5000);
			}	
		});
		document.getElementById("comment_box").scrollTop = document.getElementById("comment_box").scrollHeight;

}

function get_ques_content_2(id){
class_id=id;
	$.ajax({
		url: base_url + "index.php/liveclass/get_ques_content/"+id,
		success: function(data){
		//alert(data);
		document.getElementById('comment_box').innerHTML=data;
		

		},
		error: function(xhr,status,strErr){
setTimeout(function(){
get_ques_content(id);
},5000);
			}	
		});
		document.getElementById("comment_box").scrollTop = document.getElementById("comment_box").scrollHeight;

}


function comment(id){
var comnt=document.getElementById('comment_send').value;

var formData = {content:comnt};
document.getElementById('comment_send').value="Sending data...";
	$.ajax({
		type: "POST",
	    data : formData,
		url: base_url + "index.php/liveclass/insert_comnt/"+id,
		success: function(data){
				document.getElementById('comment_send').value="";
		},
		error: function(xhr,status,strErr){
			document.getElementById('comment_send').innerHTML="Sending failed!";
			}	
		});

}


var publish="0";
 function show_options(id,p){
comnt_id=id;
publish=p;
if(publish=="0"){
document.getElementById('pub').innerHTML="Unpublish";
}else{
document.getElementById('pub').innerHTML="Publish";

}
$("#comnt_optn").fadeIn();

}
function hide_options(){
$("#comnt_optn").fadeOut();
}
 
  function publish_comment(){

	var formData = {id:comnt_id,pub:publish};
	$.ajax({
		type: "POST",
	    data : formData,
		url: base_url + "index.php/liveclass/publish_comnt/",
		success: function(data){
				$("#comnt_optn").fadeOut();
				 get_ques_content(class_id);
		},
		});
 
 
 }
 
 function delete_comment(){
 //alert(comnt_id);
	var formData = {id:comnt_id};
	$.ajax({
		type: "POST",
	    data : formData,
		url: base_url + "index.php/liveclass/del_comnt/",
		success: function(data){
				$("#comnt_optn").fadeOut();
				 get_ques_content(class_id);
		},
		});
 
 
 }

// end classroom







 // version check 打开dashboard时会出现的提示
function update_check(sq_version){
	 
	var formData = {sq_version:sq_version};
	$.ajax({
		 type: "POST",
		 data : formData,
			url: "http://update.savsoftquiz.com/",
		success: function(data){
			if(data.trim()==sq_version){
			var msg="<div class='alert alert-success'>You are using updated version of <a href='http://savsoftquiz.com'>Savsoft Quiz "+sq_version+"</a></div>";	
			}else{
			var msg="<div class='alert alert-danger'>New version available: Savsoft Quiz v"+data.trim()+". You are using outdated version of Savsoft Quiz v"+sq_version+". Visit <a href='http://savsoftquiz.com'>www.savsoftquiz.com</a> to download</div>";	
				
			}
			if(!document.getElementById("update_notice")){
				$('body').prepend(msg);
			}else{
		$("#update_notice").html(msg);
			}
			
			},
		error: function(xhr,status,strErr){
			//alert(status);
			}	
		});
	
}

//查看上传的图片
function view_uploaded_img(rid,qid){
	// var formData = {rid:rid,qid:qid};
	$.ajax({
		type: "POST",
		data : {
			rid:rid,
			qid:qid
		},
		url: base_url + "index.php/quiz/view_uploaded_img",
		success: function(data){
			console.log(data);	// ./upload/白猫咪1.jpg
			//如果成功直接让用户下载，如果没有文件，弹窗提示
			if(data!=''){
				file_name = data.split('/')[2];	//文件名 可能包含.
				img_src = base_url + 'upload/' + file_name;
				window.open(img_src);

			}else{
				window.alert('You have not uploaded an image..');
			}
			// signal 1
			$('#save_answer_signal2').css('backgroundColor','#00ff00');
				setTimeout(function(){
			$('#save_answer_signal2').css('backgroundColor','#666666');		
				},5000);
				
		},
		error: function(xhr,status,strErr){
				
			// signal 1
			$('#save_answer_signal2').css('backgroundColor','#ff0000');
				setTimeout(function(){
			$('#save_answer_signal2').css('backgroundColor','#666666');		
				},5500);

		}
	});
}