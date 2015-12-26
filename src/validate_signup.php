<?php
	$uname=$_REQUEST['uname'];
	$pwd=$_REQUEST['pwd'];
  if(!is_dir("user_acc_info"))
    mkdir("user_acc_info");
	$user_acc_file="user_acc_info/user_acc.knrs";
	$fp=fopen($user_acc_file, 'a');
  $user_database_folder="databases/".$uname;
  if(!is_dir($user_database_folder))
    mkdir($user_database_folder);
	if(fwrite($fp, $uname."->".$pwd."\n")===false)
  {
    rmdir($user_database_folder);
		echo json_encode(array('success'=>0));
  }
	else
		echo json_encode(array('success'=>1));
	fclose($fp);
?>