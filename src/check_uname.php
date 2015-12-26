<?php
	$uname=$_REQUEST['uname'];
	if(strlen($uname)>0)
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
				if(strtoupper($uname)==strtoupper($uname_registered))
				{
					$statusCode=0;
					break;
				}
			}
			if(!isset($statusCode))
				$statusCode=1;
		}
	}
	echo json_encode(array('statusCode'=>$statusCode));
?>