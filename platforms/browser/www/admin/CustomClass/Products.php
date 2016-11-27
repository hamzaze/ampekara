<?php
require_once 'DBconnect.php';

class Products{
    var $table='products';
    
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
    
    function deleteProduct($id){ 
        if(sizeof($this->getProduct($id))>0){
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
    
    
    protected function getProduct($id){
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, articleid, name, subtitle, price, image, sorting, crdate FROM ' . $table . ' WHERE id=:id ';
        $sql .='LIMIT 0,1';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)$id));
        $rec=$sth->fetch(PDO::FETCH_OBJ);
        return $rec;
    }
    
    
    protected function getAllProducts($islimit=false){
        $islimit=$islimit===false?'':' LIMIT 0, ' . $islimit;
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, articleid, name, subtitle, price, image, sorting FROM ' . $table . ' WHERE id>:id ';
        $sql .='ORDER BY sorting ASC';
        $sql .=$islimit;
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)0));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    function addEditProduct(){
        $json=array();
        $id=$_POST['aEOE_id']>0?$this->updateProduct():$this->insertProduct();
        if($id!==false){
            require_once 'Settings.php';
            $setting=new Settings();
            require_once 'Images.php';
            $image=new Images();
            $json['success']=1;
            $json['editid']=$_POST['aEOE_id'];
            $rec=$this->getProduct($id);
            $key=0;
            $json['content']=$this->wrapSingleRowProduct($rec, $key, $setting, $image, true);
        }else{
            $json['success']=0;
            $json['message']='Error - ' . __FUNCTION__;
        }
        return $json;
    }
    
    protected function insertProduct($table=''){
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
    
    protected function updateProduct($table=''){
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
    
    function wrapAllProducts($section){
        $content='';
        require_once 'Settings.php';
        $setting=new Settings();
        require_once 'Images.php';
        $image=new Images();
        if(isset($_GET['exportcsv'])) return $this->exportAllProduct();
        $rec=$this->getAllProducts();
        $listProducts='';
        if(sizeof($rec)>0) foreach ($rec as $key => $val) $listProducts .=$this->wrapSingleRowProduct($val, $key, $setting, $image);                
        $content='';
        ob_start(); // start output buffer
        include (PATH_TO_HTML . 'tableProduct.htm');
        $content .= ob_get_contents(); // get contents of buffer
        ob_end_clean();
        return $content;
    }
    
    function wrapSingleRowProduct($rec, $key, $setting, $image, $isAjax=false){            
            $class=$key%2==0?'odd':'even'; 
            $sorting=0;
            $content='';
            $img='';
            $subtitle=$rec->subtitle!=''?'<br /><span class="small text-muted">' . $rec->subtitle . '</span>':'';
            $pre=$isAjax===true?'../':'';
            if($rec->image!=''){
                if(is_file($pre . URL_TO_PHOTOS . $rec->image)){
                    $photos=$setting->getThumbnail1(URL_TO_PHOTOS, $rec->image, __CLASS__ . '_thumbnail', $isAjax);                
                    $img=$image->wrapSingleThumb(PATH_TO_TEMP_PICS . $photos, 'mAOLThumb');
                }
            }
            $content .='<div rel="sortItem" class="tr sortableRow ' . $class . '" data-sorting="' . $sorting . '" data-id="' . $rec->id . '">';
            $content .='<div class="td td0">' . $rec->articleid . '</div>';
            $content .='<div class="td ">' . $img . $rec->name . $subtitle . '</div>';
            $content .='<div class="td td4">' . CURRENCYSIGN . ' ' . $rec->price . '</div>';
            $content .='<div class="td td6">
                <div class="right">
                    <div class="pencil left"><a data-id="' . $rec->id . '" href="#" data-action="addedititem" data-context="wrapAddEditProduct" title="Uredi proizvod"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>'
                 . '<div class="delete left"><a class="deleteCourse" data-action="deleteitem" data-context="deleteProduct" href="#" data-id="' . $rec->id . '" data-title="' . $rec->price . '" title="Obriši proizvod"><i class="fa fa-trash" aria-hidden="true"></i></a></div>
                     <div class="noFloat"></div>
                </div>
                <div class="noFloat"></div>
                </div>';
            $content .='</div>';
            return $content;
        }
    
    function wrapAddEditProduct($id=0, $isAjax=true){
        require_once 'Settings.php';
        $setting=new Settings();
        require_once 'Images.php';
        $image=new Images();
            $json=array();
            $pre=$isAjax===true?'../':'';
            $content=$fileUploader=$img='';
            if($id>0) $rec=$this->getProduct($id);
            else{
                $rec=new stdClass();
                $rec->articleid=$rec->name=$rec->subtitle=$rec->image=$rec->price='';
            }
            if($rec->image!=''){
                if(is_file($pre . URL_TO_PHOTOS . $rec->image)){
                    $photos=$setting->getThumbnail1(URL_TO_PHOTOS, $rec->image, __CLASS__ . '_vertical', $isAjax);                
                    $img=$image->wrapSingleThumb(PATH_TO_TEMP_PICS . $photos, 'mAOLThumb');
                }
            }
           $fileUploader .='<div class="fRow fileUploaderContainer">
               <label for="" class="relative">Fotografija</label>
               <div class="overlayMove"></div>
                            <div class="newfileUploader">
                                <div class="qq-upload-button">
                                    <div class="mf-button bg-gray abs"><input id="fileupload1" class="mf-file abs" type="file" name="files[]" />Browse</div>
                                </div>
                            </div>
                            <div class="hidden"><input type="hidden" name="aEOE_image" value="' . $rec->image . '" /></div>
                                <div class="uploadedFilesHere mAOLThumbnail">' . $img . '</div>
                            </div>
                        ';
            ob_start(); // start output buffer
            include ($pre . PATH_TO_HTML . 'addEditProduct.htm');
            $content .= ob_get_contents(); // get contents of buffer
            ob_end_clean();
            $json['success']=1;
            $json['content']=$content;
            return $json;
           return $content;
       }
}