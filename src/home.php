<?php
	if(!isset($_SESSION))
		session_start();
	if(!isset($_SESSION['uname']))
		echo "<script>
				location.href=\"index.php\";
			</script>";
?>

<html>
	<head>
		<script language="javascript">
			window.history.forward(0);

//the following commented out section is to prompt at the time of reloading or exiting this page i.e. 'home.php'.
/*
			window.onbeforeunload = function (e)
			{
				e = e || window.event;
				// For IE and Firefox prior to version 4
				if (e)
				{
					e.returnValue = '';
				}
				// For Safari
				return '';
			};
*/
		</script>
	</head>
	<frameset rows="10%,90%">
		<frame name="db_user_container" id="db_user_container" src="db_user.php" noresize/>
		<frame name="db_content_container" id="db_content_container" src="db_content.php" noresize/>
	</frameset>
</html>