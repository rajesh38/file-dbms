<?php
	if(!isset($_SESSION))
		session_start();
	$db_selected=$_SESSION['db_selected'];
	$tab_selected=$_SESSION['tab_selected'];

	//getting the PK_attr
	$tab_constraints_file="databases/$_SESSION[uname]/".$db_selected."/".$tab_selected."_constraints.knrs";
	$arr=file($tab_constraints_file);
	$pk_attr=substr($arr[0], strlen("PK->"));

	//This is the block to remove the references to this PK_attr.
	$reference_file="databases/$_SESSION[uname]/$db_selected/$tab_selected"."_reference.knrs";
	$arr=file($reference_file);
	foreach($arr as $referenced_by)
	{
		//extracting each (reference_db, reference_db, reference_attr) group.
		$referenced_by=substr($referenced_by, strlen("REFERENCE->"));//now $referenced_by=<DB.TAB.ATTR>
		$referenced_by=substr($referenced_by, 0, strlen($referenced_by)-1);
		$referenced_by_db=substr($referenced_by, 0, strpos($referenced_by, "."));
		$referenced_by=substr($referenced_by, strpos($referenced_by, ".")+1);
		$referenced_by_tab=substr($referenced_by, 0, strpos($referenced_by, "."));
		$referenced_by_attr=substr($referenced_by, strpos($referenced_by, ".")+1);

		//removing the FK-constraints from individual dependent attributes.
		$tab_other_constraints_file="databases/$_SESSION[uname]/".$referenced_by_db."/".$referenced_by_tab."_other_constraints.knrs";
		$arr=file($tab_other_constraints_file);
		$other_constraint_replacable[]=array();
		//taking the FK-constraint-strings associated with this PK_attr in an array from other_constraints_file which are dependent on the current PK_attr.
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
	echo json_encode(array('status'=>1));
?>