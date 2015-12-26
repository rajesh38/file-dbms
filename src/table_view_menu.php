<html>
	<body bgcolor=AliceBlue>
	<font face='lucida fax'>
	<?php
		session_start();
		if(isset($_SESSION['tab_selected']) && $_SESSION['tab_selected']!=NULL)
			echo "<center>
					<table bgcolor=victor border=0>
						<tr>
							<td><img onclick=\"parent.frames['right_container_right_bottom'].location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=structure';\" style=\"cursor:pointer\" src=\"images/structure1.jpg\" onmouseover=\"this.src='images/structure2.jpg';\" onmouseout=\"this.src='images/structure1.jpg';\">
							<td><img onclick=\"parent.frames['right_container_right_bottom'].location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=data';\" style=\"cursor:pointer\" src=\"images/data1.jpg\" onmouseover=\"this.src='images/data2.jpg';\" onmouseout=\"this.src='images/data1.jpg';\">
							<td><img onclick=\"parent.frames['right_container_right_bottom'].location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=insert';\" style=\"cursor:pointer\" src=\"images/insert1.jpg\" onmouseover=\"this.src='images/insert2.jpg';\" onmouseout=\"this.src='images/insert1.jpg';\">
							<td><img onclick=\"parent.frames['right_container_right_bottom'].location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=constraints';\" style=\"cursor:pointer\" src=\"images/constraints1.jpg\" onmouseover=\"this.src='images/constraints2.jpg';\" onmouseout=\"this.src='images/constraints1.jpg';\">
							<td><img onclick=\"parent.frames['right_container_right_bottom'].location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=truncate';\" style=\"cursor:pointer\" src=\"images/truncate1.jpg\" onmouseover=\"this.src='images/truncate2.jpg';\" onmouseout=\"this.src='images/truncate1.jpg';\">
							<td><img onclick=\"parent.frames['right_container_right_bottom'].location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=drop';\" style=\"cursor:pointer\" src=\"images/drop1.jpg\" onmouseover=\"this.src='images/drop2.jpg';\" onmouseout=\"this.src='images/drop1.jpg';\">
						</tr>
					</table>
					</center>";
		else
		{
			echo "<center><h2><i>No table selected...</i></h2></center>";
		}
	?>
	</font>
	</body>
</html>