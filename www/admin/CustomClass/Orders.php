<?php
require_once 'DBconnect.php';

class Orders{
    var $table='orders';
    var $tableDetails='orderdetails';
    
    var $arrOrderTypes=array(
        '',
        'Interna Prodavnica',
        'Externa Prodavnica',
        'Zaposlenici'
    );
    
    function reSortSortings($ids){
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        //$sql='INSERT INTO ' . $table . ' (id,sorting) VALUES :values ON DUPLICATE KEY UPDATE sorting=VALUES(sorting)';
        $sql='INSERT INTO ' . $table . ' (id,sorting) VALUES ' . $ids . ' ON DUPLICATE KEY UPDATE sorting=VALUES(sorting)';
        $sth=$dbh->prepare($sql);
        $success=$sth->execute();
        if($success!==false) return 1;
        else return false;
    }
    
    function deleteOrder($id){ 
        if(sizeof($this->getOrder($id))>0){
            $dbh=new DBconnect();
            $dbh=$dbh->sql;
            $sql='DELETE FROM ' . PRETABLE . $this->table . ' WHERE id=:id LIMIT 1';
            $sth=$dbh->prepare($sql);
            $success=$sth->execute(array(':id'=>(int)$id));
            $success=$success==1?true:false;

            if($success===true) return true;
            return $success;
        }else return false;
    }
    
    
    protected function getOrder($id){
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, userid, customerid, username, customername, cart, amount, etype, edate, crdate FROM ' . $table . ' WHERE id=:id ';
        $sql .='LIMIT 0,1';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)$id));
        $rec=$sth->fetch(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getAllProductsFromOrder($id){
        $rec=array();
        $table=PRETABLE . $this->tableDetails;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, orderid, userid, customerid, productid, productprice, qty, productname, productsubtitle, edate, crdate FROM ' . $table . ' WHERE orderid=:id ';
        $sql .='ORDER BY crdate DESC';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)$id));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getAllOrders($islimit=false){
        $islimit=$islimit===false?'':' LIMIT 0, ' . $islimit;
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, username, customername, cart, amount, etype, edate, crdate FROM ' . $table . ' WHERE id>:id ';
        $sql .='ORDER BY crdate DESC';
        $sql .=$islimit;
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)0));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    function addEditOrder(){
        $json=array();
        $id=$_POST['aEOE_id']>0?$this->updateOrder():$this->insertOrder();
        if($id!==false){
            require_once 'Settings.php';
            $setting=new Settings();
            require_once 'Images.php';
            $image=new Images();
            $json['success']=1;
            $json['editid']=$_POST['aEOE_id'];
            $rec=$this->getOrder($id);
            $key=0;
            $json['content']=$this->wrapSingleRowOrder($rec, $key, $setting, $image, true);
        }else{
            $json['success']=0;
            $json['message']='Error - ' . __FUNCTION__;
        }
        return $json;
    }
    
    protected function insertOrderDetails($rec){
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $table=PRETABLE . $this->tableDetails;
        $a=array('orderid'=>1, 'userid'=>1, 'customerid'=>1, 'productid'=>1, 'qty'=>1, 'productprice'=>1, 'productname'=>1, 'productsubtitle'=>1, 'etype'=>1, 'edate'=>1, 'crdate'=>1);
        $sql='INSERT INTO ' . $table . ' (';
        $counter=0;
        $string='';
        foreach ($a as $keya => $vala){
                if($counter>0){
                        $string .=', ';
                        $sql .=', ';
                }
                $sql .=$keya;
                $string .='?';
                $counter++;
        }
        $sql .=')';
        $sql .='VALUES (' . $string . ')';
        $sth=$dbh->prepare($sql);
        if($rec!==false && sizeof($rec)>0){
            $rec1=unserialize(base64_decode($rec->cart));
            if($rec1!==false && sizeof($rec1)>0){
                $counter=0;
                foreach($rec1 as $key => $val){
                    $b=array();
                    $b[]=$rec->id;
                    $b[]=$rec->userid;
                    $b[]=$rec->customerid;
                    $b[]=$val['id'];
                    $b[]=$val['qty'];
                    $b[]=$val['price'];
                    $b[]=$val['name'];
                    $b[]=$val['subtitle'];
                    $b[]=$rec->etype;
                    $b[]=$rec->edate;
                    $b[]=date('Y-m-d H:i:s');
                    $success=$sth->execute($b);
                    if($success!==false){
                        $counter++;
                    }
                }
                if($counter-sizeof($rec1)>=0){
                    return true;
                }else{
                    return false;
                }
            }
        }
    }
    
    protected function insertOrder($a=array(), $table=''){
        require_once 'Settings.php';
        $setting=new Settings();
        $table=$table==''?PRETABLE . $this->table:$table;
        $rec=array();
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        if($a===false || sizeof($a)<1){
            foreach ($_POST as $key => $val) if(preg_match('/^aEOE_/', $key)==1 && $key!='aEOE_id') $a[preg_replace('/^aEOE_/', '', $key)]=$val; 
        }else{
            if(true===array_key_exists('productid', $a)){
                unset($a['productid']);
            }
            if(true===array_key_exists('cart', $a)){
                $a['cart']=base64_encode(serialize($a['cart']));
            }
        }
        $sql='INSERT INTO ' . $table . ' (';
        $counter=0;
        $string='';
        foreach ($a as $keya => $vala){
                if($counter>0){
                        $string .=', ';
                        $sql .=', ';
                }
                $sql .=$keya;
                $string .='?';
                $counter++;
        }
        $sql .=',crdate)';
        $string .=',?';
        $sql .='VALUES (' . $string . ')';
        $sth=$dbh->prepare($sql);
        array_push($a, date('Y-m-d H:i:s'));
        $b=array();
        foreach ($a as $keyb =>$valb) $b[]=$valb;
        $success=$sth->execute($b);

        if($success!==false){
            $id=$dbh->lastInsertId();
            return $id;
        }
        else return false;
    }
    
    protected function updateOrder($table=''){
        require_once 'Settings.php';
        $setting=new Settings();
        $table=$table==''?PRETABLE . $this->table:$table;
        $rec=array();
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $a=array();
        foreach ($_POST as $key => $val) if(preg_match('/^aEOE_/', $key)==1 && $key!='aEOE_id') $a[preg_replace('/^aEOE_/', '', $key)]=$val;
        $sql='UPDATE ' . $table . ' SET ';
        $counter=0;
        foreach ($a as $keya => $vala){
                if($counter>0) $sql .=', ';
                $sql .=$keya . '=?';
                $counter++;
        }		
        $sql .='WHERE id=?';
        $sth=$dbh->prepare($sql);
        array_push($a, $_POST['aEOE_id']);
        $b=array();
        foreach ($a as $keyb =>$valb) $b[]=$valb;
        $success=$sth->execute($b);
        if($success!==false){
            return $_POST['aEOE_id'];
        }
        else return false;
    }
    
    protected function prepareAllOrderedProductsForInsertion($a, $rec){
        $rec1=array();
        if($a!==false && sizeof($a)>0){
            foreach($a as $key => $val){
                if(true===array_key_exists($key, $rec)){
                    $rec1[$key]=array('qty'=>$val, 'id'=>$rec[$key]->id, 'articleid'=>$rec[$key]->articleid, 'name'=>$rec[$key]->name, 'subtitle'=>$rec[$key]->subtitle, 'price'=>$rec[$key]->price);
                }
            }
        }
        return $rec1;
    }
    
    protected function getAmountAllOrderProducts($a, $rec){
        $sum=0;
        if($a!==false && sizeof($a)>0){
            foreach($a as $key => $val){
                if(true===array_key_exists($key, $rec)){
                    $sum+=$val*$rec[$key]->price;
                }
            }
        }
        return $sum;
    }
    
    protected function getAmountProductListForOrder($rec){
        $sum=0;
        if($rec!==false && sizeof($rec)>0){
            foreach($rec as $val){
                $sum +=$val->qty*$val->productprice;
            }
        }
        return $sum;
    }
    
    protected function wrapProductListForOrder($id, $isAjax=true){
        $content='';
        $rows='';
        $pre=$isAjax===true?'../':'';
        $rec=$this->getAllProductsFromOrder($id);
        if($rec!==false && sizeof($rec)>0){
            $sum=$this->getAmountProductListForOrder($rec);
            $sum=number_format($sum, 2);
            foreach($rec as $key => $val){
                $rows .=$this->wrapSingleProductListForOrder($val, $key);
            }
            ob_start(); // start output buffer
            include ($pre . PATH_TO_HTML . 'tableOrderedProducts.htm');
            $content .= ob_get_contents(); // get contents of buffer
            ob_end_clean();
        }
        return $content;
    }
    
    function wrapAllOrders($section){
        $content='';
        require_once 'Settings.php';
        $setting=new Settings();
        require_once 'Images.php';
        $image=new Images();
        if(isset($_GET['exportcsv'])) return $this->exportAllOrder();
        $rec=$this->getAllOrders();
        $listOrders='';
        if(sizeof($rec)>0) foreach ($rec as $key => $val) $listOrders .=$this->wrapSingleRowOrder($val, $key, $setting, $image);                
        $content='';
        ob_start(); // start output buffer
        include (PATH_TO_HTML . 'tableOrder.htm');
        $content .= ob_get_contents(); // get contents of buffer
        ob_end_clean();
        return $content;
    }
    
    function wrapSingleRowOrder($rec, $key, $setting, $image, $isAjax=false){            
            $class=$key%2==0?'odd':'even';
            $class .=$rec->etype==1?' minus-mode':'';
            if($rec->etype==1){
                $rec->amount='-' . $rec->amount;
            }
            $sorting=0;
            $content='';
            $img='';
            $today=date('Ymd');
            $date=date('d.m.Y', strtotime($rec->crdate));
            if($today==date('Ymd', strtotime($rec->crdate))){
                $date='Danas';
            }
            $time=date('H:i', strtotime($rec->crdate));
            $pre=$isAjax===true?'../':'';
            $stringDelete='';
            $stringDelete .='<div>';
            $stringDelete .='<div><div class="inline-block">Otpremnica #:</div> <div class="inline-block">' . $rec->id . '/' . date('Y', strtotime($rec->crdate)) . '</div><div class="noFloat"></div></div>';
            $stringDelete .='<div><div class="inline-block">Datum:</div> <div class="inline-block">' . $date . ' u ' . $time . '</div><div class="noFloat"></div></div>';
            $stringDelete .='<div><div class="inline-block">Prodavnica:</div> <div class="inline-block">' . $rec->customername . '</div><div class="noFloat"></div></div>';
            $stringDelete .='<div><div class="inline-block">Vozač:</div> <div class="inline-block">' . $rec->username . '</div><div class="noFloat"></div></div>';
            $stringDelete .='<div><div class="inline-block">Iznos:</div> <div class="inline-block">' . $rec->amount . ' ' . CURRENCYSIGN . '</div><div class="noFloat"></div></div>';
            $stringDelete .='</div>';
            $stringDelete=htmlspecialchars($stringDelete);
            
            $content .='<div rel="sortItem" class="tr ' . $class . '" data-sorting="' . $sorting . '" data-id="' . $rec->id . '">';
            $content .='<div class="td td0 text-right">' . $rec->id . '/' . date('Y', strtotime($rec->crdate)) . '</div>';
            $content .='<div class="td td5">' . $date . ' u ' . $time . '</div>';
            $content .='<div class="td">' . $rec->customername . '</div>';
            $content .='<div class="td td4">' . $rec->username . '</div>';
            $content .='<div class="td td4 text-right">' . $rec->amount . ' ' . CURRENCYSIGN . '</div>';
            $content .='<div class="td td6">
                <div class="right">
                    <div class="pencil left"><a data-id="' . $rec->id . '" href="#" data-action="addedititem" data-context="wrapAddEditOrder" title="Pogledaj otpremnicu"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>'
                 . '<div class="delete left"><a class="deleteCourse" data-action="deleteitem" data-context="deleteOrder" href="#" data-id="' . $rec->id . '" data-title="' . $stringDelete . '" title="Obriši otpremnicu"><i class="fa fa-trash" aria-hidden="true"></i></a></div>
                     <div class="noFloat"></div>
                </div>
                <div class="noFloat"></div>
                </div>';
            $content .='</div>';
           return $content;
       }
       
    private function wrapSingleProductListForOrder($rec, $key){
        $amount=$rec->qty*$rec->productprice;
        $productsubtitle=$rec->productsubtitle!=''?'<br /><span class="text-muted">' . $rec->productsubtitle . '</span>':'';
        $content='';
        $class=$key%2==0?'odd':'even';
        $content .='<div class="tr ' . $class . '">';
        $content .='<div class="td text-left">' . $rec->productname . $productsubtitle . '</div>';
        $content .='<div class="td td4">' . $rec->qty . '</div>';
        $content .='<div class="td td4 text-right">' . $rec->productprice . '</div>';
        $content .='<div class="td td4 text-right">' . number_format($amount, 2) . '</div>';
        $content .='</div>';
        return $content;
    }   
       
       
    function wrapAddEditOrder($id=0, $isAjax=true){
        require_once 'Settings.php';
        $setting=new Settings();
        
        $json=array();
        $pre=$isAjax===true?'../':'';
        $content=$fileUploader=$img='';
        if($id>0) $rec=$this->getOrder($id);
        else{
            $rec=new stdClass();
            $rec->name=$rec->address=$rec->comment='';
            $rec->etype=0;
        }
        $today=date('Ymd');
        $date=date('d.m.Y', strtotime($rec->crdate));
        if($today==date('Ymd', strtotime($rec->crdate))){
            $date='Danas';
        }
        $time=date('H:i', strtotime($rec->crdate));
        $productList=$this->wrapProductListForOrder($rec->id);
        ob_start(); // start output buffer
        include ($pre . PATH_TO_HTML . 'addEditOrder.htm');
        $content .= ob_get_contents(); // get contents of buffer
        ob_end_clean();
        $json['success']=1;
        $json['content']=$content;
        return $json;
       return $content;
   }   
}