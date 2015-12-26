<html>

	<?php
		if(!isset($_SESSION))
			session_start();
		$tab_schema_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_schema.knrs";
		$tab_other_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_other_constraints.knrs";
		$attr=$_REQUEST['attr'];
		$attr_type=$_REQUEST['attr_type'];
		$attr_size=$_REQUEST['attr_size'];

		//echo "<script>alert('".$attr_type."');</script>";//

		echo "<script>
				try
				{
					var current_db='".$_SESSION['db_selected']."';
					var current_tab='".$_SESSION['tab_selected']."';
					var current_attr='".$attr."';
				}
				catch(err)
				{
					alert(err);
				}
			</script>";
	?>

	<head>

		<script>

			function isNumberKey(evt,id)
			{
				try
				{
					var charCode = (evt.which) ? evt.which : event.keyCode;
					if (charCode==13 || (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)))
					{
						return false;
					}
					return true;
				}
				catch(err)
				{alert(err.message);}
			}

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

			function activate_select_tab()
			{
				try
				{
					var db_selected=document.getElementById("select_db").value;
					var select_tab_element=document.getElementById('select_tab');
					var select_attr_element=document.getElementById('select_attr');
					obj=pullAjax();
					obj.onreadystatechange=function()
					{
						if(obj.readyState==4)
						{
							eval("response="+obj.responseText);
							var select_options="<option>-select-";
							for(i=0; i<response.length; i++)
							{
								select_options+="<option>"+response[i];
							}
							select_tab_element.disabled=false;
							select_attr_element.disabled=true;
							select_tab_element.innerHTML="";
							select_tab_element.innerHTML=select_options;
						}
					}
					obj.open("GET","activate_select_tab.php?db_selected="+db_selected, true);
					obj.send(null);
				}
				catch(err)
				{
					alert(err);
				}
			}

			function activate_select_attr()
			{
				try
				{
					var db_selected=document.getElementById("select_db").value;
					var tab_selected=document.getElementById("select_tab").value;
					var select_attr_element=document.getElementById('select_attr');
					obj=pullAjax();
					obj.onreadystatechange=function()
					{
						if(obj.readyState==4)
						{
							eval("response="+obj.responseText);
							var select_options="<option>-select-";

							//the following condition check is for eleminating the option for the same attribute as that of which constraints are being modified. This'll occur in case the attribute whose constraints are being modified ids the PK_attr.
							if(!(current_db==db_selected && current_tab==tab_selected && current_attr==response['pk_attr']))
								select_options+="<option>"+response['pk_attr'];

							select_attr_element.disabled=false;
							select_attr_element.innerHTML=select_options;
						}
					}
					obj.open("GET","activate_select_attr.php?db_selected="+db_selected+"&tab_selected="+tab_selected, true);
					obj.send(null);
				}
				catch(err)
				{
					alert(err);
				}
			}

		</script>
	
	</head>
	
	<body bgcolor=AliceBlue>
		<?php
			echo "<span style=\"\"> <u><h3>TABLE : <font color=CornflowerBlue>".$_SESSION['tab_selected']."</font><font size=2 face='lucida fax'>(constraint-update)</font></h3></u> </span>";
			echo "<u><h4>ATTRIBUTE : <font color=victor>".$attr."</font></h4></u>";
		?>

		<form action="modify_attribute_constraint.php" method="POST">

			<input type=hidden name="attr" id="attr" value=<?php	echo $attr;	?>>
			<input type=hidden name="attr_type" id="attr_type" value=<?php	echo $attr_type;	?>>

			<table width=100% border=1 cellpadding=15px style="table-layout:fixed;">
				<tr>
					<td>
						<b>REFERENCES:</b><br>
						<table style="table-layout:fixed;" width=100% align=center>
							<tr>

								<?php
									$reference="";
									$arr=file($tab_other_constraints_file);
									foreach($arr as $other_constraint)
									{
										if(strpos($other_constraint, "FK->".$attr)===0)
										{
											$reference=substr($other_constraint, strlen("FK->".$attr."->"), strlen($other_constraint)-strrpos($other_constraint, "->")-2-1);
											break;
										}
									}
									if($reference!="")
									{
										$reference_db=substr($reference, 0, strpos($reference, "."));
										$reference=substr($reference, strpos($reference, ".")+1);
										$reference_tab=substr($reference, 0, strpos($reference, "."));
										$reference=substr($reference, strpos($reference, ".")+1);
										$reference_attr=substr($reference, 0, strlen($reference));
									}
									else
									{
										$reference_db="";
										$reference_tab="";
										$reference_attr="";
									}
								?>

								<td align=center>
									<u>ATTRIBUTE</u>
									<br>
									<select id="select_attr" name="select_attr" onchange="" onFocusOut="if(this.value=='-select-'){alert('You must select an attribute to set the reference.')}">
										<?php
											if($reference_attr!="")
											{
												echo "<option>-select-";
												echo "<option selected>$reference_attr";
											}
											else
											{
												echo "<script>document.getElementById('select_attr').disabled=true;</script>";
											}
										?>
									</select>
								</td>

								<td align=center>
									<u>TABLE</u>
									<br>
									<select id="select_tab" name="select_tab" onFocusOut="if(this.value=='-select-'){alert('You must select a table to set the reference.')}" onchange="try{if(this.value=='-select-'){document.getElementById('select_attr').innerHTML=''; document.getElementById('select_attr').disabled=true;} else{activate_select_attr();}} catch(err){alert(err);}">
										<?php
											if($reference_db!="")
											{
												echo "<option>-select-";
												$pk_table_array=array();
												$arr=file("databases/$_SESSION[uname]/$reference_db/table_list.knrs");
												foreach($arr as $tab_name)
												{
													if(strpos(file_get_contents("databases/$_SESSION[uname]/$reference_db/".(substr($tab_name, 0, strlen($tab_name)-1))."_constraints.knrs"), "PK->")===0)
														$pk_table_array[]=substr($tab_name, 0, strlen($tab_name)-1);//omitting the last character i.e "\n" from $tab_name by using substr().
												}
												foreach($pk_table_array as $content)
												{
													echo "<option".(($reference_tab!="" && $reference_tab==$content)? " selected":"").">$content";
												}
											}
											else
											{
												echo "<script>document.getElementById('select_tab').disabled=true;</script>";
											}
										?>
									</select>
								</td>

								<td align=center>
									<u>DATABASE</u>
									<br>
									<select id="select_db" name="select_db" onFocusOut="if(this.value=='-select-'){alert('You must select a database to set the reference.')}" onchange="try{if(this.value=='-select-'){document.getElementById('select_attr').innerHTML=''; document.getElementById('select_tab').innerHTML=''; document.getElementById('select_attr').disabled=true; document.getElementById('select_tab').disabled=true;} else{activate_select_tab();}} catch(err){alert(err);}">
										<option selected>-select-
										<?php
											if(strpos(file_get_contents($tab_schema_file), $attr."->file->")===0 || strpos(file_get_contents($tab_schema_file), $attr."->file->")!==FALSE)
												echo "<script>
														document.getElementById('select_db').innerHTML='';
														document.getElementById('select_db').disabled=true;
													</script>";
											else
											{
												$arr_dir_content=scandir("databases/$_SESSION[uname]");
												foreach($arr_dir_content as $content)
												{
													if($content!="." && $content!="..")
														echo "<option".(($reference_db!=="" && $reference_db==$content)? " selected":"").">$content";
												}
											}
										?>
									</select>
								</td>

							</tr>
						</table>
				</tr>
				<tr>
					<td>
						<?php
							$arr=file($tab_other_constraints_file);
							foreach($arr as $other_constraint)
							{
								if(strpos($other_constraint, "NOT_NULL->".$attr."->")===0)
								{
									$not_null=true;
									break;
								}
							}
						?>
						<b>NOT NULL:</b>
						<select id="not_null" name="not_null">
							<?php
								$tab_constraints_file="databases/$_SESSION[uname]/$_SESSION[db_selected]/$_SESSION[tab_selected]"."_constraints.knrs";
								if(file_get_contents($tab_constraints_file)=="PK->$attr")
								{
									echo "<script>document.getElementById('not_null').disabled=true</script>";
									echo "<option>TRUE";
								}
								else
								{
									echo "<option ".(isset($not_null)? "selected": "").">TRUE
										<option ".(!isset($not_null)? "selected": "").">FALSE";
								}
							?>
						</select>
					</td>
				</tr>

				<tr>
					<td>
						<?php
							$arr=file($tab_other_constraints_file);
							foreach($arr as $other_constraint)
							{
								if(strpos($other_constraint, "DEFAULT->".$attr."->")===0)
								{
									$default_val=substr($other_constraint, (strrpos($other_constraint, "->")+2), (strlen($other_constraint)-(strrpos($other_constraint, "->")+2)-1));
									break;
								}
							}
							if(!isset($default_val))
								$default_val="";
						?>
						<b>DEFAULT VALUE:</b>
						<input type=text id="default_val" name="default_val" maxlength=<?php	echo $attr_size;	?>	onKeyPress=<?php	echo ($attr_type=="number"? "'return isNumberKey(event, this.id);'": "''");	?>	value=<?php	echo ($attr_type=="file"? "'Inapplicable'": "'".$default_val."'");//In case of file type attr the default value textbox should have the text "Inapplicable". But for some reason it's not showing.	?>	<?php	echo ($attr_type=="file"? "disabled title='Not Applicable On Files'": "");	?>>
				</tr>

				<tr>
					<td align=center>
						<input type=submit value="SUBMIT">
				</tr>

			</table>

		</form>
	</body>