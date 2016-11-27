<?php
require_once 'DBconnect.php';

class Galleries{
    var $table='galleries';
    
    var $arrYears=array();
    
    var $galleriesources=array(
        '',
        'Link',
        'File Upload'
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
    
    function deleteGallery($id, $photos){
        $recG=$this->getGallery($id);
        if($recG!==false && sizeof($recG)>0){
            $photoid=$recG->photoid;
            require_once 'Settings.php';
            $setting=new Settings();
            $rec=explode('|', $photos);
            $setting->removePhotosFromServer($rec, true);
            $dbh=new DBconnect();
            $dbh=$dbh->sql;
            $sql='DELETE FROM ' . PRETABLE . $this->table . ' WHERE id=:id LIMIT 1';
            $sth=$dbh->prepare($sql);
            $success=$sth->execute(array(':id'=>(int)$id));
            $success=$success==1?true:false;

            if($success===true){
                if($photoid>0){
                    $dbh=new DBconnect();
                    $dbh=$dbh->sql;
                    $sql='DELETE FROM ' . PRETABLE . 'photos WHERE id=:id LIMIT 1';
                    $sth=$dbh->prepare($sql);
                    $success=$sth->execute(array(':id'=>(int)$recG->photoid));
                }
                return true;
            }
            return $success;
        }else return false;
    }
    
    function getGalleryP($id){
        return $this->getGallery($id);
    }
    
    protected function getGallery($id){
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, photoid, userid, year, title, image, thumbnail, crdate, lsdate FROM ' . $table . ' WHERE id=:id ';
        $sql .='LIMIT 0,1';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)$id));
        $rec=$sth->fetch(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getAllGalleriesByType($etype){
        return $this->getAllGalleries(false, $etype);
    }
    
    protected function getAllGalleries($year, $islimit=false){
        $b=array(':id'=>(int)0, ':year'=>$year, ':image'=>'');
        $islimit=$islimit===false?'':' LIMIT 0, ' . $islimit;
        $addToMySQLQuery=' AND year=:year AND (image!=:image || thumbnail!=:image)';
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, year, title, image, thumbnail, crdate FROM ' . $table . ' WHERE id>:id ' . $addToMySQLQuery;
        $sql .=' ORDER BY crdate DESC';
        $sql .=$islimit;
        $sth=$dbh->prepare($sql);
        $sth->execute($b);
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    function markGalleryOnline($year, $online){
        $json=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='UPDATE ' . $table . ' SET online=:online WHERE year=:year';
        $sth=$dbh->prepare($sql);
        $success=$sth->execute(array(':online'=>(int)$online, ':year'=>(int)$year));
        if($success!==false) $json['success']=1;
        else{
            $json['success']=0;
            $json['message']='Error on gallery update';
        }
        return $json;
    }
    
    protected function getCurrentGalleryYear($year){
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT year, online FROM ' . $table . ' WHERE id>:id AND year=:year GROUP BY year ORDER BY id DESC';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)0, ':year'=>(int)$year));
        $rec=$sth->fetch(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getAllOnlineGalleryYears(){
        $rec=array();
        $current=false;
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT year FROM ' . $table . ' WHERE id>:id AND online=:online GROUP BY year ORDER BY year DESC';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)0, ':online'=>(int)1));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getALLGalleryYears(){
        $rec=array();
        $current=false;
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT year AS id, CONCAT("Gallery - ", year) AS name FROM ' . $table . ' WHERE id>:id GROUP BY year ORDER BY year DESC';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)0));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        if($rec!==false && sizeof($rec)>0){
            foreach($rec as $val){
                if($val->id==date('Y')){
                    $current=true;
                    break;
                }
            }
        }
        if($current===false){
            $b=new stdClass();
            $b->id=date('Y');
            $b->name='Gallery - ' . date('Y');
            array_unshift($rec, $b);
        }
        return $rec;
    }
    
    protected function getAllGalleriesForExport(){
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, question_en, question_fr, answer_en, answer_fr, sorting FROM ' . $table . ' WHERE id>:id ';
        $sql .='ORDER BY sorting ASC';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)0));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    function addEditGallery(){
        require_once 'Settings.php';
        $setting=new Settings();
        require_once 'Images.php';
        $image=new Images();
        $json=array();
        $id=$_POST['aEOE_id']>0?$this->updateGallery():$this->insertGallery();
        if($id!==false){
            $json['success']=1;
            $json['editid']=$_POST['aEOE_id'];
            $rec=$this->getGallery($id);
            $key=0;
            $json['content']=$this->wrapSingleRowGallery($rec, $key, $setting, $image, true);
        }else{
            $json['success']=0;
            $json['message']='Error - ' . __FUNCTION__;
        }
        return $json;
    }
    
    function insertGalleryP(){
        return $this->insertGallery();
    }
    
    function updateGalleryP(){
        return $this->updateGallery();
    }
    
    protected function insertGallery($table=''){		
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
    
    protected function updateGallery(){
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
    
    function wrapAllGalleries($section){
        $content=$form=$fileUploader='';
        require_once 'HTMLContent.php';
        $htmlcontent=new HTMLContent();
        require_once 'Settings.php';
        $setting=new Settings();
        require_once 'Images.php';
        $image=new Images();
        if(isset($_GET['exportcsv'])) return $this->exportAllGallery();
        $year=isset($_GET['year'])?$_GET['year']:date('Y');
        $rec=$this->getAllGalleries($year); 
        $rec1=$htmlcontent->getPage($section);
        $yearSB=$setting->wrapSB1($year, 'year', $this->getALLGalleryYears());
        $rec2=$this->getCurrentGalleryYear($year);
        if($rec2===false || sizeof($rec2)<1){
            $rec2=new stdClass();
            $rec2->online=0;
        }
        $backurl='?section=' . $section;
        $listGalleries='';
        $isonline=$rec1->online==1?' checked="checked"':'';
        $classOnline=$rec1->online==1?' isOnline':'';
        $isGalleryOnline=$rec2->online==1?' checked="checked"':'';
        $classGalleryOnline=$rec2->online==1?' isOnline':'';
        $fileUploader .='<div class="fRow fileUploaderContainer" data-infunction="gallery" data-year="' . $year . '">
               <label for="" class="relative">Upload Gallery</label>
               <div class="qq-drag-and-drop">Drag & drop photos here</div>
                            <div class="newfileUploader">
                                <div class="qq-upload-button">
                                    <button type="button" class="mf-button">Browse</button>
                                    <input id="fileupload1" class="mf-file abs" type="file" name="files[]" multiple />
                                </div>
                            </div>';
        
        $form .=$fileUploader;
        $submitBtn='';
        if(sizeof($rec)>0) foreach ($rec as $key => $val) $listGalleries .=$this->wrapSingleRowGallery($val, $key, $setting, $image);                
        $content='';
        ob_start(); // start output buffer
        include (PATH_TO_HTML . 'tableGallery.htm');
        $content .= ob_get_contents(); // get contents of buffer
        ob_end_clean();
        return $content;
    }
    
    function wrapSingleRowGallery($rec, $key, $setting, $image, $isAjax=false){            
            $class=$key%2==0?'odd':'even'; 
            $content=$img='';
            $pre=$isAjax===true?'../':'';
            if($rec->image!=''){
                $rec->image=URL_TO_GALLERIES . $rec->year . '/' . $rec->image;
                if(is_file($pre . $rec->image)){
                    $photos=$setting->getThumbnail1('', $rec->image, __CLASS__ . '_thumbnail', $isAjax);                
                    $img=$image->wrapSingleThumb(PATH_TO_TEMP_PICS . $photos, 'mAOLThumb', 'edit', '', $rec->id, array($rec->image, $rec->thumbnail), 'galleries');
                }
            } 
            $content .='<div class="item left ' . $class . '" data-id="' . $rec->id . '">';
            $content .=$img;
            $content .='<div class="noFloat"></div></div>';
            return $content;
        }
    
    function wrapAddEditGallery($id=0, $isAjax=true){
        require_once 'Settings.php';
        $setting=new Settings();
        require_once 'Images.php';
        $image=new Images();
        $section=isset($_POST['section'])?$_POST['section']:1;
        $json=array();
        $pre=$isAjax===true?'../':'';
        $content=$fileUploader=$img='';
        if($id>0) $rec=$this->getGallery($id);
        else{
            $rec=new stdClass();
            $rec->title=$rec->image=$rec->thumbnail='';
            $rec->year=date('Y');
        }
        if($rec->image!=''){
            $rec->imageorig=$rec->image;
            $rec->imageorig=URL_TO_GALLERIES . $rec->year . '/' . $rec->image;
            if(is_file($pre . $rec->imageorig)){
                $photos=$setting->getThumbnail1('', $rec->imageorig, __CLASS__ . '_vertical', $isAjax);                
                $img=$image->wrapSingleThumb(PATH_TO_TEMP_PICS . $photos, 'mAOLThumb');
            }
        } 
        $fileUploader .='<div class="fRow fileUploaderContainer" data-infunction="gallery" data-year="' . $rec->year . '" data-id="' . $rec->id . '">
               <label for="" class="relative">Upload Gallery</label>
               <div class="qq-drag-and-drop">Drag & drop photos here</div>
                            <div class="newfileUploader">
                                <div class="qq-upload-button">
                                    <button type="button" class="mf-button">Browse</button>
                                    <input id="fileupload1" class="mf-file abs" type="file" name="files[]" multiple />
                                </div>
                                <div class="hidden"><input type="hidden" name="aEOE_image" value="' . $rec->image . '" /></div>
                            <div class="uploadedFilesHere mAOLThumbnail">' . $img . '</div>
                            </div>';
      
        ob_start(); // start output buffer
        include ($pre . PATH_TO_HTML . 'addEditGallery.htm');
        $content .= ob_get_contents(); // get contents of buffer
        ob_end_clean();
        $json['success']=1;
        $json['content']=$content;
        return $json;
        return $content;
    }
       
       function exportAllGallery(){
           require_once 'Settings.php';
           $setting=new Settings();
           $json=array();
           $rec=$this->getAllGalleriesForExport();
           if($rec!==false && sizeof($rec)>0){
               $filename=$setting->prepareRealLink('galleries-links-' . date('Y-m-d-H:i'));
               $pF=$_SERVER['DOCUMENT_ROOT'] . DOCUMENT_ROOT . '/' . PATH_TO_DOWNLOAD_CSV . $filename . '.csv';
               $handle = fopen($pF, 'w+');
               if(!file_exists($pF)){
                   $json['success']=3;
                   $json['message']='File is not exists, or not created . ' . $pF;
               }else{
                   $arrayExtraTitles=array();
                   $arrayTitles=array('Type', 'Name of Gallery (EN)', 'Name of Gallery (LV)', 'Source type', 'URL Source', 'Uploaded File', 'Image');
                   
               fputcsv($handle, $arrayTitles);
               
               foreach($rec as $val){
                   $media=$val->galleriesource==2?dirname($setting->getCurrPathDir()) . '/' . substr(URL_TO_PHOTOS, 3) . $val->media:' ';
                   $image=$val->image!=''?dirname($setting->getCurrPathDir()) . '/' . substr(URL_TO_PHOTOS, 3) . $val->image:' ';
                   $arrValues=array($this->etypes[$val->etype], $val->name_en, $val->name_lv, $this->galleriesources[$val->galleriesource], $val->url, $media, $image);
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