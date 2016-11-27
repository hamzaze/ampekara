<?php
require_once('config.php');
header('Content-Type: application/json');
if(isset($_POST['context'])){
    switch($_POST['context']){
        default:
            $content=array();
            $content['success']=3;
            $content['message']='<h4>Ova komanda ne može biti izvršena.</h4><div class="panel-warning">
                <div class="panel-heading">
                    <div class="panel-title">
                        ' . $_POST['context'] . '
                    </div>
                </div>
            </div><p>Provjerite da li su procedure za izvršenje spremne.</p>';
        break;
        
        case 'setupProductionDateFor':
            $timestamp=filter_input(INPUT_POST, 'timestamp', FILTER_VALIDATE_INT);
            if(!$timestamp){
                $timestamp=time()*1000;
            }
            if($timestamp){
                require_once(PATH_TO_CLASS1 . 'Settings.php');
                $cbm=new Settings();
                $_SESSION['currentProductionDate']=date('Y-m-d H:i:s', ($timestamp/1000));
                $content['success']=1;
                $content['results']=$cbm->getJSONPrivateSections();
            }
        break;
        
        case 'checkIsUserLoggedIn':
            require_once(PATH_TO_CLASS1 . 'UsersFE.php');
            $cbm=new UsersFE();
            $content=$cbm->checkIsUserLoggedIn();
        break;
    
        case 'logoutFEUser':
            //Validate post vars
            require_once(PATH_TO_CLASS1 . 'UsersFE.php');
            $cbm=new UsersFE();
            $content=$cbm->logoutFEUser();
        break;
        
        case 'loginFEUser':
            //Validate post vars
            $password=filter_input(INPUT_POST, 'aEOE_password', FILTER_SANITIZE_STRING);
            if(!$password){
                $content['success']=0;
                $content['message']='Provjerite da li ste unijeli ispravno password.';
            }else{
                require_once(PATH_TO_CLASS1 . 'UsersFE.php');
                $cbm=new UsersFE();
                $content=$cbm->loginFEUser($password);
            }
        break;
        
        case 'createOrderForCustomer':
            $customerid=filter_input(INPUT_POST, 'aEOE_customerid', FILTER_VALIDATE_INT);
            if(!$customerid){
                $content['success']=0;
                $content['message']='Kupac nije pronađen.';
            }else{
                require_once(PATH_TO_CLASS1 . 'OrdersFE.php');
                $cbm=new OrdersFE();
                $content=$cbm->createOrderForCustomer($customerid);
            }
        break;
        
        case 'createProductProduction':
            require_once(PATH_TO_CLASS1 . 'ProductionsFE.php');
            $cbm=new ProductionsFE();
            $content=$cbm->createProductProduction();
        break;
    }
}
echo json_encode($content);	
exit();