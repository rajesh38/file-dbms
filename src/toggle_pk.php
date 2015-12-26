<html>

	<head>
		<script>
			function pullAjax()
			{
				var a;
				try
				{
					a=new XMLHttpRequest()
				}
				catch(b)
				{
					try
					{
						a=new ActiveXObject("Msxml2.XMLHTTP")
					}
					catch(b)
					{
						try
						{
							a=new ActiveXObject("Microsoft.XMLHTTP")
						}
						catch(b)
						{
							alert("Your browser broke!");return false
						}
					}
				}
				return a;
			}

			function drop_pk()
			{
				try
				{
					obj=pullAjax();
					obj.onreadystatechange=function()
					{
						if(obj.readyState==4)
						{
							location.reload();
						}
					}
					obj.open("GET","drop_pk.php",true);
					obj.send(null);
				}
				catch(err)
				{
					alert(err);
				}
			}

			function change_pk(attr_no)
			{
				try
				{
					obj=pullAjax();
					obj.onreadystatechange=function()
					{
						if(obj.readyState==4)
						{
							eval ("response = "+obj.responseText)
							if(response['status']==2)
							{
								location.href=response['link'];
							}
							else
							{	
								alert(response['msg']);
								location.href=response['link'];
							}
						}
					}
					obj.open("GET","change_pk.php?attr_no="+attr_no,true);
					obj.send(null);
				}
				catch(err)
				{
					alert(err);
				}
			}
		</script>
	</head>

	<body>

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
			$tab_data_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data.knrs";
			$tab_schema_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_schema.knrs";
			$tab_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_constraints.knrs";
			$tab_reference_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_reference.knrs";
			$tab_data_backup_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data_backup.knrs";
			$tab_update_session_id_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_update_session_id.knrs";
			if($_REQUEST['pk']=="true")//i.e. operation on the current PK_attr.
			{
				$arr=file($tab_constraints_file);
				foreach($arr as $line)//searching for the string "PK=>" in constraints.knrs file.
				{
					if(strpos($line,"PK->")===0)
						break;
				}
				if(file_get_contents($tab_reference_file)=="")//i.e. no reference to the current PK_attr is set yet.
				{
					file_put_contents($tab_constraints_file, str_replace($line, "", file_get_contents($tab_constraints_file)));
					if(file_exists($tab_data_backup_file))
						unlink($tab_data_backup_file);
					if(file_exists($tab_update_session_id_file))
						unlink($tab_update_session_id_file);
					echo "<script>
							location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=structure';
						</script>";
				}
				else
				{
					echo "<script>if(confirm(\"Some tables are having Foreign Key attributes referencing this Primary Key. Are you sure to drop the Primary Key?\")==true){drop_pk();} else{location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=structure';}</script>";
				}
			}
			else//i.e operation on a non-PK_attr
			{
				if(file_get_contents($tab_reference_file)!="")//i.e. some references to the current PK_attr are set 
				{
					$new_pk_attr_no=$_REQUEST['attr_no'];
						echo "<script>if(confirm(\"Some tables are having Foreign Key attributes referencing the current Primary Key. Are you sure to toggle the Primary Key?\")==true){change_pk(".$new_pk_attr_no.");} else{location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=structure';}</script>";
				}
				else//i.e. either there is no PK_attr or no reference to the current PK_attr is set.
				{
					$new_pk_attr_no=$_REQUEST['attr_no'];

					//checking whether new_pk_attr is of datatype-file
					$arr=file($tab_schema_file);
					$i=0;
					foreach($arr as $attr_schema)
					{
						$i++;
						if($i==$new_pk_attr_no)
						{
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
									die ("<script>
											location.href=\"table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=structure\";
											alert('".$msg."');
										</script>");
								}
							}
						}
						file_put_contents($tab_constraints_file, "PK->".$_REQUEST['attr']);
						if(file_exists($tab_data_backup_file))
							unlink($tab_data_backup_file);
						if(file_exists($tab_update_session_id_file))
							unlink($tab_update_session_id_file);
						echo "<script>
								location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=structure';
							</script>";
					}
					else//i.e. when new_pk_attr is of file-datatype.
					{
						//Write code for checking existence of reference to duplicate files or null values in file-type attr in the new_pk_attr

						//remove the following js snippet after writing the code.
						echo "<script>
								alert('code for making a file type attr the primary key attr is not yet written.');
								location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=structure';
							</script>";
					}
				}
			}
		?>
	</body>
</html>