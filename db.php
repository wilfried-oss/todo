<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=todo', 'root', 'password');
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
