<?php

class CharacterManager {

    public function __construct(private $bdd) {

    }


    public function getById($id):null|Character {
        $q = $this->bdd->prepare('SELECT id, name, damage FROM characters WHERE id = :id');
        // LA REPONSE A MA REQUETE (2)
        $q->execute([':id' => $id]);

        $data = $q->fetch();
        if (is_array($data)) {
            return new Character($data);
        }
        
        return null;
    }

    public function getByName($name):null|Character {
        $q = $this->bdd->prepare('SELECT id, name, damage FROM characters WHERE name like :name');
        $q->execute([':name' => $name]);
        
        $data = $q->fetch();
        if (is_array($data)) {
            return new Character($data);
        }
        
        return null;
    }

    public function getAll():null|array {
        $q = $this->bdd->query('SELECT * FROM characters');

        return $q->fetchAll();
    }

    public function count():int {
        $count = $this->bdd->query('SELECT COUNT(*) FROM characters')->fetchColumn();
        
        return $count;
    }

    public function add(Character $character): void{
        $q = $this->bdd->prepare('INSERT INTO characters (name) VALUES (:name)');
        $q->execute([
            ':name' => $character->getName()
        ]);

        // GET LAST INSERT ID
        $id = $this->bdd->lastInsertId();
        // HYDRATE CHARACTER
        $character->hydrate([
            'id' => $id,
            'damage' => 0
        ]);
    }

    public function update(Character $character):void {
        $q = $this->bdd->prepare('UPDATE characters set damage =:damage WHERE id=:id');
        $q->execute([
            ':damage' => $character->getDamage(),
            ':id' => $character->getId()
        ]);
    }

    public function exist($id):bool {
        if (is_int($id)) {
            return (bool) $this->bdd->query('SELECT COUNT(*) FROM characters WHERE id='.$id)->fetchColumn();
        }
        return false;
    }

    public function delete(Character $character):void {
        $q = $this->bdd->prepare('DELETE FROM characters WHERE id=:id');
        $q->execute([
            ':id' => $character->getId()
        ]);
    }

    
}