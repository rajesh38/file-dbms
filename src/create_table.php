<html>
<?php
	include "create_table_form.php";
	$tab_name=$_REQUEST['f2_tab_name'];
	$no_of_attributes=$_REQUEST['f2_no_of_attributes'];
	session_start();
	$db_selected=$_SESSION['db_selected'];
	if(file_exists("databases/$_SESSION[uname]/$db_selected/$tab_name"."_schema.knrs"))
	{
		die ("<script language=\"javascript\">alert(\"The table:".$tab_name." already exists in database:".$db_selected.".\\nTry another name\");</script>");
	}
	else
	{
		//Creating 5 files for the table being created.
		$fp=fopen("databases/$_SESSION[uname]/$db_selected/$tab_name"."_schema.knrs",'w');
		fclose($fp);
		$fp=fopen("databases/$_SESSION[uname]/$db_selected/$tab_name"."_constraints.knrs",'w');
		fclose($fp);
		$fp=fopen("databases/$_SESSION[uname]/$db_selected/$tab_name"."_data.knrs",'w');
		fclose($fp);
		$fp=fopen("databases/$_SESSION[uname]/$db_selected/$tab_name"."_reference.knrs",'w');
		fclose($fp);
		$fp=fopen("databases/$_SESSION[uname]/$db_selected/$tab_name"."_other_constraints.knrs",'w');
		fclose($fp);

		//Writing table-schema details
		$fp=fopen("databases/$_SESSION[uname]/$db_selected/$tab_name"."_schema.knrs",'a');
		for($i=1;$i<=$no_of_attributes;$i++)
		{
			if(fwrite($fp,$_REQUEST['attr'.$i]."->".$_REQUEST['attr_type'.$i]."->".$_REQUEST['attr_size'.$i]."\n")==false)
			{
				fclose($fp);
				unlink("databases/$_SESSION[uname]/$db_selected/$tab_name"."_schema.knrs");
				unlink("databases/$_SESSION[uname]/$db_selected/$tab_name"."_constraints.knrs");
				unlink("databases/$_SESSION[uname]/$db_selected/$tab_name"."_data.knrs");
				die("<script language=\"javascript\">alert(\"The table could not be created due to unknown reason.\n Try again.\");</script>");
			}
		}
		fclose($fp);

		//For PK attribute.
		if(isset($_REQUEST['pk']))
		{
			$fp=fopen("databases/$_SESSION[uname]/$db_selected/$tab_name"."_constraints.knrs",'a');
			fwrite($fp,"PK->".$_REQUEST['attr'.$_REQUEST['pk']]);
			fclose($fp);
		}

		//for enlisting the table in the list of tables oin the DB
		$fp=fopen("databases/$_SESSION[uname]/$db_selected/table_list.knrs",'a');
		if(fwrite($fp,$tab_name."\n")==false)
		{
			fclose($fp);
			unlink("databases/$_SESSION[uname]/$db_selected/$tab_name"."_schema.knrs");
			unlink("databases/$_SESSION[uname]/$db_selected/$tab_name"."_constraints.knrs");
			unlink("databases/$_SESSION[uname]/$db_selected/$tab_name"."_data.knrs");
			die("<script language=\"javascript\">alert(\"The table could not be created due to unknown reason.\n Try again.\");</script>");
		}
		fclose($fp);

		echo "<script language=\"javascript\">parent.frames['right_container_left_top'].location.href=\"db_tables.php\";</script>";
	}
?>
</html>