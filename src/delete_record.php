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

	function delete_record_ajax(pk_attr_no, pk_attr, record_id_delete)
	{
		try
		{
			obj=pullAjax();
			obj.open("GET","delete_record_ajax.php?pk_attr_no="+pk_attr_no+"&pk_attr="+pk_attr+"&record_id_delete="+record_id_delete, true);
			obj.send(null);
		}
		catch(err)
		{
			alert(err);
		}
	}
    </script>
  </head>
  <?php
	  session_start();
	  if(!isset($_REQUEST['record_id_delete']) || $_REQUEST['record_id_delete']==NULL)
	  {
		  die ("<script language='javascript'>location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=data'</script>");
	  }
	  else
	  {
      //checking if the table has a PK_attribute. If exists then the records in the dependent tables will also be deleted.
      $tab_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_constraints.knrs";
      $tab_other_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_other_constraints.knrs";
      $tab_reference_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_reference.knrs";
      if(strpos(file_get_contents($tab_constraints_file), "PK->")===0)
      {
        if(file_get_contents($tab_reference_file)!="")
        {
          $pk_attr=substr(file_get_contents($tab_constraints_file), strlen("PK->"));
          $tab_schema_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_schema.knrs";
          $arr=file($tab_schema_file);
          $i=0;//$i will tell the attr_no of the PK_attr in the table-schema.
          foreach($arr as $attr_schema)
          {
            $i++;
            $attr=substr($attr_schema, 0, strpos($attr_schema, "->"));
            if($attr=$pk_attr)
              break;
          }
          die ("<script>if(confirm('This table is being referred to by some other table(s). Deleting this record may effect the dependent table(s) records permanently(cannot be rolled-back). Do you still want to delete this record?')==true){delete_record_ajax(".$i.",'".$pk_attr."','".$_REQUEST['record_id_delete']."');} history.go(-1);</script>");
        }
      }

      //keeping backup before DML operation completes, for rollback.
      $tab_data_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data.knrs";
      $tab_data_file_backup="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data_backup.knrs";
      $tab_update_session_id="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_update_session_id.knrs";
      if(!file_exists($tab_data_file_backup))
      {
        copy($tab_data_file, $tab_data_file_backup);
        file_put_contents($tab_update_session_id, session_id());
      }

		  $record_del_index=0;//this var is used to get the index of the 1st char of the record to be deleted.
		  $record_id=0;//record sequence no.
		  $record=NULL;
		  $record_id_delete=$_REQUEST['record_id_delete'];
		  $tab_data_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data.knrs";
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
    }
	echo "<script language='javascript'>location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=data'</script>";
  ?>
</html>