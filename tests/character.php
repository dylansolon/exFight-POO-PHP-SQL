<?php
include '../include/env.php';
include '../classes/Character.class.php';
include '../classes/CharacterManager.class.php';

try {
    $bdd = new PDO('mysql:host='.SERVER.';dbname='.DATABASE.';charset=utf8', USER, PASSWORD);
    $bdd->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

$data = ['id' => 1, 'name' => 'perso1', 'damage' => 0 ];
$perso1 = new Character($data);
echo 'perso1 se nomme '.$perso1->getName().'<br>';

$data = ['id' => 2, 'name' => 'perso2', 'damage' => 0 ];
$perso2 = new Character($data);
echo 'perso2 se nomme '.$perso2->getName().'<br>';

$perso1->hit($perso2);
$perso1->hit($perso2);

$perso2->hit($perso1);
echo 'perso2 a '.$perso2->getDamage(). ' dégats<br>';
echo 'perso1 a '.$perso1->getDamage(). ' dégats<br>';

