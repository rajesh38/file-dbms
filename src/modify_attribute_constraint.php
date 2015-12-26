<html>

	<head>
	</head>

	<body>
		<?php
			if(!isset($_SESSION))
				session_start();
			$tab_other_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_other_constraints.knrs";
			$attr=$_REQUEST['attr'];
			$arr=file($tab_other_constraints_file);

			if(isset($_REQUEST['select_db']))
				$db_selected=$_REQUEST['select_db'];
			else
				$db_selected="-select-";

			if(isset($_REQUEST['select_tab']))
				$tab_selected=$_REQUEST['select_tab'];
			else
				$tab_selected="-select-";

			if(isset($_REQUEST['select_attr']))
				$attr_selected=$_REQUEST['select_attr'];
			else
				$attr_selected="-select-";

			if($db_selected=="-select-" || $tab_selected=="-select-" || $attr_selected=="-select-")
				$new_fk_string="";
			else
				$new_fk_string="FK->".$attr."->".$db_selected.".".$tab_selected.".".$attr_selected."\n";

			//writing new Foreign Key constraint
			foreach($arr as $other_constraint)
			{
				if(strpos($other_constraint, "FK->".$attr."->")===0)
				{
					$current_fk_string=$other_constraint;
					break;
				}
			}
			if(!isset($current_fk_string))//currently no reference is set.
			{
				if($new_fk_string!="")//i.e. some new reference is set. then only we have to keep record of it.
				{
					file_put_contents($tab_other_constraints_file, $new_fk_string, FILE_APPEND);

					//code to keep the record of "setting a new reference" for the attr in the new referenced attribute in the selected table in the selected DB.
					$target_tab_reference_file="databases/$_SESSION[uname]/$db_selected/$tab_selected"."_reference.knrs";
					file_put_contents($target_tab_reference_file, "REFERENCE->".$_SESSION['db_selected'].".".$_SESSION['tab_selected'].".".$attr."\n", FILE_APPEND);
					unset($target_tab_reference_file);
				}
			}
			else//already some reference is set.
			{
				if($current_fk_string!=$new_fk_string)
				{
					file_put_contents($tab_other_constraints_file, str_replace($current_fk_string, $new_fk_string, file_get_contents($tab_other_constraints_file)));

					//code to remove the record of the previous reference from the previous referenced table(attribute).
					$current_fk_string=substr($current_fk_string, strrpos($current_fk_string, "->")+2);
					$current_reference_db=substr($current_fk_string, 0, strpos($current_fk_string, "."));
					$current_fk_string=substr($current_fk_string, strpos($current_fk_string, ".")+1);
					$current_reference_tab=substr($current_fk_string, 0, strpos($current_fk_string, "."));
					$current_fk_string=substr($current_fk_string, strpos($current_fk_string, ".")+1);
					$current_reference_attr=substr($current_fk_string, 0, strlen($current_fk_string-1));
					$current_target_tab_reference_file="databases/$_SESSION[uname]/$current_reference_db/$current_reference_tab"."_reference.knrs";
					file_put_contents($current_target_tab_reference_file, str_replace("REFERENCE->".$_SESSION['db_selected'].".".$_SESSION['tab_selected'].".".$attr."\n", "", file_get_contents($current_target_tab_reference_file)));
					unset($current_target_tab_reference_file);
					unset($current_fk_string);
					unset($current_reference_db);
					unset($current_reference_tab);
					unset($current_reference_attr);

					//code to keep record of the new reference in the new referenced table(attribute).
					if($new_fk_string!="")
					{
						$new_target_tab_reference_file="databases/$_SESSION[uname]/$db_selected/$tab_selected"."_reference.knrs";
						file_put_contents($new_target_tab_reference_file, "REFERENCE->".$_SESSION['db_selected'].".".$_SESSION['tab_selected'].".".$attr."\n", FILE_APPEND);
						unset($new_target_tab_reference_file);
					}
				}
			}
			//New Foreign Key constraint written

			//writing NOT_NULL constraint
			if(!isset($_REQUEST['not_null']) || $_REQUEST['not_null']=="TRUE")
			{
				$search_not_null_str="NOT_NULL->".$attr."->true\n";
				if(strpos(file_get_contents($tab_other_constraints_file), $search_not_null_str)===FALSE)
				{
					file_put_contents($tab_other_constraints_file, $search_not_null_str, FILE_APPEND);
				}
			}
			else
			{
				$search_not_null_str="NOT_NULL->".$attr."->true\n";
				if(strpos(file_get_contents($tab_other_constraints_file), $search_not_null_str)!==FALSE)
				{
					file_put_contents($tab_other_constraints_file, str_replace($search_not_null_str, "", file_get_contents($tab_other_constraints_file)));
				}
			}
			//NOT_NULL constraint written.

			//Writing default_value constraint
			$attr_type=$_REQUEST['attr_type'];
			if($attr_type!="file")
			{
				$new_default_val_str="DEFAULT->".$attr."->".$_REQUEST['default_val']."\n";
				$arr=file($tab_other_constraints_file);
				foreach($arr as $other_constraint)
				{
					if(strpos($other_constraint, "DEFAULT->".$attr."->")!==FALSE)
					{
						$current_default_val_str=$other_constraint;
						break;
					}
				}
				if(!isset($current_default_val_str))//i.e. if no default value is set yet.
				{
					file_put_contents($tab_other_constraints_file, $new_default_val_str, FILE_APPEND);
				}
				else//i.e. if some default value is already set.
				{
					file_put_contents($tab_other_constraints_file, str_replace($current_default_val_str, $new_default_val_str, file_get_contents($tab_other_constraints_file)));
				}
			}
			//Default_value constraint written
		?>
	</body>
	
	<?php
		echo "<script>
				location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=constraints';
			</script>";
	?>
	
	</html>