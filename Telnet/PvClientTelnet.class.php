<?php
	
	if(! defined('CLIENT_TELNET_BASE'))
	{
		define('CLIENT_TELNET_BASE', 1) ;
		
		class ErrRetourCmdTelnetBase
		{
			public $Nature ;
		}
		
		class EnvoiCmdTelnetBase
		{
		}
		class RetourCmdTelnetBase
		{
			public $Succes = 0 ;
			public $Erreur ;
		}
		
		class EtatClientTelnet
		{
			const Deconnecte = 0 ;
			const Connecte = 1 ;
			const ConnexionServeurEnCours = 2 ;
			const DeconnexionServeurEnCours = 3 ;
			const ConnexionUserEnCours = 4 ;
			const DeconnexionUserEnCours = 5 ;
			const TraiteCmdEnCours = 6 ;
		}
		
		class ClientTelnetBase
		{
			public $Hote ;
			public $Port ;
			public $DateCnxServeur ;
			public $DateDecnxServeur ;
			public $DelaiConnexionServeur = 0 ;
			public $DelaiExecCmd = 0 ;
			public function ConnexionServeur()
			{
			}
			public function DeconnexionServeur()
			{
			}
			public function ConnexionUser()
			{
			}
			public function DeconnexionUser()
			{
			}
			public function ExecuteCmd($cmd)
			{
			}
		}
	}
	
?>