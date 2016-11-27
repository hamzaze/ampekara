<?php
if(!isset($_SESSION))  session_start();
date_default_timezone_set("Europe/Sarajevo");

define('DOCUMENT_ROOT', '/am');
define('PATH_TO_HTML', 'views/');
define('PATH_TO_HTML_SUB', PATH_TO_HTML . 'sub/');
define('PATH_TO_IMAGES', DOCUMENT_ROOT . '/images/');

define('TOPHEADER', PATH_TO_HTML . 'topHeader.htm');
define('FOOTER', PATH_TO_HTML . 'footer.htm');
define('TOPHEADERMINIMALISTIC', PATH_TO_HTML . 'topHeaderMinimalistic.htm');
define('FOOTERMINIMALISTIC', PATH_TO_HTML . 'footerMinimalistic.htm');
define('PATH_TO_PHP', 'php/');
define('PATH_TO_ADMIN', 'admin/');
define('PATH_TO_CLASS', PATH_TO_ADMIN . 'CustomClass/');
define('PATH_TO_CLASS1', '../' . PATH_TO_ADMIN . 'CustomClass/');
define('PRETABLE', 'tx_');
define('PRETABLEHELPER', 'th_');
define('LOGO', '<img src="' . PATH_TO_IMAGES . 'logo.png" class="img-responsive" />');
define('NOPRODUCT_PHOTO', 'no_product.png');
define('GALLERY_LOGO', '<img src="' . PATH_TO_IMAGES . 'gallery-Logo.png" class="img-responsive" />');
define('LOGOWHITE', '<img src="' . PATH_TO_IMAGES . 'logoWhite.png" class="img-responsive" />');


define('AJAXLOADER', '<div class="ajaxLoader left50 top50 abs"><img src="' . PATH_TO_IMAGES . 'ajaxloader.gif" class="ajaxLoader" /></div>');

define('PATH_TO_PHOTOS', DOCUMENT_ROOT . '/uploads/photos/');
define('URL_TO_PHOTOS', 'uploads/photos/');
define('URL_TO_SESSION_PHOTOS', '../private/uploads/photos/');
define('URL_TO_GALLERIES', 'uploads/photos/galleries/');
define('PATH_TO_TEMP_PICS', 'temp/pics/');
define('PATH_TO_TEMP_PICSROOT', '../temp/pics/');

define('PATH_TO_AJAXDISPATCHER', DOCUMENT_ROOT . PATH_TO_PHP . 'ajaxDispatcher.php');



define('EXTRALARGE', 1600);
define('THUMBNAIL2a', 120);
define('THUMBNAIL2a_H', 120);

define('THUMBNAIL1a', 400);
define('THUMBNAIL1a_H', 400);

define('PHOTOTHUMB', 200);
define('PHOTOTHUMB1', 125);

define('JUSTLARGE', 640);

define('GALLERYSINGLE', 1024);
define('GALLERYSINGLE_H', 1024);

define('CURRENCYSIGN', '$');

define('TAX', 13);


ini_set('display_errors',1);
error_reporting(E_ALL & ~E_STRICT);

$defaultPageTemplate='page.htm';
$content='';
$metacontent='';
$includeCSSFileAfter='';
$siteTitle='PEKARA A&M';
$mobilejQueryFile=$includejQueryFile=$includeCSSFile=$errorMessages=$dVAD='';