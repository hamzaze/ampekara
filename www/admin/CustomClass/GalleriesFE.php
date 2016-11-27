<?php
require_once('Galleries.php');

class GalleriesFE extends Galleries{
    
    function wrapGalleriesFE($section){
        require_once 'Settings.php';
        $setting=new Settings();
        require_once 'HTMLContent.php';
        $htmlcontent=new HTMLContent();
        $rec1=$htmlcontent->getPage($section);
        $content='';
            $content .='
                                <div id="wrapGalleriesFE" class="wrapGalleriesFE relative">';
            $content .='<div class="insideContent centered transparent">';
            $content .='<div class="infoComingSoon container">';
            $content .='<div class="col-md-12 paddingTopBottom20">&nbsp;</div>';
            $content .='<div class="">
                <div class="relative">
                    <div class="col-md-12 space15">&nbsp;</div>
                <div class="row">'  . $this->wrapAllGalleriesFE() . '</div>
    </div>
                
            </div>';
            $content .='<div class="col-md-12 paddingTopBottom20">&nbsp;</div>';
            $content .='</div>';
            $content .='</div>';
            $content .='</div>';
        
        return $content;
    }
    
    function wrapGalleryChooser($rec){
        $yearP=date('Y');
        $year=isset($_GET['year'])?$_GET['year']:$yearP;
        $rec1=$this->getAllOnlineGalleryYears();
        $content='';
        if($rec1!==false && sizeof($rec1)>0){
            $content .='<div class="galleryChooser abs right0">';
            $content .='<div class="col-md-12">';
            foreach($rec1 as $val){
                $class=$year==$val->year?' btn-green':' btn-primaryopacity';
                $content .='<div class="left"><a href="' . DOCUMENT_ROOT . '/' . $rec->realpath . '/' . $val->year . '" class="relative btn' . $class . '">' . $val->year . '</a></div>';
            }
            $content .='</div>';
            $content .='<div class="noFloat"></div>';
            $content .='</div>';
        }
        return $content;
    }
    
    function wrapAllGalleriesFE(){
        require_once 'Settings.php';
        $setting=new Settings();
        require_once 'Images.php';
        $image=new Images();
        $content='';
        $yearP=date('Y');
        $year=isset($_GET['year'])?$_GET['year']:$yearP;
        $rec=$this->getAllGalleries($year);
        if($rec!==false && sizeof($rec)>0){
            foreach($rec as $val) $content .=$this->wrapGalleryItemFE($val, $setting, $image);
        }
        return $content;
    }
    
    function wrapGalleryItemFE($rec, $setting, $image, $isAjax=false){
        $content=$img='';
        $pre=$isAjax===true?'../':'';
        if($rec->image!=''){
            $src=URL_TO_GALLERIES . $rec->year . '/' . $rec->image;;
            if(is_file($src)){
                $photos=$setting->getThumbnail1('', $src, __CLASS__ . '_thumbnail', $isAjax);
                $photos1=$setting->getThumbnail1('', $src, __CLASS__ . '_slideshow', $isAjax);
                $img=$image->wrapSingleThumb(DOCUMENT_ROOT . '/' . PATH_TO_TEMP_PICS . $photos['s'], 'mAOLThumb', 'bgimagelazy', DOCUMENT_ROOT . '/' . PATH_TO_TEMP_PICS . $photos1['s'], '', '', '', $rec->title);

                $content .='<li class="relative col-md-3">';
                $content .='<div class="row"><div>';
                $content .='<article class="btn-default btn-primaryopacity text-center" data-id="' . $rec->id . '">';
                $content .=$img;
                $content .='</article>';
                $content .='</div></div>';
                $content .='</li>';
            }
        }
        return $content;
    }
    
    
}