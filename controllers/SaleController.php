<?php

class SaleController
{
    private $printer;
	function __construct()
	{
	    $this->printer = new PrintView(array('file'=>'/views/sales.php'));;
	}
	
	public function helloworld()
	{
		echo '<br/>sale controller';
	}

    public function getSalesPage()
    {

        $ClientController = new ClientController();
        $MedicineController = new MedicineController();
        $result[client_list] = $ClientController->getClientListData();
        if ($_POST[finish_transaction]==1){
            $this->saveTransaction($_POST);
        }
        else{
            if (strlen($_POST[sale_medicine_code])>0){
                $result[medicine_data] = $MedicineController->getMedicineBySearchResult(array(return_array=>true,medicine_code=>$_POST[sale_medicine_code],medicine_id=>$_POST[sale_medicine_code]));
                $_POST[sale_medicine_id] = $result[medicine_data][medicine_id];
                if (count($result[medicine_data])<5){
                    $result[medicine_data][message] = 'Товар с таким штрих кодом нет. Пожалуйста добавьте товар в базу';
                    $result[medicine_data]['medicine_code'] = '';
                }
            }
            else if ($_GET[medicine_id]>0 || $_POST[sale_medicine_id]>0){
                $medicine_id = ($_GET[medicine_id]>0)?$_GET[medicine_id]:$_POST[sale_medicine_id];
                $result[medicine_data] = $MedicineController->getMedicineByID(array(return_array=>true,medicine_id=>$medicine_id));
                $_POST[sale_medicine_id] = $result[medicine_data][medicine_id];
                if (count($result[medicine_data])<5){
                    $result[medicine_data][message] = 'Товар с таким названием нет. Пожалуйста добавьте товар в базу';
                }
            }
            if (strlen($_POST[sale_medicine_name])>0){
                $result[medicine_list] = $MedicineController->getMedicineListByName(array(medicine_name=>$_POST[sale_medicine_name]));
            }
            if ($_POST[sale_client_id]>0){
                $result[client_data] = $ClientController->getSingleClientData(array(client_id=>$_POST[sale_client_id]));
				//clinic medistom
                if ($_POST[sale_client_id]=='55'){
                    $result[medicine_data][medicine_price]=0;
                }
            }
            $result[currency_list] = getCurrencyList();
            $this->printer->printHtml(array('default_medicine_output'),$result);
        }
    }

    private function saveTransaction($params)
    {
        global $db;
        $ClientController = new ClientController();
        try{
            if ($params[sale_payment]>0){
                $PaymentController = new PaymentController();
                if ($_POST[sale_different_currency]=='on' && $_POST[sale_medicine_currency_id]!=$_POST[sale_exchange_currency_id]){
                    $PaymentController->savePayment(array(payment_client_id=>$_POST[sale_client_id],payment=>$_POST[sale_payment],payment_currency_id=>$_POST[sale_exchange_currency_id], exchange_checkbox=>$_POST[sale_different_currency], exchange_currency_id=>$_POST[sale_medicine_currency_id], exchange_rate=>$_POST[sale_exchange_rate]));
                }
                else{
                    $PaymentController->savePayment(array(payment_client_id=>$_POST[sale_client_id],payment=>$_POST[sale_payment],payment_currency_id=>$_POST[sale_medicine_currency_id], exchange_checkbox=>$_POST[sale_different_currency], exchange_currency_id=>$_POST[sale_exchange_currency_id], exchange_rate=>$_POST[sale_exchange_rate]));
                }
            }
            //updating client balance and debt
            $paymentResult = $ClientController->updateClientBalance(array(client_id=>$_POST[sale_client_id],payment=>$_POST[sale_payment],exchange_checkbox=>$_POST[sale_different_currency], exchange_currency_id=>$_POST[sale_exchange_currency_id], exchange_rate=>$_POST[sale_exchange_rate], quantity=>$_POST[sale_quantity],price=>$_POST[sale_price],medicine_currency_id=>$_POST[sale_medicine_currency_id],balance_exchange=>$_POST[balance_exchange],balance_checkbox=>$_POST[balance_checkbox]));

            //saving goods data
            if ($paymentResult!=''){
                $statement = $db->prepare("INSERT INTO sale
                    (sale_client_id, sale_medicine_id, sale_quantity, sale_price, sale_currency_id
                    , sale_status, sale_date )
                    VALUES (:sale_client_id, :sale_medicine_id, :sale_quantity, :sale_price,:sale_currency_id, :sale_status, :sale_date)");
                $today = date('Y-m-d');
                if ($_POST[sale_client_id]=='28'){
                    $paymentResult = 'paid';
                }
                $statement->bindParam(':sale_client_id', $_POST[sale_client_id]);
                $statement->bindParam(':sale_medicine_id', $_POST[sale_medicine_id]);
                $statement->bindParam(':sale_quantity', $_POST[sale_quantity]);
                $statement->bindParam(':sale_price', $_POST[sale_price]);
                $statement->bindParam(':sale_currency_id', $_POST[sale_medicine_currency_id]);
                $statement->bindParam(':sale_status', $paymentResult);
                $statement->bindParam(':sale_date', $today);

                $insertResult =  $statement->execute();
                $sale_id = $db->lastInsertId();
            }

            //saving as transaction
            if ($insertResult==true){
                $statement = $db->prepare("INSERT INTO sale_transaction
                    (sale_transaction_sale_id, sale_transaction_price,sale_transaction_currency_id, sale_transaction_quantity, sale_transaction_date)
                    VALUES (:sale_transaction_sale_id, :sale_transaction_price,:sale_transaction_currency_id, :sale_transaction_quantity, :sale_transaction_date)");

                $statement->bindParam(':sale_transaction_sale_id', $sale_id);
                $statement->bindParam(':sale_transaction_price', $_POST[sale_price]);
                $statement->bindParam(':sale_transaction_currency_id', $_POST[sale_medicine_currency_id]);
                $statement->bindParam(':sale_transaction_quantity', $_POST[sale_quantity]);
                $statement->bindParam(':sale_transaction_date',$today);

                $insertResult =  $statement->execute();
            }
            //updating medicine quantity
            if ($insertResult==true){
                $statement = $db->prepare("UPDATE medicine SET medicine_quantity=medicine_quantity - :medicine_quantity
                    WHERE medicine_id = :medicine_id");

                $statement->bindParam(':medicine_id', $_POST[sale_medicine_id]);
                $statement->bindParam(':medicine_quantity', $_POST[sale_quantity]);

                $insertResult =  $statement->execute();
            }

            $result[action_result] = ($insertResult==true)?'save':'fail';
        }
        catch(PDOException $ex) {
            echo "An Error occured!";
            echo $ex->getMessage();
            $result[action_result] = 'fail';
        }
        $ClientController = new ClientController();
        $result[client_list] = $ClientController->getClientListData();
        if ($_POST[sale_client_id]>0){
            $result[client_data] = $ClientController->getSingleClientData(array(client_id=>$_POST[sale_client_id]));
        }
        $this->printer->printHtml(array('default_medicine_output'),$result);
    }
}