<?php
require_once('DBconnect.php');

class HTMLContent{
    
    var $table='pages';
    var $tableContent='content';
    
    function deletePhoto($id, $photos, $field){
        $photos=explode('|', $photos);
        require_once 'Settings.php';
        $setting=new Settings();
        $setting->removePhotosFromServer($photos);
        if(sizeof($this->getBasicSettings($id))>0){
            $dbh=new DBconnect();
            $dbh=$dbh->sql;
            $sql='UPDATE ' . PRETABLE . $this->tableContent . ' SET name="" WHERE type=:id AND flag=:flag LIMIT 1';
            $sth=$dbh->prepare($sql);
            $success=$sth->execute(array(':id'=>(int)$id, ':flag'=>$field));
            $success=$success==1?true:false;
            return $success;
        }else return false;
    }
    
    function getPage($id){
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, pid, name, inmenu, realpath, online, media FROM ' . $table . ' WHERE id=:id';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)$id));
        $rec=$sth->fetch(PDO::FETCH_OBJ);
        return $rec;
    }
    
    function getBasicSettings($id){
        $rec=array();
        $table=PRETABLE . $this->tableContent;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, type, flag, name FROM ' . $table . ' WHERE type=:id';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)$id));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    function addEditBasicContent(){
        $json=array();
        $id=$_POST['aEOE_type']>0?$this->updateBasicContent():$this->insertBasicContent();
        if($id!==false){
            $json['success']=1;
            $json['editthpe']=$_POST['aEOE_type'];
            $json['content']='<h5>Your change has been saved successfully</h5>';
        }else{
            $json['success']=0;
            $json['message']='Error - ' . __FUNCTION__;
        }
        return $json;
    }
    
    function markPageOnline($id, $inmenu){
        $json=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='UPDATE ' . $table . ' SET inmenu=:inmenu WHERE id=:id LIMIT 1';
        $sth=$dbh->prepare($sql);
        $success=$sth->execute(array(':inmenu'=>(int)$inmenu, ':id'=>(int)$id));
        if($success!==false) $json['success']=1;
        else{
            $json['success']=0;
            $json['message']='Error on page update';
        }
        return $json;
    }
    
    private function updateBasicContent(){
        $table=PRETABLE . $this->tableContent;
        $rec=array();
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $a=array();
        foreach ($_POST as $key => $val){
            if(preg_match('/^aEOE_/', $key)==1 && $key!='aEOE_type'){
                $flag=preg_replace('/^aEOE_/', '', $key);
                $a[]=array($val, $_POST['aEOE_type'], $flag);
            }
        }
        $sql='UPDATE ' . $table . ' SET name=? ';
        $sql .='WHERE type=? AND flag=?';
        $sth=$dbh->prepare($sql);
        $counter=0;
        foreach($a as $b){
            $success=$sth->execute($b);
            if($success!==false){
                $counter++;
            }
        }
        if($counter-count($a)>=0){
            return $_POST['aEOE_type'];
        }
        else return false;
    }
    
    function wrapBasicEditForms($section, $isAjax=false){
        require_once 'Settings.php';
        $setting=new Settings();
        require_once 'Images.php';
        $image=new Images();
        $isTinyMCEClass=' tinymce';
        $rec=$setting->prepareBasicValues($this->getBasicSettings($section));
        $rec1=$this->getPage($section);
        if($rec1!==false && sizeof($rec1)>0){
            $siteTitle=$rec1->name;
            if($rec1->pid>0){
                $rec2=$this->getPage($rec1->pid);
                if($rec2!==false && sizeof($rec2)>0){
                    $siteTitle='<h1 class="redalert">' . $rec2->name . '</h1><h2>' . $siteTitle . '</h2>';
                }
            }else{
                $siteTitle='<h1 class="redalert">' . $siteTitle . '</h1>';
            }
        }
        $content=$form=$img=$img2=$fileUploader=$fileUploader1='';
        $pre=$isAjax===true?'../':'';
        ob_start(); // start output buffer
        switch($section){
            default:
                if($section==6){
                    for($i=1; $i<=6; $i++){
                        $form .='<div class="fRow"><label for="" class="relative">Details ' . $i . '</label><input type="text" class="mf-input" name="aEOE_details' . $i . '" value="' . $rec->{'details' . $i} . '" /></div>';
                    }
                }
            include ($pre . PATH_TO_HTML . 'wrapEditBasicContent.htm');
            break;
            
        }
        $content .= ob_get_contents(); // get contents of buffer
        ob_end_clean();
        return $content;
    }
}