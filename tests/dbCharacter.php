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

$data = ['id' => 2, 'name' => 'perso2', 'damage' => 0 ];
$perso2 = new Character($data);

// VIDE LA BASE
$bdd->query('DELETE  FROM characters');

$manager = new CharacterManager($bdd);

$manager->add($perso1);
$manager->add($perso2);
echo 'bd a '.$manager->count(). ' personnages<br>';
echo 'personnage '.$perso1->getName(). ' exist en db : '. $manager->exist($perso1->getId());

$characters = $manager->getAll();
echo '<pre>';
foreach($characters as $character) {
    print_r($character);
}
echo '</pre>';

echo 'perso 1 a '. $perso1->getDamage(). ' dégats<br>';
$perso1->setDamage(50);
$manager->update($perso1);
echo 'perso 1 a '. $perso1->getDamage(). ' dégats après mise a jour<br>';

$manager->delete($perso1);
$manager->delete($perso2);
echo 'bd a '.$manager->count(). ' personnages<br>';
