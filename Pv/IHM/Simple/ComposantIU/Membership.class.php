<?php
	
	if(! defined('PV_COMPOSANT_SIMPLE_IU_MEMBERSHIP'))
	{
		if(! defined('PV_COMPOSANT_UI'))
		{
			include dirname(__FILE__)."/../../ComposantIU.class.php" ;
		}
		if(! defined('PV_NOYAU_SIMPLE_IHM'))
		{
			include dirname(__FILE__)."/../Noyau.class.php" ;
		}
		if(! defined('PV_FOURNISSEUR_DONNEES_SIMPLE'))
		{
			include dirname(__FILE__)."/../FournisseurDonnees.class.php" ;
		}
		define('PV_COMPOSANT_SIMPLE_IU_MEMBERSHIP', 1) ;
		
		class PvTableauMembresMSHtml extends PvTableauDonneesHtml
		{
			protected $InclureFiltresMembre = 1 ;
			protected $InclureColonnesMembre = 1 ;
			public $RemplisseurConfigMembership = null ;
			public $DefinitionColonneProfil ;
			public $DefinitionColonneEnable ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeConfigMembership() ;
			}
			protected function InitRemplisseurConfigMembership()
			{
				$this->RemplisseurConfigMembership = & $this->ZoneParent->RemplisseurConfigMembership ;
			}
			protected function ChargeConfigMembership()
			{
				$this->InitRemplisseurConfigMembership() ;
				if($this->InclureColonnesMembre || $this->InclureFiltresMembre)
				{
					$this->RemplisseurConfigMembership->InitTableauMembre($this) ;
				}
				if($this->InclureColonnesMembre)
				{
					$this->RemplisseurConfigMembership->RemplitDefinitionsColonneTableauMembre($this) ;
				}
				if($this->InclureFiltresMembre)
				{
					$this->RemplisseurConfigMembership->RemplitFiltresTableauMembre($this) ;
					$this->RemplisseurConfigMembership->RemplitDefinitionColActionsTableauMembre($this) ;
				}
			}
		}
		class PvTableauRolesMSHtml extends PvTableauDonneesHtml
		{
			protected $InclureFiltresRole = 1 ;
			protected $InclureColonnesRole = 1 ;
			public $RemplisseurConfigMembership = null ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeConfigMembership() ;
			}
			protected function InitRemplisseurConfigMembership()
			{
				$this->RemplisseurConfigMembership = & $this->ZoneParent->RemplisseurConfigMembership ;
			}
			protected function ChargeConfigMembership()
			{
				$this->InitRemplisseurConfigMembership() ;
				if($this->InclureColonnesRole || $this->InclureFiltresRole)
				{
					$this->RemplisseurConfigMembership->InitTableauRole($this) ;
				}
				if($this->InclureColonnesRole)
				{
					$this->RemplisseurConfigMembership->RemplitDefinitionsColonneTableauRole($this) ;
				}
				if($this->InclureFiltresRole)
				{
					$this->RemplisseurConfigMembership->RemplitFiltresTableauRole($this) ;
					$this->RemplisseurConfigMembership->RemplitDefinitionColActionsTableauRole($this) ;
				}
			}
		}
		class PvTableauProfilsMSHtml extends PvTableauDonneesHtml
		{
			protected $InclureFiltresProfil = 1 ;
			protected $InclureColonnesProfil = 1 ;
			public $RemplisseurConfigMembership = null ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeConfigMembership() ;
			}
			protected function InitRemplisseurConfigMembership()
			{
				$this->RemplisseurConfigMembership = & $this->ZoneParent->RemplisseurConfigMembership ;
			}
			protected function ChargeConfigMembership()
			{
				$this->InitRemplisseurConfigMembership() ;
				if($this->InclureColonnesProfil || $this->InclureFiltresProfil)
				{
					$this->RemplisseurConfigMembership->InitTableauProfil($this) ;
				}
				if($this->InclureColonnesProfil)
				{
					$this->RemplisseurConfigMembership->RemplitDefinitionsColonneTableauProfil($this) ;
				}
				if($this->InclureFiltresProfil)
				{
					$this->RemplisseurConfigMembership->RemplitFiltresTableauProfil($this) ;
					$this->RemplisseurConfigMembership->RemplitDefinitionColActionsTableauProfil($this) ;
				}
			}
		}
			
		class PvFormulaireAjoutMembreMS extends PvFormulaireAjoutDonneesHtml
		{
			protected $InclureFiltresMembre = 1 ;
			public $FiltreIdMembreEnCours ;
			public $FiltreIdMembre ;
			public $FiltreLoginMembre ;
			public $FiltreMotPasseMembre ;
			public $FiltreEmailMembre ;
			public $FiltreContactMembre ;
			public $FiltreConfirmMotPasseMembre ;
			public $FiltreAdresseMembre ;
			public $FiltreNomMembre ;
			public $FiltrePrenomMembre ;
			public $FiltreAuthentifierADMembre ;
			public $FiltreActiverMembre ;
			public $FiltreProfilMembre ;
			public $NomClasseCommandeExecuter = "PvCommandeAjoutMembreMS" ;
			public $RemplisseurConfigMembership = null ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->InitRemplisseurConfigMembership() ;
				if($this->InclureFiltresMembre)
				{
					$this->RemplisseurConfigMembership->RemplitFormulaireGlobalMembre($this) ;
				}
			}
			protected function InitRemplisseurConfigMembership()
			{
				$this->RemplisseurConfigMembership = & $this->ZoneParent->RemplisseurConfigMembership ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				parent::InitDessinateurFiltresEdition() ;
				if($this->EstNul($this->DessinateurFiltresEdition))
				{
					return ;
				}
				$this->DessinateurFiltresEdition->MaxFiltresParLigne = 1 ;
			}
		}
		class PvFormulaireInscriptionMembreMS extends PvFormulaireAjoutMembreMS
		{
			public $NomClasseCommandeExecuter = "PvCommandeInscriptionMembreMS" ;
		}
		class PvFormulaireModifMembreMS extends PvFormulaireModifDonneesHtml
		{
			protected $InclureFiltresMembre = 1 ;
			public $FiltreIdMembreEnCours ;
			public $FiltreIdMembre ;
			public $FiltreLoginMembre ;
			public $FiltreMotPasseMembre ;
			public $FiltreConfirmMotPasseMembre ;
			public $FiltreEmailMembre ;
			public $FiltreContactMembre ;
			public $FiltreAdresseMembre ;
			public $FiltreNomMembre ;
			public $FiltrePrenomMembre ;
			public $FiltreAuthentifierADMembre ;
			public $FiltreActiverMembre ;
			public $FiltreProfilMembre ;
			public $NomClasseCommandeExecuter = "PvCommandeModifMembreMS" ;
			public $RemplisseurConfigMembership = null ;
			public $UtiliserFiltresGlobaux = 1 ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->InitRemplisseurConfigMembership() ;
				if($this->InclureFiltresMembre)
				{
					if($this->UtiliserFiltresGlobaux)
					{
						$this->RemplisseurConfigMembership->RemplitFormulaireGlobalMembre($this) ;
					}
					else
					{
						$this->RemplisseurConfigMembership->RemplitFormulaireInfosMembre($this) ;
					}
				}
			}
			protected function InitRemplisseurConfigMembership()
			{
				$this->RemplisseurConfigMembership = & $this->ZoneParent->RemplisseurConfigMembership ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				parent::InitDessinateurFiltresEdition() ;
				if($this->EstNul($this->DessinateurFiltresEdition))
				{
					return ;
				}
				$this->DessinateurFiltresEdition->MaxFiltresParLigne = 1 ;
			}
		}
		class PvFormulaireModifInfosMS extends PvFormulaireModifMembreMS
		{
			protected $InclureFiltresMembre = 1 ;
			public $UtiliserFiltresGlobaux = 0 ;
			public $InscrireCommandeAnnuler = 0 ;
		}
		class PvFormulaireSupprMembreMS extends PvFormulaireSupprDonneesHtml
		{
			public $NomClasseCommandeExecuter = "PvCommandeSupprMembreMS" ;
			protected $InclureFiltresMembre = 1 ;
			public $FiltreIdMembreEnCours ;
			public $FiltreConfirmMotPasseMembre ;
			public $FiltreIdMembre ;
			public $FiltreLoginMembre ;
			public $FiltreMotPasseMembre ;
			public $FiltreEmailMembre ;
			public $FiltreContactMembre ;
			public $FiltreAdresseMembre ;
			public $FiltreNomMembre ;
			public $FiltrePrenomMembre ;
			public $FiltreAuthentifierADMembre ;
			public $FiltreActiverMembre ;
			public $FiltreProfilMembre ;
			public $RemplisseurConfigMembership = null ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->InitRemplisseurConfigMembership() ;
				if($this->InclureFiltresMembre)
				{
					$this->RemplisseurConfigMembership->RemplitFormulaireGlobalMembre($this) ;
				}
			}
			protected function InitRemplisseurConfigMembership()
			{
				$this->RemplisseurConfigMembership = & $this->ZoneParent->RemplisseurConfigMembership ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				parent::InitDessinateurFiltresEdition() ;
				if($this->EstNul($this->DessinateurFiltresEdition))
				{
					return ;
				}
				$this->DessinateurFiltresEdition->MaxFiltresParLigne = 1 ;
			}
		}
		class PvFormulaireChangeMPMembreMS extends PvFormulaireDonneesHtml
		{
			public $NomClasseCommandeExecuter = "PvCommandeChangeMPMembreMS" ;
			protected $InclureFiltresMembre = 1 ;
			public $FiltreLoginMembre = null ;
			public $FiltreAncMotPasseMembre = null ;
			public $FiltreNouvMotPasseMembre = null ;
			public $FiltreConfirmMotPasseMembre = null ;
			public $RemplisseurConfigMembership = null ;
			public $UtiliserFiltresGlobaux = 1 ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->InitRemplisseurConfigMembership() ;
				if($this->InclureFiltresMembre)
				{
					$this->RemplisseurConfigMembership->RemplitFormulaireChangeMPMembre($this) ;
				}
			}
			protected function InitRemplisseurConfigMembership()
			{
				$this->RemplisseurConfigMembership = & $this->ZoneParent->RemplisseurConfigMembership ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				parent::InitDessinateurFiltresEdition() ;
				if($this->EstNul($this->DessinateurFiltresEdition))
				{
					return ;
				}
				$this->DessinateurFiltresEdition->MaxFiltresParLigne = 1 ;
			}
		}
		class PvFormulaireDoitChangerMotPasseMS extends PvFormulaireChangeMPMembreMS
		{
			public $UtiliserFiltresGlobaux = 0 ;
			public $NomClasseCommandeExecuter = "PvCommandeDoitChangerMotPasseMS" ;
		}
		class PvFormulaireChangeMotPasseMS extends PvFormulaireChangeMPMembreMS
		{
			public $UtiliserFiltresGlobaux = 0 ;
		}
		
		class PvFormulaireAjoutRoleMS extends PvFormulaireAjoutDonneesHtml
		{
			protected $InclureFiltresRole = 1 ;
			public $FiltreIdRoleEnCours ;
			public $FiltreNomRole ;
			public $FiltreTitreRole ;
			public $FiltreDescriptionRole ;
			public $FiltreListeProfilsRole ;
			public $NomClasseCommandeExecuter = "PvCommandeAjoutRoleMS" ;
			public $RemplisseurConfigMembership = null ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->InitRemplisseurConfigMembership() ;
				if($this->InclureFiltresRole)
				{
					$this->RemplisseurConfigMembership->RemplitFormulaireGlobalRole($this) ;
				}
			}
			protected function InitRemplisseurConfigMembership()
			{
				$this->RemplisseurConfigMembership = & $this->ZoneParent->RemplisseurConfigMembership ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				parent::InitDessinateurFiltresEdition() ;
				if($this->EstNul($this->DessinateurFiltresEdition))
				{
					return ;
				}
				$this->DessinateurFiltresEdition->MaxFiltresParLigne = 1 ;
			}
		}
		class PvFormulaireModifRoleMS extends PvFormulaireModifDonneesHtml
		{
			protected $InclureFiltresRole = 1 ;
			public $FiltreIdRoleEnCours ;
			public $FiltreNomRole ;
			public $FiltreTitreRole ;
			public $FiltreDescriptionRole ;
			public $FiltreListeProfilsRole ;
			public $NomClasseCommandeExecuter = "PvCommandeModifRoleMS" ;
			public $RemplisseurConfigMembership = null ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->InitRemplisseurConfigMembership() ;
				if($this->InclureFiltresRole)
				{
					$this->RemplisseurConfigMembership->RemplitFormulaireGlobalRole($this) ;
				}
			}
			protected function InitRemplisseurConfigMembership()
			{
				$this->RemplisseurConfigMembership = & $this->ZoneParent->RemplisseurConfigMembership ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				parent::InitDessinateurFiltresEdition() ;
				if($this->EstNul($this->DessinateurFiltresEdition))
				{
					return ;
				}
				$this->DessinateurFiltresEdition->MaxFiltresParLigne = 1 ;
			}
		}
		class PvFormulaireSupprRoleMS extends PvFormulaireSupprDonneesHtml
		{
			protected $InclureFiltresRole = 1 ;
			public $FiltreIdRoleEnCours ;
			public $FiltreNomRole ;
			public $FiltreTitreRole ;
			public $FiltreDescriptionRole ;
			public $FiltreListeProfilsRole ;
			public $NomClasseCommandeExecuter = "PvCommandeSupprRoleMS" ;
			public $RemplisseurConfigMembership = null ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->InitRemplisseurConfigMembership() ;
				if($this->InclureFiltresRole)
				{
					$this->RemplisseurConfigMembership->RemplitFormulaireGlobalRole($this) ;
				}
			}
			protected function InitRemplisseurConfigMembership()
			{
				$this->RemplisseurConfigMembership = & $this->ZoneParent->RemplisseurConfigMembership ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				parent::InitDessinateurFiltresEdition() ;
				if($this->EstNul($this->DessinateurFiltresEdition))
				{
					return ;
				}
				$this->DessinateurFiltresEdition->MaxFiltresParLigne = 1 ;
			}
		}
		
		class PvFormulaireAjoutProfilMS extends PvFormulaireAjoutDonneesHtml
		{
			protected $InclureFiltresProfil = 1 ;
			public $FiltreIdProfilEnCours ;
			public $FiltreTitreProfil ;
			public $FiltreDescriptionProfil ;
			public $FiltreListeRolesProfil ;
			public $NomClasseCommandeExecuter = "PvCommandeAjoutProfilMS" ;
			public $RemplisseurConfigMembership = null ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->InitRemplisseurConfigMembership() ;
				if($this->InclureFiltresProfil)
				{
					$this->RemplisseurConfigMembership->RemplitFormulaireGlobalProfil($this) ;
				}
			}
			protected function InitRemplisseurConfigMembership()
			{
				$this->RemplisseurConfigMembership = & $this->ZoneParent->RemplisseurConfigMembership ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				parent::InitDessinateurFiltresEdition() ;
				if($this->EstNul($this->DessinateurFiltresEdition))
				{
					return ;
				}
				$this->DessinateurFiltresEdition->MaxFiltresParLigne = 1 ;
			}
		}
		class PvFormulaireModifProfilMS extends PvFormulaireModifDonneesHtml
		{
			protected $InclureFiltresProfil = 1 ;
			public $FiltreIdProfilEnCours ;
			public $FiltreTitreProfil ;
			public $FiltreDescriptionProfil ;
			public $FiltreListeRolesProfil ;
			public $NomClasseCommandeExecuter = "PvCommandeModifProfilMS" ;
			public $RemplisseurConfigMembership = null ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->InitRemplisseurConfigMembership() ;
				if($this->InclureFiltresProfil)
				{
					$this->RemplisseurConfigMembership->RemplitFormulaireGlobalProfil($this) ;
				}
			}
			protected function InitRemplisseurConfigMembership()
			{
				$this->RemplisseurConfigMembership = & $this->ZoneParent->RemplisseurConfigMembership ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				parent::InitDessinateurFiltresEdition() ;
				if($this->EstNul($this->DessinateurFiltresEdition))
				{
					return ;
				}
				$this->DessinateurFiltresEdition->MaxFiltresParLigne = 1 ;
			}
		}
		class PvFormulaireSupprProfilMS extends PvFormulaireSupprDonneesHtml
		{
			protected $InclureFiltresProfil = 1 ;
			public $FiltreIdProfilEnCours ;
			public $FiltreTitreProfil ;
			public $FiltreDescriptionProfil ;
			public $FiltreListeRolesProfil ;
			public $NomClasseCommandeExecuter = "PvCommandeSupprProfilMS" ;
			public $RemplisseurConfigMembership = null ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->InitRemplisseurConfigMembership() ;
				if($this->InclureFiltresProfil)
				{
					$this->RemplisseurConfigMembership->RemplitFormulaireGlobalProfil($this) ;
				}
			}
			protected function InitRemplisseurConfigMembership()
			{
				$this->RemplisseurConfigMembership = & $this->ZoneParent->RemplisseurConfigMembership ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				parent::InitDessinateurFiltresEdition() ;
				if($this->EstNul($this->DessinateurFiltresEdition))
				{
					return ;
				}
				$this->DessinateurFiltresEdition->MaxFiltresParLigne = 1 ;
			}
		}
		
		class PvCommandeEditionMembreMSBase extends PvCommandeEditionElementBase
		{
			protected $InclureVerifFiltresMembre = 1 ;
			protected $StatutVerifFiltresMembre = 0 ;
			public function PrepareRendu(& $composant)
			{
				if($composant->EstPasNul($composant->FiltreLoginMembre))
				{
					$composant->FiltreLoginMembre->InsereSuffxErr("*") ;
				}
				if(isset($composant->FiltreMotPasse) && $composant->EstPasNul($composant->FiltreMotPasseMembre))
				{
					$composant->FiltreMotPasseMembre->InsereSuffxErr("*") ;
				}
				if(isset($composant->FiltreEmailMembre) && $composant->EstPasNul($composant->FiltreEmailMembre))
				{
					$composant->FiltreEmailMembre->InsereSuffxErr("*") ;
				}
				if(isset($composant->FiltreNomMembre) && $composant->EstPasNul($composant->FiltreNomMembre))
				{
					$composant->FiltreNomMembre->InsereSuffxErr("*") ;
				}
				if(isset($composant->FiltrePrenomMembre) && $composant->EstPasNul($composant->FiltrePrenomMembre))
				{
					$composant->FiltrePrenomMembre->InsereSuffxErr("*") ;
				}
			}
			/*
			public $MsgErreurLoginVide = "Le login doit avoir au moins 4 caract&egrave;res et 30 au maximum" ;
			public $MsgErreurLoginIncorrect = "Le login a un mauvais format" ;
			public $MsgErreurMotPasseVide = "Le mot de passe doit avoir au moins 4 caract&egrave;res et 30 au maximum" ;
			public $MsgErreurMotPasseIncorrect = "Le mot de passe a un mauvais format" ;
			public $MsgErreurNomVide = "Le mot de passe doit avoir au moins 4 caract&egrave;res et 90 au maximum" ;
			public $MsgErreurPrenomVide = "Le prenom doit avoir au moins 4 caract&egrave;res et 255 au maximum" ;
			*/
			protected function RenseigneErreurFiltresMembre($messageErreur = '')
			{
				if($messageErreur != '')
				{
					$this->StatutVerifFiltresMembre = 0 ;
				}
				else
				{
					$this->StatutVerifFiltresMembre = 1 ;
				}
				$this->RenseigneErreur($messageErreur) ;
			}
			protected function ValideFormatLogin($valeur)
			{
				$membership = & $this->FormulaireDonneesParent->ZoneParent->Membership ;
				$ok = false ;
				if($membership->LoginWithEmail == 0)
				{
					$ok = validate_name_user_format($valeur) ;
				}
				else
				{
					$ok = validate_email_format($valeur) ;
				}
				return $ok ;
			}
			protected function ValideFormatMotPasse($valeur)
			{
				return validate_password_format($valeur) ;
			}
			protected function ValideFormatEmail($valeur)
			{
				return validate_email_format($valeur) ;
			}
			protected function VerifieFiltresMembre()
			{
				if(! $this->InclureVerifFiltresMembre || $this->Mode == 3)
				{
					return ;
				}
				$form = & $this->FormulaireDonneesParent ;
				$membership = & $this->FormulaireDonneesParent->ZoneParent->Membership ;
				$this->FormulaireDonneesParent->LieTousLesFiltres() ;
				$this->StatutVerifFiltresMembre = 1 ;
				if($this->FormulaireDonneesParent->FiltreLoginMembre->EstEtiquette == 0)
				{
					if(empty($this->FormulaireDonneesParent->FiltreLoginMembre->ValeurParametre) || strlen($this->FormulaireDonneesParent->FiltreLoginMembre->ValeurParametre) < 4 || strlen($this->FormulaireDonneesParent->FiltreLoginMembre->ValeurParametre) > 30)
					{
						return $this->RenseigneErreurFiltresMembre($this->FormulaireDonneesParent->ZoneParent->Membership->LoginMemberFormatErrorLabel) ;
					}
					if(! $this->ValideFormatLogin($this->FormulaireDonneesParent->FiltreLoginMembre->ValeurParametre))
					{
						if($this->FormulaireDonneesParent->ZoneParent->Membership->LoginWithEmail == 0)
						{
							return $this->RenseigneErreurFiltresMembre($this->FormulaireDonneesParent->ZoneParent->Membership->LoginMemberFormatErrorLabel) ;
						}
						else
						{
							return $this->RenseigneErreurFiltresMembre($this->FormulaireDonneesParent->ZoneParent->Membership->EmailMemberFormatErrorLabel) ;
						}
					}
				}
				if($this->Mode == 1)
				{
					$authentifierParAD = 0 ;
					if($membership->ADActivatedMemberColumn != '')
					{
						if($form->FiltreAuthentifierADMembre->Lie() == 1)
						{
							$authentifierParAD = 1 ;
						}
					}
					if($authentifierParAD == 0)
					{
						if(empty($this->FormulaireDonneesParent->FiltreMotPasseMembre->ValeurParametre) || strlen($this->FormulaireDonneesParent->FiltreMotPasseMembre->ValeurParametre) < 4 || strlen($this->FormulaireDonneesParent->FiltreMotPasseMembre->ValeurParametre) > 30)
						{
							return $this->RenseigneErreurFiltresMembre($this->FormulaireDonneesParent->ZoneParent->Membership->PasswordMemberFormatErrorLabel) ;
						}
						if(! $this->ValideFormatMotPasse($this->FormulaireDonneesParent->FiltreMotPasseMembre->ValeurParametre))
						{
							return $this->RenseigneErreurFiltresMembre($this->FormulaireDonneesParent->ZoneParent->Membership->PasswordMemberFormatErrorLabel) ;
						}
					}
				}
				if(empty($this->FormulaireDonneesParent->FiltreNomMembre->ValeurParametre) || strlen($this->FormulaireDonneesParent->FiltreNomMembre->ValeurParametre) < 2 || strlen($this->FormulaireDonneesParent->FiltreNomMembre->ValeurParametre) > 90)
				{
					return $this->RenseigneErreurFiltresMembre($this->FormulaireDonneesParent->ZoneParent->Membership->LastNameMemberFormatErrorLabel) ;
				}
				if(empty($this->FormulaireDonneesParent->FiltrePrenomMembre->ValeurParametre) || strlen($this->FormulaireDonneesParent->FiltrePrenomMembre->ValeurParametre) < 2 || strlen($this->FormulaireDonneesParent->FiltrePrenomMembre->ValeurParametre) > 255)
				{
					return $this->RenseigneErreurFiltresMembre($this->FormulaireDonneesParent->ZoneParent->Membership->FirstNameMemberFormatErrorLabel) ;
				}
				if($membership->LoginWithEmail == 0)
				{
					if(! $this->ValideFormatEmail($this->FormulaireDonneesParent->FiltreEmailMembre->ValeurParametre))
					{
						return $this->RenseigneErreurFiltresMembre($this->FormulaireDonneesParent->ZoneParent->Membership->EmailMemberFormatErrorLabel) ;
					}
				}
				$form = & $this->FormulaireDonneesParent ;
				if($membership->ConfirmSetPasswordEnabled == 1 && $form->EstPasNul($form->FiltreConfirmMotPasseMembre))
				{
					if($form->FiltreConfirmMotPasseMembre->Lie() != $form->FiltreMotPasseMembre->Lie())
					{
						return $this->RenseigneErreurFiltresMembre($this->FormulaireDonneesParent->ZoneParent->Membership->ConfirmPasswordMemberMatchLabel) ;
					}
				}
				$idMembre = ($this->FormulaireDonneesParent->EstNul($this->FormulaireDonneesParent->FiltreIdMembreEnCours)) ? '' : $this->FormulaireDonneesParent->FiltreIdMembreEnCours->ValeurParametre ;
				$motPasse = ($this->FormulaireDonneesParent->EstNul($this->FormulaireDonneesParent->FiltreMotPasseMembre)) ? '' : $this->FormulaireDonneesParent->FiltreMotPasseMembre->ValeurParametre ;
				$membreSimilaire = $this->FormulaireDonneesParent->ZoneParent->Membership->FetchSimilarMember(
					$idMembre,
					$this->FormulaireDonneesParent->FiltreLoginMembre->ValeurParametre,
					$motPasse,
					($membership->LoginWithEmail == 0) ? $this->FormulaireDonneesParent->FiltreEmailMembre->ValeurParametre : $this->FormulaireDonneesParent->FiltreLoginMembre->ValeurParametre
				) ;
				if(count($membreSimilaire) > 0)
				{
					return $this->RenseigneErreurFiltresMembre($this->FormulaireDonneesParent->ZoneParent->Membership->SimilarMemberFoundErrorLabel) ;
				}
			}
			public function ExecuteInstructions()
			{
				$this->VerifieFiltresMembre() ;
				if($this->StatutVerifFiltresMembre == 0)
				{
					return ;
				}
				if($this->InterditModifsSysteme())
				{
					return ;
				}
				parent::ExecuteInstructions() ;
			}
			protected function InterditModifsSysteme()
			{
				$form = & $this->FormulaireDonneesParent ;
				if($form->InclureElementEnCours == 0)
				{
					return 0 ;
				}
				$idMembre = $form->FiltreIdMembreEnCours->Lie() ;
				$membership = & $this->ZoneParent->Membership ;
				$bd = & $membership->Database ;
				if($this->Mode == 2)
				{
					if(in_array($idMembre, array($membership->RootMemberId, $membership->GuestMemberId)))
					{
						$lgnMembre = $membership->FetchMemberRow($idMembre) ;
						if($lgnMembre["MEMBER_PROFILE"] != $form->FiltreProfilMembre->Lie())
						{
							$this->RenseigneErreur("Vous ne pouvez pas changer le profil d'un compte ROOT ou INVITE") ;
							return 1 ;
						}
					}
					if($idMembre == $membership->RootMemberId && $form->FiltreActiverMembre->Lie() == 0)
					{
						$this->RenseigneErreur("Vous ne pouvez pas desactiver le compte ROOT") ;
						return 1 ;
					}
				}
				return 0 ;
			}
		}
		class PvCommandeAjoutMembreMS extends PvCommandeEditionMembreMSBase
		{
			public $Mode = 1 ;
			public $MessageSuccesExecution = "Le membre a &eacute;t&eacute; ajout&eacute; avec succ&egrave;s" ;
		}
		class PvCommandeInscriptionMembreMS extends PvCommandeAjoutMembreMS
		{
			public function ExecuteInstructions()
			{
				parent::ExecuteInstructions() ;
				$form = & $this->FormulaireDonneesParent ;
				$membership = & $form->ZoneParent->Membership ;
				$script = & $form->ScriptParent ;
				$email = ($membership->LoginWithEmail == 0) ? $form->FiltreEmailMembre->Lie() : $form->FiltreLoginMembre->Lie() ;
				$membership = & $script->ZoneParent->Membership ;
				if($this->StatutExecution == 1 && $script->DoitConfirmMail())
				{
					$params = $form->ExtraitValeursParametre($form->FiltresEdition) ;
					$paramsUrlConfirm = array(
						"login_confirm" => $form->FiltreLoginMembre->Lie(),
						"email_confirm" => $email,
						"code_confirm" => $script->CodeConfirmMail(),
					) ;
					if($script->AutoriserUrlsRetour == 1)
					{
						$paramsUrlConfirm[$script->NomParamUrlRetour] = $script->ValeurUrlRetour ;
					}
					$params["url"] = $script->ObtientUrlParam($paramsUrlConfirm) ;
					$sujetMail = _parse_pattern($script->SujetMailConfirm, $params) ;
					$corpsMail = _parse_pattern($script->CorpsMailConfirm, $params) ;
					send_html_mail($email, $sujetMail, $corpsMail, $script->EmailEnvoiConfirm) ;
					$this->ConfirmeSucces($script->MsgSuccesEnvoiMailConfirm) ;
				}
				elseif($this->StatutExecution == 1)
				{
					$row = $membership->FetchMemberRowByLogin($form->FiltreLoginMembre->Lie()) ;
					if($script->EnvoiMailSucces == 1)
					{
						$row["login_member"] = $form->FiltreLoginMembre->Lie() ;
						$row["password_member"] = $form->FiltreMotPasseMembre->Lie() ;
						$sujetMail = _parse_pattern($script->SujetMailSuccesConfirm, $row) ;
						$corpsMail = _parse_pattern($script->CorpsMailSuccesConfirm, $row) ;
						send_html_mail($email, $sujetMail, $corpsMail, $script->EmailEnvoiConfirm) ;
					}
					if($script->ConnecterNouveauMembre == 1 || ($script->AutoriserUrlsRetour== 1 && $script->ValeurUrlRetour != ''))
					{
						$script->AutoConnecteNouveauMembre($row["MEMBER_ID"]) ;
						if($script->AutoriserUrlsRetour== 1 && $script->ValeurUrlRetour != '')
						{
							redirect_to($script->ValeurUrlRetour) ;
						}
						else
						{
							redirect_to($script->UrlAutoConnexionMembre) ;
						}
					}
				}
			}
		}
		class PvCommandeModifMembreMS extends PvCommandeEditionMembreMSBase
		{
			public $Mode = 2 ;
			public $MessageSuccesExecution = "Le membre a &eacute;t&eacute; modifi&eacute; avec succ&egrave;s" ;
		}
		class PvCommandeSupprMembreMS extends PvCommandeEditionMembreMSBase
		{
			public $Mode = 3 ;
			public $MessageSuccesExecution = "Le membre a &eacute;t&eacute; supprim&eacute; avec succ&egrave;s" ;
			public function ExecuteInstructions()
			{
				$form = & $this->FormulaireDonneesParent ;
				$idMembre = $form->FiltreIdMembreEnCours->Lie() ;
				$membership = & $this->ZoneParent->Membership ;
				$bd = & $membership->Database ;
				if($idMembre == $membership->RootMemberId)
				{
					$this->RenseigneErreur("Vous ne pouvez pas desactiver le compte ROOT") ;
					return ;
				}
				if($membership->DisableMemberOnDelete)
				{
					$this->FormulaireDonneesParent->LieFiltresSelection() ;
					$this->FormulaireDonneesParent->LieFiltresEdition() ;
					$ok = $membership->Database->UpdateRow(
						$membership->MemberTable,
						array(
							$membership->EnableMemberColumn => ($membership->EnableMemberTrueValue == 1) ? 0 : 1
						),
						$membership->Database->EscapeFieldName($membership->MemberTable, $membership->IdMemberColumn).' = '.$membership->Database->ParamPrefix.'IdMembre',
						array(
							'IdMembre' => $this->FormulaireDonneesParent->FiltreIdMembreEnCours->ValeurParametre
						)
					) ;
					if(! $ok && $membership->Database->ConnectionException != "")
					{
						$this->RenseigneErreur($membership->Database->ConnectionException) ;
					}
					elseif(! $ok)
					{
						$this->RenseigneErreur($this->MessageErreurExecution) ;
					}
					else
					{
						$this->FormulaireDonneesParent->CacherFormulaireFiltres = 1 ;
						$this->MessageExecution = $this->MessageSuccesExecution ;
					}
				}
				else
				{
					parent::ExecuteInstructions() ;
				}
			}
		}
		class PvCommandeChangeMPMembreMS extends PvCommandeEditionMembreMSBase
		{
			public $MessageSuccesExecution = "Le mot de passe a &eacute;t&eacute; chang&eacute; avec succ&egrave;s" ;
			public $Mode = 2 ;
			protected function VerifieFiltresMembre()
			{
				$this->StatutVerifFiltresMembre = 1 ;
				$membership = $this->FormulaireDonneesParent->ZoneParent->Membership ;
				$this->FormulaireDonneesParent->LieTousLesFiltres() ;
				// echo "ID Membre : ".$this->FormulaireDonneesParent->FiltreIdMembreEnCours->ValeurParametre.'<br>' ;
				if(! $this->FormulaireDonneesParent->UtiliserFiltresGlobaux && ! $this->ValideFormatMotPasse($this->FormulaireDonneesParent->FiltreAncMotPasseMembre->ValeurParametre))
				{
					return $this->RenseigneErreurFiltresMembre($this->FormulaireDonneesParent->ZoneParent->Membership->PasswordMemberFormatErrorLabel) ;
				}
				if(! $this->ValideFormatMotPasse($this->FormulaireDonneesParent->FiltreNouvMotPasseMembre->ValeurParametre))
				{
					return $this->RenseigneErreurFiltresMembre($this->FormulaireDonneesParent->ZoneParent->Membership->NewPasswordMemberFormatErrorLabel) ;
				}
				if(! $this->FormulaireDonneesParent->UtiliserFiltresGlobaux)
				{
					$idMembre = $membership->ValidateConnection(
						$this->FormulaireDonneesParent->FiltreLoginMembre->ValeurParametre,
						$this->FormulaireDonneesParent->FiltreAncMotPasseMembre->ValeurParametre
					) ;
					if($idMembre == $membership->IdMemberNotFoundValue)
					{
						return $this->RenseigneErreurFiltresMembre($this->FormulaireDonneesParent->ZoneParent->Membership->OldPasswordMemberMatchLabel) ;						
					}
				}
				if($this->FormulaireDonneesParent->FiltreNouvMotPasseMembre->ValeurParametre != $this->FormulaireDonneesParent->FiltreConfirmMotPasseMembre->ValeurParametre)
				{
 					return $this->RenseigneErreurFiltresMembre($this->FormulaireDonneesParent->ZoneParent->Membership->ConfirmPasswordMemberMatchLabel) ;
				}
			}
			public function ExecuteInstructions()
			{
				$this->VerifieFiltresMembre() ;
				if($this->StatutVerifFiltresMembre == 0)
				{
					return ;
				}
				// $membreEnCours = $this->FormulaireDonneesParent->ZoneParent->Membership->FetchMember($this->FormulaireDonneesParent->FiltreIdMembreEnCours->ValeurParametre) ;
				$membership = $this->FormulaireDonneesParent->ZoneParent->Membership ;
				$filtreNouvMotPasse = $this->FormulaireDonneesParent->FiltreNouvMotPasseMembre ;
				$filtreNouvMotPasse->NomColonneLiee = $this->FormulaireDonneesParent->ZoneParent->Membership->PasswordMemberColumn ;
				if($this->FormulaireDonneesParent->ZoneParent->Membership->PasswordMemberExpr != '')
				{
					$filtreNouvMotPasse->ExpressionColonneLiee = $this->FormulaireDonneesParent->ZoneParent->Membership->PasswordMemberExpr.'(${THIS})' ;
				}
				$filtreDoitChangerMotPasse = $this->ScriptParent->CreeFiltreFixe("doitChangerMotPasse", $membership->MustChangePasswordMemberFalseValue()) ;
				$filtreDoitChangerMotPasse->NomColonneLiee = $membership->MustChangePasswordMemberColumn ;
				$filtresSelection = $this->FormulaireDonneesParent->ObtientFiltresSelection() ;
				$succes = $this->FormulaireDonneesParent->FournisseurDonnees->ModifElement(
					$filtresSelection,
					array($filtreNouvMotPasse, $filtreDoitChangerMotPasse)
				) ;
				// print_r($this->FormulaireDonneesParent->FournisseurDonnees->BaseDonnees) ;
				if(! $succes)
				{
					// print_r($this->FormulaireDonneesParent->FournisseurDonnees->BaseDonnees) ;
					if($this->FormulaireDonneesParent->FournisseurDonnees->BaseDonnees->ConnectionException != "")
					{
						$this->RenseigneErreur("Erreur SQL : ".$this->FormulaireDonneesParent->FournisseurDonnees->BaseDonnees->ConnectionException) ;
					}
					else
					{
						$this->RenseigneErreur($this->MessageErreurExecution) ;
					}
				}
				else
				{
					$this->MessageExecution = $this->MessageSuccesExecution ;
				}
			}
		}
		class PvCommandeDoitChangerMotPasseMS extends PvCommandeChangeMPMembreMS
		{
			public $Mode = 2 ;
		}
		
		class PvCommandeEditionRoleMSBase extends PvCommandeEditionElementBase
		{
			protected $InclureVerifFiltresRole = 1 ;
			protected $StatutVerifFiltresRole = 0 ;
			protected function RenseigneErreurFiltresRole($messageErreur = '')
			{
				if($messageErreur != '')
				{
					$this->StatutVerifFiltresRole = 0 ;
				}
				else
				{
					$this->StatutVerifFiltresRole = 1 ;
				}
				$this->RenseigneErreur($messageErreur) ;
			}
			protected function VerifieFiltresRole()
			{
				if(! $this->InclureVerifFiltresRole || $this->Mode == 3)
				{
					return ;
				}
				$this->FormulaireDonneesParent->LieTousLesFiltres() ;
				// echo "tt : ".$this->FormulaireDonneesParent->FiltreTitreRole->ValeurParametre ;
				$this->StatutVerifFiltresRole = 1 ;
				if(empty($this->FormulaireDonneesParent->FiltreTitreRole->ValeurParametre) || strlen($this->FormulaireDonneesParent->FiltreTitreRole->ValeurParametre) < 4 || strlen($this->FormulaireDonneesParent->FiltreTitreRole->ValeurParametre) > 50)
				{
					return $this->RenseigneErreurFiltresRole($this->FormulaireDonneesParent->ZoneParent->Membership->TitleRoleFormatErrorLabel) ;
				}
				if(empty($this->FormulaireDonneesParent->FiltreNomRole->ValeurParametre) || strlen($this->FormulaireDonneesParent->FiltreNomRole->ValeurParametre) < 4 || strlen($this->FormulaireDonneesParent->FiltreNomRole->ValeurParametre) > 30)
				{
					return $this->RenseigneErreurFiltresRole($this->FormulaireDonneesParent->ZoneParent->Membership->NameRoleFormatErrorLabel) ;
				}
				$idRole = ($this->FormulaireDonneesParent->EstNul($this->FormulaireDonneesParent->FiltreIdRoleEnCours)) ? '' : $this->FormulaireDonneesParent->FiltreIdRoleEnCours->ValeurParametre ;
				$roleSimilaire = $this->FormulaireDonneesParent->ZoneParent->Membership->FetchSimilarRole(
					$idRole,
					$this->FormulaireDonneesParent->FiltreNomRole->ValeurParametre,
					$this->FormulaireDonneesParent->FiltreTitreRole->ValeurParametre
				) ;
				if(count($roleSimilaire) > 0)
				{
					return $this->RenseigneErreurFiltresRole($this->FormulaireDonneesParent->ZoneParent->Membership->SimilarRoleFoundErrorLabel) ;
				}
			}
			public function ExecuteInstructions()
			{
				$this->VerifieFiltresRole() ;
				if($this->StatutVerifFiltresRole == 0)
				{
					return ;
				}
				parent::ExecuteInstructions() ;
				$this->RattachePrivileges() ;
			}
			protected function RattachePrivileges()
			{
				if($this->StatutExecution != 1 || ($this->Mode != 1 && $this->Mode != 2))
				{
					return ;
				}
				// print_r($this->FormulaireDonneesParent->FiltreListeProfilsRole->ValeurBrute) ;
				$membership = & $this->FormulaireDonneesParent->ZoneParent->Membership ;
				$basedonnees = & $membership->Database ;
				$idRole = 0 ;
				if($this->Mode == 2)
				{
					$idRole = $this->FormulaireDonneesParent->FiltreIdRoleEnCours->ValeurParametre ;
					$sql = "DELETE FROM ".$basedonnees->EscapeTableName($membership->PrivilegeTable)." where ".$basedonnees->EscapeFieldName($membership->PrivilegeTable, $membership->RolePrivilegeColumn)." = ".$basedonnees->ParamPrefix."roleId" ;
					$basedonnees->RunSql($sql, array('roleId' => $idRole)) ;
					// $this->CaptureExceptionBaseDonnees($basedonnees, __FILE__, __LINE__) ;
					// echo $sql.'<br>' ;
				}
				else
				{
					$sql = "select ".$basedonnees->EscapeFieldName($membership->RoleTable, $membership->IdRoleColumn)." ROLE_ID from ".$basedonnees->EscapeTableName($membership->RoleTable)." LEFT JOIN ".$basedonnees->EscapeTableName($membership->PrivilegeTable)." ON ".$basedonnees->EscapeFieldName($membership->RoleTable, $membership->RolePrivilegeForeignKey)."=".$basedonnees->EscapeFieldName($membership->PrivilegeTable, $membership->RolePrivilegeColumn)." where ".$basedonnees->EscapeFieldName($membership->PrivilegeTable, $membership->RolePrivilegeColumn)." is null" ;
					$idRole = $basedonnees->FetchSqlValue($sql, array(), "ROLE_ID") ;
					// echo $sql." : ".$idRole ;
					// echo $sql.'<br>' ;
				}
				$sql = "INSERT INTO ".$basedonnees->EscapeTableName($membership->PrivilegeTable)." (".$basedonnees->EscapeFieldName($membership->PrivilegeTable, $membership->ProfilePrivilegeColumn).", ".$basedonnees->EscapeFieldName($membership->PrivilegeTable, $membership->RolePrivilegeColumn).", ".$basedonnees->EscapeFieldName($membership->PrivilegeTable, $membership->EnablePrivilegeColumn).") select ".$basedonnees->EscapeFieldName($membership->ProfileTable, $membership->ProfilePrivilegeForeignKey).", ".$basedonnees->ParamPrefix."roleId, ".$basedonnees->ParamPrefix."roleEnabled from ".$basedonnees->EscapeTableName($membership->ProfileTable) ;
				$basedonnees->RunSql($sql, array("roleEnabled" => $membership->EnablePrivilegeFalseValue(), "roleId" => $idRole)) ;
				// print "kk : ".$this->FormulaireDonneesParent->FiltreListeProfilsRole->ValeurBrute ;
				if(is_array($this->FormulaireDonneesParent->FiltreListeProfilsRole->ValeurBrute))
				{
					foreach($this->FormulaireDonneesParent->FiltreListeProfilsRole->ValeurBrute as $i => $valeur)
					{
						$basedonnees->UpdateRow(
							$membership->PrivilegeTable,
							array($membership->EnablePrivilegeColumn => $membership->EnablePrivilegeTrueValue),
							$basedonnees->EscapeFieldName($membership->PrivilegeTable, $membership->RolePrivilegeColumn).'='.$basedonnees->ParamPrefix.'roleId and '.$basedonnees->EscapeFieldName($membership->PrivilegeTable, $membership->ProfilePrivilegeColumn).' = '.$basedonnees->ParamPrefix.'profileId',
							array(
								'roleId' => $idRole,
								'profileId' => $valeur
							)
						) ;
					}
				}
				// echo $sql.'<br>' ;
			// exit ;
			}
		}
		class PvCommandeAjoutRoleMS extends PvCommandeEditionRoleMSBase
		{
			public $Mode = 1 ;
			public $MessageSuccesExecution = "Le role a &eacute;t&eacute; ajout&eacute; avec succ&egrave;s" ;
		}
		class PvCommandeModifRoleMS extends PvCommandeEditionRoleMSBase
		{
			public $Mode = 2 ;
			public $MessageSuccesExecution = "Le role a &eacute;t&eacute; modifi&eacute; avec succ&egrave;s" ;
		}
		class PvCommandeSupprRoleMS extends PvCommandeEditionRoleMSBase
		{
			public $Mode = 3 ;
			public $MessageSuccesExecution = "Le role a &eacute;t&eacute; supprim&eacute; avec succ&egrave;s" ;
			public function ExecuteInstructions()
			{
				$membership = $this->FormulaireDonneesParent->ZoneParent->Membership ;
				if($membership->DisableRoleOnDelete)
				{
					$this->FormulaireDonneesParent->LieFiltresSelection() ;
					$this->FormulaireDonneesParent->LieFiltresEdition() ;
					$ok = $membership->Database->UpdateRow(
						$membership->RoleTable,
						array(
							$membership->EnableRoleColumn => ($membership->EnableRoleTrueValue == 1) ? 0 : 1
						),
						$membership->Database->EscapeFieldName($membership->RoleTable, $membership->IdRoleColumn).' = '.$membership->Database->ParamPrefix.'IdRole',
						array(
							'IdRole' => $this->FormulaireDonneesParent->FiltreIdRoleEnCours->ValeurParametre
						)
					) ;
					if(! $ok && $membership->Database->ConnectionException != "")
					{
						$this->RenseigneErreur($membership->Database->ConnectionException) ;
					}
					elseif(! $ok)
					{
						$this->RenseigneErreur($this->MessageErreurExecution) ;
					}
					else
					{
						$this->FormulaireDonneesParent->CacherFormulaireFiltres = 1 ;
						$this->MessageExecution = $this->MessageSuccesExecution ;
					}
				}
				else
				{
					parent::ExecuteInstructions() ;
				}
			}
		}
		
		class PvCommandeEditionProfilMSBase extends PvCommandeEditionElementBase
		{
			protected $InclureVerifFiltresProfil = 1 ;
			protected $StatutVerifFiltresProfil = 0 ;
			protected function RenseigneErreurFiltresProfil($messageErreur = '')
			{
				if($messageErreur != '')
				{
					$this->StatutVerifFiltresProfil = 0 ;
				}
				else
				{
					$this->StatutVerifFiltresProfil = 1 ;
				}
				$this->RenseigneErreur($messageErreur) ;
			}
			protected function VerifieFiltresProfil()
			{
				if(! $this->InclureVerifFiltresProfil || $this->Mode == 3)
				{
					return ;
				}
				$this->FormulaireDonneesParent->LieTousLesFiltres() ;
				$this->StatutVerifFiltresProfil = 1 ;
				if(empty($this->FormulaireDonneesParent->FiltreTitreProfil->ValeurParametre) || strlen($this->FormulaireDonneesParent->FiltreTitreProfil->ValeurParametre) < 4 || strlen($this->FormulaireDonneesParent->FiltreTitreProfil->ValeurParametre) > 30)
				{
					return $this->RenseigneErreurFiltresProfil($this->FormulaireDonneesParent->ZoneParent->Membership->TitleProfilFormatErrorLabel) ;
				}
				$idProfil = ($this->FormulaireDonneesParent->EstNul($this->FormulaireDonneesParent->FiltreIdProfilEnCours)) ? '' : $this->FormulaireDonneesParent->FiltreIdProfilEnCours->ValeurParametre ;
				$profilSimilaire = $this->FormulaireDonneesParent->ZoneParent->Membership->FetchSimilarProfile(
					$idProfil,
					$this->FormulaireDonneesParent->FiltreTitreProfil->ValeurParametre
				) ;
				if(count($profilSimilaire) > 0)
				{
					return $this->RenseigneErreurFiltresProfil($this->FormulaireDonneesParent->ZoneParent->Membership->SimilarProfileFoundErrorLabel) ;
				}
			}
			public function ExecuteInstructions()
			{
				$this->VerifieFiltresProfil() ;
				if($this->StatutVerifFiltresProfil == 0)
				{
					return ;
				}
				parent::ExecuteInstructions() ;
				$this->RattachePrivileges() ;
			}
			protected function RattachePrivileges()
			{
				if($this->StatutExecution != 1 || ($this->Mode != 1 && $this->Mode != 2))
				{
					return ;
				}
				$membership = & $this->FormulaireDonneesParent->ZoneParent->Membership ;
				$basedonnees = & $membership->Database ;
				$idProfil = 0 ;
				if($this->Mode == 2)
				{
					$idProfil = $this->FormulaireDonneesParent->FiltreIdProfilEnCours->ValeurParametre ;
					$sql = "DELETE FROM ".$basedonnees->EscapeTableName($membership->PrivilegeTable)." where ".$basedonnees->EscapeFieldName($membership->PrivilegeTable, $membership->ProfilePrivilegeColumn)." = ".$basedonnees->ParamPrefix."profileId" ;
					$basedonnees->RunSql($sql, array('profileId' => $idProfil)) ;
					// $this->CaptureExceptionBaseDonnees($basedonnees, __FILE__, __LINE__) ;
					// echo $sql.'<br>' ;
				}
				else
				{
					$sql = "select ".$basedonnees->EscapeFieldName($membership->ProfileTable, $membership->IdProfileColumn)." PROFILE_ID from ".$basedonnees->EscapeTableName($membership->ProfileTable)." LEFT JOIN ".$basedonnees->EscapeTableName($membership->PrivilegeTable)." ON ".$basedonnees->EscapeFieldName($membership->ProfileTable, $membership->ProfilePrivilegeForeignKey)."=".$basedonnees->EscapeFieldName($membership->PrivilegeTable, $membership->ProfilePrivilegeColumn)." where ".$basedonnees->EscapeFieldName($membership->PrivilegeTable, $membership->ProfilePrivilegeColumn)." is null" ;
					$idProfil = $basedonnees->FetchSqlValue($sql, array(), "PROFILE_ID") ;
					// echo $sql.'<br>' ;
				}
				$sql = "INSERT INTO ".$basedonnees->EscapeTableName($membership->PrivilegeTable)." (".$basedonnees->EscapeFieldName($membership->PrivilegeTable, $membership->RolePrivilegeColumn).", ".$basedonnees->EscapeFieldName($membership->PrivilegeTable, $membership->ProfilePrivilegeColumn).", ".$basedonnees->EscapeFieldName($membership->PrivilegeTable, $membership->EnablePrivilegeColumn).") select ".$basedonnees->EscapeFieldName($membership->RoleTable, $membership->RolePrivilegeForeignKey).", ".$basedonnees->ParamPrefix."profileId, ".$basedonnees->ParamPrefix."profileEnabled from ".$basedonnees->EscapeTableName($membership->RoleTable) ;
				$basedonnees->RunSql($sql, array("profileEnabled" => $membership->EnablePrivilegeFalseValue(), "profileId" => $idProfil)) ;
				// print_r(array($this->FormulaireDonneesParent->FiltreListeRolesProfil->ValeurBrute)) ;
				if(is_array($this->FormulaireDonneesParent->FiltreListeRolesProfil->ValeurBrute))
				{
					foreach($this->FormulaireDonneesParent->FiltreListeRolesProfil->ValeurBrute as $i => $valeur)
					{
						$basedonnees->UpdateRow(
							$membership->PrivilegeTable,
							array($membership->EnablePrivilegeColumn => $membership->EnablePrivilegeTrueValue),
							$basedonnees->EscapeFieldName($membership->PrivilegeTable, $membership->ProfilePrivilegeColumn).'='.$basedonnees->ParamPrefix.'profileId and '.$basedonnees->EscapeFieldName($membership->PrivilegeTable, $membership->RolePrivilegeColumn).' = '.$basedonnees->ParamPrefix.'roleId',
							array(
								'profileId' => $idProfil,
								'roleId' => $valeur
							)
						) ;
						// echo $basedonnees->LastSqlText.' '.print_r($basedonnees->LastSqlParams, true).'<br />' ;
					}
				}
				// echo $sql.'<br>' ;
			}
		}
		class PvCommandeAjoutProfilMS extends PvCommandeEditionProfilMSBase
		{
			public $Mode = 1 ;
			public $MessageSuccesExecution = "Le profil a &eacute;t&eacute; ajout&eacute; avec succ&egrave;s" ;
		}
		class PvCommandeModifProfilMS extends PvCommandeEditionProfilMSBase
		{
			public $Mode = 2 ;
			public $MessageSuccesExecution = "Le profil a &eacute;t&eacute; modifi&eacute; avec succ&egrave;s" ;
		}
		class PvCommandeSupprProfilMS extends PvCommandeEditionProfilMSBase
		{
			public $Mode = 3 ;
			public $MessageSuccesExecution = "Le profil a &eacute;t&eacute; supprim&eacute; avec succ&egrave;s" ;
			public function ExecuteInstructions()
			{
				$membership = $this->FormulaireDonneesParent->ZoneParent->Membership ;
				if($membership->DisableProfilOnDelete)
				{
					$this->FormulaireDonneesParent->LieFiltresSelection() ;
					$this->FormulaireDonneesParent->LieFiltresEdition() ;
					$ok = $membership->Database->UpdateRow(
						$membership->ProfilTable,
						array(
							$membership->EnableProfilColumn => ($membership->EnableProfilTrueValue == 1) ? 0 : 1
						),
						$membership->Database->EscapeFieldName($membership->ProfilTable, $membership->IdProfilColumn).' = '.$membership->Database->ParamPrefix.'IdProfil',
						array(
							'IdProfil' => $this->FormulaireDonneesParent->FiltreIdProfilEnCours->ValeurParametre
						)
					) ;
					if(! $ok && $membership->Database->ConnectionException != "")
					{
						$this->RenseigneErreur($membership->Database->ConnectionException) ;
					}
					elseif(! $ok)
					{
						$this->RenseigneErreur($this->MessageErreurExecution) ;
					}
					else
					{
						$this->FormulaireDonneesParent->CacherFormulaireFiltres = 1 ;
						$this->MessageExecution = $this->MessageSuccesExecution ;
					}
				}
				else
				{
					parent::ExecuteInstructions() ;
				}
			}
		}
		
		class PvFormulaireRecouvreMPMS extends PvFormulaireDonneesHtml
		{
			public $InclureElementEnCours = 0 ;
			public $InclureTotalElements = 0 ;
			public $InscrireCommandeAnnuler = 0 ;
			public $FiltreLoginMembre = null ;
			public $FiltreEmailMembre = null ;
			public $NomClasseCommandeExecuter = "PvCommandeExecuterBase" ;
			public $CritereEmail = null ;
			public $ActCmdRecouvreMP = null ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$membership = & $this->ZoneParent->Membership ;
				$this->FiltreLoginMembre = $this->ScriptParent->CreeFiltreHttpPost("filtreLoginMembre") ;
				$this->FiltreLoginMembre->Libelle = ($membership->LoginWithEmail == 0) ? $membership->LoginMemberLabel : $membership->EmailMemberLabel ;
				$this->FiltreLoginMembre->DeclareComposant("PvZoneTexteHtml") ;
				$this->FiltresEdition[] = & $this->FiltreLoginMembre ;
				$nomFltsRecr = array("filtreLoginMembre") ;
				if($membership->LoginWithEmail == 0)
				{
					$this->FiltreEmailMembre = $this->ScriptParent->CreeFiltreHttpPost("filtreEmailMembre") ;
					$this->FiltreEmailMembre->Libelle = $membership->EmailMemberLabel ;
					$this->FiltreEmailMembre->DeclareComposant("PvZoneTexteHtml") ;
					$this->FiltresEdition[] = & $this->FiltreEmailMembre ;
					$this->CritereEmail = $this->CommandeExecuter->InsereCritere("PvCritereFormatEmail", array("filtreEmailMembre")) ;
					$nomFltsRecr[] = "filtreEmailMembre" ;
				}
				$this->ActCmdRecouvreMP = $this->CommandeExecuter->InsereActCmd("PvActCmdRecouvreMPMS", $nomFltsRecr) ;
				$this->InitDessinateurFiltresEdition() ;
				$this->DessinateurFiltresEdition->MaxFiltresParLigne = 1 ;
			}
		}
		class PvActCmdRecouvreMPMS extends PvActCmdBase
		{
			public function Execute()
			{
				$this->LieFiltresCibles() ;
				if($this->ScriptParent->ConfirmParUrl == 0)
				{
					$ok = $this->ScriptParent->ReinitMotPasse($this->FiltresCibles) ;
					if($ok)
					{
						if($this->ScriptParent->EnvoiParMail == 1)
						{
							$this->FormulaireDonneesParent->CommandeSelectionnee->MessageExecution = $this->ScriptParent->MessageSuccesEnvoiMail ;
						}
						else
						{
							$this->FormulaireDonneesParent->CommandeSelectionnee->MessageExecution = $this->ScriptParent->MessageSuccesAffiche.' '.$this->ScriptParent->MotPasseGenere ;
						}
					}
					else
					{
						$this->FormulaireDonneesParent->CommandeSelectionnee->MessageExecution = $this->ScriptParent->MessageErreur ;
					}
				}
				else
				{
					$ok = $this->ScriptParent->EnvoiMailDem($this->FiltresCibles) ;
					if($ok)
					{
						$this->FormulaireDonneesParent->CommandeSelectionnee->MessageExecution = $this->ScriptParent->MessageSuccesEnvoiMail ;
					}
					else
					{
						$this->FormulaireDonneesParent->CommandeSelectionnee->RenseigneErreur($this->ScriptParent->MessageErreurEnvoiMail) ;
					}
				}
			}
		}
		
		class PvBarreLiensMembre extends PvComposantIUBase
		{
			public $InclureLienAccueil = 0 ;
			public $LibelleLienAccueil = "Accueil" ;
			public $LibelleLienConnexion = "Connexion" ;
			public $LibelleLienRecouvreMP = "Mot de passe oubli&eacute;" ;
			public $LibelleLienDeconnexion = "D&eacute;connexion" ;
			public $LibelleModifMotPasse = "Changer mot de passe" ;
			public $SeparateurLiens = " | " ;
			public $FormatInfosMembre = '<b>${MEMBER_LOGIN}</b>, ' ;
			public $CacherSiNonConnecte = 0 ;
			protected function MembershipIndefini()
			{
				return $this->EstNul($this->ZoneParent) || $this->ZoneParent->EstNul($this->ZoneParent->Membership) ;
			}
			protected function RenduAutreLiensConnecte()
			{
				return "" ;
			}
			protected function RenduAutreLiensNonConnecte()
			{
				return "" ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				if($this->MembershipIndefini())
				{
					$ctn .= '(Membership indefini)' ;
					return $ctn ;
				}
				$ctn .= '<div class="barreLiensMembre">'.PHP_EOL ;
				if($this->ZoneParent->ValeurParamScriptAppele != $this->ZoneParent->NomScriptDeconnexion)
				{
					if($this->ZoneParent->PossedeMembreConnecte())
					{
						$ctn .= _parse_pattern($this->FormatInfosMembre, $this->ZoneParent->Membership->MemberLogged->RawData) ;
						if($this->InclureLienAccueil)
						{
							$ctn .= '<a href="'.$this->ZoneParent->ObtientUrl().'">'.$this->LibelleLienAccueil.'</a>' ;
							$ctn .= $this->SeparateurLiens ;
						}
						$ctn .= $this->RenduAutreLiensConnecte() ;
						$ctn .= '<a href="'.$this->ZoneParent->ScriptChangeMotPasse->ObtientUrl().'">'.$this->LibelleModifMotPasse.'</a>' ;
						$ctn .= $this->SeparateurLiens ;
						$ctn .= '<a href="'.$this->ZoneParent->ScriptDeconnexion->ObtientUrl().'">'.$this->LibelleLienDeconnexion.'</a>' ;
					}
					elseif($this->CacherSiNonConnecte == 0)
					{
						$ctn .= $this->RenduAutreLiensNonConnecte() ;
						$ctn .= '<a href="'.$this->ZoneParent->ScriptConnexion->ObtientUrl().'">'.$this->LibelleLienConnexion.'</a>' ;
						$ctn .= $this->SeparateurLiens ;
						$ctn .= '<a href="'.$this->ZoneParent->ScriptRecouvreMP->ObtientUrl().'">'.$this->LibelleLienRecouvreMP.'</a>' ;
					}
				}
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		class PvBarreLiensEditMembre extends PvBarreLiensMembre
		{
			public $LibelleLienListeMembres = "Membres" ;
			public $LibelleLienListeProfils = "Profils" ;
			public $LibelleLienListeRoles = "R&ocirc;les" ;
			public $LibelleLienAjoutMembre = "Inscription" ;
			public $LibelleLienAjoutProfil = "Cr&eacute;er profil" ;
			public $LibelleLienAjoutRole = "Cr&eacute;er r&ocirc;le" ;
			public $InclureLiensEdition = 1 ;
			public $InclureLiensAjout = 1 ;
			protected function RenduAutreLiensConnecte()
			{
				$ctn = parent::RenduAutreLiensConnecte() ;
				if($this->MembershipIndefini())
				{
					return $ctn ;
				}
				if($this->InclureLiensEdition && $this->ZoneParent->PossedeMembreConnecte() && $this->ZoneParent->ValeurParamScriptAppele != $this->ZoneParent->NomScriptDeconnexion)
				{
					if($this->ZoneParent->ScriptAccessible($this->ZoneParent->NomScriptListeMembres))
					{
						$ctn .= '<a href="'.$this->ZoneParent->ScriptListeMembres->ObtientUrl().'">'.$this->LibelleLienListeMembres.'</a>' ;
						$ctn .= $this->SeparateurLiens ;
					}
					if($this->InclureLiensAjout && $this->ZoneParent->ScriptAccessible($this->ZoneParent->NomScriptAjoutMembre))
					{
						$ctn .= '<a href="'.$this->ZoneParent->ScriptAjoutMembre->ObtientUrl().'">'.$this->LibelleLienAjoutMembre.'</a>' ;
						$ctn .= $this->SeparateurLiens ;
					}
					if($this->ZoneParent->ScriptAccessible($this->ZoneParent->NomScriptListeProfils))
					{
						$ctn .= '<a href="'.$this->ZoneParent->ScriptListeProfils->ObtientUrl().'">'.$this->LibelleLienListeProfils.'</a>' ;
						$ctn .= $this->SeparateurLiens ;
					}
					if($this->InclureLiensAjout && $this->ZoneParent->ScriptAccessible($this->ZoneParent->NomScriptAjoutProfil))
					{
						$ctn .= '<a href="'.$this->ZoneParent->ScriptAjoutProfil->ObtientUrl().'">'.$this->LibelleLienAjoutProfil.'</a>' ;
						$ctn .= $this->SeparateurLiens ;
					}
					if($this->ZoneParent->ScriptAccessible($this->ZoneParent->NomScriptListeRoles))
					{
						$ctn .= '<a href="'.$this->ZoneParent->ScriptListeRoles->ObtientUrl().'">'.$this->LibelleLienListeRoles.'</a>' ;
						$ctn .= $this->SeparateurLiens ;
					}
					if($this->InclureLiensAjout && $this->ZoneParent->ScriptAccessible($this->ZoneParent->NomScriptAjoutRole))
					{
						$ctn .= '<a href="'.$this->ZoneParent->ScriptAjoutRole->ObtientUrl().'">'.$this->LibelleLienAjoutRole.'</a>' ;
						$ctn .= $this->SeparateurLiens ;
					}
				}
				return $ctn ;
			}
		}
		
		class PvRemplisseurConfigMembershipSimple
		{
			public $IdProfilImporteMembreMS = 0 ;
			public $CheminDossierImporteMembreMS = "files" ;
			public $MessageImporteMembreMSSucces = '${TotalSucces}/${totalMembres} membres import??s avec succ??s.' ;
			public $MessageImporteMembreMSEchecs = 'Importation ??chou??e' ;
			public $LibFltFichierImporteMembreMS = "Fichier (TXT, CSV)" ;
			public $NomClasseLienModifTableauMembre = "PvConfigFormatteurColonneLien" ;
			public $NomClasseLienChangeMPTableauMembre = "PvConfigFormatteurColonneLien" ;
			public $NomClasseLienSupprTableauMembre = "PvConfigFormatteurColonneLien" ;
			public $NomClasseLienModifTableauRole = "PvConfigFormatteurColonneLien" ;
			public $NomClasseLienSupprTableauRole = "PvConfigFormatteurColonneLien" ;
			public $NomClasseLienModifTableauProfil = "PvConfigFormatteurColonneLien" ;
			public $NomClasseLienSupprTableauProfil = "PvConfigFormatteurColonneLien" ;
			public function NomsColImporteMembreMS(& $membership)
			{
				$cols = array(
					"login" => $membership->LoginMemberColumn,
					"mot_passe" => $membership->PasswordMemberColumn,
					"mot_de_passe" => $membership->PasswordMemberColumn,
					"mot de passe" => $membership->PasswordMemberColumn,
					"password" => $membership->PasswordMemberColumn,
					"compte" => $membership->LoginMemberColumn,
					"nom" => $membership->LastNameMemberColumn,
					"prenom" => $membership->FirstNameMemberColumn,
					"prenoms" => $membership->FirstNameMemberColumn,
					"actif" => $membership->EnableMemberColumn,
					"active" => $membership->EnableMemberColumn,
					"activer" => $membership->EnableMemberColumn,
					"enable" => $membership->EnableMemberColumn,
					"contact" => $membership->ContactMemberColumn,
					"adresse" => $membership->AddressMemberColumn,
					"email" => $membership->EmailMemberColumn,
					"profil" => $membership->ProfileMemberColumn,
					"profile" => $membership->ProfileMemberColumn,
					"role" => $membership->ProfileMemberColumn
				) ;
				if($membership->ADActivatedMemberColumn != '')
				{
					$cols["connexionad"] = $membership->ADActivatedMemberColumn ;
					$cols["activer ad"] = $membership->ADActivatedMemberColumn ;
					$cols["par windows"] = $membership->ADActivatedMemberColumn ;
					$cols["serveur ad"] = $membership->ADServerMemberColumn ;
					$cols["serveurad"] = $membership->ADServerMemberColumn ;
				}
				return $cols ;
			}
			public function RemplitFormulaireGlobalProfil(& $form)
			{
				$this->InitFormulaireProfil($form) ;
				$i = 0 ;
				$membership = $form->ZoneParent->Membership ;
				if($form->InclureElementEnCours)
				{
					$form->FiltresLigneSelection[$i] = $form->ScriptParent->CreeFiltreHttpGet("idProfil") ;
					$form->FiltresLigneSelection[$i]->Obligatoire = 1 ;
					$form->FiltresLigneSelection[$i]->ExpressionDonnees = $membership->Database->EscapeVariableName($membership->IdProfileColumn).' = <self>' ;
					$form->FiltreIdProfilEnCours = & $form->FiltresLigneSelection[$i] ;
				}
				$i = 0 ;
				$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreTitreProfil") ;
				$form->FiltresEdition[$i]->NomColonneLiee = $membership->TitleProfileColumn ;
				$form->FiltresEdition[$i]->NomParametreDonnees = $membership->TitleProfileColumn ;
				$form->FiltresEdition[$i]->DeclareComposant("PvZoneTexteHtml") ;
				$form->FiltresEdition[$i]->Libelle = $membership->TitleProfileLabel ;
				$form->FiltreTitreProfil = & $form->FiltresEdition[$i] ;
				$i++ ;
				$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreDescriptionProfil") ;
				$form->FiltresEdition[$i]->NomColonneLiee = $membership->DescriptionProfileColumn ;
				$form->FiltresEdition[$i]->NomParametreDonnees = $membership->DescriptionProfileColumn ;
				$form->FiltresEdition[$i]->DeclareComposant("PvZoneTexteHtml") ;
				$form->FiltresEdition[$i]->Libelle = $membership->DescriptionProfileLabel ;
				$form->FiltreDescriptionProfil = & $form->FiltresEdition[$i] ;
				$i++ ;
				$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreActiverProfil") ;
				$form->FiltresEdition[$i]->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$form->FiltresEdition[$i]->NomColonneLiee = $membership->EnableProfileColumn ;
				$form->FiltresEdition[$i]->NomParametreDonnees = $membership->EnableProfileColumn ;
				$form->FiltresEdition[$i]->Libelle = $membership->EnableProfileLabel ;
				$form->FiltresEdition[$i]->Composant->FournisseurDonnees = new PvFournisseurDonneesBool() ;
				$form->FiltresEdition[$i]->Composant->FournisseurDonnees->RequeteSelection = $form->FiltresEdition[$i]->Composant->FournisseurDonnees->NomCleBool ;
				$form->FiltresEdition[$i]->Composant->NomColonneValeur = $form->FiltresEdition[$i]->Composant->FournisseurDonnees->NomAttributValeur ;
				$form->FiltresEdition[$i]->Composant->NomColonneLibelle = $form->FiltresEdition[$i]->Composant->FournisseurDonnees->NomAttributLibelle ;
				$form->FiltresEdition[$i]->Composant->FournisseurDonnees->ValeurVrai = $membership->EnableProfileTrueValue ;
				if(! $form->InclureElementEnCours)
				{
					$form->FiltresEdition[$i]->ValeurParDefaut = $membership->EnableProfileTrueValue ;
				}
				$form->FiltreActiverProfil = & $form->FiltresEdition[$i] ;
				$i++ ;
				$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreListeRolesProfil") ;
				$form->FiltresEdition[$i]->NomColonneLiee = "" ;
				$form->FiltresEdition[$i]->NomParametreDonnees = "" ;
				$form->FiltresEdition[$i]->DeclareComposant("PvZoneBoiteOptionsCocherHtml") ;
				$form->FiltresEdition[$i]->Libelle = $membership->RoleListProfileLabel ;
				$form->FiltresEdition[$i]->Composant->FournisseurDonnees = new PvFournisseurDonneesSql() ;
				$form->FiltresEdition[$i]->Composant->FournisseurDonnees->BaseDonnees = & $membership->Database ;
				if(! $form->InclureElementEnCours)
				{
					$form->FiltresEdition[$i]->Composant->FournisseurDonnees->RequeteSelection = "(".$membership->SqlRolesForNewProfile().")" ;
				}
				else
				{
					$form->FiltresEdition[$i]->Composant->FournisseurDonnees->RequeteSelection = "(".$membership->SqlRolesForProfile().")" ;
					// echo $membership->SqlRolesForProfile() ;
					$filtreIdProfil = $form->ScriptParent->CreeFiltreHttpGet("idProfil") ;
					$filtreIdProfil->Obligatoire = 1 ;
					$filtreIdProfil->ExpressionDonnees = 'PROFILE_ID = <self>' ;
					$form->FiltresEdition[$i]->Composant->FiltresSelection[] = $filtreIdProfil ;
				}
				$form->FiltresEdition[$i]->Composant->NomColonneValeur = "ROLE_ID" ;
				$form->FiltresEdition[$i]->Composant->NomColonneLibelle = "ROLE_TITLE" ;
				$form->FiltresEdition[$i]->Composant->NomColonneValeurParDefaut = "PRIVILEGE_ENABLED" ;
				$form->FiltreListeRolesProfil = & $form->FiltresEdition[$i] ;
			}
			public function RemplitFormulaireGlobalRole(& $form)
			{
				$this->InitFormulaireRole($form) ;
				$i = 0 ;
				$membership = $form->ZoneParent->Membership ;
				if($form->InclureElementEnCours)
				{
					$form->FiltresLigneSelection[$i] = $form->ScriptParent->CreeFiltreHttpGet("idRole") ;
					$form->FiltresLigneSelection[$i]->Obligatoire = 1 ;
					$form->FiltresLigneSelection[$i]->ExpressionDonnees = $membership->Database->EscapeVariableName($membership->IdRoleColumn).' = <self>' ;
					$form->FiltreIdRoleEnCours = & $form->FiltresLigneSelection[$i] ;
				}
				$i = 0 ;
				$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreNomRole") ;
				$form->FiltresEdition[$i]->NomColonneLiee = $membership->NameRoleColumn ;
				$form->FiltresEdition[$i]->NomParametreDonnees = $membership->NameRoleColumn ;
				$form->FiltresEdition[$i]->DeclareComposant("PvZoneTexteHtml") ;
				$form->FiltresEdition[$i]->Libelle = $membership->NameRoleLabel ;
				$form->FiltreNomRole = & $form->FiltresEdition[$i] ;
				$i++ ;
				$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreTitreRole") ;
				$form->FiltresEdition[$i]->NomColonneLiee = $membership->TitleRoleColumn ;
				$form->FiltresEdition[$i]->NomParametreDonnees = $membership->TitleRoleColumn ;
				$form->FiltresEdition[$i]->DeclareComposant("PvZoneTexteHtml") ;
				$form->FiltresEdition[$i]->Libelle = $membership->TitleRoleLabel ;
				$form->FiltreTitreRole = & $form->FiltresEdition[$i] ;
				$i++ ;
				$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreDescriptionRole") ;
				$form->FiltresEdition[$i]->NomColonneLiee = $membership->DescriptionRoleColumn ;
				$form->FiltresEdition[$i]->NomParametreDonnees = $membership->DescriptionRoleColumn ;
				$form->FiltresEdition[$i]->DeclareComposant("PvZoneTexteHtml") ;
				$form->FiltresEdition[$i]->Libelle = $membership->DescriptionRoleLabel ;
				$form->FiltreDescriptionRole = & $form->FiltresEdition[$i] ;
				$i++ ;
				$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreActiverRole") ;
				$form->FiltresEdition[$i]->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$form->FiltresEdition[$i]->NomColonneLiee = $membership->EnableRoleColumn ;
				$form->FiltresEdition[$i]->NomParametreDonnees = $membership->EnableRoleColumn ;
				$form->FiltresEdition[$i]->Libelle = $membership->EnableRoleLabel ;
				$form->FiltresEdition[$i]->Composant->FournisseurDonnees = new PvFournisseurDonneesBool() ;
				$form->FiltresEdition[$i]->Composant->FournisseurDonnees->RequeteSelection = $form->FiltresEdition[$i]->Composant->FournisseurDonnees->NomCleBool ;
				$form->FiltresEdition[$i]->Composant->NomColonneValeur = $form->FiltresEdition[$i]->Composant->FournisseurDonnees->NomAttributValeur ;
				$form->FiltresEdition[$i]->Composant->NomColonneLibelle = $form->FiltresEdition[$i]->Composant->FournisseurDonnees->NomAttributLibelle ;
				$form->FiltresEdition[$i]->Composant->FournisseurDonnees->ValeurVrai = $membership->EnableRoleTrueValue ;
				if(! $form->InclureElementEnCours)
				{
					$form->FiltresEdition[$i]->ValeurParDefaut = $membership->EnableRoleTrueValue ;
				}
				$form->FiltreActiverRole = & $form->FiltresEdition[$i] ;
				$i++ ;
				$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreListeProfilsRole") ;
				$form->FiltresEdition[$i]->NomColonneLiee = "" ;
				$form->FiltresEdition[$i]->NomParametreDonnees = "" ;
				$form->FiltresEdition[$i]->DeclareComposant("PvZoneBoiteOptionsCocherHtml") ;
				$form->FiltresEdition[$i]->Libelle = $membership->ProfileListRoleLabel ;
				$form->FiltresEdition[$i]->Composant->FournisseurDonnees = new PvFournisseurDonneesSql() ;
				$form->FiltresEdition[$i]->Composant->FournisseurDonnees->BaseDonnees = & $membership->Database ;
				if(! $form->InclureElementEnCours)
				{
					$form->FiltresEdition[$i]->Composant->FournisseurDonnees->RequeteSelection = "(".$membership->SqlProfilesForNewRole().")" ;
				}
				else
				{
					$form->FiltresEdition[$i]->Composant->FournisseurDonnees->RequeteSelection = "(".$membership->SqlProfilesForRole().")" ;
					$filtreIdRole = $form->ScriptParent->CreeFiltreHttpGet("idRole") ;
					$filtreIdRole->Obligatoire = 1 ;
					$filtreIdRole->ExpressionDonnees = 'ROLE_ID = <self>' ;
					$form->FiltresEdition[$i]->Composant->FiltresSelection[] = $filtreIdRole ;
				}
				$form->FiltresEdition[$i]->Composant->NomColonneValeur = "PROFILE_ID" ;
				$form->FiltresEdition[$i]->Composant->NomColonneLibelle = "PROFILE_TITLE" ;
				$form->FiltresEdition[$i]->Composant->NomColonneValeurParDefaut = "PRIVILEGE_ENABLED" ;
				$form->FiltreListeProfilsRole = & $form->FiltresEdition[$i] ;
			}
			public function RemplitFiltresEditionFormMembre(& $form)
			{
				$membership = $form->ZoneParent->Membership ;
				$i = 0 ;
				$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreLoginMembre") ;
				$form->FiltresEdition[$i]->NomColonneLiee = $membership->LoginMemberColumn ;
				$form->FiltresEdition[$i]->NomParametreDonnees = $membership->LoginMemberColumn ;
				$form->FiltresEdition[$i]->DeclareComposant("PvZoneTexteHtml") ;
				$form->FiltresEdition[$i]->Libelle = ($membership->LoginWithEmail == 0) ? $membership->LoginMemberLabel : $membership->EmailMemberLabel ;
				$form->FiltreLoginMembre = & $form->FiltresEdition[$i] ;
				if(! $form->InclureElementEnCours)
				{
					$i++ ;
					$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreMotPasseMembre") ;
					$form->FiltresEdition[$i]->DeclareComposant("PvZoneMotPasseHtml") ;
					$form->FiltresEdition[$i]->NomColonneLiee = $membership->PasswordMemberColumn ;
					$form->FiltresEdition[$i]->NomParametreDonnees = $membership->PasswordMemberColumn ;
					$form->FiltresEdition[$i]->Libelle = $membership->PasswordMemberLabel ;
					if($membership->PasswordMemberExpr != "")
					{
						$form->FiltresEdition[$i]->ExpressionColonneLiee = $membership->PasswordMemberExpr.'('.$membership->Database->ExprParamPattern.')' ;
					} ;
					$form->FiltreMotPasseMembre = & $form->FiltresEdition[$i] ;
					if($membership->ConfirmSetPasswordEnabled == 1)
					{
						$i++ ;
						$form->FiltreConfirmMotPasseMembre = $form->ScriptParent->CreeFiltreHttpPost("filtreConfirmMotPasseMembre") ;
						$form->FiltreConfirmMotPasseMembre->Libelle = $membership->ConfirmPasswordMemberLabel ;
						$form->FiltreConfirmMotPasseMembre->DeclareComposant("PvZoneMotPasseHtml") ;
						$form->FiltresEdition[$i] = & $form->FiltreConfirmMotPasseMembre ;
					}
					if($membership->MustChangePasswordMemberColumn != '')
					{
						$i++ ;
						$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreFixe("doitChangerMotPasse", $membership->MustChangePasswordMemberTrueValue) ;
						$form->FiltresEdition[$i]->NomColonneLiee = $membership->MustChangePasswordMemberColumn ;
					}
				}
				if($membership->FirstNameMemberColumn != "")
				{
					$i++ ;
					$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreNomMembre") ;
					$form->FiltresEdition[$i]->DeclareComposant("PvZoneTexteHtml") ;
					$form->FiltresEdition[$i]->NomColonneLiee = $membership->FirstNameMemberColumn ;
					$form->FiltresEdition[$i]->NomParametreDonnees = $membership->FirstNameMemberColumn ;
					$form->FiltresEdition[$i]->Libelle = $membership->FirstNameMemberLabel ;
					$form->FiltreNomMembre = & $form->FiltresEdition[$i] ;
				}
				if($membership->LastNameMemberColumn != "")
				{
					$i++ ;
					$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtrePrenomMembre") ;
					$form->FiltresEdition[$i]->DeclareComposant("PvZoneTexteHtml") ;
					$form->FiltresEdition[$i]->NomColonneLiee = $membership->LastNameMemberColumn ;
					$form->FiltresEdition[$i]->NomParametreDonnees = $membership->LastNameMemberColumn ;
					$form->FiltresEdition[$i]->Libelle = $membership->LastNameMemberLabel ;
					$form->FiltrePrenomMembre = & $form->FiltresEdition[$i] ;
				}
				if($membership->LoginWithEmail == 0)
				{
					if($membership->EmailMemberColumn != "")
					{
						$i++ ;
						$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreEmailMembre") ;
						$form->FiltresEdition[$i]->DeclareComposant("PvZoneTexteHtml") ;
						$form->FiltresEdition[$i]->NomColonneLiee = $membership->EmailMemberColumn ;
						$form->FiltresEdition[$i]->NomParametreDonnees = $membership->EmailMemberColumn ;
						$form->FiltresEdition[$i]->Libelle = $membership->EmailMemberLabel ;
						$form->FiltreEmailMembre = & $form->FiltresEdition[$i] ;
					}
				}
				if($membership->AddressMemberColumn != "")
				{
					$i++ ;
					$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreAdresseMembre") ;
					$form->FiltresEdition[$i]->DeclareComposant("PvZoneTexteHtml") ;
					$form->FiltresEdition[$i]->NomColonneLiee = $membership->AddressMemberColumn ;
					$form->FiltresEdition[$i]->NomParametreDonnees = $membership->AddressMemberColumn ;
					$form->FiltresEdition[$i]->Libelle = $membership->AddressMemberLabel ;
					$form->FiltreAdresseMembre = & $form->FiltresEdition[$i] ;
				}
				if($membership->ContactMemberColumn != "")
				{
					$i++ ;
					$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreContactMembre") ;
					$form->FiltresEdition[$i]->DeclareComposant("PvZoneTexteHtml") ;
					$form->FiltresEdition[$i]->NomColonneLiee = $membership->ContactMemberColumn ;
					$form->FiltresEdition[$i]->NomParametreDonnees = $membership->ContactMemberColumn ;
					$form->FiltresEdition[$i]->Libelle = $membership->ContactMemberLabel ;
					$form->FiltreContactMembre = & $form->FiltresEdition[$i] ;
				}
				if($membership->ADActivatedMemberColumn != "")
				{
					$i++ ;
					$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreAuthentifierADMembre") ;
					$form->FiltresEdition[$i]->DeclareComposant("PvZoneSelectBoolHtml") ;
					$form->FiltresEdition[$i]->NomColonneLiee = $membership->ADActivatedMemberColumn ;
					$form->FiltresEdition[$i]->NomParametreDonnees = $membership->ADActivatedMemberColumn ;
					$form->FiltresEdition[$i]->Libelle = $membership->ADActivatedMemberLabel ;
					$form->FiltresEdition[$i]->ValeurParDefaut = $membership->ADActivatedMemberTrueValue ;
					$form->FiltreAuthentifierADMembre = & $form->FiltresEdition[$i] ;
				}
				if($membership->EnableMemberColumn != "")
				{
					$i++ ;
					$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreActiverMembre") ;
					$form->FiltresEdition[$i]->DeclareComposant("PvZoneBoiteSelectHtml") ;
					$form->FiltresEdition[$i]->NomColonneLiee = $membership->EnableMemberColumn ;
					$form->FiltresEdition[$i]->NomParametreDonnees = $membership->EnableMemberColumn ;
					$form->FiltresEdition[$i]->Libelle = $membership->EnableMemberLabel ;
					$form->FiltresEdition[$i]->Composant->FournisseurDonnees = new PvFournisseurDonneesBool() ;
					$form->FiltresEdition[$i]->Composant->FournisseurDonnees->RequeteSelection = $form->FiltresEdition[$i]->Composant->FournisseurDonnees->NomCleBool ;
					$form->FiltresEdition[$i]->Composant->NomColonneValeur = $form->FiltresEdition[$i]->Composant->FournisseurDonnees->NomAttributValeur ;
					$form->FiltresEdition[$i]->Composant->NomColonneLibelle = $form->FiltresEdition[$i]->Composant->FournisseurDonnees->NomAttributLibelle ;
					$form->FiltresEdition[$i]->Composant->FournisseurDonnees->ValeurVrai = $membership->EnableMemberTrueValue ;
					$form->FiltresEdition[$i]->ValeurParDefaut = $membership->EnableMemberTrueValue ;
					$form->FiltreActiverMembre = & $form->FiltresEdition[$i] ;
				}
				if($membership->ADServerMemberColumn != "")
				{
					$i++ ;
					$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreADServerMembre") ;
					$form->FiltresEdition[$i]->DeclareComposant("PvZoneBoiteSelectHtml") ;
					$form->FiltresEdition[$i]->NomColonneLiee = $membership->ADServerMemberColumn ;
					$form->FiltresEdition[$i]->NomParametreDonnees = $membership->ADServerMemberColumn ;
					$form->FiltresEdition[$i]->Libelle = $membership->ADServerMemberLabel ;
					$form->FiltresEdition[$i]->Composant->FournisseurDonnees = new PvFournisseurDonneesSql() ;
					$form->FiltresEdition[$i]->Composant->FournisseurDonnees->BaseDonnees = $membership->Database ;
					$form->FiltresEdition[$i]->Composant->FournisseurDonnees->RequeteSelection = $membership->Database->EscapeTableName($membership->ADServerTable) ;
					$form->FiltresEdition[$i]->Composant->NomColonneValeur = $membership->IdADServerColumn ;
					$form->FiltresEdition[$i]->Composant->NomColonneLibelle = $membership->HostADServerColumn ;
					$form->FiltreServeurADMembre = & $form->FiltresEdition[$i] ;
				}
				$i++ ;
				$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreProfilMembre") ;
				$form->FiltresEdition[$i]->NomColonneLiee = $membership->ProfileMemberColumn ;
				$form->FiltresEdition[$i]->NomParametreDonnees = $membership->ProfileMemberColumn ;
				$form->FiltresEdition[$i]->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$form->FiltresEdition[$i]->Libelle = $membership->ProfileMemberLabel ;
				$form->FiltresEdition[$i]->Composant->FournisseurDonnees = new PvFournisseurDonneesSql() ;
				$form->FiltresEdition[$i]->Composant->FournisseurDonnees->BaseDonnees = & $membership->Database ;
				$form->FiltresEdition[$i]->Composant->FournisseurDonnees->RequeteSelection = $membership->Database->EscapeTableName($membership->ProfileTable) ;
				$form->FiltresEdition[$i]->Composant->NomColonneValeur = $membership->ProfileMemberForeignKey ;
				$form->FiltresEdition[$i]->Composant->NomColonneLibelle = $membership->TitleProfileColumn ;
				$form->FiltreProfilMembre = & $form->FiltresEdition[$i] ;
			}
			public function InitFormulaireRole(& $form)
			{
				$membership = $form->ZoneParent->Membership ;
				$bd = & $membership->Database ;
				$form->FournisseurDonnees->BaseDonnees = $membership->Database ;
				$form->FournisseurDonnees->RequeteSelection = $bd->EscapeTableName($membership->RoleTable) ;
				$form->FournisseurDonnees->TableEdition = $membership->RoleTable ;
				$form->RedirigeAnnulerVersScript($form->ZoneParent->NomScriptListeRoles) ;
			}
			public function InitFormulaireProfil(& $form)
			{
				$membership = $form->ZoneParent->Membership ;
				$bd = & $membership->Database ;
				$form->FournisseurDonnees->BaseDonnees = $membership->Database ;
				$form->FournisseurDonnees->RequeteSelection = $bd->EscapeTableName($membership->ProfileTable) ;
				$form->FournisseurDonnees->TableEdition = $membership->ProfileTable ;
				$form->RedirigeAnnulerVersScript($form->ZoneParent->NomScriptListeProfils) ;
			}
			public function InitFormulaireMembre(& $form)
			{
				$membership = $form->ZoneParent->Membership ;
				$bd = & $membership->Database ;
				$form->FournisseurDonnees->BaseDonnees = $membership->Database ;
				$form->FournisseurDonnees->RequeteSelection = $bd->EscapeTableName($membership->MemberTable) ;
				$form->FournisseurDonnees->TableEdition = $membership->MemberTable ;
				if($form->ZoneParent->EditMembershipPossible())
				{
					$form->RedirigeAnnulerVersScript($form->ZoneParent->NomScriptListeMembres) ;
				}
				else
				{
					$form->RedirigeAnnulerVersScript($form->ZoneParent->NomScriptParDefaut) ;
				}
			}
			public function RemplitFiltresMPFormMembre(& $form)
			{
				$membership = $form->ZoneParent->Membership ;
				$i = 0 ;
				// print $form->FiltreIdMembreEnCours->Lie() ;
				if(! $form->UtiliserFiltresGlobaux)
				{
					$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreAncMotPasseMembre") ;
					$form->FiltresEdition[$i]->NomColonneLiee = $membership->PasswordMemberColumn ;
					$form->FiltresEdition[$i]->NomParametreDonnees = "" ;
					$form->FiltresEdition[$i]->DeclareComposant("PvZoneMotPasseHtml") ;
					$form->FiltresEdition[$i]->Libelle = $membership->OldPasswordMemberLabel ;
					$form->FiltresEdition[$i]->Obligatoire = 1 ;
					$form->FiltreAncMotPasseMembre = & $form->FiltresEdition[$i] ;
					$i++ ;
				}
				if(! $form->UtiliserFiltresGlobaux)
				{
					$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreMembreConnecte('idMembre', 'MEMBER_LOGIN') ;
				}
				else
				{
					$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreLoginMembre") ;
					$form->FiltresEdition[$i]->NomParametreDonnees = $membership->LoginMemberColumn ;
				}
				$form->FiltresEdition[$i]->NomColonneLiee = $membership->LoginMemberColumn ;
				$form->FiltresEdition[$i]->NePasLierColonne = 1 ;
				$form->FiltresEdition[$i]->DeclareComposant("PvZoneEtiquetteHtml") ;
				$form->FiltresEdition[$i]->Libelle = $membership->LoginMemberLabel ;
				$form->FiltreLoginMembre = & $form->FiltresEdition[$i] ;
				$i++ ;
				$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreNouvMotPasseMembre") ;
				$form->FiltresEdition[$i]->NomColonneLiee = "" ;
				$form->FiltresEdition[$i]->NomParametreDonnees = "" ;
				$form->FiltresEdition[$i]->DeclareComposant("PvZoneMotPasseHtml") ;
				$form->FiltresEdition[$i]->Libelle = $membership->NewPasswordMemberLabel ;
				$form->FiltresEdition[$i]->Obligatoire = 1 ;
				$form->FiltreNouvMotPasseMembre = & $form->FiltresEdition[$i] ;
				$i++ ;
				$form->FiltresEdition[$i] = $form->ScriptParent->CreeFiltreHttpPost("filtreConfirmMotPasseMembre") ;
				$form->FiltresEdition[$i]->NomColonneLiee = "" ;
				$form->FiltresEdition[$i]->NomParametreDonnees = "" ;
				$form->FiltresEdition[$i]->DeclareComposant("PvZoneMotPasseHtml") ;
				$form->FiltresEdition[$i]->Libelle = $membership->ConfirmPasswordMemberLabel ;
				$form->FiltresEdition[$i]->Obligatoire = 1 ;
				$form->FiltreConfirmMotPasseMembre = & $form->FiltresEdition[$i] ;
			}
			public function RemplitFormulaireGlobalMembre(& $form)
			{
				$this->InitFormulaireMembre($form) ;
				$i = 0 ;
				$membership = $form->ZoneParent->Membership ;
				if($form->InclureElementEnCours)
				{
					$form->FiltresLigneSelection[$i] = $form->ScriptParent->CreeFiltreHttpGet("idMembre") ;
					$form->FiltresLigneSelection[$i]->Obligatoire = 1 ;
					$form->FiltresLigneSelection[$i]->ExpressionDonnees = $membership->Database->EscapeVariableName($membership->IdMemberColumn).' = <self>' ;
					$form->FiltreIdMembreEnCours = & $form->FiltresLigneSelection[$i] ;
				}
				$this->RemplitFiltresEditionFormMembre($form) ;
				// print get_class($form->CommandeAnnuler) ;
			}
			public function RemplitFormulaireInfosMembre(& $form)
			{
				$membership = $form->ZoneParent->Membership ;
				$this->InitFormulaireMembre($form) ;
				$i = 0 ;
				$form->FiltresLigneSelection[$i] = $form->ScriptParent->CreeFiltreMembreConnecte('idMembre', 'MEMBER_ID') ;
				$form->FiltresLigneSelection[$i]->ExpressionDonnees = $membership->Database->EscapeVariableName($membership->IdMemberColumn).' = <self>' ;
				$form->FiltreIdMembreEnCours = & $form->FiltresLigneSelection[$i] ;
				$this->RemplitFiltresEditionFormMembre($form) ;
				$form->FiltreActiverMembre->Invisible = 1 ;
				$form->FiltreProfilMembre->EstEtiquette = 1 ;
			}
			public function RemplitFormulaireChangeMPMembre(& $form)
			{
				$membership = $form->ZoneParent->Membership ;
				$this->InitFormulaireMembre($form) ;
				if($form->InclureElementEnCours)
				{
					$i = 0 ;
					if(! $form->UtiliserFiltresGlobaux)
					{
						$form->FiltresLigneSelection[$i] = $form->ScriptParent->CreeFiltreMembreConnecte('idMembre', 'MEMBER_ID') ;
					}
					else
					{
						$form->FiltresLigneSelection[$i] = $form->ScriptParent->CreeFiltreHttpGet('idMembre') ;
					}
					$form->FiltresLigneSelection[$i]->ExpressionDonnees = $membership->Database->EscapeVariableName($membership->IdMemberColumn).' = <self>' ;
					$form->FiltreIdMembreEnCours = & $form->FiltresLigneSelection[$i] ;
				}
				$this->RemplitFiltresMPFormMembre($form) ;
			}
			public function InitTableauMembre(& $table)
			{
				$membership = $table->ZoneParent->Membership ;
				$table->FournisseurDonnees->BaseDonnees = $membership->Database ;
				$table->FournisseurDonnees->RequeteSelection = '('.$membership->SqlAllMembers().')' ;
			}
			public function InitTableauProfil(& $table)
			{
				$membership = $table->ZoneParent->Membership ;
				$table->FournisseurDonnees->BaseDonnees = $membership->Database ;
				$table->FournisseurDonnees->RequeteSelection = '('.$membership->SqlAllProfiles().')' ;
				// echo '('.$membership->SqlAllProfiles().')' ;
			}
			public function InitTableauRole(& $table)
			{
				$membership = $table->ZoneParent->Membership ;
				$table->FournisseurDonnees->BaseDonnees = $membership->Database ;
				$table->FournisseurDonnees->RequeteSelection = '('.$membership->SqlAllRoles().')' ;
			}
			public function RemplitFiltresTableauMembre(& $table)
			{
				$membership = $table->ZoneParent->Membership ;
				$i = 0 ;
				$table->FiltresSelection[$i] = $table->ScriptParent->CreeFiltreHttpGet("loginMembre") ;
				$table->FiltresSelection[$i]->Libelle = $membership->LoginMemberLabel ;
				$table->FiltresSelection[$i]->DeclareComposant("PvZoneTexteHtml") ;
				$table->FiltresSelection[$i]->ExpressionDonnees = "upper(MEMBER_LOGIN) LIKE upper(".$membership->Database->SqlConcat(array("'%'", '<SELF>', "'%'")).")" ;
				$i++ ;
				$table->FiltresSelection[$i] = $table->ScriptParent->CreeFiltreHttpGet("nomMembre") ;
				$table->FiltresSelection[$i]->Libelle = $membership->FirstNameMemberLabel ;
				$table->FiltresSelection[$i]->DeclareComposant("PvZoneTexteHtml") ;
				$table->FiltresSelection[$i]->ExpressionDonnees = "upper(MEMBER_FIRST_NAME ) LIKE upper(".$membership->Database->SqlConcat(array("'%'", '<SELF>', "'%'")).") OR upper(MEMBER_LAST_NAME ) LIKE upper(".$membership->Database->SqlConcat(array("'%'", '<SELF>', "'%'")).")" ;
				$i++ ;
				$table->FiltresSelection[$i] = $table->ScriptParent->CreeFiltreHttpGet("profilMembre") ;
				$table->FiltresSelection[$i]->NomColonneLiee = $membership->ProfileMemberColumn ;
				$table->FiltresSelection[$i]->NomParametreDonnees = $membership->ProfileMemberColumn ;
				$table->FiltresSelection[$i]->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$table->FiltresSelection[$i]->Libelle = $membership->ProfileMemberLabel ;
				$table->FiltresSelection[$i]->Composant->FournisseurDonnees = new PvFournisseurDonneesSql() ;
				$table->FiltresSelection[$i]->Composant->FournisseurDonnees->BaseDonnees = & $membership->Database ;
				$table->FiltresSelection[$i]->Composant->FournisseurDonnees->RequeteSelection = $membership->Database->EscapeTableName($membership->ProfileTable) ;
				$table->FiltresSelection[$i]->Composant->NomColonneValeur = $membership->ProfileMemberForeignKey ;
				$table->FiltresSelection[$i]->Composant->NomColonneLibelle = $membership->TitleProfileColumn ;
				$table->FiltresSelection[$i]->ExpressionDonnees = "MEMBER_PROFILE = <SELF>" ;
				$table->FiltresSelection[$i]->Composant->InclureElementHorsLigne = 1 ;
				$table->FiltresSelection[$i]->Composant->ValeurElementHorsLigne = "" ;
				$table->FiltresSelection[$i]->Composant->LibelleElementHorsLigne = "-- Tous --" ;
				$table->FiltreProfilMembre = & $table->FiltresSelection[$i] ;
				$i++ ;
				$table->FiltresSelection[$i] = $table->ScriptParent->CreeFiltreHttpGet("activeMembre") ;
				$table->FiltresSelection[$i]->DeclareComposant("PvZoneSelectBoolHtml") ;
				$table->FiltresSelection[$i]->ExpressionDonnees = "MEMBER_ENABLE = <SELF>" ;
				$table->FiltresSelection[$i]->Libelle = $membership->EnableMemberLabel ;
				$table->FiltresSelection[$i]->Composant->InclureElementHorsLigne = 1 ;
				$table->FiltresSelection[$i]->Composant->ValeurElementHorsLigne = "" ;
				$table->FiltresSelection[$i]->Composant->LibelleElementHorsLigne = "-- Tous --" ;
				$table->FiltreActiveMembre = & $table->FiltresSelection[$i] ;
			}
			public function RemplitDefinitionsColonneTableauMembre(& $table)
			{
				$membership = $table->ZoneParent->Membership ;
				$i = 0 ;
				$table->DefinitionsColonnes[$i] = new PvDefinitionColonneDonnees() ;
				$table->DefinitionsColonnes[$i]->NomDonnees = 'MEMBER_ID' ;
				$table->DefinitionsColonnes[$i]->Visible = 0 ;
				$table->DefinitionColonneLogin = & $table->DefinitionsColonnes[$i] ;
				
				$i++ ;
				
				$table->DefinitionsColonnes[$i] = new PvDefinitionColonneDonnees() ;
				$table->DefinitionsColonnes[$i]->NomDonnees = 'MEMBER_LOGIN' ;
				$table->DefinitionsColonnes[$i]->Libelle = $membership->LoginMemberLabel ;
				$table->DefinitionColonneLogin = & $table->DefinitionsColonnes[$i] ;
				
				$i++ ;
				$table->DefinitionsColonnes[$i] = new PvDefinitionColonneDonnees() ;
				$table->DefinitionsColonnes[$i]->NomDonnees = 'MEMBER_FIRST_NAME' ;
				$table->DefinitionsColonnes[$i]->Libelle = $membership->FirstNameMemberLabel ;
				$table->DefinitionColonneNom = & $table->DefinitionsColonnes[$i] ;
				
				$i++ ;
				$table->DefinitionsColonnes[$i] = new PvDefinitionColonneDonnees() ;
				$table->DefinitionsColonnes[$i]->NomDonnees = 'MEMBER_LAST_NAME' ;
				$table->DefinitionsColonnes[$i]->Libelle = $membership->LastNameMemberLabel ;
				$table->DefinitionColonnePrenom = & $table->DefinitionsColonnes[$i] ;
				
				if($membership->LoginWithEmail == 0)
				{
					$i++ ;
					$table->DefinitionsColonnes[$i] = new PvDefinitionColonneDonnees() ;
					$table->DefinitionsColonnes[$i]->NomDonnees = 'MEMBER_EMAIL' ;
					$table->DefinitionsColonnes[$i]->Libelle = $membership->EmailMemberLabel ;
					$table->DefinitionColonneEmail = & $table->DefinitionsColonnes[$i] ;
				}
				
				$table->DefinitionColonneProfil = $table->InsereDefCol("PROFILE_TITLE", $membership->ProfileMemberLabel) ;
				$table->DefinitionColonneEnable = $table->InsereDefColBool("MEMBER_ENABLE", $membership->EnableMemberLabel) ;
				if($membership->ADActivatedMemberColumn != "")
				{
					$table->InsereDefColCachee("MEMBER_AD_ACTIVATED") ;
				}
			}
			public function RemplitDefinitionColActionsTableauMembre(& $table)
			{
				$membership = $table->ZoneParent->Membership ;
				$i = count($table->DefinitionsColonnes) ;
				
				$table->DefinitionsColonnes[$i] = new PvDefinitionColonneDonnees() ;
				$table->DefinitionsColonnes[$i]->Libelle = "Actions" ;
				$table->DefinitionsColonnes[$i]->AlignElement = "center" ;
				$table->DefinitionsColonnes[$i]->TriPossible = 0 ;
				$table->DefinitionsColonnes[$i]->Formatteur = new PvFormatteurColonneLiens() ;
				
				$nomClasse = $this->NomClasseLienModifTableauMembre ;
				if(class_exists($nomClasse))
				{
					$lienModif = new $nomClasse() ;
					$lienModif->FormatIdOnglet = 'modif_membre_${MEMBER_ID}' ;
					$lienModif->FormatTitreOnglet = 'Modifier le membre ${MEMBER_LOGIN}' ;
					$lienModif->FormatLibelle = "Modifier" ;
					if($table->ZoneParent->ActiverRoutes == 0)
					{
						$lienModif->FormatURL = "?".urlencode($table->ZoneParent->NomParamScriptAppele)."=".urlencode($table->ZoneParent->NomScriptModifMembre).'&idMembre=${MEMBER_ID}' ;
					}
					else
					{
						$lienModif->FormatURL = '?idMembre=${MEMBER_ID}' ;
					}
					$table->DefinitionsColonnes[$i]->Formatteur->Liens[] = $lienModif ;
				}
				
				$nomClasse = $this->NomClasseLienChangeMPTableauMembre ;
				if(class_exists($nomClasse))
				{
					$lienModif = new $nomClasse() ;
					$lienModif->FormatIdOnglet = 'change_mp_membre_${MEMBER_ID}' ;
					$lienModif->FormatTitreOnglet = 'Changer mot de passe de ${MEMBER_LOGIN}' ;
					$lienModif->FormatLibelle = "Changer mot de passe" ;
					if($membership->ADActivatedMemberColumn != '')
					{
						$lienModif->NomDonneesValid = "MEMBER_AD_ACTIVATED" ;
						$lienModif->ValeurVraiValid = 0 ;
					}
					if($table->ZoneParent->ActiverRoutes == 0)
					{
						$lienModif->FormatURL = "?".urlencode($table->ZoneParent->NomParamScriptAppele)."=".urlencode($table->ZoneParent->NomScriptChangeMPMembre).'&idMembre=${MEMBER_ID}' ;
					}
					else
					{
						$lienModif->FormatURL = '?idMembre=${MEMBER_ID}' ;
					}
					$table->DefinitionsColonnes[$i]->Formatteur->Liens[] = $lienModif ;
				}
				
				$nomClasse = $this->NomClasseLienSupprTableauMembre ;
				if(class_exists($nomClasse))
				{
					$lienSuppr = new $nomClasse() ;
					$lienSuppr->FormatIdOnglet = 'suppr_membre_${MEMBER_ID}' ;
					$lienSuppr->FormatTitreOnglet = 'Supprimer le membre ${MEMBER_LOGIN}' ;
					$lienSuppr->FormatLibelle = "Supprimer" ;
					if($table->ZoneParent->ActiverRoutes == 0)
					{
						$lienSuppr->FormatURL = "?".urlencode($table->ZoneParent->NomParamScriptAppele)."=".urlencode($table->ZoneParent->NomScriptSupprMembre).'&idMembre=${MEMBER_ID}' ;
					}
					else
					{
						$lienSuppr->FormatURL = '?idMembre=${MEMBER_ID}' ;
					}
					$table->DefinitionsColonnes[$i]->Formatteur->Liens[] = $lienSuppr ;
				}
			}
			public function RemplitFiltresTableauRole(& $table)
			{
				$membership = $table->ZoneParent->Membership ;
				$i = 0 ;
				$table->FiltresSelection[$i] = $table->ScriptParent->CreeFiltreHttpGet("nomRole") ;
				$table->FiltresSelection[$i]->Libelle = $membership->NameRoleLabel ;
				$table->FiltresSelection[$i]->DeclareComposant("PvZoneTexteHtml") ;
				$table->FiltresSelection[$i]->ExpressionDonnees = "upper(ROLE_NAME) LIKE upper(".$membership->Database->SqlConcat(array("'%'", '<SELF>', "'%'")).")" ;
				$i++ ;
				$table->FiltresSelection[$i] = $table->ScriptParent->CreeFiltreHttpGet("titreRole") ;
				$table->FiltresSelection[$i]->Libelle = $membership->TitleRoleLabel ;
				$table->FiltresSelection[$i]->DeclareComposant("PvZoneTexteHtml") ;
				$table->FiltresSelection[$i]->ExpressionDonnees = "upper(ROLE_TITLE ) LIKE upper(".$membership->Database->SqlConcat(array("'%'", '<SELF>', "'%'")).")" ;
				$i++ ;
			}
			public function RemplitDefinitionsColonneTableauRole(& $table)
			{
				$membership = $table->ZoneParent->Membership ;
				$i = 0 ;
				$table->DefinitionsColonnes[$i] = new PvDefinitionColonneDonnees() ;
				$table->DefinitionsColonnes[$i]->NomDonnees = 'ROLE_ID' ;
				$table->DefinitionsColonnes[$i]->Visible = 0 ;
				$table->DefinitionColonneLogin = & $table->DefinitionsColonnes[$i] ;
				
				$i++ ;
				
				$table->DefinitionsColonnes[$i] = new PvDefinitionColonneDonnees() ;
				$table->DefinitionsColonnes[$i]->NomDonnees = 'ROLE_NAME' ;
				$table->DefinitionsColonnes[$i]->Libelle = $membership->NameRoleLabel ;
				$table->DefinitionColonneLogin = & $table->DefinitionsColonnes[$i] ;
				
				$i++ ;
				$table->DefinitionsColonnes[$i] = new PvDefinitionColonneDonnees() ;
				$table->DefinitionsColonnes[$i]->NomDonnees = 'ROLE_TITLE' ;
				$table->DefinitionsColonnes[$i]->Libelle = $membership->TitleRoleLabel ;
				$table->DefinitionColonneNom = & $table->DefinitionsColonnes[$i] ;
			}
			public function RemplitDefinitionColActionsTableauRole(& $table)
			{
				$membership = $table->ZoneParent->Membership ;
				$i = count($table->DefinitionsColonnes) ;
				
				$table->DefinitionsColonnes[$i] = new PvDefinitionColonneDonnees() ;
				$table->DefinitionsColonnes[$i]->Libelle = "Actions" ;
				$table->DefinitionsColonnes[$i]->AlignElement = "center" ;
				$table->DefinitionsColonnes[$i]->TriPossible = 0 ;
				$table->DefinitionsColonnes[$i]->Formatteur = new PvFormatteurColonneLiens() ;
				
				$nomClasse = $this->NomClasseLienModifTableauRole ;
				if(class_exists($nomClasse))
				{
					$lienModif = new $nomClasse() ;
					$lienModif->FormatIdOnglet = 'modif_role_${ROLE_ID}' ;
					$lienModif->FormatTitreOnglet = 'Modifier le role ${ROLE_NAME}' ;
					$lienModif->FormatLibelle = "Modifier" ;
					if($table->ZoneParent->ActiverRoutes == 0)
					{
						$lienModif->FormatURL = "?".urlencode($table->ZoneParent->NomParamScriptAppele)."=".urlencode($table->ZoneParent->NomScriptModifRole).'&idRole=${ROLE_ID}' ;
					}
					else
					{
						$lienModif->FormatURL = '?idRole=${ROLE_ID}' ;
					}
					$table->DefinitionsColonnes[$i]->Formatteur->Liens[] = $lienModif ;
				}
				
				$nomClasse = $this->NomClasseLienSupprTableauRole ;
				if(class_exists($nomClasse))
				{
					$lienSuppr = new $nomClasse() ;
					$lienSuppr->FormatIdOnglet = 'suppr_role_${ROLE_ID}' ;
					$lienSuppr->FormatTitreOnglet = 'Supprimer le role ${ROLE_NAME}' ;
					$lienSuppr->FormatLibelle = "Supprimer" ;
					if($table->ZoneParent->ActiverRoutes == 0)
					{
						$lienSuppr->FormatURL = "?".urlencode($table->ZoneParent->NomParamScriptAppele)."=".urlencode($table->ZoneParent->NomScriptSupprRole).'&idRole=${ROLE_ID}' ;
					}
					else
					{
						$lienSuppr->FormatURL = '?idRole=${ROLE_ID}' ;
					}
					$table->DefinitionsColonnes[$i]->Formatteur->Liens[] = $lienSuppr ;
				}
			}
			public function RemplitFiltresTableauProfil(& $table)
			{
				$membership = $table->ZoneParent->Membership ;
				$i = 0 ;
				$table->FiltresSelection[$i] = $table->ScriptParent->CreeFiltreHttpGet("titreProfil") ;
				$table->FiltresSelection[$i]->Libelle = $membership->TitleProfileLabel ;
				$table->FiltresSelection[$i]->DeclareComposant("PvZoneTexteHtml") ;
				$table->FiltresSelection[$i]->ExpressionDonnees = "upper(PROFILE_TITLE) LIKE upper(".$membership->Database->SqlConcat(array("'%'", '<SELF>', "'%'")).")" ;
				$i++ ;
			}
			public function RemplitDefinitionsColonneTableauProfil(& $table)
			{
				$membership = $table->ZoneParent->Membership ;
				$i = 0 ;
				$table->DefinitionsColonnes[$i] = new PvDefinitionColonneDonnees() ;
				$table->DefinitionsColonnes[$i]->NomDonnees = 'PROFILE_ID' ;
				$table->DefinitionsColonnes[$i]->Visible = 0 ;
				$table->DefinitionColonneId = & $table->DefinitionsColonnes[$i] ;
				
				$i++ ;
				$table->DefinitionsColonnes[$i] = new PvDefinitionColonneDonnees() ;
				$table->DefinitionsColonnes[$i]->NomDonnees = 'PROFILE_TITLE' ;
				$table->DefinitionsColonnes[$i]->Libelle = $membership->TitleProfileLabel ;
				$table->DefinitionColonneTitre = & $table->DefinitionsColonnes[$i] ;
			}
			public function RemplitDefinitionColActionsTableauProfil(& $table)
			{
				$membership = $table->ZoneParent->Membership ;
				$i = count($table->DefinitionsColonnes) ;
				
				$table->DefinitionsColonnes[$i] = new PvDefinitionColonneDonnees() ;
				$table->DefinitionsColonnes[$i]->Libelle = "Actions" ;
				$table->DefinitionsColonnes[$i]->AlignElement = "center" ;
				$table->DefinitionsColonnes[$i]->TriPossible = 0 ;
				$table->DefinitionsColonnes[$i]->Formatteur = new PvFormatteurColonneLiens() ;
				
				$nomClasse = $this->NomClasseLienModifTableauProfil ;
				if(class_exists($nomClasse))
				{
					$lienModif = new $nomClasse() ;
					$lienModif->FormatIdOnglet = 'modif_profil_${PROFILE_ID}' ;
					$lienModif->FormatTitreOnglet = 'Modifier le profil ${PROFILE_TITLE}' ;
					$lienModif->FormatLibelle = "Modifier" ;
					if($table->ZoneParent->ActiverRoutes == 0)
					{
						$lienModif->FormatURL = "?".urlencode($table->ZoneParent->NomParamScriptAppele)."=".urlencode($table->ZoneParent->NomScriptModifProfil).'&idProfil=${PROFILE_ID}' ;
					}
					else
					{
						$lienModif->FormatURL = '?idProfil=${PROFILE_ID}' ;
					}
					$table->DefinitionsColonnes[$i]->Formatteur->Liens[] = $lienModif ;
				}
				
				$nomClasse = $this->NomClasseLienSupprTableauProfil ;
				if(class_exists($nomClasse))
				{
					$lienSuppr = new $nomClasse() ;
					$lienSuppr->FormatIdOnglet = 'suppr_profil_${PROFILE_ID}' ;
					$lienSuppr->FormatTitreOnglet = 'Supprimer le profil ${PROFILE_TITLE}' ;
					$lienSuppr->FormatLibelle = "Supprimer" ;
					if($table->ZoneParent->ActiverRoutes == 0)
					{
						$lienSuppr->FormatURL = "?".urlencode($table->ZoneParent->NomParamScriptAppele)."=".urlencode($table->ZoneParent->NomScriptSupprProfil).'&idProfil=${PROFILE_ID}' ;
					}
					else
					{
						$lienSuppr->FormatURL = '?idProfil=${PROFILE_ID}' ;
					}
					$table->DefinitionsColonnes[$i]->Formatteur->Liens[] = $lienSuppr ;
				}
			}
			public function CreeFormulaireDonnees()
			{
				return new PvFormulaireDonneesHtml() ;
			}
			public function DetermineFormImporteMembreMS(& $script)
			{
				$this->FormImporteMembreMS = $this->CreeFormulaireDonnees() ;
				$this->FormImporteMembreMS->AdopteScript("formImporteMembreMS", $script) ;
				$this->FormImporteMembreMS->InclureTotalElements = 0 ;
				$this->FormImporteMembreMS->InclureElementEnCours = 0 ;
				$this->FormImporteMembreMS->ChargeConfig() ;
				$this->FltFichImporteMembreMS = $this->FormImporteMembreMS->InsereFltEditHttpUpload("chemin_fichier", $this->CheminDossierImporteMembreMS, "chemin_fichier") ;
				$this->FltFichImporteMembreMS->Libelle = $this->LibFltFichierImporteMembreMS ;
				$this->CompFichImporteMembreMS = $this->FltFichImporteMembreMS->ObtientComposant() ;
				$this->CompFichImporteMembreMS->ExtensionsAcceptees = array('txt', 'csv') ;
				$this->FormImporteMembreMS->CommandeExecuter->InsereActCmd("PvActCmdImporteMembreMS", array()) ;
				if($this->FormImporteMembreMS->InscrireCommandeAnnuler == 1)
				{
					$this->FormImporteMembreMS->RedirigeAnnulerVersScript($script->ZoneParent->NomScriptListeMembres) ;
				}
			}
			public function RenduFormImporteMembreMS(& $script)
			{
				return $this->FormImporteMembreMS->RenduDispositif() ;
			}
			public function AppliqueActCmdImporteMembreMS(& $act)
			{
				$membership = & $act->ZoneParent->Membership ;
				$cheminFich = $this->FltFichImporteMembreMS->Lie() ;
				$totalSucces = 0 ;
				$totalEchecs = 0 ;
				if($cheminFich != '')
				{
					$colsAcceptees = $this->NomsColImporteMembreMS($membership) ;
					$nomsColsAcceptees = array_keys($colsAcceptees) ;
					if(($fh = fopen($cheminFich, 'r')) !== false)
					{
						$nomsCol = array() ;
						if(($ligne = fgets($fh)) !== false)
						{
							$donneesEntete = explode(";", trim($ligne)) ;
							foreach($donneesEntete as $i => $valEntete)
							{
								$valEntete = trim(strtolower($valEntete)) ;
								if(in_array($valEntete, $nomsColsAcceptees))
								{
									$nomsCol[$i] = $colsAcceptees[$valEntete] ;
								}
							}
						}
						// print_r($nomsCol) ;
						if(count($nomsCol) > 0)
						{
							while(($ligne = fgets($fh)) !== false)
							{
								$lgn = array() ;
								$donneesLigne = explode(";", trim($ligne)) ;
								foreach($donneesLigne as $i => $valLigne)
								{
									if(! isset($nomsCol[$i]))
									{
										continue ;
									}
									$valLigne = trim($valLigne) ;
									$lgn[$nomsCol[$i]] = $valLigne ;
								}
								// print_r($lgn) ;
								$ok = $this->ImporteLgnMembreMS($lgn, $membership) ;
								if($ok)
								{
									$totalSucces ++ ;
								}
								else
								{
									$totalEchecs ++ ;
								}
							}
						}
						else
						{
							$act->CommandeParent->RenseigneErreur('Aucune colonne n\'est renseign??e') ;
							return ;
						}
						fclose($fh) ;
					}
					else
					{
						$act->CommandeParent->RenseigneErreur('Veuillez soumettre un fichier valide') ;
						return ;
					}
					$totalMembres = $totalSucces + $totalEchecs ;
					if($totalSucces > 0)
					{
						$act->CommandeParent->ConfirmeSucces(
							_parse_pattern(
								$this->MessageImporteMembreMSSucces,
								array('TotalSucces' => $totalSucces, 'TotalEchecs' => $totalEchecs, 'totalMembres' => $totalMembres)
							)
						) ;
					}
					elseif($totalEchecs > 0)
					{
						$act->CommandeParent->ConfirmeSucces(
							_parse_pattern(
								$this->MessageImporteMembreMSEchecs,
								array('TotalSucces' => $totalSucces, 'TotalEchecs' => $totalEchecs, 'totalMembres' => $totalMembres)
							)
						) ;
					}
				}
				else
				{
					$act->CommandeParent->RenseigneErreur('Veuillez soumettre un fichier valide') ;
				}
			}
			protected function ImporteLgnMembreMS(& $lgn, & $membership)
			{
				$bd = & $membership->Database ;
				$lgnMembre = array() ;
				$modeEdition = 1 ;
				if(isset($lgn[$membership->LoginMemberColumn]))
				{
					$lgnMembre = $bd->FetchSqlRow(
						'select * from '.$membership->MemberTable.' MEMBER_TABLE where '.$bd->EscapeVariableName($membership->LoginMemberColumn).'='.$bd->ParamPrefix.'login',
						array('login' => $lgn[$membership->LoginMemberColumn])
					) ;
					if(count($lgnMembre) > 0)
					{
						$modeEdition = 2 ;
					}
				}
				if($lgn[$membership->LoginMemberColumn] == '')
				{
					return false ;
				}
				$idProfil = ($this->IdProfilImporteMembreMS == 0) ? $membership->RootMemberId : $this->IdProfilImporteMembreMS ;
				if(isset($lgn[$membership->ProfileMemberColumn]))
				{
					$lgnProfil = $bd->FetchSqlRow('select '.$bd->EscapeTableName($membership->IdProfileColumn).' PROFILE_ID
from '.$bd->EscapeTableName($membership->ProfileTable).' PROFILE_TABLE
where (PROFILE_TABLE. '.$bd->EscapeVariableName($membership->IdProfileColumn).' = '.$bd->ParamPrefix.'profil
or PROFILE_TABLE. '.$bd->EscapeVariableName($membership->TitleProfileColumn).' = '.$bd->ParamPrefix.'profil)', array('profil' => $lgn[$membership->ProfileMemberColumn])) ;
					if(count($lgnProfil) > 0)
					{
						$idProfil = $lgnProfil["PROFILE_ID"] ;
					}
				}
				$lgn[$membership->ProfileMemberColumn] = $idProfil ;
				if(isset($lgn[$membership->EnableMemberColumn]))
				{
					$lgn[$membership->EnableMemberColumn] = $this->ExtraitValeurBool($lgn[$membership->EnableMemberColumn]) ;
				}
				else
				{
					$lgn[$membership->EnableMemberColumn] = $membership->EnableMemberTrueValue ;
				}
				if(isset($lgn[$membership->ADActivatedMemberColumn]))
				{
					$lgn[$membership->ADActivatedMemberColumn] = $this->ExtraitValeurBool($lgn[$membership->ADActivatedMemberColumn]) ;
				}
				if(! isset($lgn[$membership->PasswordMemberColumn]))
				{
					if($membership->ADActivatedMemberColumn == '' || ! isset($lgn[$membership->ADActivatedMemberColumn]) || $lgn[$membership->ADActivatedMemberColumn] != $membership->ADActivatedMemberTrueValue)
					{
						$lgn[$membership->PasswordMemberColumn] = $lgn[$membership->LoginMemberColumn] ;
					}
				}
				if($membership->PasswordMemberExpr != '')
				{
					$lgn[$bd->ExprKeyName][$membership->PasswordMemberColumn] = $membership->PasswordMemberExpr.'(<self>)' ;
				}
				if(! isset($lgn[$membership->FirstNameMemberColumn]) || ! isset($lgn[$membership->LastNameMemberColumn]))
				{
					return false ;
				}
				if(! validate_name_user_format($lgn[$membership->LoginMemberColumn]))
				{
					return false ;
				}
				if(! validate_email_format($lgn[$membership->EmailMemberColumn]))
				{
					return false ;
				}
				if($modeEdition == 1)
				{
					$ok = $bd->InsertRow($membership->MemberTable, $lgn) ;
					return $ok ;
				}
				else
				{
					$loginMembre = $lgn[$membership->LoginMemberColumn] ;
					unset($lgn[$membership->LoginMemberColumn]) ;
					$ok = $bd->UpdateRow(
						$membership->MemberTable,
						$lgn,
						$bd->EscapeVariableName($membership->LoginMemberColumn)." = ".$bd->ParamPrefix."login",
						array("login" => $loginMembre)
					) ;
					return $ok ;
				}
			}
			protected function ExtraitValeurBool($contenu)
			{
				return (in_array(strtolower($contenu), array('1', 'oui', 'y', 'yes', 'ok'))) ? 1 : 0 ;
			}
		}
		
		class PvActCmdImporteMembreMS extends PvActCmdBase
		{
			public function Execute()
			{
				$this->ZoneParent->RemplisseurConfigMembership->AppliqueActCmdImporteMembreMS($this) ;
			}
		}
	}
	
?>