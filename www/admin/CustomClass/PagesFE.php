<?php
require_once('Pages.php');

class PagesFE extends Pages{
    
    function getAllActiveMenuItemsP(){
        return $this->getAllActiveMenuItems();
    }
   
    function prepareCurTemplateClassFE(){
        return $this->prepareCurTemplateClass();
    }
    
    //Wrap menu at the bottom of the page, footer links
    function wrapBottomMenuFE(){
        $content='';
        $rec=$this->getAllPagesForBottomMenu();
        $content .='<div class="menu"><ul class="relative">';
        if($rec!==false && sizeof($rec)>0){
            foreach($rec as $val) $content .=$this->wrapSingleMenuItem1($val);
        }
        $content .='<div class="noFloat"></div></ul></div>';
        return $content;
    }
    
    //Wrap menu at the top of the page, main links
    function wrapMainMenuFE($section){
        $rec=$this->getAllPagesForFEMenu();
        $content='';
        $content .='<div class="visible-xs">
            <button href="#" class="navbar-toggle collapsed" data-target="#menu" data-toggle="collapse" class="relative" type="button"><i class="fa fa-bars" aria-hidden="true"></i></button>
        </div>';
        $content .='<nav id="menu" class="menu navbar-collapse collapse"><ul class="relative">';
        if($rec!==false && sizeof($rec)>0){
            foreach($rec as $val) $content .=$this->wrapSingleMenuItem($val);
        }
        $content .='<div class="noFloat"></div></ul></nav>';
        return $content;
    }
    
    function wrapMainSubmenuMenuFE($pid){
        $rec=$this->getAllPagesForFEMenu($pid);
        $content='';
        $content .='<ul class="dropdown-menu" role="menu">';
        if($rec!==false && sizeof($rec)>0){
            foreach($rec as $val) $content .=$this->wrapSingleMenuItem($val);
        }               
        $content .='</ul>';
        return $content;
    }
    
    function wrapSingleMenuItem1($rec){
        $content='';
        $link=$rec->realpath!=''?$rec->realpath:'?section=' . $rec->id;
        $content .='<li class="relative"><a href="' . $link . '" class="relative">' . $rec->name . '</a></li>';
        return $content;
    }
    
    function wrapSingleMenuItem($rec){
        $rec1=$this->getAllPagesForFEMenu($rec->id);
        $link=$rec->realpath!=''?$rec->realpath:'?section=' . $rec->id;
        if($rec->id==9){
            require_once 'HTMLContent.php';
            $htmlcontent=new HTMLContent();
            require_once 'Settings.php';
            $setting=new Settings();
            $recR=$setting->prepareBasicValues($htmlcontent->getBasicSettings($rec->id));
            $isAjax=false;
            $pre=$isAjax===true?'../':'';
            if($recR->media!=''){
                    if(is_file($pre . URL_TO_PHOTOS . $recR->media)){
                        $link='<a class="relative" target="_blank" href="' . URL_TO_PHOTOS . '/' . $recR->media . '">';
                    }
                }
        }
        $class=$rec->pid==0?' left':'';
        $content='';
        $content .='<li class="relative' . $class . '">';
        if($rec1==false || sizeof($rec1)<1){
            if($rec->id==9){
                $content .=$link;
            }else{
                $content .='<a class="relative" href="' . DOCUMENT_ROOT . '/' . $link . '">';
            }
        }else{
            $content .='<span class="akalink relative dropdown-toggle">';
        }
        $content .=$rec->navname;
        if($rec1==false || sizeof($rec1)<1){
            $content .='</a>';
        }else{
            $content .=' <span class="caret"></span></span>';
            $content .=$this->wrapMainSubmenuMenuFE($rec->id);
        }
        $content .='</li>';
        return $content;
    }
    
    function wrapMainFooter(){
        require_once 'FooterFE.php';
        $content=new FooterFE();
        return $content->wrapMainFooter();
    }
    
    function wrapMainMetaContent(){
        require_once 'Settings.php';
        $setting=new Settings();
        $title='IPVEDUCATE - ';
        $description='';
        $image_src=$setting->getCurrPathDir() . '/' . PATH_TO_IMAGES . 'logo.jpg';
        $content='';
        $content .='<meta name="og:title" content="' . $title . '" />' . "\n";
        $content .='<meta name="og:description" content="' . $description . '" />' . "\n";
        $content .='<meta property="og:image" content="' . $image_src . '"/>' . "\n";
        $content .='<link rel="' . $image_src . '" href="thumbnail_image" />' . "\n";
        return $content;
    }
    
    function includeCSSFileAfter(){
        $_GET=$this->get;
        
        $section=isset($_GET['section'])?$_GET['section']:1;
        $content='';
        switch($section){
            default:
                
            case 2:
                require_once 'Events.php';
                $event=new Events();
                if(isset($_GET['id']) && $_GET['id']>0){
                    $content .=$event->prepareCurTemplateStyleAfter($section, $_GET['id']);
                }
            break;
            case 8:
                if(isset($_SESSION['deviceViewAlreadyDetected']) && $_SESSION['deviceViewAlreadyDetected'][0]==2){
                    
                }else{
                $content .='<link href="' . DOCUMENT_ROOT . '/css/canada/css/reset.css" rel="stylesheet" type="text/css" />
<link href="' . DOCUMENT_ROOT . '/css/canada/css/fonts.css" rel="stylesheet" type="text/css" />
<link href="' . DOCUMENT_ROOT . '/css/canada/css/style.css" rel="stylesheet" type="text/css" />
<link href="' . DOCUMENT_ROOT . '/css/canada/css/map.css" rel="stylesheet" type="text/css" />';
                }
            break;
        }
        return $content;
    }
    
    function includeCSSFile(){
        
        $_GET=$this->get;
        
        $section=isset($_GET['section'])?$_GET['section']:1;
        $content='';
        $content .='<link rel="stylesheet" href="' . DOCUMENT_ROOT . '/css/bootstrap.min.css" />';
        switch($section){
            default:
                
            break;
        }
        return $content;
    }
    
    function includejQueryFile(){
        $_GET=$this->get;
        
        $section=isset($_GET['section'])?$_GET['section']:1;
        $content='';
        switch($section){
            default:
            return '';   
            break;
            case 2:
                $content .='"' . DOCUMENT_ROOT . '/js/jquery.countdown.min.js", ';
                $content .='"//cdnjs.cloudflare.com/ajax/libs/lodash.js/0.10.0/lodash.min.js", ';
            break;
            case 8:
                if(isset($_SESSION['deviceViewAlreadyDetected']) && $_SESSION['deviceViewAlreadyDetected'][0]==2){
                    
                }else{
                    $content .='"' . DOCUMENT_ROOT . '/css/canada/js/raphael.min.js", ';
                    $content .='"' . DOCUMENT_ROOT . '/css/canada/js/scale.raphael.js", ';
                    $content .='"' . DOCUMENT_ROOT . '/css/canada/js/paths.js", ';
                    $content .='"' . DOCUMENT_ROOT . '/css/canada/js/init.js", ';
                }
            break;
            case 11:
                $content .='"http://maps.googleapis.com/maps/api/js?key=' . GOOGLE_API_KEY . '", ';
            break;
        }
        
        return $content;
    }
    
    function wrapSiteTitleFE(){
        return $this->wrapSiteTitle();
    }
    
    
    //Wrap main page content
    function wrapMainContentFE(){
        return $this->wrapMainContent();
       
    }
    
    private function wrapHomePageFE(){
        return '';
        require_once 'HTMLContentFE.php';
        $htmlContent=new HTMLContentFE();
        return $htmlContent->wrapHomePageFE();
    }
}
?>
