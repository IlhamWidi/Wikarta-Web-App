<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=wikarta','root','');
foreach($pdo->query('SELECT user_id, menu_access FROM role_access_menus') as $row){
    echo 'User '.$row['user_id'].': '.$row['menu_access'].PHP_EOL;
}
