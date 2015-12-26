<?php
  echo "<script>alert('hi');</script>";//
	$pk_attr=$_REQUEST['pk_attr'];
	$i=$_REQUEST['pk_attr_no'];
	if(!isset($_SESSION))
    session_start();

  $tab_data_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data.knrs";
	$tab_data_file_backup="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data_backup.knrs";
	$tab_update_session_id="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_update_session_id.knrs";
	$tab_reference_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_reference.knrs";

  //No backup has to be kept. Hence commented out
	//keeping backup before DML operation completes, for rollback.
/*
  if(!file_exists($tab_data_file_backup))
	{
		copy($tab_data_file, $tab_data_file_backup);
		file_put_contents($tab_update_session_id, session_id());
	}
*/

	//now deleting the record.
	$record_del_index=0;//this var is used to get the index of the 1st char of the record to be deleted.
	$record_id=0;//record sequence no.
	$record=NULL;
	$record_id_delete=$_REQUEST['record_id_delete'];
	$arr=file($tab_data_file);
	foreach($arr as $line)
	{
		$record_id++;
		if($record_id_delete==$record_id)
		{
			$record=$line;
			break;
		}
		$record_del_index+=strlen($line);
	}
	$fp=fopen($tab_data_file,'r');
	$record_list=fread($fp,filesize($tab_data_file));
	$updated_record_list=substr_replace($record_list, "", $record_del_index, strlen($record));
	fclose($fp);
	$fp=fopen($tab_data_file,'w');
	fwrite($fp,$updated_record_list);
	fclose($fp);

	//now deleting the records from the dependent table(s) having reference to the record being deleted from this parent table.
	if(isset($pk_attr))
	{
		$separater_string="#$**$#";
		while($i!=1)
		{
			$record=substr($record, strlen($separater_string));
			$record=substr($record, strpos($record, $separater_string));
			$i--;
		}
		$record=substr($record, strlen($separater_string));
		$data=substr($record, 0, strpos($record, $separater_string));
		$arr=file($tab_reference_file);
		foreach($arr as $dependent_db_tab_attr)
		{
			//now tracing the dependent attribute names along with the DB and Table
			$dependent_db_tab_attr=substr($dependent_db_tab_attr, strlen("REFERENCE->"));
			$dependent_db=substr($dependent_db_tab_attr, 0, strpos($dependent_db_tab_attr, "."));
			$dependent_db_tab_attr=substr($dependent_db_tab_attr, strpos($dependent_db_tab_attr, ".")+1);
			$dependent_tab=substr($dependent_db_tab_attr, 0, strpos($dependent_db_tab_attr, "."));
			$dependent_db_tab_attr=substr($dependent_db_tab_attr, strpos($dependent_db_tab_attr, ".")+1);
			$dependent_attr=substr($dependent_db_tab_attr, 0, strlen($dependent_db_tab_attr)-1);

			//now modifying the data-file contents of the dependent table
			$dependent_record_arr=array();//each time initializing a new array for each individual dependent table.
			$dependent_tab_data_file="databases/$_SESSION[uname]/$dependent_db/$dependent_tab"."_data.knrs";
			$dependent_tab_data_backup_file="databases/$_SESSION[uname]/$dependent_db/$dependent_tab"."_data_backup.knrs";
			$dependent_tab_schema_file="databases/$_SESSION[uname]/$dependent_db/$dependent_tab"."_schema.knrs";
			//tracing the attribute_no. of the dependent attribute.
			$arr1=file($dependent_tab_schema_file);
			$i=0;//$i is to keep the attribute_no of the dependent attr.
			foreach($arr1 as $attr_schema)
			{
				$i++;
				$attr=substr($attr_schema, 0, strpos($attr_schema, "->"));
				if($attr==$dependent_attr)
					break;//reached the dependent attr schema in the schema file.
			}
			$arr1=file($dependent_tab_data_file);
			foreach($arr1 as $dependent_record)
			{
        $dependent_record_copy=$dependent_record;
				$i1=$i;
				while($i1!==1)
				{
					$record=substr($dependent_record, strlen($separater_string));
					$record=substr($dependent_record, strpos($dependent_record, $separater_string));
					$i1--;
				}
				$dependent_record=substr($dependent_record, strlen($separater_string));
				$dependent_data=substr($dependent_record, 0, strpos($dependent_record, $separater_string));
				if($dependent_data==$data)
				{
					$dependent_record_arr[]=$dependent_record_copy;
          //echo "<script>alert('".str_replace("\n", "", $dependent_record_copy)."');</script>";//
				}
			}
			$dependent_tab_data_file_content=file_get_contents($dependent_tab_data_file);
			foreach($dependent_record_arr as $dependent_record)
			{
				$dependent_tab_data_file_content=str_replace($dependent_record, "", $dependent_tab_data_file_content);
			}
			file_put_contents($dependent_tab_data_file, $dependent_tab_data_file_content);
			unset($dependent_record_arr);
			if(file_exists($dependent_tab_data_backup_file))
			{
				$dependent_backup_record_arr=array();
				$arr1=file($dependent_tab_data_backup_file);
				foreach($arr1 as $dependent_backup_record)
				{
          $dependent_backup_record_copy=$dependent_backup_record;
					$i1=$i;
					while($i1!==1)
					{
						$dependent_backup_record=substr($dependent_backup_record, strlen($separater_string));
						$dependent_backup_record=substr($dependent_backup_record, strpos($dependent_backup_record, $separater_string));
						$i1--;
					}
					$dependent_backup_record=substr($dependent_backup_record, strlen($separater_string));
					$dependent_backup_data=substr($dependent_backup_record, 0, strpos($dependent_backup_record, $separater_string));
					if($dependent_backup_data==$data)
					{
						$dependent_backup_record_arr[]=$dependent_backup_record_copy;
            echo "<script>alert('".str_replace("\n", "", $dependent_backup_record_copy)."');</script>";//
					}
				}
				$dependent_tab_data_backup_file_content=file_get_contents($dependent_tab_data_backup_file);
				foreach($dependent_backup_record_arr as $dependent_backup_record)
				{
					$dependent_tab_data_backup_file_content=str_replace($dependent_backup_record, "", $dependent_tab_data_backup_file_content);
				}
				file_put_contents($dependent_tab_data_backup_file, $dependent_tab_data_backup_file_content);
				unset($dependent_backup_record_arr);
			}
		}
	}
	if(file_exists($tab_data_file_backup))
		unlink($tab_data_file_backup);
	if(file_exists($tab_update_session_id))
		unlink($tab_update_session_id);
?>