<?php
	
	if(! defined('PV_ZONE_IHM'))
	{
		if(! defined('PV_NOYAU_IHM'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		if(! defined('AK_MEMBERSHIP'))
		{
			include dirname(__FILE__)."/../../Ak/Membership.class.php" ;
		}
		define('PV_ZONE_IHM', 1) ;
		
		class PvEtatScriptExecZone
		{
			public $ID ;
			public $IDScript ;
			public $TimestmpDebut ;
			public $TimestmpFin ;
			public $TimestmpCapt ;
		}
		class PvGestScriptsExecZoneBase extends PvObjet
		{
			public $EtatActuel ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->EtatActuel = $this->CreeStatut() ;
				$this->EtatActuel->ID = uniqid() ;
			}
			protected function CreeStatut()
			{
				return new PvEtatScriptExecZone() ;
			}
			public function Demarre(& $script)
			{
				$this->EtatActuel->IDScript = $script->IDInstanceCalc ;
				$this->EtatActuel->TimestmpDebut = date("U") ;
				$this->EtatActuel->TimestmpCapt = date("U") ;
				$this->SauveEtatActuel() ;
			}
			public function Termine(& $script)
			{
				$this->EtatActuel->TimestmpFin = date("U") ;
				$this->EtatActuel->TimestmpCapt = date("U") ;
				$this->SauveEtatActuel() ;
			}
			protected function EffaceEtatActuel()
			{
				$this->EffaceEtat($this->EtatActuel) ;
			}
			protected function SauveEtatActuel()
			{
				$this->SauveEtat($this->EtatActuel) ;
			}
			protected function SauveEtat($etat)
			{
			}
			protected function EffaceEtat($etat)
			{
			}
		}
		class PvGestScriptExecInactif extends PvGestScriptsExecZoneBase
		{
		}
		class PvGestScriptExecNatif extends PvGestScriptsExecZoneBase
		{
			public $ChemRelDossierEtats = "" ;
			protected function ObtientChemDossierEtats()
			{
				$chemin = (php_sapi_name() == 'cli') ? $_SERVER["argv"][0] : $_SERVER["SCRIPT_FILENAME"] ;
				if($this->ChemRelDossierEtats != '')
				{
					$chemin .= PATH_SEPARATOR . $this->ChemRelDossierEtats ;
				}
				if(! is_dir($chemin))
				{
					mkdir($chemin) ;
				}
				if(is_dir($chemin))
				{
					return $chemin ;
				}
				return false ;
			}
			protected function SauveEtat($etat)
			{
				$chemin = $this->ObtientChemDossierEtats() ;
				if($chemin == false)
				{
					return ;
				}
				$fh = fopen($chemin. PATH_SEPARATOR . $etat->ID .'.dat', 'w') ;
				fputs($fh, serialize($etat)) ;
				fclose($fh) ;
				
			}
			protected function EffaceEtat($etat)
			{
				$chemin = $this->ObtientChemDossierEtats() ;
				if($chemin == false)
				{
					return ;
				}
				unlink($chemin. PATH_SEPARATOR . $etat->ID .'.dat', 'w') ;
			}
		}
		
		class PvZoneIHMDeBase extends PvIHM
		{
			public $TypeIHM = "zone" ;
			public $GestScriptsExec ;
			public $Scripts = array() ;
			public $NomParamScriptAppele = "appelleScript" ;
			public $AutoDetectParamScriptAppele = 1 ;
			public $ValeurParamScriptAppeleFixe = "" ;
			public $ValeurParamScriptAppele = "" ;
			public $ScriptParDefaut = null ;
			public $NomScriptParDefaut = "accueil" ;
			public $ScriptAppele = null ;
			public $ScriptNonTrouve = null ;
			public $Membership = null ;
			public $NomClasseMembership = null ;
			protected $NomScriptsEditMembership = array() ;
			public $InclureScriptsMembership = 0 ;
			public $PrivilegesEditMembership = array() ;
			public $NomClasseScriptDeconnexion = "" ;
			public $NomClasseScriptRecouvreMP = "" ;
			public $NomClasseScriptConnexion = "" ;
			public $NomClasseScriptChangeMotPasse = "" ;
			public $NomClasseScriptChangeMPMembre = "" ;
			public $NomClasseScriptDoitChangerMotPasse = "" ;
			public $NomClasseScriptInscription = "" ;
			public $NomClasseScriptAjoutMembre = "" ;
			public $NomClasseScriptModifMembre = "" ;
			public $NomClasseScriptModifPrefs = "" ;
			public $NomClasseScriptSupprMembre = "" ;
			public $NomClasseScriptListeMembres = "" ;
			public $NomClasseScriptAjoutProfil = "" ;
			public $NomClasseScriptModifProfil = "" ;
			public $NomClasseScriptSupprProfil = "" ;
			public $NomClasseScriptListeProfils = "" ;
			public $NomClasseScriptAjoutRole = "" ;
			public $NomClasseScriptModifRole = "" ;
			public $NomClasseScriptSupprRole = "" ;
			public $NomClasseScriptListeRoles = "" ;
			public $NomClasseScriptAjoutServeurAD = "" ;
			public $NomClasseScriptModifServeurAD = "" ;
			public $NomClasseScriptSupprServeurAD = "" ;
			public $NomClasseScriptListeServeursAD = "" ;
			public $NomScriptConnexion = "connexion" ;
			public $NomScriptInscription = "inscription" ;
			public $AutoriserInscription = 0 ;
			public $AutoriserModifPrefs = 0 ;
			public $NomScriptRecouvreMP = "recouvreMP" ;
			public $NomScriptDeconnexion = "deconnexion" ;
			public $NomScriptChangeMPMembre = "changeMPMembre" ;
			public $NomScriptChangeMotPasse = "changeMotPasse" ;
			public $NomScriptDoitChangerMotPasse = "doitChangerMotPasse" ;
			public $NomScriptAjoutMembre = "ajoutMembre" ;
			public $NomScriptModifMembre = "modifMembre" ;
			public $NomScriptModifPrefs = "modifPrefs" ;
			public $NomScriptSupprMembre = "supprMembre" ;
			public $NomScriptListeMembres = "listeMembres" ;
			public $NomScriptAjoutProfil = "ajoutProfil" ;
			public $NomScriptModifProfil = "modifProfil" ;
			public $NomScriptSupprProfil = "supprProfils" ;
			public $NomScriptListeProfils = "listeProfils" ;
			public $NomScriptAjoutRole = "ajoutRole" ;
			public $NomScriptModifRole = "modifRole" ;
			public $NomScriptSupprRole = "supprRole" ;
			public $NomScriptListeRoles = "listeRoles" ;
			public $NomScriptAjoutServeurAD = "ajoutServeurAD" ;
			public $NomScriptModifServeurAD = "modifServeurAD" ;
			public $NomScriptSupprServeurAD = "supprServeurAD" ;
			public $NomScriptListeServeursAD = "listeServeursAD" ;
			public $ScriptDeconnexion = null ;
			public $ScriptInscription = null ;
			public $ScriptConnexion = null ;
			public $ScriptRecouvreMP = null ;
			public $ScriptChangeMotPasse = null ;
			public $ScriptChangeMPMembre = null ;
			public $ScriptDoitChangerMotPasse = null ;
			public $ScriptAjoutMembre = null ;
			public $ScriptModifMembre = null ;
			public $ScriptModifPrefs = null ;
			public $ScriptSupprMembre = null ;
			public $ScriptListeMembres = null ;
			public $ScriptAjoutProfil = null ;
			public $ScriptModifProfil = null ;
			public $ScriptSupprProfil = null ;
			public $ScriptListeProfils = null ;
			public $ScriptAjoutRole = null ;
			public $ScriptModifRole = null ;
			public $ScriptSupprRole = null ;
			public $ScriptListeRoles = null ;
			public $ScriptAjoutServeurAD = null ;
			public $ScriptModifServeurAD = null ;
			public $ScriptSupprServeurAD = null ;
			public $ScriptListeServeursAD = null ;
			public $PrivilegesExceptions = array() ;
			public $PrivilegesPassePartout = array() ;
			public $ExceptionsToujoursVisibles = 0 ;
			public $ExceptionsVisiblesPourSuperAdmin = 1 ;
			public $ExceptionsAvantRendu = array() ;
			public $Exceptions = array() ;
			public $LibelleDetailsException = "Plus de d&eacute;tails" ;
			public $AliasDetailsException = "exception_more_details" ;
			public $UtiliserJournalRequetesEnvoyees = 0 ;
			public $JournalRequetesEnvoyees = null ;
			public $UtiliserJournalExceptions = 0 ;
			public $JournalExceptions = null ;
			public $NomClasseJournalRequetesEnvoyees = "PvJournalRequetesEnvoyeesBase" ;
			public $NomClasseJournalExceptions = "PvJournalExceptions" ;
			public $NomClasseRemplisseurConfigMembership = "PvRemplisseurConfigMembershipSimple" ;
			public $RemplisseurConfigMembership = null ;
			public $MessageScriptMalRefere = "<p>Ce script n'est pas bien refere. Il ne peut etre affiche.</p>" ;
			public $AnnulDetectMemberCnx = 0 ;
			public function NatureZone()
			{
				return "base" ;
			}
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->GestScriptsExec = $this->CreeGestScriptsExec() ;
			}
			protected function CreeGestScriptsExec()
			{
				return new PvGestScriptExecInactif() ;
			}
			public function CaptureExceptionBaseDonnees(& $basedonnees)
			{
				if($basedonnees->ConnectionException == "")
				{
					return ;
				}
			}
			public function RenduException($exception)
			{
				$this->Exceptions[] = $exception ;
				if(! $this->ExceptionsVisible())
				{
					return "" ;
				}
				$ctn = '' ;
				if($this->RenduEnCours == 0)
				{
					$this->ExceptionsAvantRendu[] = $exception ;
				}
				else
				{
					$ctn = $this->RenduContenuException($exception) ;
				}
				return $ctn ;
			}
			protected function RenduContenuException($exception)
			{
				$nomFonctJS = 'BasculeDetailsException_'.count($this->Exceptions) ;
				$nomBlocDetailsException = 'detailsException_'.count($this->Exceptions) ;
				$ctn = '' ;
				$ctn .= '<div class="exception">'.htmlentities($exception->Message).' <a href="javascript:'.$nomFonctJS.'()">'.$this->LibelleDetailsException.'</a></div>'.PHP_EOL ;
				$ctn .= '<div class="detailsException" id="'.$nomBlocDetailsException.'" style="display:none ;">'.PHP_EOL ;
				$ctn .= '<table width="100%" cellspacing="0" cellpadding="3">' ;
				$ctn .= '<tr><th valign="top" align="left">Parametres : </th>' ;
				$ctn .= '<td valign="top"><div style="overflow:scroll; height:120px; width:700px"><pre>'.var_export($exception->Parametres, true).'</pre></div></td></tr>'.PHP_EOL ;
				$ctn .= '<tr><th valign="top" align="left">Fichier : </th>' ;
				$ctn .= '<td valign="top">'.$exception->CheminFichier.'</td></tr>'.PHP_EOL ;
				$ctn .= '<tr><th valign="top" align="left">Ligne : </th>' ;
				$ctn .= '<td valign="top">'.$exception->NumeroLigne.'</td></tr>'.PHP_EOL ;
				$ctn .= '</table>' ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '<script type="text/javascript">
	function '.$nomFonctJS.'()
	{
		var bloc = document.getElementById("'.$nomBlocDetailsException.'") ;
		if(bloc.style.display == "none")
		{
			bloc.style.display = "block" ;
		}
		else
		{
			bloc.style.display = "none" ;
		}
	}
</script>'.PHP_EOL ;
				return $ctn ;
			}
			public function ExceptionsVisible()
			{
				if($this->ExceptionsToujoursVisibles)
				{
					return 1 ;
				}
				if(! $this->PossedeMembreConnecte())
				{
					return 0 ;
				}
				$ok = 0 ;
				if(count($this->PrivilegesExceptions) > 0)
				{
					$ok = $this->ZoneParent->PossedePrivileges($this->PrivilegesExceptions) ;
				}
				if($this->ExceptionsVisiblesPourSuperAdmin && $this->MembreSuperAdminConnecte())
				{
					$ok = 1 ;
				}
				return $ok ;
			}
			protected function ChargeScriptsMembership()
			{
				if(! $this->InclureScriptsMembership || $this->EstNul($this->Membership))
					return ;
				if(! $this->PossedeMembreConnecte())
				{
					$this->ChargeScriptsMSNonConnecte() ;
				}
				else
				{
					$this->ChargeScriptsMSConnecte() ;
				}
			}
			protected function ChargeScriptsMSNonConnecte()
			{
				if(class_exists($this->NomClasseScriptConnexion))
				{
					$nomClasse = $this->NomClasseScriptConnexion ;
					$this->ScriptConnexion = new $nomClasse() ;
					$this->InscritScript($this->NomScriptConnexion, $this->ScriptConnexion) ;
				}
				if($this->AutoriserInscription && class_exists($this->NomClasseScriptInscription))
				{
					$nomClasse = $this->NomClasseScriptInscription ;
					$this->ScriptInscription = new $nomClasse() ;
					$this->InscritScript($this->NomScriptInscription, $this->ScriptInscription) ;
				}
				if(class_exists($this->NomClasseScriptRecouvreMP))
				{
					$nomClasse = $this->NomClasseScriptRecouvreMP ;
					$this->ScriptRecouvreMP = new $nomClasse() ;
					$this->InscritScript($this->NomScriptRecouvreMP, $this->ScriptRecouvreMP) ;
				}
			}
			public function MembreADActive()
			{
				return ($this->EstPasNul($this->Membership->MemberLogged) && $this->Membership->MemberLogged->ADActivated != $this->Membership->ADActivatedMemberTrueValue) ;
			}
			public function MembreDoitChangerMP()
			{
				return ($this->MembreAuthentifieParAD() == 0 && $this->EstPasNul($this->Membership->MemberLogged) && $this->Membership->MemberLogged->MustChangePassword == $this->Membership->MustChangePasswordMemberTrueValue) ;
			}
			public function MembreAuthentifieParAD()
			{
				return ($this->AttrMembreConnecte("MEMBER_AD_ACTIVATED") == 1) ;
			}
			protected function ChargeScriptsMSConnecte()
			{
				if(class_exists($this->NomClasseScriptDeconnexion))
				{
					$nomClasse = $this->NomClasseScriptDeconnexion ;
					$this->ScriptDeconnexion = new $nomClasse() ;
					$this->InscritScript($this->NomScriptDeconnexion, $this->ScriptDeconnexion) ;
				}
				if($this->MembreADActive())
				{
					if(class_exists($this->NomClasseScriptChangeMotPasse))
					{
						$nomClasse = $this->NomClasseScriptChangeMotPasse ;
						$this->ScriptChangeMotPasse = new $nomClasse() ;
						$this->InscritScript($this->NomScriptChangeMotPasse, $this->ScriptChangeMotPasse) ;
					}
					if($this->MembreDoitChangerMP() && class_exists($this->NomClasseScriptDoitChangerMotPasse))
					{
						$nomClasse = $this->NomClasseScriptDoitChangerMotPasse ;
						$this->ScriptDoitChangerMotPasse = new $nomClasse() ;
						$this->InscritScript($this->NomScriptDoitChangerMotPasse, $this->ScriptDoitChangerMotPasse) ;
					}
				}
				if(class_exists($this->NomClasseScriptChangeMPMembre))
				{
					$nomClasse = $this->NomClasseScriptChangeMPMembre ;
					$this->ScriptChangeMPMembre = new $nomClasse() ;
					$this->InscritScript($this->NomScriptChangeMPMembre, $this->ScriptChangeMPMembre) ;
				}
				if($this->MembreAuthentifieParAD() == 0)
				{
					if(class_exists($this->NomClasseScriptChangeMotPasse))
					{
						$nomClasse = $this->NomClasseScriptChangeMotPasse ;
						$this->ScriptChangeMotPasse = new $nomClasse() ;
						$this->InscritScript($this->NomScriptChangeMotPasse, $this->ScriptChangeMotPasse) ;
					}
				}
				if(class_exists($this->NomClasseScriptAjoutMembre))
				{
					$nomClasse = $this->NomClasseScriptAjoutMembre ;
					$this->ScriptAjoutMembre = new $nomClasse() ;
					$this->ScriptAjoutMembre->DeclarePrivileges($this->PrivilegesEditMembership) ;
					$this->InscritScript($this->NomScriptAjoutMembre, $this->ScriptAjoutMembre) ;
					$this->NomScriptsEditMembership[] = $this->NomScriptAjoutMembre ;
				}
				if(class_exists($this->NomClasseScriptModifMembre))
				{
					$nomClasse = $this->NomClasseScriptModifMembre ;
					$this->ScriptModifMembre = new $nomClasse() ;
					$this->ScriptModifMembre->DeclarePrivileges($this->PrivilegesEditMembership) ;
					$this->InscritScript($this->NomScriptModifMembre, $this->ScriptModifMembre) ;
					$this->NomScriptsEditMembership[] = $this->NomScriptModifMembre ;
				}
				if($this->AutoriserModifPrefs && class_exists($this->NomClasseScriptModifPrefs))
				{
					$nomClasse = $this->NomClasseScriptModifPrefs ;
					$this->ScriptModifPrefs = new $nomClasse() ;
					$this->InscritScript($this->NomScriptModifPrefs, $this->ScriptModifPrefs) ;
				}
				if(class_exists($this->NomClasseScriptSupprMembre))
				{
					$nomClasse = $this->NomClasseScriptSupprMembre ;
					$this->ScriptSupprMembre = new $nomClasse() ;
					$this->ScriptSupprMembre->DeclarePrivileges($this->PrivilegesEditMembership) ;
					$this->InscritScript($this->NomScriptSupprMembre, $this->ScriptSupprMembre) ;
					$this->NomScriptsEditMembership[] = $this->NomScriptSupprMembre ;
				}
				if(class_exists($this->NomClasseScriptListeMembres))
				{
					$nomClasse = $this->NomClasseScriptListeMembres ;
					$this->ScriptListeMembres = new $nomClasse() ;
					$this->ScriptListeMembres->DeclarePrivileges($this->PrivilegesEditMembership) ;
					$this->InscritScript($this->NomScriptListeMembres, $this->ScriptListeMembres) ;
					$this->NomScriptsEditMembership[] = $this->NomScriptListeMembres ;
				}
				if(class_exists($this->NomClasseScriptAjoutProfil))
				{
					$nomClasse = $this->NomClasseScriptAjoutProfil ;
					$this->ScriptAjoutProfil = new $nomClasse() ;
					$this->ScriptAjoutProfil->DeclarePrivileges($this->PrivilegesEditMembership) ;
					$this->InscritScript($this->NomScriptAjoutProfil, $this->ScriptAjoutProfil) ;
					$this->NomScriptsEditMembership[] = $this->NomScriptAjoutProfil ;
				}
				if(class_exists($this->NomClasseScriptModifProfil))
				{
					$nomClasse = $this->NomClasseScriptModifProfil ;
					$this->ScriptModifProfil = new $nomClasse() ;
					$this->ScriptModifProfil->DeclarePrivileges($this->PrivilegesEditMembership) ;
					$this->InscritScript($this->NomScriptModifProfil, $this->ScriptModifProfil) ;
					$this->NomScriptsEditMembership[] = $this->NomScriptModifProfil ;
				}
				if(class_exists($this->NomClasseScriptSupprProfil))
				{
					$nomClasse = $this->NomClasseScriptSupprProfil ;
					$this->ScriptSupprProfil = new $nomClasse() ;
					$this->ScriptSupprProfil->DeclarePrivileges($this->PrivilegesEditMembership) ;
					$this->InscritScript($this->NomScriptSupprProfil, $this->ScriptSupprProfil) ;
					$this->NomScriptsEditMembership[] = $this->NomScriptSupprProfil ;
				}
				if(class_exists($this->NomClasseScriptListeProfils))
				{
					$nomClasse = $this->NomClasseScriptListeProfils ;
					$this->ScriptListeProfils = new $nomClasse() ;
					$this->ScriptListeProfils->DeclarePrivileges($this->PrivilegesEditMembership) ;
					$this->InscritScript($this->NomScriptListeProfils, $this->ScriptListeProfils) ;
					$this->NomScriptsEditMembership[] = $this->NomScriptListeProfils ;
				}
				if(class_exists($this->NomClasseScriptAjoutRole))
				{
					$nomClasse = $this->NomClasseScriptAjoutRole ;
					$this->ScriptAjoutRole = new $nomClasse() ;
					$this->ScriptAjoutRole->DeclarePrivileges($this->PrivilegesEditMembership) ;
					$this->InscritScript($this->NomScriptAjoutRole, $this->ScriptAjoutRole) ;
					$this->NomScriptsEditMembership[] = $this->NomScriptAjoutRole ;
				}
				if(class_exists($this->NomClasseScriptModifRole))
				{
					$nomClasse = $this->NomClasseScriptModifRole ;
					$this->ScriptModifRole = new $nomClasse() ;
					$this->ScriptModifRole->DeclarePrivileges($this->PrivilegesEditMembership) ;
					$this->InscritScript($this->NomScriptModifRole, $this->ScriptModifRole) ;
					$this->NomScriptsEditMembership[] = $this->NomScriptModifRole ;
				}
				if(class_exists($this->NomClasseScriptSupprRole))
				{
					$nomClasse = $this->NomClasseScriptSupprRole ;
					$this->ScriptSupprRole = new $nomClasse() ;
					$this->ScriptSupprRole->DeclarePrivileges($this->PrivilegesEditMembership) ;
					$this->InscritScript($this->NomScriptSupprRole, $this->ScriptSupprRole) ;
					$this->NomScriptsEditMembership[] = $this->NomScriptSupprRole ;
				}
				if(class_exists($this->NomClasseScriptListeRoles))
				{
					$nomClasse = $this->NomClasseScriptListeRoles ;
					$this->ScriptListeRoles = new $nomClasse() ;
					$this->ScriptListeRoles->DeclarePrivileges($this->PrivilegesEditMembership) ;
					$this->InscritScript($this->NomScriptListeRoles, $this->ScriptListeRoles) ;
					$this->NomScriptsEditMembership[] = $this->NomScriptListeRoles ;
				}
				if($this->Membership->ADServerMemberColumn != '')
				{
					if(class_exists($this->NomClasseScriptAjoutServeurAD))
					{
						$nomClasse = $this->NomClasseScriptAjoutServeurAD ;
						$this->ScriptAjoutServeurAD = new $nomClasse() ;
						$this->ScriptAjoutServeurAD->DeclarePrivileges($this->PrivilegesEditMembership) ;
						$this->InscritScript($this->NomScriptAjoutServeurAD, $this->ScriptAjoutServeurAD) ;
						$this->NomScriptsEditMembership[] = $this->NomScriptAjoutServeurAD ;
					}
					if(class_exists($this->NomClasseScriptModifServeurAD))
					{
						$nomClasse = $this->NomClasseScriptModifServeurAD ;
						$this->ScriptModifServeurAD = new $nomClasse() ;
						$this->ScriptModifServeurAD->DeclarePrivileges($this->PrivilegesEditMembership) ;
						$this->InscritScript($this->NomScriptModifServeurAD, $this->ScriptModifServeurAD) ;
						$this->NomScriptsEditMembership[] = $this->NomScriptModifServeurAD ;
					}
					if(class_exists($this->NomClasseScriptSupprServeurAD))
					{
						$nomClasse = $this->NomClasseScriptSupprServeurAD ;
						$this->ScriptSupprServeurAD = new $nomClasse() ;
						$this->ScriptSupprServeurAD->DeclarePrivileges($this->PrivilegesEditMembership) ;
						$this->InscritScript($this->NomScriptSupprServeurAD, $this->ScriptSupprServeurAD) ;
						$this->NomScriptsEditMembership[] = $this->NomScriptSupprServeurAD ;
					}
					if(class_exists($this->NomClasseScriptListeServeursAD))
					{
						$nomClasse = $this->NomClasseScriptListeServeursAD ;
						$this->ScriptListeServeursAD = new $nomClasse() ;
						$this->ScriptListeServeursAD->DeclarePrivileges($this->PrivilegesEditMembership) ;
						$this->InscritScript($this->NomScriptListeServeursAD, $this->ScriptListeServeursAD) ;
						$this->NomScriptsEditMembership[] = $this->NomScriptListeServeursAD ;
					}
				}
			}
			public function ChargeConfig()
			{
				$this->ChargeScripts() ;
				$this->ChargeScriptParDefaut() ;
				$this->ChargeScriptNonTrouve() ;
				$this->ChargeMembership() ;
				$this->ChargeJournalExceptions() ;
				$this->ChargeJournalRequetesEnvoyees() ;
			}
			protected function ChargeMembership()
			{
				$nomClasseMembership = $this->NomClasseMembership ;
				if(class_exists($nomClasseMembership))
				{
					$this->Membership = new $nomClasseMembership($this) ;
				}
				if($this->InclureScriptsMembership)
				{
					$nomClasseMembership = $this->NomClasseRemplisseurConfigMembership ;
					if(class_exists($nomClasseMembership))
					{
						$this->RemplisseurConfigMembership = new $nomClasseMembership() ;
					}
				}
			}
			protected function ChargeScripts()
			{
			}
			protected function ChargeScriptParDefaut()
			{
				$this->ScriptParDefaut = $this->ValeurNulle() ;
				if(isset($this->Scripts[$this->NomScriptParDefaut]))
				{
					$this->ScriptParDefaut = & $this->Scripts[$this->NomScriptParDefaut] ;
				}
			}
			protected function ChargeScriptNonTrouve()
			{
			}
			public function & InsereScriptParDefaut($script)
			{
				$this->InscritScriptParDefaut($script) ;
				return $script ;
			}
			public function & InsereScript($nom, $script)
			{
				$this->InscritScript($nom, $script) ;
				return $script ;
			}
			public function InscritScriptParDefaut(& $script)
			{
				$this->InscritScript($this->NomScriptParDefaut, $script) ;
			}
			public function InscritScript($nom, & $script)
			{
				$this->Scripts[$nom] = & $script ;
				$script->AdopteZone($nom, $this) ;
			}
			public function ScriptAccessible($nomScript)
			{
				if(! isset($this->Scripts[$nomScript]))
				{
					return 0 ;
				}
				return $this->Scripts[$nomScript]->EstAccessible() ;
			}
			public function TypeZone()
			{
				return "BASE" ;
			}
			protected function DetecteScriptAppele()
			{
				$this->DetecteParamScriptAppele() ;
				$nomScripts = array_keys($this->Scripts) ;
				$this->ScriptAppele = & $this->ScriptParDefaut ;
				foreach($nomScripts as $i => $nom)
				{
					$script = & $this->Scripts[$nom] ;
					if($script->AccepteAppel($this->ValeurParamScriptAppele))
					{
						$this->ScriptAppele = & $script ;
						break ;
					}
				}
				// print get_class($this->ScriptAppele)." hdfhdh" ;
			}
			protected function ExecuteScriptAppele()
			{
				if($this->EstPasNul($this->ScriptAppele))
				{
					$this->ScriptAppele->ChargeConfig() ;
					$this->ExecuteScript($this->ScriptAppele) ;
				}
				else
				{
					if($this->EstPasNul($this->ScriptNonTrouve))
					{
						$this->ScriptNonTrouve->ChargeConfig() ;
						$this->ExecuteScript($this->ScriptNonTrouve) ;
					}
					else
					{
						$this->AfficheRenduNonTrouve() ;
					}
				}
			}
			protected function PrepareScript(& $script)
			{
			}
			protected function TermineScript(& $script)
			{
			}
			public function ExecuteScript(& $script)
			{
				$this->PrepareScript($script) ;
				$this->VerifieValiditeMotPasse($script) ;
				if($script->EstAccessible())
				{
					$this->DetermineEnvironnement($script) ;
					$script->Execute() ;
				}
				else
				{
					$this->ExecuteScriptInaccessible($script) ;
				}
				$this->TermineScript($script) ;
			}
			protected function AfficheRenduNonTrouve()
			{
				Header("HTTP/1.0 404 Not Found") ;
				exit ;
			}
			protected function AfficheRenduInacessible()
			{
				header('HTTP/1.1 401 Unauthorized');
				echo "Vous n'avez pas le droit d'acc&eacute;der &agrave; ce script !!!" ;
				exit ;
			}
			protected function ExecuteScriptInaccessible(& $script)
			{
				$this->AfficheRenduInacessible() ;
			}
			protected function ExecuteScriptMalRefere(& $script)
			{
				echo $this->MessageScriptMalRefere ;
				exit ;
			}
			protected function DetecteParamScriptAppele()
			{
				$this->ValeurBruteParamScriptAppele = "" ;
				$this->ValeurParamScriptAppele = $this->NomScriptParDefaut ;
				if($this->AutoDetectParamScriptAppele == 0)
				{
					if($this->ValeurParamScriptAppeleFixe != "")
					{
						$this->ValeurBruteParamScriptAppele = $this->ValeurParamScriptAppeleFixe ;
						$this->ValeurParamScriptAppele = $this->ValeurBruteParamScriptAppele ;
					}
				}
				else
				{
					if(isset($_GET[$this->NomParamScriptAppele]))
					{
						$this->ValeurBruteParamScriptAppele = $_GET[$this->NomParamScriptAppele] ;
						$this->ValeurParamScriptAppele = $this->ValeurBruteParamScriptAppele ;
					}
				}
			}
			public function DeclareScript($nom, $nomClasseScript)
			{
				if(! class_exists($nomClasseScript))
				{
					return ;
				}
				$nomPropriete = $nom.'Script' ;
				$this->$nomPropriete = new $nomClasseScript() ;
				$this->InscritScript($nom, $this->$nomPropriete) ;
			}
			protected function DetermineEnvironnement(& $script)
			{
				$script->DetermineEnvironnement() ;
			}
			protected function DetecteScriptsMembership()
			{
				$this->DetecteMembreConnecte() ;
				$this->ChargeScriptsMembership() ;
			}
			public function Execute()
			{
				$this->DemarreExecution() ;
				$this->DetecteScriptsMembership() ;
				$this->DetecteScriptAppele() ;
				$this->ExecuteScriptAppele() ;
				$this->TermineExecution() ;
			}
			public function MembershipActive()
			{
				return class_exists($this->NomClasseMembership) ? 1 : 0;
			}
			protected function DetecteMembreConnecte()
			{
				if($this->Membership == null || $this->AnnulDetectMemberCnx == 1)
				{
					return ;
				}
				$this->Membership->Run() ;
				// print_r($this->Membership->MemberLogged) ;
			}
			public function PossedeMembreConnecte()
			{
				$ok = 0 ;
				if($this->Membership != null)
				{
					if($this->EstPasNul($this->Membership->MemberLogged))
					{
						if(! $this->Membership->UseGuestMember || $this->Membership->MemberLogged->Id != $this->Membership->GuestMemberId)
						{
							$ok = 1 ;
						}
					}
				}
				return $ok ;
			}
			public function SurScriptConnecte()
			{
				return ($this->InscrireScriptsMembership == 0 || ($this->PossedeMembreConnecte() && $this->NomScriptDeconnexion != $this->ValeurBruteParamScriptAppele)) ;
			}
			public function ObtientMembreConnecte()
			{
				$membre = null ;
				if($this->EstPasNul($this->Membership))
				{
					if($this->Membership->MemberLogged != null)
					{
						$membre = $this->Membership->MemberLogged ;
					}
				}
				return $membre ;
			}
			public function EstSuperAdmin($membre)
			{
				if($this->Membership->RootMemberId != "" && $membre->Id == $this->Membership->RootMemberId)
				{
					return 1 ;
				}
				return 0 ;
			}
			public function EstSuperAdminConnecte()
			{
				return $this->MembreSuperAdminConnecte() ;
			}
			public function MembreSuperAdminConnecte()
			{
				if(! $this->PossedeMembreConnecte())
				{
					return 0 ;
				}
				return $this->EstSuperAdmin($this->Membership->MemberLogged) ;
			}
			public function EditMembershipPossible()
			{
				if($this->PossedeMembreConnecte() && count($this->PrivilegesEditMembership) == 0)
					return 1 ;
				return $this->PossedePrivileges($this->PrivilegesEditMembership) ;
			}
			public function IdMembreConnecte()
			{
				if(! $this->PossedeMembreConnecte())
				{
					return 0 ;
				}
				return $this->Membership->MemberLogged->Id ;
			}
			public function LoginMembreConnecte()
			{
				if(! $this->PossedeMembreConnecte())
				{
					return 0 ;
				}
				return $this->Membership->MemberLogged->Login ;
			}
			public function AttrMembreConnecte($nomAttr)
			{
				if(! $this->PossedeMembreConnecte()|| ! isset($this->Membership->MemberLogged->RawData[$nomAttr]))
				{
					return null ;
				}
				return $this->Membership->MemberLogged->RawData[$nomAttr] ;
			}
			public function TitreProfilConnecte()
			{
				if(! $this->PossedeMembreConnecte()|| ! isset($this->Membership->MemberLogged->Profile))
				{
					return null ;
				}
				return $this->Membership->MemberLogged->Profile->Title ;
			}
			public function PossedeTousPrivileges()
			{
				$ok = 1 ;
				foreach($this->Membership->MemberLogged->Profile->Privileges as $nomRole => $priv)
				{
					if($priv->Enabled == 0)
					{
						$ok = 0 ;
						break ;
					}
				}
				return $ok ;
			}
			public function PossedePrivilege($nomRole, $strict=0)
			{
				return $this->PossedePrivileges(array($nomRole), $strict) ;
			}
			public function PossedePrivileges($privileges=array(), $strict=0)
			{
				$ok = 0 ;
				$privilegesSpec = $privileges ;
				if($strict == 0 && count($this->PrivilegesPassePartout) > 0)
					array_splice($privileges, 0, 0, $this->PrivilegesPassePartout) ;
				if($this->PossedeMembreConnecte() == 0)
				{
					return 0 ;
				}
				if(count($privilegesSpec) == 0)
				{
					return 1 ;
				}
				$membre = $this->Membership->MemberLogged ;
				if(count($privileges) > 0)
				{
					foreach($privileges as $i => $nomRole)
					{
						if(isset($membre->Profile->Privileges[$nomRole]))
						{
							if($membre->Profile->Privileges[$nomRole]->Enabled)
							{
								$ok = 1 ;
								break ;
							}
						}
					}
				}
				return $ok ;
			}
			public function DoitChangerMotPasse(& $script)
			{
				// if($this->PossedeMembreConnecte() == 0 || ! $script->EstAccessible())
				if($this->PossedeMembreConnecte() == 0)
				{
					return 0 ;
				}
				$membership = $this->Membership ;
				$membre = $membership->MemberLogged ;
				$ok = 0 ;
				if($membre->MustChangePassword == $membership->MustChangePasswordMemberTrueValue)
				{
					$ok = 1 ;
				}
				return $ok ;
			}
			protected function VerifieValiditeMotPasse($script)
			{
				if($script->NomElementZone != $this->NomScriptDoitChangerMotPasse && $this->DoitChangerMotPasse($script))
				{
					$this->RedirigeVersScript($this->ScriptDoitChangerMotPasse) ;
				}
			}
			protected function ChargeJournalRequetesEnvoyees()
			{
				if(! $this->UtiliserJournalRequetesEnvoyees)
				{
					return ;
				}
				$this->JournalRequetesEnvoyees = null ;
				$nomClasse = $this->NomClasseJournalRequetesEnvoyees ;
				if(class_exists($nomClasse))
				{
					$this->JournalRequetesEnvoyees = new $nomClasse() ;
					$this->JournalRequetesEnvoyees->ChargeConfig() ;
				}
			}
			protected function ChargeJournalExceptions()
			{
				if(! $this->UtiliserJournalExceptions)
				{
					return ;
				}
				$this->JournalExceptions = null ;
				$nomClasse = $this->NomClasseJournalExceptions ;
				if(class_exists($nomClasse))
				{
					$this->JournalExceptions = new $nomClasse() ;
					$this->JournalExceptions->ChargeConfig() ;
				}
			}
			public function RapporteException($exception)
			{
				
			}
			public function RapporteRequeteEnvoyee()
			{
				if(! $this->UtiliserJournalRequetesEnvoyees || $this->EstNul($this->JournalRequetesEnvoyees))
				{
					return ;
				}
				$this->JournalRequetesEnvoyees->Inscrit() ;
			}
			public function RedirigeVersScript(& $script, $params=array())
			{
			}
			public function InvoqueScript($nomScript, $params=array(), $valeurPost=array(), $async=1)
			{
				return $this->InvoqueScriptSpec($nomScript, $params, $valeurPost, $async) ;
			}
			protected function InvoqueScriptSpec($nomScript, $params=array(), $valeurPost=array(), $async=1)
			{
			}
		}
		
		class PvZoneDInclusions extends PvZoneIHMDeBase
		{
			public $CheminAbsoluDossierRacine = "" ;
			public $CheminRelatifDossierRacine = "." ;
			public $CheminDossierVariables = "variables" ;
			public $CheminDossierRendus = "rendu" ;
			public $ExtensionFichierVariables = "php" ;
			public $ExtensionFichierRendus = "php" ;
			public $PrefixeNomFichier = "" ;
			public $SuffixeNomFichier = "" ;
			public $BaliseContenuScript = "[main_content]" ;
			public function NatureZone()
			{
				return "inclusions" ;
			}
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DetermineCheminAbsoluDossierRacine() ;
			}
			public function DetermineCheminAbsoluDossierRacine()
			{
				$this->CheminAbsoluDossierRacine = realpath(dirname(__FILE__)."/../../..") ;
			}
			public function ObtientCheminDossierRacine()
			{
				return $this->CheminAbsoluDossierRacine. DIRECTORY_SEPARATOR .$this->CheminRelatifDossierRacine ;
			}
			public function DeclareInclusionScript($nom)
			{
				$script = new PvScriptDInclusion() ;
				$script->CheminFichier = $nom ;
				$nomSansSymboles = str_replace(array('/', '\\'), '_', $nom) ;
				$nomPropriete = 'Script'.ucfirst($nomSansSymboles) ;
				$this->$nomPropriete = & $script ;
				$this->InscritScript($nom, $this->$nomPropriete) ;
			}
			public function DeclareInclusionDossierVariables($cheminRelatifDossier)
			{
				$this->DeclareInclusionDossier($this->ZoneParent->ObtientCheminDossierVariables(), $cheminRelatifDossier) ;
			}
			public function DeclareInclusionDossierRendus($cheminRelatifDossier)
			{
				$this->DeclareInclusionDossier($this->ZoneParent->ObtientCheminDossierRendus(), $cheminRelatifDossier) ;
			}
			public function DeclareInclusionDossier($cheminAbsoluRacine, $cheminRelatifDossier)
			{
				$cheminDossier = $cheminAbsoluRacine.DIRECTORY_SEPARATOR .$cheminRelatifDossier ;
				if(! is_dir($cheminDossier))
				{
					return ;
				}
				$resourceDossier = opendir($cheminDossier) ;
				if($dh != false)
				{
					$origine = strpos($cheminAbsoluRacine, $this->ObtientCheminDossierVariables() === 0) ? 'VAR' : 'REND' ;
					while($nomFichier = readdir($resourceDossier) !== false)
					{
						$inclureFichier = 0 ;
						if($origine == 'VAR' && $this->strrpos($nomFichier, $this->ExtensionFichierVariables) == strlen($nomFichier) - strlen($this->ExtensionFichierVariables))
						{
							$inclureFichier = 1 ;
						}
						elseif($this->strrpos($nomFichier, $this->ExtensionFichierRendus) == strlen($nomFichier) - strlen($this->ExtensionFichierRendus))
						{
							$inclureFichier = 1 ;
						}
						if($inclureFichier)
						{
							$this->InscritScript($cheminRelatifDossier.DIRECTORY_SEPARATOR .$nomFichier) ;
						}
					}
					closedir($resourceDossier) ;
				}
			}
			public function ObtientCheminDossierVariables()
			{
				return $this->ObtientCheminDossierRacine() . DIRECTORY_SEPARATOR . $this->CheminDossierVariables. DIRECTORY_SEPARATOR .$this->PrefixeNomFichier.$this->NomElementApplication.$this->SuffixeNomFichier ;
			}
			public function ObtientCheminDossierRendus()
			{
				return $this->ObtientCheminDossierRacine() . DIRECTORY_SEPARATOR . $this->CheminDossierRendus. DIRECTORY_SEPARATOR . $this->PrefixeNomFichier.$this->NomElementApplication.$this->SuffixeNomFichier ;
			}
			public function ObtientCheminFichierVariables()
			{
				return $this->ObtientCheminDossierVariables(). "." . $this->ExtensionFichierVariables ;
			}
			public function ObtientCheminFichierRendus()
			{
				return $this->ObtientCheminDossierRendus(). "." . $this->ExtensionFichierRendus ;
			}
		}
		
		class PvZoneWeb extends PvZoneIHMDeBase
		{
			public $CheminFavicon ;
			public $CheminBanniere ;
			public $NomClasseJournalRequetesEnvoyees = "PvJournalRequetesEnvoyeesHttp" ;
			public $NomSessionTraducteur = "traducteur" ;
			public $NomParamTraducteur = "traducteur" ;
			public $ReglesHtmlSur = array() ;
			protected $PourImpression = 0 ;
			public function NatureZone()
			{
				return "web" ;
			}
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->InitReglesHtmlSur() ;
			}
			protected function InitReglesHtmlSur()
			{
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeTraducteurActif() ;
			}
			public function ObtientUrl()
			{
				if($this->ApplicationParent->EnModeConsole())
				{
					if($this->ApplicationParent->UrlRacine != '')
					{
						return $this->ApplicationParent->UrlRacine."/".$this->CheminFichierRelatif ;
					}
					elseif($this->ApplicationParent->NomElementActif == $this->NomElementApplication)
					{
						return $_SERVER["argv"][0] ;
					}
					else
					{
						return "" ;
					}
				}
				$url = remove_url_params(get_current_url()) ;
				if($this->ApplicationParent->NomElementActif == $this->NomElementApplication)
				{
					return $url ;
				}
				$url = ((isset($_SERVER["HTTPS"])) ? 'https' : 'http').'://'.$_SERVER["SERVER_NAME"].(($_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '443') ? ':'.$_SERVER["SERVER_PORT"] : '').'/'.$this->CheminFichierRelatif ;
				return $url ;
			}
			public function ObtientUrlParam($params=array())
			{
				return $this->ObtientUrl()."?".http_build_query_string($params) ;
			}
			public function ObtientUrlScript($nomScript, $params=array(), $strict=1)
			{
				if(! isset($this->Scripts[$nomScript]) && $strict == 1)
					return false ;
				$chaineParams = http_build_query_string($params) ;
				$url = $this->ObtientUrl()."?".urlencode($this->NomParamScriptAppele).'='.urlencode($nomScript) ;
				if($chaineParams != '')
				{
					$url .= '&'.$chaineParams ;
				}
				return $url ;
			}
			public function RedirigeVersScript(& $script, $params=array())
			{
				$url = $this->ObtientUrlScript($script->NomElementZone, $params) ;
				if($url == false)
				{
					exit ;
				}
				Header("Location:".$url) ;
				exit ;
			}
			public function ObtientScriptParDefaut()
			{
				$script = null ;
				if($this->EstPasNul($this->Scripts[$this->NomScriptParDefaut]))
				{
					$script = & $this->Scripts[$this->NomScriptParDefaut] ;
				}
				return $script ;
			}
			public function ChargeTraducteurActif()
			{
				$nomSession = $this->NomElementApplication.'_'.$this->NomSessionTraducteur ;
				$nomParam = $this->NomParamTraducteur ;
				$nomTrad = '' ;
				if(isset($_GET[$nomParam]))
				{
					$nomTrad = $_GET[$nomParam] ;
				}
				elseif(isset($_SESSION[$nomSession]))
				{
					$nomTrad = $_SESSION[$nomSession] ;
				}
				if($nomTrad != "")
				{
					$this->ActiveTraducteur($nomTrad) ;
				}
				$_SESSION[$nomSession] = $this->ApplicationParent->SystTrad->NomTraducteurActif ;
			}
			public function HtmlSur($ctn)
			{
				$result = $ctn ;
				return $result ;
			}
			public function DemarreRenduImpression()
			{
				$this->PourImpression = 1 ;
			}
			public function TermineRenduImpression()
			{
				$this->PourImpression = 1 ;
			}
			public function ImpressionEnCours()
			{
				return $this->PourImpression ;
			}
			public function InvoqueScriptSpec($nomScript, $params=array(), $valeurPost=array(), $async=1)
			{
				return PvApplication::TelechargeUrl($this->ObtientUrlScript($nomScript, $params, 0), $valeurPost, $async) ;
			}
		}
		class PvZoneRequetesBase extends PvZoneIHMDeBase
		{
			public $Methodes = array() ;
			// public function 
		}
		
		class PvZoneConsole extends PvZoneIHMDeBase
		{
			protected $ArgsExecution = array() ;
			public function NatureZone()
			{
				return "console" ;
			}
			public function Execute()
			{
				$this->DetecteArgsExecution() ;
				parent::Execute() ;
			}
			protected function DetecteArgsExecution()
			{
				$platf = new PvPlateformProcConsole() ;
				$this->ArgsExecution = $platf->RecupArgs() ;
			}
			protected function DetecteParamScriptAppele()
			{
				$this->ValeurBruteParamScriptAppele = "" ;
				$this->ValeurParamScriptAppele = $this->NomScriptParDefaut ;
				if(isset($this->ArgsExecution[$this->NomParamScriptAppele]))
				{
					$this->ValeurBruteParamScriptAppele = $this->ArgsExecution[$this->NomParamScriptAppele] ;
					$this->ValeurParamScriptAppele = $this->ValeurBruteParamScriptAppele ;
				}
			}
			public function ObtientUrl()
			{
				if($this->ApplicationParent->NomElementActif == $this->NomElementApplication)
				{
					$url = $_SERVER["argv"][0] ;
					return $url ;
				}
				else
				{
					$execPath = PvApplication::ObtientCheminPHP() ;
					$cmd = realpath(dirname(__FILE__).'/../../../'.$this->CheminFichierRelatif) ;
					return $execPath.' '.$cmd ;
				}
			}
			public function ObtientUrlParam($params=array())
			{
				return $this->ObtientUrl()." ".PvApplication::EncodeArgsShell($params) ;
			}
			public function ObtientUrlScript($nomScript, $params=array(), $strict=1)
			{
				if(! isset($this->Scripts[$nomScript]) && $strict == 1)
					return false ;
				$params[$this->NomParamScriptAppele] = $nomScript ;
				$url = $this->ObtientUrl()." ".PvApplication::EncodeArgsShell($params) ;
				// echo $url ;
				return $url ;
			}
			public function InvoqueScriptSpec($nomScript, $params=array(), $valeurPost=array(), $async=1)
			{
				return PvApplication::TelechargeShell($this->ObtientUrlScript($nomScript, $params, 0), $valeurPost, $async) ;
			}
		}
		class PvZoneBureau extends PvZoneIHMDeBase
		{
		}
	}
	
?>