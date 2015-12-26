<?php
function removeDir($dir)
{
	if(file_exists($dir))
	{
		// create directory pointer
		$dp = opendir($dir) or die ('ERROR: Cannot open directory');
		// read directory contents
		// delete files found
		// call itself recursively if directories found
		while ($file = readdir($dp))
		{
			if ($file != '.' && $file != '..')
			{
				if (is_file("$dir/$file"))
				{
					unlink("$dir/$file");
				}
				else if (is_dir("$dir/$file"))
				{
					removeDir("$dir/$file");
				}
			}
		}
		// close directory pointer
		closedir($dp);
		// remove now-empty directory
		if (rmdir($dir))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

	$db_delete=$_REQUEST['db_delete'];
  session_start();
	if(removeDir("databases/$_SESSION[uname]/".$db_delete)==false)
		echo "<script language='javascript'> alert('database:'.$db_delete.' could not be dropped')</script>";
  else
  {
  	echo "<script language='javascript'> alert('database:'.$db_delete.' has been dropped')</script>";
    $db_parent_dir='databases/$_SESSION[uname]';
		$db_count=count(glob("$db_parent_dir/*"));
    if(isset($_SESSION['db_selected']) && ($_SESSION['db_selected']==$db_delete || $db_count==0))
    {
      $_SESSION['db_selected']=NULL;
      echo "<script language=\"javascript\">parent.frames['right_container'].location.href=\"default_right_container.php\";</script>";
    }
  }
	include "db_list.php";
?>