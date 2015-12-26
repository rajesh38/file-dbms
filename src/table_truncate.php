<html>
	<head>
		<script>
		</script>
	</head>
  <?php
	if(!isset($_SESSION))
		session_start();

  function removeDir($dir)
  {
	  if(file_exists($dir))
	  {
		  // create directory pointer
		  $dp = opendir($dir) or die ('ERROR: Cannot open directory');
		  // read directory contents
		  // delete files found
		  // call itself recursively if directories found
		  while ($file = readdir($dp))
		  {
			  if ($file != '.' && $file != '..')
			  {
				  if (is_file("$dir/$file"))
				  {
					  unlink("$dir/$file");
				  }
				  else if (is_dir("$dir/$file"))
				  {
					  removeDir("$dir/$file");
				  }
			  }
		  }
		  // close directory pointer
		  closedir($dp);
		  // remove now-empty directory
		  if (rmdir($dir))
		  {
			  return true;
		  }
		  else
		  {
			  return false;
		  }
	  }
  }

	//actual table files.
	$tab_data_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data.knrs";
	$tab_schema_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_schema.knrs";
	$tab_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_constraints.knrs";
	$tab_files_directory="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_files";
	$tab_update_session_id_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_update_session_id.knrs";
	$tab_other_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_other_constraints.knrs";
	$tab_reference_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_reference.knrs";

	//backup of table files.
	$tab_data_file_bak="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data_backup.knrs";
	$tab_schema_file_bak="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_schema_bak.knrs";
	$tab_constraints_file_bak="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_constraints_bak.knrs";

	//TRUNCATE started
	try
	{
		file_put_contents($tab_data_file, "");// Now data_file contails nothing.
		if(file_exists($tab_data_file_bak))
		  unlink($tab_data_file_bak);
		if(file_exists($tab_schema_file_bak))
		  unlink($tab_schema_file_bak);
		if(file_exists($tab_constraints_file_bak))
		  unlink($tab_constraints_file_bak);
		if(file_exists($tab_update_session_id_file))
		  unlink($tab_update_session_id_file);
		if(is_dir($tab_files_directory))
			removeDir($tab_files_directory);
	}
	catch(Exception $e)
	{
		if(file_exists($tab_data_file_bak))
			copy($tab_data_file_bak,$tab_data_file);
		if(file_exists($tab_schema_file_bak))
			copy($tab_schema_file_bak,$tab_schema_file);
		if(file_exists($tab_constraints_file_bak))
			copy($tab_constraints_file_bak,$tab_constraints_file);
		die("<acript language='javascript'>alert('Table:".$_SESSION[tab_selected]." could not be dropped.\nTry again...'); location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=data';</script>");
	}
	//Deletion of three table_files complete.

	echo "<script language='javascript'>/*top.right_container.location.reload();*/parent.parent.location.reload();</script>";
?>
</html>