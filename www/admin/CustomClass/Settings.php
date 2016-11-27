<?php
require_once('DBconnect.php');
//require_once('Mail.php');
//require_once('Mail/mime.php');
class Settings extends DBconnect 
{
    
    var $salt='FM2014';
    
    var $arrYesNo=array(0=>'Not selected', 1=>'Yes', 2=>'No');
    var $arrYesNo1=array(2=>'No', 1=>'Yes');
    
    function generateArrNumbers($s=1, $e=10){
        $rec=array();
        $rec[0]='Not selected';
        for($i=$s; $i<=$e; $i++){
            $rec[$i]=$i;
        }
        return $rec;
    }
    
    function getAllFromAdmin($id){
        $rec=array();
        $table='be_admin';
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, sponsorcase0, sponsorcase1, sponsorcase2, sponsorcase3, sponsorcase4, sponsorcase5 FROM ' . $table . ' WHERE id=:id LIMIT 0,1';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)$id));
        $rec=$sth->fetch(PDO::FETCH_OBJ);
        return $rec;
    }
    
    function getAllTemplates(){
        $rec=array();
        $table=PRETABLEHELPER . 'templates';
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, name, templateimage FROM ' . $table . ' WHERE id>:id ORDER BY id';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)0));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    function getAllLogos(){
        $rec=array();
        $table=PRETABLEHELPER . 'logos';
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, name FROM ' . $table . ' WHERE id>:id ORDER BY id';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)0));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    function getLogo($id){
        $rec=array();
        $table=PRETABLEHELPER . 'logos';
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, name, image FROM ' . $table . ' WHERE id=:id ORDER BY id';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)$id));
        $rec=$sth->fetch(PDO::FETCH_OBJ);
        return $rec;
    }
    
    function getAllCountries($id=false){
        $rec=array();
        $table='regions';
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, country AS name FROM ' . $table . ' WHERE id>:id ORDER BY sorting DESC, country ASC';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)0));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    function getProvinceNameByID($id){
        $rec=array();
        $table=PRETABLEHELPER . 'provinces';
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, name FROM ' . $table . ' WHERE id=:id LIMIT 0,1';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)$id));
        $rec=$sth->fetch(PDO::FETCH_OBJ);
        if($rec!==false && sizeof($rec)>0){
            return $rec->name;
        }else return 'N/A';
        return $rec;
    }
    
    function getAllProvinces($region=0, $id=false){
        $rec=array();
        $table=PRETABLEHELPER . 'provinces';
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, name FROM ' . $table . ' WHERE id>:id ORDER BY sorting ASC';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)-1));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    function getAllRegions(){
        $rec=array();
        $table='continents';
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT code, name FROM ' . $table . ' ORDER BY name ASC';
        $sth=$dbh->prepare($sql);
        $sth->execute();
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    function wrapProvincesSB($id, $name='province'){
        $json=array();
        $rec=$this->getAllProvinces($id);
        $content='';
        $selected='';
        $content .='<select class="mf-select" name="aEOE_' . $name . '" size="1">';
        $content .='<option value="0"' . $selected . '>-- Select one --</option>';
            foreach($rec as $val){
                $content .='<option value="' . $val->id . '"' . $selected . '>' . $val->name . '</option>';
            }
            $content .='</select>';
        $json['success']=1;
        $json['content']=$content;
        return $json;
    }
    
    function wrapCountriesSB1($id, $name='country'){
        $json=array();
        $rec=$this->getAllCountries($id);
        $content='';
        $selected='';
        $content .='<select class="mf-select" name="aEOE_' . $name . '" size="1">';
        $content .='<option value="0"' . $selected . '></option>';
            foreach($rec as $val){
                $content .='<option value="' . $val->id . '"' . $selected . '>' . $val->country . '</option>';
            }
            $content .='</select>';
        $json['success']=1;
        $json['content']=$content;
        return $json;
    }
    
    function wrapCountriesSB($id=false, $what, $name='country'){
        $rec=$this->getAllCountries($id);
        $content='';
        $selected=$what==0?' selected="selected"':'';
        $content .='<select class="mf-select" name="aEOE_' . $name . '" size="1">';
        $content .='<option value="0"' . $selected . '></option>';
            foreach($rec as $val){
                $selected=$what==$val->id?' selected="selected"':'';
                $content .='<option value="' . $val->id . '"' . $selected . '>' . $val->country . '</option>';
            }
            $content .='</select>';
        return $content;
    }
    
    function wrapRegionsSB($what, $name='region'){
        $rec=$this->getAllRegions();
        $content='';
        $selected=$what==0?' selected="selected"':'';
        $content .='<select class="mf-select" name="aEOE_' . $name . '" size="1">';
        $content .='<option value="0"' . $selected . '></option>';
            foreach($rec as $val){
                $selected=$what==$val->code?' selected="selected"':'';
                $content .='<option value="' . $val->code . '"' . $selected . '>' . $val->name . '</option>';
            }
            $content .='<option value="840">USA</option>';
            $content .='</select>';
        return $content;
    }
    
    function wrapSB1($what=0, $name='', $arr=array(), $after='', $extra=''){
        $content='';
        $content .='<select class="mf-select" name="aEOE_' . $name . '" size="1"' . $extra . '>';
if($name=='toevent' || $name=='fromevent'){
            $default='Not required';
        }else{
            $default='-- Select one --';
        }
        $content .='<option value="0">' . $default . '</option>';
        foreach($arr as $val){
            if($name=='toevent' || $name=='fromevent'){
                $val->id=$val->timeid . $val->eventid . $val->etype;
                $val->name=$val->etime . ' - ' . $val->location;
            }
            $selected=$what==$val->id?' selected="selected"':'';
            $content .='<option value="' . $val->id . '"' . $selected . '>' . $val->name . $after . '</option>';
        }
        $content .='</select>';
        return $content;
    }
    
    function wrapSB($what=0, $name='', $arr=array()){
        $content='';
        $content .='<select class="mf-select" name="aEOE_' . $name . '" size="1">';
        foreach($arr as $key => $val){
            $selected=$what==$key?' selected="selected"':'';
            $content .='<option value="' . $key . '"' . $selected . '>' . $val . '</option>';
        }
        $content .='</select>';
        return $content;
    }
    
    function wrapRB($what=0, $name='', $arr=array(), $class=''){
        $content='';
        $content .='<div class="mf-radio">';
        foreach($arr as $key => $val){
            $checked=$what==$key?$checked=' checked="checked"':'';
            $class1=preg_replace('/\w+(\d+)/', '$1', $class)==$key?' ' . $class:'';
            $content .='<div class="left' . $class1 . '" data-value="' . $key . '"><span class="relative left"><input type="radio" value="' . $key . '" name="aEOE_' . $name . '" class="mf-radio"' . $checked . ' /></span><span class="relative right">' . $val . '</span><div class="noFloat"></div></div>';
        }
        $content .='</div>';
        return $content;
    }
    
    function wrapSponsorhsipCB($rec, $arr=array()){
        $sum=0;
        $rec1=$this->getAllFromAdmin($_SESSION['loggedInAdmin']->id);
        $content='';
        foreach($arr as $key => $val){
            $sum +=$rec->{'so' . $key}==1?($key==5?$rec->{'sponsorcase' . $key}:$rec1->{'sponsorcase' . $key}):0;
            $rec->{'so' . $key}=$rec->{'so' . $key}==1?' checked="checked"':'';
            $content .='<div class="fRow fRow1"><input type="checkbox" value="1" name="aEOE_so' . $key . '"' . $rec->{'so' . $key} . ' class="mf-checkbox" data-amount="' . $rec1->{'sponsorcase' . $key} . '" /><span class="mf-checkbox">' . $val . '</span><span class="relative right">' . CURRENCYSIGN;
            $content .=$key==5?'<input type="text" name="aEOE_sponsorcase' . $key . '" class="mf-input short" value="' . $rec->{'sponsorcase' . $key} . '" />':number_format($rec1->{'sponsorcase' . $key}, 0);
            $content .='</span><div class="noFloat"></div></div>';
        }
        $content .='<div class="fRow fRow1 paddTop"><span class="relative left">Sponsor Amount</span><span id="sponsorshipTotal" class="relative right">' . CURRENCYSIGN . number_format($sum, 0) . '</span>
        <div class="noFloat"></div></div>';
        return $content;
    }
    
    function hueSaturateImage($path, $img, $a=false){
        require_once('Images.php');
        $thumbnail = new Images();
        //return $path . $img;
        if($a===false){
            $rec=$thumbnail->createHueThumbnail($path . $img, PATH_TO_TEMP_PICS, true);
        }else{
            $rec=$thumbnail->createHueThumbnail('../' . $path . $img, '../' . PATH_TO_TEMP_PICS, false);
        }
        return $rec;
    }
    
    function getThumbnail1($path, $img, $t = 'large', $a = false) {        
        $content = '';
        require_once('Images.php');
        $thumbnail = new Images();
        if(preg_match('/youtube\.com/', $img)==1){                    
                    $pF=$_SERVER['DOCUMENT_ROOT'] . '/' . PATH_TO_PHOTOS;                    
                    $imgname=md5('yt' . $img) . '.jpg';
                    $origThumb=$pF . $imgname;
                    if($thumbnail->url_exists($path . $imgname)==0){
                        file_put_contents($origThumb,file_get_contents($img));
                    }
                    $img=$imgname;
                }
        switch ($t) {            
            case 'qqFileUploader_vertical':
                $rec=array();
                if($a===false){
                    $rec=$thumbnail->createThumbnail($path . $img, EXTRALARGE, EXTRALARGE, '../' . PATH_TO_TEMP_PICS, false, true);
                }else{
                    $rec=$thumbnail->createThumbnail('../' . $path . $img, EXTRALARGE, EXTRALARGE, '../' . PATH_TO_TEMP_PICS, false, true);
                }
                return $rec;
                break;
                
            case 'qqFileUploader_vertical1':
                $rec=array();
                if($a===false){
                    $rec=$thumbnail->createThumbnail($path . $img, PHOTOLARGE, PHOTOLARGE, '../' . PATH_TO_TEMP_PICS, false, true);
                }else{
                    $rec=$thumbnail->createThumbnail('../' . $path . $img, PHOTOLARGE, PHOTOLARGE, '../' . PATH_TO_TEMP_PICS, false, true);
                }
                return $rec;
                break;    
                
            case 'qqFileUploader_gallery':
                $rec=array();
                if($a===false){
                    $rec=$thumbnail->createThumbnail($path . $img, EXTRALARGE, 0, '../../' . PATH_TO_GALLERIES . $_GET['year'] . '/', false, true);
                }else{
                    $rec=$thumbnail->createThumbnail('../' . $path . $img, EXTRALARGE, 0, '../../../' . PATH_TO_GALLERIES . $_GET['year'] . '/', false, true);
                }
                return $rec;
                break;
                
            case 'qqFileUploader_gallery1':    
                $rec=array();
                if($a===false){
                    $rec=$thumbnail->createThumbnail($path . $img, THUMBNAIL2a, 0, '../../' . PATH_TO_GALLERIES . $_GET['year'] . '/thumbnails/', THUMBNAIL2a . ',' . THUMBNAIL2a_H, true);
                }else{
                    $rec=$thumbnail->createThumbnail('../' . $path . $img, THUMBNAIL2a, 0, '../../../' . PATH_TO_GALLERIES . $_GET['year'] . '/thumbnails/', THUMBNAIL2a . ',' . THUMBNAIL2a_H, true);
                }
                return $rec;
                break;             
                
            
            case 'HTMLContent_vertical':    
            case 'Banners_vertical':  
            case 'qqFileUploader_vertical1':
                $rec=array();
                if($a===false){
                    $rec=$thumbnail->createThumbnail($path . $img, EXTRALARGE, 0, PATH_TO_TEMP_PICS, false);
                }else{
                    $rec=$thumbnail->createThumbnail('../' . $path . $img, EXTRALARGE, 0, '../' . PATH_TO_TEMP_PICS, false, true);
                }
                return $rec;
                break;
            
            case 'Products_thumbnail':    
                $rec=array();
                if($a===false){
                    $rec=$thumbnail->createThumbnail($path . $img, PHOTOTHUMB1, 0, PATH_TO_TEMP_PICS);
                }else{
                    $rec=$thumbnail->createThumbnail('../' . $path . $img, PHOTOTHUMB1, 0, '../' . PATH_TO_TEMP_PICS, false, true);
                }
                return $rec;
                break;   
                
            case 'ProductsFE_thumbnail':    
                $rec=array();
                if($a===false){
                    $rec=$thumbnail->createThumbnail($path . $img, PHOTOTHUMB1, 0, PATH_TO_TEMP_PICS);
                }else{
                    $rec=$thumbnail->createThumbnail('../' . $path . $img, PHOTOTHUMB1, 0, '../' . PATH_TO_TEMP_PICS);
                }
                return $rec;
                break;       
            
            default:
                $rec=array();
                if($a===false){
                    $rec=$thumbnail->createThumbnail($path . $img, EXTRALARGE, EXTRALARGE, PATH_TO_TEMP_PICS, false);
                }else{
                    $rec=$thumbnail->createThumbnail('../' . $path . $img, EXTRALARGE, EXTRALARGE, '../' . PATH_TO_TEMP_PICS, false, true);
                }
                return $rec;
                break;
        }
        return $a === false ? $content : $content;
    }
    
    function checkHash($password, $hash){
        require_once 'Bcrypt.php';
        $bcrypt = new Bcrypt(5);
        $isGood = $bcrypt->verify($password, $hash);
        return $isGood;
    }
        
       function generateSalt($string){
           require_once 'Bcrypt.php';
           $bcrypt = new Bcrypt(5);
           $hash = $bcrypt->hash($string);
           return $hash;
        }
        
        function prepareRealLink($string){
            $string=trim($string);
            $string = preg_replace( '/[«»""!?,.!@£$%^&*{};:()]+/', '', $string );
            $string = strtolower($string);
            $slug=preg_replace('/[^A-Za-z0-9-]+/', '', $string);
            return $slug;
        }
        
        function checkIsExistsPrivateURL($string, $table){
            $rec=array();
            $table=PRETABLE . $table;
            $dbh=new DBconnect();
            $dbh=$dbh->sql;
            $sql='SELECT id FROM ' . $table . ' WHERE id>:id AND realpath LIKE :realpath';
            $sth=$dbh->prepare($sql);
            $sth->execute(array(':id'=>(int)0, ':realpath'=>$string));
            $rec=$sth->fetch(PDO::FETCH_OBJ);
            if($rec!==false && sizeof($rec)>0){
                return true;
            }else{
                return false;
            }
        }
        
        function generatePermalinkUrl($string, $table='events'){
            $string=dirname(dirname($this->getCurrPathDir())) . '/' . $this->prepareRealLink($string);
            $counter='';
            do{                
                $string1=$string . $counter;
                if($counter=='') $counter=0;
                $counter++;
            }while($this->checkIsExistsPrivateURL($string1, $table)!==false);
            return $string1;
        }
        
        function prepareSearchLink($string){
            $string = preg_replace( '/[«»""!?,.!@£$%^&*{};:()]+/', '', $string );
            $string = strtolower($string);
            $slug=preg_replace('/[^A-Za-z0-9-\s]+/', '-', $string);
            $slug=preg_replace('/\s/', '+', $slug);
            return $slug;
        }
        
        function getFromComedianURL($string){
            $string=preg_replace('/.*\/(\.?\w+\.?\w+)$/', '$1', $string);
            return $string;
        }
        
        function encodeURIComponent($str) {         
	    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
	    return strtr(rawurlencode($str), $revert);
	}
        
        function getCurrPathDir(){
            $string=dirname("http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI']);
            return $string;
	}
        
        function array_to_object($array) {
            $obj = new stdClass;
            foreach($array as $k => $v) {
               if(is_array($v)) {
                  $obj->{$k} = array_to_object($v); //RECURSION
               } else {
                  $obj->{$k} = $v;
               }
            }
            return $obj;
          } 
          
        function detectDevice(){
            $device='';
            $iPod    = stripos($_SERVER['HTTP_USER_AGENT'],'iPod');
            $iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],'iPhone');
            $iPad    = stripos($_SERVER['HTTP_USER_AGENT'],'iPad');
            $Android = stripos($_SERVER['HTTP_USER_AGENT'],'Android');
            $webOS   = stripos($_SERVER['HTTP_USER_AGENT'],'webOS');

            //do something with this information
            if( $iPod || $iPhone ){
                $device='iPhone';
            }else if($iPad){
                $device='iPad';
            }else if($Android){
                $device='Android';
            }else if($webOS){
                $device='webOS';
            }
            return $device;
        }
        
        function removePhotosFromServer($rec, $isAjax=true){
            $prefix=$isAjax===true?'../':'';
            $return=true;
            if(sizeof($rec)>0) foreach($rec as $val) if(is_file($prefix . $val)) unlink($prefix . $val); else $return=false;
            return $return;
        }
        
	private function checkForActions(){
		
	}
        
        function unzipZipped($file, $id){
            $pF=$_SERVER['DOCUMENT_ROOT'] . '/' . PATH_TO_PHOTOS;
            $zip = new ZipArchive;
            $res = $zip->open($pF . $file);
            if ($res === TRUE) {
              $zip->extractTo($pF . $id);
              $zip->close();
              return true;
            }else{
              return false;
            }
        }
        
        static function filetime_callback($a, $b)
{
  if (filemtime($a) === filemtime($b)) return 0;
  return filemtime($a) < filemtime($b) ? -1 : 1; 
}

function hex2rgb( $colour ) {
        if ( $colour[0] == '#' ) {
                $colour = substr( $colour, 1 );
        }
        if ( strlen( $colour ) == 6 ) {
                list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
        } elseif ( strlen( $colour ) == 3 ) {
                list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
        } else {
                return false;
        }
        $r = hexdec( $r );
        $g = hexdec( $g );
        $b = hexdec( $b );
        return array( 'red' => $r, 'green' => $g, 'blue' => $b );
}


function sec2hms ($sec, $padHours = false) 
        {

          // start with a blank string
          $hms = "";

          // do the hours first: there are 3600 seconds in an hour, so if we divide
          // the total number of seconds by 3600 and throw away the remainder, we're
          // left with the number of hours in those seconds
          $hours = intval(intval($sec) / 3600); 

          // add hours to $hms (with a leading 0 if asked for)
          $hms .= ($padHours) 
                ? str_pad($hours, 2, "0", STR_PAD_LEFT)
                : $hours;
          $hms .='h ';

          // dividing the total seconds by 60 will give us the number of minutes
          // in total, but we're interested in *minutes past the hour* and to get
          // this, we have to divide by 60 again and then use the remainder
          $minutes = intval(($sec / 60) % 60); 

          // add minutes to $hms (with a leading 0 if needed)
          $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT);
          $hms .='min ';

          // seconds past the minute are found by dividing the total number of seconds
          // by 60 and using the remainder
          $seconds = intval($sec % 60); 

          // add seconds to $hms (with a leading 0 if needed)
          //$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
          //$hms .='s ';

          // done!
          return $hms;

        }
        
    function getJSONPrivateSections(){
        $rec=array();
        if(isset($_SESSION['whatUserOnDetails']) && $this->checkIsUserLogedAndToken($_SESSION['whatUserOnDetails']->id)===true){
            switch($_SESSION['whatUserOnDetails']->roles){
                case 3:
                    require_once 'CustomersFE.php';
                    $customer=new CustomersFE();
                    $rec=$customer->getJSONCustomers();
                break;
                case 2:
                    require_once 'ProductsFE.php';
                    $product=new ProductsFE();
                    $rec=$product->getJSONProducts();
                break;
            }
        }else{
            $rec['message'] .='Nemate ovlaštenja za ovu sekciju.';
        }
        return $rec;
    }    
          
        
    function prepareBasicValues($rec){
        $rec1=new stdClass();
        if($rec!==false && sizeof($rec)>0){
            foreach($rec as $val){
                $rec1->{$val->flag}=$val->name;
            }
        }
        return $rec1;
    }

   
    
    function sanitizeEntirePost($a){
        $rec1=array();
        if($a!==false && sizeof($a)>0){
            foreach($a as $key => $val){
                $matchKey='/^aEOE_/';
                if(preg_match($matchKey, $key)==1 && $key!='aEOE_id'){
                    $newKey=preg_replace($matchKey, '', $key);
                    if(is_array($val) && sizeof($val)>0){
                       foreach($val as $key1 => $val1){
                           $rec1[$newKey][$key1]=filter_var($val1, FILTER_SANITIZE_STRING);
                       } 
                    }else{
                        $rec1[$newKey]=filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING);
                    }
                }
            }
        }
        return $rec1;
    }
    
    function _ip(){
        if(preg_match('/^([d]{1,3}).([d]{1,3}).([d]{1,3}).([d]{1,3})$/', getenv('HTTP_X_FORWARDED_FOR')))
        {
            return getenv('HTTP_X_FORWARDED_FOR');
        }
        return getenv('REMOTE_ADDR');
    }
    
    function checkIsUserLogedAndToken($id){
            if(isset($_SESSION['whatUserOn']) && $_SESSION['whatUserOn']['id'][$id]==md5(session_id() . $id . $_SERVER['SERVER_NAME'])){
                return true;
            }else return false;
        }
}