<html>
<?php
	//the 'if' block is invoked on the 1st occurance of the home-page or when not using the URL get-data-sending method.
	//The 'else' block is invoked when using the URL for sending the selected_DB name.
	session_start();
	if(!isset($_REQUEST['db_selected']))
	{
		if(!isset($_SESSION['db_selected']) || $_SESSION['db_selected']==NULL)
		{
			if(!isset($_SESSION['db_count']))
			{
				$db_parent_dir='databases/$_SESSION[uname]';
				$db_count=count(glob("$db_parent_dir/*"));
				$_SESSION['db_count']=$db_count;
			}
			if($_SESSION['db_count']>0)
				die ("<body bgcolor='aliceBlue'><font face='lucida fax' size=5><b><i><center>No database selected...</center></i></b></font></body>");
			else
				die ("<body bgcolor='aliceBlue'><font face='lucida fax' size=5><b><i><center>No database exists...</center></i></b></font></body>");
		}
		else
		{
			$db_selected=$_SESSION['db_selected'];
		}
	}
	else
	{
		if(isset($_SESSION['tab_selected']))
			$_SESSION['tab_selected']=NULL;
		$db_selected=$_REQUEST['db_selected'];
		$_SESSION['db_selected']=$db_selected;
	}
	echo "<frameset cols='38%,70%'>
			<frameset rows='50%,50%'>
				<frame name=\"right_container_left_top\" src=\"db_tables.php?db_selected=$db_selected\" noresize scrolling=auto>
				<frame name=\"right_container_left_bottom\" src=\"create_table_form.php\" noresize scrolling=auto>
			</frameset>
			<frame name=\"right_container_right\" noresize scrolling=auto src=\"default_right_container_right.php\">
		</frameset>";
?>
</html>