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
	<body bgcolor="aliceblue">
		<center>

		<?php
			$separater_string="#$**$#";
			session_start();
			if(!isset($_REQUEST['record_id_update']) || $_REQUEST['record_id_update']==NULL)
			{
				die ("<script language='javascript'>location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=data'</script>");
			}
			else
			{
				echo "</center>";
				echo "<u><h3>TABLE : <font color=CornflowerBlue>".$_SESSION['tab_selected']."</font><font size=2 face='lucida fax'>(update)</font></h3></u>";
				echo "<center>";
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
				echo "<form enctype=\"multipart/form-data\" action=\"update_record.php\" method=\"POST\" onsubmit=\"return validate();\">";
				echo "<input type=hidden id=\"no_attr\" name=\"no_attr\">";
				echo "<input type=hidden id=\"pk_attr\" name=\"pk_attr\">";
				echo "<input type=hidden id=\"record_id_update\" name=\"record_id_update\" value='".$_REQUEST['record_id_update']."'>";
				echo "<table>";
				foreach($arr as $attr_details)
				{
					$attr=substr($attr_details,0,strpos($attr_details,'->'));
					$attr_details=substr($attr_details,strpos($attr_details,'->')+2);
					$attr_type=substr($attr_details,0,strpos($attr_details,'->'));
					$attr_details=substr($attr_details,strpos($attr_details,'->')+2);
					$attr_size=$attr_details;
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
								<select id='attr".++$no_attr."' name='attr".$no_attr."'>";
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
						echo "<input type=hidden name='attr_existing_val".$no_attr."' id='attr_existing_val".$no_attr."'>";//for getting the existing value of an attribute.
						if($attr_type!='file')
							echo "<input type=hidden id='file_true".$no_attr."' name='file_true".$no_attr."' value='0'>";
						echo "</tr>";
						echo "<script language=\"javascript\">try{++n;} catch(err){alert(err.message)}</script>";
					}
					else
					{
						echo "<tr>
								<td>$attr";
						if($attr==$pk_attr)
							$pk_attr=$no_attr+1;
						if($attr_type=='number')
						{
							if($attr_size<=20)
								echo "<td><input type=text maxlength='".$attr_size."' id='attr".++$no_attr."' name='attr".$no_attr."' onfocus=\"try{if(num_textbox_id.indexOf(this.id) == -1){num_textbox_id.push(this.id); num_attr_wrong[this.id]='".$attr."';}} catch(err){alert(err.message)}\" onkeypress=\"return isNumberKey(event,this.id)\">";
							else
								echo "<td><textarea maxlength='".$attr_size."' cols=25 rows='".(string)(2+($attr_size/27))."' id='attr".++$no_attr."' name='attr".$no_attr."' onfocus=\"try{if(num_textbox_id.indexOf(this.id) == -1){num_textbox_id.push(this.id); num_attr_wrong[this.id]='".$attr."'}} catch(err){alert(err.message)}\" onkeypress=\"return isNumberKey(event,this.id)\"></textarea>";
							echo "<input type=hidden name='attr_existing_val".$no_attr."' id='attr_existing_val".$no_attr."'>";//for getting the existing value of an attribute.
						}
						else if($attr_type=='char')
						{
							if($attr_size<=20)
								echo "<td><input type=text maxlength='".$attr_size."' id='attr".++$no_attr."' name='attr".$no_attr."'>";
							else
								echo "<td><textarea maxlength='".$attr_size."' cols=25 rows='".(string)(2+($attr_size/27))."' id='attr".++$no_attr."' name='attr".$no_attr."'></textarea>";
							echo "<input type=hidden name='attr_existing_val".$no_attr."' id='attr_existing_val".$no_attr."'>";//for getting the existing value of an attribute.
						}
						else
						{
							echo "<td>
									<input type=file id='attr".++$no_attr."' name='attr".$no_attr."' style=\"display:none\" onchange=\"document.getElementById('file_name".$no_attr."').innerHTML=this.value.substr(this.value.lastIndexOf('\\\')+1);\">
									<input type=button id='file_button".$no_attr."' name='file_button".$no_attr."' value='Choose File' onclick=\"document.getElementById('attr".$no_attr."').click()\"> <span id='file_name".$no_attr."' name='file_name".$no_attr."'>No File Chosen</span>
									<input type=hidden id='file_current_name".$no_attr."' name='file_current_name".$no_attr."' value=''>
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
				echo "<script language=\"javascript\">try{document.getElementById('no_attr').value=n;} catch(err){alert(err.message)}</script>";
				echo "<script language=\"javascript\">document.getElementById('pk_attr').value='".$pk_attr."'</script>";
				echo "<tr>";
				echo "<td><input type=submit value='UPDATE'>";
				echo "<td><input type=button onclick=\"history.go(-1);\" value=\"CANCEL\">";
				echo "</table>";
				echo "</form>";
				
				//setting the contents of the text-fields with existing contents in the database.
				$record_id_update=$_REQUEST['record_id_update'];
				$arr=file($tab_data_file);
				$line=$arr[$record_id_update-1];//index of the record marked is ($record_id_update-1).
				//tracing each attribute value.
				$separater_string="#$**$#";
				$pos1=0;
				$pos2=0;
				$attr_no=0;
				$substr1=$line;
				$substr1=substr($substr1,6,strlen($substr1)-6);
				while(($pos2+strlen($separater_string))<=strlen($substr1))
				{
					$attr_no++;
					$pos1=strpos($substr1,$separater_string);
					$data=substr($substr1,0,$pos1);

					$offset=0;
					$old='<br>';
					$new='\n';
					$tmpOldStrLength = 4;
					while (($offset = strpos($data, $old, $offset)) !== FALSE)
					{
						$data = substr_replace($data, $new, $offset, $tmpOldStrLength);
					}
					$offset = strpos($data, $old);
					if($offset!==FALSE)
						$data = substr_replace($data, $new, $offset, $tmpOldStrLength);

					echo "<script language='javascript'>try{/*alert('".$data."');*/ if(document.getElementById('attr".$attr_no."').type!='file'){document.getElementById('attr".$attr_no."').value='".$data."'; document.getElementById('attr_existing_val".$attr_no."').value='".$data."';}else{if('".$data."'!=''){document.getElementById('file_name".$attr_no."').innerHTML=\"".$data."\"; document.getElementById('file_current_name".$attr_no."').value='".$data."'}}} catch(err){alert(err.message);}</script>";
					$substr1=substr($substr1,$pos1+strlen($separater_string),(strlen($substr1)-($pos1+strlen($separater_string))));
					if($substr1=="")
						break;
					$pos2=strpos($substr1,$separater_string);
				}
				echo "</tr>";
			}
		?>

		</center>
	</body>
</html>