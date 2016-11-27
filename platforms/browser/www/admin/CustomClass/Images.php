<?php
class Images
{
	var $thumbsWidth1=75;
	var $thumbsHeight1=75;
	var $thumbsWidth2=160;
	var $thumbsHeight2=160;
	var $tempFolder='tempdata';
	
	function url_exists($url) {
		if(@file_get_contents($url,0,NULL,0,1)){return 1;}else{ return 0;}
	}
        
        function hueSaturateImage($original, $thumbnail){
            try{
                $image = $original;
                $im = new Imagick();                        
                $im->pingImage($image);
                $im->readImage($image);
                $im->modulateImage(100, 100, 100); 
                $im->writeImage($thumbnail); 
                $im->clear(); 
                $im->destroy(); 
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        
        function create_thumb($new_width, $new_height,$original,$thumbnail, $crop=false) {
		try
		{
                    $orig_new_width=$new_width;
                    $orig_new_height=$new_height;
                       	$image = $original;
			$im = new Imagick();                        
			$im->pingImage($image);
			$im->readImage($image);
                        $im->setImageCompression(imagick::COMPRESSION_JPEG);
                        $im->setImageCompressionQuality(0);
			$dimensions=$im->getImageGeometry();
                        
                        if($orig_new_width>0 && $orig_new_height>0){
                            if($dimensions['width']-$dimensions['height']<0){
                                $new_width=$dimensions['width']-$orig_new_width<=0?$dimensions['width']:$orig_new_width;
                                $new_height=null;
                            }else{
                                $new_width=null;
                                $new_height=$dimensions['height']-$orig_new_height<=0?$dimensions['height']:$orig_new_height;
                            }
                        }elseif($orig_new_width>0 && $orig_new_height==0){
                                $new_width=$dimensions['width']-$orig_new_width<=0?$dimensions['width']:$orig_new_width;
                                $new_height=null;
                        }elseif($orig_new_width==0 && $orig_new_height>0){
                                $new_width=null;
                                $new_height=$dimensions['height']-$orig_new_width<=0?$dimensions['height']:$orig_new_height;
                        }
			
			if($crop!==false){
				$crop=explode(',', $crop);
				$im->cropThumbnailImage($crop[0], $crop[1]);
				//if($crop[0]<$new_width && $crop[1]<$new_height) $im->cropThumbnailImage($crop[0], $crop[1]);
			}else $im->thumbnailImage($new_width, $new_height);
			$im->writeImage($thumbnail);
			$im->destroy();
			//echo 'thumbnail is <a href="' . $thumbnail . '">thumbnail</a>';	
			return true;
		}
		catch(Exception $e)
		{
		      echo $e->getMessage();
		}
	}
	
	function create_thumb_deprecated($new_width, $new_height,$original,$thumbnail, $crop=false) {
		try
		{
		       	$image = $original;                        
			$im = new Imagick();
			$im->pingImage($image);
			$im->readImage($image );
			$dimensions=$im->getImageGeometry();
                        
                        
                        
			if($dimensions['width']<=$new_width){
				$new_width=$dimensions['width'];
			}
			if($dimensions['width']>=$dimensions['height']){
				$new_height=null;
			}else{
				if($new_height>0) $new_width=null;
			}
                        
                        //echo $image . ' : ' . $new_width . 'x' . $new_height . '<br />';
			
			if($crop!==false){
				$crop=explode(',', $crop);
				$im->cropThumbnailImage($crop[0], $crop[1]);
				//if($crop[0]<$new_width && $crop[1]<$new_height) $im->cropThumbnailImage($crop[0], $crop[1]);
			}else $im->thumbnailImage($new_width, $new_height);
			$im->writeImage($thumbnail);
			$im->destroy();
			//echo 'thumbnail is <a href="' . $thumbnail . '">thumbnail</a>';	
			return true;
		}
		catch(Exception $e)
		{
		      echo $e->getMessage();
		}
	}
        
        function create_crop($width, $height, $x, $y, $img, $imgpreview, $path, $newWidth=PHOTOSMALLDIMHEAD_W, $newHeight=PHOTOSMALLDIMHEAD_H){
            $ratio=PHOTOLARGEDIM/PHOTOLARGEDIMHEAD;
            $width*=$ratio;
            $height*=$ratio;
            $x*=$ratio;
            $y*=$ratio;
            
            
            $ext=strtolower(substr($img, strrpos($img, ".")));	
            $imgName=substr($img, 0, strrpos($img, "."));
            $newTempImgName=md5($imgName . $width . $height . $x . $y);
            $thumbnail=$path . '/' . $newTempImgName . $ext;
            $im = new Imagick();
            $im->pingImage($img);
            $im->readImage($img);
            $dimensions=$im->getImageGeometry();
            $im->cropimage($width, $height, $x, $y);
            $im->writeImage($thumbnail);
            $newCropImg=$this->createThumbnail($thumbnail, $newWidth, $newHeight, $path, false, true);
            unset($thumbnail);
            return $newCropImg;
        }
	
        function createHueThumbnail($img, $path_to_tempPics, $override=false){
            $ext=strtolower(substr($img, strrpos($img, ".")));	
            $imgName=substr($img, 0, strrpos($img, "."));	
            //$newTempImgName=substr(md5($imgName . $topMediaW . $topMediaH),0,$len=-10);
            $newTempImgName=md5($imgName . 'hue');
            if($this->url_exists($path_to_tempPics . $newTempImgName . $ext)==0)
            {
                if($this->url_exists($img)==0) return 'blank.gif';
                $this->hueSaturateImage($img, $path_to_tempPics . $newTempImgName . $ext);
            }
            if($this->url_exists($path_to_tempPics . $newTempImgName . $ext)==1)
		{
			if($override===true){
                            if($this->url_exists($img)==0) return 'blank.gif';
                            //rename($img, $path_to_tempPics . $newTempImgName . $ext);
                            //return $newTempImgName . $ext;
                            $this->hueSaturateImage($img, $path_to_tempPics . $newTempImgName . $ext);
                        }
			//return $path_to_tempPics . '/' . $newTempImgName . $ext;
			return $newTempImgName . $ext;
		}
        }
	
	function createThumbnail($img, $topMediaW, $topMediaH, $path_to_tempPics='', $crop=false, $override=false){            
		$ext=strtolower(substr($img, strrpos($img, ".")));	
		$imgName=substr($img, 0, strrpos($img, "."));	
		//$newTempImgName=substr(md5($imgName . $topMediaW . $topMediaH),0,$len=-10);
		$newTempImgName=$crop===false?md5($imgName . $topMediaW . $topMediaH):md5($imgName . $topMediaW . $topMediaH . $crop);
		//$newTempImgName=$imgName . $topMediaW . 'x' . $topMediaH;
		//echo $img . ' :: ' . $path_to_tempPics . $newTempImgName . $ext . '<br>'; return;
		if($this->url_exists($path_to_tempPics . $newTempImgName . $ext)==0)
		{                    
                    if($this->url_exists($img)==0) return 'blank.gif';
                    //rename($img, $path_to_tempPics . $newTempImgName . $ext);
                    //return basename($img);
                    //return $newTempImgName . $ext;                    
			 $this->create_thumb($topMediaW, $topMediaH, $img, $path_to_tempPics . $newTempImgName . $ext, $crop);
		}
		if($this->url_exists($path_to_tempPics . $newTempImgName . $ext)==1)
		{
			if($override===true){
                            
                            
                            if($this->url_exists($img)==0) return 'blank.gif';
                            //rename($img, $path_to_tempPics . $newTempImgName . $ext);
                            //return $newTempImgName . $ext;
                            $this->create_thumb($topMediaW, $topMediaH, $img, $path_to_tempPics . $newTempImgName . $ext, $crop);
                        }
			//return $path_to_tempPics . '/' . $newTempImgName . $ext;
			return $newTempImgName . $ext;
		}
		
	}
	
	function wrapSingleThumb($pathTothumb, $className='mAOLThumb', $extra='', $url='', $rel=0, $photos=array(), $table='', $field='image'){
            $addExtra='';
            switch($extra){
            default:
                $content='<div class=\'' . $className . '\' rel=\'' . $rel . '\'><span></span><img class=\'' . $className . '\' src=\'' . $pathTothumb . '\' /></div>';  
                
                break;
            case 'slideshow':
                $content='<img src=\'' . $pathTothumb . '\' />';
            break;
            case 'bgimage':
                $content='<div class=\'' . $className . '\' rel=\'' . $rel . '\' style=\'background-image: url(' . $pathTothumb . ')\'></div>';
            break;
            case 'bgimagelazy':
                $content='<div class=\'' . $className . '\' rel=\'' . $rel . '\' data-src=\'' . $pathTothumb . '\' data-src1=\'' . $url . '\' data-caption=\'' . $field . '\'>' . AJAXLOADER . '</div>';
            break;
            case 'lazy':
                $content='http://e-solucije.com/am/' . $pathTothumb;
            break;
           case 'edit':
                $content='<div class="' . $className . '">';
                if($table=='galleries'){
                    $content .='<div class="controls abs">';
                    $content .='<a data-id="' . $rel . '" href="#" data-action="addedititem" data-context="wrapAddEditGallery"><img src="images/iconPen.png"></a>';
                }
                $content .='<a class="deletePhoto mf-button abs" href="#" data-photos="' . implode('|', $photos) . '" data-id="' . $rel . '" data-table="' . $table . '" data-field="' . $field . '">REMOVE</a>';
                $content .=$addExtra;
                if($table=='galleries'){
                    $content .='<div class="noFloat"></div></div>';
                }
                $content .='<span></span><img class="' . $className . '" src="' . $pathTothumb . '" /></div>';
		return $content;    
                break;
             case 1:
                $iconimg=$photos>2?'<img src="' . PATH_TO_IMAGES . 'deliveryRest.png" />':'<img src="' . PATH_TO_IMAGES . 'delivery' . $photos . '.png" />';
                $content='<div class=\'' . $className . '\' rel=\'' . $rel . '\'><span class="abs icon">' . $iconimg . '</span><span></span><a href="' . $url . '" target="_blank"><img class=\'' . $className . '\' src=\'' . DOCUMENT_ROOT . '/' . $pathTothumb . '\' /><h3 class="relative centered">' . nl2br($rel) . '</h3></a></div>';  
             break;
            }
            return $content;
	}
        
        function deletePhoto($photo, $table, $id){
            if(file_exists($photo)){
                if($id>0){
                    switch($table){
                        case 'sponsors':
                            require_once 'Sponsors.php';
                            $sponsor=new Sponsors();
                            $sponsor->deleteSponsorPhoto($id);
                        break;
                        case 'stories':
                            require_once 'Stories.php';
                            $story=new Stories();
                            $story->deleteStoriesPhoto($id);
                        break;
                        case 'slideshows':
                            require_once 'Slideshows.php';
                            $slideshow=new Slideshows();
                            return $slideshow->deleteSlideshowPhoto($id);
                        break;
                        case 'galleries':
                            require_once 'Galleries.php';
                            $gallery=new Galleries();
                            return $gallery->deleteGalleryPhoto($id);
                        break;
                    }
                }
                return unlink($photo);
            }else return false;            
        }
        
        function deletePhotoPhotosOnly($photos, $isAjax=true){
            $photos=explode('|', $photos);
            require_once 'Settings.php';
            $setting=new Settings();
            return $setting->removePhotosFromServer($photos);
        }
}
?>