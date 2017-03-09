<?php
$db = new PDO('mysql:host=localhost;dbname=medicine_db;charset=utf8', 'root', '', array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));