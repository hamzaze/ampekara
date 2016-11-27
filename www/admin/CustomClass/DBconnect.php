<?PHP
class DBconnect
{
	var $check;
	var $objekt='*';
	var $list;
	var $counter=0;
	
	var $base='hamzaze_am'; 
	var $localhost='localhost';
	var $user='hamzaze_am';
	var $password='zmCA25@y;4VA';
	
	var $success;
	
	function __construct() {
            try{
                $this->sql=new PDO('mysql:host=' . $this->localhost . ';dbname=' . $this->base, $this->user, $this->password);
                $this->sql->exec('set names utf8');
		$this->sql->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                return $this->sql;
            }catch(Exception $e){
                echo $e->getMessage();
            }		
	}
}
?>