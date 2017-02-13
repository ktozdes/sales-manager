
<div class="row">
<?php if ($section =='report_navigation'):?>
        <ul class="nav nav-tabs" role="tablist">
            <li <?php echo ($_GET[reportby]=='client')?'class="active"':'';?>><a href="<?php echo $siteData['url'].'/report?reportby=client'?>">Покупки</a></li>
            <li <?php echo ($_GET[reportby]=='sale_transaction')?'class="active"':'';?>><a href="<?php echo $siteData['url'].'/report?reportby=sale_transaction'?>">Оплата/Возврат</a></li>
            <li <?php echo ($_GET[reportby]=='income')?'class="active"':'';?>><a href="<?php echo $siteData['url'].'/report?reportby=income'?>">Поступление Товаров</a></li>
            <li <?php echo ($_GET[reportby]=='payment')?'class="active"':'';?>><a href="<?php echo $siteData['url'].'/report?reportby=payment'?>">Пополнение Баланса</a></li>
        </ul>

<?php elseif($section =='report_client'):?>
    <div class="col-sm-12">
        <h3 class="page-header">Покупки Товаров</h3>
        <form method="post">
            <table class="table table-striped table-responsive table-hover table-condensed">
                <tbody>
                <tr>
                    <td>Период с : <input type="text" style="display:inline;width:auto;" name="start_date" class="datepicker form-control" value="<?php echo $_POST[start_date];?>"/></td>
                    <td> по : <input type="text" style="display:inline;width:auto;" name="end_date" class="datepicker form-control" value="<?php echo isset($_POST[end_date])?$_POST[end_date]:date('d-m-Y');?>"/></td>
                    <td> Товар <select name="medicine_id" style="display:inline;width:auto;" class="form-control">
                            <option value="" <?php echo !isset($_POST[medicine_id]) || $_POST[medicine_id]==''?'selected="selected"':'';?>>Все</option>
                            <?php foreach($result[medicine_list] as $singleMedicine):?>

                                <option value="<?php echo $singleMedicine[medicine_id];?>" <?php echo $_POST[medicine_id]==$singleMedicine[medicine_id] ? 'selected="selected"':'';?>><?php echo $singleMedicine[medicine_name];?></option>
                            <?php endforeach;?>
                        </select></td>
                    <td> Производитель <select name="medicine_manufacture_id" style="display:inline;width:auto;" class="form-control">
                            <option value="" <?php echo !isset($_POST[medicine_manufacture_id]) || $_POST[medicine_manufacture_id]==''?'selected="selected"':'';?>>Все</option>
                            <?php foreach($result[medicine_category_list] as $singleMedicine):?>

                                <option value="<?php echo $singleMedicine[medicine_manufacture_id];?>" <?php echo $_POST[medicine_manufacture_id]==$singleMedicine[medicine_manufacture_id] ? 'selected="selected"':'';?>><?php echo $singleMedicine[medicine_manufacture_name];?></option>
                            <?php endforeach;?>
                        </select></td>
                    <td> Клиент <select name="client_id" style="display:inline;width:auto;" class="form-control">
                            <option value="" <?php echo !isset($_POST[client_id]) || $_POST[client_id]==''?'selected="selected"':'';?>>Все</option>
                            <?php foreach($result[client_list] as $singleMedicine):?>

                                <option value="<?php echo $singleMedicine[client_id];?>" <?php echo $_POST[client_id]==$singleMedicine[client_id] ? 'selected="selected"':'';?>><?php echo $singleMedicine[client_firstname] .' '.$singleMedicine[client_lastname];?></option>
                            <?php endforeach;?>
                        </select></td>
                    <td> Статус <select name="status" style="display:inline;width:auto;" class="form-control">
                            <option value="" <?php echo !isset($_POST[status]) || $_POST[status]==''?'selected="selected"':'';?>>Все</option>
                            <option value="paid" <?php echo $_POST[status]=='paid' ? 'selected="selected"':'';?>>Оплаченные</option>
                            <option value="pending" <?php echo $_POST[status]=='returned' ? 'selected="selected"':'';?>>Не Оплаченные</option>
                        </select></td>
                    <td><input type="submit" class="btn btn-default" value="OK"/></td>
                </tr>
                </tbody>
            </table>
        </form>
        <?php if (count($result[search_result])>0):?>
            <table class="table table-striped table-responsive table-hover table-condensed">
                <tbody>
                <tr>
                    <th>Дата</th>
                    <th>Название Товара</th>
                    <th>Производитель</th>
                    <th>Клиент</th>
                    <th>Количество</th>
                    <th>Цена</th>
                    <th>Сумма</th>
                    <th>Оплачено</th>
                </tr>
                <?php foreach($result[search_result] as $singleSales):?>
                    <tr>
                        <td><?php echo $singleSales[sale_date];?></td>
                        <td><?php echo $singleSales[medicine_name];?></td>
                        <td><?php echo $singleSales[medicine_manufacture_name];?></td>
                        <td><?php echo $singleSales[client_firstname].' '.$singleSales[client_lastname];?></td>
                        <td><?php echo $singleSales[sale_quantity];?></td>
                        <td><?php echo number_format($singleSales[sale_price],2,'.',' ').' '.$singleSales[currency_code];?></td>
                        <td><?php echo number_format($singleSales[sale_price] * $singleSales[sale_quantity],2,'.',' ').' '.$singleSales[currency_code];?></td>
                        <td><?php echo $singleSales[sale_status]=='paid'?'<div class="alert-success" role="alert">Оплачено</div>':'<div class="alert-danger" role="alert">Не Оплачено</div>';?></td>
                    </tr>
                <?php endforeach;?>
                <?php foreach($result[total] as $singleTotal):?>
                    <tr  class="success">
                        <td colspan="2"><strong>Итого Оплачено</strong></td>
                        <td colspan="3"><strong>Количество :</strong> <?php echo $singleTotal[paid][quantity];?></td>
                        <td colspan="3"><strong>Сумма :</strong> <?php echo number_format($singleTotal[paid][ammount],2,'.',' ').' '.$singleTotal[paid][currency_code];?></td>
                    </tr>
                    <tr  class="danger">
                        <td colspan="2"><strong>Итого Не Оплачено</strong></td>
                        <td colspan="3"><strong>Количество :</strong> <?php echo $singleTotal[pending][quantity];?></td>
                        <td colspan="3"><strong>Сумма :</strong> <?php echo number_format($singleTotal[pending][ammount],2,'.',' ').' '.$singleTotal[paid][currency_code];?></td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
            <a href="<?php echo $result['save_url'];?>" class="btn btn-warning">Сохранить</a>
        <?php endif;?>
    </div>

<?php elseif($section =='report_sale_transaction'):?>
    <div class="col-sm-12">
        <h3 class="page-header">Оплата/Возврат Товаров</h3>
        <form method="post">
            <table class="table table-striped table-responsive table-hover table-condensed">
                <tbody>
                <tr>
                    <td>Период с : <input type="text" style="display:inline;width:auto;" name="start_date" class="datepicker form-control" value="<?php echo $_POST[start_date];?>"/></td>
                    <td> по : <input type="text" style="display:inline;width:auto;" name="end_date" class="datepicker form-control" value="<?php echo isset($_POST[end_date])?$_POST[end_date]:date('d-m-Y');?>"/></td>
                    <td> Товар <select name="medicine_id" style="display:inline;width:auto;" class="form-control">
                            <option value="" <?php echo !isset($_POST[medicine_id]) || $_POST[medicine_id]==''?'selected="selected"':'';?>>Все</option>
                            <?php foreach($result[medicine_list] as $singleMedicine):?>

                                <option value="<?php echo $singleMedicine[medicine_id];?>" <?php echo $_POST[medicine_id]==$singleMedicine[medicine_id] ? 'selected="selected"':'';?>><?php echo $singleMedicine[medicine_name];?></option>
                            <?php endforeach;?>
                        </select></td>
                    <td> Производитель <select name="medicine_manufacture_id" style="display:inline;width:auto;" class="form-control">
                            <option value="" <?php echo !isset($_POST[medicine_manufacture_id]) || $_POST[medicine_manufacture_id]==''?'selected="selected"':'';?>>Все</option>
                            <?php foreach($result[medicine_category_list] as $singleMedicine):?>

                                <option value="<?php echo $singleMedicine[medicine_manufacture_id];?>" <?php echo $_POST[medicine_manufacture_id]==$singleMedicine[medicine_manufacture_id] ? 'selected="selected"':'';?>><?php echo $singleMedicine[medicine_manufacture_name];?></option>
                            <?php endforeach;?>
                        </select></td>
                    <td> Клиент <select name="client_id" style="display:inline;width:auto;" class="form-control">
                            <option value="" <?php echo !isset($_POST[client_id]) || $_POST[client_id]==''?'selected="selected"':'';?>>Все</option>
                            <?php foreach($result[client_list] as $singleMedicine):?>

                                <option value="<?php echo $singleMedicine[client_id];?>" <?php echo $_POST[client_id]==$singleMedicine[client_id] ? 'selected="selected"':'';?>><?php echo $singleMedicine[client_firstname] .' '.$singleMedicine[client_lastname];?></option>
                            <?php endforeach;?>
                        </select></td>
                    <td> Статус <select name="status" style="display:inline;width:auto;" class="form-control">
                            <option value="" <?php echo !isset($_POST[status]) || $_POST[status]==''?'selected="selected"':'';?>>Все</option>
                            <option value="paid" <?php echo $_POST[status]=='paid' ? 'selected="selected"':'';?>>Оплата</option>
                            <option value="returned" <?php echo $_POST[status]=='returned' ? 'selected="selected"':'';?>>Возврат</option>
                        </select></td>
                    <td><input type="submit" class="btn btn-default" value="OK"/></td>
                </tr>
                </tbody>
            </table>
        </form>
        <?php 
		if (count($result[search_result])>0):?>
            <table class="table table-striped table-responsive table-hover table-condensed">
                <tbody>
                <tr>
                    <th>Дата</th>
                    <th>Название Товара</th>
                    <th>Производитель</th>
                    <th>Клиент</th>
                    <th>Количество</th>
                    <th>Цена</th>
                    <th>Сумма</th>
                    <th>Трансакция</th>
                </tr>
                <?php foreach($result[search_result] as $singleSales):?>
                    <tr>
                        <td><?php echo $singleSales[sale_transaction_date];?></td>
                        <td><?php echo $singleSales[medicine_name];?></td>
                        <td><?php echo $singleSales[medicine_manufacture_name];?></td>
                        <td><?php echo $singleSales[client_firstname].' '.$singleSales[client_lastname];?></td>
                        <td><?php echo $singleSales[sale_transaction_quantity];?></td>
                        <td><?php echo number_format($singleSales[sale_transaction_price],2,'.',' ').' '.$singleSales[currency_code];?></td>
                        <td><?php echo number_format($singleSales[sale_transaction_price] * $singleSales[sale_transaction_quantity],2,'.',' ').' '.$singleSales[currency_code];?></td>
                        <td><?php if ($singleSales[sale_transaction_quantity]>0){
							if ($singleSales[sale_status]=='pending'){
								echo '<div class="alert-warning" role="alert">Не Оплачено</div>';
							}
							else if ($singleSales[sale_status]=='paid'){
								echo '<div class="alert-success" role="alert">Оплачено</div>';
							}
						}
						else{
							echo '<div class="alert-danger" role="alert">Возвращено</div>';
						}?></td>
                    </tr>
                <?php endforeach;?>


                <?php foreach($result[total] as $singleTotal):?>
                    <tr  class="success">
                        <td colspan="2"><strong>Итого Оплачено</strong></td>
                        <td colspan="3"><strong>Количество :</strong> <?php echo $singleTotal[paid][quantity];?></td>
                        <td colspan="3"><strong>Сумма :</strong> <?php echo number_format($singleTotal[paid][ammount],2,'.',' ').' '.$singleTotal[paid][currency_code];?></td>
                    </tr>
                    <tr  class="danger">
                        <td colspan="2"><strong>Итого Не Оплачено</strong></td>
                        <td colspan="3"><strong>Количество :</strong> <?php echo $singleTotal[pending][quantity];?></td>
                        <td colspan="3"><strong>Сумма :</strong> <?php echo number_format($singleTotal[pending][ammount],2,'.',' ').' '.$singleTotal[pending][currency_code];?></td>
                    </tr>
                    <tr  class="warning">
                        <td colspan="2"><strong>Итого Возвращено</strong></td>
                        <td colspan="3"><strong>Количество :</strong> <?php echo $singleTotal[returned][quantity];?></td>
                        <td colspan="3"><strong>Сумма :</strong> <?php echo number_format($singleTotal[returned][ammount],2,'.',' ').' '.$singleTotal[returned][currency_code];?></td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
            <a href="<?php echo $result['save_url'];?>" class="btn btn-warning">Сохранить</a>
        <?php endif;?>
    </div>


<?php elseif($section =='report_income'):?>
    <div class="col-sm-12">
        <h3 class="page-header">Поступление Товаров</h3>
        <form method="post">
            <table class="table table-striped table-responsive table-hover table-condensed">
                <tbody>
                <tr>
                    <td>Период с : <input type="text" style="display:inline;width:auto;" name="start_date" class="datepicker form-control" value="<?php echo $_POST[start_date];?>"/></td>
                    <td> по : <input type="text" style="display:inline;width:auto;" name="end_date" class="datepicker form-control" value="<?php echo isset($_POST[end_date])?$_POST[end_date]:date('d-m-Y');?>"/></td>
                    <td> Товар : <select name="medicine_id" style="display:inline;width:auto;" class="form-control">
                            <option value="" <?php echo !isset($_POST[medicine_id]) || $_POST[medicine_id]==''?'selected="selected"':'';?>>Все</option>
                            <?php foreach($result[medicine_list] as $singleMedicine):?>

                                <option value="<?php echo $singleMedicine[medicine_id];?>" <?php echo $_POST[medicine_id]==$singleMedicine[medicine_id] ? 'selected="selected"':'';?>><?php echo $singleMedicine[medicine_name];?></option>
                            <?php endforeach;?>
                        </select></td>
                    <td> Производитель <select name="medicine_manufacture_id" style="display:inline;width:auto;" class="form-control">
                            <option value="" <?php echo !isset($_POST[medicine_manufacture_id]) || $_POST[medicine_manufacture_id]==''?'selected="selected"':'';?>>Все</option>
                            <?php foreach($result[medicine_category_list] as $singleMedicine):?>

                                <option value="<?php echo $singleMedicine[medicine_manufacture_id];?>" <?php echo $_POST[medicine_manufacture_id]==$singleMedicine[medicine_manufacture_id] ? 'selected="selected"':'';?>><?php echo $singleMedicine[medicine_manufacture_name];?></option>
                            <?php endforeach;?>
                        </select></td>
                    <td><input type="submit" class="btn btn-default" value="OK"/></td>
                </tr>
                </tbody>
            </table>
        </form>
        <?php
        if (count($result[search_result])>0):?>
            <table class="table table-striped table-responsive table-hover table-condensed">
                <tbody>
                <tr>
                    <th>Дата</th>
                    <th>Название Товара</th>
                    <th>Производитель</th>
                    <th>Количество</th>
                    <th>Цена</th>
                    <th>Сумма</th>
                </tr>
                <?php foreach($result[search_result] as $singlePayment):?>
                    <tr>
                        <td><?php echo $singlePayment[medicine_income_date];?></td>
                        <td><?php echo $singlePayment[medicine_name];?></td>
                        <td><?php echo $singlePayment[medicine_manufacture_name];?></td>
                        <td><?php echo $singlePayment[medicine_income_quantity];?></td>
                        <td><?php echo number_format($singlePayment[medicine_income_price],2,'.',' ').' '.$singlePayment[currency_code];?></td>
                        <td><?php echo number_format($singlePayment[medicine_income_price] * $singlePayment[medicine_income_quantity],2,'.',' ').' '.$singlePayment[currency_code];?></td>
                    </tr>
                <?php endforeach;?>
                <?php foreach($result[total] as $singleTotal):?>
                <tr class="success">
                    <td colspan="4"><strong>Итого</strong></td>
                    <td><strong>Количество :</strong> <?php echo $singleTotal[quantity];?></td>
                    <td><strong>Сумма :</strong> <?php echo number_format($singleTotal[ammount],2,'.',' ').' '.$singleTotal[currency_code];?></td>
                </tr>
                <?php endforeach;?>
                </tbody>
            </table>
            <a href="<?php echo $result['save_url'];?>" class="btn btn-warning">Сохранить</a>
        <?php endif;?>
    </div>
<?php elseif($section =='report_payment'):?>
    <div class="col-sm-12">
        <h3 class="page-header">Оплата по Клиентам</h3>
        <form method="post">
            <table class="table table-striped table-responsive table-hover table-condensed">
                <tbody>
                <tr>
                    <td>Период с : <input type="text" style="display:inline;width:auto;" name="start_date" class="datepicker form-control" value="<?php echo $_POST[start_date];?>"/></td>
                    <td> по : <input type="text" style="display:inline;width:auto;" name="end_date" class="datepicker form-control" value="<?php echo isset($_POST[end_date])?$_POST[end_date]:date('d-m-Y');?>"/></td>
                    <td> Клиент <select name="client_id" style="display:inline;width:auto;" class="form-control">
                            <option value="" <?php echo !isset($_POST[client_id]) || $_POST[client_id]==''?'selected="selected"':'';?>>Все</option>
                            <?php foreach($result[client_list] as $singleMedicine):?>

                                <option value="<?php echo $singleMedicine[client_id];?>" <?php echo $_POST[client_id]==$singleMedicine[client_id] ? 'selected="selected"':'';?>><?php echo $singleMedicine[client_firstname] .' '.$singleMedicine[client_lastname];?></option>
                            <?php endforeach;?>
                        </select></td>
                    <td><input type="submit" class="btn btn-default" value="OK"/></td>
                </tr>
                </tbody>
            </table>
        </form>
        <?php if (count($result[search_result])>0):?>
            <table class="table table-striped table-responsive table-hover table-condensed">
                <tbody>
                <tr>
                    <th>Дата</th>
                    <th>Клиент</th>
                    <th>Сумма</th>
                    <th>По Курсу</th>
                    <th>Итого</th>
                </tr>
                <?php foreach($result[search_result] as $singlePayment):?>
                    <tr>
                        <td><?php echo $singlePayment[payment_date];?></td>
                        <td><?php echo $singlePayment[client_firstname].' '.$singlePayment[client_lastname];?></td>
                        <td><?php echo number_format($singlePayment[payment_amount],2,'.',' ').' '.$singlePayment[currency_a];?></td>
                        <td><?php echo (($singlePayment[payment_exchange_rate]>0)?number_format($singlePayment[payment_exchange_rate],2,'.',' '):'Без Обмена');?></td>
                        <td>
                            <?php if($singlePayment[payment_exchange_rate]>0){
                                echo number_format($singlePayment[payment_exchange_rate]*$singlePayment[payment_amount],2,'.',' ').' '.$singlePayment[currency_b];
                            }
                            else{
                                echo number_format($singlePayment[payment_amount],2,'.',' ').' '.$singlePayment[currency_a];
                            }?></td>
                    </tr>
                <?php endforeach;?>
                <tr class="success">
                    <td colspan="5"><strong>Итого</strong></td>
                </tr>
                <?php foreach($result[summary] as $singleTotal):?>
                <tr>
                    <td colspan="2"></td>
                    <td ><strong>Сумма :</strong><?php echo number_format($singleTotal[ammount],2,'.',' ').' '.$singleTotal[currency_code];?></td>
                    <td colspan="2"></td>
                </tr>
                <?php endforeach;?>
                </tbody>
            </table>
            <a href="<?php echo $result['save_url'];?>" class="btn btn-warning">Сохранить</a>
        <?php endif;?>
    </div>
<?php endif;?>

</div>