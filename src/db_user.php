<html>
	<head>
		<style type=text/css>
			body
			{
				overflow-y:hidden;
			}
		</style>

		<script>
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

			function logout()
			{
				try
				{
					obj=pullAjax();
					obj.onreadystatechange=function()
					{
						if(obj.readyState==4)
						{
							top.location.href="index.php";
							alert("You have logged out of your account...");
						}
					}
					obj.open("GET","logout.php",true);
					obj.send(null);
				}
				catch(err)
				{
					alert(err);
				}
			}
		</script>
	</head>
	<body bgcolor="lightblue">
		<span id="uname" style="float:right">
			<?php
				session_start();
				echo $_SESSION['uname']."<br><div style=\"color:blue; cursor:pointer;\" onclick=\"logout();\">LogOut</div>";
			?>
		</span>

		<span style="cursor:pointer; float:left" onclick="top.location.reload()">
			<img src="images/dBase_logo2.png">
			<img src="images/knrs_logo3.png">
		</span>
	</body>
</html>