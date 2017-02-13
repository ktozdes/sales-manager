
<script>
    $(function(){
        $("input[name=medicine_code]").click(function() {
            $(this).val('');
            $('input[type=hidden]').val('');
            $('input[type=text]').val('');
        });
        if ($('input[name=medicine_code]').val()==''){
            $('input[name=medicine_code]').focus();
        }
        else if ($('input[name=medicine_name]').val()==''){
            $('input[name=medicine_name]').focus();
        }
        else{
            $('input[name=medicine_quantity]').focus();
        }
    });
</script>
<div class="messagebox">
</div>
<?php
if ($section =='medicine_list'):?>
    <div class="row">
        <div class="col-sm-12">
            <?php if ($result['action_result']=='delete_ok'):?>
                <div class="alert alert-success" role="alert">Удалено</div>
            <?php elseif ($result['action_result']=='delete_fail'):?>
                <div class="alert alert-danger" role="danger">Не удалось удалить</div>
            <?php endif;?>
        </div>
        <div class="col-sm-12">
            <h1 class="page-header">
                <?php echo $result[page_title];?>
            </h1>
            <p><a href="?action=new" class="btn btn-primary">Новый Товар</a>   <a href="?action=new_category" class="btn btn-primary">Добавить Производителя</a></p>
        <?php echo $result[pagination];?>
            <form method="post">
                <table class="table table-striped table-responsive table-hover table-condensed">
                    <tr>
                        <td><input type="text" class="form-control" name="medicine_name" placeholder="Название Товара"/> </td>
                        <td><input type="text" class="form-control" name="medicine_code" placeholder="Штрих Код"/></td>
                        <td><input type="submit" class="btn btn-default" value="Поиск"/></td>
                    </tr>

                </table>
            </form>
        <table class="table table-striped table-responsive table-hover table-condensed">
            <thead>
            <tr>
                <th>Код</th>
                <th>Название</th>
                <th>Цена</th>
                <th>В наличии</th>
                <th>Операции</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (count($result[medicine_list])>0):?>
                <?php foreach ($result[medicine_list] as $key=>$singleMedicineRow):?>
                    <tr>
                        <td><?php echo $singleMedicineRow[medicine_code];?></td>
                        <td><?php echo $singleMedicineRow[medicine_name];?></td>
                        <td><?php echo number_format($singleMedicineRow[medicine_price],2,'.',' ').' '.$singleMedicineRow[currency_code];?></td>
                        <td><?php echo $singleMedicineRow[medicine_quantity];?></td>
                        <td>
                            <a href="?action=edit&medicine_id=<?php echo $singleMedicineRow[medicine_id];?>"><span class="glyphicon glyphicon-pencil"></span></a>
                            <a href="?action=delete&medicine_id=<?php echo $singleMedicineRow[medicine_id];?>"><span class="glyphicon glyphicon-remove"></span></a>
                        </td>
                    </tr>
                <?php endforeach;?>
            <?php endif;?>
            </tbody>
        </table>
    </div>

<?php elseif ($section =='single_medicine'):?>

    <div class="row">
        <div class="col-sm-12">
            <?php if ($result['action_result']=='update'):?>
                <div class="alert alert-success" role="alert">Изменено</div>
            <?php elseif ($result['action_result']=='insert'):?>
                <div class="alert alert-success" role="danger">Добавлено</div>
            <?php elseif ($result['action_result']=='code_exists'):?>
                <div class="alert alert-danger" role="danger">Товар с таким штрих кодом существует</div>
            <?php elseif ($result['action_result']=='name_exists'):?>
                <div class="alert alert-danger" role="name_exists">Товар с таким названием существует</div>
            <?php elseif ($result['action_result']=='fail'):?>
                <div class="alert alert-danger" role="danger">Произошла ошибка</div>
            <?php endif;?>
        </div>
        <div class="col-sm-12">
            <h1 class="page-header">
                <?php echo $result[page_title];?>
            </h1>
            <form method="post">
                <table class="table table-striped table-responsive table-hover table-condensed">
                    <tbody>
                    <tr>
                        <th>Код</th>
                        <td>
                            <input type="hidden" name="medicine_id" value="<?php echo isset($result['medicine_id'])?$result['medicine_id']:'';?>"/>
                            <input type="text" name="medicine_code" class="form-control" value="<?php echo isset($result['medicine_code'])?$result['medicine_code']:'';?>"/></td>
                    </tr>
                    <tr>
                        <th>Название</th>
                        <td><input type="text" name="medicine_name" class="form-control" value="<?php echo isset($result['medicine_name'])?$result['medicine_name']:'';?>"/></td>
                    </tr>
                    <tr>
                        <th>Производитель</th>
                        <td><select name="medicine_manufacture_id" class="form-control">
                                <option  <?php echo $result[medicine_manufacture_id]==''?'selected':'';?>>Выберите Производителя</option>
                                <?php foreach($result[category_list] as $singleCountry):?>
                                    <option value="<?php echo $singleCountry[medicine_manufacture_id]?>" <?php echo $singleCountry[medicine_manufacture_id]==$result[medicine_manufacture_id]?'selected':'';?>><?php echo $singleCountry[medicine_manufacture_name]?></option>
                                <?php endforeach;?>
                            </select></td>
                    </tr>
                    <tr>
                        <th>Количество</th>
                        <td>
                            <?php if ($result['medicine_id']>0):?>
                            <div class="input-group">
                                    <span class="input-group-addon"><?php echo $result['medicine_quantity'];?> + </span>
                                <input type="text" name="medicine_quantity" class="form-control" value="0"/>
                                </div>
                            <?php else:?>
                            <input type="text" name="medicine_quantity" class="form-control" value="<?php echo isset($result['medicine_quantity'])?$result['medicine_quantity']:'';?>"/></td>
                            <?php endif?>
                    </tr>
                    <tr>
                        <th>Цена</th>
                        <td><input type="text" name="medicine_price" class="form-control" value="<?php echo isset($result['medicine_price'])?number_format($result['medicine_price'],2,'.',' '):'';?>"/></td>
                    </tr>
                    <tr>
                        <th>Валюта</th>
                        <td><select name="medicine_currency_id" class="form-control">
                                <option  <?php echo $result[medicine_currency_id]==''?'selected':'';?>>Выберите Валюту</option>
                                <?php foreach($result[currency_list] as $singleCurrency):?>
                                    <option value="<?php echo $singleCurrency[currency_id]?>" <?php echo $singleCurrency[currency_id]==$result[medicine_currency_id]?'selected':'';?>><?php echo $singleCurrency[currency_name]?></option>
                                <?php endforeach;?>
                            </select></td>
                    </tr>
                    <tr>
                        <th>Страна</th>
                        <td><select name="medicine_country" class="form-control">
                                <option  <?php echo $result[medicine_country]==''?'selected':'';?>>Выберите Страну</option>
                                <?php foreach($result[country_list] as $singleCountry):?>
                                <option value="<?php echo $singleCountry[country_name]?>" <?php echo $singleCountry[country_name]==$result[medicine_country]?'selected':'';?>><?php echo $singleCountry[country_name]?></option>
                            <?php endforeach;?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Дата Производства</th>
                        <td><input type="date" name="medicine_production_date" class="form-control datepicker" value="<?php echo isset($result['medicine_production_date'])?$result['medicine_production_date']:'';?>"/></td>
                    </tr>
                    <?php
                    if (is_array($result[custom_fields])):
                    foreach($result[custom_fields] as $key=>$singleCustomField):?>
                    <tr>
                        <th><input type="hidden" name="custom_field_name[]" class="form-control" value="<?php echo $key;?>"/><?php echo $key;?></th>
                        <td><input type="text" name="custom_field_value[]" class="form-control" value="<?php echo $singleCustomField;?>"/></td>
                    </tr>
                    <?php endforeach;
                    endif;?>
                    <tr>
                        <th>Допольнительное Поле</th>
                        <td><a href="javascript:void(0)" class="btn btn-warning" onclick="addCustomFieldRow(this)">Добавить</a></td>
                    </tr>
                    </tbody>
                </table>
                <input type="submit" class="btn btn-primary" value="Сохранить"/> <a href="<?php echo $siteData['url']?>/input" class="btn btn-primary">Назад</a>
            </form>
        </div>
    </div>

<?php elseif ($section =='single_medicine_category'):?>

    <div class="row">
        <div class="col-sm-12">
            <?php
            if ($result['action_result']=='update'):?>
                <div class="alert alert-success" role="alert">Изменено</div>
            <?php elseif ($result['action_result']=='insert'):?>
                <div class="alert alert-success" role="danger">Добавлено</div>
            <?php elseif ($result['action_result']=='delete_ok'):?>
                <div class="alert alert-success" role="danger">Удалено</div>
            <?php elseif ($result['action_result']=='code_exists'):?>
                <div class="alert alert-danger" role="name_exists">Фирма с таким названием существует</div>
            <?php elseif ($result['action_result']=='fail'):?>
                <div class="alert alert-danger" role="danger">Произошла ошибка</div>
            <?php endif;?>
        </div>
        <div class="col-sm-12">
            <h1 class="page-header">
                <?php echo $result[page_title];?>
            </h1>
            <form method="post">
                <table class="table table-striped table-responsive table-hover table-condensed">
                    <tbody>
                    <tr>
                        <th>Название</th>
                        <td>
                            <input type="hidden" name="medicine_manufacture_id" value="<?php echo isset($result['category_data']['medicine_manufacture_id'])?$result['category_data']['medicine_manufacture_id']:'';?>"/>
                            <input type="text" name="medicine_manufacture_name" class="form-control" value="<?php echo isset($result['category_data']['medicine_manufacture_name'])?$result['category_data']['medicine_manufacture_name']:'';?>"/></td>
                    </tr>
                    </tbody>
                </table>
                <input type="submit" class="btn btn-primary" value="Сохранить"/> <a href="<?php echo $siteData['url']?>/input" class="btn btn-primary">Назад</a>
            </form>
            <?php if (count($result[category_list])>0):?>
            <table class="table table-striped table-responsive table-hover table-condensed">
            <thead>
            <tr>
                <th>Название</th>
                <th>Операции</th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($result[category_list] as $key=>$singleMedicineRow):?>
                    <tr>
                        <td><?php echo $singleMedicineRow[medicine_manufacture_name];?></td>
                        <td>
                            <a href="?action=edit_category&medicine_manufacture_id=<?php echo $singleMedicineRow[medicine_manufacture_id];?>"><span class="glyphicon glyphicon-pencil"></span></a>
                            <a href="?action=delete_category&medicine_manufacture_id=<?php echo $singleMedicineRow[medicine_manufacture_id];?>"><span class="glyphicon glyphicon-remove"></span></a>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
            </table>

            <?php endif;?>
        </div>
    </div>
<?php endif;?>