<html>
<head>
	<style type="text/css">
		body
		{
			overflow-x:hidden;
		}
	</style>
</head>
<body bgcolor=aliceblue>
<?php
	if(isset($_REQUEST['view']))
	{
		echo "<div style=\"\"> <u><b><font size=4>TABLE : <font color=CornflowerBlue style=\"word-wrap:break-word;\">".$_REQUEST['tab_selected']."</font><font size=2 face='lucida fax'>(".$_REQUEST['view'].")</font></font></b></u> </div>";
		if(!isset($_SESSION))
			session_start();
		$_SESSION['view']=$_REQUEST['view'];
	}
	else
		die("");
	if($_REQUEST['view']=='data')
	{
		$tab_data_file_backup="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data_backup.knrs";
		$tab_data_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data.knrs";
		$tab_update_session_id="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_update_session_id.knrs";
		if(file_get_contents("session_info/session_id.knrs")==session_id())
		{
			if(file_exists($tab_data_file_backup))
			{
				if(@file_get_contents($tab_update_session_id)==session_id())
				{
					echo "<table align=right>";
					echo "<tr>";
					echo "<td>";
					echo "<img onclick=\"parent.frames['right_container_right_bottom'].location.href='commit.php';\" style=\"cursor:pointer\" src=\"images/commit1.jpg\" onmouseover=\"this.src='images/commit2.jpg';\" onmouseout=\"this.src='images/commit1.jpg';\">";
					echo "<td>";
					echo "<img onclick=\"parent.frames['right_container_right_bottom'].location.href='rollback.php';\" style=\"cursor:pointer\" src=\"images/rollback1.jpg\" onmouseover=\"this.src='images/rollback2.jpg';\" onmouseout=\"this.src='images/rollback1.jpg';\">";
					echo "</tr>";
					echo "</table>";
					echo "<br>";
					echo "<hr color='red'>";
				}
				else
				{
					copy($tab_data_file_backup, $tab_data_file);
					unlink($tab_data_file_backup);
					@unlink($tab_update_session_id);
					echo "<br><br><br>";
				}
			}
			else
				echo "<br><br>";
		}
		else
		{
			if(file_exists($tab_data_file_backup))
			{
				copy($tab_data_file_backup, $tab_data_file);
				unlink($tab_data_file_backup);
				unlink($tab_update_session_id);
			}
			echo "<br><br><br>";
		}
		if(file_get_contents("session_info/session_id.knrs")!=session_id())
			file_put_contents("session_info/session_id.knrs",session_id());
		
		//remove the line below with the following comment, if not to allow a user to open the page in a new tab for an enlarged view.
		//echo "<div align=right>Have problem in viewing the page?<br><a href=\"#\" target=\"_blank\">Click Here</a><br>Reload the page after coming back.</div>";
		//remove the line above with the comment above, if not to allow a user to open the page in a new tab.
		
		include ("read.php");
	}
	else if($_REQUEST['view']=='structure')
	{
		include("table_structure.php");
	}
	else if($_REQUEST['view']=='insert')
	{
		include ("write_table_data_form.php");
	}
	else if($_REQUEST['view']=='constraints')
	{
		include("table_constraints.php");
	}
	else if($_REQUEST['view']=='truncate')
	{
		include("table_truncate_confirm.php");
	}
	else if($_REQUEST['view']=='drop')
	{
		include("table_drop_confirm.php");
	}
	else
	{
		echo "error";
	}
?>
</body>
</html>