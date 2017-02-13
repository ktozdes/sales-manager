<?php if ($section =='default_payment_page'):?>
    <div class="messagebox">
    </div>
    <script>
        $(function(){
            $('select[name=payment_client_id]').change(function(){
                if ($(this).val()>0){
                    $('form').submit();
                }
            });
        });
    </script>
    <div class="row">
        <div class="col-sm-12">
            <?php if ($result['action_result']=='balance_paid'):?>
                <div class="alert alert-success" role="alert">Баланс Пополнен</div>
            <?php elseif ($result['action_result']=='update'):?>
                <div class="alert alert-success" role="danger">Изменено</div>
            <?php elseif ($result['action_result']=='balance_fail'):?>
                <div class="alert alert-danger" role="danger">Произошла ошибка</div>
            <?php endif;?>
        </div>
        <div class="col-sm-12">
            <h1 class="page-header">
                Оплата За Товары
            </h1>
            <form method="post">
                <table class="table table-striped table-responsive table-hover table-condensed">
                    <tbody>
                    <tr>
                        <td><strong>Клиент : </strong><select style="display:inline;width:auto;"name="payment_client_id" class="form-control">
                                <option value="" <?php echo $_POST[payment_client_id]==''?'selected':'';?>>Выберите Клиента</option>
                                <?php foreach($result[client_list] as $singleClient):?>
                                    <option value="<?php echo $singleClient[client_id];?>" <?php echo $_POST[payment_client_id]==$singleClient[client_id]?'selected':'';?>><?php echo $singleClient[client_firstname].' '.$singleClient[client_lastname];?></option>
                                <?php endforeach;?>
                            </select></td>
                        <td><strong>Телефон : </strong><?php echo isset($result[client_data]['client_phone'])?$result[client_data]['client_phone']:'';?></td>
                        <td><strong>Фирма : </strong><?php echo isset($result[client_data]['client_company'])?$result[client_data]['client_company']:'';?></td>
                    </tr>
                    <?php
                    if (count($result[client_data][balance])>0):
                    foreach($result[client_data][balance] as $singleBalance):?>
                        <tr>
                            <td></td>
                            <td><strong>Баланс : </strong><span class="text-success"><?php echo number_format($singleBalance[client_balance_balance], 2, '.', ' ').' '.$singleBalance[currency_code];?></span></td>
                            <td><strong>Долг : </strong><span class="text-danger"><?php echo number_format($singleBalance[client_balance_debt], 2, '.', ' ').' '.$singleBalance[currency_code];?></span></td>
                        </tr>
                    <?php endforeach;
                        endif;?>
                    <tr>
                        <td></td>
                        <td><strong>Оплата : </strong><input style="display:inline;width:auto;"  class="form-control" type="text" name="payment" value=""/></td>
                        <td><strong>Валюта : </strong><select name="payment_currency_id" class="form-control" style="display:inline;width:auto">
                                <option value="" <?php echo $result[medicine_currency_id]==''?'selected':'';?>>Выберите Валюту</option>
                                <?php foreach($result[currency_list] as $singleCurrency):?>
                                    <option value="<?php echo $singleCurrency[currency_id]?>" <?php echo $singleCurrency[currency_id]==$result[medicine_currency_id]?'selected':'';?>><?php echo $singleCurrency[currency_name]?></option>
                                <?php endforeach;?>
                            </select></td>
                    </tr>
                    <tr>
                        <td><strong>Обменять валюту : </strong><input type="checkbox" name="exchange_checkbox"></td>
                        <td class="exchange_currency"><strong>Курс по : </strong><input style="display:inline;width:auto;"  class="form-control" type="text" name="exchange_rate" value=""/></td>
                        <td class="exchange_currency"><strong>Валюта : </strong>
                                <select name="exchange_currency_id" class="form-control" style="display:inline;width:auto">
                                    <option value="" <?php echo $result[medicine_currency_id]==''?'selected':'';?>>Выберите Валюту</option>
                                    <?php foreach($result[currency_list] as $singleCurrency):?>
                                        <option value="<?php echo $singleCurrency[currency_id]?>" <?php echo $singleCurrency[currency_id]==$result[medicine_currency_id]?'selected':'';?>><?php echo $singleCurrency[currency_name]?></option>
                                    <?php endforeach;?>
                                </select>
                            </div></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-center"><input name="set_balance" style="display:inline;width:auto;" class="form-control" type="submit" value="OK"/></td>
                    </tr>
                    </tbody>
                </table>
            </form>
            <h3 class="page-header">
                Не Оплаченые Товары
            </h3>
            <table class="table table-striped table-responsive table-hover table-condensed">
                <?php if (count($result[unpaid_good_list])>0):?>
                    <tr>
                        <th>Название</th>
                        <th>Цена</th>
                        <th>Количество</th>
                        <th>Сумма</th>
                        <th>Операции</th>
                    </tr>
                    <?php foreach($result[unpaid_good_list] as $singleGood):?>
                        <tr>

                            <td><input type="hidden" name="sale_medicine_id" value="<?php echo $singleGood[sale_medicine_id];?>"/>
                                <input type="hidden" name="sale_id" value="<?php echo $singleGood[sale_id];?>"/>
                                <?php echo $singleGood[medicine_name];?></td>
                            <td><?php echo number_format($singleGood[sale_price],2,'.',' ').' '.$singleGood[currency_code]?></td>
                            <td><input type="text" name="quantity" class="form-control" value="<?php echo $singleGood[sale_quantity];?>" disabled/> </td>
                            <td><?php echo number_format(($singleGood[sale_price] * $singleGood[sale_quantity]),2,'.',' ').' '.$singleGood[currency_code];?></td>
                            <td><?php if ($singleGood[payment_ok]===true):?>
                                    <a href="javascript:void(0)" onclick="paymentPaid(this)" class="btn btn-info payment_button"><span class="glyphicon glyphicon-ok"></span>Оплатить</a>
                                <?php endif;?>
                                <a href="javascript:void(0)" onclick="activateReturn(this)" class="btn btn-warning return_button"><span class="glyphicon glyphicon-pencil"></span> Возврат</a>
                                <a href="javascript:void(0)" onclick="saveReturn(this)" class="btn btn-info save_return_button"><span class="glyphicon glyphicon-pencil"></span>Возвратить</a>
                                <a href="javascript:void(0)" onclick="cancelReturn(this)" class="btn btn-warning cancel_return_button"><span class="glyphicon glyphicon-remove"></span> Отмена</a>
                            </td>
                        </tr>
                    <?php endforeach;
                endif;?>
            </table>
        </div>
    </div>
<?php endif;?>