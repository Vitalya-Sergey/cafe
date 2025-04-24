<?php
$db = new PDO(
    'mysql:host=localhost;dbname=cafe;charset=utf8', 
    'root',
    '', 
    [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);
?>