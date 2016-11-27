<?php
require_once('php/config.php');
$_SESSION['lastActivity']=time();
if(isset($_SESSION[session_id()]['adminID']))
{
	if(isset($_GET['logout']))
	{
		require_once(PATH_TO_CLASS . 'AdminLogin.php');
		$checkAdmin=new AdminLogin();
		$checkAdmin->logoutAdmin();
	}else require_once(PATH_TO_PHP . 'admin.php');
}else {
	require_once(PATH_TO_CLASS . 'AdminLogin.php');
	$checkAdmin=new AdminLogin();
	$errMessage='';
	if(isset($_GET['errorMessage'])){
		switch($_GET['errorMessage']){
			case 'wrongPassword':
				$errMessage=$checkAdmin->getErrMessage(1);
			break;
		}
	}
	require_once(TOPHEADER);
	require_once(PATH_TO_HTML . 'login.htm');
	require_once(FOOTER);
}
?>