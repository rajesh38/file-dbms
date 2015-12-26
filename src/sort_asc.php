<?php
	if(!isset($_SESSION))
		session_start();
	$attr_no=$_REQUEST['attr_no'];
  $attr_type=$_REQUEST['attr_type'];
	$tab_data_file="databases/$_SESSION[uname]/$_SESSION[db_selected]/$_SESSION[tab_selected]"."_data.knrs";
	$tab_data_file_new="databases/$_SESSION[uname]/$_SESSION[db_selected]/$_SESSION[tab_selected]"."_data_new.knrs";
	$separator_string="#$**$#";
	$len_separator=strlen($separator_string);

	while(file_get_contents($tab_data_file)!=="")
	{
		$arr=file($tab_data_file);
		foreach($arr as $record)
		{
			$record_copy=$record;
			$record=substr($record, $len_separator);
			$i=0;
			while((++$i) < $attr_no)
			{
				$record=substr($record, strpos($record, $separator_string)+$len_separator);
			}

			if(!isset($min))
			{
				$min=substr($record, 0, strpos($record, $separator_string));
				$record_backup=$record_copy;
			}
			else
			{
				if(($attr_type=="char" && (strcasecmp( substr($record, 0, strpos($record, $separator_string)), $min )<0)) || ($attr_type=="number" && (substr($record, 0, strpos($record, $separator_string))<$min)))
				{
					$min=substr($record, 0, strpos($record, $separator_string));
					$record_backup=$record_copy;
				}
			}
		}
		file_put_contents($tab_data_file_new, $record_backup, FILE_APPEND);
		file_put_contents($tab_data_file, substr_replace(file_get_contents($tab_data_file), "", strpos(file_get_contents($tab_data_file), $record_backup), strlen($record_backup)));
		unset($min);
	}
	unlink($tab_data_file);
	rename($tab_data_file_new, $tab_data_file);
?>