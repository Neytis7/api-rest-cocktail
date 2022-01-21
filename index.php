<?php
header('Content-Type: application/json; charset=utf-8');
// Parametre
define ('LOGIN','root');
define ('MDP','');
define ('HOST','localhost');
define ('BASE','tp-cocktail');

// Connexion
try{
	$db="mysql:host=".HOST.";dbname=".BASE.";charset=utf8";
	$pdo = new PDO($db,LOGIN,MDP);
	$pdo->setAttribute(
		PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
}
catch( PDOException $e){
	echo " PB connection :".$e->getMessage();
}

// Requete
$sql = "SELECT * FROM personne";
// envoie de la rqt
$results = $pdo->query($sql);

// on souhaite avoir le nom des colonnes
$p = $results->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER["REQUEST_METHOD"] == "GET"){
	
	//Affichage (PHP ->JAVA)
	echo json_encode($p);
}
else if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$body = file_get_contents('php://input');
	$objet = json_decode($body);

	$prepared_sql = "INSERT INTO personne VALUES(NULL,:nom,:prenom,:age,:presence)";
    $prepared_query = $pdo->prepare($prepared_sql);
    // binder 
    $prepared_query->bindParam(":nom",$objet->nom,PDO::PARAM_STR);
    $prepared_query->bindParam(":prenom",$objet->prenom,PDO::PARAM_STR);
    $prepared_query->bindParam(":age",$objet->age,PDO::PARAM_INT);
    $prepared_query->bindParam(":presence",$objet->presence,PDO::PARAM_BOOL);
    //Execution
    $prepared_query->execute();

	$tab["nom"] = $objet->nom;
	$tab["prenom"] = $objet->prenom;
	$tab["age"] = $objet->age;
	$tab["presence"] = $objet->presence;
	$tab["info"] ="Ajouter";
	echo json_encode($tab);
}
else if ($_SERVER["REQUEST_METHOD"] == "DELETE")
{
	$prepared_sql = "DELETE FROM `personne` WHERE `personne`.`id_Personne` = ".$_GET["id"];
	$prepared_query = $pdo->prepare($prepared_sql);
	$prepared_query->execute();

	$tab["id"] = $_GET["id"];
	$tab["info"] = "delete";
	echo json_encode($tab);
	
}
else if ($_SERVER["REQUEST_METHOD"] == "PUT")
{
	$body = file_get_contents('php://input');
	$objet = json_decode($body);

	$prepared_sql = "UPDATE `personne` SET `nom` = '".$objet->nom."', `prenom` = '".$objet->prenom."' WHERE `personne`.`id_Personne` = ".$_GET["id"];
    $prepared_query = $pdo->prepare($prepared_sql);
    $prepared_query->execute();

	$tab["nom"] = $objet->nom;
	$tab["prenom"] = $objet->prenom;
	$tab["id"] = $_GET["id"];
	$tab["info"] ="Modifier Update";
	echo json_encode($tab);
}
