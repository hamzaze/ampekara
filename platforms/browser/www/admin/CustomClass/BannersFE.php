<?php
require_once('Banners.php');

class BannersFE extends Banners{
    
    function wrapSlideShowFE(){
        $rec=$this->getAllBanners(5);
        $content='';
        if($rec!==false && sizeof($rec)>0){
            require_once 'Settings.php';
            $setting=new Settings();
            require_once 'Images.php';
            $image=new Images();
            $content .='<section id="slideshow" class="slideshow">
                            <div id="main-slideshow" class="main-slideshow">';
            foreach($rec as $val) $content .=$this->wrapSingleSlideShowItemFE($val, $setting, $image);
            $content .='</div>
                    </section>';
        }
        return $content;
    }
    
    function wrapSingleSlideShowItemFE($rec, $setting, $image, $isAjax=false){
        $pre=$isAjax===true?'../':'';
        $content='';
        $content .='<div class="item">';
        $img='';
            if($rec->image!=''){
                if(is_file($pre . URL_TO_PHOTOS . $rec->image)){
                    $photos=$setting->getThumbnail1(URL_TO_PHOTOS, $rec->image, __CLASS__ . '_slideshow', $isAjax);                
                    $img=$image->wrapSingleThumb(PATH_TO_TEMP_PICS . $photos, 'mAOLThumb', 'slideshow');
                }
            }
        $content .=$img;
        if($rec->caption!=''){
            $content .='<div class="slide-caption">';
            $content .='<h2 class="slide1anim1">' . $rec->caption . '</h2>';
            if($rec->subcaption!=''){
                $content .='<h3 class="slide1anim2">' . $rec->subcaption . '</h3>';
            }
            if($rec->isbutton==1){
                $content .='<a href="' . $rec->buttonurl . '" class="btn btn-slideshow slide1anim3">' . $rec->buttontext . '</a>';
            }
            $content .='</div>';
        }
        $content .='</div>';
        return $content;
    }
}

