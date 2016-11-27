<?php
require_once('Users.php');

class UsersFE extends Users{
    
    function logoutFEUser(){
        require_once 'Settings.php';
        $setting=new Settings();
        $json=array();
        if(isset($_SESSION['whatUserOnDetails']) && $setting->checkIsUserLogedAndToken($_SESSION['whatUserOnDetails']->id)===true){
            unset($_SESSION['whatUserOn']);
            unset($_SESSION['whatUserOnDetails']);
            $json['success']=1;
        }else{
            $json['success']=0;
        }
        return $json;
    }
    
    function checkIsUserLoggedIn(){
        require_once 'Settings.php';
        $setting=new Settings();
        $json=array();
        if(isset($_SESSION['whatUserOnDetails']) && $setting->checkIsUserLogedAndToken($_SESSION['whatUserOnDetails']->id)===true){
            if($_SESSION['whatUserOnDetails']->roles==3){
                    require_once 'ProductionsFE.php';
                    $production=new ProductionsFE();
                    $todayEdate=isset($_SESSION['currentProductionDate'])?$_SESSION['currentProductionDate']:date('Y-m-d H:i:s');
                    $rec1=$production->getAllProductedForDateP($todayEdate);
                    if($rec1!==false && sizeof($rec1)>0){
                        $json['success']=1;
                        $json['name']=$_SESSION['whatUserOnDetails']->name;
                        $json['roles']=$_SESSION['whatUserOnDetails']->roles;
                        $json['redirect']=DOCUMENT_ROOT . '/my';

                        $json['results']=$setting->getJSONPrivateSections();
                    }else{
                        $json['success']=6;
                        $json['message']='Nema ni jednog proizvoda u magacinu za danas!';
                    }
                }else{
                    $json['success']=1;
                    $json['name']=$_SESSION['whatUserOnDetails']->name;
                    $json['roles']=$_SESSION['whatUserOnDetails']->roles;
                    $json['redirect']=DOCUMENT_ROOT . '/my';

                    $json['results']=$setting->getJSONPrivateSections();
                }
        }else{
            $json['success']=0;
        }
        return $json;
    }
    
    function loginFEUser($password){
        require_once 'Settings.php';
        $setting=new Settings();
        $json=array();
        if(!$password){
            $json['success']=0;
            $json['message']='Provjerite da li ste unijeli ispravan password.';
        }else{
            $rec=$this->loginUser($password);
            if($rec!==false && sizeof($rec)>0){
                $this->loginFEUserAfterRegistration($rec->id);
                if($_SESSION['whatUserOnDetails']->roles==3){
                    require_once 'ProductionsFE.php';
                    $production=new ProductionsFE();
                    $todayEdate=isset($_SESSION['currentProductionDate'])?$_SESSION['currentProductionDate']:date('Y-m-d H:i:s');
                    $rec1=$production->getAllProductedForDateP($todayEdate);
                    if($rec1!==false && sizeof($rec1)>0){
                        $json['success']=1;
                        $json['name']=$_SESSION['whatUserOnDetails']->name;
                        $json['roles']=$_SESSION['whatUserOnDetails']->roles;
                        $json['redirect']=DOCUMENT_ROOT . '/my';

                        $json['results']=$setting->getJSONPrivateSections();
                    }else{
                        $json['success']=6;
                        $json['message']='Nema ni jednog proizvoda u magacinu za danas!';
                    }
                }else{
                    $json['success']=1;
                    $json['name']=$_SESSION['whatUserOnDetails']->name;
                    $json['roles']=$_SESSION['whatUserOnDetails']->roles;
                    $json['redirect']=DOCUMENT_ROOT . '/my';

                    $json['results']=$setting->getJSONPrivateSections();
                }
            }else{
                $json['success']=0;
                $json['message']='Unijeli ste pogrešan password.';
            }
        }
        return $json;
    }
    
    function loginFEUserAfterRegistration($id){
        require_once 'Settings.php';
        $setting=new Settings();
        
        $_SESSION['whatUserOn']['id'][$id]=md5(session_id() . $id . $_SERVER['SERVER_NAME']);         
        $rec2=$this->getUserDetails($id);
        if($_SESSION['whatUserOn']['id'][$rec2->id]==md5(session_id() . $id . $_SERVER['SERVER_NAME'])){
            $_SESSION['whatUserOnDetails']=$rec2;
            $ip=$setting->_ip();
            $this->updateLastLogin($ip, $_SESSION['whatUserOnDetails']->id);
        }
        
    }
    
    function wrapMyPrivateFE($section){
        require_once 'Settings.php';
        $setting=new Settings();
        
        $content='';
        if(isset($_SESSION['whatUserOnDetails']) && $setting->checkIsUserLogedAndToken($_SESSION['whatUserOnDetails']->id)===true){
            
        }else{
            $content .=$setting->wrapNonLoggedMessage();
        }
        
        return $content;
    }
}
