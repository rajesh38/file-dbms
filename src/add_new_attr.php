<?php
	if(!isset($_SESSION))
		session_start();
	$attr_name=$_REQUEST['attr_name'];
	$attr_type=$_REQUEST['attr_type'];
	$attr_size=$_REQUEST['attr_size'];

	//these files are to be modified if the attribute is successfully added.
	$tab_data_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data.knrs";
	$tab_schema_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_schema.knrs";

	//these files are to be deleted if the attribute is successfully added.
	$tab_update_session_id="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_update_session_id.knrs";
	$tab_data_file_bak="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data_backup.knrs";

	$arr=file($tab_schema_file);
	foreach($arr as $attr_schema)
	{
		if(strtoupper(substr($attr_schema, 0, strpos($attr_schema, '->')))===strtoupper($attr_name))
		{
			$status=0;//give alert that the new attribute name already exists.
			break;
		}
	}
	if(!isset($status))
	{
		$separator_string="#$**$#";

		//adding the attribute info.
		file_put_contents($tab_schema_file, $attr_name."->".$attr_type."->".$attr_size."\n", FILE_APPEND);

		//making proper format to the data in the table so that it displays the newly introduced attribute properly.
		file_put_contents($tab_data_file, str_replace("\n", $separator_string."\n", file_get_contents($tab_data_file)));

		//deleting the unnecessary files if the structure gets modified.
		if(file_exists($tab_update_session_id))
			unlink($tab_update_session_id);
		if(file_exists($tab_data_file_bak))
			unlink($tab_data_file_bak);

		$status=1;
	}
	echo json_encode(array('status'=>$status));
?>