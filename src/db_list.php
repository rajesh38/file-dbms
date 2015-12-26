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
<body bgcolor="FFDADA">
<?php
	if(!isset($_SESSION))
		session_start();
	$db_parent_dir='databases/'.$_SESSION['uname']."/";
	$db_count=count(glob("$db_parent_dir/*"));
	$_SESSION['db_count']=$db_count;
	if($db_count===0)
		die ("<font size=4 face='lucida fax'>No database..</font>");
	else
	{	
		echo "<h3><u>DATABSES($db_count)</u></h3>";
		echo "<table id='db_list_tab' class='my_special_table' width=100% style=\"table-layout:fixed\" cellpadding=5 border=0>";
		$results = scandir($db_parent_dir);
		foreach ($results as $result)
		{
			if ($result === '.' or $result === '..')
				continue;
			if (is_dir($db_parent_dir.'/'.$result))
			{
				if(isset($_SESSION['db_selected']) && $_SESSION['db_selected']!=NULL)
				{
					if($_SESSION['db_selected']==$result)
						$a_style="background-color:white;";
					else
						$a_style="";
					echo "<tr>";
					echo "<td width=20% align=right><img src='images/database_icon1.png' style=\"max-width:100%; max-height:100%\">";
					echo "<td width=40% align=left style=\"word-wrap:break-word;\"><a id=".$result." style=\"width:100%; text-decoration:none; height:90%; display:block;".$a_style."\" onclick=\"var anchor_tags = document.getElementById('db_list_tab').getElementsByTagName('a'); for( var i = 0, len = anchor_tags.length; i < len; i++ ){anchor_tags[i].style['background-color']='FFDADA'}; this.style['background-color']='white';\" href='default_right_container.php?db_selected=".$result."' target=right_container>".$result."</a></td>";
					echo "<td align=center valign=top align=right><font color=red> <div style=\"cursor:pointer\" title=\"Delete database:".$result."\" onclick=\"if(confirm('Are you sure to delete database:".$result."')==false){return false;} else{parent.frames['left_top_container'].location.href='delete_db.php?db_delete=".$result."';}\"><img src='images/delete_object1.jpg' width=40%></div> </font></td>";
					//echo "<td valign=top align=right><font color=red> <div style=\"cursor:pointer\" title=\"Rename database:".$result."\" onclick=\"var new_db_name=null; new_db_name=prompt('New name for database:".$result."'); if(new_db_name=='".$result."') return (false); if(new_db_name.indexOf(' ')>-1){alert('Whitespace is not allowed in the Database name.'); return false;}; if(new_db_name===null || new_db_name===''){return false;} else{location.href='rename_db.php?db_rename=".$result."&new_db_name='+new_db_name;}\"><img src='images/rename_object.jpg' width=80%></div> </font></td>";
					echo "</tr>";
				}
				else
				{
					echo "<tr>";
					echo "<td width=20% align=right><img src='images/database_icon1.png' style=\"max-width:100%; max-height:100%\">";
					echo "<td width=40% align=left style=\"word-wrap: break-word;\"><a id=".$result." style=\"width:100%; text-decoration:none; height:90%; display:block\" onclick=\"var anchor_tags = document.getElementById('db_list_tab').getElementsByTagName('a'); for( var i = 0, len = anchor_tags.length; i < len; i++ ){anchor_tags[i].style['background-color']='FFDADA'}; this.style['background-color']='white';\" href='default_right_container.php?db_selected=".$result."' target=right_container>".$result."</a></td>";
					echo "<td align=center valign=top align=right><font color=red> <div style=\"cursor:pointer\" title=\"Delete database:".$result."\" onclick=\"if(confirm('Are you sure to delete database:".$result."')==false){return false;} else{parent.frames['left_top_container'].location.href='delete_db.php?db_delete=".$result."';}\"><img src='images/delete_object1.jpg' width=40%></div> </font></td>";
					//echo "<td valign=top align=right><font color=red> <div style=\"cursor:pointer\" title=\"Rename database:".$result."\" onclick=\"var new_db_name=null; new_db_name=prompt('New name for database:".$result."'); if(new_db_name=='".$result."') return (false); if(new_db_name.indexOf(' ')>-1){alert('Whitespace is not allowed in the Database name.'); return false;}; if(new_db_name===null || new_db_name===''){return false;} else{location.href='rename_db.php?db_rename=".$result."&new_db_name='+new_db_name;}\"><img src='images/rename_object.jpg' width=80%></div> </font></td>";
					echo "</tr>";
				}
			}
		}
		echo "</table>";
	}
	?>
	</body>
</html>