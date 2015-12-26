<html>
	<head>
		<script language="javascript">
			function drop_tab()
			{
				location.href="table_drop.php";
			}
		</script>
	</head>
	<body bgcolor="aliceblue">
		<center>
		<?php
			if(!isset($_SESSION))
				session_start();
			$tab_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_constraints.knrs";
			$tab_reference_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_reference.knrs";
			echo "<br><font size=4 face='lucida fax'><b>";
			if(filesize($tab_constraints_file)!=0) //i.e. some attribute is set as pk_attr.
			{
				//echo "<script>alert(1);</script>";//
				if(filesize($tab_reference_file)!=0) //i.e. some references are set to the pk_attr of this table.
				{
					//echo "<script>alert(2);</script>";//
					echo "This table is held under relationship by some other table.";
				}
			}
			echo "Are you sure to DROP table:<font color='cornflowerBlue'>".$_SESSION['tab_selected']."</b><font size=4><br><br>";
			echo "<button style=\"width:100px\" onclick=\"location.href='table_drop.php'\">YES</button> <button style=\"width:100px\" onclick=\"location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=data';\">NO</button>";
		?>
		<center>
	</body>
</html>