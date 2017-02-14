<?php

class MedicineController
{
    private $printer;

    function __construct()
    {
        $this->printer = new PrintView(array('file'=>'views/view_input.php'));
    }

    public function helloworld()
    {
        echo '<br/>medicine controller';
    }

    public  function getContentController()
    {
        if ($_GET[action]=='delete' && is_numeric($_GET[medicine_id])){
            $result[action_result] = $this->deleteMedicine($_GET);
            $fullQuantity = $this->getFullMedicineListNumber();
            $result[pagination] = getPagination($fullQuantity);
            $result[medicine_list] = $this->getMedicineList();
            $result[page_title] = 'Список Товаров';
            $this->printer->printHtml(array('medicine_list'),$result);
        }
        else if ($_GET[action]=='edit' && is_numeric($_GET[medicine_id])){
            if (isset($_POST[medicine_id]) && $_POST[medicine_id]>0){
                $action_result = $this->updateMedicine($_POST);
            }
            $result = $this->getMedicineByID($_GET);
            $result[action_result] = isset($_GET[action_result])?$_GET[action_result]:$action_result;
            $result[page_title] = 'Редактировать Товар';
            $result[country_list] = getCountryList();
            $result[currency_list] = getCurrencyList();
            $result[category_list] = $this->getMedicineCategoryList();
            $this->printer->printHtml(array('single_medicine'),$result);
        }
        else if ($_GET[action]=='new'){
            $result[medicine_code] = $_POST[medicine_code];
            $result[medicine_country] = getCountryByCode(substr($_POST[medicine_code]
                ,0,3));
            $result[page_title] = 'Новый Товар';
            $result[country_list] = getCountryList();
            $result[currency_list] = getCurrencyList();
            $result[category_list] = $this->getMedicineCategoryList();
            if (strlen($_GET[medicine_name])>3 || strlen($_POST[medicine_code])>3){
                $action_result = $this->checkIfMedicineExists($_POST);
                if ($action_result!=''){
                    $result[medicine_code]='';
                    $result[medicine_name]='';
                }
            }
            if (strlen($_GET[medicine_name])>3 && $_POST[medicine_quantity]>0 && $_POST[medicine_price]>0 && $action_result==''){
                $action_result = $this->saveMedicine($_POST);
            }
            $result[action_result] = $action_result;
            $this->printer->printHtml(array('single_medicine'),$result);
        }
        else if ($_GET[action]=='new_category'){
            $result[page_title] = 'Новый Производитель';
            if (strlen($_POST[medicine_manufacture_name])>=3){
                $result[action_result] = $this->saveMedicineCategory();
            }
            $result[category_list] = $this->getMedicineCategoryList();
            $this->printer->printHtml(array('single_medicine_category'),$result);
        }
        else if ($_GET[action]=='delete_category'){
            $result[page_title] = 'Новый Производитель';
            if ($_GET[medicine_manufacture_id]>0){
                $result[action_result] = $this->deleteMedicineCategory($_GET);
            }
            if (strlen($_POST[medicine_manufacture_name])>=3){
                $result[action_result] = $this->saveMedicineCategory();
            }
            $result[category_list] = $this->getMedicineCategoryList();
            $this->printer->printHtml(array('single_medicine_category'),$result);
        }
        else if ($_GET[action]=='edit_category'){
            $result[page_title] = 'Изменить Производитель';
            $result[category_data] = $this->getMedicineCategoryByID($_GET);
            $result[action_result] = $_GET[action_result];
            if ($_POST[medicine_manufacture_id]>0){
                $result[action_result] = $this->updateMedicineCategory($_POST);
                $result[category_data] = $this->getMedicineCategoryByID($_POST);

            }
            $this->printer->printHtml(array('single_medicine_category'),$result);
        }
        else{
            $result[page_title] = 'Список Товаров';
            $fullQuantity = $this->getFullMedicineListNumber();
            $result[pagination] = getPagination($fullQuantity);
            $result[medicine_list] = $this->getMedicineList();
            $this->printer->printHtml(array('medicine_list'),$result);
        }
    }

    public function getMedicineList()
    {
        global $db;
        $query = '';
        if (strlen($_GET[medicine_name])>0){
            $query .= (strlen($query)<3)?" WHERE medicine_name LIKE '%$_GET[medicine_name]%'":"  medicine_name LIKE '%$_GET[medicine_name]%'";
        }
        if (strlen($_POST[medicine_code])>0){
            $query .= (strlen($query)<3)?" WHERE medicine_code LIKE '%$_POST[medicine_code]%'":"   OR medicine_code LIKE '%$_POST[medicine_code]%'";
        }
        $limit = 'LIMIT '.(isset($_GET[page])?($_GET[page]-1)*ITEM_PER_PAGE : 0).', '. ITEM_PER_PAGE;
        $statement = $db->query("SELECT * FROM `medicine` INNER JOIN currency on medicine_currency_id = currency_id $query $limit");
        $medicine_list =  $statement->fetchAll();

        return $medicine_list;
    }

    public function getMedicineCategoryList()
    {
        global $db;
        $statement = $db->query("SELECT * FROM `medicine_manufacture`");
        $medicine_list =  $statement->fetchAll();

        return $medicine_list;
    }

    public function getFullMedicineList()
    {
        global $db;

        $statement = $db->query("SELECT * FROM `medicine` INNER JOIN currency where medicine_currency_id = currency_id ");
        $medicine_list =  $statement->fetchAll();

        return $medicine_list;
    }

    public function getFullMedicineListNumber()
    {
        global $db;
        $query = '';
        if (strlen($_GET[medicine_name])>0){
            $query .= (strlen($query)<3)?" WHERE medicine_name LIKE '%$_GET[medicine_name]%'":"  medicine_name LIKE '%$_GET[medicine_name]%'";
        }
        if (strlen($_POST[medicine_code])>0){
            $query .= (strlen($query)<3)?" WHERE medicine_code LIKE '%$_POST[medicine_code]%'":"   OR medicine_code LIKE '%$_POST[medicine_code]%'";
        }
        $statement = $db->query("SELECT count(*) FROM `medicine` $query");
        $rowCount = $statement->fetch();
        return $rowCount[0];
    }

    public function getMedicineByID($params)
    {
        global $db;
        $statement = $db->prepare("SELECT * FROM `medicine`
            INNER JOIN currency on medicine_currency_id = currency_id
            LEFT JOIN medicine_manufacture on medicine.medicine_manufacture_id = medicine_manufacture.medicine_manufacture_id
            WHERE medicine_id = :medicine_id");
        $statement->bindParam(':medicine_id', $params[medicine_id]);
        $statement->execute();
        $result = $statement->fetch();
        $result[custom_fields] = json_decode($result[medicine_other],true);
        $result[medicine_production_date] = DateTime::createFromFormat('Y-m-d', $result[medicine_production_date]);
        $result[medicine_production_date] = $result[medicine_production_date]->format('d-m-Y');
        return $result;
    }

    public function getMedicineCategoryByID($params)
    {
        global $db;
        $statement = $db->prepare("SELECT * FROM `medicine_manufacture` WHERE medicine_manufacture_id = :medicine_id");
        $statement->bindParam(':medicine_id', $params[medicine_manufacture_id]);
        $statement->execute();
        $result = $statement->fetch();
        return $result;
    }

    public function getMedicineBySearchResult($params = null)
    {
        global $db;
        $medicine_code = (isset($params[medicine_code]))?$params[medicine_code]:$_POST[medicine_code];
        $statement = $db->query("SELECT * FROM `medicine`
        INNER JOIN currency on medicine_currency_id = currency_id
        WHERE `medicine_code` LIKE '$medicine_code'");
        $result = $statement->fetch();
        $result[medicine_code] = $medicine_code;
        $result[medicine_country] = ($result[medicine_country]=='')?getCountryByCode(substr($medicine_code,0,3)):$result[medicine_country];
        $result[page_title] = ($result[medicine_name]=='')?'Новый Товар':$result[medicine_name];
        $result[custom_fields] = json_decode($result[medicine_other],true);
        if ($params[return_array]==true){
            return $result;
        }
        else{
            $result[country_list] = getCountryList();
            $result[currency_list] = getCurrencyList();
            $this->printer->printHtml(array('default_medicine_input'),$result);
        }
    }

    public function getMedicineListByName($params)
    {
        global $db;
        $statement = $db->query("SELECT * FROM `medicine`
            INNER JOIN currency ON medicine_currency_id = currency_id
            LEFT JOIN medicine_manufacture ON medicine_manufacture.medicine_manufacture_id = medicine.medicine_manufacture_id
            WHERE `medicine_name` LIKE '%$params[medicine_name]%' OR medicine_manufacture_name LIKE '%$params[medicine_name]%'");
        return $statement->fetchAll();
    }

    private function saveMedicine()
    {
        echo 'save';
        global $db,$siteData;
        $recordCollection = array();
        if (strlen($_POST['medicine_production_date'])>6){
            $date = DateTime::createFromFormat('d-m-Y', $_POST['medicine_production_date']);
            $_POST['medicine_production_date'] = $date->format('Y-m-d');
        }
        try{
            if (count($_POST[custom_field_name])>=1){
                $customFields = array();
                foreach($_POST[custom_field_name] as $key=>$value){
                    $customFields[$value] = $_POST[custom_field_value][$key];
                }
                $customFields = json_encode($customFields);
            }
            else{
                $customFields = '';
            }
            $statement = $db->prepare("INSERT INTO medicine (medicine_code, medicine_name, medicine_price,medicine_currency_id ,medicine_quantity, medicine_production_date, medicine_country, medicine_other, medicine_manufacture_id)
                VALUES(:medicine_code, :medicine_name, :medicine_price,:medicine_currency_id, :medicine_quantity, :medicine_production_date, :medicine_country, :medicine_other,:medicine_manufacture_id)");
            $statement->bindParam(':medicine_code', $_POST[medicine_code]);
            $statement->bindParam(':medicine_name', $_GET[medicine_name]);
            $statement->bindParam(':medicine_quantity', $_POST[medicine_quantity]);
            $statement->bindParam(':medicine_production_date', $_POST[medicine_production_date]);
            $statement->bindParam(':medicine_country', $_POST[medicine_country]);
            $statement->bindParam(':medicine_price', $_POST[medicine_price]);
            $statement->bindParam(':medicine_currency_id', $_POST[medicine_currency_id]);
            $statement->bindParam(':medicine_manufacture_id', $_POST[medicine_manufacture_id]);
            $statement->bindParam(':medicine_other', $customFields);

            $insertResult =  $statement->execute();
            $medicine_id = $db->lastInsertId();

            $statement = $db->prepare("INSERT INTO medicine_income (medicine_income_medicine_id,medicine_income_price,medicine_income_quantity,medicine_income_date)
                VALUES(:medicine_id,:medicine_price,:medicine_quantity,:today)");
            $today =  date('Y-m-d');
            $statement->bindParam(':medicine_id',$medicine_id);
            $statement->bindParam(':medicine_quantity', $_POST[medicine_quantity]);
            $statement->bindParam(':medicine_price', $_POST[medicine_price]);
            $statement->bindParam(':today', $today);

            $insertResult = $statement->execute();

            if ($insertResult==true){
                header( 'Location: '.$siteData['url'].'/input?action=edit&action_result=insert&medicine_id=' . $medicine_id) ;
            }
        }
        catch(PDOException $ex) {
            echo 'exception:'. $ex;
            $insertResult = 'fail';
        }
        return $insertResult;
    }

    private function saveMedicineCategory()
    {
        global $db,$siteData;
        try{
            $statement = $db->prepare("SELECT * FROM `medicine_manufacture` WHERE `medicine_manufacture_name` = :name");
            $statement->bindParam(':name', $_POST[medicine_manufacture_name]);
            $result = $statement->fetch();
            if (count($result)>0 && $result!=false){
                return 'code_exists';
            }

            $statement = $db->prepare("INSERT INTO medicine_manufacture (medicine_manufacture_name)
                VALUES(:name)");
            $statement->bindParam(':name', $_POST[medicine_manufacture_name]);

            $insertResult =  $statement->execute();
            $medicine_id = $db->lastInsertId();
            if ($insertResult==true){
                //header( 'Location: '.$siteData['url'].'/input?action=edit_category&action_result=insert&medicine_manufacture_id=' . $medicine_id) ;
            }
        }
        catch(PDOException $ex) {
            echo 'exception:'. $ex;
            $insertResult = 'fail';
        }
        return $insertResult;
    }

    private function deleteMedicine($params)
    {
        global $db;
        $statement = $db->prepare("DELETE FROM medicine WHERE medicine_id = :medicine_id");
        $statement->bindParam(':medicine_id', $params[medicine_id]);
        $insertResult = $statement->execute();
        return $insertResult==true?'delete_ok':'delete_fail';
    }

    private function deleteMedicineCategory($params)
    {
        global $db;
        $statement = $db->prepare("DELETE FROM medicine_manufacture WHERE medicine_manufacture_id = :medicine_id");
        $statement->bindParam(':medicine_id', $params[medicine_manufacture_id]);
        $insertResult = $statement->execute();
        return $insertResult==true?'delete_ok':'delete_fail';
    }

    private function updateMedicine($params)
    {
        echo 'update';
        global $db;
        try{
            if (strlen($params['medicine_production_date'])>6){
                $date = DateTime::createFromFormat('d-m-Y', $params['medicine_production_date']);
                $params['medicine_production_date'] = $date->format('Y-m-d');
            }
            if (count($params[custom_field_name])>=1){
                $customFields = array();
                foreach($params[custom_field_name] as $key=>$value){
                    $customFields[$value] = $params[custom_field_value][$key];
                }
                $customFields = json_encode($customFields);
            }
            else{
                $customFields = '';
            }

            $statement = $db->prepare("UPDATE medicine set medicine_code=:medicine_code, medicine_name=:medicine_name, medicine_price=:medicine_price, medicine_currency_id =:medicine_currency_id, medicine_quantity=medicine_quantity +:medicine_quantity, medicine_production_date=:medicine_production_date,medicine_country=:medicine_country, medicine_other = :medicine_other, medicine_manufacture_id=:medicine_manufacture_id WHERE medicine_id = :medicine_id");

            $statement->bindParam(':medicine_code', $params[medicine_code]);
            $statement->bindParam(':medicine_name', $params[medicine_name]);
            $statement->bindParam(':medicine_quantity', $params[medicine_quantity]);
            $statement->bindParam(':medicine_production_date', $params[medicine_production_date]);
            $statement->bindParam(':medicine_country', $params[medicine_country]);
            $statement->bindParam(':medicine_price', $params[medicine_price]);
            $statement->bindParam(':medicine_currency_id', $params[medicine_currency_id]);
            $statement->bindParam(':medicine_manufacture_id', $params[medicine_manufacture_id]);
            $statement->bindParam(':medicine_other', $customFields);
            $statement->bindParam(':medicine_id', $params[medicine_id]);

            $updateResult =  $statement->execute();
            $statement = $db->prepare("INSERT INTO medicine_income (medicine_income_medicine_id,medicine_income_price,medicine_income_currency_id,medicine_income_quantity,medicine_income_date)
                VALUES(:medicine_id,:medicine_price,:medicine_currency_id,:medicine_quantity,:today)");
            $today =  date('Y-m-d');
            $statement->bindParam(':medicine_id', $params[medicine_id]);
            $statement->bindParam(':medicine_quantity', $params[medicine_quantity]);
            $statement->bindParam(':medicine_price', $params[medicine_price]);
            $statement->bindParam(':medicine_currency_id', $params[medicine_currency_id]);
            $statement->bindParam(':today', $today);

            $updateResult =  $statement->execute();

            return ( $updateResult==true)?'update':'fail';
        }
        catch(PDOException $ex) {
            return 'fail';
        }
        return 'fail';
    }

    private function updateMedicineCategory($params)
    {
        echo 'update';
        global $db;
        try{
            $statement = $db->prepare("UPDATE medicine_manufacture set medicine_manufacture_name=:medicine_name WHERE medicine_manufacture_id = :medicine_id");
            $statement->bindParam(':medicine_name', $params[medicine_manufacture_name]);
            $statement->bindParam(':medicine_id', $params[medicine_manufacture_id]);

            $updateResult =  $statement->execute();

            return ( $updateResult==true)?'update':'fail';
        }
        catch(PDOException $ex) {
            return 'fail';
        }
        return 'fail';
    }

    private function getMedicineByCode($params=null)
    {
        global $db;
        $statement = $db->query("SELECT * FROM `medicine`
            INNER JOIN currency on medicine.medicine_currency_id = currency.currency_id
            WHERE `medicine_code` LIKE '$params[medicine_code]'");
        $result = $statement->fetch();
        $result[medicine_code] = $params[medicine_code];
        $result[medicine_country] = ($result[medicine_country]=='') ? getCountryByCode(substr($params[medicine_code],0,3)):$result[medicine_country];
        if ($params[action]=='update'){
            $result[page_title] = $result[medicine_name];
        }
        else{
            $result[page_title] = ($result[medicine_name]=='')?'Новый Товар':$result[medicine_name];
        }
        $result[country_list] = getCountryList();
        $result[currency_list] = getCurrencyList();
        $result[custom_fields] = json_decode($result[medicine_other],true);
        $result[action_result] = $params[action_result];
        $this->printer->printHtml(array('default_medicine_input'),$result);
    }

    private function checkIfMedicineExists($params)
    {
        global $db;
        if (strlen($params[medicine_code])>3){
            $statement = $db->prepare("SELECT * FROM `medicine`
                INNER JOIN currency on medicine.medicine_currency_id = currency.currency_id
                WHERE `medicine_code` = :medicine_code");
            $statement->bindParam(':medicine_code', $params[medicine_code]);
            $statement->execute();
            $result = $statement->fetch();
            if (count($result)>1)
                return 'code_exists';

        }
        if (strlen($params[medicine_name])>3){
            $statement = $db->prepare("SELECT * FROM `medicine`
                INNER JOIN currency on medicine.medicine_currency_id = currency.currency_id
                WHERE `medicine_name` = :medicine_name");
            $statement->bindParam(':medicine_name', $params[medicine_name]);
            $statement->execute();
            $result = $statement->fetch();
            if (count($result)>1)
                return 'name_exists';

        }
        return '';
    }
}