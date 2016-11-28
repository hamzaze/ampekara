<?php
require_once('Products.php');

class ProductsFE extends Products{
    
    function getJSONProducts($isAjax=true){
        require_once 'Settings.php';
        $setting=new Settings();
        require_once 'Images.php';
        $image=new Images();
        require_once 'ProductionsFE.php';
        $production=new ProductionsFE();
        $rec=$this->getAllProducts();
        $rec1=false;
        if($rec!==false && sizeof($rec)>0){
            if(isset($_SESSION['whatUserOnDetails']) && $setting->checkIsUserLogedAndToken($_SESSION['whatUserOnDetails']->id)===true){
                if($_SESSION['whatUserOnDetails']->roles==2){
                    $todayEdate=isset($_SESSION['currentProductionDate'])?$_SESSION['currentProductionDate']:date('Y-m-d H:i:s');
                }else{
                    $todayEdate=date('Y-m-d H:i:s');
                }
                $rec2=$production->getAllCurrentForDateP($todayEdate);
            }
            
            
            foreach(@$rec as $key => $val){
                unset($val->price);
                $img='';
                $pre=$isAjax===true?'../':'';
                if($val->image==''){
                    $val->image=NOPRODUCT_PHOTO;
                }
                if($val->image!=''){
                    if(is_file($pre . URL_TO_PHOTOS . $val->image)){
                        $photos=$setting->getThumbnail1(URL_TO_PHOTOS, $val->image, __CLASS__ . '_thumbnail', $isAjax);                
                        $img=$image->wrapSingleThumb(PATH_TO_TEMP_PICS . $photos, 'mAOLThumb lazy', 'lazy');
                    }
                }
                $val->productimg=$img;
                if($rec2!==false && sizeof($rec2)>0){
                    if(true===array_key_exists($val->id, $rec2)){
                        $val->qtycurrent=$rec2[$val->id]['count'];
                        $val->qty=$rec2[$val->id]['countproduced'];
                    }else{
                        $val->qtycurrent=0;
                        $val->qty=0;
                    }
                }
                $val->name .=' - ' . rand(0,1000);
            }
        }
        return $rec;
    }
    
    function prepareAllProductsForArray(){
        $rec1=array();
        $rec=$this->getAllProducts();
        if($rec!==false && sizeof($rec)>0){
            foreach($rec as $key => $val){
                $rec1[$val->id]=$val;
            }
        }
        return $rec1;
    }
}