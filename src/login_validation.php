<?php
	$uname=$_REQUEST['uname'];
	$pwd=$_REQUEST['pwd'];
	if(strlen($uname)==0 || strlen($pwd)==0)
		$statusCode=0;
	else
	{
		$user_acc_file="user_acc_info/user_acc.knrs";
		if(!file_exists($user_acc_file))
			$statusCode=1;
		else
		{
			$arr=file($user_acc_file);
			foreach($arr as $user_acc_info)
			{
				$uname_registered=substr($user_acc_info,0,strpos($user_acc_info,'->'));
				$pwd_registered=substr($user_acc_info,(strpos($user_acc_info,'->')+2),strlen($user_acc_info)-strpos($user_acc_info,'->')-2-1);
				if(strtoupper($uname)===strtoupper($uname_registered))
				{
					if($pwd===$pwd_registered)
					{
            //checking if any other instance with the same user name is already active
            /*
            $login_list_file="user_acc_info/login_list.knrs";
            if( (strpos(file_get_contents($login_list_file), $uname."\n")===0) || (strpos(file_get_contents($login_list_file), "\n".$uname."\n")!==FALSE) )
            {
              $statusCode=4;
              break;
            }
            */

            session_start();
						$_SESSION['uname']=$uname_registered;
						$statusCode=3;

            //registering that the user has logged in.
            $login_register_file="user_acc_info/login_list.knrs";
            $fp=fopen($login_register_file,'a');
            fwrite($fp, $uname_registered."\n");
            fclose($fp);
					}
					else
						$statusCode=2;
					break;
				}
			}
      if(!isset($statusCode))
        $statusCode=1;
		}
	}
	echo json_encode(array('statusCode'=>$statusCode));
?>