<?php
require_once('HTMLContent.php');

class HTMLContentFE extends HTMLContent{
    
    function getPagePB($id){
        return $this->getPage($id);
    }
    
    function pageContentFE($section){
        require_once 'Settings.php';
        $setting=new Settings();
        $rec=$setting->prepareBasicValues($this->getBasicSettings($section));
        return $rec;
    }
    
    function wrapVideoHome($isAjax=false){
        require_once 'Settings.php';
        $setting=new Settings();
        require_once 'Images.php';
        $image=new Images();
        $pre=$isAjax===true?'../':'';
        $content='';
        $videothumbnail='';
        $rec=$this->pageContentFE(1);
        if($rec!==false && sizeof($rec)>0){
            if($rec->youtubevideo!=''){
                if($rec->videothumbnail!=''){
                    if(is_file($pre . URL_TO_PHOTOS . $rec->videothumbnail)){
                        $photos=$setting->getThumbnail1(URL_TO_PHOTOS, $rec->videothumbnail, __CLASS__ . '_thumbnail', $isAjax);                
                        $videothumbnail=$image->wrapSingleThumb(PATH_TO_TEMP_PICS . $photos, 'mAOLThumb');
                    }
                }
                $rec->youtubevideo=$setting->prepareYouTubeVideoForIframe($rec->youtubevideo);
                $content .='<div class="wrapYouTubeVideo"><a href="#" data-href="' . $rec->youtubevideo . '">' . $videothumbnail . '<div class="videoControls">
                    <div class="circled inline-block bg-primary"><i class="fa fa-play abs top50 left50" aria-hidden="true"></i></div><div class="inline-block text-primary">
                        <p>Click here to watch and learn about the<br />EDUCATE Training Program!</p>
                    </div>
                </div></a></div>';
            }
        }
        return $content;
    }
    
    function wrapHTMLContentFE($section){
        $content='';
        require_once 'Settings.php';
        $setting=new Settings();
        if($section==13){
            require_once 'TrainingUpdatesFE.php';
            $trainingUpdates=new TrainingUpdatesFE();
            $content .=$trainingUpdates->wrapTraingingUpdatesFE($section);
            return $content;
        }elseif(true===in_array($section, array(21,22,23))){
            if(isset($_SESSION['deviceViewAlreadyDetected'])){
                if($_SESSION['deviceViewAlreadyDetected'][0]==1 || $_SESSION['deviceViewAlreadyDetected'][0]==2){
                    $content .='<div class="alert alert-warning">To access this module please visit this page from a PC device.</div>';
                    return $content;
                }
            }
        }
        $rec=$this->pageContentFE($section);
        if($rec!==false && sizeof($rec)>0){
            $content .=$rec->content;
            if($section==17){
                $rec->youtubevideo=$setting->prepareYouTubeVideoForIframe($rec->youtubevideo);
                $content .='<div id="mainCVideo"><div id="videoContainer"><iframe src="' . $rec->youtubevideo . '" frameborder="0" width="640" height="360" allowfullscreen="true"></iframe></div></div>';
            }elseif($section==19){
                if($rec->media!==''&& preg_match('/\.(pptx?)$/i', $rec->media)==1){
                    $content .='<div class="text-center paddTopBottom40"><a href="' . URL_TO_PHOTOS . $rec->media . '" target="_blank" class="btn mf-button greenish">DOWNLOAD TRAINING MODULE (.ppt)</a></div>';
                }
            }
        }
        return $content;
    }
}
