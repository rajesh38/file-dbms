<?php
	function myStrLen($a,$b)
	{
		return strlen($a) < strlen($b) ? 1 : -1; 
	}
	if(!isset($_SESSION))
		session_start();
  if(!isset($_REQUEST['attr_type']))
  {
    $_REQUEST['attr_type_prev']="file";
    $_REQUEST['attr_type']="file";
  }
	if($_REQUEST['attr_prev']==$_REQUEST['attr'] && $_REQUEST['attr_type_prev']==$_REQUEST['attr_type'] && $_REQUEST['attr_size_prev']==$_REQUEST['attr_size'])
	{
		echo "<script>history.go(-2);</script>";
	}
	else
	{
		$tab_data_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data.knrs";
    $tab_data_backup_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data_backup.knrs";
    $tab_update_session_id_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_update_session_id.knrs";
		if($_REQUEST['attr_size_prev']>$_REQUEST['attr_size'])
		{
      if(filesize($tab_data_file)!=0)
      {
			  $arr=file($tab_data_file);
			  usort($arr,"myStrLen");
			  $arr[0]=substr($arr[0],0,strlen($arr[0])-1);
			  $attr_no=$_REQUEST['attribute_no_update'];
			  $attr_no_counter=$attr_no;
			  $separater_string="#$**$#";
			  while($attr_no_counter--)
			  {
				  $arr[0]=substr($arr[0],strpos($arr[0],$separater_string)+strlen($separater_string));
				  if($attr_no_counter==0)
					  $value=substr($arr[0],0,strpos($arr[0],$separater_string));
			  }
			  if(strlen($value)>$_REQUEST['attr_size'])
          die("<script>alert(\"Attribute:'$_REQUEST[attr_prev]' contains some values of length greater than $_REQUEST[attr_size]. Minimum length allowed=".strlen($value).".\"); history.go(-1);</script>");
      }
		}
		if($_REQUEST['attr_type_prev']=="char" && $_REQUEST['attr_type']=="number")
		{
			$attr_no=$_REQUEST['attribute_no_update'];
			$arr=file($tab_data_file);
			$separater_string="#$**$#";
			foreach($arr as $record)
			{
				$record=substr($record,0,strlen($record)-1);
				$attr_no_counter=$attr_no;
				$record=substr($record,strlen($separater_string));
				while($attr_no_counter-->1)
				{
					$record=substr($record,strpos($record,$separater_string)+strlen($separater_string));
				}
        $value=substr($record,0,strpos($record,$separater_string));
				if(!is_numeric($value))
					die("<script>alert(\"Attribute:'$_REQUEST[attr_prev]' contains some non-numeric values. It cannot be modified to 'number' datatype.\");  history.go(-1);</script>");
			}
		}
		//For checking if there is any non-numeric value in the char-type attribute-
		//becoz they will not support number data-type and hence will incur inconsistency.
		
		$tab_schema_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_schema.knrs";
    $tab_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_constraints.knrs";
		$prev_schema_file_content=file_get_contents($tab_schema_file);
		$new_attr_details="$_REQUEST[attr]->$_REQUEST[attr_type]->$_REQUEST[attr_size]";
		$old_attr_details="$_REQUEST[attr_prev]->$_REQUEST[attr_type_prev]->$_REQUEST[attr_size_prev]";
		$new_schema_file_content=str_replace($old_attr_details,$new_attr_details,$prev_schema_file_content);
		file_put_contents($tab_schema_file,$new_schema_file_content);

    //modifying the name of the attribute in the parent table reference file if it holds some FK_constraint referencing to some attribute belonging to the table.
    if($_REQUEST['attr_prev']!=$_REQUEST['attr'])
    {
      $tab_other_constraints_file="databases/$_SESSION[uname]/$_SESSION[db_selected]/$_SESSION[tab_selected]"."_other_constraints.knrs";
      $arr=file($tab_other_constraints_file);
      foreach($arr as $other_constraint)
      {
        if(strpos($other_constraint, "FK->$_REQUEST[attr_prev]->")===0)
        {
          $fk_constraint=$other_constraint;
          break;
        }
      }
      if(isset($fk_constraint))//i.e. the attribute references to some other attribute
      {
        //Extracting the reference PK_attr details.
        $reference=substr($fk_constraint, strlen("FK->$_REQUEST[attr_prev]->"));//$reference=<db.tab.attr>\n
        $reference=substr($reference, 0, strlen($reference)-1);//$reference=<db.tab.attr>
        $reference_db=substr($reference, 0, strpos($reference, "."));//$reference_db=<db>
        $reference=substr($reference, strpos($reference, ".")+1);//$reference=<tab.attr>
        $reference_tab=substr($reference, 0, strpos($reference, "."));//$reference_db=<tab>
        $reference=substr($reference, strpos($reference, ".")+1);//$reference=<attr>
        $reference_attr=$reference;//$reference_attr=<attr>
        //modifying the name of the dependent attr in the reference_file of the PK_attr_tab
        $reference_tab_reference_file="databases/$_SESSION[uname]/$reference_db/$reference_tab"."_reference.knrs";
        $reference_tab_reference_file_content=file_get_contents($reference_tab_reference_file);
        file_put_contents($reference_tab_reference_file, str_replace("REFERENCE->$_SESSION[db_selected].$_SESSION[tab_selected].$_REQUEST[attr_prev]", "REFERENCE->$_SESSION[db_selected].$_SESSION[tab_selected].$_REQUEST[attr]", $reference_tab_reference_file_content));
        //Keeping the new-name of the attr being modified in the other_constraint_file
        $tab_other_constraints_file_content=file_get_contents($tab_other_constraints_file);
        file_put_contents($tab_other_constraints_file, str_replace("FK->$_REQUEST[attr_prev]->", "FK->$_REQUEST[attr]->", $tab_other_constraints_file_content));
      }
    }

    //modifying constraints file of the current table as well as the other_constraints file of the dependent attr-tables if the pk_attr-name gets modified(renamed), the new name must be present there too.
    if($_REQUEST['pk_true']==1)
    {
      if($_REQUEST['attr_prev']!=$_REQUEST['attr'])
      {
        $tab_reference_file="databases/$_SESSION[uname]/$_SESSION[db_selected]/$_SESSION[tab_selected]"."_reference.knrs";
        $arr=file($tab_reference_file);
        foreach($arr as $reference)
        {
          //extracting each <db.tab.attr> group referencing the current PK_attr.
          $reference=substr($reference, strlen("REFERENCE->"));//$reference=<db.tab.attr>\n
          $reference=substr($reference, 0, strlen($reference)-1);//$reference=<db.tab.attr>
          $reference_db=substr($reference, 0, strpos($reference, "."));//$reference_db=<db>
          $reference=substr($reference, strpos($reference, ".")+1);//$reference_db=<tab.attr>
          $reference_tab=substr($reference, 0, strpos($reference, "."));//$reference_tab=<tab>
          $reference=substr($reference, strpos($reference, ".")+1);//$reference=<attr>
          $reference_attr=$reference;//$reference_attr=<attr>

          //changing the PK_attr name in the other constraints file to the new name(if changed).
          $reference_tab_other_constraints_file="databases/$_SESSION[uname]/$reference_db/$reference_tab"."_other_constraints.knrs";
          $current_other_constraints_file_content=file_get_contents($reference_tab_other_constraints_file);
          $new_other_constraints_file_content=str_replace("->".$_SESSION['db_selected'].".".$_SESSION['tab_selected'].".".$_REQUEST['attr_prev']."\n", "->".$_SESSION['db_selected'].".".$_SESSION['tab_selected'].".".$_REQUEST['attr']."\n", $current_other_constraints_file_content);
          file_put_contents($reference_tab_other_constraints_file, $new_other_constraints_file_content);
        }
      }
      file_put_contents($tab_constraints_file, "PK->".$_REQUEST['attr']);
      if(file_exists($tab_data_backup_file))
        unlink($tab_data_backup_file);
      if(file_exists($tab_update_session_id_file))
        unlink($tab_update_session_id_file);
    }

		echo "<script>location.href=\"table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=structure\"</script>";
	}
?>