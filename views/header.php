<html lang="ru">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Medistom. Medicine sale manager">
	<meta name="author" content="Sydykov Chyngyz">
	<title>
		<?php echo $siteData['page_title'];?>
	</title>
	<!-- CSS -->
	<link href="<?php echo $siteData['url']?>/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo $siteData['url']?>/css/style.css" rel="stylesheet">
	<link href="<?php echo $siteData['url']?>/css/jquery-ui.css" rel="stylesheet">
	
	<!-- Javascript -->
	<script src="<?php echo $siteData['url']?>/js/jquery-1.11.1.min.js"></script>
	<script src="<?php echo $siteData['url']?>/js/bootstrap.min.js"></script>
	<script src="<?php echo $siteData['url']?>/js/jquery-ui.js"></script>
	<script src="<?php echo $siteData['url']?>/js/jquery.mask.min.js"></script>
	<script src="<?php echo $siteData['url']?>/js/jquery.ui.datepicker-ru.js"></script>
	<script src="<?php echo $siteData['url']?>/js/main.js"></script>
</head>
<body class="<?php echo $siteData['body_class'];?>">
	<div class="container-fluid">
	<?php if ($siteData['page']!='home'):?>
		<div class="navbar-container navbar-inverse">
			<a class="navbar-brand" href="<?php echo $siteData['url']?>">Medicine Sales</a>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="<?php echo $siteData['url']?>/sales">Продажа</a></li>
					<li><a href="<?php echo $siteData['url']?>/input">Товары</a></li>
                    <li><a href="<?php echo $siteData['url']?>/payment">Оплата</a></li>
                    <li><a href="<?php echo $siteData['url']?>/client">Клиенты</a></li>
					<li><a href="<?php echo $siteData['url']?>/report">Отчет</a></li>
				</ul>
				<form class="navbar-form navbar-right">
					<input class="form-control" placeholder="Search..." type="text">
				</form>
			</div>
		</div>
	<?php endif;?>