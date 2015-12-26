<?php
	if(!isset($_REQUEST['db_rename']) || $_REQUEST['db_rename']==NULL)
	{
		die ("<script language='javascript'>location.href='db_list.php'</script>");
	}
	else
	{
    if(!isset($_SESSION))
		  session_start();
		$old_db_name=$_REQUEST['db_rename'];
		$new_db_name=$_REQUEST['new_db_name'];

		include('db_list.php');

		if(strpos($new_db_name," ")!==FALSE)
			die ("");
		if(is_dir("databases/$_SESSION[uname]/".$new_db_name)==TRUE && strtolower($new_db_name)!=strtolower($old_db_name))
    {
      echo "<script language='javascript'>alert(\"Database : ".$new_db_name." already exists. Rename operation aborted.\");</script>";
			die ("");
    }

		if(@rename("databases/$_SESSION[uname]/".$old_db_name, "databases/$_SESSION[uname]/".$new_db_name)===TRUE)
		{
			if($_SESSION['db_selected']==$old_db_name)
			{
				$_SESSION['db_selected']=$new_db_name;
				echo "<script language='javascript'>location.href='db_list.php'; parent.frames['right_container'].location.href='default_right_container.php?db_selected=".$new_db_name."';</script>";
			}
			else
			{
				echo "<script language='javascript'>location.href='db_list.php'</script>";
			}
		}
		else
		{
			echo "<script language='javascript'>alert('Unable to rename Database : \'".$old_db_name."\' as \'".$new_db_name."\'')</script>";
		}
	}
?>