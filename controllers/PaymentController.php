<?php

class PaymentController
{
    private $printer;
    private $ClientController;
    private $PaymentController;

	function __construct()
	{
        $this->printer = new PrintView(array('file'=>'views/view_payment.php'));
	}
	
	public function helloworld()
	{
		echo '<br/>payment controller';
	}

    public function getPaymentDefaultPage()
    {
        $this->ClientController = new ClientController();
        $this->PaymentController = new PaymentController();
        $result[client_list] = $this->ClientController->getClientListData();
        $result[currency_list] = getCurrencyList();
        //print_r($_POST);
        if ($_POST[payment_client_id]>0 && isset($_POST[set_balance]) && $_POST[payment]>0){

            $bla = new ClientController();
            if ($_POST[exchange_checkbox]=='on'){
                $result[action_result] = $bla->increaseClientBalance(array(client_id=>$_POST[payment_client_id],balance=>($_POST[payment]*$_POST[exchange_rate]),currency=>$_POST[exchange_currency_id]));
            }
            else{
                $result[action_result] = $bla->increaseClientBalance(array(client_id=>$_POST[payment_client_id],balance=>$_POST[payment],currency=>$_POST[payment_currency_id]));
            }
            $result[action_result] = $this->PaymentController->savePayment($_POST);
        }
        if ($_POST[data][action]=='payment_paid' && is_numeric($_POST[data][sale_id])){
            $this->payForSaleTransaction($_POST[data]);
        }
        if ($_POST[data][action]=='good_returned' && is_numeric($_POST[data][sale_id])){
            $this->returnGoods($_POST[data]);
        }
        if ($_POST[payment_client_id]>0){
            $result[client_data] = $this->ClientController->getSingleClientData(array(client_id=>$_POST[payment_client_id]));
            $result[unpaid_good_list] = $this->getUnpaidSaleListByClientID(array(client_id=>$_POST[payment_client_id]));
            $result[free_good_list] = $this->getFreeSaleListByClientID(array(client_id=>$_POST[payment_client_id]));
            foreach($result[client_data][balance] as $singleBalance){
                foreach($result[unpaid_good_list]  as  $key=>$singleItem){
                    if ($singleBalance[client_balance_currency_id]==$singleItem[sale_currency_id] && $singleBalance[client_balance_balance]>=$singleItem[sale_quantity]*$singleItem[sale_price]){
                        $result[unpaid_good_list][$key][payment_ok] = true;
                    }
                }
            }
        }

        if ($_GET[request]!='ajax')
            $this->printer->printHtml(array('default_payment_page'),$result);
    }

    public function savePayment($params)
    {
        global $db;
        try{
            //saving payment
            $statement = $db->prepare("INSERT INTO payment
				(payment_client_id, payment_amount,payment_currency_id, payment_exchange_currency_id, payment_exchange_rate, payment_date)VALUES
				(:payment_client_id,:payment_amount,:payment_currency_id,:payment_exchange_currency_id, :payment_exchange_rate,:payment_date)");
            $today = date('Y-m-d');
            if ($params[exchange_checkbox]!='on'){
                $params[exchange_currency_id] = 2;
                $params[exchange_rate] = 0;
            }
            $statement->bindParam(':payment_client_id', $params[payment_client_id]);
            $statement->bindParam(':payment_amount', $params[payment]);
            $statement->bindParam(':payment_currency_id', $params[payment_currency_id]);
            $statement->bindParam(':payment_exchange_currency_id', $params[exchange_currency_id]);
            $statement->bindParam(':payment_exchange_rate', $params[exchange_rate]);
            $statement->bindParam(':payment_date', $today);

            $insertResult =  $statement->execute();

            return $insertResult;

        }
        catch(PDOException $ex) {
            echo "An Error occured!";
            echo $ex->getMessage();
            return false;
        }
        return false;
    }

    private function getUnpaidSaleListByClientID($params)
    {
        global $db;
        $statement = $db->prepare("SELECT * FROM sale
            INNER JOIN medicine ON medicine.medicine_id = sale.sale_medicine_id
            INNER JOIN currency ON sale.sale_currency_id = currency.currency_id
            WHERE `sale_client_id` = :client_id AND sale_status = :status");
        $status = 'pending';
        $statement->bindParam(':client_id', $params[client_id]);
        $statement->bindParam(':status', $status);
        $statement->execute();
        return  $statement->fetchAll();
    }

    private function getFreeSaleListByClientID($params)
    {
        global $db;
        $statement = $db->prepare("SELECT * FROM sale
            INNER JOIN medicine ON medicine.medicine_id = sale.sale_medicine_id
            INNER JOIN currency ON sale.sale_currency_id = currency.currency_id
            WHERE `sale_client_id` = :client_id AND `sale_price` = 0");
        $statement->bindParam(':client_id', $params[client_id]);
        $statement->execute();
        return  $statement->fetchAll();
    }
/*
 * accepts payment for sold items by sale id
 * */
    private function payForSaleTransaction($params)
    {
        global $db;
        $statement = $db->prepare("SELECT * FROM sale WHERE `sale_id` = :sale_id");
        $statement->bindParam(':sale_id', $params[sale_id]);
        $statement->execute();
        $saleData = $statement->fetch();
        $ClientController = new ClientController();
        $payment = $saleData[sale_price]*$saleData[sale_quantity];
        try{
            $insertResult = $ClientController->decreaseClientDebt(array(client_id=>$saleData[sale_client_id],debt=>$payment,currency_id=>$saleData[sale_currency_id]));
            if ($insertResult!='ok'){
                echo 'fail';
                exit();
            }
            $this->savePayment(array(payment_client_id=>$saleData[sale_client_id],payment_currency_id=>$saleData[sale_currency_id],payment=>$payment));
            $paymentResult = $ClientController->updateClientBalance(array(client_id=>$saleData[sale_client_id] ,payment=>$saleData[sale_price]*$saleData[sale_quantity]*(-1), quantity=>$saleData[sale_quantity], price=>0,medicine_currency_id=>$saleData[sale_currency_id]));

            //saving goods data
            $statement = $db->prepare("UPDATE sale SET sale_status = :sale_status WHERE sale_id = :sale_id");
            $today = date('Y-m-d');
            $status = 'paid';
            $statement->bindParam(':sale_status', $status);
            $statement->bindParam(':sale_id', $params[sale_id]);

            $insertResult =  $statement->execute();
            if ($insertResult==false){
                echo 'fail';
                exit();
            }

            //saving as transaction
            $statement = $db->prepare("INSERT INTO sale_transaction
				(sale_transaction_sale_id, sale_transaction_price, sale_transaction_currency_id, sale_transaction_quantity, sale_transaction_date)
				VALUES (:sale_transaction_sale_id, :sale_transaction_price,:sale_transaction_currency_id, :sale_transaction_quantity, :sale_transaction_date)");

            $statement->bindParam(':sale_transaction_sale_id', $params[sale_id]);
            $statement->bindParam(':sale_transaction_price', $saleData[sale_price]);
            $statement->bindParam(':sale_transaction_currency_id', $saleData[sale_currency_id]);
            $statement->bindParam(':sale_transaction_quantity', $saleData[sale_quantity]);
            $statement->bindParam(':sale_transaction_date',$today);

            $insertResult =  $statement->execute();
            if ($insertResult==false){
                echo 'fail';
                exit();
            }

            $result[action_result] = ($insertResult==true)?'save':'fail';
        }
        catch(PDOException $ex) {
            echo "An Error occured!";
            echo $ex->getMessage();
            $result[action_result] = 'fail';
        }
        echo $result[action_result];
    }

    private function returnGoods($params)
    {
        global $db;
        $statement = $db->prepare("SELECT * FROM sale WHERE `sale_id` = :sale_id");
        $statement->bindParam(':sale_id', $params[sale_id]);
        $statement->execute();
        $saleData = $statement->fetch();
        $today = date('Y-m-d');
        try{
            //saving goods data
            if ($params[good_quantity] < $saleData[sale_quantity]){
                $statement = $db->prepare("UPDATE sale SET sale_quantity = :sale_quantity WHERE sale_id = :sale_id");

                $sale_quantity = $saleData[sale_quantity] - $params[good_quantity];
                $statement->bindParam(':sale_quantity', $sale_quantity);
                $statement->bindParam(':sale_id', $params[sale_id]);
                $insertResult =  $statement->execute();
            }
            else if (abs($params[good_quantity]-$saleData[sale_quantity])<0.01){
                $statement = $db->prepare("DELETE FROM sale WHERE sale_id = :sale_id");
                $statement->bindParam(':sale_id', $params[sale_id]);
                $insertResult = $statement->execute();
            }
            else if ($params[good_quantity] > $saleData[sale_quantity]){
                echo 'returning more than needed';
                exit();
            }
            if ($insertResult!=true){
                echo 'fail';
                exit();
            }
            $ClientController = new ClientController();

            $insertResult = $ClientController->decreaseClientDebt(array(client_id=>$saleData[sale_client_id], debt=>($params[good_quantity] * $saleData[sale_price]), currency_id=>$saleData[sale_currency_id]));
            if ($insertResult=='free item returned'){
                echo 'Free item returned';
                exit();
            }

            //saving as transaction
            $statement = $db->prepare("INSERT INTO sale_transaction
				(sale_transaction_sale_id, sale_transaction_price, sale_transaction_currency_id, sale_transaction_quantity, sale_transaction_date)
				VALUES (:sale_transaction_sale_id, :sale_transaction_price,:sale_transaction_currency_id, :sale_transaction_quantity, :sale_transaction_date)");
            $tempPrice = 0;
            $tempQuantity = $params[good_quantity]*(-1);
            $statement->bindParam(':sale_transaction_sale_id', $params[sale_id]);
            $statement->bindParam(':sale_transaction_price', $tempPrice);
            $statement->bindParam(':sale_transaction_currency_id', $saleData[sale_currency_id]);
            $statement->bindParam(':sale_transaction_quantity', $tempQuantity);
            $statement->bindParam(':sale_transaction_date',$today);

            $insertResult =  $statement->execute();

            if ($insertResult==false){
                echo 'fail';
                exit();
            }

            $statement = $db->prepare("UPDATE medicine SET medicine_quantity=medicine_quantity + :medicine_quantity
                WHERE medicine_id = :medicine_id");

            $statement->bindParam(':medicine_id', $params[sale_medicine_id]);
            $statement->bindParam(':medicine_quantity', $params[good_quantity]);

            $insertResult =  $statement->execute();

            $result[action_result] = ($insertResult==true)?'returned':'fail';
        }
        catch(PDOException $ex) {
            echo "An Error occured!";
            echo $ex->getMessage();
            $result[action_result] = 'fail';
        }
        echo $result[action_result];
    }
}