	<div class="row">
		<div class="col-sm-6  col-sm-offset-3 intro-info">
			<h1>Добро Пожаловать</h1>
			<p class="lead">Программа для управления продажами лекарставми</p>
			<p>Функции программы следующие:</p>
			<ul>
				<li>Поступление Товаров(цена, количество, код, пользовательские поля)</li>
				<li>Продажа Товаров(в догл, с оплатой, возвращение)</li>
				<li>Список Клиентов и Просмотр Клиента(личные данные, пользовательские поля, баланс, долг)</li>
				<li>Список Лекарств и Просмотр Лекарства(цена, количество, код, пользовательские поля)</li>
				<li>Отчет
				<ul>
					<li>По Дате</li>
					<li>По Клиентам</li>
				</ul>
				</li>
			</ul>
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-6">
			<p class="text-center"><a href="<?php echo $siteData['url']?>/sales" class="btn btn-lg btn-primary">Продажа</a></p>
		</div>
		<div class="col-sm-6">
			<p class="text-center"><a href="<?php echo $siteData['url']?>/input" class="btn btn-lg btn-primary">Товары</a></p>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<p class="text-center"><a href="<?php echo $siteData['url']?>/client" class="btn btn-lg btn-success">Клиенты</a></p>
		</div>
		<div class="col-sm-6">
			<p class="text-center"><a href="<?php echo $siteData['url']?>/payment" class="btn btn-lg btn-success">Оплата</a></p>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<p class="text-center"><a href="<?php echo $siteData['url']?>/report" class="btn btn-lg btn-info">Отчет</a></p>
		</div>
		<div class="col-sm-6">
			<p class="text-center"><a href="<?php echo $siteData['url']?>#" class="btn btn-lg btn-info">Айка</a></p>
		</div>
	</div>