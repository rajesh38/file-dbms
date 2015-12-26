<?php
	if(!isset($_SESSION))
		session_start();
	$drop_attr_no=$_REQUEST['attribute_id_drop'];
	$pk_true=$_REQUEST['pk_true'];
	$separator_string="#$**$#";
	$len_separator=strlen($separator_string);
	if($pk_true==0)//i.e. the drop_attr is not the PK attr.
	{
		$tab_data_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data.knrs";
		$tab_schema_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_schema.knrs";
		$tab_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_constraints.knrs";
		$tab_update_session_id="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_update_session_id.knrs";
		$tab_data_file_bak="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data_backup.knrs";
		
		//deleting the values of drop_attr from all the records in the table.
		$arr=file($tab_schema_file);
		$no_of_attrs=count($arr);
		if($no_of_attrs==1)
		{
			die("<script>
					alert(\"This attribute is the only attribute in the table. It can't be dropped. You may drop the table if you want.\");
					location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=structure';
				</script>");
		}
		else
		{
			$arr=file($tab_data_file);
			$modified_table_data_file_content="";
			if($drop_attr_no==1)
			{
				foreach($arr as $record)
				{
					$record=substr($record, $len_separator);
					$record=substr($record, strpos($record, $separator_string));
					$modified_table_data_file_content=$modified_table_data_file_content.$record;
				}
			}
			else
			{
				foreach($arr as $record)
				{
					$i=0;
					$modified_table_data_file_content=$modified_table_data_file_content.$separator_string;
					while(++$i<=$no_of_attrs)
					{
						$record=substr($record, strpos($record, $separator_string)+$len_separator);
						if($i!=$drop_attr_no)
							$modified_table_data_file_content=$modified_table_data_file_content.substr($record, 0, strpos($record, $separator_string)+$len_separator);
					}
					$modified_table_data_file_content=$modified_table_data_file_content."\n";
				}
			}
			file_put_contents($tab_data_file, $modified_table_data_file_content);
			
			//Removing the attr being dropped from the schema file
			$arr=file($tab_schema_file);
			$i=0;
			foreach($arr as $attr_schema)
			{
				if(++$i==$drop_attr_no)
					break;
			}
			file_put_contents($tab_schema_file, substr_replace(file_get_contents($tab_schema_file), "", strpos(file_get_contents($tab_schema_file), $attr_schema), strlen($attr_schema)));
			if(file_exists($tab_update_session_id))
				unlink($tab_update_session_id);
			if(file_exists($tab_data_file_bak))
				unlink($tab_data_file_bak);
		}
	}
	else
	{
		//write code for when user is dropping the PK attr. It's to be designed, such that the PK attr can be dropped only when it is not referenced by any other table_attr, but cannot be dropped when being referenced by any other table_attr.
    
    //REMOVE THE FOLLOWING JS SNIPPET BLCK AFTER WRITING THE CODE FOR DROPPING THE PK_ATTR.
    echo "<script>alert(\"Code for dropping the PK is not yet designed.\")</script>";
	}
  echo "<script>location.href=\"table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=structure\"</script>";
?>