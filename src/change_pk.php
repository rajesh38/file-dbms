<?php
	function val_exists_array($arr, $val)
	{
		foreach($arr as $current_val)
		{
			if($val===$current_val)
			return true;
		}
		return false;
	}

	if(!isset($_SESSION))
		session_start();
	$new_pk_attr_no=$_REQUEST['attr_no'];
	$tab_data_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data.knrs";
	$tab_schema_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_schema.knrs";
	$tab_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_constraints.knrs";

	//checking whether new_pk_attr is of datatype-file
	$arr=file($tab_schema_file);
	$i=0;
	foreach($arr as $attr_schema)
	{
		$i++;
		if($i==$new_pk_attr_no)
		{
			$new_pk_attr=substr($attr_schema, 0, strpos($attr_schema,"->"));
			$attr_schema=substr($attr_schema, strpos($attr_schema,"->")+2);
			$type=substr($attr_schema, 0, strpos($attr_schema,"->"));
			break;
		}
	}
	if($type!=="file")
	{
		//checking whether the new_pk_attr has duplicate values in the table. If there exist such duplicates then it can't be set the Pk_attr.
		$separator_string="#$**$#";
		$len_separator=strlen($separator_string);
		$arr=file($tab_data_file);
		foreach($arr as $record)
		{
			$record=substr($record, $len_separator);
			$i=0;
			while((++$i) < $new_pk_attr_no)
			{
				$record=substr($record, strpos($record, $separator_string)+$len_separator);
			}
			$attr_data=substr($record, 0, strpos($record, $separator_string));
			if(!isset($new_pk_attr_val_array))
			{
				$new_pk_attr_val_array=array();
				$new_pk_attr_val_array[]=$attr_data;
			}
			else
			{
				if(!val_exists_array($new_pk_attr_val_array, $attr_data) && $attr_data!=="")
				{
					$new_pk_attr_val_array[]=$attr_data;
				}
				else
				{
					unset($new_pk_attr_val_array);
					if($attr_data==="")
						$msg="This attribute contains null value. It cannot be made the PrimaryKey attribute.";
					else
						$msg="This attribute contains some duplicate values. It cannot be a PrimaryKey attribute.";
					$error=true;
					echo json_encode(array('status'=>1, 'msg'=>$msg, 'link'=>"table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=structure"));
					exit();//exit, as a duplicate or null value has been found. Hence no further processing will be done.
				}
			}
		}

		//removing all the records of the current PK_attr.
		if(!isset($error))
		{
			$db_selected=$_SESSION['db_selected'];
			$tab_selected=$_SESSION['tab_selected'];
			//getting the PK_attr
			$tab_constraints_file="databases/$_SESSION[uname]/".$db_selected."/".$tab_selected."_constraints.knrs";
			$arr=file($tab_constraints_file);
			$pk_attr=substr($arr[0], strlen("PK->"));
			//This is the block to remove the references to this current PK_attr.
			$reference_file="databases/$_SESSION[uname]/$db_selected/$tab_selected"."_reference.knrs";
			$arr=file($reference_file);
			foreach($arr as $referenced_by)
			{
				//extracting each (reference_db, reference_db, reference_attr) group.
				$referenced_by=substr($referenced_by, strlen("REFERENCE->"));//now $referenced_by=<DB.TAB.ATTR>\n
				$referenced_by=substr($referenced_by, 0, strlen($referenced_by)-1);//now $referenced_by=<DB.TAB.ATTR>
				$referenced_by_db=substr($referenced_by, 0, strpos($referenced_by, "."));
				$referenced_by=substr($referenced_by, strpos($referenced_by, ".")+1);
				$referenced_by_tab=substr($referenced_by, 0, strpos($referenced_by, "."));
				$referenced_by_attr=substr($referenced_by, strpos($referenced_by, ".")+1);
				//removing the FK-constraints from individual dependent attributes.
				$tab_other_constraints_file="databases/$_SESSION[uname]/".$referenced_by_db."/".$referenced_by_tab."_other_constraints.knrs";
				$arr=file($tab_other_constraints_file);
				$other_constraint_replacable[]=array();
				//taking the FK-constraint-strings associated with this PK_attr in an array from other_constraints_file of individual dependent tables which are dependent on the current PK_attr.
				foreach($arr as $other_constraint)
				{
					if(strpos($other_constraint, "FK->")===0 && strpos($other_constraint, $db_selected.".".$tab_selected.".".$pk_attr)!==FALSE)
					{
						$other_constraint_replacable[]=$other_constraint;
					}
				}
				$other_constraint_file_content=file_get_contents($tab_other_constraints_file);
				foreach($other_constraint_replacable as $replace)
				{
					$other_constraint_file_content=str_replace($replace, "", $other_constraint_file_content);
				}
				//replacing with new constraint-list in the other_constraint_file
				file_put_contents($tab_other_constraints_file, $other_constraint_file_content);
			}
			file_put_contents($reference_file, "");

			//keeping record of the newly set PK_attr in it's own table.
			file_put_contents($tab_constraints_file, "PK->".$new_pk_attr);
			echo json_encode(array('status'=>2, 'link'=>"table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=structure"));
		}
	}
	else//i.e. when new_pk_attr is of file-datatype.
	{
		//Write code for checking existence of reference to duplicate files or null values in file-type attr in the new_pk_attr

		//remove the following js snippet after writing the code.
		$msg="code for making a file type attr the primary key attr is not yet written.";
		echo json_encode(array('status'=>1, 'msg'=>$msg, 'link'=>"table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=structure"));
	}
?>