<?php
$titrePage='Réservation';
require('../templates/navbar.php');
require('../utility/fonctions.php');

// ON RECUPERE L'"id"
$id=$_GET['id'];

// VARIABLE DATE
$datenow=date('Y-m-d');

// RECUPERATION DES DONNEES DANS LE BASE DE DONNEE
$connect= connexion('sira');
$req=$connect->prepare("SELECT * FROM vehicule AS v INNER JOIN agences AS a ON v.id_agence=a.id_agence  WHERE id_vehicule='$id'");
$req->execute();
while($donnees = $req->fetch()){
	$ida=$donnees['id_agence'];
	$titre=$donnees['titreV'];
	$marque=$donnees['marque'];
	$modele=$donnees['modele'];
	$prixJ=$donnees['prix_journalier'];
	$descr=$donnees['descriptionV'];
	$image=$donnees['photoV'];
	$nomA=$donnees['titreA'];
	$adr=$donnees['adresse'];
	$ville=$donnees['ville'];
	$cp=$donnees['cp'];
	$descrA=$donnees['descriptionA'];
	$imageA=$donnees['photoA'];
}
// FIN DE LA RECUPERATION

?> <h1>Réserver la <?= $titre?></h1>

<!-- AFFICHAGE DE L'INFORMATION DE LA VOITURE + AFFICHAGE -->
<div class="infosCar">
	<img src="../img/voitures/<?= $image;?>" class='photOrder'>
	<div class="infos">
	<p>Véhicule : <?= ' ' . $titre; ?></p>
	<p>Description: <em><?= ' ' . $descr; ?></em></p>
	<p>Prix journalier : <?= ' ' . $prixJ; ?>€/jour</p>
	</div>

<!-- AFFICHAGE DES INFORMATIONS DES AGENCES -->
<div class="infosA">
	<fieldset>
	<img src="../img/agences/<?= $imageA;?>" class="photoTab2 photoTab">
	<p>Agence: <?= ' ' . $nomA;?></p>
	<p><?=$adr ;?></p>
	<p><?=$cp .' '.  $ville;?></p></div>
</fieldset>
</div>
<!-- FIN AFFICHAGE INFORMATIONS DE L'AGENCE -->

<!-- SECTION DE RESERVATION -->
<div class="resa">
	<form method='post' name="formOrder">

		<!-- INPUT CACHE QUI RECUPERE LA VALEUR DU PRIX DU VEHICULE -->
		<input type="text" hidden name="prixT" id="pt" value="">

		<!-- INPUT POUR LA DATE DE DEBUT -->
		<input type="date"   min="<?= $datenow;?>"  value="<?= $datenow;?>" name="dateD" id="dateD">

		<!-- INPUT DE LA DATE DE FIN -->
		<input type="date"  onclick="datej()" min="<?= $datenow;?>" name="dateF" id="dateF">
		
		<!-- LIEN POUR AFFICHER LE PRIX TOTAL -->
		<a onclick="calculer(<?= $prixJ; ?>)">Réserver</a>

		<p id="res"></p>
	</form>
</div>
<!-- FIN DE LA SECTION RESERVATION -->



<script>

//FONCTION DE RECUPEARTION DE LA DATE 
function temps(date)
{
var d = new Date(date[2], date[1] - 1, date[0]);
return d.getTime();
}

//FONCTION DE CALCUL DU PRIX AVEC LA VALEUR DE LA function 'temps'
function calculer(prixj) 
{ 


var date1=document.getElementById('dateD').value;
var date2=document.getElementById('dateF').value;
var dateD=date1.replace(/-/gi, '/');
var dateF=date2.replace(/-/gi, '/');

var debut = temps(dateD.split("/"));
var fin = temps(dateF.split("/"));
var nb = (fin - debut) / (1000 * 60 * 60 * 24); // + " jours";
nb= (Math.floor(nb/365));
nb++;
document.getElementById('res').innerHTML='Vous réservez pour ' + nb + 'jours: ' + (nb*prixj)+ '€.';
document.getElementById('res').innerHTML+= '<input type="submit" name="envoi" value="Confirmer">';
document.getElementById('pt').value = nb*prixj;
} 

//FONCTION DE RESERVATION
function datej(){

	var date1=document.getElementById('dateD').value;
	document.getElementById('dateF').min=date1;
}

</script>

<!-- INSERTION DE LA COMMANDE DANS LA BASE DE DONNEES -->
<?php 
if (isset($_POST['envoi'])) {
	$db=connexion('sira');
	$ins=$db->prepare('INSERT INTO commande (id_membre, id_vehicule, id_agence, date_depart, date_fin, prix_total) VALUES (:idm,:idv,:ida,:dd,:df,:pt)');
	$ins->execute(['idm' => $_SESSION['id'],
					'idv' =>$id,
					'ida' =>$ida,
						'dd'=>$_POST['dateD'],
						'df'=>$_POST['dateF'],
						'pt'=>$_POST['prixT']]);
	// FIN DE LA REQUETE D'INSERTION DANS LA TABLE COMMANDE

}

