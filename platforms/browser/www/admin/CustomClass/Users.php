<?php
require_once 'DBconnect.php';

class Users{
    var $table='admin';
    var $tableAdmin='admin';
    var $tablelogin='userlogins';
    
    var $arrRoles=array(
        1 => 'Administrator',
        2 => 'Magacin',
        3 => 'Dostava'
    );
    
    function deleteAdminUser($id){ 
        if($id==1) return false;
        if(sizeof($this->getAdminUser($id))>0){
            $dbh=new DBconnect();
            $dbh=$dbh->sql;
            $sql='DELETE FROM ' . PRETABLE . $this->tableAdmin . ' WHERE id=:id LIMIT 1';
            $sth=$dbh->prepare($sql);
            $success=$sth->execute(array(':id'=>(int)$id));
            $success=$success==1?true:false;

            if($success===true) return true;
            return $success;
        }else return false;
    }
    
    function deleteUser($id){ 
        if(sizeof($this->getUser($id))>0){
            $dbh=new DBconnect();
            $dbh=$dbh->sql;
            $sql='DELETE FROM ' . PRETABLE . $this->table . ' WHERE id=:id LIMIT 1';
            $sth=$dbh->prepare($sql);
            $success=$sth->execute(array(':id'=>(int)$id));
            $success=$success==1?true:false;

            if($success===true) return true;
            return $success;
        }else return false;
    }
    
    protected function getUserDetails($id){
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, username, name, email, roles, sorting, crdate, lsdate FROM ' . $table . ' WHERE id=:id ';
        $sql .='LIMIT 0,1';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)$id));
        $rec=$sth->fetch(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getAdminUser($id){
        $rec=array();
        $table=PRETABLE . $this->tableAdmin;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, username, name, email, roles FROM ' . $table . ' WHERE id=:id ';
        $sql .='LIMIT 0,1';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)$id));
        $rec=$sth->fetch(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getAllAdminUsers($islimit=false){
        $islimit=$islimit===false?'':' LIMIT 0, ' . $islimit;
        $rec=array();
        $table=PRETABLE . $this->tableAdmin;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, username, name, email, roles FROM ' . $table . ' WHERE id>:id ';
        $sql .='ORDER BY roles';
        $sql .=$islimit;
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)0));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getAllUsers($islimit=false){
        $islimit=$islimit===false?'':' LIMIT 0, ' . $islimit;
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, name_en, sorting FROM ' . $table . ' WHERE id>:id ';
        $sql .='ORDER BY sorting ASC';
        $sql .=$islimit;
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)0));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getAllUsersForExport(){
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, name_en, name_lv, esize, brochureurl, youtubeurl, description_en, description_lv, image, sorting FROM ' . $table . ' WHERE id>:id ';
        $sql .='ORDER BY sorting ASC';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)0));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    function loginUser($password){
        $return=false;
        if($password!=''){  
            $id=0;
            require_once 'Settings.php';
            $setting=new Settings();
            $rec=array();
            $dbh=new DBconnect();
            $dbh=$dbh->sql;
            $sql='SELECT id, name, username, email, password, username, roles FROM ' . PRETABLE . $this->table . ' where id>:id';
            $sth=$dbh->prepare($sql);
            $sth->execute(array(':id'=>(int)$id));
            $rec=$sth->fetchAll(PDO::FETCH_OBJ);
            if($rec!==false && sizeof($rec)>0){
                foreach($rec as $val){
                    $check=$setting->checkHash($password, $val->password);
                    if($check===true){
                        $id=$val->id;
                        $sql='SELECT id, name, username, email, password, username, roles FROM ' . PRETABLE . $this->table . ' where id=:id';
                        $sth=$dbh->prepare($sql);
                        $sth->execute(array(':id'=>(int)$id));
                        $rec=$sth->fetch(PDO::FETCH_OBJ);
                        return $rec;
                        break;
                    }
                }
            }
        }
        return $return;
    }
    
    protected function updateLastLogin($ipaddress, $userid){
        $table=PRETABLE . $this->tablelogin;
        $rec=array();
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='INSERT INTO ' . $table . ' (userid, crdate, ipaddress) VALUES (?, ?, ?)';
        $sth=$dbh->prepare($sql);
        $success=$sth->execute(array((int)$userid, date('Y-m-d H:i:s'), $ipaddress));
        if($success!==false) return true;
        else return false;
    }
    
    function addEditAdminUser(){
        $json=array();
        $id=$_POST['aEOE_id']>0?$this->updateUser(PRETABLE . $this->tableAdmin):$this->insertUser(PRETABLE . $this->tableAdmin);
        if($id!==false){
            $json['success']=1;
            $json['editid']=$_POST['aEOE_id'];
            $rec=$this->getAdminUser($id);
            $key=0;
            $json['content']=$this->wrapSingleRowAdminUser($rec, $key);
        }else{
            $json['success']=0;
            $json['message']='Error - ' . __FUNCTION__;
        }
        return $json;
    }
    
    function addEditUser(){
        $json=array();
        $id=$_POST['aEOE_id']>0?$this->updateUser():$this->insertUser();
        if($id!==false){
            $json['success']=1;
            $json['editid']=$_POST['aEOE_id'];
            $rec=$this->getUser($id);
            $key=0;
            $json['content']=$this->wrapSingleRowUser($rec, $key);
        }else{
            $json['success']=0;
            $json['message']='Error - ' . __FUNCTION__;
        }
        return $json;
    }
    
    protected function insertUser($table=''){
        require_once 'Settings.php';
        $setting=new Settings();
        $table=$table==''?PRETABLE . $this->table:$table;
        $rec=array();
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $a=array();
        foreach ($_POST as $key => $val) if(preg_match('/^aEOE_/', $key)==1 && $key!='aEOE_id') $a[preg_replace('/^aEOE_/', '', $key)]=$val;  
        if(true===array_key_exists('password', $a) && $a['password']=='') unset($a['password']);
        if(true===array_key_exists('password', $a) && $a['password']!='') $a['password']=$setting->generateSalt($a['password']);
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

        if($success!==false){
            $id=$dbh->lastInsertId();
            return $id;
        }
        else return false;
    }
    
    protected function updateUser($table=''){
        require_once 'Settings.php';
        $setting=new Settings();
        $table=$table==''?PRETABLE . $this->table:$table;
        $rec=array();
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $a=array();
        foreach ($_POST as $key => $val) if(preg_match('/^aEOE_/', $key)==1 && $key!='aEOE_id') $a[preg_replace('/^aEOE_/', '', $key)]=$val;
        if(true===array_key_exists('password', $a) && $a['password']=='') unset($a['password']);
        if(true===array_key_exists('password', $a) && $a['password']!='') $a['password']=$setting->generateSalt($a['password']);
        $sql='UPDATE ' . $table . ' SET ';
        $counter=0;
        foreach ($a as $keya => $vala){
                if($counter>0) $sql .=', ';
                $sql .=$keya . '=?';
                $counter++;
        }		
        $sql .='WHERE id=?';
        $sth=$dbh->prepare($sql);
        array_push($a, $_POST['aEOE_id']);
        $b=array();
        foreach ($a as $keyb =>$valb) $b[]=$valb;
        $success=$sth->execute($b);
        if($success!==false){
            return $_POST['aEOE_id'];
        }
        else return false;
    }
    
    function wrapAllAdminUsers($section){
        $content='';
        if(isset($_GET['exportcsv'])) return $this->exportAllUser();
        $rec=$this->getAllAdminUsers();
        $listUsers='';
        if(sizeof($rec)>0) foreach ($rec as $key => $val) $listUsers .=$this->wrapSingleRowAdminUser($val, $key, $section);                
        $content='';
        ob_start(); // start output buffer
        include (PATH_TO_HTML . 'tableAdminUser.htm');
        $content .= ob_get_contents(); // get contents of buffer
        ob_end_clean();
        return $content;
    }
    
    function wrapSingleRowAdminUser($rec, $key){            
            $class=$key%2==0?'odd':'even'; 
            $sorting=0;
            $content='';
            $content .='<div class="tr ' . $class . '" data-sorting="' . $sorting . '" data-id="' . $rec->id . '">';
            $content .='<div class="td">' . $rec->name . '</div>';
            $content .='<div class="td td4">' . $rec->username . '</div>';
            $content .='<div class="td td4">' . $this->arrRoles[$rec->roles] . '</div>';
            $content .='<div class="td td6">
                <div class="right">
                    <div class="pencil left"><a data-id="' . $rec->id . '" href="#" data-action="addedititem" data-context="wrapAddEditAdminUser" title="Uredi korisnika"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>';
            $content .=$rec->id>1?'<div class="delete left"><a class="deleteCourse" data-action="deleteitem" data-context="deleteAdminUser" href="#" data-id="' . $rec->id . '" data-title="' . $rec->name . '" title="Obriši korisnika"><i class="fa fa-trash" aria-hidden="true"></i></a></div>':'<div class="left"><div class="akafa"></div></div>';
            $content .='<div class="noFloat"></div>
                </div>
                <div class="noFloat"></div>
                </div>';
            $content .='</div>';
            return $content;
        }
    
    function wrapAddEditAdminUser($id=0, $isAjax=true){
            require_once 'Settings.php';
            $setting=new Settings();
            require_once 'Images.php';
            $image=new Images();
            $json=array();
            $pre=$isAjax===true?'../':'';
            $content=$fileUploader=$img='';
            if($id>0) $rec=$this->getAdminUser($id);
            else{
                $rec=new stdClass();
                $rec->username=$rec->name=$rec->email='';
                $rec->roles=1;
            } 
            $rolesSB=$setting->wrapSB($rec->roles, 'roles', $this->arrRoles);
            ob_start(); // start output buffer
            include ($pre . PATH_TO_HTML . 'addEditAdminUser.htm');
            $content .= ob_get_contents(); // get contents of buffer
            ob_end_clean();
            $json['success']=1;
            $json['content']=$content;
            return $json;
           return $content;
       }
       
       function exportAllUser(){
           require_once 'Settings.php';
           $setting=new Settings();
           $json=array();
           $rec=$this->getAllUsersForExport();
           if($rec!==false && sizeof($rec)>0){
               $filename=$setting->prepareRealLink('users-' . date('Y-m-d-H:i'));
               $pF=$_SERVER['DOCUMENT_ROOT'] . DOCUMENT_ROOT . '/' . PATH_TO_DOWNLOAD_CSV . $filename . '.csv';
               $handle = fopen($pF, 'w+');
               if(!file_exists($pF)){
                   $json['success']=3;
                   $json['message']='File is not exists, or not created . ' . $pF;
               }else{
                   $arrayExtraTitles=array();
                   $arrayTitles=array('Name of Room (EN)', 'Name of Room (LV)', 'Size of room', 'Description (EN)', 'Description (LV)', 'Brochure Link', 'YouTube', 'Image');
                   
               fputcsv($handle, $arrayTitles);
               
               foreach($rec as $val){
                   $arrValues=array($val->name_en, $val->name_lv, $val->esize, $val->description_en, $val->description_lv, $val->brochureurl, $val->youtubeurl, dirname($setting->getCurrPathDir()) . '/' . substr(URL_TO_PHOTOS, 3) . $val->image);
                   fputcsv($handle, $arrValues);
               }
               
               @header('Content-Type: application/csv; charset=utf-8');
               @header('Content-Disposition: attachment; filename="'.$filename.'.csv"');
               readfile($pF);
               exit;
               
               $json['success']=1;
               }
           }else{
               $json['success']=0;
               $json['message']='This event doesn\'t have any attendee created.';
           }
           
           return $json;
       }
}