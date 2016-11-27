<?php
require_once 'DBconnect.php';

class Productions{
    var $table='productions';
    
    protected function getAllProductedForDate($edate){
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT productid, SUM(IF(etype=1, -1*qty, qty)) AS count FROM ' . $table . ' WHERE id>:id AND DATE(edate)=DATE(NOW()) ';
        $sql .='GROUP BY productid ';
        $sql .='ORDER BY crdate DESC';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)0));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getAllCurrentForDate($edate){
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT c.id AS productid, c.name, DATE(a.edate) AS producedate, DATE(b.crdate) AS solddate, DATE("' . $edate . '") AS todaydate, a.count AS countproduced, b.count AS countsold, (a.count-b.count) AS count
FROM 
' . PRETABLE . 'products c LEFT JOIN 
(SELECT productid, edate, SUM(IF(etype=1, -1*qty, qty)) AS count FROM ' . $table . ' WHERE DATE(edate) = DATE("' . $edate . '") GROUP BY productid) a ON c.id=a.productid
LEFT JOIN 
(SELECT productid, crdate, SUM(IF(etype=1, -1*qty, qty)) AS count FROM ' . PRETABLE . 'orderdetails WHERE DATE(crdate) = DATE("' . $edate . '") GROUP BY productid) b ON c.id=b.productid
WHERE c.id>:id
GROUP BY c.id';
        
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)0));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function insertProductProduction($a, $rec){
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $table=PRETABLE . $this->table;
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
                $counter=0;
                foreach($rec as $key => $val){
                    $b=array();
                    $b[]=$a['etype'];
                    $b[]=$a['userid'];
                    $b[]=$a['username'];
                    $b[]=$val['id'];
                    $b[]=$val['qty'];
                    $b[]=$val['price'];
                    $b[]=$val['name'];
                    $b[]=$val['subtitle'];
                    $b[]=$a['edate'];
                    $b[]=date('Y-m-d H:i:s');
                    $success=$sth->execute($b);
                    if($success!==false){
                        $counter++;
                    }
                }
                if($counter-sizeof($rec)>=0){
                    return true;
                }else{
                    return false;
                }
        }
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
}