<html>
<?php
	if(!isset($_SESSION))
		session_start();
	include "create_db_form.php";
	$db_name=$_REQUEST['t1'];
	if(is_dir("databases/$_SESSION[uname]/$db_name"))
	{
		die ("<script language=\"javascript\">alert(\"A database with the same name already exists.\\nTry another name\");</script>");
	}
	else
	{
		if(!is_dir("databases"))
		{
			mkdir("databases");
			mkdir("databases/$_SESSION[uname]");
		}
		mkdir("databases/$_SESSION[uname]/".$db_name) or die("Unable to create the database. Try again");
		$fp=fopen("databases/$_SESSION[uname]/".$db_name."/table_list.knrs",'w');
		fclose($fp);
		$db_parent_dir='databases/$_SESSION[uname]';
		$db_count=count(glob("$db_parent_dir/*"));
		if($db_count==1)
		{
			echo "<script language=\"javascript\">setTimeout('parent.frames[\'right_container\'].location.href=\"default_right_container.php\";', 200);</script>";
		}
	}
?>
</html>