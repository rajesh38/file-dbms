<html>
  <body bgcolor="aliceblue">
	<?php
		session_start();
		echo "<u><h3>TABLE : <font color=CornflowerBlue>".$_SESSION['tab_selected']."</font><font size=2 face='lucida fax'>(".$_SESSION['view'].")</font></h3></u>";

		$tab_data_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data.knrs";
		$tab_schema_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_schema.knrs";
		$tab_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_constraints.knrs";
		$tab_other_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_other_constraints.knrs";
		$tab_reference_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_reference.knrs";
		$tab_data_file_backup_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data_backup.knrs";
		$tab_update_session_id_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_update_session_id.knrs";

		$no_attr=$_REQUEST['no_attr'];
		$pk_attr_no=$_REQUEST['pk_attr'];//for getting the attr_no of the pk_attr.
		$record_id_update=$_REQUEST['record_id_update'];
		$current_record_no=0;
		$record_update_index=0;
		$record_update_initial_index=0;
		$substr1=NULL;
		$separater_string="#$**$#";
		$pos1=0;
		$pos2=0;
		$data=NULL;
		$new_record="";
		$new_record_string="";
		if($pk_attr_no!="")
		{
			if($_REQUEST['file_true'.$pk_attr_no]==1)
			{
				if($_FILES["attr".$pk_attr_no]["error"]==4)
				{
					if($_REQUEST['file_current_name'.$pk_attr_no]=="")
						die ("<script>alert('Primary Key cannot be NULL. Updation aborted...'); history.go(-1);</script>");
				}
				else if($_FILES["attr".$pk_attr_no]["error"]>0)
					die ("<script>alert('The upload operation on the file led to some unexpected state. Insertion aborted...'); history.go(-1);</script>");
			}
			else
			{
				if($_REQUEST['attr'.$pk_attr_no]=="")
					die ("<script>alert('Primary Key cannot be NULL. Updation aborted...'); history.go(-1);</script>");
			}
			if(filesize($tab_data_file)>0)
			{
				$arr=file($tab_data_file);
				foreach($arr as $line)
				{
					$current_record_no++;
					$data_no=0;
					$substr1=$line;
					$substr1=substr($substr1,6,strlen($substr1)-6);
					while(($pos2+strlen($separater_string))<=strlen($substr1))
					{
						$data_no++;
						$pos1=strpos($substr1,$separater_string);
						$data=substr($substr1,0,$pos1);
						if($_REQUEST['file_true'.$pk_attr_no]==1)
							$_REQUEST['attr'.$pk_attr_no]=$_FILES['attr'.$pk_attr_no]['name'];
						if($pk_attr_no==$data_no && $data==$_REQUEST['attr'.$pk_attr_no] && $current_record_no!=$record_id_update)
							die ("<script>alert('The Primary Key Attribute cannot have duplicte values. Updation aborted...'); history.go(-1);</script>");
						$substr1=substr($substr1,$pos1+strlen($separater_string),(strlen($substr1)-($pos1+strlen($separater_string))));
						if($substr1=="")
							break;
						$pos2=strpos($substr1,$separater_string);
					}
					if($current_record_no==$record_id_update)
					{
						$record_update_initial_index=$record_update_index;
						$old_record=$line;
					}
					$record_update_index+=strlen($line);
				}
			}
		}
		else
		{
			if(filesize($tab_data_file)>0)
			{
				$arr=file($tab_data_file);
				foreach($arr as $line)
				{
					$current_record_no++;
					if($current_record_no==$record_id_update)
					{
						$record_update_initial_index=$record_update_index;
						$old_record=$line;
					}
					$record_update_index+=strlen($line);
				}
			}
		}

		//Checking if NULL value is entered in any NOT_NULL attr.
		$arr=file($tab_schema_file);
		for($i=1;$i<=$no_attr;$i++)
		{
			$attr=substr($arr[$i-1], 0, strpos($arr[$i-1], "->"));
			if(strpos(file_get_contents($tab_other_constraints_file), "NOT_NULL->".$attr."->true")!==FALSE)
			{
				if($_REQUEST['file_true'.$i]==1)
				{
					if($_FILES['attr'.$i]["error"]==4)
						die ("<script>alert('Attribute:\"".$attr."\" does not allow NULL value. Updation aborted...'); history.go(-1);</script>");
				}
				else
				{
					if($_REQUEST['attr'.$i]=="")
						die ("<script>alert('Attribute:\"".$attr."\" does not allow NULL value. Updation aborted...'); history.go(-1);</script>");
				}
			}
		}

		//Formatting new data for processing in HTML format.
		for($i=1;$i<=$no_attr;$i++)
		{
			if($_REQUEST['file_true'.$i]==1)
			{
				//echo "<script>alert('Upload method.');</script>";//checking
				if ($_FILES["attr".$i]["error"] > 0)
				{
					switch($_FILES['attr'.$i]["error"])
					{
						case 1:
							$err_msg="'The uploaded file exceeds the upload_max_filesize directive in php.ini.";
							break;
						case 2:
							$err_msg="The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
							break;
						case 3:
							$err_msg="The uploaded file was only partially uploaded.";
							break;
						case 4:
							$err_msg="No file was uploaded.";
							break;
						case 5:
							$err_msg="";
							break;
						case 6:
							$err_msg="Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.";
							break;
						case 7:
							$err_msg="Failed to write file to disk. Introduced in PHP 5.1.0.";
							break;
						case 8:
							$err_msg="A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help. Introduced in PHP 5.2.0.";
					}
					if($_FILES['attr'.$i]["error"]!=4)
						die ("<script>alert('Error : ".$err_msg." Try again.'); history.go(-1);</script>");
				}
				else
				{
					//echo "<script>alert('Upload started.');</script>";//checking
					//echo "Upload: " . $_FILES['attr'.$i]["name"] . "<br>";
					//echo "Type: " . $_FILES['attr'.$i]["type"] . "<br>";
					//echo "Size: " . ($_FILES['attr'.$i]["size"] / 1024) . " kB<br>";
					//echo "Stored in: " . $_FILES['attr'.$i]["tmp_name"];
					$file_upload_url="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_files/";
					if(($_FILES['attr'.$i]["size"] / 1024)>$_REQUEST['file_max_size'.$i])
						die("<script>if(confirm(' Upload Aborted. The size of the file being uploaded exceeds the Maximum Size specified in the table schema. Would you like to upload any other file?')==true){history.go(-1)} else{location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=data'};</script>");
					if(!is_dir($file_upload_url))
						mkdir($file_upload_url);
					move_uploaded_file($_FILES['attr'.$i]["tmp_name"], $file_upload_url.$_FILES['attr'.$i]["name"]);
					//echo "<script>alert('Upload Complete.');</script>";//checking
				}
			}
			if($_REQUEST['file_true'.$i]==1)
			{
				if($_FILES["attr".$i]["error"] == 4)
					$new_data=$_REQUEST['file_current_name'.$i];
				else
					$new_data=$_FILES["attr$i"]["name"];
			}
			else
				$new_data=$_REQUEST["attr$i"];
			$offset=0;
			$old="\n";
			$new="<br>";
			$tmpOldStrLength = 2;
			while (($offset = strpos($new_data, $old, $offset)) !== false)
			{
				$new_data = substr_replace($new_data, $new, $offset-1, $tmpOldStrLength);
			}
			$offset = strpos($new_data, $old);
			if($offset!==FALSE)
				$new_data = substr_replace($new_data, $new, $offset, $tmpOldStrLength);
			$new_record_string=$new_record_string.$separater_string.$new_data;
		}
		$new_record_string=$new_record_string.$separater_string;

		//Validating the new record.
		if(strlen($new_record_string)==($no_attr+1)*strlen($separater_string))
			die("<script>alert('Values of all the attributes of a record cannot be NULL. Updation aborted...'); history.go(-1);</script>");
		
		//Writing the new record at its proper place.
		$fp=fopen($tab_data_file,'r');
		$old_tab_data_file_content=fread($fp,filesize($tab_data_file));
		fclose ($fp);

		//keeping backup before DML operation completes, for rollback.
		$tab_data_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data.knrs";
		$tab_data_file_backup="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data_backup.knrs";
		$tab_update_session_id="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_update_session_id.knrs";
		if(!file_exists($tab_data_file_backup))
		{
			copy($tab_data_file, $tab_data_file_backup);
			file_put_contents($tab_update_session_id, session_id());
		}

		$fp=fopen($tab_data_file,'w');
		$new_tab_data_file_content=substr_replace($old_tab_data_file_content, $new_record_string."\n", $record_update_initial_index, strlen($old_record));
		fwrite($fp,$new_tab_data_file_content);
		fclose($fp);

		//if some fk_attr is set to the pk_attr of this table then the necessary changes are to be made to the child_tab's data.knrs and data_backup.knrs.
		if(filesize($tab_reference_file)>0)
		{
			if($_REQUEST["attr".$pk_attr_no]!=$_REQUEST["attr_existing_val".$pk_attr_no])//i.e. if any change is made to the pk_attr then make changes in the child tables.
			{
				$arr=file($tab_reference_file);
				foreach($arr as $reference)//$reference="REFERENCE-><db.tab.attr>\n"
				{
					if(isset($reference_tab_record_array))
						unset($reference_tab_record_array);
					if(isset($reference_tab_record_replacement_array))
						unset($reference_tab_record_replacement_array);
					if(isset($reference_tab_backup_record_array))
						unset($reference_tab_backup_record_array);
					if(isset($reference_tab_backup_record_replacement_array))
						unset($reference_tab_backup_record_replacement_array);

					$reference_tab_record_array=array();//this array will hold those records of the reference tables which refer to the record being updated in the current table.
					$reference=substr($reference, 0, strlen($reference)-1);//$reference="REFERENCE-><db.tab.attr>"
					$reference=substr($reference, strlen("REFERENCE->"));//$reference="<db.tab.attr>"
					$reference_db=substr($reference, 0, strpos($reference, "."));//$reference_db="<db>"
					$reference_tab=substr($reference, strpos($reference, ".")+1, strrpos($reference, ".")-strpos($reference, ".")-1);//$reference_tab="<tab>"
					$reference_attr=substr($reference, strrpos($reference, ".")+1);

					$reference_tab_data_file="databases/$_SESSION[uname]/".$reference_db."/".$reference_tab."_data.knrs";
					$reference_tab_data_backup_file="databases/$_SESSION[uname]/".$reference_db."/".$reference_tab."_data_backup.knrs";
					$reference_tab_schema_file="databases/$_SESSION[uname]/".$reference_db."/".$reference_tab."_schema.knrs";
					$arr1=file($reference_tab_schema_file);
					$i=0;//for taking the attr_no of the fk_attr
					foreach($arr1 as $reference_attr_schema)
					{
						$i++;
						if(strpos($reference_attr_schema, $reference_attr."->")===0)
							break;
					}

					//the following segment is for modifying the reference_tab_data_file
					$arr1=file($reference_tab_data_file);
					foreach($arr1 as $reference_tab_record)
					{
						$reference_tab_record_copy=$reference_tab_record;
						$i1=1;
						while($i1<$i)
						{
							$reference_tab_record=substr($reference_tab_record, strlen($separater_string));
							$reference_tab_record=substr($reference_tab_record, strpos($reference_tab_record, $separater_string));
							$i1++;
						}
						$reference_tab_record=substr($reference_tab_record, strlen($separater_string));
						$current_reference_attr_data=substr($reference_tab_record, 0, strpos($reference_tab_record, $separater_string));
						if($current_reference_attr_data==$_REQUEST["attr_existing_val".$pk_attr_no])
						{
							if(!isset($change))
								$change=true;
							$reference_tab_record_array[]=$reference_tab_record_copy;//putting those records which has the fk_attr-val as the pk_attr-val in the record being updated
						}
					}
					if(isset($reference_tab_record_replacement_array))
						unset($reference_tab_record_replacement_array);
					$reference_tab_record_replacement_array=array();
					foreach($reference_tab_record_array as $record)//this loop is for making another array with the replacement-records for the array elements.
					{
						$i1=1;
						$record_replacement="";
						while($i1<$i)
						{
							$record_replacement=$record_replacement.substr($record, 0, strlen($separater_string));
							$record=substr($record, strlen($separater_string));
							$record_replacement=$record_replacement.substr($record, 0, strpos($record, $separater_string));
							$record=substr($record, strpos($record, $separater_string));
							$i1++;
						}
						echo "<script>alert('record data->".str_replace("\n", "<>", $record)."<-')</script>";//
						$record_replacement=$record_replacement.substr($record, 0, strlen($separater_string));
						$record=substr($record, strlen($separater_string));
						$record_replacement=$record_replacement.$_REQUEST["attr".$pk_attr_no];
						$record=substr($record, strpos($record, $separater_string));
						$record_replacement=$record_replacement.$record;
						$reference_tab_record_replacement_array[]=$record_replacement;//putting the replacement records in the array.
					}
					$reference_tab_data_file_content=file_get_contents($reference_tab_data_file);
					$new_reference_tab_data_file_content=str_replace($reference_tab_record_array, $reference_tab_record_replacement_array, $reference_tab_data_file_content);
					file_put_contents($reference_tab_data_file, $new_reference_tab_data_file_content);

					//the following segment is for modifying the reference_tab_data_backup_file
					if(file_exists($reference_tab_data_backup_file))
					{
						$reference_tab_backup_record_array=array();
						$arr1=file($reference_tab_data_backup_file);
						foreach($arr1 as $reference_tab_backup_record)
						{
							$reference_tab_backup_record_copy=$reference_tab_backup_record;
							$i1=1;
							while($i1<$i)
							{
								$reference_tab_backup_record=substr($reference_tab_backup_record, strlen($separater_string));
								$reference_tab_backup_record=substr($reference_tab_backup_record, strpos($reference_tab_backup_record, $separater_string));
								$i1++;
							}
							$reference_tab_backup_record=substr($reference_tab_backup_record, strlen($separater_string));
							$current_reference_backup_attr_data=substr($reference_tab_backup_record, 0, strpos($reference_tab_backup_record, $separater_string));
							if($current_reference_backup_attr_data==$_REQUEST["attr_existing_val".$pk_attr_no])
							{
								if(!isset($change))
									$change=true;
								$reference_tab_backup_record_array[]=$reference_tab_backup_record_copy;//putting those records which has the fk_attr-val as the pk_attr-val in the record being updated
							}
						}
						$reference_tab_backup_record_replacement_array=array();
						foreach($reference_tab_backup_record_array as $record)//this loop is for making another array with the replacement-records for the array elements.
						{
							$i1=1;
							$backup_record_replacement="";
							while($i1<$i)
							{
								$backup_record_replacement=$backup_record_replacement.substr($record, 0, strlen($separater_string));
								$record=substr($record, strlen($separater_string));
								$backup_record_replacement=$backup_record_replacement.substr($record, 0, strpos($record, $separater_string));
								$record=substr($record, strpos($record, $separater_string));
								$i1++;
							}
							$backup_record_replacement=$backup_record_replacement.substr($record, 0, strlen($separater_string));
							$record=substr($record, strlen($separater_string));
							$backup_record_replacement=$backup_record_replacement.$_REQUEST["attr".$pk_attr_no];
							$record=substr($record, strpos($record, $separater_string));
							$backup_record_replacement=$backup_record_replacement.$record;
							$reference_tab_backup_record_replacement_array[]=$backup_record_replacement;//putting the replacement records in the array.
						}
						$reference_tab_data_backup_file_content=file_get_contents($reference_tab_data_backup_file);
						$new_reference_tab_data_backup_file_content=str_replace($reference_tab_backup_record_array, $reference_tab_backup_record_replacement_array, $reference_tab_data_backup_file_content);
						file_put_contents($reference_tab_data_backup_file, $new_reference_tab_data_backup_file_content);
					}
				}
			}
		}

		if(isset($change))
		{
			if(file_exists($tab_data_file_backup_file))
				unlink($tab_data_file_backup_file);
			if(file_exists($tab_update_session_id_file))
				unlink($tab_update_session_id_file);
		}

		echo "<script language='javascript'>location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=data';</script>";
	?>
	</body>
</html>