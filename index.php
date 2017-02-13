<?php
include_once('controllers/bootstrap.php');
if ($_GET[request]!='ajax' && $_GET[request]!='file'){
    include_once('views/header.php');
}
if (!isset($_SESSION[login])){
    include_once('views/login.php');
    if ($_GET[action]='login' && $_POST[login_password]=='medist0m'){
        $_SESSION[login] = true;	
    }
    include_once('views/footer.php');
    exit();
}
if ($segments[0] == 'home'){
	include_once('views/home.php');
}
else if ($segments[0] == 'sales'){
	$SaleController = new SaleController();
    $SaleController->getSalesPage();
}
else if ($segments[0] == 'input'){
    $MedicineController = new MedicineController();
    $MedicineController->getContentController();

}
else if ($segments[0] == 'client'){
	$ClientController 	= new ClientController($_GET);
	$recordCollection = $ClientController->getContentController($_GET);
	include_once('views/client.php');
}
else if ($segments[0] == 'payment'){
	$PaymentController 	= new PaymentController();
    $PaymentController->getPaymentDefaultPage();
}
else if ($segments[0] == 'medicine'){
	$MedicineController = new MedicineController();
}
else if ($segments[0] == 'report'){
	$reporController 	= new ReportController();
    $reporController->getContentController();
}
if ($_GET[request]!='ajax' && $_GET[request]!='file'){
    include_once('views/footer.php');
}