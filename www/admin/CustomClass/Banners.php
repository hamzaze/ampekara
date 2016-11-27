<?php
require_once 'DBconnect.php';

class Banners{
    var $table='banners';
    
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
    
    
    function deleteBanner($id){ 
        if(sizeof($this->getBanner($id))>0){
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
    
    protected function getBanner($id){
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, image, caption, subcaption, isbutton, buttontext, buttonurl, sorting, crdate, lsdate FROM ' . $table . ' WHERE id=:id ';
        $sql .='LIMIT 0,1';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)$id));
        $rec=$sth->fetch(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getAllBanners($islimit=false){
        $islimit=$islimit===false?'':' LIMIT 0, ' . $islimit;
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, image, caption, subcaption, isbutton, buttontext, buttonurl, sorting FROM ' . $table . ' WHERE id>:id ';
        $sql .='ORDER BY sorting ASC, id DESC';
        $sql .=$islimit;
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)0));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getAllBannersForExport(){
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, image, caption, subcaption, isbutton, buttontext, buttonurl, sorting FROM ' . $table . ' WHERE id>:id ';
        $sql .='ORDER BY sorting ASC';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)0));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    function addEditBanner(){
        $json=array();
        $id=$_POST['aEOE_id']>0?$this->updateBanner():$this->insertBanner();
        if($id!==false){
            require_once 'Settings.php';
           $setting=new Settings();
           require_once 'Images.php';
           $image=new Images();
            $json['success']=1;
            $json['editid']=$_POST['aEOE_id'];
            $rec=$this->getBanner($id);
            $key=0;
            $json['content']=$this->wrapSingleRowBanner($rec, $key, $setting, $image, true);
        }else{
            $json['success']=0;
            $json['message']='Error - ' . __FUNCTION__;
        }
        return $json;
    }
    
    protected function insertBanner($table=''){		
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
    
    protected function updateBanner(){
		$table=PRETABLE . $this->table;
		$rec=array();
		$dbh=new DBconnect();
		$dbh=$dbh->sql;
		$a=array();
		foreach ($_POST as $key => $val) if(preg_match('/^aEOE_/', $key)==1 && $key!='aEOE_id') $a[preg_replace('/^aEOE_/', '', $key)]=$val;
                //$a['comedianurl']=$this->generateComedianUrl($_POST['aEOE_firstname'], $_POST['aEOE_lastname']);
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
    
    function wrapAllBanners($section){
        require_once 'Settings.php';
        $setting=new Settings();
        require_once 'Images.php';
        $image=new Images();
        $content='';
        if(isset($_GET['exportcsv'])) return $this->exportAllBanner();
        $rec=$this->getAllBanners();                
        $backurl='../admin.php?section=' . $section;
        $listBanners='';
        if(sizeof($rec)>0) foreach ($rec as $key => $val) $listBanners .=$this->wrapSingleRowBanner($val, $key, $setting, $image);                
        $content='';
        ob_start(); // start output buffer
        include (PATH_TO_HTML . 'tableBanner.htm');
        $content .= ob_get_contents(); // get contents of buffer
        ob_end_clean();
        return $content;
    }
    
    function wrapSingleRowBanner($rec, $key, $setting, $image, $isAjax=false){            
            $class=$key%2==0?'odd':'even'; 
            $sorting=$rec->sorting>0?$rec->sorting:1000*($key+1);
            $pre=$isAjax===true?'../':'';
            $img='';
            if($rec->image!=''){
                if(is_file($pre . URL_TO_PHOTOS . $rec->image)){
                    $photos=$setting->getThumbnail1(URL_TO_PHOTOS, $rec->image, __CLASS__ . '_thumbnail', $isAjax);                
                    $img=$image->wrapSingleThumb(PATH_TO_TEMP_PICS . $photos, 'mAOLThumb');
                }
            }
            $content='';
            $content .='<div rel="sortItem" class="tr sortableRow ' . $class . '" data-sorting="' . $sorting . '" data-id="' . $rec->id . '">';
            $content .='<div class="td td1">' . $img . '</div>';
            $content .='<div class="td td6">
                <div class="right">
                    <div class="pencil left"><a data-id="' . $rec->id . '" href="#" data-action="addedititem" data-context="wrapAddEditBanner" title="Edit Banner"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>'
                 . '<div class="delete left"><a class="deleteCourse" data-action="deleteitem" data-context="deleteBanner" href="#" data-id="' . $rec->id . '" data-title="' . $img . '" title="Delete banner"><i class="fa fa-trash" aria-hidden="true"></i></a></div>
                     <div class="noFloat"></div>
                </div>
                <div class="noFloat"></div>
                </div>';
            $content .='</div>';
            return $content;
        }
    
    function wrapAddEditBanner($id=0, $isAjax=true){
           require_once 'Settings.php';
           $setting=new Settings();
           require_once 'Images.php';
           $image=new Images();
           $json=array();
           $pre=$isAjax===true?'../':'';
           $content=$fileUploader=$img='';
           $hidden1=$hidden2=' hidden';
           if($id>0) $rec=$this->getBanner($id);
           else{
               $rec=new stdClass();
               $rec->image=$rec->caption=$rec->subcaption=$rec->buttontext=$rec->buttonurl=$rec->image='';
               $rec->isbutton=0;
           } 
           $isbuttonSB=$setting->wrapSB($rec->isbutton, 'isbutton', $setting->arrYesNo);
           $hidden1=$rec->isbutton==1?'':' hidden';
           if($rec->image!=''){
                if(is_file($pre . URL_TO_PHOTOS . $rec->image)){
                    $photos=$setting->getThumbnail1(URL_TO_PHOTOS, $rec->image, __CLASS__ . '_vertical', $isAjax);                
                    $img=$image->wrapSingleThumb(PATH_TO_TEMP_PICS . $photos, 'mAOLThumb');
                }
            }
           $fileUploader .='<div class="fRow fileUploaderContainer">
               <label for="" class="relative">Banner Image</label>
                            <div class="newfileUploader">
                                <div class="qq-upload-button">
                                    <button type="button" class="mf-button abs">Browse</button>
                                    <input id="fileupload1" class="mf-file abs" type="file" name="files[]" multiple />
                                </div>
                            </div>
                            <div class="hidden"><input type="hidden" name="aEOE_image" value="' . $rec->image . '" /></div>
                                <div class="uploadedFilesHere mAOLThumbnail">' . $img . '</div>
                            </div>
                        ';
           ob_start(); // start output buffer
            include ($pre . PATH_TO_HTML . 'addEditBanner.htm');
            $content .= ob_get_contents(); // get contents of buffer
            ob_end_clean();
            $json['success']=1;
            $json['content']=$content;
            return $json;
           return $content;
       }
       
       function exportAllBanner(){
           require_once 'Settings.php';
           $setting=new Settings();
           $json=array();
           $rec=$this->getAllBannersForExport();
           if($rec!==false && sizeof($rec)>0){
               $filename=$setting->prepareRealLink('banners-' . date('Y-m-d-H:i'));
               $pF=$_SERVER['DOCUMENT_ROOT'] . DOCUMENT_ROOT . '/' . PATH_TO_DOWNLOAD_CSV . $filename . '.csv';
               $handle = fopen($pF, 'w+');
               if(!file_exists($pF)){
                   $json['success']=3;
                   $json['message']='File is not exists, or not created . ' . $pF;
               }else{
                   $arrayExtraTitles=array();
                   $arrayTitles=array('Image', 'Caption', 'Sub Caption', 'Button', 'Button Text', 'Button URL');
                   
               fputcsv($handle, $arrayTitles);
               
               foreach($rec as $val){
                   $media=dirname($setting->getCurrPathDir()) . '/' . substr(URL_TO_PHOTOS, 3) . $val->image;
                   $arrValues=array($media, $val->caption, $val->subcaption, $setting->arrYesNo[$val->isbutton], $val->buttontext, $val->buttonurl);
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