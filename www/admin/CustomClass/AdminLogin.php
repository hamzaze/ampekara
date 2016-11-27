<?php
require_once('DBconnect.php');
//require_once('Mail.php');
//require_once('Mail/mime.php');
class AdminLogin extends DBconnect 
{
	var $isAdminLogged;
	var $adminRoles;
	var $adminID;
	var $backURLAfterLogin;
	
	var $table='admin';
	
	var $SMTP;
	
	var $arrTitles=array(0=>'', 1=>'Dr.', 2=>'Mr.', 3=>'Mrs.', 4=>'Ms.');
	
	function removeAdmin($id){
		if(sizeof($this->getAdmin($id))>0){
			$dbh=new DBconnect();
			$dbh=$dbh->sql;
			$sql='DELETE FROM ' . PRETABLE . $this->table . ' WHERE id=:id LIMIT 1';
			$sth=$dbh->prepare($sql);
			$success=$sth->execute(array(':id'=>(int)$id));
			$success=$success==1?true:false;
			return $success;
		}else return false;
	}
        
        function getComedianInfo($id=1){
		$rec=array();
		$dbh=new DBconnect();
		$dbh=$dbh->sql;
		$sql='SELECT id, twitter FROM ' . PRETABLE . $this->table .' where id=:id';
		$sth=$dbh->prepare($sql);
		$sth->execute(array(':id'=>(int)$id));
		$rec=$sth->fetch(PDO::FETCH_OBJ);
		return $rec;
	}
	
	private function getAdmin($id){
		$rec=array();
		$dbh=new DBconnect();
		$dbh=$dbh->sql;
		$sql='SELECT id FROM ' . PRETABLE . $this->table .' where id=:id';
		$sth=$dbh->prepare($sql);
		$sth->execute(array(':id'=>(int)$id));
		$rec=$sth->fetch(PDO::FETCH_OBJ);
		return $rec;
	}
	
	function getAdminS($id){
		$rec=array();
		$dbh=new DBconnect();
		$dbh=$dbh->sql;
		$sql='SELECT id, firstname, lastname, email, password, username, usergroup FROM ' . PRETABLE . $this->table .' where id=:id';
		$sth=$dbh->prepare($sql);
		$sth->execute(array(':id'=>(int)$id));
		$rec=$sth->fetch(PDO::FETCH_OBJ);
		return $rec;
	}
	
	private function checkIsExists($email){
		$rec=array();
		$dbh=new DBconnect();
		$dbh=$dbh->sql;
		$sql='SELECT id FROM ' . PRETABLE . $this->table .' where email=?';
		$sth=$dbh->prepare($sql);
		$sth->execute(array($email));
		$rec=$sth->fetch(PDO::FETCH_OBJ);
		return $rec;
	}
	
	private function updateAdmin(){
		$table=PRETABLE . $this->table;
		$rec=array();
		$dbh=new DBconnect();
		$dbh=$dbh->sql;
		$a=array();
		foreach ($_POST as $key => $val) if(preg_match('/^aEOE_/', $key)==1 && $key!='aEOE_id' && $key!='aEOE_passwordagain') $a[preg_replace('/^aEOE_/', '', $key)]=$key=='aEOE_password'?md5($val):$val;
		$sql='UPDATE ' . $table . ' SET ';
		$counter=0;
		foreach ($a as $keya => $vala){
			if($counter>0) $sql .=', ';
			$sql .=$keya . '=?';
			$counter++;
		}
		$sql .=', lsdate=? ';
		$sql .='WHERE id=?';
		$sth=$dbh->prepare($sql);
		array_push($a, date('Y-m-d H:i:s'), $_POST['aEOE_id']);
		$b=array();
		foreach ($a as $keyb =>$valb) $b[]=$valb;
		$success=$sth->execute($b);
		if($success!==false) return $_POST['aEOE_id'];
		else return false;
	}
	
	private function insertAdmin(){
		if($this->checkIsExists($_POST['aEOE_email'])!==false) return 'sameemail';
		$table=PRETABLE . $this->table;
		$rec=array();
		$dbh=new DBconnect();
		$dbh=$dbh->sql;
		$a=array();
		foreach ($_POST as $key => $val) if(preg_match('/^aEOE_/', $key)==1 && $key!='aEOE_id' && $key!='aEOE_passwordagain') $a[preg_replace('/^aEOE_/', '', $key)]=$key=='aEOE_password'?md5($val):$val;
		$sql='INSERT INTO ' . $table . ' (';
		$counter=0;
		$string='';
		foreach ($a as $keya => $vala){
			if($counter>0){
				$string .=', ';
				$sql .=', ';
			}
			$sql .=$keya;
			$string .='?';
			$counter++;
		}
		$sql .=',crdate)';
		$string .=',?';
		$sql .='VALUES (' . $string . ')';
		$sth=$dbh->prepare($sql);
		array_push($a, date('Y-m-d H:i:s'));
		$b=array();
		foreach ($a as $keyb =>$valb) $b[]=$valb;
		$success=$sth->execute($b);
		
		if($success!==false) return $dbh->lastInsertId();
		else return false;
	}
	
	function addEditAdmin(){
		if($_POST['aEOE_id']>0) return $this->updateAdmin();
		else return $this->insertAdmin();
	}
		
	function prepareSMTPInformations()
	{
		$host = "ssl://smtp.gmail.com";
		$username = "hh@pixelgraf.dk";
		$password = "pravo2dio";
		$debug = false;
		$port="465";
		
		$rec=array();
		$rec['host'] = $host;
		$rec['username'] = $username;
		$rec['password'] = $password;
		$rec['debug'] = false;
		$rec['port']=$port;
		return $rec;
		
		$rec=array();
		$rec['host'] = "smtp.com";
		$rec['username'] = "info@myorthoevidence.com";
		$rec['password'] = "mohitB";
		$rec['debug'] = false;
		$rec['port']="2525";
		return $rec;
	}
	
	function updateAdministratorPasswords(){
            require_once 'Settings.php';
            $setting=new Settings();
            $dbh=new DBconnect();
            $dbh=$dbh->sql;
            $json=array();
            $a=array();
            if(isset($_POST['aEOE_password1']) && $_POST['aEOE_password1']!=''){
                $a[2]=array('password'=>$setting->generateSalt(trim($_POST['aEOE_password1'])));
            }
            if(isset($_POST['aEOE_password2']) && $_POST['aEOE_password2']!=''){
                $a[3]=array('password'=>$setting->generateSalt(trim($_POST['aEOE_password2'])));
            }
            if(isset($_POST['aEOE_password3']) && $_POST['aEOE_password3']!=''){
                $a[3]=array('password'=>$setting->generateSalt(trim($_POST['aEOE_password3'])));
            }
            $sql='UPDATE ' . PRETABLE . $this->table .' SET password=? WHERE id=?';
            $sth=$dbh->prepare($sql);
            foreach($a as $key => $val){
                $b=array($val['password'], $key);
                $success=$sth->execute($b);
            }
            if($success!==false){
                $json['success']=1;
                $json['content']='<h3>Your admin passwords are updated successfully</h3>';
            }
            return $json;
        }
	
	function checkIsPost($password)
	{
            $return=false;
            if($password!='')
            {  
                $id=0;
                require_once 'Settings.php';
                $setting=new Settings();
                $rec=array();
                $dbh=new DBconnect();
                $dbh=$dbh->sql;
                $sql='SELECT id, name, email, password, username, roles FROM ' . PRETABLE . $this->table .' where id>:id';
                $sth=$dbh->prepare($sql);
                $sth->execute(array(':id'=>(int)$id));
                $rec=$sth->fetchAll(PDO::FETCH_OBJ);
                if($rec!==false && sizeof($rec)>0){
                    foreach($rec as $val){
                        $check=$setting->checkHash($password, $val->password);
                        if($check===true){
                            foreach($val as $key => $val1){
                                if($key!=='password'){
                                    $_SESSION[session_id()]['admin' . strtoupper($key)]=$val1;
                                }
                            }
                            return true;
                        }
                    }
                }
            }
            return $return;
	}
	

	function logoutAdmin()
	{		
		if(isset($_GET['logout']) && $_GET['logout']==1) 
		{	
			$expire=time()-60*60;
			setcookie('USER_NAME', '', $expire);
			setcookie('isAdminLogged', '', $expire);
			setcookie('adminID', '', $expire);
			foreach ($_COOKIE as $key => $val){
				setcookie($key, '', $expire);
			}
			//echo "logout";		
			session_destroy();
			$this->setIsLogged(0);
			header('Location: index.php');			
		}
		
	}
	
	function countPosts($id){
		require_once('SubSubSections.php');
		$content=new SubSubSections();
		return $content->countPosts($id);
	}
	
	private function getIsLogged()
	{
		return $this->isAdminLogged;
	}
	private function setIsLogged($value)
	{
		$this->isAdminLogged=$value;
	}
	
	function getErrMessage($type){
		$content='';
		switch($type){
			case 1:
			$content .='<p>Sorry, you entered an incorrect password please try again!</p>';
			break;
		}
		return $content;
	}
	
	function checkIsMail($string){
		$reMail='//';
		$content=preg_match($reMail, $string)==1?true:false;
		return $content;
	}
	

	
	function wrapLoginForm(){
		if(!isset($_GET['action'])){
			return 'login.htm';
		}else{
			$query=explode('|', base64_decode($_GET['action']));
			if(is_array($query)){
				switch ($query[0]){
					case 'activate':
						require_once('Educators.php');
						$educator=new Educators();
						switch($educator->checkIsActivated($query[1])){
							case 1:
							return 'login.htm';	
							break;
							case 2:
							return 'prelogin.htm';	
							break;
							case 3:
							return 'notEducator.htm';	
							break;
							
						}
					break;
				}
			}	
		}
	}
	
	function checkForActionsOutside(){
		if(isset($_GET['action'])){
			$query=explode('|', base64_decode($_GET['action']));
			if(is_array($query)){
				switch ($query[0]){
					case 'activatecontribution':
					$_SESSION['activatecontribution']=$query[1];
					header('Location: admin/index.php?section=1');	
					break;
				}
			}
		}
	}
	
	function getCurrPathDir(){
		$string=dirname("http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI']);
		return $string;
	}
	
	
	
}
?>