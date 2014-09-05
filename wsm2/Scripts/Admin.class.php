<?php
	
	if(! defined('SCRIPT_ADMIN_WSM'))
	{
		if(! defined('SCRIPT_NOYAU_WSM'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('SCRIPT_ADMIN_WSM', 1) ;
		
		class ScriptWebAdminBaseWsm extends ScriptWebBaseWsm
		{
			public $NecessiteMembreConnecte = 1 ;
		}
		
		class ScriptAccueilNonConnecteAdminWsm extends ScriptWebBaseWsm
		{
			public $NecessiteMembreConnecte = 0 ;
			public $CompMsgBienvenue = null ;
			public function ChargeComposants()
			{
				parent::ChargeComposants() ;
				$this->CompMsgBienvenue = new PvPortionRenduHtml() ;
				$this->CompMsgBienvenue->Contenu = "Bienvenue sur la page de WSM 2.0" ;
				$this->InscritComposantRendu("msgBienvenue", $this->CompMsgBienvenue) ;
			}
		}
	}
	
?>