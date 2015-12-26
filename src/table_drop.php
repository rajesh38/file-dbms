<html>
	<head>
		<script>
		</script>
	</head>
  <?php
	session_start();

  function removeDir($dir)
  {
	  if(file_exists($dir))
	  {
		  // create directory pointer
		  $dp = opendir($dir) or die ('ERROR: Cannot open directory');
		  // read directory contents
		  // delete files found
		  // call itself recursively if directories found
		  while ($file = readdir($dp))
		  {
			  if ($file != '.' && $file != '..')
			  {
				  if (is_file("$dir/$file"))
				  {
					  unlink("$dir/$file");
				  }
				  else if (is_dir("$dir/$file"))
				  {
					  removeDir("$dir/$file");
				  }
			  }
		  }
		  // close directory pointer
		  closedir($dp);
		  // remove now-empty directory
		  if (rmdir($dir))
		  {
			  return true;
		  }
		  else
		  {
			  return false;
		  }
	  }
  }

	//actual table files.
	$tab_data_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data.knrs";
	$tab_schema_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_schema.knrs";
	$tab_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_constraints.knrs";
	$tab_files_directory="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_files";
	$tab_update_session_id_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_update_session_id.knrs";
	$tab_other_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_other_constraints.knrs";
	$tab_reference_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_reference.knrs";

	//backup of table files.
	$tab_data_file_bak="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data_backup.knrs";
	$tab_schema_file_bak="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_schema_bak.knrs";
	$tab_constraints_file_bak="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_constraints_bak.knrs";

	try
	{
    //checking if the table has a primary key and if it is held in a relationship by another table.
    if(filesize($tab_constraints_file)!=0) //i.e. some attribute is set as pk_attr.
    {
      if(filesize($tab_reference_file)!=0) //i.e. some references are set to the pk_attr of this table.
      {
        $pk_attr=substr(file_get_contents($tab_constraints_file), strlen("PK->"));
        $arr=file($tab_reference_file);
        foreach($arr as $reference)//$arr is array of all the reference <db.tab.attr> group.
        {
          $reference=substr($reference, strlen("REFERENCE->"));//$reference=<db.tab.attr>\n
          $reference=substr($reference, 0, strlen($reference)-1);//$reference=<db.tab.attr>
          $reference_db=substr($reference, 0, strpos($reference, "."));//$reference_db=<db>
          $reference=substr($reference, strpos($reference, ".")+1);//$reference=<tab.attr>
          $reference_tab=substr($reference, 0, strpos($reference, "."));//$reference_tab=<tab>
          //$reference=substr($reference, strpos($reference, ".")+1);//$reference=<attr>
          //$reference_attr=$reference;//$reference_attr=<attr>
          $reference_tab_other_constraints_file="databases/$_SESSION[uname]/".$reference_db."/".$reference_tab."_other_constraints.knrs";
          if(strpos(file_get_contents($reference_tab_other_constraints_file), "->".$_SESSION['db_selected'].".".$_SESSION['tab_selected'].".".$pk_attr."\n")!==FALSE)
          {//i.e. if any fk_attr is referencing to the pk_attr of this table.
            if(!isset($reference_tab_fk_constraint_arr))
            {
              $reference_tab_fk_constraint_arr=array();
            }
            else
            {
              unset($reference_tab_fk_constraint_arr);
              $reference_tab_fk_constraint_arr=array();
            }
            $arr1=file($reference_tab_other_constraints_file);
            foreach($arr1 as $reference_tab_other_constraint)
            {
              if(strpos($reference_tab_other_constraint, "FK->")===0 && strpos($reference_tab_other_constraint, "->".$_SESSION['db_selected'].".".$_SESSION['tab_selected'].".".$pk_attr."\n")!==FALSE)
              {
                $reference_tab_fk_constraint_arr[]=$reference_tab_other_constraint;//this array will hold all the fk_constraints referencing to the corrent attribute
              }
            }
            $reference_tab_other_constraints_file_content=file_get_contents($reference_tab_other_constraints_file);
            foreach($reference_tab_fk_constraint_arr as $selected_fk_constraint)
            {
              $reference_tab_other_constraints_file_content=str_replace($selected_fk_constraint, "", $reference_tab_other_constraints_file_content);
            }
            file_put_contents($reference_tab_other_constraints_file, $reference_tab_other_constraints_file_content);
          }
        }
      }
    }

    //checking if there exists any fk_attr in this table, then we have to remove the reference from the parent tables reference_file.
    $arr=file($tab_other_constraints_file);
    foreach($arr as $other_constraint)
    {
      if(strpos($other_constraint, "FK->")===0)
      {
        $parent=substr($other_constraint, strrpos($other_constraint, "->")+2);//$parent="<db.tab.attr>\n"
        $parent=substr($parent, 0, strlen($parent)-1);//$parent="<db.tab.attr>"
        $parent_db=substr($parent, 0, strpos($parent, "."));//$parent_db=<db>
        $parent=substr($parent, strpos($parent, ".")+1);//$parent=<tab.attr>
        $parent_tab=substr($parent, 0, strpos($parent, "."));//$parent_tab=<tab>
        //$parent=substr($parent, strpos($parent, ".")+1);//$parent=<attr>
        //$parent_attr=$parent;//$parent_attr=<attr>
        $parent_tab_reference_file="databases/$_SESSION[uname]/".$parent_db."/".$parent_tab."_reference.knrs";
        $arr1=file($parent_tab_reference_file);
        foreach($arr1 as $reference)
        {
          if(strpos($reference, "REFERENCE->".$_SESSION['db_selected'].".".$_SESSION['tab_selected'].".")===0)
          {
            break;
          }
        }
        file_put_contents($parent_tab_reference_file, str_replace($reference, "", file_get_contents($parent_tab_reference_file)));
      }
    }

    //drop started
    if(file_exists($tab_data_file))
    {
		  while(copy($tab_data_file,$tab_data_file_bak)==false)
		  {
			  continue;
		  }
    }
    if(file_exists($tab_schema_file))
    {
		  while(copy($tab_schema_file,$tab_schema_file_bak)==false)
		  {
			  continue;
		  }
    }
    if(file_exists($tab_constraints_file))
    {
		  while(copy($tab_constraints_file,$tab_constraints_file_bak)==false)
		  {
			  continue;
		  }
    }
    if(file_exists($tab_other_constraints_file))
		  unlink($tab_other_constraints_file);
    if(file_exists($tab_reference_file))
		  unlink($tab_reference_file);
    if(file_exists($tab_data_file))
		  unlink($tab_data_file);
    if(file_exists($tab_schema_file))
		  unlink($tab_schema_file);
    if(file_exists($tab_constraints_file))
		  unlink($tab_constraints_file);
    if(file_exists($tab_data_file_bak))
      unlink($tab_data_file_bak);
    if(file_exists($tab_schema_file_bak))
      unlink($tab_schema_file_bak);
    if(file_exists($tab_constraints_file_bak))
      unlink($tab_constraints_file_bak);
    if(file_exists($tab_update_session_id_file))
      unlink($tab_update_session_id_file);
    if(is_dir($tab_files_directory))
    removeDir($tab_files_directory);
	}
	catch(Exception $e)
	{
		if(file_exists($tab_data_file_bak))
			copy($tab_data_file_bak,$tab_data_file);
		if(file_exists($tab_schema_file_bak))
			copy($tab_schema_file_bak,$tab_schema_file);
		if(file_exists($tab_constraints_file_bak))
			copy($tab_constraints_file_bak,$tab_constraints_file);
		die("<acript language='javascript'>alert('Table:".$_SESSION[tab_selected]." could not be dropped.\nTry again...'); location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=data';</script>");
	}
	//Deletion of three table_files complete.
	
	//Now delete from table_list.knrs.
	$tab_list_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/table_list.knrs";
	$fp=fopen($tab_list_file,'r');
	$tab_list=fread($fp,filesize($tab_list_file));
  if(strpos($tab_list,$_SESSION['tab_selected']."\n")==0)
  {
    $replace_str=$_SESSION['tab_selected']."\n";
    $new_tab_list=substr_replace($tab_list,"",strpos($tab_list,$replace_str),strlen($replace_str));
  }
  else if(strpos($tab_list,$_SESSION['tab_selected']."\n")>0 && strpos($tab_list,"\n".$_SESSION['tab_selected']."\n")!==false)
  {
    $replace_str="\n".$_SESSION['tab_selected']."\n";
    $new_tab_list=substr_replace($tab_list,"\n",strpos($tab_list,$replace_str),strlen($replace_str));
  }
  else if(strpos($tab_list,$_SESSION['tab_selected']."\n")==FALSE)
    $replace_str=NULL;
  fclose($fp);
  $fp=fopen($tab_list_file,'w');
  if(isset($replace_str) && $replace_str!=NULL)
    fwrite($fp,$new_tab_list);
  fclose($fp);
	$_SESSION['tab_selected']=NULL;
	echo "<script language='javascript'>/*top.right_container.location.reload();*/parent.parent.location.reload();</script>";
?>
</html>