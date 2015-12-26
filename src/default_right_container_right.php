<html>
<?php
	session_start();
	echo "<frameset rows=\"12%,88%\" border=0>";
	if(isset($_REQUEST['tab_selected']))
	{
		$_SESSION['tab_selected']=$_REQUEST['tab_selected'];
		echo "<frame name=right_container_right_top src=\"table_view_menu.php?tab_selected=".$_SESSION['tab_selected']."\" noresize scrolling=no>";
		echo "<frame name=right_container_right_bottom src=\"table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=data\" noresize scrolling=auto>";
	}
	else
	{
		if(isset($_SESSION['tab_selected']))
		{
			echo "<frame name=right_container_right_top src=\"table_view_menu.php?tab_selected=".$_SESSION['tab_selected']."\" noresize scrolling=no>";
			echo "<frame name=right_container_right_bottom src=\"table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=data\" noresize scrolling=auto>";
		}
		else
		{
			echo "<frame name=right_container_right_top src=\"table_view_menu.php\" noresize scrolling=no>";
			echo "<frame name=right_container_right_bottom src=\"table_action.php\" noresize scrolling=no>";
		}
	}
	echo "</frameset>";
?>
</html>