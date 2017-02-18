    <div class="row">
		
<?php if ((isset($_GET['client_id']) && isset($_GET['action']) && $_GET['action']!='delete') || (isset($_GET['action']) && $_GET['action']=='new')):?>
			<div class="col-sm-12">
				<?php if (isset($recordCollection['action_result']) && $recordCollection['action_result']===true):?>
					<div class="alert alert-success" role="alert">Сохранено</div>
				<?php elseif (isset($recordCollection['action_result']) && $recordCollection['action_result']===false):?>
					<div class="alert alert-danger" role="danger">Не Удалось Сохранить</div>
				<?php endif;?>
			</div>
			<div class="col-sm-12 ">
				<h1 class="page-header">
					<?php echo (isset($recordCollection['client_firstname']) && $recordCollection['client_lastname'])?$recordCollection['client_firstname'].' '.$recordCollection['client_lastname']:'Новый Клиент';?>
					<img class="img-rounded" src="<?php echo $siteData['url']?>/images/default_user_avatar.png"/>
				</h1>
				<form method="post">
				<table class="table table-striped table-responsive table-hover table-condensed">
				<tbody>
					<tr>
						<th>Имя</th>
						<td>
						<input type="hidden" name="client_id" value="<?php echo isset($recordCollection['client_id'])?$recordCollection['client_id']:'';?>"/>
						<input type="text" name="client_firstname" class="form-control" value="<?php echo isset($recordCollection['client_firstname'])?$recordCollection['client_firstname']:'';?>"/></td>
					</tr>
					<tr>
						<th>Фамилия</th>
						<td><input type="text" name="client_lastname" class="form-control" value="<?php echo isset($recordCollection['client_lastname'])?$recordCollection['client_lastname']:'';?>"/></td>
					</tr>
					<tr>
						<th>Телефон</th>
						<td><input type="text" name="client_phone" class="form-control" value="<?php echo isset($recordCollection['client_phone'])?$recordCollection['client_phone']:'';?>"/></td>
					</tr>
					<tr>
						<th>Фирма</th>
						<td><input type="text" name="client_company" class="form-control" value="<?php echo isset($recordCollection['client_company'])?$recordCollection['client_company']:'';?>"/></td>
					</tr>
					<tr>
						<th>Адрес</th>
						<td><textarea class="form-control" name="client_address" rows="3"><?php echo isset($recordCollection['client_address'])?$recordCollection['client_address']:'';?></textarea></td>
					</tr>
					<tr>
						<th>Электронная Почта</th>
						<td><input type="email" name="client_email" class="form-control email" value="<?php echo isset($recordCollection['client_email'])?$recordCollection['client_email']:'';?>"/></td>
					</tr>
					<tr>
						<th>Дата Рождения</th>
						<td><input type="date" name="client_birthday" class="form-control datepicker" value="<?php echo isset($recordCollection['client_birthday'])?$recordCollection['client_birthday']:'';?>"/></td>
					</tr>
                    <?php if (count($recordCollection[balance])>0):?>
                    <?php foreach($recordCollection[balance] as $key=>$singleBalance):?>
					<tr class="form-inline client-balance">
						<th>Баланс</th>
						<td class="text-success">
							<input type="hidden" name="currency_id[<?php echo $key;?>]" value="<?php echo $singleBalance[currency_id];?>"/>
							<input class="form-control" name="client_balance_balance[<?php echo $key;?>]" style="text-align:right;" type="number" disabled="disabled" value="<?php echo number_format($singleBalance[client_balance_balance], 2, '.', '')?>"/><?php echo $singleBalance[currency_code];?>
						</td>
					</tr>
					<tr class="form-inline client-balance">
						<th>Долг</th>
						<td class="text-danger">
						<input class="form-control" name="client_balance_debt[<?php echo $key;?>]" style="text-align:right;" type="number" disabled="disabled" value="<?php echo number_format($singleBalance[client_balance_debt], 2, '.', '')?>"/><?php echo $singleBalance[currency_code];?>
						</td>
					</tr>
                    <?php endforeach;?>
                    <?php endif;?>
					<tr>
						<td colspan="2">
							<input type="submit" class="btn btn-primary save-client" value="Cохранить"/> 
							<a href="javascript:void(0)" onclick="editableBalance();" class="btn btn-primary">Редактировать Баланс</a> 
							<a href="<?php echo $siteData['url']?>/client" class="btn btn-primary">Назад</a></td>
					</tr>
				</tbody>
				</table>
				</form>
			</div>
			
		<?php else:?>
			<div class="col-sm-12">
				<?php if (isset($recordCollection['action_result']) && $recordCollection['action_result']===true):?>
					<div class="alert alert-danger" role="danger">Удален</div>
				<?php elseif (isset($recordCollection['action_result']) && $recordCollection['action_result']===false):?>
					<div class="alert alert-alert" role="alert">Не Удалось Удалить</div>
				<?php 
				endif;
				unset($recordCollection['action_result']);
				?>
			</div>
			<div class="col-sm-12">
				<h1 class="page-header">Список Клиентов</h1>
				<a href="?action=new" class="btn btn-primary">Новый Клиент</a>
				<table class="table table-striped table-responsive table-hover table-condensed">
				<thead>
					<tr>
						<th>#</th>
						<th>ФИО</th>
						<th>Телефон</th>
						<th>Фирма</th>
						<th>Операции</th>
					</tr>
				</thead>
				<tbody>
				<?php
				if (count($recordCollection)>0):?>
					<?php foreach ($recordCollection as $key=>$singleClientRow):?>
					<tr>
						<td><?php echo $key+1;?></td>
						<td><a href="?action=edit&client_id=<?php echo $singleClientRow['client_id'];?>"><?php echo $singleClientRow['client_firstname'].' '.$singleClientRow['client_lastname'];?></a></td>
						<td><?php echo $singleClientRow['client_phone'];?></td>
						<td><?php echo $singleClientRow['client_company'];?></td>
						<td>
							<a href="?action=edit&client_id=<?php echo $singleClientRow['client_id'];?>"><span class="glyphicon glyphicon-pencil"></span></a>
							<a href="?action=delete&client_id=<?php echo $singleClientRow['client_id'];?>"><span class="glyphicon glyphicon-remove"></span></a>
						</td>
					</tr>
					<?php endforeach;?>
				<?php endif;?>
				</tbody>
				</table>
			</div>
			
		<?php endif;?>

		</div>
		<button type="button" class="hidden popup-button" data-toggle="modal" data-target="#myModal">
		  Launch demo modal
		</button>

		<!-- Modal -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Подтвердите</h4>
					</div>
					<div class="modal-body">
					<p>Изменение баланса крайне не желательно и любые изменения в балансе будут сохранены.</p>
					<p>Вы действительно хотите изменить баланс клиента?</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default yes-button" data-dismiss="modal">Да</button>
						<button type="button" class="btn btn-primary no-button" data-dismiss="modal">Нет</button>
					</div>
				</div>
			</div>
		</div>