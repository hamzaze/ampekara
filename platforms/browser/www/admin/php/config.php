<?php
session_start();
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
define('DOCUMENT_ROOT', '/am');
define('PRETABLE', 'tx_');
define('PRETABLEHELPER', 'th_');
define('PATH_TO_IMAGES', 'images/');
define('PATH_TO_HTML', 'views/');
define('PATH_TO_TEMP', DOCUMENT_ROOT . '/temp/pics/');
define('TOPHEADER', PATH_TO_HTML . 'topHeader.htm');
define('TOPHEADER1', PATH_TO_HTML . 'topHeader1.htm');
define('FOOTER', PATH_TO_HTML . 'footer.htm');
define('PATH_TO_ADMIN', 'admin/');
define('PATH_TO_PHP', 'php/');
define('PATH_TO_CLASS', 'CustomClass/');
define('PATH_TO_CLASS1', '../CustomClass/');
define('PATH_TO_PHOTOS',  DOCUMENT_ROOT . '/uploads/photos/');
define('PATH_TO_CSV', DOCUMENT_ROOT . '/uploads/csv/');
define('PATH_TO_DOCUMENTS', DOCUMENT_ROOT . 'uploads/docs/');
define('PATH_TO_GALLERIES', PATH_TO_PHOTOS . 'galleries/');

define('PATH_TO_TEMP_PICS', '../temp/pics/');
define('PATH_TO_TEMP_DOCS', '../temp/docs/');
define('PATH_TO_VIDEOS', 'uploads/videos/');
define('URL_TO_PHOTOS', '../uploads/photos/');
define('URL_TO_GALLERIES', '../uploads/photos/galleries/');
define('PATH_TO_DOWNLOAD_CSV', 'downloads/csv/');

define('URL_TO_TEMP_PICS', DOCUMENT_ROOT . '/temp/pics/');

define('MAXVIDEOSIZE', 25*1024*1024);
define('MAXIMAGESIZE', 4*1024*1024);
define('PHOTOLARGEDIMHEAD', 250);
define('PHOTOLARGE', 400);
define('EXTRALARGE', 1000);
define('PHOTOTHUMB', 200);
define('PHOTOTHUMB1', 125);
define('PHOTOLARGE1', 500);

define('THUMBNAIL1a', 250);
define('THUMBNAIL1a_H', 150);

define('TOPSLIDESHOW', 1000);
define('TOPSLIDESHOW_H', 300);

define('THUMBNAIL2a', 200);
define('THUMBNAIL2a_H', 200);

define('DELETEWHITEICON', '<img src="' . PATH_TO_IMAGES . 'cross.png" class="icon" />');
define('EDITICON', '<img src="' . PATH_TO_IMAGES . 'pencil.png" class="icon" />');
define('DELETEICON', DELETEWHITEICON);
define('LOGO', '<img src="' . PATH_TO_IMAGES . 'logo.png" class="icon" />');
define('LOGOLANDING', '<img src="' . PATH_TO_IMAGES . 'logolanding.png" class="icon" />');
define('ICONNOTPAID', '<img src="' . PATH_TO_IMAGES . 'sponsorshipNoPayed.png" class="icon" />');
define('EXPORTICON', '<img src="' . PATH_TO_IMAGES . 'iconExport.png" class="icon" />');

define('CURRENCYSIGN', 'KM');

define('SALT', 'cfevents2016');

define('GOOGLEAPIKEY', 'AIzaSyC4RLVWRv4pJM3ytIohT6uiis0R46gUXzY');

ini_set('display_errors',1);
error_reporting(E_ALL & ~E_STRICT);


?>
