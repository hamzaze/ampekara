<?php
require_once 'DBconnect.php';

class Schedulers{
    var $table='schedulers';
    
    var $arrIsBooked=array(0=>'Available', 1=>'Booked');
    
    var $arrPaymentMethods=array(0=>'Cach', 1=>'Email Transfer', 2=>'PayPal');
    
    var $arrTimeSheetFields=array(
        'isonline'=>1,
        'firstname'=>'',
        'lastname'=>'',
        'phonenumber'=>'',
        'email'=>'',
        'isbooked'=>0,
        'familysize'=>'',
        'paymentconfirmationnumber'=>'',
        'comment'=>'',
        'edate'=>'',
        'paymentmethod'=>0,
        'ispaid'=>0
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
    
    function markOnlineTimeSheet($id, $schedulerid){
        $json=array();
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $rec=$this->getTimeSheetIsOnline($id, $schedulerid);
        if($rec!==false && sizeof($rec)>0){
            $isonline=$rec->content==1?0:1;
            $sql='UPDATE ' . PRETABLE . 'timesheets SET ';
            $sql .='content=:content ';
            $sql .='WHERE schedulerid=:schedulerid AND timesheetid=:id AND fieldname=:fieldname LIMIT 1';
        }else{
            $isonline=1;
            $sql='INSERT INTO ' . PRETABLE . 'timesheets (schedulerid, timesheetid, fieldname, content, crdate) ';
            $sql .='VALUES (:schedulerid, :id, :fieldname, :content, "' . date('Y-m-d H:i:s') . '")';
        }
        $b=array(':schedulerid'=>(int)$schedulerid, ':id'=>(int)$id, ':fieldname'=>'isonline', ':content'=>$isonline);
        $sth=$dbh->prepare($sql);
        $success=$sth->execute($b);
        if($success!==false){
            $json['success']=1;
            $json['content']=$isonline==1?'<i class="fa fa-eye" aria-hidden="true"></i>':'<i class="fa fa-eye-slash" aria-hidden="true"></i>';
        }else{
            $json['success']=0;
            $json['message']='Error# Truncating TimeSheet';
        }
        return $json;
    }
    
    function truncateTimeSheetForScheduler($id, $schedulerid){
        $json=array();
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='DELETE FROM ' . PRETABLE . 'timesheets WHERE timesheetid=:id AND schedulerid=:schedulerid';
        $sth=$dbh->prepare($sql);
        $success=$sth->execute(array(':id'=>(int)$id, ':schedulerid'=>(int)$schedulerid));
        if($success!==false){
            $json['success']=1;
        }else{
            $json['success']=0;
            $json['message']='Error# Truncating TimeSheet';
        }
        return $json;
    }
    
    function deleteTimeSheetForSheduler($id, $schedulerid){
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='DELETE FROM ' . PRETABLE . 'timesheets WHERE timesheetid=:id AND schedulerid=:schedulerid';
        $sth=$dbh->prepare($sql);
        $success=$sth->execute(array(':id'=>(int)$id, ':schedulerid'=>(int)$schedulerid));
        $success=$success==1?true:false;

        if($success===true) return true;
        return $success;
    }
    
    function deleteScheduler($id){ 
        if(sizeof($this->getScheduler($id))>0){
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
    
    
    protected function getScheduler($id){
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, edate, sorting, crdate FROM ' . $table . ' WHERE id=:id ';
        $sql .='LIMIT 0,1';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)$id));
        $rec=$sth->fetch(PDO::FETCH_OBJ);
        return $rec;
    }
    
    private function getAvailableTimesForScheduler($schedulerid, $isBooked=0){
        $rec=array();
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT a.id, a.name
FROM
(
SELECT z.schedulerid, z.timesheetid FROM
(
SELECT schedulerid, timesheetid
FROM ' . PRETABLE . 'timesheets
WHERE fieldname=:fieldname1 AND content=:content1
) z INNER JOIN 
(
SELECT schedulerid, timesheetid
FROM ' . PRETABLE . 'timesheets
WHERE fieldname=:fieldname2 AND content=:content2
) x ON CONCAT(z.schedulerid, z.timesheetid)=CONCAT(x.schedulerid, x.timesheetid)
WHERE z.schedulerid=:schedulerid
) y INNER JOIN ' . PRETABLE . 'schedulers c ON y.schedulerid=c.id INNER JOIN ' . PRETABLEHELPER . 'timesheet a ON y.timesheetid=a.id
GROUP BY a.id ORDER BY a.sorting';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':schedulerid'=>(int)$schedulerid, ':fieldname1'=>'isbooked', ':content1'=>$isBooked, ':fieldname2'=>'isonline', ':content2'=>'1'));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getTimeSheetIsOnline($id, $schedulerid){
        $rec=array();
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT fieldname, content FROM ' . PRETABLE . 'timesheets WHERE schedulerid=:schedulerid AND timesheetid=:id AND fieldname=:fieldname LIMIT 0,1';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)$id, ':schedulerid'=>(int)$schedulerid, ':fieldname'=>'isonline'));
        $rec=$sth->fetch(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getSchedulerAndTime($id, $schedulerid){
        $rec=array();
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT a.name, b.edate 
FROM ' . PRETABLEHELPER . 'timesheet a
CROSS JOIN
' . PRETABLE . 'schedulers b
WHERE a.id=:id AND b.id=:schedulerid';
        
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)$id, ':schedulerid'=>(int)$schedulerid));
        $rec=$sth->fetch(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getAllFieldsForTimeSheetScheduler($id, $schedulerid){
        $rec=array();
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT a.id AS timesheetid, a.name AS timesheetname, b.fieldname, b.content
FROM ' . PRETABLEHELPER . 'timesheet a INNER JOIN ' . PRETABLE . 'timesheets b ON a.id=b.timesheetid
    INNER JOIN ' . PRETABLE . $this->table . ' c ON b.schedulerid=c.id
WHERE b.schedulerid=:schedulerid AND b.timesheetid=:id
GROUP BY a.id, b.fieldname
ORDER BY a.id';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':schedulerid'=>(int)$schedulerid, ':id'=>(int)$id));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getAllTimeSheetForScheduler($id){
        $rec=array();
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT a.id AS timesheetid, a.name AS timesheetname, b.fieldname, b.content, b.schedulerid, b.sorting
FROM ' . PRETABLEHELPER . 'timesheet a LEFT JOIN ' . PRETABLE . 'timesheets b ON a.id=b.timesheetid
    LEFT JOIN ' . PRETABLE . $this->table . ' c ON b.schedulerid=c.id AND c.id=:id
GROUP BY a.id, b.fieldname
ORDER BY a.sorting';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)$id));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getAllSchedulers($islimit=false){
        $islimit=$islimit===false?'':' LIMIT 0, ' . $islimit;
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, edate, sorting FROM ' . $table . ' WHERE id>:id ';
        $sql .='ORDER BY sorting ASC';
        $sql .=$islimit;
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)0));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    function addEditTimeSheetForScheduler(){
        require_once 'Settings.php';
        $setting=new Settings();
        $json=array();
        $a=$setting->sanitizeEntirePost($_POST);
        $this->deleteTimeSheetForSheduler($a['timesheetid'], $a['schedulerid']);
        $id=$this->insertTimeSheetForScheduler($a);
        if($id!==false){
            $json['success']=1;
            $json['editid']=$a['timesheetid'];
            $rec=$this->getAllFieldsForTimeSheetScheduler($a['timesheetid'], $a['schedulerid']);
            if($rec!==false && sizeof($rec)>0){
               $rec1=array();
               
               foreach($rec as $val){
                   $whatKey=$val->fieldname!==NULL?$val->fieldname:0;
                   if($val->fieldname!==NULL){
                        $rec1[$val->timesheetid][$val->fieldname]=$val;
                   }else{
                       $rec1[$val->timesheetid]=$val->timesheetname;
                   }
               }
            }
           
            $counter=0;
            $json['content']=$this->wrapSingleRowTimeSheetForScheduler(current($rec1), $a['timesheetid'], $counter);
        }else{
            $json['success']=0;
            $json['message']='Error - ' . __FUNCTION__;
        }
        return $json;
    }
    
    function addEditScheduler(){
        $json=array();
        $id=$_POST['aEOE_id']>0?$this->updateScheduler():$this->insertScheduler();
        if($id!==false){
            $json['success']=1;
            $json['editid']=$_POST['aEOE_id'];
            $rec=$this->getScheduler($id);
            $key=0;
            $json['content']=$this->wrapSingleRowScheduler($rec, $key);
        }else{
            $json['success']=0;
            $json['message']='Error - ' . __FUNCTION__;
        }
        return $json;
    }
    
    private function insertTimeSheetForScheduler($c){
        $table=PRETABLE . 'timesheets';
        $rec=array();
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $a=array('schedulerid'=>1, 'timesheetid'=>1, 'fieldname'=>1, 'content'=>1, 'crdate'=>1);
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
        $b=array();
        $counter=0;
        $counter1=0;
        foreach($c as $keyc => $valc){
            $b=array();
            if(true===array_key_exists($keyc, $this->arrTimeSheetFields)){
                $b[]=$c['schedulerid'];
                $b[]=$c['timesheetid'];
                $b[]=$keyc;
                $b[]=$valc;
                $b[]=date('Y-m-d H:i:s');
                $counter++;
                $success=$sth->execute($b);
                if($success!==false) $counter1++;
            }
        }
        if($counter1-$counter>=0) return true;
        else return false;
    }
    
    protected function insertScheduler($table=''){
        require_once 'Settings.php';
        $setting=new Settings();
        $table=$table==''?PRETABLE . $this->table:$table;
        $rec=array();
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $a=array();
        foreach ($_POST as $key => $val) if(preg_match('/^aEOE_/', $key)==1 && $key!='aEOE_id') $a[preg_replace('/^aEOE_/', '', $key)]=$val;  
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
    
    protected function updateScheduler($table=''){
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
    
    function wrapAllSchedulers($section){
        $content='';
        require_once 'Settings.php';
        $setting=new Settings();
        require_once 'Images.php';
        $image=new Images();
        if(isset($_GET['exportcsv'])) return $this->exportAllScheduler();
        $rec=$this->getAllSchedulers();
        $listSchedulers='';
        if(sizeof($rec)>0) foreach ($rec as $key => $val) $listSchedulers .=$this->wrapSingleRowScheduler($val, $key, $setting, $image);                
        $content='';
        ob_start(); // start output buffer
        include (PATH_TO_HTML . 'tableScheduler.htm');
        $content .= ob_get_contents(); // get contents of buffer
        ob_end_clean();
        return $content;
    }
    
    private function wrapAvailableTimes($schedulerid){
        $content='';
        $rec=$this->getAvailableTimesForScheduler($schedulerid);
        if($rec!==false && sizeof($rec)>0){
            foreach($rec as $key => $val){
                $content .='<div class="inline-block btn btn-default btn-xs">' . $val->name . '</div>';
            }
        }
        return $content;
    }
    
    private function wrapBookedTimes($schedulerid){
        $content='';
        $rec=$this->getAvailableTimesForScheduler($schedulerid, 1);
        if($rec!==false && sizeof($rec)>0){
            foreach($rec as $key => $val){
                $content .='<div class="inline-block btn btn-default btn-xs">' . $val->name . '</div>';
            }
        }
        return $content;
    }
    
    function wrapSingleRowScheduler($rec, $key){            
            $class=$key%2==0?'odd':'even'; 
            $sorting=0;
            $content='';
            $availableTimes=$this->wrapAvailableTimes($rec->id);
            $bookedTimes=$this->wrapBookedTimes($rec->id);
            $date=date('F j, Y', strtotime($rec->edate));
            $content .='<div rel="sortItem" class="tr sortableRow ' . $class . '" data-sorting="' . $sorting . '" data-id="' . $rec->id . '">';
            $content .='<div class="td td1">' . $date . '</div>';
            $content .='<div class="td td4">' . $availableTimes . '</div>';
            $content .='<div class="td td4">' . $bookedTimes . '</div>';
            $content .='<div class="td td6">
                <div class="right">
                    <div class="pencil left"><a data-id="' . $rec->id . '" href="#" data-action="addedititem" data-context="wrapAddEditScheduler" title="Edit Scheduler"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>'
                 . '<div class="delete left"><a class="deleteCourse" data-action="deleteitem" data-context="deleteScheduler" href="#" data-id="' . $rec->id . '" data-title="' . $date . '" title="Delete scheduler"><i class="fa fa-trash" aria-hidden="true"></i></a></div>
                     <div class="noFloat"></div>
                </div>
                <div class="noFloat"></div>
                </div>';
            $content .='</div>';
            return $content;
        }
    
    function wrapAddEditScheduler($id=0, $isAjax=true){
            $json=array();
            $pre=$isAjax===true?'../':'';
            $content=$fileUploader=$img='';
            if($id>0){
                $fileName='addEditSchedulerDetails';
                $rec=$this->getScheduler($id);
                $rec->edate=date('F j, Y', strtotime($rec->edate));
                $timeSheetTable=$this->wrapTimeSheetTable($rec->id);
            }else{
                $fileName='addEditScheduler';
                $rec=new stdClass();
                $rec->edate='';
            }
            
            ob_start(); // start output buffer
            include ($pre . PATH_TO_HTML . $fileName . '.htm');
            $content .= ob_get_contents(); // get contents of buffer
            ob_end_clean();
            $json['success']=1;
            $json['content']=$content;
            return $json;
           return $content;
       }
       
       function wrapAddEditTimeSheetForScheduler($id, $schedulerid, $isAjax=true){
            require_once 'Settings.php';
            $setting=new Settings();
            $json=array();
            $pre=$isAjax===true?'../':'';
            $rec=new stdClass();
                foreach($this->arrTimeSheetFields as $key => $val){
                    $rec->{$key}=$val;
                }
                $rec->edate=date('Y-m-d');
            $rec1=$this->getAllFieldsForTimeSheetScheduler($id, $schedulerid);
            if($rec1!==false && sizeof($rec1)>0){
                foreach($rec1 as $val1){
                    $rec->{$val1->fieldname}=$val1->content;
                }
            }
            $rec2=$this->getSchedulerAndTime($id, $schedulerid);
            if($rec2!==false && sizeof($rec2)>0){
                $date=date('F j, Y', strtotime($rec2->edate));
                $timesheetName=$rec2->name . ' - ' . $date;
            }
            $rec->isonline=$rec->isonline==1?' checked="checked"':'';
            $rec->ispaid=$rec->ispaid==1?' checked="checked"':'';
            $isBookedSB=$setting->wrapSB($rec->isbooked, 'isbooked', $this->arrIsBooked);
            $paymentMethodSB=$setting->wrapSB($rec->paymentmethod, 'paymentmethod', $this->arrPaymentMethods);
            
            ob_start(); // start output buffer
            include ($pre . PATH_TO_HTML . __FUNCTION__ . '.htm');
            $content .= ob_get_contents(); // get contents of buffer
            ob_end_clean();
            $json['success']=1;
            $json['content']=$content;
            return $json;
       }
       
       function wrapTimeSheetTable($id, $isAjax=true){
           $content='';
           $row='';
           $pre=$isAjax===true?'../':'';
           $rec=$this->getAllTimeSheetForScheduler($id);
           if($rec!==false && sizeof($rec)>0){
               $rec1=array();
               foreach($rec as $val){
                   $whatKey=$val->fieldname!==NULL?$val->fieldname:0;
                   if($val->fieldname!==NULL){
                        $rec1[$val->timesheetid][$val->fieldname]=$val;
                   }else{
                       $rec1[$val->timesheetid]=$val->timesheetname;
                   }
               }
               if($rec1!==false && sizeof($rec1)>0){
                   $counter=0;
                    foreach($rec1 as $key => $val){
                        $row .=$this->wrapSingleRowTimeSheetForScheduler($val, $key, $counter);
                        $counter++;
                    }
                    ob_start(); // start output buffer
                    include ($pre . PATH_TO_HTML . 'tableTimeSheetForScheduler.htm');
                    $content .= ob_get_contents(); // get contents of buffer
                    ob_end_clean();
               }
           }
           return $content;
       }
       
       function wrapSingleRowTimeSheetForScheduler($rec, $key, $counter){
           $class=$counter%2==0?'odd':'even'; 
           $name='';
           $bookedOn='';
           $email='';
           $isonline='-slash';
           if(!is_array($rec)){
               $timesheetname=$rec;
           }else{
               $timesheetname=current($rec)->timesheetname;
               if(true===array_key_exists('isbooked', $rec)){
                   if($rec['isbooked']->content==1){
                       $class .=' bg-green';
                   }
               }
               if(true===array_key_exists('firstname', $rec)){
                   $name .=$rec['firstname']->content;
               }
               if(true===array_key_exists('lastname', $rec)){
                   $name .=' ' . $rec['lastname']->content;
               }
               if(true===array_key_exists('edate', $rec)){
                   $bookedOn=date('F j, Y', strtotime($rec['edate']->content));
               }
               if(true===array_key_exists('email', $rec)){
                   $email=$rec['email']->content;
               }
               if(true===array_key_exists('isonline', $rec)){
                   $isonline=$rec['isonline']->content==1?'':'-slash';
               }
           }
           $content='';
           
            $sorting=0;
            $content='';
            $content .='<div class="tr ' . $class . '" data-sorting="' . $sorting . '" data-id="' . $key . '">';
            $content .='<div class="td td1 text-left">' . $timesheetname . '</div>';
            $content .='<div class="td td4 text-left" data-rel="clearafter">' . $name . '</div>';
            $content .='<div class="td td4 text-left" data-rel="clearafter">' . $email . '</div>';
            $content .='<div class="td td4 text-center" data-rel="clearafter">' . $bookedOn . '</div>';
            $content .='<div class="td td6">
                <div class="right">
                    <div class="pencil left"><a data-id="' . $key . '" href="#" data-action="addedititem" data-context="wrapAddEditTimeSheetForScheduler" title="Edit Time Sheet"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>'
                 . '<div class="delete left"><a class="deleteCourse" data-action="deleteitem" data-context="truncateTimeSheetForScheduler" href="#" data-id="' . $key . '" data-title="' . $timesheetname . '<br /><br /><strong>name</strong>: ' . $name . '<br /><strong>email</strong>: ' . $email . '<br /><strong>Booked on</strong>: ' . $bookedOn . '" data-alttitle="Are you sure you want to reset this Time Sheet" title="Delete Time Sheet"><i class="fa fa-trash" aria-hidden="true"></i></a></div>'
                    . '<div class="pencil left"><a data-id="' . $key . '" href="#" data-action="markonline" data-context="markOnlineTimeSheet" title="Online"><i class="fa fa-eye' . $isonline . '" aria-hidden="true"></i></a></div>
                     <div class="noFloat"></div>
                </div>
                <div class="noFloat"></div>
                </div>';
            $content .='</div>';
           return $content;
       }
}