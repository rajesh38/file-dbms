<html>
	<head>
		<style type="text/css">
		body
		{
			overflow-x:hidden;
			font-family:lucida fax;
		}
		</style>

		<script language="javascript">
			var no_attr=0;
			function validate()
			{
				if(f1.tab_name.value == "")
				{
					alert("Enter Table Name.");
					return (false);
				}
				if(f1.no_of_attributes.value == "")
				{
					alert("Enter No. Of Attributes.");
					return (false);
				}
				if(f1.tab_name.value.indexOf(" ") > -1)
				{
					alert("Whitespace not allowed in Table name.");
					return (false);
				}
				return (true);
			}
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
			function generate_attributes(n)
			{
				try
				{
					if(n==0)
					{
						alert("The table cannot have zero attributes.");
						return false;
					}
					no_attr=n;
					var div_string="<form name=f2 action=\"create_table.php\" onsubmit=\"return attribute_validate()\" method=\"POST\">";
					div_string=div_string+"<input type=hidden id='f2_tab_name' name='f2_tab_name' value='"+document.getElementById('tab_name').value+"'>";
					div_string=div_string+"<input type=hidden id='f2_no_of_attributes' name='f2_no_of_attributes' value='"+document.getElementById('no_of_attributes').value+"'>";
					div_string=div_string+"<table border=1>";
					div_string=div_string+"<tr>\
											<th style='font-size:16'>SN.\
											<th style='font-size:16'>ATTRIBUTE\
											<th style='font-size:16'>DATATYPE\
											<th style='font-size:16'>LENGTH\
											<th style='font-size:16'>PK\
										</tr>";
					for(i=1;i<=n;i++)
					{
						try
						{
						div_string=div_string+"<tr><td>"+i+"<td><input type=text name=attr"+i+" id=attr"+i+" size=10 maxlength=255 onkeypress=\"return isAlphaNumberUnderscoreKey(event,this.id);\"> <td><select name=attr_type"+i+" id=attr_type"+i+" onchange=\"if(this.value=='file'){document.getElementById('show_size_kb"+i+"').innerHTML='KB'; document.getElementById('attr_size"+i+"').setAttribute('maxlength', 8);}else{document.getElementById('show_size_kb"+i+"').innerHTML=''; document.getElementById('attr_size"+i+"').setAttribute('maxlength', 3);}\"><option value='char'>char</option> <option value='number'>number</option> <option value='file'>file</option></select> <td><input type=text name=attr_size"+i+" id=attr_size"+i+" size=2 maxlength=3 onkeypress=\"return isNumberKey(event,this.id)\"><span id='show_size_kb"+i+"'></span> <td><input type=radio name=pk id=pk"+i+" value="+i+"></tr>";
						}
						catch(err)
						{
							alert('hi');
						}
					}
					div_string+="</table><input type=submit value='CREATE TABLE'></form>";
					document.getElementById('attribute_list').innerHTML=div_string;
				}
				catch(err)
				{
					alert(err.message);
				}
			}

			function attribute_validate()
			{
				for(var i=1;i<=no_attr;i++)
				{
					if(document.getElementById('attr'+i).value == "")
					{
						alert("Enter All Attribute Names.");
						return (false);
					}
					if(document.getElementById('attr'+i).value.indexOf(" ") > -1)
					{
						alert("Whitespace Not Allowed In Any Attribute Name.");
						return (false);
					}
					if(document.getElementById('attr_type'+i).selectedIndex<0)
					{
						alert("Select DataType For All Attributes.");
						return (false);
					}
					if(document.getElementById('attr_size'+i).value == "")
					{
						alert("Enter Size Of All Attributes.");
						return (false);
					}
				}
				return (true);
			}

		</script>
	</head>
	<body bgcolor="Moccasin" onload="parent.frames['left_top_container'].location.reload()">
		<form method="get" name="f1">
			New Table Name<input type=text id="tab_name" id="tab_name" onkeypress="return isAlphaNumberUnderscoreKey(event,this.id);">
			<br>
			No. Of Attributes<input type=text id="no_of_attributes" name="no_of_attributes" size=1 maxlength=2 onkeypress="return isNumberKey(event,this.id);">
			<input type=button value="generate" onClick="if(!validate()){return false;} generate_attributes(document.getElementById('no_of_attributes').value);">
			<hr>
		</form>
		<div id="attribute_list"></div>
	</body>
</html>