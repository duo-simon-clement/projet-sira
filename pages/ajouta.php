<?php
$titrePage='Gestion des agences';
require('../templates/navbar.php');
require('../utility/fonctions.php');
//INSERTION DES NOUVELLES DONNES DANS LA BDD APRES REMPLISSAGE DU FORM

// SI LE STATUT EST DIFFERENT DE "admin"
if(!isset($_SESSION['id']) OR $_SESSION['statut']!='admin'){
	header('location: ../index.php');
}

// CODE PHP L'AJOUT DE L'AGENCE. BOUTON "envoyer" DE LA PAGE AJOUT
if (isset($_POST['envoyer'])) {

// VERIFICATION DE LA PHOTO
if (isset($_FILES['mfichier']) AND $_FILES['mfichier']['error'] == 0)  {

// LE FICHIER NE DOIT PAS EXEDER 1Mo
	if ($_FILES['mfichier']['size'] <= 1000000) {


		// EXTENSION AUTORISEES
		$extension_autorisees = ["jpg", "jpeg", "png", "gif"];
		$info= pathinfo($_FILES['mfichier']['name']);
		
		// EXTENSION DE NOTRE FICHIER
		$extension_uploadee = $info['extension'];
		
		// ON VERIFIE L'EXTENSION
		if (in_array($extension_uploadee, $extension_autorisees)) {
			
			move_uploaded_file($_FILES['mfichier']['tmp_name'], '../img/agences/' .basename($_FILES['mfichier']['name']));

			// DECLARATION DE LA VARIABLE IMAGE
			$image = basename($_FILES['mfichier']['name']);

			// INSERTION DE DONNEES
			$db=connexion('sira');
			$insert=$db->prepare(
				'INSERT INTO agences (
				titreA, 
				adresse, 
				cp, 
				ville,
				descriptionA, 
				photoA) 
				VALUES(
				:nomAgence, 
				:addr, :cp, 
				:ville, 
				:des, :
				photo)');
			$insert->execute(
				['nomAgence'=>$_POST['nom_agence'],
				 'addr' =>$_POST['adresse'],
				  'cp'=> $_POST['cp'],
				  'ville' => $_POST['ville'],
				 'des'=>$_POST['des'],
				  'photo'=>$image]);
			
		}
		// FIN DE LA VERIFICATION DE L'EXTENSION ET DE LA REQUETE D'INSERTION
	
	}
	// FIN DE LA VERIFICATION DU FICHIER
	else{

		echo " Les données n'ont pas été modifiées! ";
	}
}
}
?>


<body id="page_ajouta">


<!--  DEBUT DE L'AFFICHAGE DU TABLEAU DES VEHICULES DE LA BDD-->	
	<h1>Nos agences</h1>
	<table>
		<tr>
			<td>Numéro de l'agence</td>
			<td>Nom de l'agence</td>
			<td>Adresse</td>
			<td>Code postal</td>
			<td>Ville</td>
			<td>Description</td>
			<td>Photo</td>
			<td>Modification/Suppression</td>
		</tr>
<?php 
//DEBUT DE LA REQUETE 
$connect=connexion('sira');
$requete=$connect->prepare('SELECT * FROM agences ');
$requete->execute();
while($donnees =$requete->fetch()){
	echo "<tr>
			<td> ". $donnees['id_agence'] . "</td>
			<td>". $donnees['titreA'] ."</td>
			<td>" . $donnees['adresse'] ."</td>
			<td>" . $donnees['cp'] ."</td>
			<td>". $donnees['ville']."</td>
			<td>". $donnees['descriptionA']." </td>
			<td><img src='../img/agences/" . $donnees['photoA'] . "' class='photoTab'> </td>
			<td><a href=../pages/modifa.php?ida=" . $donnees['id_agence'] .">Modifier</a>/<a href=../utility/suppr.php?ida=" . $donnees['id_agence'] .">Supprimer</a></td>
		</tr>";
}
?>
<table>
<!--  FIN DE L'AFFICHAGE DU TABLEAU DES VEHICULES DE LA BDD-->			

<!-- DEBUT DE L'AJOUT DE VEHICULE DANS LA BSS-->
	<h1>Ajoutez une agence</h1>
	<form method="post" action="" enctype="multipart/form-data">
		<fieldset>
			<label>Nom de l'agence</label>
			<input type="text" name="nom_agence">
			<br><br>
			<label>Adresse</label>
			<input type="text" name="adresse">
			<br><br>
			<label>Code postal</label>
			<input type="text" name="cp">
			<br><br>
			<label>Ville</label>
			<input type="text" name="ville">
			<br><br>
			<label>Description</label>
			<br><br>
			<textarea type="text" name="des"></textarea> 
			<br><br>
			<label>Ajoutez une photo</label>
			<br><br>
			<img id="blah" src="#" alt="Prévisualisation" />
			<input type="file" name="mfichier" id="imgInp" >
			<br><br>
			<input type="submit" name="envoyer">
		</fieldset>
	</form>
</body>
</html>
<?php include '../utility/picPreview.js';?>