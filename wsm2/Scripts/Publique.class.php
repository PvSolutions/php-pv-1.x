<?php
	
	if(! defined('SCRIPT_PUBLIQUE_WSM'))
	{
		if(! defined('SCRIPT_NOYAU_WSM'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('SCRIPT_PUBLIQUE_WSM', 1) ;
		
		class ScriptAccueilPublWsm extends ScriptWebBaseWsm
		{
			public $CompSlogan ;
			protected function DetermineComposants()
			{
				parent::DetermineComposants() ;
			}
		}
		
		class ScriptAffichPagePublWsm extends ScriptPageAffichBaseWsm
		{
		}
		
		class ScriptRecherchePublWsm extends ScriptWebBaseWsm
		{
		}
		
		class ScriptRSSPublWsm extends ScriptWebBaseWsm
		{
		}
		
		class ScriptCarteSitePublWsm extends ScriptWebBaseWsm
		{
		}
	}
	
?>