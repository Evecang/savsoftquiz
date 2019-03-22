savsoftquiz
Savsoft Quiz v4.0 is a php based open source web application to create and manage online quiz, test, exam on your website.

Main Features:

1) Support Five types of questions: Multiple choice - Single Answer , Multiple choice - Multiple Answers, Short Answer, Long Answer, and Match the Column

2) Question Bank: Question bank is the question's database, where you can store unlimited questions. you can manage questions by categories (eg. science, history) and difficulty levels (eg. easy, difficult)

3) Capture user photo while attempting quiz

4) Quiz/Test: Create unlimited quiz. You can set Quiz name, Description, Start & end time, Quiz duration, Assign to groups and many more options. please check it in demo.

5) Quiz attempt: A user friendly module available for quiz. user can jump to any question. question loads instantly after click on question number or next/back button. user can change question category wise, mark any question for review later.

6) Result/Report: Result display instantly after submitting quiz. user can view correct answers of questions. Column Chart & Pie Chart for user performance. Administrator can generate CSV report of results by selecting group , quiz name and date range. An email will be sent to user email address with result score.

7) PDF Certificate

8) Mobile Compatible Theme

9) Paid Group with Payment gateway

10) Users/students: Manage users by groups.

Online Demo

URL: http://savsoftquiz.com/savsoftquiz_v4.0/

Administrator login: Username: admin@example.com password: admin

Student login Username: user@example.com password: 123456

Server Requirements:

PHP 5+ One MySql Database (v5+) Linux or Windows server ( Recommend Linux with cPanel hosting) Minimum 35 MB Disk space (web space)

Community Support https://savsoftquiz.com/forum/

Documentation: https://savsoftquiz.com/docs/

Installation: https://savsoftquiz.com/docs/installation.php


If you like Savsoft Quiz and want to help us to keep it free and upgradeable then please get professional installation service at https://savsoftquiz.com/commercial.php

一、页面以及接口路由文档:
index                            默认路由，导到 Login 类的index 函数，登陆页
login/verifylogin                登陆页的登陆按钮
login/pre_registration           用户注册按钮
login/forgot                     忘记密码按钮
user/new_user                    add_new 按钮
user                             list 按钮：



二、重要点:

1) $this->session->userdata('item')   s详见：http://codeigniter.org.cn/user_guide/libraries/sessions.html
在之前的 CodeIgniter 版本中，常规的 session 数据被称之为 'userdata' ，当文档中出现这个词时请记住这一点,即
$this->session->userdata('item') === $this->session->item === $_SESSION['item']
  1、添加 session ：
  $this->session->set_userdata($array);
  $this->session->set_userdata('some_name', 'some_value');
  2、检查某个session值是否存在：
  isset($_SESSION['some_name']);
  $this->session->has_userdata('some_name');
  3、删除 session值
  unset(
    $_SESSION['some_name'],
    $_SESSION['another_name']
  );
  $this->session->unset_userdata('some_name');

2)$logged_in['su']
$logged_in['su'] = 1 表示是管理人员？ 

3) flashdata
CodeIgniter 支持 "flashdata" ，它指的是一种只对下一次请求有效的 session 数据， 之后将会自动被清除。
这用于一次性的信息时特别有用，例如错误或状态信息（诸如 "第二条记录删除成功" 这样的信息）。
userdata() 方法不会返回 flashdata 数据。
如果你要确保你读取的就是 "flashdata" 数据，而不是其他类型的数据，可以使用 flashdata() 方法:
$this->session->flashdata('item');

4)$this->db->where()
1、简单的 key/value 方式:
$this->db->where('name', $name); // Produces: WHERE name = 'Joe'
注意自动为你加上了等号。
如果你多次调用该方法，那么多个 WHERE 条件将会使用 AND 连接起来：
$this->db->where('name', $name);
$this->db->where('title', $title);
$this->db->where('status', $status);
// WHERE name = 'Joe' AND title = 'boss' AND status = 'active'
2、自定义 key/value 方式:
为了控制比较，你可以在第一个参数中包含一个比较运算符：
$this->db->where('name !=', $name);
$this->db->where('id <', $id); // Produces: WHERE name != 'Joe' AND id < 45
3、关联数组方式:
$array = array('name' => $name, 'title' => $title, 'status' => $status);
$this->db->where($array);
// Produces: WHERE name = 'Joe' AND title = 'boss' AND status = 'active'
你也可以在这个方法里包含你自己的比较运算符：
$array = array('name !=' => $name, 'id <' => $id, 'date >' => $date);
$this->db->where($array);
4、自定义字符串:
你可以完全手工编写 WHERE 子句:
$where = "name='Joe' AND status='boss' OR status='active'";
$this->db->where($where);
$this->db->where() 方法有一个可选的第三个参数，如果设置为 FALSE，CodeIgniter 将不保护你的表名和字段名。
$this->db->where('MATCH (field) AGAINST ("value")', NULL, FALSE);

5)$this->db->limit()
$this->db->limit()
该方法用于限制你的查询返回结果的数量:
$this->db->limit(10);  // Produces: LIMIT 10
第二个参数可以用来设置偏移。
// Produces: LIMIT 20, 10 (in MySQL.  Other databases have slightly different syntax)
$this->db->limit(10, 20);

三、CI 项目启动路线以及各个文件作用：
application\config\routes.php : 这里定义了后台路由，$route['default_controller'] = 'login'; 这个默认路由将所有请求页面导向登陆界面 -> Login.php 中的 Login 
application\controllers\Login.php ： 这里定义了登陆页面的页面路由index ，还有登陆界面的许多后台接口，包括 login/verifyLogin 这些接口
application\views\header_login.php  首页（不包括登陆页）的头部组件 <header></header>部分
application\views\login.php  登陆页

register按钮 -> pre_register ->register
js/basic.js  用 JS 写的功能函数，包括发送试卷请求等？
js/calender.js 一个手写时间的函数集合，一个时间组件？
