<html>
	<head>
		<script type="text/javascript">
			var n=0;
			var num_textbox_id=new Array();
			var num_attr_wrong=new Array();
			function isNumberKey(evt,id)
			{
				try
				{
					var charCode = (evt.which) ? evt.which : event.keyCode;
					if (charCode==13 || (charCode != 46 && charCode != 45 && charCode > 31 && (charCode < 48 || charCode > 57)))
					{
						return false;
					}
					if(charCode == 45)
					{
						if(document.getElementById(id).value.indexOf('-')!=-1)
							return false;
					}
					if(charCode == 46)
					{
						if(document.getElementById(id).value.indexOf('.')!=-1)
							return false;
					}
					return true;
				}
				catch(err)
				{alert(err.message);}
			}
			function validate()
			{
				try
				{
					var id;
					var alrt='';
					for(var i=0;i<num_textbox_id.length;i++)
					{
						id=num_textbox_id[i];
						if(document.getElementById(id.toString()).value!="" && (isNaN(document.getElementById(id.toString()).value) || (document.getElementById(id.toString()).value.indexOf('-')!=-1 && document.getElementById(id.toString()).value.indexOf('-')!=0) || document.getElementById(id.toString()).value.indexOf('.')==(document.getElementById(id.toString()).value.length-1)))
						{
							alert("Enter proper numeric value in the number-type attribute : "+num_attr_wrong[id]);
							return (false);
						}
					}
					return (true);
				}
				catch(err)
				{
					alert(err.message);
				}
			}
		</script>
	</head>
	<body>
		<center>
		<?php
			$separater_string="#$**$#";
			$no_attr=0;
			$pk_attr=NULL;
			$tab_data_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data.knrs";
			$tab_schema_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_schema.knrs";
			$tab_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_constraints.knrs";
			$tab_other_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_other_constraints.knrs";
			$arr=file($tab_constraints_file);
			foreach($arr as $constraint)
			{
				if(strpos($constraint,'PK')!=0)
					continue;
				else
				{
					$pk_attr=substr($constraint,4);
					break;
				}
			}
			$arr=file($tab_schema_file);
			echo "<form enctype=\"multipart/form-data\" action=\"write_table_data.php\" method=\"POST\" onsubmit=\"return validate();\">";
			echo "<input type=hidden id=\"no_attr\" name=\"no_attr\">";
			echo "<input type=hidden id=\"pk_attr\" name=\"pk_attr\">";
			echo "<table>";
			foreach($arr as $attr_details)
			{
				$attr=substr($attr_details,0,strpos($attr_details,'->'));
				$attr_details=substr($attr_details,strpos($attr_details,'->')+2);
				$attr_type=substr($attr_details,0,strpos($attr_details,'->'));
				$attr_details=substr($attr_details,strpos($attr_details,'->')+2);
				$attr_size=substr($attr_details, 0, strlen($attr_details)-1);
				if(strpos(file_get_contents($tab_other_constraints_file), "FK->".$attr."->")!==false)
				{
					echo "<tr>
							<td>$attr";
					if($attr==$pk_attr)
						$pk_attr=$no_attr+1;
					if(strpos(file_get_contents($tab_other_constraints_file), "NOT_NULL->".$attr."->")===false && file_get_contents($tab_constraints_file)!=="PK->".$attr)
						$accept_null=1;
					else
						$accept_null=0;
					echo "<td>
							<select id='attr".++$no_attr."' name=attr".$no_attr.">";
					if($accept_null===1)
						echo "<option selected>";
					
					$arr1=file($tab_other_constraints_file);
					//tracing the primary attribute for the current attribute.
					foreach($arr1 as $other_constraints)
					{
						if(strpos($other_constraints, "FK->".$attr."->")!==false)
						{
							$other_constraints=substr($other_constraints, strrpos($other_constraints, "->")+2);
							$primary_db_tab_attr=substr($other_constraints, 0, strlen($other_constraints));
							break;
						}
					}
					$primary_db=substr($primary_db_tab_attr, 0, strpos($primary_db_tab_attr, "."));
					$primary_db_tab_attr=substr($primary_db_tab_attr, strpos($primary_db_tab_attr, ".")+1);
					$primary_tab=substr($primary_db_tab_attr, 0, strpos($primary_db_tab_attr, "."));
					$primary_db_tab_attr=substr($primary_db_tab_attr, strpos($primary_db_tab_attr, ".")+1);
					$primary_attr=substr($primary_db_tab_attr, 0, strlen($primary_db_tab_attr)-1);
					
					$primary_tab_schema_file="databases/$_SESSION[uname]/$primary_db/$primary_tab"."_schema.knrs";
					$primary_tab_data_file="databases/$_SESSION[uname]/$primary_db/$primary_tab"."_data.knrs";
					//For tracing the attr_no of the primary attr.
					$arr1=file($primary_tab_schema_file);
					$i=0;
					foreach($arr1 as $primary_tab_attr_schema)
					{
						$i++;
						if(strpos($primary_tab_attr_schema, $primary_attr."->")===0)
							break;
					}
					
					//now getting the list of values of the primary attr.
					$arr1=file($primary_tab_data_file);
					foreach($arr1 as $primary_tab_record)
					{
						$i1=1;
						while($i1<$i)
						{
							$primary_tab_record=substr($primary_tab_record, strlen($separater_string));
							$primary_tab_record=substr($primary_tab_record, strpos($primary_tab_record, $separater_string));
							$i1++;
						}
						$primary_tab_record=substr($primary_tab_record, strlen($separater_string));
						$primary_data=substr($primary_tab_record, 0, strpos($primary_tab_record, $separater_string));
						if(strlen($primary_data)<=$attr_size)//giving options for those primary-attr values whose length are within the size of this attribute
						{
							if($attr_type!="number" || ($attr_type=="number" && (is_numeric($primary_data) && strpos($primary_data, "e")===FALSE)))//if "string" type then allow all the values, but if "number" then allow only integer.
								echo "<option>".$primary_data;
						}
					}
					echo "</select>";
					if($attr_type!='file')
						echo "<input type=hidden id='file_true".$no_attr."' name='file_true".$no_attr."' value='0'>";
					echo "</tr>";
					echo "<script language=\"javascript\">try{++n;} catch(err){alert(err.message)}</script>";
				}
				else
				{
					if(isset($other_constraint))
						unset($other_constraint);
					if(strpos(file_get_contents($tab_other_constraints_file), "\nDEFAULT->".$attr."->")!==false || strpos(file_get_contents($tab_other_constraints_file), "DEFAULT->".$attr."->")===0)
					{
						$arr1=file($tab_other_constraints_file);
						foreach($arr1 as $other_constraint)
						{
							if(strpos($other_constraint, "DEFAULT->".$attr."->")===0)
								break;
						}
					}
					if(!isset($other_constraint))
						$other_constraint="";
					else
					{
						$other_constraint=substr($other_constraint, 0, strlen($other_constraint)-1);
						$other_constraint=substr($other_constraint, strrpos($other_constraint, "->")+2);
					}
					echo "<tr>
							<td>$attr";
					if($attr==$pk_attr)
						$pk_attr=$no_attr+1;
					if($attr_type=='number')
					{
						if($attr_size<=20)
							echo "<td><input type=text maxlength='".$attr_size."' id='attr".++$no_attr."' name=attr".$no_attr." onfocus=\"try{if(num_textbox_id.indexOf(this.id) == -1){num_textbox_id.push(this.id); num_attr_wrong[this.id]='".$attr."';}} catch(err){alert(err.message)}\" onkeypress=\"return isNumberKey(event,this.id)\" value=\"".$other_constraint."\">";
						else
							echo "<td><textarea maxlength='".$attr_size."' cols=25 rows='".(string)(2+($attr_size/27))."' id='attr".++$no_attr."' name=attr".$no_attr." onfocus=\"try{if(num_textbox_id.indexOf(this.id) == -1){num_textbox_id.push(this.id); num_attr_wrong[this.id]='".$attr."'}} catch(err){alert(err.message)}\" onkeypress=\"return isNumberKey(event,this.id)\">".$other_constraint."</textarea>";
					}
					else if($attr_type=='char')
					{
						if($attr_size<=20)
							echo "<td><input type=text maxlength='".$attr_size."' id='attr".++$no_attr."' name='attr".$no_attr."' value=\"".$other_constraint."\">";
						else
							echo "<td><textarea maxlength='".$attr_size."' cols=25 rows='".(string)(2+($attr_size/27))."' id='attr".++$no_attr."' name='attr".$no_attr."'>".$other_constraint."</textarea>";
					}
					else
					{
						echo "<td>
								<input type=file id='attr".++$no_attr."' name='attr".$no_attr."'>
								<input type=hidden id='file_max_size".$no_attr."' name='file_max_size".$no_attr."' value=\"".$attr_size."\">
								<input type=hidden id='file_true".$no_attr."' name='file_true".$no_attr."' value='1'>";
					}
					if($attr_type!='file')
						echo "<input type=hidden id='file_true".$no_attr."' name='file_true".$no_attr."' value='0'>";
					echo "</tr>";
					echo "<script language=\"javascript\">try{++n;} catch(err){alert(err.message)}</script>";
				}
			}
			$_SESSION['no_attr']=$no_attr;
			echo "</table>";
			echo "<script language=\"javascript\">try{document.getElementById('no_attr').value=n;} catch(err){alert(err.message)}</script>";
			echo "<script language=\"javascript\">document.getElementById('pk_attr').value='".$pk_attr."'</script>";
			echo "<input type=submit value=\"INSERT\">";
			echo "<input type=button onclick=\"location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=data'\" value=\"CANCEL\">";
			echo "</form>";
		?>
		</center>
	</body>
</html>