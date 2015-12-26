<?php
	if(!isset($_SESSION))
		session_start();
	$db_selected=$_REQUEST['db_selected'];
	$pk_table_array=array();
	$arr=file("databases/$_SESSION[uname]/$db_selected/table_list.knrs");
	foreach($arr as $tab_name)
	{
		if(strpos(file_get_contents("databases/$_SESSION[uname]/$db_selected/".(substr($tab_name, 0, strlen($tab_name)-1))."_constraints.knrs"), "PK->")===0)
			$pk_table_array[]=substr($tab_name, 0, strlen($tab_name)-1);//omitting the last character i.e "\n" from $tab_name by using substr().
	}
	echo json_encode($pk_table_array);
?>