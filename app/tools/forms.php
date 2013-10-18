<?php
$forms = array('NomDuFormulaire'=>array(	//le nom du formulaire ne doit contenir qu'un seul et unique "-" et doit se définir de la façon suivante : [fonctionnalité/nom/titre/action]-[NomduModelAssocié] (ex: "add-user")
	array(									//
		"field"=>"",						//field (string) : Nom de la colonne de la base de données
		"label"=>"",						//label (string) : Libellé du champ du formulaire
		"type"=>"",							//type (string) : Type du champ
		"check"=>"",						//check (string) : Clé de traitement (cf. règles de traitements ci-après)
		"mandatory"=>,						//mandatory (boolean) : Nature obligatoire du champ
		"placeholder"=>"",					//placeholder (string) : Exemple de valeur attendue	
		"err"=>"",							//err (string) : Message informatif par rapport à une erreur sur le champ
		"class"=>""							//class (string) : Class CSS spécifique au champ du formulaire
		"values"=>""						//values (mixed) : Tableau de données dans le cas d'un select/checkbox/multiple (sinon mettre a NULL)
		)
	)
);

?>