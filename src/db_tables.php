<html>
<head>
	<style type="text/css">
		body
		{
			overflow-x:hidden;
			font-family:lucida fax;
		}
	</style>
</head>
  <body bgcolor="Moccasin">
<?php
	if(!isset($_SESSION))
		session_start();
	$path = "databases/$_SESSION[uname]";
	if(!isset($_REQUEST['db_selected']) || $_REQUEST['db_selected']==NULL)
		$db_selected = $_SESSION['db_selected'];
	else
	{
		$db_selected = $_REQUEST['db_selected'];
		$_SESSION['db_selected']=$db_selected;
	}
	echo "<h4><u>DATABASE : <font size=5 color=victor style=\"word-wrap: break-word\">".$db_selected."</font></u></h4>";
	if(@filesize($path."/".$db_selected."/table_list.knrs")==0)
	{
		die("<i><font size=3 face='lucida fax'>No table in the database...</font></i>");
	}
	$table_list = file($path."/".$db_selected."/table_list.knrs");
	echo "<table id='tab_list_tab' width=40% cellpadding=5 style=\"table-layout:fixed\">";
	foreach($table_list as $tab)
	{
		$tab=substr($tab,0,strlen($tab)-1);
		if(isset($_SESSION['tab_selected']) && $_SESSION['tab_selected']!=NULL)
		{
			//As file is read into an array, each array element is terminated with '\n'. Hence it is appended with the session variable.
			if($_SESSION['tab_selected']==$tab)
			{
				$a_style="background-color:white;";
			}
			else
				$a_style="";
			echo "<tr>
					<td align=right><img src='images/table_icon3.png' style=\"max-width:100%; max-height:100%\">
					<td width=70% style=\"word-wrap: break-word;\"><a id=".$tab." style=\"width:100%; height:100%; text-decoration:none; display:block;".$a_style."\" onclick=\"var anchor_tags = document.getElementById('tab_list_tab').getElementsByTagName('a'); for( var i = 0, len = anchor_tags.length; i < len; i++ ){anchor_tags[i].style['background-color']='Moccasin'}; this.style['background-color']='white';\" href='default_right_container_right.php?tab_selected=".$tab."' target='right_container_right'>".$tab."</a></tr>";
		}
		else
			echo "<tr>
					<td align=right><img src='images/table_icon3.png' style=\"max-width:100%; max-height:100%\">
					<td width=70% style=\"word-wrap: break-word;\"><a id=".$tab." style=\"width:100%; height:90%; text-decoration:none; display:block\" onclick=\"var anchor_tags = document.getElementById('tab_list_tab').getElementsByTagName('a'); for( var i = 0, len = anchor_tags.length; i < len; i++ ){anchor_tags[i].style['background-color']='Moccasin'}; this.style['background-color']='white';\" href='default_right_container_right.php?tab_selected=".$tab."' target='right_container_right'>".$tab."</a></tr>";
	}
	echo "</table>";
?>
  </body>
</html>