<?php
if(!isset($_SESSION[session_id()]['adminID']))
{
	echo 'You are not an administrator'; exit ;
}
require_once PATH_TO_CLASS . 'AdminLogin.php';
require_once PATH_TO_CLASS . 'Pages.php';
require_once PATH_TO_CLASS . 'HTMLContent.php';
$checkAdmin=new AdminLogin();
$page=new Pages();
$htmlcontent=new HTMLContent();
$checkAdmin->logoutAdmin();

$isSF=false;
$isNoSearch=false;



$htmlTemplate=PATH_TO_HTML . 'page.htm';
$topHeader=TOPHEADER;

$section=array_key_exists('section', $_GET)?$_GET['section']:12;
$subsection=array_key_exists('subsection', $_GET)?$_GET['subsection']:1;
$recPage=$htmlcontent->getPage($section);
if($recPage!==false && sizeof($recPage)>0) $parentsection=$recPage->pid;
else $parentsection=0;

$menu=$page->wrapMainMenu($section);
$content='';
$curLink=array();
for($i=1; $i<=55; $i++) $curLink[$i]=$i==$section?' class="curLink"':'';
switch($_SESSION[session_id()]['adminROLES'])
{
    case 1:
    case 2:
    case 3:
    case 4:
        switch($section){
            default:
                $content='';
            break;
            case 9:    
                $content=$page->wrapAllAdminUsers($section);
            break;
            case 10:    
                $content=$page->wrapAllProducts($section);
            break;
            case 11:    
                $content=$page->wrapAllCustomers($section);
            break;
            case 12:    
                $content=$page->wrapAllOrders($section);
            break;
        }
    break;
    case 5:
        switch($section){
            default:
                $content='';
            break;
           
        }
    break;
    case 6:
        switch($section){
            default:
                $content='';
            break;
           
        }
    break;
    case 9:
        switch($section){
            default:
                $content='';
            break;
        }
    break;
    case 5:
    case 10:
        switch($section){
            default:
                $content='';
            break;
        }
    break;
        
}

require_once($topHeader);
require_once($htmlTemplate);
require_once(FOOTER);
?>