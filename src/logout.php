<?php
	if(!isset($_SESSION))
		session_start();
  $login_list_file="user_acc_info/login_list.knrs";
  if(strpos(file_get_contents($login_list_file), $_SESSION['uname']."\n")!=0)
    file_put_contents($login_list_file, str_replace("\n".$_SESSION['uname']."\n", "\n", file_get_contents($login_list_file)));
  else
    file_put_contents($login_list_file, str_replace($_SESSION['uname']."\n", "", file_get_contents($login_list_file)));
	session_destroy();
?>