<?php
require_once 'DBconnect.php';

class Pages{
    
    var $table='pages';
    var $get;
    
    var $curTemplateClass='';
    
    var $arrHTMLRegularPages=array(
        4,
        5,
        6,
        7,
        13,
        14,
        15,
        17,
        18,
        19,
        20,
        21,
        22,
        23,
        24
    );
    
    function __construct() {
        $this->get=$this->getFromRealPathOEPages();
        $this->curTemplateClass=$this->prepareCurTemplateClass();
    }
    
    //Check for page (human readable links) is exists or not in the system, either like a regular page, either as a report, or filtered results
    
    function getFromRealPathOEPages(){
        $a=array();
        if(isset($_GET['realurl'])){
            if($_GET['realurl']=='') return false;
            else{
                $_GET['realurl']=preg_replace('/(.+)\?utm_.+/', '$1', $_GET['realurl']);
                $whatURL=explode('/', $_GET['realurl']);
                if(sizeof($whatURL)>0){
                    
                        $rec=$this->checkIsRealURLFromPages($whatURL[0]);
                        if($rec!==false){
                            $a['section']=$rec->id;
                            if(true===array_key_exists(1, $whatURL)){
                                $a[2]='id';
                                $a[3]=$whatURL[1];
                                if($a[1]==3){
                                    $a[2]='action';
                                    $a[3]=$whatURL[1];
                                    $a[4]='token';
                                    $a[5]=$whatURL[2];
                                }elseif($a[1]==2){
                                    if(true!==array_key_exists(2, $whatURL)){
                                        $a[2]='id';
                                        $a[3]=$whatURL[1];
                                    }
                                    if(true===array_key_exists(2, $whatURL)){
                                        $a[2]=$whatURL[1];
                                        $a[3]=$whatURL[2];
                                    }
                                    if(true===array_key_exists(3, $whatURL)){
                                        $a[4]='id';
                                        $a[5]=$whatURL[3];
                                    }
                                    if(true===array_key_exists(4, $whatURL)){
                                        $a[6]='title';
                                        $a[7]=$whatURL[4];
                                    }
                                }
                                elseif($a[1]==4){
                                    $a[2]=$whatURL[1];
                                    $a[3]=$whatURL[2];
                                }
                                elseif($a[1]==5){
                                    $a[2]='vids';
                                    $a[3]=$whatURL[1];
                                    if(true===array_key_exists(2, $whatURL)){
                                        $a[4]='cat';
                                        $a[5]=$whatURL[2];
                                    }
                                }elseif($a[1]==23){
                                    $a[2]='id';
                                    $a[3]=$whatURL[1];
                                }
                            }
                        }else{
                            if($whatURL[0]=='logout'){
                                $a[0]='logout';
                                $a[1]=1;
                            }
                        }
                }
            }
        }else{
            $a['section']=1;
        }
        return $a;
    }
    
    protected function checkIsRealURLFromPages($what){
        $rec=array();
        $table=PRETABLE . 'pages';
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id FROM ' . $table . ' WHERE realpath LIKE :realpath or id=:id LIMIT 0,1';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':realpath'=>$what, ':id'=>(int)$what));
        $rec=$sth->fetch(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getAllActiveMenuItems(){
        $rec1=array();
        $rec=$this->getAllPagesForMenu();
        if($rec!==false && sizeof($rec)>0){
            foreach($rec as $val){
                
            }
        }
    }
    
    protected function getAllPagesForBottomMenu($pid=0){
        $addToMySQLQuery=' AND id IN(1,4,5,13,10,11) ';
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, pid, name, navname, inmenu, realpath FROM ' . $table . ' WHERE inmenu=' . (int)1 . ' ' . $addToMySQLQuery . 'ORDER BY sorting ASC';
        $sth=$dbh->prepare($sql);
        $sth->execute();
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getAllPagesForFEMenu($pid=0){
        $addToMySQLQuery='';
        $addToMySQLQuery .='AND pid=' . (int)$pid . ' ';
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, pid, name, navname, inmenu, realpath FROM ' . $table . ' WHERE inmenu=' . (int)1 . ' ' . $addToMySQLQuery . 'ORDER BY sorting ASC';
        $sth=$dbh->prepare($sql);
        $sth->execute();
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    protected function getAllPagesForMenu($pid=0){
        $rec=array();
        $table=PRETABLE . $this->table;
        $dbh=new DBconnect();
        $dbh=$dbh->sql;
        $sql='SELECT id, pid, name, inmenu, realpath, sorting FROM ' . $table . ' WHERE id>:id AND hideinmenu=:hideinmenu ORDER BY sorting ASC';
        $sth=$dbh->prepare($sql);
        $sth->execute(array(':id'=>(int)0, ':hideinmenu'=>(int)0));
        $rec=$sth->fetchAll(PDO::FETCH_OBJ);
        return $rec;
    }
    
    function updatePageP(){
        return $this->updatePage();
    }
    
    protected function updatePage(){
		$table=PRETABLE . $this->table;
		$rec=array();
		$dbh=new DBconnect();
		$dbh=$dbh->sql;
		$a=array();
		foreach ($_POST as $key => $val) if(preg_match('/^aEOE_/', $key)==1 && $key!='aEOE_id') $a[preg_replace('/^aEOE_/', '', $key)]=$val;
                //$a['comedianurl']=$this->generateComedianUrl($_POST['aEOE_firstname'], $_POST['aEOE_lastname']);
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
    
    
    //Wrap menu at the top of the page, main links
    function wrapMainMenu($section){
        $rec1=array();
        $rec=$this->getAllPagesForMenu();
        if($rec!==false && sizeof($rec)>0){
            foreach($rec as $val){
                $rec1[$val->pid][$val->id]=$val;
            }
        }
        $content='';
        $content .=''
            . '<nav id="menu" class="menu">';
        $content .=$this->wrapMainRealMenu($rec1, 0, $section);
        $content .='</nav>';
        return $content;
    }
    
    function wrapMainRealMenu($rec, $level=0, $section, $isFE=false){
        $content=$current='';
        $content .='<ul class="relative">';
        if($rec!==false && sizeof($rec)>0){
            foreach($rec as $key => $val){
                foreach($val as $key1 => $val1){
                    if($key==$level){
                        if(true===array_key_exists($key1, $rec)){
                            if(isset($_SESSION[session_id()]['adminROLES'])){
                                if($_SESSION[session_id()]['adminROLES']==2){
                                    if($key1==4) break;
                                }elseif($_SESSION[session_id()]['adminROLES']==5){
                                    if($key1!=12) continue;
                                }elseif($_SESSION[session_id()]['adminROLES']==6){
                                    if($key1!=9) continue;
                                }elseif($_SESSION[session_id()]['adminROLES']==9){
                                    if($key1!=4) break;
                                }elseif($_SESSION[session_id()]['adminROLES']==10){
                                    if($key1!=4) break;
                                }
                            }
                            $name=$isFE===true?$val1->navname:$val1->name;
                            $current=true===array_key_exists($section, $rec[$key1])?' current hoveratag':'';
                            $content .='<li data-pid="' . $key1 . '" class="nolink' . $current . '">' . $name;
                            $content .=$this->wrapMainRealMenu($rec, $key1, $section);
                            $content .='</li>';
                        }else{
                            $content .=$this->wrapMainMenuItem($val1, $section, $isFE);
                        }
                    }
                }
            }
        }    
        $content .='</ul>';
        return $content;
    }
    
    function wrapMainMenuItem($rec, $section, $isFE=false){
        $content=$current='';
        if(isset($_SESSION[session_id()]['adminROLES'])){
            if($_SESSION[session_id()]['adminROLES']==2){
                if($rec->id==34) return '';
            }elseif($_SESSION[session_id()]['adminROLES']==9){
                if($rec->id!=4) return '';
            }elseif($_SESSION[session_id()]['adminROLES']==10){
                if($rec->id!=4) return '';
            }elseif($_SESSION[session_id()]['adminROLES']==3){
                if($rec->id==4 || $rec->id==34) return '';
            }elseif($_SESSION[session_id()]['adminROLES']==4){
                if($rec->id==4 || $rec->id==34) return '';
            }elseif($_SESSION[session_id()]['adminROLES']==5){
                if($rec->id!=12) return '';
            }elseif($_SESSION[session_id()]['adminROLES']==6){
                if($rec->id!=9) return '';
            }
        }
        $current=$section==$rec->id?' current':'';
        $name=$isFE===true?$rec->navname:$rec->name;
        $content .='<li class="relative' . $current . '"><a class="relative" href="?section=' . $rec->id . '">' . $name . '</a></li>';
        return $content;
    }
    
    function wrapSiteTitle(){
        $_GET=$this->get;
        $section=isset($_GET['section'])?$_GET['section']:1;
        $content='';
        switch($section){
            case 1: default:
                $content .='IPV Education';
            break;
        }
        return $content;
    }
    
    function prepareCurTemplateClass(){
        $class='';
        $section=isset($_GET['section'])?$_GET['section']:1;
        switch($section){
        case 1: default:
            
        break;
        }
        return $class;
    }
    
    //Wrap main page content
    function wrapMainContent(){
        require_once 'HTMLContent.php';
        $htmlcontent=new HTMLContent(); 
        $_GET=$this->get;
        
        $section=isset($_GET['section'])?$_GET['section']:1;
        $rec1=$htmlcontent->getPage($section);
        
        $content='';
        switch($section){
            default:
                if(true===in_array($section, $this->arrHTMLRegularPages)){
                    $rec1=$htmlcontent->getPage($section);
                    if($rec1!==false && sizeof($rec1)>0){
                        $pageTitle=$rec1->name;
                    }
                    $row=$this->wrapHTMLContentFE($section);
                    ob_start(); // start output buffer
                    include (PATH_TO_HTML . 'wrapHTMLPage.htm');
                    $content .= ob_get_contents(); // get contents of buffer
                    ob_end_clean();
                    break;
                }
            break;
            case 1:
                $content .=$this->wrapHomePage();
            break;
            case 8:
                if($rec1->online==1){
                    $content .=$this->wrapResourcesFE($section);
                }
            break;
            case 10:
                if($rec1->online==1){
                    $content .=$this->wrapPublicationsFE($section);
                }
            break;
            case 11:
                if($rec1->online==1){
                    $content .=$this->wrapContactsFE($section);
                }
            break;
        }
        return $content;
    }
    
    
    
    private function wrapHomePage(){
        $content='';
        $content .='<div class="container">
            <div class="row paddTopBottom40">
                <div class="col-md-12">
                    <div class="col-md-6 col-sm-6">' . $this->wrapDidYouKnowHome() . '</div>
                    <div class="col-md-1 hidden-md col-lg-1"></div>
                    <div class="col-md-6 col-sm-6 col-lg-5">' . $this->wrapVideoHome() . '</div>
                </div>
            </div>
        </div>';
        /*
        $content .='<div id="wrapAboutUs" class="bg-black text-white"><a class="anchor" id="about-us"></a>
            <div class="container"><div class="col-md-12"><div class="row">' . $this->wrapHTMLContentFE(3) . '</div></div></div>
        </div>';
        */
            $content .='<div class="bg-info text-center"><div class="container">' . $this->wrapQuoteHome() . '</div></div>';
        
        if(isset($_SESSION['deviceViewAlreadyDetected']) && $_SESSION['deviceViewAlreadyDetected'][0]==2){
            
        }else{
            //$content .=$this->wrapRecentPublications();
        }
        return $content;
    }
    
    function wrapEventDetailsFE($section, $id){
        require_once 'EventsFE.php';
        $content=new EventsFE();
        return $content->wrapEventDetailsFE($section, $id);
    }
    
    function wrapAllCommitteeBoardMembers($section){
        require_once 'CommitteeBoardMembers.php';
        $content=new CommitteeBoardMembers();
        return $content->wrapAllCommitteeBoardMembers($section);
    }
    
    function wrapAllEvents($section){
        require_once 'Events.php';
        $content=new Events();
        return $content->wrapAllEvents($section);
    }
    
    function wrapAllDonations($section){
        require_once 'Donations.php';
        $content=new Donations();
        return $content->wrapAllDonations($section);
    }
    
    function wrapAllOrders($section){
        require_once 'Orders.php';
        $content=new Orders();
        return $content->wrapAllOrders($section);
    }
    
    function wrapAllProducts($section){
        require_once 'Products.php';
        $content=new Products();
        return $content->wrapAllProducts($section);
    }
    
    function wrapAllCustomers($section){
        require_once 'Customers.php';
        $content=new Customers();
        return $content->wrapAllCustomers($section);
    }
    
    function wrapAllTrainingUpdates($section){
        require_once 'TrainingUpdates.php';
        $content=new TrainingUpdates();
        return $content->wrapAllTrainingUpdates($section);
    }
    
    function wrapAllBanners($section){
        require_once 'Banners.php';
        $content=new Banners();
        return $content->wrapAllBanners($section);
    }
    
    function wrapAllMarketingBoxes($section){
        require_once 'MarketingBoxes.php';
        $content=new MarketingBoxes();
        return $content->wrapAllMarketingBoxes($section);
    }
    
    function wrapAdminDashboard(){
        require_once 'Dashboards.php';
        $content=new Dashboards();
        return $content->wrapAdminDashboard();
    }
    
    function wrapHTMLContent($section){
        require_once 'HTMLContent.php';
        $content=new HTMLContent();
        return $content->wrapBasicEditForms($section);
    }
    
    function wrapAllAdminUsers($section){
        require_once 'Users.php';
        $content=new Users();
        return $content->wrapAllAdminUsers($section);
    }
    
    function wrapAllGalleries($section){
        require_once 'Galleries.php';
        $content=new Galleries();
        return $content->wrapAllGalleries($section);
    }
    
    function wrapAllQuotes($section){
        require_once 'Quotes.php';
        $content=new Quotes();
        return $content->wrapAllQuotes($section);
    }
    
    function wrapDidYouKnowHome(){
        require_once 'QuotesFE.php';
        $content=new QuotesFE();
        return $content->wrapQuoteHome(12);
    }
    
    function wrapQuoteHome(){
        require_once 'QuotesFE.php';
        $content=new QuotesFE();
        return $content->wrapQuoteHome(2);
    }
    
    function wrapVideoHome(){
        require_once 'HTMLContentFE.php';
        $content=new HTMLContentFE();
        return $content->wrapVideoHome();
    }
    
    function wrapHTMLContentFE($section){
        require_once 'HTMLContentFE.php';
        $content=new HTMLContentFE();
        return $content->wrapHTMLContentFE($section);
    }
    
    function wrapContactsFE($section){
        require_once 'ContactsFE.php';
        $content=new ContactsFE();
        return $content->wrapContactsFE($section);
    }
    
    function wrapPublicationsFE($section){
        require_once 'PublicationsFE.php';
        $content=new PublicationsFE();
        return $content->wrapPublicationsFE($section);
    }
    
    function wrapRecentPublications(){
        require_once 'PublicationsFE.php';
        $content=new PublicationsFE();
        return $content->wrapRecentPublications();
    }
    
    function wrapResourcesFE($section){
        require_once 'ResourcesFE.php';
        $content=new ResourcesFE();
        return $content->wrapResourcesFE($section);
    }
}
