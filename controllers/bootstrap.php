<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
//error_reporting( E_ALL );
define('ITEM_PER_PAGE',20);
session_start();
//get url slash variable
$_SERVER['REQUEST_URI_PATH'] = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = array_values(array_filter(explode('/', rtrim($_SERVER['REQUEST_URI_PATH'], '/'))));
unset($segments[0]);
$segments = array_values($segments);
if (!isset($segments[0]))
	$segments[0] = 'home';
$siteData = array();
$siteData['dir'] = $_SERVER['DOCUMENT_ROOT'].'/sales-manager';
$siteData['url'] = 'http://'.$_SERVER['SERVER_NAME'].'/sales-manager';
$siteData['body_class']  	= 'page '.$segments[0].'_page';
$siteData['page']  	= $segments[0];

include_once($siteData['dir'].'/controllers/PaymentController.php');
include_once($siteData['dir'].'/controllers/ReportController.php');
include_once($siteData['dir'].'/controllers/SaleController.php');
include_once($siteData['dir'].'/controllers/ClientController.php');
include_once($siteData['dir'].'/controllers/MedicineController.php');

include_once($siteData['dir'].'/libraries/Classes/PHPExcel.php');

include_once($siteData['dir'].'/includes/Database.php');
include_once($siteData['dir'].'/includes/PrintView.php');
include_once($siteData['dir'].'/includes/Pagination.php');
include_once($siteData['dir'].'/includes/Excel.php');

pageTitle();

function pageTitle()
{
	global $siteData, $segments;
	if ($segments[0] == 'home'){
		$siteData['page_title'] = 'Продажа Лекарств';
	}
	if ($segments[0] == 'sales'){
		$siteData['page_title'] = 'Продажа';
	}
	else if ($segments[0] == 'client'){
		$siteData['page_title'] = 'Клиент';
	}
	else if ($segments[0] == 'payment'){
		$siteData['page_title'] = 'Оплата';
	}
	else if ($segments[0] == 'medicine'){
		$siteData['page_title'] = 'Лекарства';
	}
	else if ($segments[0] == 'report'){
		$siteData['page_title'] = 'Отчет';
	}
	else if ($segments[0] == 'input'){
		$siteData['page_title'] = 'Поступление Товара';
	}
}

function getCountryByCode($code)
{
    global $db;
    $recordCollection = array();
    try{
        global $db;
        $statement = $db->query("SELECT country_name FROM `countries` WHERE `country_barcode` LIKE '%$code%'");
        $result = $statement->fetch();
        return $result[country_name];
    }
    catch(PDOException $ex) {
        echo "An Error occured!";
        echo $ex->getMessage();
    }
    return false;
}
function getCountryList()
{
    global $db;
    try{
        $statement = $db->query("SELECT * FROM `countries` ORDER BY country_name");
        $result = $statement->fetchAll();
        return $result;
    }
    catch(PDOException $ex) {
        echo "An Error occured!";
        echo $ex->getMessage();
    }
    return false;
}

function getCurrencyList()
{
    global $db;
    try{
        $statement = $db->query("SELECT * FROM `currency`");
        $result = $statement->fetchAll();
        return $result;
    }
    catch(PDOException $ex) {
        echo "An Error occured!";
        echo $ex->getMessage();
    }
    return false;
}

function getPagination($max_row)
{
    $currentPage = isset($_GET[page])?$_GET[page]:1;
    echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $p = new pagination();
    $p->Items($max_row);
    $p->limit(ITEM_PER_PAGE);
    $p->parameterName("page");
    $p->currentPage($currentPage);
    $p->nextLabel('');
    $p->prevLabel('');
    return $p->getOutput();
}