<?php
require_once('Customers.php');

class CustomersFE extends Customers{
    
    function getCustomerPB($id){
        return $this->getCustomer($id);
    }
    
    function getJSONCustomers(){
        $rec=$this->getAllCustomers();
        if($rec!==false && sizeof($rec)>0){
            require_once 'ProductsFE.php';
            $product=new ProductsFE();
            require_once 'Settings.php';
            $setting=new Settings();
            
            foreach(@$rec as $key => $val){
                $val->name .=' - ' . rand(0,1000);
                if(isset($_SESSION['whatUserOnDetails']) && $setting->checkIsUserLogedAndToken($_SESSION['whatUserOnDetails']->id)===true){
                    switch($_SESSION['whatUserOnDetails']->roles){
                        default:
                        case 3:
                          $whatIcon='';  
                        break;
                        case 2:
                            $whatIcon='';
                        break;
                        case 1:
                            $whatIcon='';
                        break;
                    }
                    $val->username='<i class="icon ' . $whatIcon . '"></i> ' . $_SESSION['whatUserOnDetails']->name;
                }
                $val->products=$product->getJSONProducts();
            }
        }
        return $rec;
    }
}