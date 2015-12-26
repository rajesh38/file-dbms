<?php
	if(!isset($SESSION))
		session_start();
	if(isset($_SESSION['uname']))
		header("Location:/knrs_dbms/home.php");
	if(!function_exists("trim_all"))
	{
		function trim_all($str)
		{
			$str=trim($str);
			$str=trim($str, "\n");
			$str=trim($str, "\t");
			$str=trim($str, "\r");
			return $str;
		}
	}
?>
<html>
	<body bgcolor="lightblue" onload="document.getElementById('query_string').focus();">
		<span style="float:left; cursor:pointer" onclick="location.href='index.php'">
			<img src="images/dBase_logo2.png">
			<img src="images/knrs_logo3.png">
		</span>
		<br><br><br>
		<hr>
		<form method="POST">
			<table height=10% width=100%>
				<tr>
					<td align=center width=4%>
						<textarea readonly style="width:100%; height:100%; background-color:darkcyan; color:aqua; font-weight:bold; font-size:15;">SQL></textarea>
					<td>
						<textarea id="query_string" name="query_string" style="width:100%; height:100%; background-color:darkcyan; color:aqua; font-weight:bold; font-size:16"><?php	echo (isset($_REQUEST['query_string'])? $_REQUEST['query_string']: "");	?></textarea>
			</table>

			<input type=submit value="RUN" style="position:absolute; right:1%;"/>
			<?php
				echo "<input type=hidden name='uname_query' value=\"".(isset($_SESSION['uname_query'])? $_SESSION['uname_query']: "")."\">";
				echo "<input type=hidden name='db_selected_query' value=\"".(isset($_SESSION['db_selected_query'])? $_SESSION['db_selected_query']: "")."\">";
			?>

			<br><br>

			<table style="min-width:100%; min-height:50%; max-height:50%;" border=0 cellpadding=5>
				<tr>
					<td width=80% align=center>
						<div id='output_div' style="min-width:100%; min-height:100%; background-color:aliceblue;"></div>
					<td align=center>
						<fieldset style="min-width:85%; max-width:85%; min-height:100%; max-height:100%;">
							<legend style="font-size:20; font-family:arial;">Log</legend>
							<textarea id="log" name="log" readonly style="width:100%; height:280; background-color:aliceblue; color:green;"><?php	echo (isset($_REQUEST['log'])? $_REQUEST['log']: "")	?></textarea>
						</fieldset>
				</tr>
			</table>

			<center>
				<fieldset style="width:97%; height:20%; float:left; position:absolute; right:3">
					<legend style="font-size:20; font-family:arial;">USED QUERIES</legend>
					<textarea id="used_query" name="used_query" readonly style="width:100%; height:85%; background-color:aliceblue; color:green; font-weight:bold; font-size:16"><?php	echo (isset($_REQUEST['used_query'])? $_REQUEST['used_query']: "")	?></textarea>
				</fieldset>
			</center>
		</form>
	</body>
</html>

<?php
	if(isset($_REQUEST['query_string']))
	{
		echo "<script>document.getElementById('query_string').focus();</script>";
		$query_string=trim_all($_REQUEST['query_string']);
		if($query_string=="")
			die ("<script>alert('Query section left NULL.');</script>");
		$query_string=str_replace("\r", " ", str_replace("\n", "", $query_string));
		echo "<script>document.getElementById('used_query').innerHTML=\"".str_replace("\"", "\\\"", $query_string)."\\n-------------------------------------------------------------\\n\" + document.getElementById('used_query').innerHTML + \"\\n\";</script>";
		$command_array=array("SELECT", "UPDATE", "DELETE", "ALTER", "DROP", "DISCONNECT", "SHOW", "USE");
		if(!isset($_SESSION['uname_query']))// i.e. if user has not connected to his account successfully.
		{
			if(strtoupper(substr($query_string, 0, strlen("connect ")))!="CONNECT " && strtoupper($query_string)!="CONNECT")// when uname_query is not in session it'll not accept any query apart from 'connect'
			{
				echo "<script>alert('You must connect to your account first using \'CONNECT\'');</script>";
			}
			else
			{
				if(strtoupper($query_string)=="CONNECT")
					die ("<script>alert('Enter \'CONNECT\' command in proper format');</script>");
				$parsed_query_string=substr($query_string, strlen("connect"));// taking the part of the query string following "connect"
				//echo "<script>alert('1-".$parsed_query_string."-');</script>";//
				$parsed_query_string=trim_all($parsed_query_string);// trimming the part of the query string following "connect" to omit the starting whitespaces
				//echo "<script>alert('2-".$parsed_query_string."-');</script>";//
				$uname_query=substr($parsed_query_string, 0, strpos($parsed_query_string, " "));// taking the <uname> just following "connect"
				//echo "<script>alert('3-".$uname_query."-');</script>";//
				$parsed_query_string=substr($parsed_query_string, strlen($uname_query));// taking the part of the query string following <uname>
				//echo "<script>alert('4-".$parsed_query_string."-');</script>";//
				$parsed_query_string=trim_all($parsed_query_string);
				//echo "<script>alert('5-".$parsed_query_string."-');</script>";//
				$pwd_query=$parsed_query_string;
				if(strpos($pwd_query, "\"")===0 && strrpos($pwd_query, "\"")===(strlen($pwd_query)-1))// i.e if password is enclosed within double-quotes.
				{
					$user_acc_file="user_acc_info/user_acc.knrs";
					if(strpos(strtoupper(file_get_contents($user_acc_file)), strtoupper($uname_query."->"))===0 || strpos(strtoupper(file_get_contents($user_acc_file)), "\n".strtoupper($uname_query."->"))!==FALSE)
					{
						$arr=file($user_acc_file);
						$pwd_query=substr($pwd_query, 1, strlen($pwd_query)-2);
						foreach($arr as $uname_pwd)// checking each "uname->pwd" pair.
						{
							if(strtoupper($uname_query)."->".$pwd_query."\n"==strtoupper(substr($uname_pwd, 0, strpos($uname_pwd, "->")))."->".substr($uname_pwd, strpos($uname_pwd, "->")+2))
							{
								$connect_account=true;
								//echo "<script>alert('uname & pwd ok.');</script>";//
								$_SESSION['uname_query']=$uname_query;
								echo "<script>document.getElementById('log').innerHTML=\"Connected to:".substr($uname_pwd, 0, strpos($uname_pwd, "->"))."\\n----------------------\\n\" + document.getElementById('log').innerHTML + \"\\n\";</script>";
								break;
							}
						}
						if(!isset($connect_account))
							echo "<script>alert('Valid UserName, but wrong Password.');</script>";
					}
					else
						echo "<script>alert('Invalid UserName.');</script>";
				}
				else// i.e if password is not enclosed within double-quotes.
				{
					echo "<script>alert('Enter the password enclosed within a pair of double-quotes.');</script>";
				}
			}
		}
		else// i.e. if user has already connected to his account.
		{
			$query_string=str_replace("\r", " ", str_replace("\n", " ", $query_string));// convering the "\n" and "\r" into " " before parsing the code
			$query_string_backup=$query_string;// for the sake of any value to be checked keeping in mind the case of the textual value of any attribute.
			$query_string=strtoupper($query_string);// for the sake of removing ambiguity ref=garding the case of the query_string
			if(substr($query_string, 0, strlen("CONNECT "))=="CONNECT " || $query_string=="CONNECT")// when uname_query is not in session it'll not accept any query apart from 'connect'
			{
				echo "<script>alert('You are already connected to your account \'".$_SESSION['uname_query']."\'. Disconnect first using \'DISCONNECT\' to connect again to another account.');</script>";
			}
			else
			{
				foreach($command_array as $allowed_command)
				{
					//the following code-snippet is for parsing the command out of the query-string
					if(strpos($query_string, " ")===FALSE && strpos($query_string, "\n")===FALSE)
						$given_command=$query_string;
					else
					{
						if(strpos($query_string, " ")!==FALSE && strpos($query_string, "\n")!==FALSE)
						{
							if(strpos($query_string, " ") < strpos($query_string, "\n"))
								$given_command=substr($query_string, 0, strpos($query_string, " "));
							else
								$given_command=substr($query_string, 0, strpos($query_string, "\n"));
						}
						else
						{
							if(strpos($query_string, " ")!==FALSE)
								$given_command=substr($query_string, 0, strpos($query_string, " "));
							else
								$given_command=substr($query_string, 0, strpos($query_string, "\n"));
						}
					}
					$given_command=trim_all($given_command);

					if($given_command==$allowed_command)// checking for whether any match b/w the given command and any allowed_command exist.
					{
						switch($allowed_command)// code-segment for executing the qauery
						{
							case "SELECT":
								if(isset($_SESSION['db_selected_query']))
								{
									// this code block for this case is the query-engine for select statement
									$parsed_query_string=substr($query_string, strlen("SELECT"));// taking the part of the query string following "select"
									if($parsed_query_string=="")// i.e the query string is noting but the keyword 'select'
										die("<script>alert('\'SELECT\' query is not in proper format');</script>");
									else
									{
										$parsed_query_string=trim_all($parsed_query_string);// trimming the part of the query string following "select" to omit the starting whitespaces, \n, \t, \r etc.
										if((strpos($parsed_query_string, " FROM ")===FALSE && strrpos($parsed_query_string, " FROM")!=strlen($parsed_query_string)-5) || strlen($parsed_query_string)<4)// i.e. no "FROM " clause in "SELECT" query.
											die("<script>alert('No \'FROM\' clause in \'SELECT\' query.');</script>");
										else// i.e. "FROM " clause is there in "SELECT" query.
										{
											if(strpos($parsed_query_string, " FROM ")===FALSE && strrpos($parsed_query_string, " FROM")===strlen($parsed_query_string)-5)//i.e. query ending with "FROM"
												die("<script>alert('\'SELECT\' query is not in proper format.');</script>");
											if(strpos($parsed_query_string, " FROM ")!=strrpos($parsed_query_string, " FROM ") || (strrpos($parsed_query_string, " FROM")===strlen($parsed_query_string)-5 && strpos($parsed_query_string, " FROM")!=strrpos($parsed_query_string, " FROM")))// i.e. multiple "FROM" clause are used, which is not allowed.
												die("<script>alert('Ambiguous use of \'FROM\' clause in \'SELECT\' query.');</script>");
											else// i.e. only one "FROM" clause is there in the query_string.
											{
												if(strpos($parsed_query_string, "*")===0)// i.e. if the query_string started with 'select *'
												{
													if(strpos($parsed_query_string, " WHERE ")===FALSE && strpos($parsed_query_string, " WHERE")!=strlen($parsed_query_string)-6)// i.e no "WHERE" clause is used in the query.
													{
														$parsed_query_string=trim_all(substr($parsed_query_string, 1));// take the trimmed part of query_string following '*'
														if(strpos($parsed_query_string, "FROM")===0)// i.e. "FROM" clause is placed just after "*"
														{
															$parsed_query_string=trim_all(substr($parsed_query_string, strlen("FROM")));// take the trimmed part of query_string following 'FROM' clause.
															$tab_name=$parsed_query_string;
															if(strpos($tab_name, " ")===FALSE)
															{
																//checking whether the specified tab_name is present in the table_list.knrs file under the selected DB.
																$table_list_file="databases/".$_SESSION['uname_query']."/".$_SESSION['db_selected_query']."/table_list.knrs";
																if(stripos(file_get_contents($table_list_file), "\n".$tab_name."\n")!==FALSE || stripos(file_get_contents($table_list_file), $tab_name."\n")===0)
																{
																	$tab_data_file="databases/".$_SESSION['uname_query']."/".$_SESSION['db_selected_query']."/".$tab_name."_data.knrs";
																	$tab_schema_file="databases/".$_SESSION['uname_query']."/".$_SESSION['db_selected_query']."/".$tab_name."_schema.knrs";
																	if(filesize($tab_data_file)>0)
																	{
																		$substr=NULL;
																		$separater_string="#$**$#";
																		$pos1=0;
																		$pos2=0;
																		$data=NULL;
																		echo "<script> var div_string=\"<table border=1 bgcolor=cyan width='100%' style='table-layout:fixed'>\"</script>";
																		$arr=file($tab_schema_file);
																		echo "<script> div_string+=\"<tr>\"</script>";
																		$file_type_attr_arr=array();//Array of all the attributes of type=file
																		$attr_no=0;
																		$file_attr_present=false;
																		foreach($arr as $attr)
																		{
																			$attr_no++;
																			$attr_type=substr($attr, strpos($attr,'->')+2, strrpos($attr,'->') - strpos($attr,'->') - 2);
																			$attr=substr($attr, 0, strpos($attr,'->'));
																			if($attr_type=='file')
																			{
																				$file_type_attr_arr[]=$attr_no;
																				if($file_attr_present==false)
																				{
																					$file_attr_present=true;
																					$file_extensions=array();
																					$file_extensions['archive']=array('7z', 'cab', 'jar', 'rar', 'rpm', 'tar', 'zip');
																					$file_extensions['document']=array('doc', 'docx', 'rtf','info','txt');
																					$file_extensions['image']=array('jpg', 'jpeg', 'bmp', 'gif', 'ico', 'png', 'tiff',);
																					$file_extensions['web']=array('html', 'htm', 'xml', 'xhtml');
																					$file_extensions['audio']=array('wma', 'mp2', 'mp3', 'amr', 'mid');
																					$file_extensions['video']=array('3gp', 'avi', 'dat', 'flv', 'mkv', 'mpeg', 'swf', 'mp4');
																					$file_extensions['script']=array('cmd', 'php', 'pl', 'py', 'asp', 'jsp', 'cgi', 'js');
																					$file_extensions['executable']=array('bat', 'exe', 'vb');
																					$file_extensions['ebook']=array('pdf');
																				}
																			}
																			echo "<script>div_string+=\"<th style='word-wrap:break-word;'><font size=5 face='lucida fax'>".$attr."</font>\";</script>";
																		}
																		echo "<script>div_string+=\"</tr>\";</script>";
																		
																		//Data retrieval
																		$record_no=0;
																		$arr=file($tab_data_file);
																		foreach($arr as $line)
																		{
																			$record_no++;
																			$attr_no=0;
																			$substr1=$line;
																			echo "<script> div_string+=\"<tr>\"</script>";
																			$substr1=substr($substr1,6,strlen($substr1)-6);
																			while(($pos2+strlen($separater_string))<=strlen($substr1))
																			{
																				$attr_no++;//increasing the attribute no to get access to the value of the attributes in each record one by one.
																				$file_type_attr=false;
																				if(in_array($attr_no, $file_type_attr_arr))
																					$file_type_attr=true;
																				$pos1=strpos($substr1,$separater_string);
																				$data=substr($substr1,0,$pos1);
																				if($file_type_attr==false)
																					echo "<script> div_string+=\"<td align=center style='font-size:20;word-wrap:break-word;'>"."<span style='background-color: aliceblue; -moz-border-radius: 3px; -webkit-border-radius: 3px; display: inline;'>".str_replace(" ", "&nbsp;", $data)."</span>"."</td>\"</script>";
																				else
																				{
																					$file_icon_image="";
																					$type_name="Unknown Type";
																					if(strrpos($data,'.')===false)
																					{
																						$file_icon_image=".\\Images\\file_icons\\unknown_file_icon.jpg";
																						if($data=="")
																							unset($file_icon_image);
																					}
																					else
																					{
																						foreach($file_extensions as $type => $type_ext_arr)//$type is the key, and $type_ext_arr==$file_extensions[$type].
																						{
																							$type_name=$type;
																							foreach($type_ext_arr as $ext)
																							{
																								if(strtolower(substr($data,strrpos($data,'.') + 1))==$ext)
																								{
																									$file_icon_image=".\\Images\\file_icons\\".$type_name."_icon.jpg";
																									break;
																								}
																							}
																							if($file_icon_image!=="")
																								break;
																						}
																					}
																					if(isset($file_icon_image) && $file_icon_image==="")
																						$file_icon_image=".\\Images\\file_icons\\unknown_file_icon.jpg";
																					$file_path="./databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_files/".$data;
																					$show_file_script="";
																					switch($type_name)
																					{
																						case "image":
																							$show_file_script=(file_exists($file_path)?"<img src=\"".$file_path."\"  style=\"max-width:100%; max-height:100%;\">":"<table width=100% height=100% style=\"table-layout:fixed\" border=1><tr><td width=100%>File Does Not Exist.</td></tr></table>");
																							break;
																						case "audio":
																							$show_file_script=(file_exists($file_path)?"<table border=1><tr><td align=center>	<table width=100%><tr><td align=center>Volume<td align=center>	<table border=0><tr><td align=center><button style=\"cursor:pointer; font-size:12; font-family:lucida fax\" onclick=\"document.getElementById('file_obj_show_rec_".$record_no."_attr_".$attr_no."').volume+=0.1;\">+</button></td><td align=center><button style=\"cursor:pointer; font-size:12; font-family:lucida fax\" onclick=\"document.getElementById('file_obj_show_rec_".$record_no."_attr_".$attr_no."').volume-=0.1;\">-</button></tr></table></tr></table>	</tr>	<tr><td align=center style=\'font-family:lucida fax; display:block\'><a href=\"".$file_path."\" target=\"_blank\"><font face='lucida fax' size=3>Click to download the audio file.</font></a></tr></table>	<table><tr><td><br><td><br></tr></table>"."<audio id='file_obj_show_rec_".$record_no."_attr_".$attr_no."' hidden='true' autostart='false' style=\"max-width:100%; max-height:100%;\" controls=\"\">	<source src=\"".$file_path."\" type=\"audio/".$ext."\" onerror=\"document.getElementById('file_show_rec_".$record_no."_attr_".$attr_no.").innerHTML='<table border=1><tr><td align=center style=\'font-family:lucida fax; display:block\'>Your browser does not support the audio format.<br><a href=\'".$file_path."\' target=\'_blank\'>click here to download it.</a></tr></table>'\">	</audio>":"File Does Not Exist.");
																							break;
																						case "video":
																							$show_file_script=(file_exists($file_path)?"<video id='file_obj_show_rec_".$record_no."_attr_".$attr_no."' autostart='false' style=\"max-width:100%; max-height:100%;\" controls>	<source src=\"".$file_path."\" type=\"video/".$ext."\" onerror=\"document.getElementById('file_show_rec_".$record_no."_attr_".$attr_no."').innerHTML='<table border=1><tr><td align=center style=\'font-family:lucida fax; display:block\'>Video format not supported.<br><a href=\'".$file_path."\' target=\'_blank\'>click to download it.</a></tr></table>'\">	</video>":"File Does Not Exist.");
																							break;
																						default:
																							if($data!=="")
																								$show_file_script="<font size=3 face='lucida fax'><table border=1><tr><td align=center>No Preview Available.<br><a href=\"".$file_path."\" target=\"_blank\">Click To Open In A New Tab</a></tr></table></font>";
																							else
																								$show_file_script="";
																					}
																					echo "<script> div_string+=\"<td align=center style='min-height:70; font-size:20; word-wrap:break-word;'>"."<span id='file_desc_rec_".$record_no."_attr_".$attr_no."' class='show' style='margin:15px 15px 15px 15px;'><table border=0 width=100% style='table-layout:fixed'><tr><td align=center width=30% align=center>".(isset($file_icon_image)?"<img src='".$file_icon_image."' height=50 width=50 title='".$type_name."'>":"")."<td align=center>".$data."</tr></table></span>"."</td>\"</script>";
																				}
																				$substr1=substr($substr1,$pos1+strlen($separater_string),(strlen($substr1)-($pos1+strlen($separater_string))));
																				if($substr1=="")
																					break;
																				$pos2=strpos($substr1,$separater_string);
																			}
																			echo "<script> div_string+=\"</tr>\"</script>";
																		}
																		echo "<script> div_string+=\"</table>\"</script>";
																		echo "<script>document.getElementById('output_div').innerHTML=div_string;</script>";
																	}
																	else
																		die("<script>alert('No data exists in table: \'".$tab_name."\'');</script>");
																}
																else
																{
																	die("<script>alert('Table:\"".$tab_name."\" not found');</script>");
																}
															}
															else// i.e tablename includes space(" "), that is not allowed
																die("<script>alert('\'SELECT\' query is not in proper format');</script>");
														}
														else
														{
															echo "-".$parsed_query_string."-";//
															die("<script>alert('\'FROM\' clause misplaced in \'SELECT\' query.');</script>");
														}
													}
													else// i.e "WHERE" clause is there in the query
													{
														if(strrpos($parsed_query_string, " WHERE")===strlen($parsed_query_string)-6)// i.e. "WHERE" clause is placed at the end of the query_string.
														{
															die("<script>alert('\'SELECT\' query is not in proper format');</script>");
														}
														else// i.e. "WHERE" clause is placed somewhere in the middle of the query_string, i.e. not at the end.
														{
															if(strrpos($parsed_query_string, " WHERE ")!=strpos($parsed_query_string, " WHERE "))// i.e. there are multiple "WHERE" clause in the query_string.
															{
																die("<script>alert('Ambiguous use of \'WHERE\' clause in \'SELECT\' query.');</script>");
															}
															else// i.e. only one "where" clause is present in the query_string.
															{
																$parsed_query_string=substr($parsed_query_string, strpos($parsed_query_string, " WHERE ")+7);// i.e. taking the part of the query_string following "WHERE" clause.
																$parsed_query_string=substr($query_string_backup, stripos($query_string_backup, $parsed_query_string));// taking the part of the query string following "WHERE" in the actual case as provided.
																$parsed_query_string=trim_all($parsed_query_string);
																{
																	echo "<script>alert('".$parsed_query_string."');</script>";//
																	if(stripos($parsed_query_string, " and ")!==FALSE || stripos($parsed_query_string, " or ")!==FALSE)// i.e there is "AND" or "OR" in the query_string.
																	{
																		die("<script>alert('Composite condition check in \'WHERE\' clause in \'SELECT\' query is not yet programmed.');</script>");
																	}
																	else// i.e. "AND" or "OR" clause is not used.
																	{
																		// checking if any of the following is present- ">", "<", "=", ">=", "<="
																		if(strpos($parsed_query_string, ">")===FALSE && strpos($parsed_query_string, "<")===FALSE && strpos($parsed_query_string, "=")===FALSE)// i.e. if no conditional operator is used in the condition
																			die("<script>alert('No valid condition is given in \'WHERE\' clause.');</script>");
																		if(strrpos($parsed_query_string, ">")===strlen($parsed_query_string)-1 || strrpos($parsed_query_string, "<")===STRLEN($parsed_query_string)-1 && strrpos($parsed_query_string, "=")===STRLEN($parsed_query_string)-1)
																			die("<script>alert('\'select\' query terminated incorrectly.');</script>");
																		$parsed_query_string=trim_all(substr($parsed_query_string, strlen("WHERE")));// taking the part of the query_string following "WHERE". $parsed_query_string now has the condition to be checked for.

																		//checking for occurrence of multiple relational operators in the query_string.
																		if(substr_count($parsed_query_string, ">")>1 || substr_count($parsed_query_string, "<")>1 || substr_count($parsed_query_string, "=")>1)
																			die("<script>alert('\'SELECT\' query is not in proper format');</script>");
																		if(strrpos($parsed_query_string, ">")!==FALSE)
																		{
																			if(substr_count($parsed_query_string, ">")>1 || strrpos($parsed_query_string, "<")!==FALSE || (strrpos($parsed_query_string, ">=")===FALSE && strrpos($parsed_query_string, "=")!==FALSE))
																				die("<script>alert('\'SELECT\' query is not in proper format');</script>");
																		}
																		if(strrpos($parsed_query_string, "<")!==FALSE)
																		{
																			if(substr_count($parsed_query_string, "<")>1 || strrpos($parsed_query_string, ">")!==FALSE || (strrpos($parsed_query_string, "<=")===FALSE && strrpos($parsed_query_string, "=")!==FALSE))
																				die("<script>alert('\'SELECT\' query is not in proper format');</script>");
																		}
																		if(strrpos($parsed_query_string, "=")!==FALSE)
																		{
																			if(substr_count($parsed_query_string, "=")>1 || (strrpos($parsed_query_string, "<=")===FALSE && strrpos($parsed_query_string, "<")!==FALSE) || (strrpos($parsed_query_string, ">=")===FALSE && strrpos($parsed_query_string, ">")!==FALSE))
																				die("<script>alert('\'SELECT\' query is not in proper format');</script>");
																		}

																		$parsed_query_string=trim_all(substr($parsed_query_string, 1));// take the trimmed part of query_string following '*'
																		if(stripos($parsed_query_string, "FROM")===0)// i.e. "FROM" clause is placed just after "*"
																		{
																			$parsed_query_string=trim_all(substr($parsed_query_string, strlen("FROM")));// take the trimmed part of query_string following 'FROM' clause.
																			$tab_name=trim_all(substr($parsed_query_string, 0, stripos($parsed_query_string, " where ")));
																			$condition=trim_all(substr($parsed_query_string, stripos($parsed_query_string, " where ")+7));// taking the condition in the "WHERE" clause in the variable $condition
																			if(strpos($tab_name, " ")===FALSE)
																			{
																				//checking whether the specified tab_name is present in the table_list.knrs file under the selected DB.
																				$table_list_file="databases/".$_SESSION['uname_query']."/".$_SESSION['db_selected_query']."/table_list.knrs";
																				if(stripos(file_get_contents($table_list_file), "\n".$tab_name."\n")!==FALSE || stripos(file_get_contents($table_list_file), $tab_name."\n")===0)
																				{
																					$tab_data_file="databases/".$_SESSION['uname_query']."/".$_SESSION['db_selected_query']."/".$tab_name."_data.knrs";
																					$tab_schema_file="databases/".$_SESSION['uname_query']."/".$_SESSION['db_selected_query']."/".$tab_name."_schema.knrs";
																					if(filesize($tab_data_file)>0)
																					{
																						$substr=NULL;
																						$separater_string="#$**$#";
																						$pos1=0;
																						$pos2=0;
																						$data=NULL;
																						echo "<script> var div_string=\"<table border=1 bgcolor=cyan width='100%' style='table-layout:fixed'>\"</script>";
																						$arr=file($tab_schema_file);
																						echo "<script> div_string+=\"<tr>\"</script>";
																						$file_type_attr_arr=array();//Array of all the attributes of type=file
																						$attr_no=0;
																						$file_attr_present=false;
																						foreach($arr as $attr)
																						{
																							$attr_no++;
																							$attr_type=substr($attr, strpos($attr,'->')+2, strrpos($attr,'->') - strpos($attr,'->') - 2);
																							$attr=substr($attr, 0, strpos($attr,'->'));
																							if($attr_type=='file')
																							{
																								$file_type_attr_arr[]=$attr_no;
																								if($file_attr_present==false)
																								{
																									$file_attr_present=true;
																									$file_extensions=array();
																									$file_extensions['archive']=array('7z', 'cab', 'jar', 'rar', 'rpm', 'tar', 'zip');
																									$file_extensions['document']=array('doc', 'docx', 'rtf','info','txt');
																									$file_extensions['image']=array('jpg', 'jpeg', 'bmp', 'gif', 'ico', 'png', 'tiff',);
																									$file_extensions['web']=array('html', 'htm', 'xml', 'xhtml');
																									$file_extensions['audio']=array('wma', 'mp2', 'mp3', 'amr', 'mid');
																									$file_extensions['video']=array('3gp', 'avi', 'dat', 'flv', 'mkv', 'mpeg', 'swf', 'mp4');
																									$file_extensions['script']=array('cmd', 'php', 'pl', 'py', 'asp', 'jsp', 'cgi', 'js');
																									$file_extensions['executable']=array('bat', 'exe', 'vb');
																									$file_extensions['ebook']=array('pdf');
																								}
																							}
																							echo "<script>div_string+=\"<th style='word-wrap:break-word;'><font size=5 face='lucida fax'>".$attr."</font>\";</script>";
																						}
																						echo "<script>div_string+=\"</tr>\";</script>";
																						
																						//Data retrieval
																						$record_no=0;
																						$arr=file($tab_data_file);
																						foreach($arr as $line)
																						{
																							$record_no++;
																							$attr_no=0;
																							$substr1=$line;
																							echo "<script> div_string+=\"<tr>\"</script>";
																							$substr1=substr($substr1,6,strlen($substr1)-6);
																							while(($pos2+strlen($separater_string))<=strlen($substr1))
																							{
																								$attr_no++;//increasing the attribute no to get access to the value of the attributes in each record one by one.
																								$file_type_attr=false;
																								if(in_array($attr_no, $file_type_attr_arr))
																									$file_type_attr=true;
																								$pos1=strpos($substr1,$separater_string);
																								$data=substr($substr1,0,$pos1);
																								if($file_type_attr==false)
																									echo "<script> div_string+=\"<td align=center style='font-size:20;word-wrap:break-word;'>"."<span style='background-color: aliceblue; -moz-border-radius: 3px; -webkit-border-radius: 3px; display: inline;'>".str_replace(" ", "&nbsp;", $data)."</span>"."</td>\"</script>";
																								else
																								{
																									$file_icon_image="";
																									$type_name="Unknown Type";
																									if(strrpos($data,'.')===false)
																									{
																										$file_icon_image=".\\Images\\file_icons\\unknown_file_icon.jpg";
																										if($data=="")
																											unset($file_icon_image);
																									}
																									else
																									{
																										foreach($file_extensions as $type => $type_ext_arr)//$type is the key, and $type_ext_arr==$file_extensions[$type].
																										{
																											$type_name=$type;
																											foreach($type_ext_arr as $ext)
																											{
																												if(strtolower(substr($data,strrpos($data,'.') + 1))==$ext)
																												{
																													$file_icon_image=".\\Images\\file_icons\\".$type_name."_icon.jpg";
																													break;
																												}
																											}
																											if($file_icon_image!=="")
																												break;
																										}
																									}
																									if(isset($file_icon_image) && $file_icon_image==="")
																										$file_icon_image=".\\Images\\file_icons\\unknown_file_icon.jpg";
																									$file_path="./databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_files/".$data;
																									$show_file_script="";
																									switch($type_name)
																									{
																										case "image":
																											$show_file_script=(file_exists($file_path)?"<img src=\"".$file_path."\"  style=\"max-width:100%; max-height:100%;\">":"<table width=100% height=100% style=\"table-layout:fixed\" border=1><tr><td width=100%>File Does Not Exist.</td></tr></table>");
																											break;
																										case "audio":
																											$show_file_script=(file_exists($file_path)?"<table border=1><tr><td align=center>	<table width=100%><tr><td align=center>Volume<td align=center>	<table border=0><tr><td align=center><button style=\"cursor:pointer; font-size:12; font-family:lucida fax\" onclick=\"document.getElementById('file_obj_show_rec_".$record_no."_attr_".$attr_no."').volume+=0.1;\">+</button></td><td align=center><button style=\"cursor:pointer; font-size:12; font-family:lucida fax\" onclick=\"document.getElementById('file_obj_show_rec_".$record_no."_attr_".$attr_no."').volume-=0.1;\">-</button></tr></table></tr></table>	</tr>	<tr><td align=center style=\'font-family:lucida fax; display:block\'><a href=\"".$file_path."\" target=\"_blank\"><font face='lucida fax' size=3>Click to download the audio file.</font></a></tr></table>	<table><tr><td><br><td><br></tr></table>"."<audio id='file_obj_show_rec_".$record_no."_attr_".$attr_no."' hidden='true' autostart='false' style=\"max-width:100%; max-height:100%;\" controls=\"\">	<source src=\"".$file_path."\" type=\"audio/".$ext."\" onerror=\"document.getElementById('file_show_rec_".$record_no."_attr_".$attr_no.").innerHTML='<table border=1><tr><td align=center style=\'font-family:lucida fax; display:block\'>Your browser does not support the audio format.<br><a href=\'".$file_path."\' target=\'_blank\'>click here to download it.</a></tr></table>'\">	</audio>":"File Does Not Exist.");
																											break;
																										case "video":
																											$show_file_script=(file_exists($file_path)?"<video id='file_obj_show_rec_".$record_no."_attr_".$attr_no."' autostart='false' style=\"max-width:100%; max-height:100%;\" controls>	<source src=\"".$file_path."\" type=\"video/".$ext."\" onerror=\"document.getElementById('file_show_rec_".$record_no."_attr_".$attr_no."').innerHTML='<table border=1><tr><td align=center style=\'font-family:lucida fax; display:block\'>Video format not supported.<br><a href=\'".$file_path."\' target=\'_blank\'>click to download it.</a></tr></table>'\">	</video>":"File Does Not Exist.");
																											break;
																										default:
																											if($data!=="")
																												$show_file_script="<font size=3 face='lucida fax'><table border=1><tr><td align=center>No Preview Available.<br><a href=\"".$file_path."\" target=\"_blank\">Click To Open In A New Tab</a></tr></table></font>";
																											else
																												$show_file_script="";
																									}
																									echo "<script> div_string+=\"<td align=center style='min-height:70; font-size:20; word-wrap:break-word;'>"."<span id='file_desc_rec_".$record_no."_attr_".$attr_no."' class='show' style='margin:15px 15px 15px 15px;'><table border=0 width=100% style='table-layout:fixed'><tr><td align=center width=30% align=center>".(isset($file_icon_image)?"<img src='".$file_icon_image."' height=50 width=50 title='".$type_name."'>":"")."<td align=center>".$data."</tr></table></span>"."</td>\"</script>";
																								}
																								$substr1=substr($substr1,$pos1+strlen($separater_string),(strlen($substr1)-($pos1+strlen($separater_string))));
																								if($substr1=="")
																									break;
																								$pos2=strpos($substr1,$separater_string);
																							}
																							echo "<script> div_string+=\"</tr>\"</script>";
																						}
																						echo "<script> div_string+=\"</table>\"</script>";
																						echo "<script>document.getElementById('output_div').innerHTML=div_string;</script>";
																					}
																					else
																						die("<font face='lucida fax' size=4><b>No Data Exists</b></font>");
																				}
																				else
																				{
																					die("<script>alert('Table:\"".$tab_name."\" not found');</script>");
																				}
																			}
																			else// i.e tablename includes space(" "), that is not allowed
																				die("<script>alert('\'SELECT\' query is not in proper format');</script>");
																		}
																		else
																		{
																			echo "-".$parsed_query_string."-";//
																			die("<script>alert('\'FROM\' clause misplaced in \'SELECT\' query.');</script>");
																		}









																		//parsing the attr_name in the "where" condition
																		$attr_name_length=( (strpos($parsed_query_string, ">")!==FALSE)? strpos($parsed_query_string, ">") : (strpos($parsed_query_string, "<")!==FALSE? strpos($parsed_query_string, "<") : strpos($parsed_query_string, "=")));
																		$attr=substr($parsed_query_string, 0, $attr_name_length);
																		$tab_schema_file="databases/";
																		//if()
																	}
																}
															}
														}
													}
												}
												else// i.e. if "*" is not immediately following 'select' in the query_string
												{
													if(strpos($parsed_query_string, "*")!==FALSE)// i.e. "*" is there in the query_string, but not adjacently with "SELECT" clause.
														die("<script>alert('\'SELECT\' query is not in proper format');</script>");
													else// i.e. "*" is not there in the query_string.
													{
													
													}
												}
											}
										}
									}
								}
								else
								{
									die("<script>alert('Select a DataBase first before running \'SELECT\' query.');</script>");
								}
								break;
							case "UPDATE":
								if(isset($_SESSION['db_selected_query']))
								{
									echo "<script>alert('\'UPDATE\' operation is not defined yet in the engine.');</script>";
								}
								else
								{
									die("<script>alert('Select a DataBase first before running \'UPDATE\' query.');</script>");
								}
								break;
							case "DELETE":
								if(isset($_SESSION['db_selected_query']))
								{
									echo "<script>alert('\'DELETE\' operation is not defined yet in the engine.');</script>";
								}
								else
								{
									die("<script>alert('Select a DataBase first before running \'DELETE\' query.');</script>");
								}
								break;
							case "ALTER":
								if(isset($_SESSION['db_selected_query']))
								{
									echo "<script>alert('\'ALTER\' operation is not defined yet in the engine.');</script>";
								}
								else
								{
									die("<script>alert('Select a DataBase first before running \'ALTER\' query.');</script>");
								}
								break;
							case "DROP":
								$parsed_query_string=trim_all(substr($query_string, strlen("DROP")));// getting the part of the query-sring following "drop"
								if(strpos($parsed_query_string, "DATABASE ")===0 || strpos($parsed_query_string, "TABLE ")===0)
								{
									if(strpos($parsed_query_string, " ")===strrpos($parsed_query_string, " "))
										echo "<script>alert('\'DROP\' operation is not defined yet in the engine.');</script>";
									else
										die ("<script>alert('\'DROP\' command is not in proper format.');</script>");
								}
								else
								{
									echo "<script>alert('\'DROP\' command is not in proper format.');</script>";
								}
								break;
							case "DISCONNECT":
								if($query_string=="DISCONNECT")
								{
									unset($_SESSION['db_selected_query']);
									unset($_SESSION['uname_query']);
									echo "<script>document.getElementById('log').innerHTML=\"Disconnected\\n----------------------\\n\" + document.getElementById('log').innerHTML + \"\\n\";</script>";
								}
								else
								{
									echo "<script>alert('Enter the command \'DISCONNECT\' in proper format.');</script>";
								}
								break;
							case "SHOW":
								$parsed_query_string=trim_all(substr($query_string, strlen("SHOW")));
								if($parsed_query_string=="DATABASES" || $parsed_query_string=="TABLES")
								{
									if($parsed_query_string=="DATABASES")// i.e. if the command is "show databases"
									{
										$user_acc_folder="databases/".$_SESSION['uname_query'];
										if(count(glob($user_acc_folder."/*")) === 0)
										{
											echo "<script>alert('No database found.')</script>";
										}
										else
										{
											$div_string="<div style='background-color:lightblue; float:left;'>";
											$div_string=$div_string."<font face='lucida fax'>";
											$div_string=$div_string."<h2>ACCOUNT: <font color=aliceblue>".$_SESSION['uname_query']."</font></h2>";
											$div_string=$div_string."<table border=1 bgcolor=cyan style='table-layout:fixed'>";
											$div_string=$div_string."	<tr>";
											$div_string=$div_string."		<th style='word-wrap: break-word; font-size:20; font-family:lucida fax'>DATABASES";
											$div_string=$div_string."	</tr>";
											$dp=opendir($user_acc_folder);
											while ($directory_member = readdir($dp))
											{
												if(is_dir($user_acc_folder."/".$directory_member) && $directory_member!="." && $directory_member!="..")
												{
													$div_string=$div_string."<tr><td align=center style='min-height:70; font-size:20; word-wrap:break-word;'>".$directory_member."</tr>";
												}
											}
											$div_string=$div_string."</table>";
											$div_string=$div_string."</font>";
											$div_string=$div_string."</div>";
											echo "<script>document.getElementById('output_div').innerHTML=\"".$div_string."\";</script>";
										}
									}
									else// i.e. if the command is "show tables"
									{
										if(!isset($_SESSION['db_selected_query']))
										{
											echo "<script>alert('You must first select a DataBase using \'USE\' command, before running the command \'SHOW TABLES\'.');</script>";
										}
										else// i.e. if the database has already been selected, then only "show tables"
										{
											$table_list_file="databases/".$_SESSION['uname_query']."/".$_SESSION['db_selected_query']."/table_list.knrs";
											if(filesize($table_list_file)>0)
											{
												$div_string="<div style='background-color:lightblue; float:left;'>";
												$div_string=$div_string."<font face='lucida fax'>";
												$div_string=$div_string."<h2>DATABASE: <font color=aliceblue>".$_SESSION['db_selected_query']."</font></h2>";
												$div_string=$div_string."<table border=1 bgcolor=cyan style='table-layout:fixed'>";
												$div_string=$div_string."	<tr>";
												$div_string=$div_string."		<th style='word-wrap: break-word; font-size:20; font-family:lucida fax'>TABLES";
												$div_string=$div_string."	</tr>";
												$arr=file($table_list_file);
												foreach($arr as $tab_name)
												{
													$div_string=$div_string."<tr><td align=center style='min-height:70; font-size:20; word-wrap:break-word;'>".substr($tab_name, 0, strlen($tab_name)-1)."</tr>";
												}
												$div_string=$div_string."</table>";
												$div_string=$div_string."</font>";
												$div_string=$div_string."</div>";
												echo "<script>document.getElementById('output_div').innerHTML=\"".$div_string."\";</script>";
											}
											else
											{
												echo "<script>alert('There is no table in the database: \'".$_SESSION['db_selected_query']."\'')</script>";
											}
										}
									}
								}
								else
								{
									echo "<script>alert('Enter the command \'SHOW\' in proper format.');</script>";
								}
								break;
							case "USE":
								$use_db_name=trim_all(substr($query_string, strlen("use")));
								if(strpos($use_db_name, " ")===FALSE)
								{
									if(is_dir("databases/".$_SESSION['uname_query']."/".$use_db_name))
									{
										$_SESSION['db_selected_query']=$use_db_name;
										echo "<script>document.getElementById('log').innerHTML=\"Selected DataBase:'".$_SESSION['db_selected_query']."'\\n----------------------\\n\" + document.getElementById('log').innerHTML + \"\\n\";</script>";
									}
									else
									{
										echo "<script>alert('DataBase:\'$use_db_name\' does not exist.');</script>";//
									}
								}
								else
								{
									die("<script>alert('\'USE\' query is not in proper format.');</script>");
								}
								break;
						}
						if(isset($invalid_command))
							unset($invalid_command);
						break;
					}
					else
					{
						$invalid_command=true;
					}
				}
				if(isset($invalid_command))
					echo "<script>alert('No valid command is entered.');</script>";
			}
		}
	}
?>