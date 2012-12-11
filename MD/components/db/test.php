<?php

include('./CDbCommand.php');
include('./Criteria.php');

$com = CDbCommand::getInstance();

// delete
$cri = new Criteria(array('table'=>'users', 
						  'where'=>'id=1', 
						  'limit'=>array(0,10)));
$com->delete($cri);

// update
$cri = new Criteria(array('table'=>'users', 
						  'set'=>array('name'=>'1', 'age'=>2) ,
						  'where'=> array('id'=>array('eq','1'),'_op'=>'OR','time'=>array('lt',22)), 
						  'limit'=>array(0,10)));
$com->update($cri);

// insert
$cri = new Criteria(array('table'=>'users', 
						  'data'=>array('name'=>'t','gender'=>'f','age'=>2)));
$com->insert($cri);

// select
$cri = new Criteria(array(
'field'=>array('u.name','u.id'), 
'table'=>array('users'=>'u'), 
'join'=>'left join question q',
'on'=>'u.id=q.user_id', 
'group' => 'u.id', 
'having'=>'count(q.id) > 2', 
'where' => array('u.id' => array('in', array(1,2,3))), 
'order' => 'u.age desc', 
'limit'=>array(10)));
$com->select($cri);
