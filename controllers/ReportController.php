<?php

class ReportController
{
    private $printer;

    function __construct()
    {
        if ($_GET[request]!='file')
            $this->printer = new PrintView(array('file'=>'views/view_report.php'));
        else{
            $this->printer = new ExcelController(array(filename=>"report_medistom",columnLength=>7,autoWidth=>true));
        }
    }

    public function getContentController()
    {
        if ($_GET[request]!='file'){
            $this->printer->printHtml(array('report_navigation'));
        }
        if ($_GET[reportby]=='client'){
            $this->getReportByClient();
        }
        if ($_GET[reportby]=='income'){
            $this->getReportByIncome();
        }
        if ($_GET[reportby]=='sale_transaction'){
            $this->getReportBySaleTransaction();
        }
        if ($_GET[reportby]=='payment'){
            $this->getReportByPayment();
        }
        if ($_GET[request]=='file'){
            $this->printer->finish();
        }
    }

    private function getReportByClient()
    {
        global $siteData;
        $MedicineController = new MedicineController();
        $ClientController = new ClientController();
        $date = mktime(0,0,0,date('m')-1,date('d'),date('Y'));
        $_POST[start_date] = isset($_POST[start_date])? $_POST[start_date]: date('d-m-Y', $date);
        $result[medicine_list] = $MedicineController->getFullMedicineList();
        $result[client_list] = $ClientController->getClientListData();
        $result[medicine_category_list] = $MedicineController->getMedicineCategoryList();

        if (count($_POST)>1){
            $result[search_result] = $this->getMedicineListByFilter($_POST);
            $result[total] = $result[search_result][total];
            unset($result[search_result][total]);
            $result['save_url'] = $siteData['url'].'/report?reportby='.$_GET[reportby].'&request=file&start_date='.$_POST[start_date].'&end_date='.$_POST[end_date];
            $result['save_url'] .= ($_POST[medicine_id]>0)?"&medicine_id=".$_POST[medicine_id]:'';
            $result['save_url'] .= ($_POST[client_id]>0)?"&client_id=".$_POST[client_id]:'';
            $result['save_url'] .= "&status=".$_POST[status];

        }
        if ($_GET[request]=='file'){
            $this->printFileHeader($_GET);
            $result[search_result] = $this->getMedicineListByFilter($_GET);
            $result[total] = $result[search_result][total];
            unset($result[search_result][total]);
            if (count($result[search_result])>0){
                $this->printer->writeLine(array('Дата', 'Название Товара', 'Производитель', 'Клиент', 'Количество', 'Цена', 'Сумма ', 'Статус'),array(borderStyle=>array('color'=>'000000'),fontStyle=>array(bold=>true)));
                foreach($result[search_result] as $singleItem){
                    $singleItem[sale_status] = $singleItem[sale_status]=='paid'?'Оплачено':'Не Оплачено';
                    $this->printer->writeLine(array($singleItem[sale_date], $singleItem[medicine_name],$singleItem[medicine_manufacture_name], $singleItem[client_firstname].' '.$singleItem[client_lastname], $singleItem[sale_quantity],$singleItem[sale_price],$singleItem[sale_price] * $singleItem[sale_quantity], $singleItem[sale_status]));
                }
                $this->printer->writeLine(array('Итого Оплачено'));
                $this->printer->writeLine(array('Количество : '. $result[summary][paid][quantity], 'Сумма : '.$result[summary][paid][real_price]));
                $this->printer->writeLine(array('Итого Не Оплачено'));
                $this->printer->writeLine(array('Количество : '. $result[summary][pending][quantity], 'Сумма : '.$result[summary][pending][real_price]));
            }
        }
        else{
            $this->printer->printHtml(array('report_client'),$result);
        }
    }

    private function getReportBySaleTransaction()
    {
        global $siteData;
        $MedicineController = new MedicineController();
        $ClientController = new ClientController();
        $date = mktime(0,0,0,date('m')-1,date('d'),date('Y'));
        $_POST[start_date] = isset($_POST[start_date])? $_POST[start_date]: date('d-m-Y', $date);
        $result[medicine_list] = $MedicineController->getFullMedicineList();
        $result[client_list] = $ClientController->getClientListData();
        $result[medicine_category_list] = $MedicineController->getMedicineCategoryList();

        if (count($_POST)>1){
            $result[search_result] = $this->getSaleTransactionListByFilter($_POST);
            $result[total] = $result[search_result][total];
            unset($result[search_result][total]);
            $result['save_url'] = $siteData['url'].'/report?reportby='.$_GET[reportby].'&request=file&start_date='.$_POST[start_date].'&end_date='.$_POST[end_date];
            $result['save_url'] .= ($_POST[medicine_id]>0)?"&medicine_id=".$_POST[medicine_id]:'';
            $result['save_url'] .= ($_POST[client_id]>0)?"&client_id=".$_POST[client_id]:'';
            $result['save_url'] .= "&status=".$_POST[status];
        }
        if ($_GET[request]=='file'){
            $this->printFileHeader($_GET);
            $result[search_result] = $this->getSaleTransactionListByFilter($_GET);
            $result[total] = $result[search_result][total];
            unset($result[search_result][total]);
            if (count($result[search_result])>0){
                $this->printer->writeLine(array('Дата', 'Название Товара','Производитель', 'Клиент','Количество', 'Цена', 'Сумма ','Трансакция'),array(borderStyle=>array('color'=>'000000'),fontStyle=>array(bold=>true)));
                foreach($result[search_result] as $singleItem){
                    $bla = '';
					if ($singleItem[sale_transaction_quantity]>0){
						if ($singleItem[sale_status]=='pending'){
							$bla = 'Не Оплачено';
						}
						else if ($singleItem[sale_status]=='paid'){
							$bla = 'Оплачено';
						}
					}
					else{
						$bla = 'Возвращено';
					}
                    $this->printer->writeLine(array($singleItem[sale_transaction_date], $singleItem[medicine_name], $singleItem[medicine_manufacture_name], $singleItem[client_firstname].' '.$singleItem[client_lastname], $singleItem[sale_transaction_quantity],$singleItem[sale_transaction_price], $singleItem[sale_transaction_quantity] * $singleItem[sale_transaction_price],$bla));
                }
                $this->printer->writeLine(array('Итого Оплачено'));
                $this->printer->writeLine(array('Количество : '. $result[summary][paid][quantity], 'Сумма : '.$result[summary][paid][real_price]));
                $this->printer->writeLine(array('Итого Не Оплачено'));
                $this->printer->writeLine(array('Количество : '. $result[summary][pending][quantity], 'Сумма : '.$result[summary][pending][real_price]));
                $this->printer->writeLine(array('Итого Не Оплачено'));
                $this->printer->writeLine(array('Количество : '. $result[summary][returned][quantity], 'Сумма : '.$result[summary][returned][real_price]));
            }
        }
        else
            $this->printer->printHtml(array('report_sale_transaction'),$result);
        //
    }

    private function getReportByIncome()
    {
        global $siteData;
        $MedicineController = new MedicineController();
        $date = mktime(0,0,0,date('m')-1,date('d'),date('Y'));
        $_POST[start_date] = isset($_POST[start_date])? $_POST[start_date]: date('d-m-Y', $date);
        $result[medicine_list] = $MedicineController->getFullMedicineList();
        $result[medicine_category_list] = $MedicineController->getMedicineCategoryList();

        if (count($_POST)>1){
            $result[search_result] = $this->getMedicineIncomeListByFilter($_POST);
            $result[total] = $result[search_result][total];
            unset($result[search_result][total]);
            $result['save_url'] = $siteData['url'].'/report?reportby='.$_GET[reportby].'&request=file&start_date='.$_POST[start_date].'&end_date='.$_POST[end_date];
            $result['save_url'] .= ($_POST[medicine_id]>0)?"&medicine_id=".$_POST[medicine_id]:'';
        }
        if ($_GET[request]=='file'){
            $this->printFileHeader($_GET);
            $result[search_result] = $this->getMedicineIncomeListByFilter($_GET);
            $result[total] = $result[search_result][total];
            unset($result[search_result][total]);
            unset($result[search_result][total]);
            if (count($result[search_result])>0){
                $this->printer->writeLine(array('Дата', 'Название Товара','Производитель',  'Количество', 'Цена', 'Сумма'),array(borderStyle=>array('color'=>'000000'),fontStyle=>array(bold=>true)));
                foreach($result[search_result] as $singleItem){
                    $this->printer->writeLine(array($singleItem[medicine_income_date], $singleItem[medicine_name], $singleItem[medicine_manufacture_name], $singleItem[medicine_income_quantity], $singleItem[medicine_income_price],$singleItem[medicine_income_price] * $singleItem[medicine_income_quantity]));
                }
                $this->printer->writeLine(array('Итого'));
                $this->printer->writeLine(array('Количество : '. $result[summary][quantity],'Сумма : '.$result[summary][real_price]));
            }
        }
        else
            $this->printer->printHtml(array('report_income'),$result);
    }

    private function getReportByPayment()
    {
        global $siteData;
        $ClientController = new ClientController();
        $date = mktime(0,0,0,date('m')-1,date('d'),date('Y'));
        $_POST[start_date] = isset($_POST[start_date])? $_POST[start_date]: date('d-m-Y', $date);
        $result[client_list] = $ClientController->getClientListData();

        if (count($_POST)>1){
            $result[search_result] = $this->getPaymentListByFilter($_POST);
            $result[summary] = $result[search_result][total];
            unset($result[search_result][total]);

            $result['save_url'] = $siteData['url'].'/report?reportby='.$_GET[reportby].'&request=file&start_date='.$_POST[start_date].'&end_date='.$_POST[end_date];
            $result['save_url'] .= ($_POST[client_id]>0)?"&client_id=".$_POST[client_id]:'';
        }

        if ($_GET[request]=='file'){
            $this->printFileHeader($_GET);
            $result[search_result] = $this->getPaymentListByFilter($_GET);
            $result[summary] = $result[search_result][total];
            unset($result[search_result][total]);
            if (count($result[search_result])>0){
                $this->printer->writeLine(array('Дата', 'Клиент', 'Сумма '),array(borderStyle=>array('color'=>'000000'),fontStyle=>array(bold=>true)));
                foreach($result[search_result] as $singleItem){
                    $this->printer->writeLine(array($singleItem[payment_date], $singleItem[client_firstname].' '.$singleItem[client_lastname], $singleItem[payment_amount]));
                }
                $this->printer->writeLine(array('Итого'));
                $this->printer->writeLine(array('Сумма : '. $result[summary][payment_amount]));
            }
        }
        else
            $this->printer->printHtml(array('report_payment'),$result);
    }

    private function getMedicineListByFilter($params)
    {
        global $db;
        $query = ($params[medicine_id]>0)?" AND sale_medicine_id = $params[medicine_id]":'';
        $query .= ($params[medicine_manufacture_id]>0)?" AND medicine.medicine_manufacture_id = $params[medicine_manufacture_id]":'';
        $query .= ($params[client_id]>0)?" AND sale_client_id = $params[client_id]":'';
        $query .= ($params[status]!='')?" AND sale_status = '$params[status]'":'';
        $startDate = DateTime::createFromFormat('d-m-Y', $params[start_date]);
        $startDate = $startDate->format('Y-m-d');
        $endDate = DateTime::createFromFormat('d-m-Y', $params[end_date]);
        $endDate = $endDate->format('Y-m-d');
        $statement = $db->prepare("SELECT *, sale_price as price, sale_quantity as quantity FROM `sale`
            INNER JOIN currency ON currency.currency_id = sale.sale_currency_id
            INNER JOIN medicine ON medicine.medicine_id = sale.sale_medicine_id
            LEFT JOIN medicine_manufacture ON medicine.medicine_manufacture_id = medicine_manufacture.medicine_manufacture_id
            INNER JOIN client ON client.client_id = sale.sale_client_id
            WHERE str_to_date( sale_date , '%Y-%m-%d' ) BETWEEN str_to_date( :start_date , '%Y-%m-%d' ) AND str_to_date( :end_date , '%Y-%m-%d' ) $query");
        $statement->bindParam(':start_date', $startDate);
        $statement->bindParam(':end_date', $endDate);
        $statement->execute();
        $recordCollection =  $statement->fetchAll();
        $total = array();
        foreach($recordCollection as $key=>$singleItem){
            $tempDate = DateTime::createFromFormat('Y-m-d', $singleItem[sale_date]);
            $recordCollection[$key][sale_date]  = $tempDate->format('d-m-Y');
        }
        $total = $this->calculateTotals($recordCollection,array(check_sale_status=>true));
        $recordCollection[total] = $total;
        return $recordCollection;
    }

    private function getSaleTransactionListByFilter($params)
    {
        global $db;
        $query = ($params[medicine_id]>0)?" AND sale_medicine_id = $params[medicine_id]":'';
        $query .= ($params[medicine_manufacture_id]>0)?" AND medicine.medicine_manufacture_id = $params[medicine_manufacture_id]":'';
        $query .= ($params[client_id]>0)?" AND sale_client_id = $params[client_id]":'';
        $query .= ($params[status]=='paid')?" AND sale_transaction_quantity >0":'';
        $query .= ($params[status]=='returned')?" AND sale_transaction_quantity < 0":'';
        $startDate = DateTime::createFromFormat('d-m-Y', $params[start_date]);
        $startDate = $startDate->format('Y-m-d');
        $endDate = DateTime::createFromFormat('d-m-Y', $params[end_date]);
        $endDate = $endDate->format('Y-m-d');
        $statement = $db->prepare("SELECT *, sale_transaction_price as price, sale_transaction_quantity as quantity FROM `sale_transaction`
            INNER JOIN sale ON sale_transaction.sale_transaction_sale_id = sale.sale_id
            INNER JOIN currency ON currency.currency_id = sale_transaction.sale_transaction_currency_id
            INNER JOIN medicine ON medicine.medicine_id = sale.sale_medicine_id
            LEFT JOIN medicine_manufacture ON medicine.medicine_manufacture_id = medicine_manufacture.medicine_manufacture_id
            INNER JOIN client ON client.client_id = sale.sale_client_id
            WHERE str_to_date( sale_transaction_date , '%Y-%m-%d' ) BETWEEN str_to_date( :start_date , '%Y-%m-%d' ) AND str_to_date( :end_date , '%Y-%m-%d' ) $query");
        $statement->bindParam(':start_date', $startDate);
        $statement->bindParam(':end_date', $endDate);
        $statement->execute();
        $recordCollection =  $statement->fetchAll();
        $total = array();
        foreach($recordCollection as $key=>$singleItem){
            $tempDate = DateTime::createFromFormat('Y-m-d', $singleItem[sale_transaction_date]);
            $recordCollection[$key][sale_transaction_date]  = $tempDate->format('d-m-Y');
        }
        $total = $this->calculateTotals($recordCollection,array(check_sale_status=>true));
        $recordCollection[total] = $total;
        return $recordCollection;
    }

    private function getMedicineIncomeListByFilter($params)
    {
        global $db;
        $query = ($params[medicine_id]>0)?" AND medicine_income_medicine_id = $params[medicine_id]":'';
        $query .= ($params[medicine_manufacture_id]>0)?" AND medicine.medicine_manufacture_id = $params[medicine_manufacture_id]":'';
        $startDate = DateTime::createFromFormat('d-m-Y', $params[start_date]);
        $startDate = $startDate->format('Y-m-d');
        $endDate = DateTime::createFromFormat('d-m-Y', $params[end_date]);
        $endDate = $endDate->format('Y-m-d');
        $statement = $db->prepare("SELECT *, medicine_income_price as price, medicine_income_quantity as quantity FROM `medicine_income`
            INNER JOIN currency on currency.currency_id = medicine_income.medicine_income_currency_id
            INNER JOIN medicine ON medicine.medicine_id = medicine_income.medicine_income_medicine_id
            LEFT JOIN medicine_manufacture ON medicine.medicine_manufacture_id = medicine_manufacture.medicine_manufacture_id
            WHERE str_to_date( medicine_income_date , '%Y-%m-%d' ) BETWEEN str_to_date( :start_date , '%Y-%m-%d' ) AND str_to_date( :end_date , '%Y-%m-%d' ) $query");
        $statement->bindParam(':start_date', $startDate);
        $statement->bindParam(':end_date', $endDate);
        $statement->execute();
        $recordCollection =  $statement->fetchAll();
        foreach($recordCollection as $key=>$singleItem){
            $tempDate = DateTime::createFromFormat('Y-m-d', $singleItem[medicine_income_date]);
            $recordCollection[$key][medicine_income_date]  = $tempDate->format('d-m-Y');
        }
        $total = $this->calculateTotals($recordCollection);
        $recordCollection[total] =  $total;
        return $recordCollection;
    }

    private function getPaymentListByFilter($params)
    {
        global $db;
        $query = (is_numeric( $params[client_id]) && $params[client_id]>0)?" AND payment_client_id = $params[client_id]":'';
        $startDate = DateTime::createFromFormat('d-m-Y', $params[start_date]);
        $startDate = $startDate->format('Y-m-d');
        $endDate = DateTime::createFromFormat('d-m-Y', $params[end_date]);
        $endDate = $endDate->format('Y-m-d');
        $statement = $db->prepare("SELECT *,
            (
                SELECT currency_code
                FROM currency
                WHERE currency_id = payment_currency_id
            ) AS currency_a,
            (
                SELECT currency_code
                FROM currency
                WHERE currency_id = payment_exchange_currency_id
            ) AS currency_b
            FROM `payment`
            INNER JOIN client ON client.client_id = payment.payment_client_id
            WHERE str_to_date( payment_date , '%Y-%m-%d' ) BETWEEN str_to_date( :start_date , '%Y-%m-%d' ) AND str_to_date( :end_date , '%Y-%m-%d' ) $query");
        $statement->bindParam(':start_date', $startDate);
        $statement->bindParam(':end_date', $endDate);
        $statement->execute();
        $total = array();
        $recordCollection =  $statement->fetchAll();
        foreach($recordCollection as $key=>$singleItem){
                $total[$singleItem[payment_currency_id]][ammount] += $singleItem[payment_amount];
                $total[$singleItem[payment_currency_id]][currency_code] = $singleItem[currency_a];
            $tempDate = DateTime::createFromFormat('Y-m-d', $singleItem[payment_date]);
            $recordCollection[$key][payment_date]  = $tempDate->format('d-m-Y');
        }
        $recordCollection[total] = $total;
        return $recordCollection;
    }

    private function printFileHeader($params)
    {
        $headerData = array();
        if (isset($params[start_date])){
            $headerData[]  = 'Период с : '.$params[start_date];
        }
        if (isset($params[end_date])){
            $headerData[]  = 'по : '.$params[end_date];
        }
        if (isset($params[client_id])){
            $ClientController = new ClientController();
            $clientData = $ClientController->getSingleClientData(array(client_id=>$params[client_id]));
            $headerData[]  = 'Пациент : '.$clientData[client_firstname].' '.$clientData[client_lastname];
        }
        if (isset($params[medicine_id])){
            $MedicineController = new MedicineController();
            $medicineData = $MedicineController->getMedicineByID(array(medicine_id=>$params[medicine_id]));
            $headerData[]  = 'Товар : '.$medicineData[medicine_name];
        }
        if (isset($params[medicine_manufacture_id])){
            $MedicineController = new MedicineController();
            $medicineData = $MedicineController->getMedicineCategoryByID(array(medicine_manufacture_id=>$params[medicine_manufacture_id]));
            $headerData[]  = 'Производитель : ' .$medicineData[medicine_manufacture_name];
        }
        if (isset($params[status]) && $params[status]!=''){
            $headerData[]  = 'Статус : '.$params[status];
        }
        $this->printer->writeLine($headerData);
    }

    private function calculateTotals($recordCollection,$additionalData = null)
    {
        $total = array();
        foreach($recordCollection as $key=>$singleItem){
            if ($additionalData[check_sale_status]==true){
                if ($singleItem[quantity]>0){
                    if ($singleItem[sale_status]=='paid'){
                        $total[$singleItem[currency_id]][paid][quantity] += $singleItem[quantity];
                        $total[$singleItem[currency_id]][paid][ammount]  += $singleItem[price] * $singleItem[quantity];
                        $total[$singleItem[currency_id]][paid][currency_code] = $singleItem[currency_code];
                    }
                    else if ($singleItem[sale_status]=='pending'){
                        $total[$singleItem[currency_id]][pending][quantity]  += $singleItem[quantity];
                        $total[$singleItem[currency_id]][pending][ammount]   += $singleItem[price] * $singleItem[quantity];
                        $total[$singleItem[currency_id]][pending][currency_code] = $singleItem[currency_code];
                    }
                }
                else{
                    $total[$singleItem[currency_id]][returned][quantity]  += $singleItem[quantity];
                    $total[$singleItem[currency_id]][returned][ammount]   += $singleItem[price] * $singleItem[quantity];
                    $total[$singleItem[currency_id]][returned][currency_code] = $singleItem[currency_code];
                }
            }
            else{
                $total[$singleItem[currency_id]][ammount] += $singleItem[price] * $singleItem[quantity];
                $total[$singleItem[currency_id]][currency_code] = $singleItem[currency_code];
            }
        }
        return $total;
    }
}