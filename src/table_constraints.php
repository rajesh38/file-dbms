<html>
	<head>
	</head>

	<body>
		<?php
			if(!isset($_SESSION))
				session_start();
			$tab_schema_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_schema.knrs";
			$tab_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_constraints.knrs";
			$tab_other_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_other_constraints.knrs";
		?>

		<center>
		<br><br>
		<table border=1 bgcolor=cyan width='100%' style="table-layout:fixed">
			<tr>
				<th bgcolor=turquoise width='70px'>MODIFY
				<th style=\"word-wrap: break-word;\"><font size=4 face='lucida fax'><b>ATTRIBUTE</b></font>
				<th style=\"word-wrap: break-word;\"><font size=4 face='lucida fax'><b>REFERENCES</b></font>
				<th style=\"word-wrap: break-word;\"><font size=4 face='lucida fax'><b>DEFAULT<br>VALUE</b></font>
				<th style=\"word-wrap: break-word;\"><font size=4 face='lucida fax'><b>NOT<br>NULL</b></font>
			</tr>

		<?php
			$row_no=0;
			$arr=file($tab_schema_file);
			$attr_array=array();
			$attr_type_array=array();
			$attr_size_array=array();
			foreach($arr as $row)
			{
				$row_no++;
				$attr=substr($row,0,strpos($row, "->"));
				$attr_array[]=$attr;
				$row=substr($row, strpos($row, "->")+2);
				$attr_type=substr($row, 0, strpos($row, "->"));
				$attr_type_array[]=$attr_type;
				$row=substr($row, strpos($row, "->")+2);
				$attr_size=substr($row, 0, strlen($row)-1);
				$attr_size_array[]=$attr_size;
			}

			$i=0;
			foreach($attr_array as $attr)
			{
				echo "<tr>
						<td bgcolor=turquoise align=center><div style=\"cursor:pointer\" title=\"Modify constraints on this attribute\" onclick=\"{location.href='modify_attribute_constraint_form.php?attr=".$attr."&attr_type=".$attr_type_array[$i]."&attr_size=".$attr_size_array[$i]."';}\"><font color=red><img src='images/edit_object2.jpg' width=50%></font></div> </td>
						<td align=center style=\"word-wrap: break-word;\"><font size=4 face='lucida fax'>$attr</font>
						<td align=center style=\"word-wrap: break-word;\"><span id='".$attr."_fk'></span>
						<td align=center style=\"word-wrap: break-word;\"><span id='".$attr."_default'></span>
						<td align=center style=\"word-wrap: break-word;\"><span id='".$attr."_not_null'></span>
					</tr>";
				$i++;
			}
			unset($i);
/*
			$tab_constraints_file="databases/$_SESSION[uname]/$_SESSION[db_selected]/$_SESSION[tab_selected]"."_constraints.knrs";
			if(file_get_contents($tab_constraints_file)=="PK->$attr")
			{
				echo "<script>
						document.getElementById('".$attr."_not_null').innerHTML=\"<img src='images/tick_icon3.png' height=20 width=20/>\";
					</script>";
			}
			else
			{
				echo "<script>
						alert('content-".file_get_contents($tab_constraints_file)."-content-"."PK->$attr"."-content');
					</script>";
			}
*/
			if(file_exists($tab_other_constraints_file) && filesize($tab_other_constraints_file)!==0)
			{
				foreach($attr_array as $attr)
				{
					$constraint_count=3;
					$arr=file($tab_other_constraints_file);
					foreach($arr as $other_constraint)
					{
						if($constraint_count===0)
							break;
						if(strpos($other_constraint, "->".$attr."->")!==FALSE)
						{
							if(strpos($other_constraint, "FK->")===0)
							{
								//getting the reference information.
								$reference=substr($other_constraint, (strrpos($other_constraint, "->")+2), (strlen($other_constraint)-(strrpos($other_constraint, "->")+2)-1) );
								$reference_db=substr($reference, 0, strpos($reference, "."));
								$reference=substr($reference, strpos($reference, ".")+1);
								$reference_tab=substr($reference, 0, strpos($reference, "."));
								$reference=substr($reference, strpos($reference, ".")+1);
								$reference_attr=$reference;

								//formatting $reference_db, $reference_tab, $reference_attr for display
								$formatted_reference="<font color=DeepPink size=6>$reference_db</font>.<font color=Green size=5>$reference_tab</font>.<font color=DarkViolet size=4>$reference_attr</font>\
														<br>\
														<font size=2>	<b>(<font color=DeepPink>DB</font>.<font color=Green>TAB</font>.<font color=DarkViolet>ATTR</font>)</b>	</font>";

								echo "<script>
										document.getElementById('".$attr."_fk').innerHTML='".$formatted_reference."';
									</script>";
								$constraint_count--;
							}
							if(strpos($other_constraint, "DEFAULT->")===0)
							{
								echo "<script>
										document.getElementById('".$attr."_default').innerHTML='".substr($other_constraint, (strrpos($other_constraint, "->")+2), (strlen($other_constraint)-(strrpos($other_constraint, "->")+2)-1) )."';
									</script>";
								$constraint_count--;
							}
							if(strpos($other_constraint, "NOT_NULL->")===0)
							{
								echo "<script>
										document.getElementById('".$attr."_not_null').innerHTML=\"<img src='images/tick_icon3.png' height=20 width=20/>\";
									</script>";
								$constraint_count--;
							}
						}
					}
					//Checking whether the $attr is PK_attr. In that case the NOT_NULL field for that attr will be checked.
					$tab_constraints_file="databases/$_SESSION[uname]/$_SESSION[db_selected]/$_SESSION[tab_selected]"."_constraints.knrs";
					if(file_get_contents($tab_constraints_file)=="PK->$attr")
					{
						echo "<script>
								document.getElementById('".$attr."_not_null').innerHTML=\"<img src='images/tick_icon3.png' height=20 width=20/>\";
							</script>";
					}
				}
			}
		?>
	</body>
</html>