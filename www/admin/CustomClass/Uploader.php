<?php
require_once('DBconnect.php');
//require_once('Mail.php');
//require_once('Mail/mime.php');
/*
 * jQuery File Upload Plugin PHP Example 5.7
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

error_reporting(E_ALL | E_STRICT);

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {    
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);
        
        if ($realSize != $this->getSize()){            
            return false;
        }
        
        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);
        
        return true;
    }
    function getName() {
        return $_GET['qqfile'];
    }
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            throw new Exception('Getting content length is not supported.');
        }      
    }   
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {  
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
            return false;
        }
        return true;
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}

class qqFileUploader {
    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;
    var $pathToUpload;
    var $mediaType;
    var $urlToUpload;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760, $mediaType='photo'){        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
        $this->mediaType=$mediaType;
        
        $this->checkServerSettings(); 
        
        $content_disposition_header=$_SERVER['HTTP_CONTENT_DISPOSITION'];
        
        $file_name = $content_disposition_header ?
            rawurldecode(preg_replace(
                '/(^[^"]+")|("$)/',
                '',
                $content_disposition_header
            )) : null;
        
        $_GET['qqfile']=$file_name;
        

        if (isset($_GET['qqfile'])) {
            $this->file = new qqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new qqUploadedFileForm();
        } else {
            $this->file = false; 
        }
    }
    
   
    
    private function checkServerSettings(){        
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        
        
        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';             
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");    
        }        
    }
    
    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    }
    
    function renameOddFileNames($string){
        $string=preg_replace('/\s+/', '_', $string);
        $string=preg_replace('/[^a-zA-Z0-9_\-\']/', '', $string);
        if(isset($_GET['infunction'])){
            switch($_GET['infunction']){
                case 'dietarymenu':
                    $string='KD_Dietary_Menu';
                break;
            }
        }
        return $string;
    }
    
    

    
    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE){
        if (!is_writable($uploadDirectory)){
            return array('error' => "Server error. Upload directory isn't writable. " . $uploadDirectory);
        }
        if (!$this->file){
            return array('error' => 'No files were uploaded.');
        }
        
        $size = $this->file->getSize();
        
        if ($size == 0) {
            return array('error' => 'File is empty');
        }
        
        if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }
        
        $pathinfo = pathinfo($this->file->getName());
        $filename = $pathinfo['filename'];
        $filename=$this->renameOddFileNames($filename);
        //$filename = md5(uniqid());
        $ext = $pathinfo['extension'];

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }
        
        
        
        
        if(!$replaceOldFile){
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }
        
        
        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){
        	$orig=$uploadDirectory . $filename . '.' . $ext;
        	switch ($this->mediaType){
        		case 'photo':
        		require_once 'Settings.php';
                        $setting=new Settings();
                        require_once 'Images.php';
                        $image=new Images();
                        $id=isset($_GET['ID'])?$_GET['ID']:0;
                        $table=isset($_GET['table'])?$_GET['table']:'';
                        if(isset($_GET['infunction'])){
                            switch($_GET['infunction']){
                            default:
                            $photos=$setting->getThumbnail1($uploadDirectory, $filename . '.' . $ext, __CLASS__ . '_vertical');
                            $large=$photos;
                            $photos1=$setting->getThumbnail1($uploadDirectory, $filename . '.' . $ext, __CLASS__ . '_vertical1');
                            $thumbnail=$image->wrapSingleThumb(URL_TO_TEMP_PICS . $photos1, 'mAOLThumb', 'edit', '', $id, array($uploadDirectory . $filename . '.' . $ext, URL_TO_TEMP_PICS . $photos1), $table);  
                            break;
                            case 'worddoc':
                            case 'powerpoint':    
                             
                            require_once('Settings.php');
                            $setting=new Settings();
                            //if(file_exists($orig)) unlink($orig);                       
                            return array('success'=>true, 'filename'=>$filename . '.' . $ext, 'thumbnail'=>'<a class="relative" target="_blank" href="' . URL_TO_PHOTOS . '/' . $filename . '.' . $ext . '">' . $filename . '.' . $ext . '</a>');
                            break;
                            case 'importcsv':
                            require_once 'Donations.php';
                            $donation=new Donations();
                            return array('success'=>$donation->importCSV($filename . '.' . $ext, $_GET['ID']), 'filename'=>$filename . '.' . $ext);
                            break;
                            case 'brochureurl':
                                if(preg_match('/pdf|PDF|doc|DOC|docx|DOCX/', $ext)==1){
                                    $thumbnail='<a class="relative" target="_blank" href="' . URL_TO_PHOTOS . $filename . '.' . $ext . '">' . $filename . '.' . $ext . '</a>';
                                }elseif(preg_match('/jpe?g|JPE?G|gif|png|PNG/', $ext)==1){
                                    $photos=$setting->getThumbnail1($uploadDirectory, $filename . '.' . $ext, __CLASS__ . '_vertical');
                                    $large=$photos;    
                                    $thumbnail=$image->wrapSingleThumb(PATH_TO_TEMP_PICS . $photos, 'mAOLThumb', 'edit', '', $id, array($uploadDirectory . $filename . '.' . $ext, PATH_TO_TEMP_PICS . $photos), $table);
                                }
                                return array('success'=>true, 'filename'=>$filename . '.' . $ext, 'thumbnail'=>$thumbnail);
                            break;
                            case 'gallery':
                            $photos=$setting->getThumbnail1($uploadDirectory, $filename . '.' . $ext, __CLASS__ . '_gallery');
                            $photos1=$setting->getThumbnail1($uploadDirectory, $filename . '.' . $ext, __CLASS__ . '_gallery1');
                            $large=$photos;  
                            $small=$photos1; 
                            return array('success'=>true, 'filename'=>$large, 'filename1'=>$small);
                            break;
                            }
                        }
                                              
	        	//if(file_exists($orig)) unlink($orig);                       
	        	return array('success'=>true, 'filename'=>$filename . '.' . $ext, 'l'=>$large, 'thumbnail'=>$thumbnail);	        		
        		break;
                    
                        
                        
                    
                        case 'documents':
        		require_once('Settings.php');
                        $setting=new Settings();
	        	//if(file_exists($orig)) unlink($orig);                       
	        	return array('success'=>true, 'filename'=>$filename . '.' . $ext, 'thumbnail'=>$orig);	
        		break;
        	}        	
        	
        
        } else {
            return array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
        
    }    
}

class Uploader{
    
    var $year=2015;

	function upload($sizeLimit=MAXIMAGESIZE){
            require_once 'UsersFE.php';
            $user=new UsersFE();
            require_once 'Settings.php';
            $setting=new Settings();
            if($user->checkIsUserLogged()!==true){
                return array('success'=>false);
            }else{
                $sessID=$_SESSION['whatUserOnDetails']->id;
                if(isset($_SESSION['tx_finalShoppingCart'][$sessID])){
                    if(sizeof($_SESSION['tx_finalShoppingCart'][$sessID])>0){
                        $dir=$setting->getNextUserDirectory($sessID, date('Y-m-d'));
                        
                        $id=$_GET['id'];
                        $pf=PATH_TO_PRIVATE . $_SESSION['whatUserOnDetails']->id;
                        $pf .='/' . $dir;
                        if(!is_dir($pf)){ mkdir($pf);}
                        if(is_dir($pf)){
                            $pf .='/' . $id;
                            if(!is_dir($pf)){ mkdir($pf);}
                            if(is_dir($pf)){
                                $allowedExtensions = array();
                                $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
                                $uploader->pathToUpload=$pf . '/';
                                $uploader->mediaType='documents';
                                $result = $uploader->handleUpload($uploader->pathToUpload, true);
                                // to pass data through iframe you will need to encode all html tags
                                return $result;
                            }
                        }
                    }
                }
            }
	}
        
        function upload1($sizeLimit=MAXIMAGESIZE){
		$pF=$_SERVER['DOCUMENT_ROOT'] . PATH_TO_PHOTOS;
                if(isset($_GET['infunction'])){
                    switch($_GET['infunction']){
                        case 'eventadditional':
                            $pF=$_SERVER['SPT_DOCROOT'] . '/' . PATH_TO_DOCUMENTS;
                        break;
                        case 'gallery':
                            $pF=$_SERVER['SPT_DOCROOT'] . '/' . PATH_TO_GALLERIES . $_GET['year'] . '/';
                            if(!is_dir($pF)){ mkdir($pF, 0755);}
                            $pFThumbnails=$_SERVER['SPT_DOCROOT'] . '/' . PATH_TO_GALLERIES . $_GET['year'] . '/thumbnails/';
                            if(!is_dir($pFThumbnails)){ mkdir($pFThumbnails, 0755);}
                        break;
                    }
                }
                $allowedExtensions = array();
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$uploader->pathToUpload=$pF;
                $uploader->mediaType='photo';
		$result = $uploader->handleUpload($uploader->pathToUpload, true);
		// to pass data through iframe you will need to encode all html tags
		return $result;
	}
        
        function upload2($sizeLimit=MAXIMAGESIZE){
		$pF=$_SERVER['DOCUMENT_ROOT'] . DOCUMENT_ROOT . '/' . PATH_TO_CSV;
                $allowedExtensions = array();
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$uploader->pathToUpload=$pF;
                $uploader->mediaType='csvimport';
		$result = $uploader->handleUpload($uploader->pathToUpload, true);
		// to pass data through iframe you will need to encode all html tags
		return $result;
	}
        
        function upload3($sizeLimit=MAXIMAGESIZE){
		$pF=$_SERVER['DOCUMENT_ROOT'] . DOCUMENT_ROOT . '/' . PATH_TO_BANNERS;
                $allowedExtensions = array();
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$uploader->pathToUpload=$pF;
                $uploader->mediaType='portfolioLeft';
		$result = $uploader->handleUpload($uploader->pathToUpload, true);
		// to pass data through iframe you will need to encode all html tags
		return $result;
	}
        
        function upload4($sizeLimit=MAXIMAGESIZE){
		$pF=$_SERVER['DOCUMENT_ROOT'] . DOCUMENT_ROOT . '/' . PATH_TO_GALLERIES;
                $allowedExtensions = array();
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$uploader->pathToUpload=$pF;
                $uploader->mediaType='gallery';
		$result = $uploader->handleUpload($uploader->pathToUpload, true);
		// to pass data through iframe you will need to encode all html tags
		return $result;
	}
        
        function upload5($sizeLimit=MAXIMAGESIZE){
		$pF=$_SERVER['DOCUMENT_ROOT'] . DOCUMENT_ROOT . '/' . PATH_TO_PRODUCTS;                
                $allowedExtensions = array();
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$uploader->pathToUpload=$pF;
                $uploader->mediaType='photo';
		$result = $uploader->handleUpload($uploader->pathToUpload, true);
		// to pass data through iframe you will need to encode all html tags
		return $result;
	}
        
        function upload6($sizeLimit=MAXIMAGESIZE){
		$pF=$_SERVER['DOCUMENT_ROOT'] . DOCUMENT_ROOT . '/' . PATH_TO_PRODUCTS;                
                $allowedExtensions = array();
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$uploader->pathToUpload=$pF;
                $uploader->mediaType='documents';
		$result = $uploader->handleUpload($uploader->pathToUpload, true);
		// to pass data through iframe you will need to encode all html tags
		return $result;
	}
        

}
?>