<?php
	
	if(! defined('PV_COMPOSANT_SIMPLE_IU_SSH'))
	{
		if(! defined('PV_COMPOSANT_UI'))
		{
			include dirname(__FILE__)."/../../ComposantIU.class.php" ;
		}
		if(! defined('PV_NOYAU_SIMPLE_IHM'))
		{
			include dirname(__FILE__)."/../Noyau.class.php" ;
		}
		if(! defined('PV_COMPOSANT_SIMPLE_IU_TABLEAU'))
		{
			include dirname(__FILE__)."/Tableau.class.php" ;
		}
		if(! defined('PV_COMPOSANT_SIMPLE_IU_FORMULAIRE'))
		{
			include dirname(__FILE__)."/Formulaire.class.php" ;
		}
		if(class_exists('Net_SSH2'))
		{
			include dirname(__FILE__)."/../../../../misc/phpseclib/SSH2.php" ;
		}
		define('PV_COMPOSANT_SIMPLE_IU_SSH', 1) ;
		
		class PvDefTransactSSH
		{
			public $Hote ;
			public $Port ;
			public $Login ;
			public $MotPasse ;
			public $RepCourant = "" ;
			public $MdlPromptLogin ;
			public $MdlPromptMotPasse ;
			public $MdlPromptBienvenue ;
			public $DefCmdsAvant = array() ;
			public $DefCmdsExec = array() ;
			public $DefCmdsApres = array() ;
			public function DetectePromptLogin($retour)
			{
				if($this->MdlPromptLogin == "")
					return true ;
				if($retour == "")
					return false ;
				return stripos($this->MdlPromptLogin, trim($retour)) == strlen($retour) - strlen($this->MdlPromptLogin) ;
			}
			public function DetectePromptMotPasse($retour)
			{
				if($this->MdlPromptMotPasse == "")
					return true ;
				if($retour == "")
					return false ;
				return stripos(trim($retour), $this->MdlPromptMotPasse) == strlen($retour) - strlen($this->MdlPromptMotPasse) ;
			}
			public function DetectePromptBienvenue($retour)
			{
				if($this->MdlPromptBienvenue == "")
					return true ;
				if($retour == "")
					return false ;
				return stripos(trim($retour), $this->MdlPromptBienvenue) == strlen($retour) - strlen($this->MdlPromptBienvenue) ;
			}
		}
		class PvDefCmdSSH
		{
			public $MsgEnvoi ;
			public $DelaiEnvoi = 0 ;
			public $MdlMsgSucces ;
			public $MsgRetour ;
			public $DelaiRetour ;
			public function EstSucces()
			{
				return (trim($this->MsgRetour) == $this->MdlMsgSucces) ;
			}
		}
		
		class PvComposantIUSshBase extends PvComposantIUBase
		{
			public $DefTransact ;
			public $CltSupport ;
			public $CltConnecte = 0 ;
			public $MdlMsgSucces = "" ;
			public $MdlPromptLogin ;
			public $MdlPromptMotPasse ;
			public $MdlPromptBienvenue ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DefTransact = new PvDefTransactSSH() ;
			}
			protected function OuvreCnx()
			{
				$this->CltConnecte = 0 ;
				$this->CltSupport = new Net_SSH2($this->DefTransact->Hote) ;
				if (! $this->CltSupport->login($this->DefTransact->Login, $this->DefTransact->MotPasse))
				{
					return 0 ;
				}
				$this->CltConnecte = 1 ;
				return 1 ;
			}
			protected function FermeCnx()
			{
				if($this->CltConnecte)
				{
					$this->CltSupport->disconnect() ;
				}
				$this->CltSupport = null ;
			}
			protected function EffectueTransact()
			{
				if(! $this->OuvreCnx())
				{
					return ;
				}
				$this->EnvoieCmdsAvant() ;
				$this->EnvoieCmdsExec() ;
				$this->EnvoieCmdsApres() ;
				$this->FermeCnx() ;
			}
			protected function EnvoiCmdsAvant()
			{
				$this->EnvoieCmds($this->DefTransact->DefCmdsAvant) ;
			}
			protected function EnvoiCmdsExec()
			{
				$this->EnvoieCmds($this->DefTransact->DefCmdsExec) ;
			}
			protected function EnvoiCmdsApres()
			{
				$this->EnvoieCmds($this->DefTransact->DefCmdsApres) ;
			}
			protected function EnvoieCmds(& $cmds)
			{
				foreach($cmds as $i => $cmd)
				{
					
				}
			}
			protected function RenduTransact()
			{
			}
			protected function RenduDispositifBrut()
			{
				$this->EffectueTransact() ;
			}
		}		
	}
	
?>