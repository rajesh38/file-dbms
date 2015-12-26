<?php
	if(!isset($_SESSION))
		session_start();
	if(isset($_SESSION['uname']))
		echo "<script>location.href='home.php'</script>";
?>
<html>
<head>
	<style>
		.highlight1
		{
			color:blue;
		}
		.highlight2
		{
			color:darkorange;
		}
	</style>
	<script>
		window.history.forward(0);
		var new_uname_allowed=1;
		function isAlphaNumberUnderscoreKey(evt,id)
		{
			try
			{
				var charCode = (evt.which) ? evt.which : event.keyCode;
				if (charCode ==9 || charCode ==8 || charCode ==13 || charCode ==15 || charCode ==16 || charCode ==17 || charCode ==18 || charCode ==46 || charCode ==95 || (charCode >= 33 && charCode <= 40) || (charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122))
				{
					check_uname_true=1;
					check_uname();
					return (true);
				}
				alert("Only Underscore(_), Number(0-9), Alphabets(A-Z/a-z) are allowed."+charCode);
				document.getElementById('new_uname').value=document.getElementById('new_uname').value.substr(0, document.getElementById('new_uname').value.length - 1);
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

		function validate_login()
		{
			try
			{
				var uname = document.getElementById('uname');
				var pwd = document.getElementById('pwd');
				uname = uname.value;
				pwd = pwd.value;
				message = '';
				obj=pullAjax();
				obj.onreadystatechange=function()
				{
					if(obj.readyState==4)
					{
						eval("response = "+obj.responseText);
						switch(response['statusCode'])
						{
							case(0):
								message="UserName or Password cannot be left blank.";
								break;
							case(1):
								message="This UserName is not registered";
								break;
							case(2):
								message="The Password for the UserName is not correct.";
								break;
							case(3):
								location.href="home.php";
								break;
								
							//the following case is for forbidding multiple instance of the same user. It's disabled due to unhandled behaviour in case of browser close without LOGOUT.
							//case(4):
								//message="A session for this UserName is already active. Login not allowed";
						}
						if(response['statusCode']!=3)
							alert(message);
					}
				}
				obj.open("GET","login_validation.php?uname="+uname+"&pwd="+pwd,true);
				obj.send(null);
			}
			catch(err)
			{
				alert(err);
			}
		}
		
		function validate_signup()
		{
			try
			{
				if(document.getElementById('new_pwd').value!==document.getElementById('re_pwd').value)
				{
					alert('The passwords in the two sections are not the same.');
					return;
				}
				if(new_uname_allowed===0)
				{
					alert('This UserName is already being used by another user');
					return;
				}
				var uname = document.getElementById('new_uname');
				var pwd = document.getElementById('new_pwd');
				uname = uname.value;
				pwd = pwd.value;
				message = '';
				obj=pullAjax();
				obj.onreadystatechange=function()
				{
					if(obj.readyState==4)
					{
						eval("response = "+obj.responseText);
						switch(response['success'])
						{
							case(0):
								message="Server Error:Unable to register. Try again";
								break;
							case(1):
								message="Welcome to DBASE. Try LogIn";
								break;
						}
						alert(message);
						if(response['success']==0)
						{
							document.gtElementById('new_uname').focus();
							document.gtElementById('new_uname').select();
						}
						else
						{
							document.getElementById('signup_span').style.display='none';
							document.getElementById('login_span').style.display='inline';
						}
					}
				}
				obj.open("GET","validate_signup.php?uname="+uname+"&pwd="+pwd,true);
				obj.send(null);
			}
			catch(err)
			{
				alert(err);
			}
		}

		function check_uname()
		{
			try
			{
				var uname = document.getElementById('new_uname');
				uname = uname.value;
				msg=document.getElementById('uname_error');
				if(uname=="")
				{
					msg.innerHTML="";
					return false;
				}
				message = '';
				obj=pullAjax();
				obj.onreadystatechange=function()
				{
					if(obj.readyState==4)
					{
						eval("response = "+obj.responseText);
						if(response['statusCode']===0)
						{
							message="<font color=red>Unavailable</font>";
							new_uname_allowed=0;
						}
						else
						{
							message="<font color=limegreen>Available</font>";
							new_uname_allowed=1;
						}
						msg.innerHTML=message;
					}
				}
				obj.open("GET","check_uname.php?uname="+uname,true);
				obj.send(null);
			}
			catch(err)
			{
				alert(err);
			}
		}

	function change_highlight_color()
	{
		try
		{
			//alert('hi');
			if(document.getElementById('highlight_text').className=="highlight1")
				document.getElementById('highlight_text').className="highlight2";
			else if(document.getElementById('highlight_text').className=="highlight2")
				document.getElementById('highlight_text').className="highlight1";
		}
		catch(err)
		{
			alert(err);
		}
	}

	function change_highlight_color_periodic()
	{
		setInterval("change_highlight_color()",500);
	}

	</script>
</head>
<body bgcolor="lightblue">
	<span id=login_span style="float:right">
		<table border=1 bgcolor=bisque>
			<tr>
				<td align=center>
					<table border=0 width=250>
						<tr>
							<td align=center style="font-size:20">UserName
							<td align=center><input type=text name="uname" id="uname" onkeypress="return isAlphaNumberUnderscoreKey(event,this.id);">
						</tr>
						<tr>
							<td align=center style="font-size:20">Password
							<td align=center>
								<input type=password name="pwd" id="pwd" size=15>
								<span id="eye" style="cursor:pointer">
									<img src="images/eye_plus.png" width=20px height=20px title="Show Password" onmouseover="if(document.getElementById('pwd').type=='password'){document.getElementById('pwd').type='text'; this.src='images/eye_minus.png'; this.title='Show Password';} else{document.getElementById('pwd').type='password'; this.src='images/eye_plus.png'; this.title='Hide Password';}" onmouseout="if(document.getElementById('pwd').type=='password'){document.getElementById('pwd').type='text'; this.src='images/eye_minus.png'; this.title='Show Password';} else{document.getElementById('pwd').type='password'; this.src='images/eye_plus.png'; this.title='Hide Password';}">
								</span>
						</tr>
					</table>
			</tr>
			<tr>
				<td align=center>
					<table border=0 width=250>
						<tr><td align=center align=center><input type=button value=LOGIN onclick="validate_login()"/>
					</table>
			</tr>
			<tr>
				<td align=center style="font-size:20">New Here? <font color=magenta style="cursor:pointer" onclick="document.getElementById('login_span').style.display='none'; document.getElementById('signup_span').style.display='inline';">Sign up</font>
			</tr>
			<tr>
				<td align=center style="background-color:azure;">
					<marquee id='marquee1' direction=left scrollamount=2 scrolldelay=1 style="font-size:20; color:darkcyan; font-family:lucida fax">Use The <span id="highlight_text" class="highlight1" style="cursor:pointer; background-color:aqua;" onclick="location.href='query_home.php'" onmouseover="document.getElementById('marquee1').stop();" onmouseout="document.getElementById('marquee1').start();">QUERY INTERFACE</span></marquee>
			</tr>
		</table>
	</span>

	<span id=signup_span style="float:right; display:none">
		<table border=1 bgcolor=bisque>
			<tr>
				<td align=center>
					<table border=0 width=350>
						<tr>
							<td align=center style="font-size:20">UserName
							<td align=center><input type=text name="new_uname" id="new_uname" onkeyup="return isAlphaNumberUnderscoreKey(event,this.id);">
								<span id="uname_error"></span>
						</tr>
						<tr>
							<td align=center style="font-size:20">Password
							<td align=center>
								<input type=password name="new_pwd" id="new_pwd">
								<span id="eye" style="cursor:pointer">
									<img src="images/eye_plus.png" width=20px height=20px title="Show Password" onmouseover="if(document.getElementById('new_pwd').type=='password'){document.getElementById('new_pwd').type='text'; this.src='images/eye_minus.png'; this.title='Show Password';} else{document.getElementById('new_pwd').type='password'; this.src='images/eye_plus.png'; this.title='Hide Password';}" onmouseout="if(document.getElementById('new_pwd').type=='password'){document.getElementById('new_pwd').type='text'; this.src='images/eye_minus.png'; this.title='Show Password';} else{document.getElementById('new_pwd').type='password'; this.src='images/eye_plus.png'; this.title='Hide Password';}">
								</span>
						</tr>
						<tr>
							<td align=center style="font-size:20">RetypePassword
							<td align=center>
								<input type=password name="re_pwd" id="re_pwd">
								<span id="eye" style="cursor:pointer">
									<img src="images/eye_plus.png" width=20px height=20px title="Show Password" onmouseover="if(document.getElementById('re_pwd').type=='password'){document.getElementById('re_pwd').type='text'; this.src='images/eye_minus.png'; this.title='Show Password';} else{document.getElementById('re_pwd').type='password'; this.src='images/eye_plus.png'; this.title='Hide Password';}" onmouseout="if(document.getElementById('re_pwd').type=='password'){document.getElementById('re_pwd').type='text'; this.src='images/eye_minus.png'; this.title='Show Password';} else{document.getElementById('re_pwd').type='password'; this.src='images/eye_plus.png'; this.title='Hide Password';}">
								</span>
						</tr>
					</table>
			</tr>
			<tr>
				<td align=center>
					<table border=0 width=250>
						<tr><td align=center align=center><input type=button value=SIGNUP onclick="validate_signup()"/>
					</table>
			</tr>
			<tr>
				<td align=center style="font-size:20">Already registered? <font color=magenta style="cursor:pointer" onclick="document.getElementById('signup_span').style.display='none'; document.getElementById('login_span').style.display='inline';">Log In</font>
			</tr>
		</table>
	</span>
	
	<span style="float:left; cursor:pointer" onclick="location.reload();">
		<img src="images/dBase_logo2.png">
		<img src="images/knrs_logo3.png">
	</span>
	<br><br><br>
	<span style="float:left; font-family:calibri, monotype corsiva, lucida fax; color:brown">
		<?php
			$user_acc_file="user_acc_info/user_acc.knrs";
			if(!file_exists($user_acc_file))
				$user_count=0;
			else
			{
				$user_count=count(file($user_acc_file));
			}
			if($user_count>0)
				echo "<font face='lucida fax' color=black size=6><font color=crimson>".$user_count." user".($user_count>1?"s":"")."</font> already registered.</font><br>";
			echo "<font size=5>Register yourself to use our very own DATABASE PORTAL.<br>We are committed towards making it a soothing experience for you.</font>";
		?>
	</span>
	<script>
		change_highlight_color_periodic();
	</script>
</body>
</html>