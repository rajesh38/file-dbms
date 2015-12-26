<html>
<head>
	<script>

		function isNumberKey(evt,id)
		{
			try
			{
				var charCode = (evt.which) ? evt.which : event.keyCode;
				if (charCode == 46 || charCode > 31 && (charCode < 48 || charCode > 57))
				{
					return false;
				}
				return true;
			}
			catch(err)
			{alert(err.message);}
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
				alert("Only Underscore(_), Number(0-9), Alphabets(A-Z/a-z) are allowed."+charCode);
				return (false);
			}
			catch(err){alert(err.message);}
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

		function add_new_attr()
		{
			try
			{
				var attr_name=document.getElementById('add_attr_name').value;
				var attr_type=document.getElementById('add_attr_type').value;
				var attr_size=document.getElementById('add_attr_size').value;
				if(attr_name=="" || attr_size=="")
				{
					alert("Fill up all relevant information.");
					return false;
				}
				obj=pullAjax();
				obj.onreadystatechange=function()
				{
					if(obj.readyState==4)
					{
						eval("response = "+obj.responseText);
						if(response['status']===0)
						{
							alert("Aborted:There is another attribute with the same name.");
							return false;
						}
						else
						{
							location.reload();
						}
					}
				}
				obj.open("GET","add_new_attr.php?attr_name="+attr_name+"&attr_type="+attr_type+"&attr_size="+attr_size,true);
				obj.send(null);
			}
			catch(err)
			{
				alert(err);
			}
		}

	</script>
</head>
<body>
	<center>
	<br><br>
	<table border=1 bgcolor=cyan width='100%' style="table-layout:fixed">
		<tr>
			<th bgcolor=turquoise width='70px'>DROP
			<th bgcolor=turquoise width='70px'>MODIFY
			<th bgcolor=turquoise width=90>PRIMARY<br>KEY
			<th><font size=4 face='lucida fax'><b>ATTRIBUTE</b></font>
			<th><font size=4 face='lucida fax'><b>DATATYPE</b></font>
			<th><font size=4 face='lucida fax'><b>SIZE</b></font>
		</tr>
<?php
	if(!isset($_SESSION))
		session_start();
	$tab_schema_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_schema.knrs";
	$tab_constraints_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_constraints.knrs";
	$arr=file($tab_constraints_file);
	$_SESSION['pk_count']=0;
	foreach($arr as $row)
	{
		if(substr($row,0,4)=="PK->")
		{
			$_SESSION['pk_count']++;
			$_SESSION['pk'.$_SESSION['pk_count']]=substr($row,4);
		}
	}
	$row_no=0;
	$arr=file($tab_schema_file);
	foreach($arr as $row)
	{
		$row_no++;
		$attr_pk=0;
		$attr=substr($row,0,strpos($row,'->'));
		$row=substr($row,strpos($row,'->')+2);
		$data_type=substr($row,0,strpos($row,'->'));
		$size=substr($row,strpos($row,'->')+2);
		for($i=1;$i<=$_SESSION['pk_count'];$i++)
		{
			if($_SESSION['pk'.$i]==$attr)
			{
				$attr_pk=1;
			}
		}
		echo "<tr>";
		echo "<td bgcolor=turquoise align=center><div style=\"cursor:pointer\" title=\"Drop this attribute\" onclick=\"if(confirm('Are you sure to delete this record?')==false){return false;} else{location.href='drop_attribute.php?attribute_id_drop=".$row_no."&pk_true=".$attr_pk."';}\"><font color=red><img src='images/delete_object1.jpg' width=50%></font></div> </td>";
		echo "<td bgcolor=turquoise align=center><div style=\"cursor:pointer\" title=\"Modify this attribute\" onclick=\"{location.href='modify_attribute_form.php?attribute_id_update=".$row_no."&pk=".$attr_pk."';}\"><font color=red><img src='images/edit_object2.jpg' width=50%></font></div> </td>";
		echo "<td bgcolor=turquoise align=center>";
		if($attr_pk==1)
		{
			echo "<img src='images/pk2.gif' height=40px style='cursor:pointer' title=\"Click to disable primary key from this attribute.\" onclick=\"if(confirm('Do you want to disable the primary key from this attribute?')){location.href='toggle_pk.php?pk=true'}\">";//pk=true becoz it is the current Primary Key.
		}
		else
		{
			echo "<div style=\"cursor:pointer; border:0px solid; width:100%, height:100%;\" title=\"Click to apply primary key on this attribute.\" onclick=\"if(confirm('Do you want to apply the primary key on this attribute?')){location.href='toggle_pk.php?pk=false&attr=".$attr."&attr_no=".$row_no."'}\">&nbsp</div>";
		}
		echo "<td align=center style=\"word-wrap: break-word;\"><font size=4 face='lucida fax'>$attr</font>";
		echo "<td align=center style=\"word-wrap: break-word;\"><font size=4 face='lucida fax'>$data_type</font>";
		echo "<td align=center style=\"word-wrap: break-word;\"><font size=4 face='lucida fax'>$size".($data_type=="file"?"(KB)":"")."</font>";
		echo "</tr>";
	}
?>
	</table>
	</center>
	<br>
	<div id=add_attr_div align=left>
		<span id="add_attr_button" style="display:inline; font-family:lucida fax;">
			<img src="images/add_icon1.png" height=3% width=3% style="cursor:pointer;" onclick="document.getElementById('add_attr_button').style.display='none'; document.getElementById('add_attr_form').style.display='inline';"/>
			<span style="background-color:chartreuse">Add new attribute</span>
		</span>
		<span id="add_attr_form" style="display:none">
			<form>
				<table width=300 HEIGHT=110 style="float:left">
					<tr>
						<td>AttributeName
						<td><input type=text name="add_attr_name" id="add_attr_name" maxlength=255 onkeypress="return isAlphaNumberUnderscoreKey(event,this.id);"/>
					<tr>
						<td>DataType
						<td>
							<select id="add_attr_type" name="add_attr_type" onchange="if(this.value=='file'){document.getElementById('show_kb').innerHTML='KB'; document.getElementById('add_attr_size').maxLength=8; document.getElementById('add_attr_size').value='';} else{document.getElementById('show_kb').innerHTML=''; document.getElementById('add_attr_size').maxLength=3; document.getElementById('add_attr_size').value='';}">
								<option>number
								<option>char
								<option>file
							</select>
					<tr>
						<td>size
						<td>
							<input type=text size=15 maxlength=3 id="add_attr_size" name="add_attr_size" onkeypress="return isNumberKey(event,this.id);">
							<span id="show_kb"></span>
				</table>
			</form>
			<span align=center onclick="add_new_attr()" style="height:100; width:100; float:left; background-image:url('images/arrow_icon2.png'); background-size: 100% 100%; cursor:pointer">
				<table height=100% width=100%>
					<tr>
						<td align=center style="color:yellow; font-family:lucida fax, monotype corsiva; font-size:20">ADD
				</table>
			</span>
		</span>
	</div>
</body>
</html>