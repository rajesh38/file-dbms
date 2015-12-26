<?php
	if(!isset($_SESSION))
		session_start();
	$db_selected=$_REQUEST['db_selected'];
  $tab_selected=$_REQUEST['tab_selected'];
	$arr=file("databases/$_SESSION[uname]/$db_selected/$tab_selected"."_constraints.knrs");
  $pk_attr=substr($arr[0], strlen("PK->"));
	echo json_encode(array('pk_attr'=>$pk_attr));
?>