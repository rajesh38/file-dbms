<html>
<head>
	<style type="text/css">
		span.show
		{
			display:inline-block;
		}
		span.not_show
		{
			display:none;
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

		function sort_asc(attr_no, attr_type)
		{
			try
			{
				obj=pullAjax();
				obj.onreadystatechange=function()
				{
					if(obj.readyState==4)
					{
						location.reload();
					}
				}
				obj.open("GET", "sort_asc.php?attr_no="+attr_no+"&attr_type="+attr_type, true);
				obj.send(null);
			}
			catch(err)
			{
				alert(err);
			}
		}

		function sort_desc(attr_no, attr_type)
		{
			try
			{
				obj=pullAjax();
				obj.onreadystatechange=function()
				{
					if(obj.readyState==4)
					{
						location.reload();
					}
				}
				obj.open("GET", "sort_desc.php?attr_no="+attr_no+"&attr_type="+attr_type, true);
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
	<?php
		if(!isset($_SESSION))
			session_start();
		$tab_data_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_data.knrs";
		$tab_schema_file="databases/$_SESSION[uname]/".$_SESSION['db_selected']."/".$_SESSION['tab_selected']."_schema.knrs";
		if(!file_exists($tab_data_file))
		{
			echo "<script language=\"javascript\">
				parent.frames['right_container_left_top'].location.reload();
				</script>";
			die("table not found");
		}
		if(filesize($tab_data_file)>0)
		{
			$substr=NULL;
			$separater_string="#$**$#";
			$pos1=0;
			$pos2=0;
			$data=NULL;
			echo "<table border=1 bgcolor=cyan width='100%' style=\"table-layout:fixed\">";
			$arr=file($tab_schema_file);
			echo "<tr>";
			echo "<th bgcolor=turquoise width='70px' style=\"word-wrap: break-word;\">DELETE";
			echo "<th bgcolor=turquoise width='70px' style=\"word-wrap: break-word;\">UPDATE";
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
				if($attr_type!=='file')
					echo "<th style=\"word-wrap:break-word;\"><font size=5 face='lucida fax'>".$attr."</font><br><img id='sort_asc".$attr_no."' src='images/sort_ascending.jpg' title='SORT ASCENDING' style=\"cursor:pointer;\" onclick=\"sort_asc('".$attr_no."', '".$attr_type."')\"> <img id='sort_desc".$attr_no."' src='images/sort_descending.jpg' title='SORT DESCENDING' style=\"cursor:pointer;\" onclick=\"sort_desc('".$attr_no."', '".$attr_type."');\">";
				else
					echo "<th style=\"word-wrap:break-word;\"><font size=5 face='lucida fax'>".$attr."</font></th>";
			}
			echo "</tr>";
			
			//Data retrieval
			$record_no=0;
			$arr=file($tab_data_file);
			foreach($arr as $line)
			{
				$record_no++;
				$attr_no=0;
				$substr1=$line;
				echo "<tr>";
				$substr1=substr($substr1,6,strlen($substr1)-6);
				echo "<td bgcolor=turquoise align=center><div style=\"cursor:pointer\" title=\"Delete This record\" onclick=\"if(confirm('Are you sure to delete this record?')==false){return false;} else{location.href='delete_record.php?record_id_delete=".$record_no."';}\"><font color=red><img src='images/delete_object1.jpg' width=50%></font></div> </td>";
				echo "<td bgcolor=turquoise align=center><div style=\"cursor:pointer\" title=\"Update This record\" onclick=\"{location.href='update_record_form.php?record_id_update=".$record_no."';}\"><font color=red><img src='images/edit_object2.jpg' width=50%></font></div> </td>";
				while(($pos2+strlen($separater_string))<=strlen($substr1))
				{
					$attr_no++;//increasing the attribute no to get access to the value of the attributes in each record one by one.
					$file_type_attr=false;
					if(in_array($attr_no, $file_type_attr_arr))
						$file_type_attr=true;
					$pos1=strpos($substr1,$separater_string);
					$data=substr($substr1,0,$pos1);
					if($file_type_attr==false)
						echo "<td align=center style=\"font-size:20;word-wrap:break-word;\">"."<span style=\"background-color: aliceblue; -moz-border-radius: 3px; -webkit-border-radius: 3px; display: inline;\">".str_replace(" ", "&nbsp;", $data)."</span>"."</td>";
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
						echo "<td align=center style=\"min-height:70; font-size:20; word-wrap:break-word;\">"."<span id='file_show_rec_".$record_no."_attr_".$attr_no."' class='not_show' style=\"margin:10px 10px 10px 10px;\" onMouseOut=\"this.className='not_show'; document.getElementById('file_desc_rec_".$record_no."_attr_".$attr_no."').className='show'; ".($type_name!="image"?"try{if(document.getElementById('file_obj_show_rec_".$record_no."_attr_".$attr_no."')!=null)document.getElementById('file_obj_show_rec_".$record_no."_attr_".$attr_no."').pause();} catch(err){alert(err)}":"")."\">".$show_file_script."</span>"."<span id='file_desc_rec_".$record_no."_attr_".$attr_no."' class='show' style=\"margin:15px 15px 15px 15px;\"><table border=0 width=100% style=\"table-layout:fixed\"><tr><td align=center width=30% align=center>".(isset($file_icon_image)?"<img src='".$file_icon_image."' height=50 width=50 title=\"".$type_name."\"/>":"")."<td align=center onMouseOver=\"document.getElementById('file_show_rec_".$record_no."_attr_".$attr_no."').className='show'; document.getElementById('file_desc_rec_".$record_no."_attr_".$attr_no."').className='not_show'; ".($type_name!="image"?"try{if(document.getElementById('file_obj_show_rec_".$record_no."_attr_".$attr_no."')!=null && document.getElementById('file_obj_show_rec_".$record_no."_attr_".$attr_no."').paused){document.getElementById('file_obj_show_rec_".$record_no."_attr_".$attr_no."').play();}} catch(err){alert(err)}":"")."\">".$data."</tr></table></span>"."</td>";
					}
					$substr1=substr($substr1,$pos1+strlen($separater_string),(strlen($substr1)-($pos1+strlen($separater_string))));
					if($substr1=="")
						break;
					$pos2=strpos($substr1,$separater_string);
				}
				echo "</tr>";
			}
			echo "</table>";
		}
		else
			die("<font face='lucida fax' size=4><b>No Data Exists</b></font>");
		?>
	</center>
	</body>
</html>