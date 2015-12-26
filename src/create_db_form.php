<html>
	<head>
		<style type="text/css">
		body
		{
			font-family:lucida fax;
		}
		</style>

		<script language="javascript">
			function validate()
			{
				if(f1.t1.value == "")
				{
					alert("DB name must not be empty.");
					return (false);
				}
				if(f1.t1.value.indexOf(" ") > -1)
				{
					alert("Whitespace not allowed in DB name.");
					return (false);
				}
				return (true);
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
	<body bgcolor="#FFDADA" onload="parent.frames['left_top_container'].location.reload()">
		<form method="get" name="f1" onsubmit="return validate();" action="create_db.php">
			Enter new database name<input type=text id="t1" name="t1" onKeyPress="return isAlphaNumberUnderscoreKey(event, this.id)">
			<input type=submit>
		</form>
	</body>
</html>