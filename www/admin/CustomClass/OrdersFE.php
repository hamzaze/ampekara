<?php
require_once('Orders.php');

class OrdersFE extends Orders{
    
    function createOrderForCustomer($customerid){
        require_once 'Settings.php';
        $setting=new Settings();
        $json=array();
        if(isset($_SESSION['whatUserOnDetails']) && $setting->checkIsUserLogedAndToken($_SESSION['whatUserOnDetails']->id)===true){
            switch($_SESSION['whatUserOnDetails']->roles){
                default:
                    $json['success']=3;
                    $json['message']='Nemate ovlaštenja za ovu sekciju. Dozvoljeno samo vozačima';
                break;
                case 3:
                    $a=$setting->sanitizeEntirePost($_POST);
                    if($a!==false && sizeof($a)>0){
                        if(true===array_key_exists('productid', $a) && is_array($a['productid']) && sizeof($a['productid'])>0){
                            require_once 'ProductsFE.php';
                            $product=new ProductsFE();
                            $rec=$product->prepareAllProductsForArray();
                            if($rec!==false && sizeof($rec)>0){
                                require_once 'CustomersFE.php';
                                $customer=new CustomersFE();
                                $recCustomer=$customer->getCustomerPB($customerid);
                                if($recCustomer!==false && sizeof($recCustomer)>0){
                                    $a['customername']=$recCustomer->name;
                                }
                                $a['userid']=$_SESSION['whatUserOnDetails']->id;
                                $a['username']=$_SESSION['whatUserOnDetails']->name;
                                $a['cart']=$this->prepareAllOrderedProductsForInsertion($a['productid'], $rec);
                                $a['amount']=$this->getAmountAllOrderProducts($a['productid'], $rec);
                                $a['edate']=date('Y-m-d H:i:s');
                                
                                $id=$this->insertOrder($a);
                                if($id!==false && $id+0>0){
                                    $rec=$this->getOrder($id);
                                    if($rec!==false && sizeof($rec)>0){
                                        $id=$this->insertOrderDetails($rec);
                                        if($id!==false){
                                            require_once 'ProductionsFE.php';
                                            $production=new ProductionsFE();
                                            $todayEdate=date('Y-m-d H:i:s');
                                            $rec2=$production->getAllCurrentForDateP($todayEdate);
                                            
                                            $json['success']=1;
                                            $json['message']='Otpremnica kreirana uspješno!';
                                            $json['results']=$rec2;
                                        }
                                    }else{
                                        $json['success']=6;
                                        $json['message']='Otpremnica nije pronađena u bazi.';
                                    }
                                }else{
                                    $json['success']=5;
                                    $json['message']='Greška prilikom kreiranja otpremnice.';
                                }
                            }
                        }else{
                            $json['success']=4;
                            $json['message']='Niste zadužili ni jedan proizvod';
                        }
                    }
                break;
            }
        }else{
            $json['success']=0;
            $json['message']='Nemate ovlaštenja za ovu sekciju.';
        }
        return $json;
    }
}