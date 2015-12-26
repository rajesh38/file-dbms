<?php
	session_start();
	$tab_data_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data.knrs";
	$tab_data_file_backup="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data_backup.knrs";
	$tab_update_session_id="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_update_session_id.knrs";
	copy($tab_data_file_backup, $tab_data_file);
	unlink($tab_data_file_backup);
	unlink($tab_update_session_id);
	echo "<script>this.location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=data';</script>";
?>