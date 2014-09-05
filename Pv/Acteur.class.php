<?php
	
	if(! defined('PV_ACTEUR'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_ACTEUR', 1) ;
		
		class PvConfigMailPourAlerte
		{
			public $Serveur = "" ;
			public $Port = "" ;
			public $Type = "" ;
			public $Email = "" ;
			public $MotPasse = "" ;
		}
		
		class PvActeurBase
		{
			public $Role = "" ;
			public $Nom = "" ;
			public $Prenom = "" ;
			public $Email = array() ;
			public $ContactMobile = array() ;
			public $ContactFixe = "" ;
			public $Adresse = "" ;
			public $Actif = 1 ;
		}
		
		class PvResponsable extends PvActeurBase
		{
			public $Role = "Responsable" ;
		}
		
		class PvDeveloppeur extends PvActeurBase
		{
			public $Role = "Developpeur" ;
		}
		
		class PvSupport extends PvActeurBase
		{
			public $Role = "Support" ;
		}
	}
	
?>