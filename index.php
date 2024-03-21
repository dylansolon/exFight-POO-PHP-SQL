<?php
session_start();

// Réinitialiser l'action de la session si elle est détruite
if (isset($_GET['action']) && $_GET['action'] === 'destroy') {
  session_destroy();
  $_SESSION['action'] = 'choice'; // Réinitialiser l'action
  header('location:' . $_SERVER['PHP_SELF']);
  exit();
}

// Initialiser l'action de la session si elle n'est pas déjà définie
if (!isset($_SESSION['action'])) {
  $_SESSION['action'] = 'choice';
}

require 'include/env.php';
require 'include/db-connect.php';
require 'classes/Character.class.php';
require 'classes/CharacterManager.class.php';

// START MANAGER
$manager = new CharacterManager($bdd);

// Charger le personnage depuis la session s'il est défini
if (isset($_SESSION['character'])) {
  $character = unserialize($_SESSION['character']);
}

// TEST IF EMPTY DB
if ($manager->count() === 0) {
  $perso = new Character(['name' => 'toto', 'damage' => 0]);
  $manager->add($perso);
  $perso = new Character(['name' => 'titi', 'damage' => 0]);
  $manager->add($perso);
  $perso = new Character(['name' => 'tata', 'damage' => 0]);
  $manager->add($perso);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
  session_destroy();
}

// HANDLE CHOICE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action']) && $_POST['action'] === 'choice') {
  // NEW
  if (!empty($_POST['name']) && empty($_POST['character_id'])) {
    // NEW
    $character = new Character(['name' => $_POST['name']]);
    // SAVE DB
    $manager->add($character);
    // SAVE EN SESSION
    $_SESSION['character'] = serialize($character);
  }
  if (!empty($_POST['character_id']) && empty($_POST['name'])) {
    // USE
    $character = $manager->getById($_POST['character_id']);

    $_SESSION['character'] = serialize($character);
  }
  $_SESSION['action'] = 'play';
}

// HANDLE PLAY AND HIT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action']) && $_POST['action'] === 'hit') {
  //print_r($_POST);
  $message = '';
  // CHECK IF CHARACTER HIT EXIST
  if ($manager->exist((int)$_POST['character_id'])) {
    $otherCharacter = $manager->getById($_POST['character_id']);
    $response = $character->hit($otherCharacter);

    switch ($response) {
      case Character::CHARACTER_SELF:
        $message = 'Are you masochist ?';
        break;

      case Character::CHARACTER_HIT:
        $message = 'you hit your opponent';
        $manager->update($otherCharacter);
        break;

      case Character::CHARACTER_DEAD:
        $message = 'You opponent is dead';
        $manager->delete($otherCharacter);
        break;
    }
  } else {
    $message =  'this character dont exists !';
  }

  echo $message . '<br>';
}

$listCharacter = $manager->getAll();

?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Carmageddon</title>
</head>

<body>
  <?php if ($_SESSION['action'] === 'choice') { ?>
    <form action="" method="post">
      <input type="hidden" name="action" value="choice">
      <div>Create a new character or select an existing one</div>
      <input type="text" name="name" id="name" value="">
      <select name="character_id" id="character_id">
        <option value="">-- Select a character --</option>
        <?php foreach ($listCharacter as $char) { ?>
          <option value="<?= $char['id'] ?>"><?= $char['name'] ?></option>
        <?php } ?>
      </select>
      <input type="submit" name="send" value="send">
    </form>
  <?php } ?>

  <?php if ($_SESSION['action'] === 'play') { ?>
    <?php if (isset($character)) { ?>
      <p>you play with <?= $character->getName() ?>, you have <?= $character->getDamage() ?> damage(s)</p>

      <p>click on a other character to play with</p>
      <table>
        <tr>
          <td>Name</td>
          <td>Damages</td>
          <td>Action</td>
        </tr>
        <?php foreach ($listCharacter as $char) { ?>
          <tr>
            <td><?= $char['name'] ?></td>
            <td><?= $char['damage'] ?></td>
            <td>
              <form action="" method="POST">
                <input type="hidden" name="character_id" value="<?= $char['id'] ?>">
                <input type="hidden" name="action" value="hit">
                <input type="submit" name="submit" value="hit">
              </form>
            </td>
          </tr>
        <?php } ?>
      </table>
      <form name='reset' method='post'>
        <button type='submit' name='reset'>Reset</button>
      </form>
    <?php } else {
      echo "No character selected!";
      echo "<form name='reset' method='post'> 
      <button type='submit' name='reset'>Reset</button> 
      </form>";
    } ?>
  <?php } ?>

</body>

</html>