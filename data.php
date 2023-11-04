<?php
require('db.php');

if (isset($_POST['todo'])) {
    // Block pour enregistrer un todo avec toutes ses étapes !! 
    $i = 0;
    $todoTitle = $_POST['todo']['todoTitle'];
    $todoSteps = $_POST['todo']['todoSteps'];
    $query1 = $db->prepare('insert into todo(title) values (?);'); // query1
    $feedback = $query1->execute([$todoTitle]);
    $todo_id = $db->lastInsertId(); // on recupere le id du todo enregistré pour l'utiliser dans le query2
    $query2 = $db->prepare('insert into steps(todo_id, title) values(?, ?);'); // query2

    // Un todo peut avoir plusieurs etapes. donc on fait une boucle qui part de 0 jusqu'au dernier element 
    // du tableau todoSteps
    for ($i = 0; $i < sizeof($todoSteps); $i++) {
        $feedback &= $query2->execute(array($todo_id, $todoSteps[$i]));
    }
    echo $feedback;
} elseif (isset($_GET['todos'])) {
    // Ce block permet de selectionner tous les todos avec leurs etapes
    $query1 = $db->query("select id, title, DATE_FORMAT(date_ajout, '%d/%m/%Y') as date_ajout from todo order by id desc limit 2;");
    $query2 = $db->prepare('select * from steps where todo_id=? order by id');
    while ($data1 = $query1->fetch()) {
        $todo = []; // on cree au debut un tableau todo vide
        $todo['id'] = $data1['id']; // on selectionne son id
        $todo['title'] = $data1['title']; // on selectionne son title
        $todo['date_ajout'] = $data1['date_ajout']; // on selectionne sa data ajout
        $query2->execute([$data1['id']]);
        $steps = []; // on cree un tableau d'étapes vide
        // pour selectionner les etapes d'un todo on boucle également
        $all_done = true;
        while ($data2 = $query2->fetch()) {
            $step['id'] = $data2['id'];
            $step['title'] = $data2['title'];
            $step['done'] = $data2['done'];
            $steps[] = $step; // on met dans le tebleau des étapes chaque étape
        }
        $todo['steps'] = $steps; // on ajoute au todo toutes ses etapes precédemment sélectionnées
        $collection[] = $todo; // on ajoute à la fin le todo complet à la collection (ensemble des todos)
    }
    echo json_encode($collection);
} elseif (isset($_GET['all_todos'])) {
    // Ce block permet de selectionner tous les todos avec leurs etapes
    $query1 = $db->query("select id, title, DATE_FORMAT(date_ajout, '%d/%m/%Y à %Hh:%imin:%ss') as date_ajout from todo order by id desc;");
    $query2 = $db->prepare('select * from steps where todo_id=? order by id');
    $query3 = $db->prepare('select count(*) as steps_number from steps where todo_id=?');
    while ($data1 = $query1->fetch()) {
        $todo = []; // on cree au debut un tableau todo vide
        $todo['id'] = $data1['id']; // on selectionne son id
        $todo['title'] = $data1['title']; // on selectionne son title
        $todo['date_ajout'] = $data1['date_ajout']; // on selectionne son title
        $query2->execute([$data1['id']]);
        $query3->execute([$data1['id']]);
        $steps = []; // on cree un tableau d'étapes vide
        // pour selectionner les etapes d'un todo on boucle également
        $all_done = true;
        while ($data2 = $query2->fetch()) {
            $step['id'] = $data2['id'];
            $step['title'] = $data2['title'];
            $step['done'] = $data2['done'];
            $steps[] = $step; // on met dans le tebleau des étapes chaque étape
            $all_done &= $data2['done'];
        }
        while ($data3 = $query3->fetch()) {
            $steps_number = $data3['steps_number'];
        }
        $todo['all_done'] = $all_done;
        $todo['steps'] = $steps; // on ajoute au todo toutes ses etapes precédemment sélectionnées
        $todo['steps_number'] = $steps_number;
        $collection[] = $todo; // on ajoute à la fin le todo complet à la collection (ensemble des todos)
    }
    echo json_encode($collection);
} elseif (isset($_POST['step_id'])) {
    // on fait le update d'une etape qui passe de undo à done
    $step_id = $_POST['step_id'];
    $query = $db->prepare('update steps set done=1 where id=?;');
    $feedback = $query->execute([$step_id]);
    echo $feedback;
} elseif (isset($_POST['step_id_toundo'])) {
    // on fait le update d'une etape qui passe de done à undo
    $step_id = $_POST['step_id_toundo'];
    $query = $db->prepare('update steps set done=0 where id=?;');
    $feedback = $query->execute([$step_id]);
    echo $feedback;
} elseif (isset($_POST['add_step_to_todo'])) {
    // pour ajouter une nouvelle etape a un ancien todo
    $todo_id = $_POST['add_step_to_todo'][0];
    $step_title = $_POST['add_step_to_todo'][1];
    $query = $db->prepare('insert into steps (todo_id, title) values(?, ?);');
    $feedback = $query->execute([$todo_id, $step_title]);
    echo $feedback;
} elseif (isset($_GET['charts'])) {
    /*
     on selectionne ici l'essentiel pour creer un diagramme 
     le nombre détapes par todo
    */
    $query1 = $db->query("select id, title from todo order by id;");
    $query2 = $db->prepare('select count(*) as steps_number from steps where todo_id=?');
    while ($data1 = $query1->fetch()) {
        $todo = [];
        $todo['title'] = $data1['title'];
        $feedback = $query2->execute([$data1['id']]);
        while ($data2 = $query2->fetch()) {
            $steps_number = $data2['steps_number'];
        }
        $todo['steps_number'] = $steps_number;
        $collection[] = $todo;
    }
    echo json_encode($collection);
} elseif (isset($_POST['todo_id'])) {
    /*
     dans ce block on récupère l'id d'un todo pour le supprimer
     étant donné qu'on utilise on delete cascade, toutes les 
     etapes du todo sont automatiquement supprimées 
    */
    $todo_id = $_POST['todo_id'];
    $query = $db->prepare('delete from todo where id=?;');
    $feedback = $query->execute([$todo_id]);
    echo $feedback;
}
