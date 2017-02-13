	<?php if ($section=='default_medicine_output'):?>
        <div class="messagebox">
        </div>
    <div class="row">
        <script>
            var activateForm = <?php echo (isset($result[medicine_data][message]))?'false':'true'?>;
            var medicineCodeChanged = false;
            $(function(){
                $("input[name=sale_medicine_code],input[name=sale_medicine_name]").click(function() {
                    $('input[name=sale_medicine_name]').val('');
                    $('input[name=sale_medicine_code]').val('');
                    $('input[name=sale_medicine_id]').val('');
                    $('input[name=sale_quantity]').val('');
                    $('input[name=sale_price]').val('');
                    $('.medicine_data_table tr td').html('');

                });
                if ($('input[name=sale_medicine_code]').val()==''){
                    $('input[name=sale_medicine_code]').focus();
                }
                $('form.save_form').submit(function(event){
                    //searching goods
                    if (medicineCodeChanged==true){
                    }
                    else if ($('input[name=sale_medicine_id]').val()>0 && (
                        $('select[name=sale_client_id]').val()=='' || $('input[name=sale_price]').val()==''  || $('input[name=sale_quantity]').val()=='' || $('input[name=sale_quantity]').val()=='0')){
                        $('.label-danger').remove();
                        if ($('select[name=sale_client_id]').val()==''){
                            $('select[name=sale_client_id]').after('<span class="label label-danger">Выберите Клиента</span>');
                        }
                        if ($('input[name=sale_price]').val()==''){
                            $('input[name=sale_price]').after('<span class="label label-danger">Выберите Клиента</span>');
                        }
                        if ( $('input[name=sale_quantity]').val()=='' || $('input[name=sale_quantity]').val()=='0'){
                            $('input[name=sale_quantity]').after('<span class="label label-danger">Выберите Количество</span>');
                        }
                        event.preventDefault();
                        return false;
                    }
                    else if ($('input[name=sale_medicine_id]').val()>0 && $('select[name=sale_client_id]').val()>0 && $('input[name=sale_price]').val()>=0 && $('input[name=sale_quantity]').val()>0){
                        $('input[name=finish_transaction]').val('1');
                    }
                    else if ($('input[name=sale_medicine_id]').val()==''){
                        $('.label-danger').remove();
                        $('input[name=sale_medicine_code]').after('<span class="label label-danger">Выберите Товар</span>');
                        event.preventDefault();
                        return false;
                    }
                });
                $('input[name=sale_medicine_code]').change(function(){
                    medicineCodeChanged = true;
                });
                $('select[name=sale_client_id]').change(function(){
                    if ($(this).val()>0){
                        medicineCodeChanged = true;
                        $('form').submit();
                    }
                });
            });
        </script>
        <div class="col-sm-12">
            <?php if ($result['action_result']=='save'):?>
                <div class="alert alert-success" role="alert">Добавлено</div>
            <?php elseif ($result['action_result']=='fail'):?>
                <div class="alert alert-danger" role="danger">Произошла ошибка</div>
            <?php elseif (isset($result[medicine_data][message])):?>
                <div class="alert alert-danger" role="danger"><?php echo $result[medicine_data][message];?></div>
            <?php endif;?>
            <h1 class="page-header">
                Отпукс Товаров
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <form method="post" action="<?php echo $siteData['url'].'/sales'?>">
                <table class="table table-striped table-responsive table-hover table-condensed">
                    <tbody>
                    <tr>
                        <td><input type="text" name="sale_medicine_name" class="form-control" value="<?php echo isset($result[medicine_data]['medicine_name'])?$result[medicine_data]['medicine_name']:'';?>" placeholder="Название товара"/></td>
                        <td><input type="text" name="sale_medicine_code" class="form-control" value="<?php echo isset($result[medicine_data]['medicine_code'])?$result[medicine_data]['medicine_code']:'';?>" placeholder="Штрих код товара"/></td>
                        <td><input type="submit" class="btn btn-default" value="Поиск"/></td>
                    </tr>
                    </tbody>
                </table>
            </form>
            <?php if (count($result[medicine_list])>0):?>
            <h3>Результаты Поиска</h3>
            <table class="table table-striped table-responsive table-hover table-condensed">
                <tbody>
                <tr>
                    <th>Код</th>
                    <th>Название</th>
                    <th>Производитель</th>
                </tr>
            <?php foreach($result[medicine_list] as $singleMedicine):?>
                <tr>
                    <td><a href="?medicine_id=<?php echo $singleMedicine[medicine_id]?>"><?php echo $singleMedicine[medicine_code]?></a></td>
                    <td><a href="?medicine_id=<?php echo $singleMedicine[medicine_id]?>"><?php echo $singleMedicine[medicine_name]?></a></td>
                    <td><a href="?medicine_id=<?php echo $singleMedicine[medicine_id]?>"><?php echo $singleMedicine[medicine_manufacture_name]?></a></td>
                </tr>
            <?php endforeach;?>
                </tbody>
            </table>
            <?php endif;?>
        </div>
        <form method="post" class="save_form">
        <div class="col-sm-6">
                <table class="table table-striped table-responsive table-hover table-condensed">
                    <tbody>
                    <tr>
                        <th>Клиент</th>
                        <td>
                            <input type="hidden" name="sale_medicine_id" value="<?php echo isset($result[medicine_data]['medicine_id'])?$result[medicine_data][medicine_id]:'';?>"/>
                            <input type="hidden" name="sale_medicine_currency_id" value="<?php echo $result[medicine_data][currency_id]?>"/>
                            <input type="hidden" name="finish_transaction" value="0"/>
                            <select name="sale_client_id" class="form-control">
                            <option value="" <?php echo $_POST[sale_client_id]==''?'selected':'';?>>Выберите Клиента</option>
                            <?php foreach($result[client_list] as $singleClient):?>
                                <option value="<?php echo $singleClient[client_id];?>" <?php echo $_POST[sale_client_id]==$singleClient[client_id]?'selected':'';?>><?php echo $singleClient[client_firstname].' '.$singleClient[client_lastname];?></option>
                            <?php endforeach;?>
                            </select></td>
                    </tr>
                    <tr>
                        <th>Цена</th>
                        <td>
                            <div class="input-group">
                                <span class="input-group-addon"><?php echo $result[medicine_data][currency_code]?></span>
                                <input type="text" name="sale_price" class="form-control" value="<?php echo (isset($result[medicine_data][medicine_price]) && strlen($result[medicine_data][medicine_price])>0) ? $result[medicine_data][medicine_price] : $_POST['sale_price'];?>"/>
                            </div><!-- /input-group -->
                            </td>
                    </tr>
                    <tr>
                        <th>Количество</th>
                        <td>
                            <input type="text" name="sale_quantity" class="form-control" value="<?php echo (isset($_POST['sale_quantity']) && isset($result[medicine_data][medicine_quantity]) && $_POST['sale_quantity']<$result[medicine_data][medicine_quantity])?$_POST['sale_quantity']:'';?>"/></td>
                    </tr>
                    <tr>
                        <th>Оплата</th>
                        <td>
                            <div class="input-group">
                                <span class="input-group-addon"><?php echo $result[medicine_data][currency_code]?></span>
                                <input type="text" name="sale_payment" class="form-control" value="<?php echo isset($_POST['sale_payment'])?$result['sale_payment']:'';?>"/>
                            </div>
                    <tr>
                        <th>Другая Валюта</th>
                        <td>
                            <div class="input-group">
                                <span class="input-group-addon"><input type="checkbox" name="sale_different_currency"></span>
                                <input type="text" name="sale_exchange_rate" class="form-control" value="" placeholder="Курс Обмена"/>
                                <select name="sale_exchange_currency_id" class="form-control">
                                    <option  value="">Выберите Валюту</option>
                                    <?php foreach($result[currency_list] as $singleCurrency):?>
                                        <option value="<?php echo $singleCurrency[currency_id]?>"><?php echo $singleCurrency[currency_name]?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Общая Стоимость</th>
                        <td><input type="text" disabled name="sale_full_price" class="form-control" value="<?php echo ($result[medicine_data][medicine_price]>0 && $result[medicine_data][medicine_price]>0)?$result[medicine_data][medicine_price] * $_POST['sale_quantity']:$_POST['sale_quantity'] * $_POST['sale_price'];?>"/></td>
                    </tr>
                    <tr>
                        <th>Текущий Остаток</th>
                        <td><input type="text" disabled name="sale_residue" class="form-control" value="<?php echo isset($result['sale_price'])?$result['sale_price']:'';?>"/></td>
                    </tr>
                    <tr>
                        <th>Общий Остаток</th>
                        <td><input type="text" disabled name="sale_total_residue" class="form-control" value="<?php echo ((($result[medicine_data][medicine_price]>0 && $result[medicine_data][medicine_price]>0)?$result[medicine_data][medicine_price] : $_POST['sale_price']) * $_POST['sale_quantity'] + $result[client_data]['client_debt'] - $result[client_data]['client_balance'] - $_POST['sale_payment'])*(-1);?>"/></td>
                    </tr>
                    </tbody>
                </table>
                <input type="submit" class="btn btn-primary" value="Сохранить"/>
        </div>
        <div class="col-sm-6">
            <table class="table table-striped table-responsive table-hover table-condensed">
                <tbody>
                <tr>
                    <th>ФИО</th>
                    <td colspan="2">
                        <?php echo isset($result[client_data]['client_firstname'])?$result[client_data]['client_firstname'].''.$result[client_data]['client_lastname']:'';?></td>
                </tr>
                <tr>
                    <th>Телефон</th>
                    <td colspan="2"><?php echo isset($result[client_data]['client_phone'])?$result[client_data]['client_phone']:'';?></td>
                </tr>
                <tr>
                    <th>Фирма</th>
                    <td colspan="2"><?php echo isset($result[client_data]['client_company'])?$result[client_data]['client_company']:'';?></td>
                </tr>
                <?php if (count($result[client_data][balance])>0):?>
                <?php foreach ($result[client_data][balance] as $singleBalance):?>
                <tr class="balance_row balance_currency_id_<?php echo $singleBalance[currency_id]?>">
                    <td><strong>Баланс : </strong><span class="text-success client_balance"><?php echo (isset($singleBalance['client_balance_balance']))?$singleBalance['client_balance_balance']:'0';?></span> <?php echo $singleBalance[currency_code];?></td>
                    <td><strong>Долг : </strong><span class="text-danger client_balance"><?php echo (isset($singleBalance['client_balance_debt']))?$singleBalance['client_balance_debt']:'0';?></span> <?php echo $singleBalance[currency_code];?></td>
                    <td><div class="input-group" style="max-width:250px;">
                            <span class="input-group-addon" id="basic-addon2">Включить</span>
                            <span class="input-group-addon"><input type="checkbox" name="balance_checkbox[<?php echo $singleBalance[currency_id]?>]" class="balance_checkbox balance_checkbox_currency_id_<?php echo $singleBalance[currency_id]?>"></span>
                            <input style="display:inline; width:auto"type="text" name="balance_exchange[<?php echo $singleBalance[currency_id]?>]" class="form-control balance_exchange balance_exchange_currency_id_<?php echo $singleBalance[currency_id]?>" <?php echo $result[medicine_data][currency_id]==$singleBalance[currency_id]?'value="1"':''?> placeholder="Курс Обмена"/>
                        </div></td>
                </tr>
                <?php endforeach;?>
                <?php endif;?>
                </tbody>
            </table>
            <h3 class="page-header">Данные Товара</h3>
            <table class="table table-striped table-responsive table-hover table-condensed medicine_data_table">
                <tbody>
                <tr>
                    <th>Название</th>
                    <td>
                        <?php echo isset($result[medicine_data][medicine_name]) ? $result[medicine_data][medicine_name]:'';?></td>
                </tr>
                <tr>
                    <th>Производитель</th>
                    <td>
                        <?php echo isset($result[medicine_data][medicine_manufacture_name]) ? $result[medicine_data][medicine_manufacture_name]:'';?></td>
                </tr>
                <tr>
                    <th>Количествое в складе</th>
                    <td class="max_quantity"><?php echo isset($result[medicine_data][medicine_quantity])?$result[medicine_data][medicine_quantity]:'';?></td>
                </tr>
                <tr>
                    <th>Цена</th>
                    <td><?php echo isset($result[medicine_data][medicine_price])?$result[medicine_data][medicine_price]:'';?></td>
                </tr>
                <?php if (is_array($result[medicine_data][custom_fields])):
                foreach($result[medicine_data][custom_fields] as $key=>$singleCustomField):?>
                <tr>
                    <th><?php echo $key;?></th>
                    <td><?php echo $singleCustomField;?></td>
                </tr>
                <?php endforeach;
                endif;?>
                </tbody>
            </table>
        </div>
        </form>
	</div>
<?php endif;?>