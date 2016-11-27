<?php
require_once('Productions.php');

class ProductionsFE extends Productions{
    
    function getAllProductedForDateP($edate){
        $rec1=array();
        $rec=$this->getAllCurrentForDate($edate);
        if($rec!==false && sizeof($rec)>0){
            foreach($rec as $val){
                $rec1[$val->productid]=array('countproduced'=>$val->countproduced, 'count'=>$val->count);
            }
        }
        return $rec1;
    }
    
    function getAllCurrentForDateP($edate){
        $rec1=array();
        $rec=$this->getAllCurrentForDate($edate);
        if($rec!==false && sizeof($rec)>0){
            foreach($rec as $val){
                $rec1[$val->productid]=array('countproduced'=>$val->countproduced, 'count'=>$val->count);
            }
        }
        return $rec1;
    }
    
    function createProductProduction(){
        require_once 'Settings.php';
        $setting=new Settings();
        $json=array();
        if(isset($_SESSION['whatUserOnDetails']) && $setting->checkIsUserLogedAndToken($_SESSION['whatUserOnDetails']->id)===true){
            if($_SESSION['whatUserOnDetails']->roles==2){
                $a=$setting->sanitizeEntirePost($_POST);
                if($a!==false && sizeof($a)>0){
                    if(true===array_key_exists('productid', $a) && is_array($a['productid']) && sizeof($a['productid'])>0){
                        require_once 'ProductsFE.php';
                        $product=new ProductsFE();
                        $rec=$product->prepareAllProductsForArray();
                        if($rec!==false && sizeof($rec)>0){
                            $a['userid']=$_SESSION['whatUserOnDetails']->id;
                            $a['username']=$_SESSION['whatUserOnDetails']->name;
                            $products=$this->prepareAllOrderedProductsForInsertion($a['productid'], $rec);
                            unset($a['productid']);
                            $a['productid']=1;
                            $a['qty']=1;
                            $a['productprice']=1;
                            $a['productname']=1;
                            $a['productsubtitle']=1;
                            $a['edate']=isset($_SESSION['currentProductionDate'])?$_SESSION['currentProductionDate']:date('Y-m-d H:i:s');
                            $a['crdate']=1;
                            $id=$this->insertProductProduction($a, $products);
                            if($id!==false && $id+0>0){
                                $todayEdate=isset($_SESSION['currentProductionDate'])?$_SESSION['currentProductionDate']:date('Y-m-d H:i:s');
                                $rec1=$this->getAllProductedForDateP($todayEdate);
                                $json['success']=1;
                                $json['message']='Magacin je zadužen uspješno!';
                                $json['results']=$rec1;
                            }else{
                                $json['success']=5;
                                $json['message']='Greška! Proizvodi ne mogu biti dodani.';
                            }
                        }
                    }else{
                        $json['success']=4;
                        $json['message']='Niste zadužili ni jedan proizvod';
                    }
                }
            }else{
                $json['success']=3;
                $json['message']='Greška! Samo magacioner može proizvoditi.';
            }
        }else{
            $json['success']=0;
            $json['message']='Nemate ovlaštenja za ovu sekciju';
        }
        return $json;
    }
}