<?php

class ClientController
{
	private $clientID;
	private $clientData;
	
	public function __construct($params = null)
	{
		if (isset($params['clientID'])){
			$this->clientID = $params['clientID'];
			$this->clientData = $this->getSingleClientData($params);
		}
	}
	
	public function getContentController($params)
	{
		if (isset($params['client_id'])){
			if ($_GET['action']=='delete'){
				$returnValue['action_result'] = $this->deleteClient($params);
				$data = $this->getClientListData($params);
				return array_merge($data,$returnValue);
			}
			else if ($_GET['action']=='edit'){
				$returnValue = array();
				if (count($_POST)>2){
					if (is_numeric($_POST['client_id']) && $_POST['client_id']>0){
						$returnValue['action_result'] = $this->editClient($_POST);
					}
				}
				if (isset($params['action_result']) && $params['action_result']=='true'){
					$returnValue['action_result'] = true;
				}
				$data = $this->getSingleClientData($params);
				return array_merge($data,$returnValue);
			}
		}
		else if (isset($_GET['action']) && $_GET['action']=='new'){
			if (count($_POST)>2){
				
				$returnValue['action_result'] = $this->createClient($_POST);
			}
		}
		else{
			return $this->getClientListData($params);
		}
	}
	
	public function getSingleClientData($params)
	{
		global $db;
		$recordCollection = array();
		$statement = $db->prepare("SELECT * FROM client
		  WHERE client_id = :client_id");
		$statement->bindParam(':client_id', $params['client_id']); 
		$statement->execute();
        $this->clientData =  $statement->fetch();
		if (strlen($this->clientData['client_birthday'])>6){
			$birthday = DateTime::createFromFormat('Y-m-d', $this->clientData['client_birthday']);
			$this->clientData['client_birthday'] = $birthday->format('d-m-Y');
		}
        $statement = $db->prepare("SELECT * FROM client_balance
		    INNER JOIN currency on client_balance.client_balance_currency_id = currency.currency_id
		    WHERE client_balance_client_id = :client_id");
        $statement->bindParam(':client_id', $params['client_id']);
        $statement->execute();
        $tempArray =  $statement->fetchAll();
        if (count($tempArray)>0){
            $this->clientData[balance] = $tempArray;
        }
		return $this->clientData;
	}
	
	public function getClientListData($params = null)
	{
		global $db;
		$recordCollection = array();

        //LEFT JOIN client_balance on client.client_id = client_balance.client_balance_client_id
		//LEFT JOIN currency on client_balance.client_balance_currency_id = currency.currency_id

        $statement = $db->query("SELECT * FROM client
		    ORDER BY client_firstname");
		while($row = $statement->fetch()) {
			$recordCollection[] = $row;
		}
		return $recordCollection;
	}
	
	private function deleteClient($params)
	{
		global $db;
		$statement = $db->prepare("DELETE FROM client WHERE client_id = :client_id");
		$statement->bindParam(':client_id', $params['client_id']);
        $statement->execute();
        $statement = $db->prepare("DELETE FROM client_balance WHERE client_balance_client_id = :client_id");
        $statement->bindParam(':client_id', $params['client_id']);
        return $statement->execute();
	}
	
	private function editClient($params)
	{
		global $db;
		$recordCollection = array();
		if (strlen($params['client_birthday'])>6){
			$birthday = DateTime::createFromFormat('d-m-Y', $params['client_birthday']);
			$params['client_birthday'] = $birthday->format('Y-m-d');
		}
		try{


            $statement = $db->query("UPDATE client SET 
                client_firstname = '$params[client_firstname]' ,
                client_lastname = '$params[client_lastname]' ,
                client_phone = '$params[client_phone]' ,
                client_email = '$params[client_email]' ,
                client_address = '$params[client_address]' ,
                client_company = '$params[client_company]' ,
                client_birthday = '$params[client_birthday]'
            WHERE client_id = $params[client_id]");
            
            $result = $statement->execute(); 
            //saving manual balance change
            if (isset($params['client_balance_balance']) && count($params['client_balance_balance']) > 0){
                $clientData= $this->getSingleClientData(array(client_id=>$params[client_id]));
                foreach($params['client_balance_balance'] as $key=>$value){
                    foreach($clientData['balance'] as $innerKey => $currentBalance){
                        if (abs($params['currency_id'][$key] - $currentBalance['currency_id']) < 0.01 && 
                            (abs($params['client_balance_balance'][$key] - $currentBalance['client_balance_balance']) > 0.01 ||
                            abs($params['client_balance_debt'][$key] - $currentBalance['client_balance_debt']) > 0.01)
                            ){
                            $statement = $db->prepare("INSERT INTO balance_change
                                (client_id, balance_was, balance_became, debt_was, debt_became, currency_id, `date`)
                                VALUES 
                                (:client_id, :balance_was, :balance_became, :debt_was, :debt_became, :currency_id, :date)");

                            $statement->bindParam(':client_id', $params['client_id']);
                            $statement->bindParam(':balance_was', $currentBalance['client_balance_balance']);
                            $statement->bindParam(':balance_became', $params['client_balance_balance'][$key]);
                            $statement->bindParam(':debt_was', $currentBalance['client_balance_debt']);
                            $statement->bindParam(':debt_became', $params['client_balance_debt'][$key]);
                            $statement->bindParam(':currency_id', $params['currency_id'][$key]);
                            $statement->bindParam(':date', date('Y-n-d H:i:s'));

                            $insertResult =  $statement->execute();

                            $statement = $db->prepare("UPDATE client_balance SET 
                                client_balance_debt = :client_balance_debt,
                                client_balance_balance = :client_balance_balance
                                WHERE client_balance_client_id = :client_balance_client_id AND client_balance_currency_id = :client_balance_currency_id");

                            $statement->bindParam(':client_balance_balance', $params['client_balance_balance'][$key]);
                            $statement->bindParam(':client_balance_debt', $params['client_balance_debt'][$key]);
                            $statement->bindParam(':client_balance_client_id', $params['client_id']);
                            $statement->bindParam(':client_balance_currency_id', $params['currency_id'][$key]);

                            $insertResult =  $statement->execute();
                        }
                    }
                }
            }
			return $result;
		}
		catch(PDOException $ex) {
			echo "An Error occured!";
			echo $ex->getMessage();
		}
		return false;
	}
	
	private function createClient($params)
	{
		global $db,$siteData;
		if (strlen($params['client_birthday'])>6){
			$birthday = DateTime::createFromFormat('d-m-Y', $params['client_birthday']);
			$params['client_birthday'] = $birthday->format('Y-m-d');
		}
		try{
			$statement = $db->query("INSERT INTO client
				(client_firstname, client_lastname, client_phone, client_email, client_address, client_company, client_birthday)
				VALUES ('$params[client_firstname]' ,'$params[client_lastname]' ,'$params[client_phone]' ,'$params[client_email]' ,'$params[client_address]' ,'$params[client_company]' ,'$params[client_birthday]')");
			
			if ($db->lastInsertId()>0){
				header( 'Location: '.$siteData['url'].'/client?action=edit&action_result=true&client_id=' . $db->lastInsertId()) ;
			}
		}
		catch(PDOException $ex) {
			echo "An Error occured!";
			echo $ex->getMessage();
		}
		return false;
		
	}

    public function updateClientBalance($params)
    {
        global $db;
        $clientData= $this->getSingleClientData(array(client_id=>$params[client_id]));
        try{
            $balance = 0;
            //calculate balance
            if (count($clientData[client_balance])>0){
                foreach($clientData[client_balance] as $singleBalance){
                    if ($params[balance_checkbox][$singleBalance[client_balance_currency_id]]=='on'){
                        $balance += $singleBalance[client_balance_balance]*$params[balance_exchange][$singleBalance[client_balance_currency_id]] - $singleBalance[client_balance_debt]*$params[balance_exchange][$singleBalance[client_balance_currency_id]];
                    }
                }
            }
            $params[payment] = ($params[payment]=='')?0:$params[payment];
            if ($params[exchange_checkbox]=='on'){
                $params[payment] = $params[payment] * $params[exchange_rate];
            }
            //not paid
            if ($params[price] * $params[quantity] > $params[payment]+$balance){
                $newDebt = $params[price] * $params[quantity];
                $newBalance = $params[payment];
                $num_rows = $this->checkIfBalanceExists(array(client_id=>$params[client_id],currency=>$params[medicine_currency_id]));
                if ($num_rows==1){
                    $statement = $db->prepare("UPDATE client_balance SET client_balance_balance = client_balance_balance + :new_balance, client_balance_debt = client_balance_debt + :new_debt
                    WHERE client_balance_client_id = :client_id AND client_balance_currency_id = :currency");
                    $statement->bindParam(':new_balance', $newBalance);
                    $statement->bindParam(':new_debt', $newDebt);
                    $statement->bindParam(':client_id', $params[client_id]);
                    $statement->bindParam(':currency', $params[medicine_currency_id]);
                    $insertResult =  $statement->execute();
                }
                else if ($num_rows==0){
                    $statement = $db->prepare("INSERT INTO client_balance
                      (client_balance_client_id, client_balance_currency_id, client_balance_balance, client_balance_debt) VALUES
                      (:client_balance_client_id,:client_balance_currency_id,:client_balance_balance,:client_balance_debt)");
                    $statement->bindParam(':client_balance_client_id', $params[client_id]);
                    $statement->bindParam(':client_balance_currency_id', $params[medicine_currency_id]);
                    $statement->bindParam(':client_balance_balance', $newBalance);
                    $statement->bindParam(':client_balance_debt', $newDebt);
                    $insertResult =  $statement->execute();
                }
                return 'pending';
            }
            //paid
            else if ($params[price] * $params[quantity] <= $params[payment]+$balance){

                $newBalance = ($params[payment]+$balance) - ($params[price] * $params[quantity]);
                $statement = $db->prepare("SELECT * FROM client_balance WHERE client_balance_client_id = :client_id AND client_balance_currency_id = :currency_id");
                $statement->bindParam(':client_id', $params[client_id]);
                $statement->bindParam(':currency_id', $params[medicine_currency_id]);
                $statement->execute();
                $rows = $statement->fetchAll();
                $num_rows = count($rows);
                if ($num_rows===1){
                    $statement = $db->prepare("UPDATE client_balance SET client_balance_balance = client_balance_balance + :new_balance
                    WHERE client_balance_client_id = :client_id AND client_balance_currency_id = :currency");
                    $statement->bindParam(':new_balance', $newBalance);
                    $statement->bindParam(':client_id', $params[client_id]);
                    $statement->bindParam(':currency', $params[medicine_currency_id]);
                    $insertResult =  $statement->execute();
                }
                else if ($num_rows===0){
                    $statement = $db->prepare("INSERT INTO client_balance
                      (client_balance_client_id, client_balance_currency_id, client_balance_balance) VALUES
                      (:client_balance_client_id,:client_balance_currency_id,:client_balance_balance)");
                    $statement->bindParam(':client_balance_client_id', $params[client_id]);
                    $statement->bindParam(':client_balance_currency_id', $params[medicine_currency_id]);
                    $statement->bindParam(':client_balance_balance', $newBalance);
                    $insertResult =  $statement->execute();
                }
                return 'paid';
            }

            /*
             * backup dont' remove.
             *
             * if ($params[price] * $params[quantity] > $params[payment]+$clientData[client_balance]){
                //not paid
                $new_debt = $params[price] * $params[quantity];
                $new_balance = $params[payment];

                $statement = $db->prepare("UPDATE client SET client_balance = client_balance+ :new_balance, client_debt = client_debt + :new_debt
                    WHERE client_id = :client_id");
                $statement->bindParam(':new_balance',$new_balance);
                $statement->bindParam(':new_debt', $new_debt);
                $statement->bindParam(':client_id', $params[client_id]);
                $statement->execute();
                return 'pending';
            }
            else if ($params[price] * $params[quantity] <= $params[payment]+$clientData[client_balance]){
                //unpaid
                $new_balance = ($params[payment]+$clientData[client_balance]) - ($params[price] * $params[quantity]);

                $statement = $db->prepare("UPDATE client SET client_balance = :new_balance WHERE client_id = :client_id");
                $statement->bindParam(':new_balance', $new_balance);
                $statement->bindParam(':client_id', $params[client_id]);
                $statement->execute();
                return 'paid';
            }*/
        }
        catch(PDOException $ex) {
            echo "An Error occured!";
            echo $ex->getMessage();
            return false;
        }
        return false;

    }

    public function increaseClientBalance($params)
    {
        global $db;
        try{
            if (is_numeric($params[balance]) && $params[balance]>0){
                $num_rows = $this->checkIfBalanceExists($params);
                if ($num_rows==1){
                    $statement = $db->prepare("UPDATE client_balance SET client_balance_balance = client_balance_balance + :new_balance
                    WHERE client_balance_client_id = :client_id AND client_balance_currency_id = :currency");
                    $statement->bindParam(':new_balance', $params[balance]);
                    $statement->bindParam(':client_id', $params[client_id]);
                    $statement->bindParam(':currency', $params[currency]);
                    $insertResult =  $statement->execute();
                    return $insertResult==true?'balance_paid':'balance_fail';
                }
                else if ($num_rows==0){
                    $statement = $db->prepare("INSERT INTO client_balance
                      (client_balance_client_id, client_balance_currency_id, client_balance_balance) VALUES
                      (:client_balance_client_id,:client_balance_currency_id,:client_balance_balance)");
                    $statement->bindParam(':client_balance_client_id', $params[client_id]);
                    $statement->bindParam(':client_balance_currency_id', $params[currency]);
                    $statement->bindParam(':client_balance_balance', $params[balance]);
                    $insertResult =  $statement->execute();
                    return $insertResult==true?'balance_paid':'balance_fail';
                }
                else{
                    return 'balance_fail';
                }

            }
        }
        catch(PDOException $ex) {
            echo "An Error occured!";
            echo $ex->getMessage();
            return 'balance_fail';
        }
        return 'balance_fail';
    }

    public function decreaseClientDebt($params)
    {
        global $db;
        try{
            if (is_numeric($params[debt]) && $params[debt]>0){
                if ($this->checkIfBalanceExists(array(client_id=>$params[client_id],currency=>$params[currency_id]))){
                    $statement = $db->prepare("UPDATE client_balance SET client_balance_debt = client_balance_debt - :debt
                      WHERE client_balance_client_id = :client_id AND client_balance_currency_id = :currency_id");
                    $statement->bindParam(':client_id', $params[client_id]);
                    $statement->bindParam(':currency_id', $params[currency_id]);
                    $statement->bindParam(':debt', $params[debt]);
                    $insertResult =  $statement->execute();
                }
                else{
                    $statement = $db->prepare("INSERT INTO client_balance
                  (client_balance_client_id, client_balance_currency_id, client_balance_debt) VALUES
                  (:client_id,:currency_id,:debt)");
                    $statement->bindParam(':client_id', $params[client_id]);
                    $statement->bindParam(':currency_id', $params[currency_id]);
                    $statement->bindParam(':debt', $params[debt]);
                    $insertResult =  $statement->execute();
                }
                return $insertResult==true?'ok':'fail';
            }
        }
        catch(PDOException $ex) {
            echo "An Error occured!";
            echo $ex->getMessage();
            return 'fail';
        }
        return 'fail';
    }

	public function helloworld()
	{
		echo '<br/>client controller';
	}

    private function checkIfBalanceExists($params)
    {
        global $db;
        $statement = $db->prepare("SELECT * FROM client_balance WHERE client_balance_client_id = :client_id AND client_balance_currency_id = :currency_id");
        $statement->bindParam(':client_id', $params[client_id]);
        $statement->bindParam(':currency_id', $params[currency]);
        $statement->execute();
        $rows = $statement->fetchAll();
        return count($rows)>0?true:false;
    }
}