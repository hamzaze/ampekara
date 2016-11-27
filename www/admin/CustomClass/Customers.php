<?php
require_once 'DBconnect.php';

class Customers{
    var $table='customers';
    
    var $arrCustomerTypes=array(
        '',
        'Interna Prodavnica',
        'Externa Prodavnica',
        'Zaposlenici'
    );
    
    function reSortSortings($ids){
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        //$sql='INSERT INTO ' . $table . ' (id,sorting) VALUES :values ON DUPLICATE KEY UPDATE sorting=VALUES(sorting)';
        $sql='INSERT INTO ' . $table . ' (id,sorting) VALUES ' . $ids . ' ON DUPLICATE KEY UPDATE sorting=VALUES(sorting)';
        $sth=$dbh->prepare($sql);
        $success=$sth->execute();
        if($success!==false) return 1;
        else return false;
    }
    
    function deleteCustomer($id){ 
        if(sizeof($this->getCustomer($id))>0){
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
    
    
    protected function getCustomer($id){
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, name, etype, address, comment, sorting, crdate FROM ' . $table . ' WHERE id=:id ';
        $sql .='LIMIT 0,1';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)$id));
        $rec=$sth->fetch(PDO::FETCH_OBJ);
        return $rec;
    }
    
    
    protected function getAllCustomers($islimit=false){
        $islimit=$islimit===false?'':' LIMIT 0, ' . $islimit;
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, name, etype, address, comment, sorting FROM ' . $table . ' WHERE id>:id ';
        $sql .='ORDER BY sorting ASC';
        $sql .=$islimit;
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)0));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    function addEditCustomer(){
        $json=array();
        $id=$_POST['aEOE_id']>0?$this->updateCustomer():$this->insertCustomer();
        if($id!==false){
            require_once 'Settings.php';
            $setting=new Settings();
            require_once 'Images.php';
            $image=new Images();
            $json['success']=1;
            $json['editid']=$_POST['aEOE_id'];
            $rec=$this->getCustomer($id);
            $key=0;
            $json['content']=$this->wrapSingleRowCustomer($rec, $key, $setting, $image, true);
        }else{
            $json['success']=0;
            $json['message']='Error - ' . __FUNCTION__;
        }
        return $json;
    }
    
    protected function insertCustomer($table=''){
        require_once 'Settings.php';
        $setting=new Settings();
        $table=$table==''?PRETABLE . $this->table:$table;
        $rec=array();
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $a=array();
        foreach ($_POST as $key => $val) if(preg_match('/^aEOE_/', $key)==1 && $key!='aEOE_id') $a[preg_replace('/^aEOE_/', '', $key)]=$val;  
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
    
    protected function updateCustomer($table=''){
        require_once 'Settings.php';
        $setting=new Settings();
        $table=$table==''?PRETABLE . $this->table:$table;
        $rec=array();
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $a=array();
        foreach ($_POST as $key => $val) if(preg_match('/^aEOE_/', $key)==1 && $key!='aEOE_id') $a[preg_replace('/^aEOE_/', '', $key)]=$val;
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
    
    function wrapAllCustomers($section){
        $content='';
        require_once 'Settings.php';
        $setting=new Settings();
        require_once 'Images.php';
        $image=new Images();
        if(isset($_GET['exportcsv'])) return $this->exportAllCustomer();
        $rec=$this->getAllCustomers();
        $listCustomers='';
        if(sizeof($rec)>0) foreach ($rec as $key => $val) $listCustomers .=$this->wrapSingleRowCustomer($val, $key, $setting, $image);                
        $content='';
        ob_start(); // start output buffer
        include (PATH_TO_HTML . 'tableCustomer.htm');
        $content .= ob_get_contents(); // get contents of buffer
        ob_end_clean();
        return $content;
    }
    
    function wrapSingleRowCustomer($rec, $key, $setting, $image, $isAjax=false){            
            $class=$key%2==0?'odd':'even'; 
            $sorting=0;
            $content='';
            $img='';
            $pre=$isAjax===true?'../':'';
            
            $content .='<div rel="sortItem" class="tr sortableRow ' . $class . '" data-sorting="' . $sorting . '" data-id="' . $rec->id . '">';
            $content .='<div class="td">' . $rec->name . '</div>';
            $content .='<div class="td td5">' . $this->arrCustomerTypes[$rec->etype] . '</div>';
            $content .='<div class="td td5">' . $rec->address . '</div>';
            $content .='<div class="td td6">
                <div class="right">
                    <div class="pencil left"><a data-id="' . $rec->id . '" href="#" data-action="addedititem" data-context="wrapAddEditCustomer" title="Uredi kupca"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>'
                 . '<div class="delete left"><a class="deleteCourse" data-action="deleteitem" data-context="deleteCustomer" href="#" data-id="' . $rec->id . '" data-title="' . $rec->name . ' (' . $rec->address . ')' . '" title="Obriši kupca"><i class="fa fa-trash" aria-hidden="true"></i></a></div>
                     <div class="noFloat"></div>
                </div>
                <div class="noFloat"></div>
                </div>';
            $content .='</div>';
            return $content;
        }
    
    function wrapAddEditCustomer($id=0, $isAjax=true){
        require_once 'Settings.php';
        $setting=new Settings();
        require_once 'Images.php';
        $image=new Images();
            $json=array();
            $pre=$isAjax===true?'../':'';
            $content=$fileUploader=$img='';
            if($id>0) $rec=$this->getCustomer($id);
            else{
                $rec=new stdClass();
                $rec->name=$rec->address=$rec->comment='';
                $rec->etype=0;
            }
            $etypeSB=$setting->wrapSB($rec->etype, 'etype', $this->arrCustomerTypes);
            ob_start(); // start output buffer
            include ($pre . PATH_TO_HTML . 'addEditCustomer.htm');
            $content .= ob_get_contents(); // get contents of buffer
            ob_end_clean();
            $json['success']=1;
            $json['content']=$content;
            return $json;
           return $content;
       }
}