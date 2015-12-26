<html>
  <body bgcolor="aliceblue">
	<?php
		session_start();
		echo "<u><h3>TABLE : <font color=CornflowerBlue>".$_SESSION['tab_selected']."</font><font size=2 face='lucida fax'>(".$_SESSION['view'].")</font></h3></u>";

		$tab_data_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data.knrs";
		$tab_schema_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_schema.knrs";
		$tab_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_constraints.knrs";
		$tab_other_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_other_constraints.knrs";
		$no_attr=$_REQUEST['no_attr'];
		$pk_attr=$_REQUEST['pk_attr'];
		if($pk_attr!="")
		{
			if($_REQUEST['file_true'.$pk_attr]==1)
			{
				if($_FILES["attr".$pk_attr]["error"]==4)
					die ("<script>alert('Primary Key cannot be NULL. Insertion aborted...'); history.go(-1);</script>");
				else if($_FILES["attr".$pk_attr]["error"]>0)
					die ("<script>alert('The upload operation on the file led to some unexpected state. Insertion aborted...'); history.go(-1);</script>");
			}
			else
			{
				if($_REQUEST['attr'.$pk_attr]=="")
					die ("<script>alert('Primary Key cannot be NULL. Insertion aborted...'); history.go(-1);</script>");
			}
			if(filesize($tab_data_file)>0)
			{
				$substr=NULL;
				$separater_string="#$**$#";
				$pos1=0;
				$pos2=0;
				$data=NULL;
				$arr=file($tab_data_file);
				foreach($arr as $line)
				{
					$data_no=0;
					$substr1=$line;
					$substr1=substr($substr1,6,strlen($substr1)-6);
					while(($pos2+strlen($separater_string))<=strlen($substr1))
					{
						$data_no++;
						$pos1=strpos($substr1,$separater_string);
						$data=substr($substr1,0,$pos1);
						if($_REQUEST['file_true'.$pk_attr]==1)
							$_REQUEST['attr'.$pk_attr]=$_FILES['attr'.$pk_attr]['name'];
						if($pk_attr==$data_no && $data==$_REQUEST['attr'.$pk_attr])
							die ("<script>alert('The Primary Key Attribute cannot have duplicte values. Insertion aborted...'); history.go(-1);</script>");
						$substr1=substr($substr1,$pos1+strlen($separater_string),(strlen($substr1)-($pos1+strlen($separater_string))));
						if($substr1=="")
							break;
						$pos2=strpos($substr1,$separater_string);
					}
				}
			}
		}
		$record_string="";
		$fp=fopen($tab_data_file,'a');

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
						die ("<script>alert('Attribute:\"".$attr."\" does not allow NULL value. Insertion aborted...'); history.go(-1);</script>");
				}
				else
				{
					if(!isset($_REQUEST['attr'.$i]) || $_REQUEST['attr'.$i]=="")
						die ("<script>alert('Attribute:\"".$attr."\" does not allow NULL value. Insertion aborted...'); history.go(-1);</script>");
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
					//else
						//echo "<script>if(confirm('Error : ".$err_msg." Do you want to proceed with no file uploaded?')==false){history.go(-1);}</script>";
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
/*
					if (file_exists($file_upload_url . $_FILES["file"]["name"]))
					{
						echo "<script>if(confirm(\"Another file with the same name exists in the table. You must Rename the file to upload. Do you want to rename it?\")==true){var new_file_name=prompt('Enter new name for the file'); if(new_file_name.trim!=''){code to use the new name from prompt-box}} else{if(confirm(\"Insert aborted. Do you want to try again?\")==true){history.go(-1)} else{location.href=\"table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=data';\"}}</script>";
					}
					//this is the code for the case when a file exists having the same name as the file being uploaded. The user has to rename it.
*/
					if(!is_dir($file_upload_url))
						mkdir($file_upload_url);
					move_uploaded_file($_FILES['attr'.$i]["tmp_name"], $file_upload_url.$_FILES['attr'.$i]["name"]);
					//echo "<script>alert('Upload Complete.');</script>";//checking
				}
			}
			if($_REQUEST['file_true'.$i]==1)
			{
				if($_FILES["attr".$i]["error"] == 4)
					$data="";
				else
					$data=$_FILES["attr$i"]["name"];
			}
			else
				$data=$_REQUEST["attr$i"];
			$offset=0;
			$old="\n";
			$new="<br>";
			$tmpOldStrLength = 2;
			while (($offset = strpos($data, $old, $offset)) !== FALSE)
			{
				$data = substr_replace($data, $new, $offset-1, $tmpOldStrLength);
			}
			$offset = strpos($data, $old);
			if($offset!==FALSE)
				$data = substr_replace($data, $new, $offset, $tmpOldStrLength);
			$record_string=$record_string."#$**$#".$data;
		}
		$record_string=$record_string."#$**$#";
		if(strlen($record_string)==($no_attr+1)*strlen('#$**$#'))
			die("<script>alert('Values of all the attributes of a record cannot be NULL. Insertion aborted...'); history.go(-1);</script>");
			
		//keeping backup before DML operation completes, for rollback.
		$tab_data_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data.knrs";
		$tab_data_file_backup="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data_backup.knrs";
		$tab_update_session_id="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_update_session_id.knrs";
		if(!file_exists($tab_data_file_backup))
		{
			copy($tab_data_file, $tab_data_file_backup);
			file_put_contents($tab_update_session_id, session_id());
		}
			
		fwrite($fp,$record_string."\n");

		//if this table has a parent table, the tables have to be committed after the write operation completes i.e. have to delete the 'data_backup' file of the parent tables.
		if(strpos(file_get_contents($tab_other_constraints_file), "FK->")!==FALSE)
		{
			$arr=file($tab_other_constraints_file);
			foreach($arr as $other_constraint)
			{
				if(strpos($other_constraint, "FK->")===0)
				{
					$other_constraint=str_replace("\n", "", $other_constraint);//removing "\n" from other_constraint "FK-><attr>-><db.tab.attr>\n".
					$other_constraint=substr($other_constraint, strrpos($other_constraint, "->")+2);//now $other_constraint=<db.tab.attr>.
					//echo "<script>alert('".$other_constraint."')</script>";//
					$parent_db=substr($other_constraint, 0, strpos($other_constraint, "."));
					//echo "<script>alert('-".$parent_db."-')</script>";//
					$parent_tab=substr($other_constraint, strpos($other_constraint, ".")+1, strrpos($other_constraint, ".")-strpos($other_constraint, ".")-1);
					//echo "<script>alert('-".$parent_tab."-')</script>";//
					$parent_attr=substr($other_constraint, strrpos($other_constraint, ".")+1);
					//echo "<script>alert('-".$parent_attr."-')</script>";//

					$parent_tab_data_backup_file="databases/$_SESSION[uname]/".$parent_db."/".$parent_tab."_data_backup.knrs";
					$parent_tab_update_session_id_file="databases/$_SESSION[uname]/".$parent_db."/".$parent_tab."_update_session_id.knrs";
					if(file_exists($parent_tab_data_backup_file))
						unlink($parent_tab_data_backup_file);
					if(file_exists($parent_tab_data_backup_file))
						unlink($parent_tab_update_session_id_file);
				}
			}
		}

		fclose($fp);
		echo "<script language='javascript'>if(confirm(\"Record inserted. Do you want to insert another record?\")){location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=insert';} else{location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=data';}</script>";
	?>
	</body>
</html>