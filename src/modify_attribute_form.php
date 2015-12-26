<html>
	<head>
		<style>
			span.invisible
			{
				display:none;
			}
			span.visible
			{
				background:cyan;
				color:blue;
				font-family:lucida fax;
				font-size:15;
			}
			input[type=text].tooltip_enabled span.visible
			{
				display:inline;
			}
		</style>
		<script type="text/javascript">
			//var n=0;
			var attr_list=new Array();
			function isNumberKey(evt,id)
			{
				try
				{
					var charCode = (evt.which) ? evt.which : event.keyCode;
					if ((charCode != 46 && charCode > 31 && (charCode < 35 || charCode > 40) && (charCode < 48 || charCode > 57)))
					{
						return false;
					}
					return true;
				}
				catch(err)
				{alert(err.message);}
			}
			function array_contains(a, obj)
			{
				for (var i=0; i<a.length; i++)
				{
					if (a[i].toUpperCase() === obj.toUpperCase())
					{
						return a[i];
					}
				}
				return false;
			}
			function validate()
			{
				try
				{
					if(document.getElementById('attr').value=="")
					{
						document.getElementById('attr_blank').className="visible";
						document.getElementById('attr').className="tooltip_enabled";
						document.getElementById('attr').focus();
						return false;
					}
					else
					{
						if(document.getElementById('attr').value!==document.getElementById('attr_prev').value)
						{
							if(array_contains(attr_list,document.getElementById('attr').value.trim())!==false)
							{
								document.getElementById('attr_duplicate').innerHTML="Duplicate AttributeName:\"<font color=darkblue>"+array_contains(attr_list,document.getElementById('attr').value.trim())+"</font>\". Try another name.";
								document.getElementById('attr_duplicate').className="visible";
								document.getElementById('attr').className="tooltip_enabled";
								document.getElementById('attr').focus();
								document.getElementById('attr').select();
								return false;
							}
						}
					}
					if(document.getElementById('attr_size').value=="")
					{
						document.getElementById('attr_size_blank').className="visible";
						document.getElementById('attr_size').className="tooltip_enabled";
						document.getElementById('attr_size').focus();
						return false;
					}
					return true;
				}
				catch(err)
				{
					alert(err.message);
				}
			}
			function isAlphaNumberUnderscoreKey(evt,id)
			{
				try
				{
					var charCode = (evt.which) ? evt.which : event.keyCode;
					if (charCode ==9 || charCode ==13 || charCode ==95 || (charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122))
					{
						return (true);
					}
					alert("Only Underscore(_), Number(0-9), Alphabets(A-Z/a-z) are allowed.");
					return (false);
				}
				catch(err){alert(err.message);}
			}
		</script>
	</head>
	<body bgcolor="aliceblue">
		<center>

		<?php
			if(!isset($_SESSION))
				session_start();
			if(!isset($_REQUEST['attribute_id_update']) || $_REQUEST['attribute_id_update']==NULL)
			{
				die ("<script language='javascript'>location.href='table_action.php?tab_selected=".$_SESSION['tab_selected']."&view=data'</script>");
			}
			else
			{
				echo "</center>";
				echo "<span style=\"\"> <u><h3>TABLE : <font color=CornflowerBlue>".$_SESSION['tab_selected']."</font><font size=2 face='lucida fax'>(structure-update)</font></h3></u> </span>";
				echo "<center>";

				$attr_no=$_REQUEST['attribute_id_update'];
				//attribute's sequence no.

				$pk_attr=$_REQUEST['pk'];
				//if pk then 1, else 0.

				$tab_data_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data.knrs";
				$tab_schema_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_schema.knrs";
				$tab_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_constraints.knrs";

				$arr=file($tab_schema_file);
				$attr_no_counter=0;
				foreach($arr as $attr_details)//accessing each attribute details line-by-line.
				{
					$attr_no_counter++;
					if($attr_no_counter==$attr_no)
					{
						$arr=file($tab_schema_file);

						echo "<form action=\"modify_attribute.php\" method=\"POST\" onsubmit=\"return validate();\">";
						echo "<input type=hidden id=\"attribute_no_update\" name=\"attribute_no_update\" value='".$_REQUEST['attribute_id_update']."'>";
						echo "<input type=hidden id=\"pk_true\" name=\"pk_true\" value='".$pk_attr."'>";
						echo "<table border=0 style=\"table-layout:fixed;\" width=80%>";

						$attr=substr($attr_details,0,strpos($attr_details,'->'));
						$attr_details=substr($attr_details,strpos($attr_details,'->')+2);
						$attr_type=substr($attr_details,0,strpos($attr_details,'->'));
						$attr_details=substr($attr_details,strpos($attr_details,'->')+2);
						$attr_size=$attr_details;
						echo "<tr>
								<td align=right>AttributeName
								<td align=left><input type=text id='attr' name='attr' value=".$attr." maxlength=255 onKeyPress=\"return isAlphaNumberUnderscoreKey(event,this.id);\" onKeyDown=\"this.className='tooltip_enabled'; document.getElementById('attr_blank').className='invisible'; document.getElementById('attr_duplicate').className='invisible';\">
								<td align=left><span id='attr_blank' class='invisible'>AttributeName kept blank. Fill it up.</span>
									<span id='attr_duplicate' class='invisible'>j</span>
							</tr>";
						echo "<tr>
								<td align=right>DataType
								<td align=left>
									<select id='attr_type' name='attr_type'".($attr_type=="file"?" disabled title='A file-type attribute cannot be changed to any other type.' value='file'":"").">
										<option".($attr_type=="number"?" selected":"").">number
										<option".($attr_type=="char"?" selected":"").">char
										".($attr_type=="file"?"<option selected>file":"")."
									</select>
							</tr>";
						echo "<tr>
								<td align=right>Size
								<td align=left><input class='tooltip_disabled' type=text ".($attr_type=="file"?"maxlength=8 size=10":"maxlength=3")." id='attr_size' name='attr_size' value=".$attr_size." onKeyDown=\"this.className='tooltip_enabled'; document.getElementById('attr_size_blank').className='invisible'; return isNumberKey(event,this.id);\">".($attr_type=="file"?"KB":"")."
								<td align=left><span id='attr_size_blank' class='invisible'>Attribute Size kept blank. Fill it up.</span>
							</tr>";
						echo "<tr>
								<td align=right><input type=submit Value='Modify'>
								<td align=left><input type=button style='cursor:default' onclick=\"history.go(-1);\" value=Cancel>
							</tr>";
						echo "</table>";
						echo "<input type=hidden name='attr_prev' id='attr_prev' value=$attr>";
						echo "<input type=hidden name='attr_type_prev' id='attr_prev_type' value=$attr_type>";
						echo "<input type=hidden name='attr_size_prev' id='attr_prev_size' value=$attr_size>";
						echo "</form>";
					}
					else
					{
						echo "<script>attr_list.push('".substr($attr_details,0,strpos($attr_details,'->'))."');</script>";
					}
				}
			}
		?>

		</center>
	</body>
</html>