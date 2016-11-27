<?php
require_once('config.php');

header('Content-Type: application/json');
if(isset($_POST['context'])){
	switch($_POST['context']){
            
            default:
            $content=array();
            $content['success']=3;
            $content['message']='<h4>Ova komanda nemože biti izvršena.</h4><div class="panel panel-warning">
                <div class="panel-heading">
                    <div class="panel-title">
                        ' . $_POST['context'] . '
                    </div>
                </div>
            </div><p>Provjerite da li je procedura za izvršenje ove akcije isprogramirana.</p>';
            break;

            case 'checkAdminLogin':
            require_once PATH_TO_CLASS1 . 'AdminLogin.php';
            $admin=new AdminLogin();
            $password=trim($_POST['adminPassword']);
            $content=array();
            if($admin->checkIsPost($password)===true){
                    $success=1;
                    $message='';
            }else{
                    $success=0;
                    $message='<p>Sorry, please try again!</p>';
            }
            $content['redirect']=DOCUMENT_ROOT . '/admin'; 
            $content['success']=$success;
            $content['message']=$message;
            break;
            
            case 'wrapAddEditAdminUser':
                if(!isset($_SESSION[session_id()]['adminID']))
                {
                        echo 'You are not Administrator'; exit();
                }     
		require_once(PATH_TO_CLASS1 . 'Users.php');
		$cbm=new Users();
                $id=filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
                $content=$cbm->wrapAddEditAdminUser($id);
            break;
            
            case 'addEditAdminUser':
                if(!isset($_SESSION[session_id()]['adminID']))
                {
                        echo 'You are not Administrator'; exit();
                }      
		require_once(PATH_TO_CLASS1 . 'Users.php');
		$cbm=new Users();
                $content=$cbm->addEditAdminUser();
            break;
            
            case 'deleteAdminUser':
                if(!isset($_SESSION[session_id()]['adminID']))
                {
                        echo 'You are not Administrator'; exit();
                }     
		require_once(PATH_TO_CLASS1 . 'Users.php');
		$cbm=new Users();
                $content=array();
                $id=filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
		if($cbm->deleteAdminUser($id)!==false) $content['success']=1;
                else $content['success']=0;
            break;
            
            case 'wrapAddEditBiography':
                if(!isset($_SESSION[session_id()]['adminID']))
                {
                        echo 'You are not Administrator'; exit();
                }     
		require_once(PATH_TO_CLASS1 . 'Biographies.php');
		$cbm=new Biographies();
                $id=filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                $content=$cbm->wrapAddEditBiography($id);
            break;
            
            case 'addEditBiography':
                if(!isset($_SESSION[session_id()]['adminID']))
                {
                        echo 'You are not Administrator'; exit();
                }      
		require_once(PATH_TO_CLASS1 . 'Biographies.php');
		$cbm=new Biographies();
                $content=$cbm->addEditBiography();
            break;
            
            case 'deleteBiography':
                if(!isset($_SESSION[session_id()]['adminID']))
                {
                        echo 'You are not Administrator'; exit();
                }     
		require_once(PATH_TO_CLASS1 . 'Biographies.php');
		$cbm=new Biographies();
                $content=array();
                $id=filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
		if($cbm->deleteBiography($id)!==false) $content['success']=1;
                else $content['success']=0;
            break;
            
            case 'wrapAddEditProduct':
                if(!isset($_SESSION[session_id()]['adminID']))
                {
                        echo 'You are not Administrator'; exit();
                }     
		require_once(PATH_TO_CLASS1 . 'Products.php');
		$cbm=new Products();
                $id=filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                $content=$cbm->wrapAddEditProduct($id);
            break;
            
            case 'addEditProduct':
                if(!isset($_SESSION[session_id()]['adminID']))
                {
                        echo 'You are not Administrator'; exit();
                }      
		require_once(PATH_TO_CLASS1 . 'Products.php');
		$cbm=new Products();
                $content=$cbm->addEditProduct();
            break;
            
            case 'deleteProduct':
                if(!isset($_SESSION[session_id()]['adminID']))
                {
                        echo 'You are not Administrator'; exit();
                }     
		require_once(PATH_TO_CLASS1 . 'Products.php');
		$cbm=new Products();
                $content=array();
                $id=filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
		if($cbm->deleteProduct($id)!==false) $content['success']=1;
                else $content['success']=0;
            break;
            
            case 'wrapAddEditCustomer':
                if(!isset($_SESSION[session_id()]['adminID']))
                {
                        echo 'You are not Administrator'; exit();
                }     
		require_once(PATH_TO_CLASS1 . 'Customers.php');
		$cbm=new Customers();
                $id=filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                $content=$cbm->wrapAddEditCustomer($id);
            break;
            
            case 'addEditCustomer':
                if(!isset($_SESSION[session_id()]['adminID']))
                {
                        echo 'You are not Administrator'; exit();
                }      
		require_once(PATH_TO_CLASS1 . 'Customers.php');
		$cbm=new Customers();
                $content=$cbm->addEditCustomer();
            break;
            
            case 'deleteCustomer':
                if(!isset($_SESSION[session_id()]['adminID']))
                {
                        echo 'You are not Administrator'; exit();
                }     
		require_once(PATH_TO_CLASS1 . 'Customers.php');
		$cbm=new Customers();
                $content=array();
                $id=filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
		if($cbm->deleteCustomer($id)!==false) $content['success']=1;
                else $content['success']=0;
            break;
           
            case 'wrapAddEditOrder':
                if(!isset($_SESSION[session_id()]['adminID']))
                {
                        echo 'You are not Administrator'; exit();
                }     
		require_once(PATH_TO_CLASS1 . 'Orders.php');
		$cbm=new Orders();
                $id=filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                $content=$cbm->wrapAddEditOrder($id);
            break;
            
            case 'addEditOrder':
                if(!isset($_SESSION[session_id()]['adminID']))
                {
                        echo 'You are not Administrator'; exit();
                }      
		require_once(PATH_TO_CLASS1 . 'Orders.php');
		$cbm=new Orders();
                $content=$cbm->addEditOrder();
            break;
            
            case 'deleteOrder':
                if(!isset($_SESSION[session_id()]['adminID']))
                {
                        echo 'You are not Administrator'; exit();
                }     
		require_once(PATH_TO_CLASS1 . 'Orders.php');
		$cbm=new Orders();
                $content=array();
                $id=filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
		if($cbm->deleteOrder($id)!==false) $content['success']=1;
                else $content['success']=0;
            break;
            
            
            
            //Resort list
            case 'reSortSortings':
                $table=$_POST['table'];
                switch($_POST['table']){
                    default:
                    $className=ucfirst(strtolower($table));
                    require_once(PATH_TO_CLASS1 . $className . '.php');
                    $cbm=new $className();
                    $content['success']=$cbm->reSortSortings($_POST['ids']);    
                    break;
                }
            break;
        
            case 'addEditBasicContent':
                if(!isset($_SESSION[session_id()]['adminID']))
                {
                        echo 'You are not Administrator'; exit();
                }      
		require_once(PATH_TO_CLASS1 . 'HTMLContent.php');
		$htmlcontent=new HTMLContent();
                $content=$htmlcontent->addEditBasicContent();
            break;
            
                
                // Bellow are FE calls
                
                
                
	}
}elseif (isset($_GET['context'])){
    date_default_timezone_set('Europe/Sarajevo');
	switch($_GET['context']){
           
            case 'uploadPhoto':
            require_once(PATH_TO_CLASS1 . 'Uploader.php');                
            $uploader=new Uploader();
            $content=$uploader->upload1();
            if(isset($_GET['infunction'])){
                switch($_GET['infunction']){
                    case 'gallery':
                        require_once(PATH_TO_CLASS1 . 'Galleries.php');
                        $gallery=new Galleries();
                        require_once(PATH_TO_CLASS1 . 'Settings.php');
                        require_once(PATH_TO_CLASS1 . 'Images.php');
                        $setting=new Settings();
                        $image=new Images();
                        $_POST=array();
                        $_POST['aEOE_year']=$_GET['year'];
                        $_POST['aEOE_image']=$content['filename'];
                        $_POST['aEOE_thumbnail']=$content['filename1'];
                        if(isset($_GET['ID']) && $_GET['ID']>0){
                            $_POST['aEOE_id']=$_GET['ID'];
                            $id=$gallery->updateGalleryP();
                        }else{
                            $id=$gallery->insertGalleryP();
                        }
                        if($id!==false){
                            $rec=$gallery->getGalleryP($id);
                            if($rec!==false && sizeof($rec)>0){
                                $isAjax=true;
                                $content['thumbnail']=$gallery->wrapSingleRowGallery($rec, 0, $setting, $image, $isAjax);
                                $pre=$isAjax===true?'../':'';
                                if($rec->image!=''){
                                        $content['thumbnail1']=$image->wrapSingleThumb($rec->image, 'mAOLThumb');
                                } 
                            }
                        }
                    break;
                    case 'pdfattachment':
                        if(isset($_GET['ID']) && $_GET['ID']>0){
                            if($_GET['table']=='pages'){
                                require_once PATH_TO_CLASS1 . 'Pages.php';
                                $page=new Pages();
                                $_POST=array();
                                $_POST['aEOE_id']=$_GET['ID'];
                                $_POST['aEOE_media']=$content['filename'];
                                $id=$page->updatePageP();
                            }
                        }
                    break;
                }
            }
            break;
        
            case 'importEventAttendees':
            require_once(PATH_TO_CLASS1 . 'Uploader.php');                
            $uploader=new Uploader();
            $content=$uploader->upload2();
            break;
        
        }
}
echo json_encode($content);	
exit();    
?>