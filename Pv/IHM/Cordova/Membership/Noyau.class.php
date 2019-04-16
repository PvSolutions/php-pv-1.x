<?php
	
	if(! defined('PV_NOYAU_MEMBERSHIP_CORDOVA'))
	{
		define('PV_NOYAU_MEMBERSHIP_CORDOVA', 1) ;
		
		class PvTableauMembresCordova extends PvTableauMembresMSHtml
		{
			public $SautLigneSansCommande = 0 ;
			public $MaxFiltresEditionParLigne = 2 ;
			public $ClasseCSSRangee = "table-striped" ;
			public $ClasseCSSBtnNav = "btn-primary" ;
			public $ClsBstBoutonSoumettre = "btn-success" ;
			public $ClsBstFormFiltresSelect = "col-sm-8" ;
			public $ActPrincCalculeRendu ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DessinateurFiltresSelection = new PvDessinFiltresDonneesCordova() ;
				$this->DessinateurBlocCommandes = new PvDessinCommandesCordova() ;
				$this->NavigateurRangees = new PvNavTableauDonneesCordova() ;
			}
			public function DetecteParametresLocalisation()
			{
				parent::DetecteParametresLocalisation() ;
			}
			public function DeclarationSoumetFormulaireFiltres($filtres)
			{
				return PvTableauDonneesCordova::DeclarationSoumetFormulaireFiltresTabl($this, $filtres) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvTableauDonneesCordova::ChargeConfigTabl($this) ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				PvTableauDonneesCordova::AdopteZoneTabl($this, $nom, $zone) ;
			}
			protected function RenduFormulaireFiltres()
			{
				if($this->CacherFormulaireFiltres)
					return '' ;
				if($this->EstNul($this->DessinateurFiltresSelection))
				{
					$this->InitDessinateurFiltresSelection() ;
				}
				// print_r(get_class($this->DessinateurFiltresSelection)) ;
				if($this->EstNul($this->DessinateurFiltresSelection))
				{
					return "<p>Le dessinateur de filtres n'est pas d&eacute;fini</p>" ;
				}
				$ctn = "" ;
				if(! $this->PossedeFiltresRendus())
				{
					return '' ;
				}
				$this->DessinateurFiltresSelection->MaxFiltresParLigne = $this->MaxFiltresEditionParLigne ;
				$ctn .= '<form class="FormulaireFiltres" method="post" enctype="multipart/form-data" onsubmit="return SoumetFormulaire'.$this->IDInstanceCalc.'(this) ;" role="form">'.PHP_EOL ;
				$ctn .= '<div class="panel panel-default">'.PHP_EOL ;
				if($this->TitreFormulaireFiltres != '')
				{
					$ctn .= '<div class="panel-heading" align="'.$this->AlignTitreFormulaireFiltres.'">'.PHP_EOL ;
					$ctn .= $this->TitreFormulaireFiltres ;
					$ctn .= '</div>'.PHP_EOL ;
				}
				$ctn .= '<div class="panel-body">'.PHP_EOL ;
				$ctn .= '<div class="container-fluid">'.PHP_EOL ;
				$ctn .= '<div class="row">'.PHP_EOL ;
				$ctn .= '<div class="'.$this->ClsBstFormFiltresSelect.'">'.PHP_EOL ;
				$ctn .= $this->DessinateurFiltresSelection->Execute($this->ScriptParent, $this, $this->FiltresSelection) ;
				$ctn .= '<input type="hidden" name="'.$this->NomParamFiltresSoumis().'" id="'.$this->NomParamFiltresSoumis().'" value="1" />'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '<div class="panel-footer">'.PHP_EOL ;
				$ctn .= '<button class="btn '.$this->ClsBstBoutonSoumettre.'" align="'.$this->AlignBoutonSoumettreFormulaireFiltres.'" type="submit">'.$this->TitreBoutonSoumettreFormulaireFiltres.'</button>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</form>'.PHP_EOL ;
				$ctn .= $this->DeclarationSoumetFormulaireFiltres($this->FiltresSelection) ;
				return $ctn ;
			}
			protected function RenduRangeeDonnees()
			{
				return PvTableauDonneesCordova::RenduRangeeDonneesTabl($this) ;
			}
		}
		class PvTableauRolesCordova extends PvTableauRolesMSHtml
		{
			public $SautLigneSansCommande = 0 ;
			public $MaxFiltresEditionParLigne = 2 ;
			public $ClasseCSSRangee = "table-striped" ;
			public $ClasseCSSBtnNav = "btn-primary" ;
			public $ClsBstBoutonSoumettre = "btn-success" ;
			public $ClsBstFormFiltresSelect = "col-sm-8" ;
			public $ActPrincCalculeRendu ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DessinateurFiltresSelection = new PvDessinFiltresDonneesCordova() ;
				$this->DessinateurBlocCommandes = new PvDessinCommandesCordova() ;
				$this->NavigateurRangees = new PvNavTableauDonneesCordova() ;
			}
			public function DetecteParametresLocalisation()
			{
				parent::DetecteParametresLocalisation() ;
			}
			public function DeclarationSoumetFormulaireFiltres($filtres)
			{
				return PvTableauDonneesCordova::DeclarationSoumetFormulaireFiltresTabl($this, $filtres) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvTableauDonneesCordova::ChargeConfigTabl($this) ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				PvTableauDonneesCordova::AdopteZoneTabl($this, $nom, $zone) ;
			}
			protected function RenduFormulaireFiltres()
			{
				if($this->CacherFormulaireFiltres)
					return '' ;
				if($this->EstNul($this->DessinateurFiltresSelection))
				{
					$this->InitDessinateurFiltresSelection() ;
				}
				// print_r(get_class($this->DessinateurFiltresSelection)) ;
				if($this->EstNul($this->DessinateurFiltresSelection))
				{
					return "<p>Le dessinateur de filtres n'est pas d&eacute;fini</p>" ;
				}
				$ctn = "" ;
				if(! $this->PossedeFiltresRendus())
				{
					return '' ;
				}
				$this->DessinateurFiltresSelection->MaxFiltresParLigne = $this->MaxFiltresEditionParLigne ;
				$ctn .= '<form class="FormulaireFiltres" method="post" enctype="multipart/form-data" onsubmit="return SoumetFormulaire'.$this->IDInstanceCalc.'(this) ;" role="form">'.PHP_EOL ;
				$ctn .= '<div class="panel panel-default">'.PHP_EOL ;
				if($this->TitreFormulaireFiltres != '')
				{
					$ctn .= '<div class="panel-heading" align="'.$this->AlignTitreFormulaireFiltres.'">'.PHP_EOL ;
					$ctn .= $this->TitreFormulaireFiltres ;
					$ctn .= '</div>'.PHP_EOL ;
				}
				$ctn .= '<div class="panel-body">'.PHP_EOL ;
				$ctn .= '<div class="container-fluid">'.PHP_EOL ;
				$ctn .= '<div class="row">'.PHP_EOL ;
				$ctn .= '<div class="'.$this->ClsBstFormFiltresSelect.'">'.PHP_EOL ;
				$ctn .= $this->DessinateurFiltresSelection->Execute($this->ScriptParent, $this, $this->FiltresSelection) ;
				$ctn .= '<input type="hidden" name="'.$this->NomParamFiltresSoumis().'" id="'.$this->NomParamFiltresSoumis().'" value="1" />'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '<div class="panel-footer">'.PHP_EOL ;
				$ctn .= '<button class="btn '.$this->ClsBstBoutonSoumettre.'" align="'.$this->AlignBoutonSoumettreFormulaireFiltres.'" type="submit">'.$this->TitreBoutonSoumettreFormulaireFiltres.'</button>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</form>'.PHP_EOL ;
				$ctn .= $this->DeclarationSoumetFormulaireFiltres($this->FiltresSelection) ;
				return $ctn ;
			}
			protected function RenduRangeeDonnees()
			{
				return PvTableauDonneesCordova::RenduRangeeDonneesTabl($this) ;
			}
		}
		class PvTableauProfilsCordova extends PvTableauProfilsMSHtml
		{
			public $SautLigneSansCommande = 0 ;
			public $MaxFiltresEditionParLigne = 2 ;
			public $ClasseCSSRangee = "table-striped" ;
			public $ClasseCSSBtnNav = "btn-primary" ;
			public $ClsBstBoutonSoumettre = "btn-success" ;
			public $ClsBstFormFiltresSelect = "col-sm-8" ;
			public $ActPrincCalculeRendu ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DessinateurFiltresSelection = new PvDessinFiltresDonneesCordova() ;
				$this->DessinateurBlocCommandes = new PvDessinCommandesCordova() ;
				$this->NavigateurRangees = new PvNavTableauDonneesCordova() ;
			}
			public function DetecteParametresLocalisation()
			{
				parent::DetecteParametresLocalisation() ;
			}
			public function DeclarationSoumetFormulaireFiltres($filtres)
			{
				return PvTableauDonneesCordova::DeclarationSoumetFormulaireFiltresTabl($this, $filtres) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvTableauDonneesCordova::ChargeConfigTabl($this) ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				PvTableauDonneesCordova::AdopteZoneTabl($this, $nom, $zone) ;
			}
			protected function RenduFormulaireFiltres()
			{
				if($this->CacherFormulaireFiltres)
					return '' ;
				if($this->EstNul($this->DessinateurFiltresSelection))
				{
					$this->InitDessinateurFiltresSelection() ;
				}
				// print_r(get_class($this->DessinateurFiltresSelection)) ;
				if($this->EstNul($this->DessinateurFiltresSelection))
				{
					return "<p>Le dessinateur de filtres n'est pas d&eacute;fini</p>" ;
				}
				$ctn = "" ;
				if(! $this->PossedeFiltresRendus())
				{
					return '' ;
				}
				$this->DessinateurFiltresSelection->MaxFiltresParLigne = $this->MaxFiltresEditionParLigne ;
				$ctn .= '<form class="FormulaireFiltres" method="post" enctype="multipart/form-data" onsubmit="return SoumetFormulaire'.$this->IDInstanceCalc.'(this) ;" role="form">'.PHP_EOL ;
				$ctn .= '<div class="panel panel-default">'.PHP_EOL ;
				if($this->TitreFormulaireFiltres != '')
				{
					$ctn .= '<div class="panel-heading" align="'.$this->AlignTitreFormulaireFiltres.'">'.PHP_EOL ;
					$ctn .= $this->TitreFormulaireFiltres ;
					$ctn .= '</div>'.PHP_EOL ;
				}
				$ctn .= '<div class="panel-body">'.PHP_EOL ;
				$ctn .= '<div class="container-fluid">'.PHP_EOL ;
				$ctn .= '<div class="row">'.PHP_EOL ;
				$ctn .= '<div class="'.$this->ClsBstFormFiltresSelect.'">'.PHP_EOL ;
				$ctn .= $this->DessinateurFiltresSelection->Execute($this->ScriptParent, $this, $this->FiltresSelection) ;
				$ctn .= '<input type="hidden" name="'.$this->NomParamFiltresSoumis().'" id="'.$this->NomParamFiltresSoumis().'" value="1" />'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '<div class="panel-footer">'.PHP_EOL ;
				$ctn .= '<button class="btn '.$this->ClsBstBoutonSoumettre.'" align="'.$this->AlignBoutonSoumettreFormulaireFiltres.'" type="submit">'.$this->TitreBoutonSoumettreFormulaireFiltres.'</button>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</form>'.PHP_EOL ;
				$ctn .= $this->DeclarationSoumetFormulaireFiltres($this->FiltresSelection) ;
				return $ctn ;
			}
			protected function RenduRangeeDonnees()
			{
				return PvTableauDonneesCordova::RenduRangeeDonneesTabl($this) ;
			}
		}
		
		class PvFormulaireAjoutMembreCordova extends PvFormulaireAjoutMembreMS
		{
			public $ActPrincCalculeRendu ;
			public $ActPrincSelectCommande ;
			public function DetecteParametresLocalisation()
			{
				parent::DetecteParametresLocalisation() ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesCordova() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesCordova() ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				PvFormulaireDonneesCordova::AdopteZoneForm($this, $nom, $zone) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvFormulaireDonneesCordova::ChargeConfigForm($this) ;
			}
			public function RenduResultatCommandeExecutee()
			{
				return PvFormulaireDonneesCordova::RenduResultatCommandeExecuteeForm($this) ;
			}
			protected function DeclarationSoumetFormulaireFiltres($filtres)
			{
				return PvFormulaireDonneesCordova::DeclarationSoumetFormulaireFiltresFrm($this, $filtres) ;
			}
		}
		class PvFormulaireInscriptionMembreCordova extends PvFormulaireInscriptionMembreMS
		{
			public $ActPrincCalculeRendu ;
			public $ActPrincSelectCommande ;
			public function DetecteParametresLocalisation()
			{
				parent::DetecteParametresLocalisation() ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesCordova() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesCordova() ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				PvFormulaireDonneesCordova::AdopteZoneForm($this, $nom, $zone) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvFormulaireDonneesCordova::ChargeConfigForm($this) ;
			}
			protected function RenduFormulaireFiltres()
			{
				if($this->InclureElementEnCours == 1)
				{
					$this->ElementEnCoursTrouve = 1 ;
				}
				$ctn = parent::RenduFormulaireFiltres() ;
				return $ctn ;
			}
			public function RenduResultatCommandeExecutee()
			{
				return PvFormulaireDonneesCordova::RenduResultatCommandeExecuteeForm($this) ;
			}
			protected function DeclarationSoumetFormulaireFiltres($filtres)
			{
				return PvFormulaireDonneesCordova::DeclarationSoumetFormulaireFiltresFrm($this, $filtres) ;
			}
		}
		class PvFormulaireModifMembreCordova extends PvFormulaireModifMembreMS
		{
			public $ActPrincCalculeRendu ;
			public $ActPrincSelectCommande ;
			public function DetecteParametresLocalisation()
			{
				parent::DetecteParametresLocalisation() ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesCordova() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesCordova() ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				PvFormulaireDonneesCordova::AdopteZoneForm($this, $nom, $zone) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvFormulaireDonneesCordova::ChargeConfigForm($this) ;
			}
			protected function RenduFormulaireFiltres()
			{
				if($this->InclureElementEnCours == 1)
				{
					$this->ElementEnCoursTrouve = 1 ;
				}
				$ctn = parent::RenduFormulaireFiltres() ;
				return $ctn ;
			}
			public function RenduResultatCommandeExecutee()
			{
				return PvFormulaireDonneesCordova::RenduResultatCommandeExecuteeForm($this) ;
			}
			protected function DeclarationSoumetFormulaireFiltres($filtres)
			{
				return PvFormulaireDonneesCordova::DeclarationSoumetFormulaireFiltresFrm($this, $filtres) ;
			}
		}
		class PvFormulaireModifInfosCordova extends PvFormulaireModifInfosMS
		{
			public $ActPrincCalculeRendu ;
			public $ActPrincSelectCommande ;
			public function DetecteParametresLocalisation()
			{
				parent::DetecteParametresLocalisation() ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesCordova() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesCordova() ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				PvFormulaireDonneesCordova::AdopteZoneForm($this, $nom, $zone) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvFormulaireDonneesCordova::ChargeConfigForm($this) ;
			}
			protected function RenduFormulaireFiltres()
			{
				if($this->InclureElementEnCours == 1)
				{
					$this->ElementEnCoursTrouve = 1 ;
				}
				$ctn = parent::RenduFormulaireFiltres() ;
				return $ctn ;
			}
			public function RenduResultatCommandeExecutee()
			{
				return PvFormulaireDonneesCordova::RenduResultatCommandeExecuteeForm($this) ;
			}
			protected function DeclarationSoumetFormulaireFiltres($filtres)
			{
				return PvFormulaireDonneesCordova::DeclarationSoumetFormulaireFiltresFrm($this, $filtres) ;
			}
		}
		class PvFormulaireSupprMembreCordova extends PvFormulaireSupprMembreMS
		{
			public $ActPrincCalculeRendu ;
			public $ActPrincSelectCommande ;
			public function DetecteParametresLocalisation()
			{
				parent::DetecteParametresLocalisation() ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesCordova() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesCordova() ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				PvFormulaireDonneesCordova::AdopteZoneForm($this, $nom, $zone) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvFormulaireDonneesCordova::ChargeConfigForm($this) ;
			}
			protected function RenduFormulaireFiltres()
			{
				if($this->InclureElementEnCours == 1)
				{
					$this->ElementEnCoursTrouve = 1 ;
				}
				$ctn = parent::RenduFormulaireFiltres() ;
				return $ctn ;
			}
			public function RenduResultatCommandeExecutee()
			{
				return PvFormulaireDonneesCordova::RenduResultatCommandeExecuteeForm($this) ;
			}
			protected function DeclarationSoumetFormulaireFiltres($filtres)
			{
				return PvFormulaireDonneesCordova::DeclarationSoumetFormulaireFiltresFrm($this, $filtres) ;
			}
		}
		class PvFormulaireChangeMPMembreCordova extends PvFormulaireChangeMPMembreMS
		{
			public $MaxFiltresEditionParLigne = 1 ;
			public $ActPrincCalculeRendu ;
			public $ActPrincSelectCommande ;
			public function DetecteParametresLocalisation()
			{
				parent::DetecteParametresLocalisation() ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesCordova() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesCordova() ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				PvFormulaireDonneesCordova::AdopteZoneForm($this, $nom, $zone) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvFormulaireDonneesCordova::ChargeConfigForm($this) ;
			}
			protected function RenduFormulaireFiltres()
			{
				if($this->InclureElementEnCours == 1)
				{
					$this->ElementEnCoursTrouve = 1 ;
				}
				$ctn = parent::RenduFormulaireFiltres() ;
				return $ctn ;
			}
			public function RenduResultatCommandeExecutee()
			{
				return PvFormulaireDonneesCordova::RenduResultatCommandeExecuteeForm($this) ;
			}
			protected function DeclarationSoumetFormulaireFiltres($filtres)
			{
				return PvFormulaireDonneesCordova::DeclarationSoumetFormulaireFiltresFrm($this, $filtres) ;
			}
		}
		class PvFormulaireDoitChangerMotPasseCordova extends PvFormulaireDoitChangerMotPasseMS
		{
			public $ActPrincCalculeRendu ;
			public $ActPrincSelectCommande ;
			public function DetecteParametresLocalisation()
			{
				parent::DetecteParametresLocalisation() ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesCordova() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesCordova() ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				PvFormulaireDonneesCordova::AdopteZoneForm($this, $nom, $zone) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvFormulaireDonneesCordova::ChargeConfigForm($this) ;
			}
			protected function RenduFormulaireFiltres()
			{
				if($this->InclureElementEnCours == 1)
				{
					$this->ElementEnCoursTrouve = 1 ;
				}
				$ctn = parent::RenduFormulaireFiltres() ;
				return $ctn ;
			}
			public function RenduResultatCommandeExecutee()
			{
				return PvFormulaireDonneesCordova::RenduResultatCommandeExecuteeForm($this) ;
			}
			protected function DeclarationSoumetFormulaireFiltres($filtres)
			{
				return PvFormulaireDonneesCordova::DeclarationSoumetFormulaireFiltresFrm($this, $filtres) ;
			}
		}
		class PvFormulaireChangeMotPasseCordova extends PvFormulaireChangeMotPasseMS
		{
			public $MaxFiltresEditionParLigne = 1 ;
			public $ActPrincCalculeRendu ;
			public $ActPrincSelectCommande ;
			public function DetecteParametresLocalisation()
			{
				parent::DetecteParametresLocalisation() ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesCordova() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesCordova() ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				PvFormulaireDonneesCordova::AdopteZoneForm($this, $nom, $zone) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvFormulaireDonneesCordova::ChargeConfigForm($this) ;
			}
			protected function RenduFormulaireFiltres()
			{
				if($this->InclureElementEnCours == 1)
				{
					$this->ElementEnCoursTrouve = 1 ;
				}
				$ctn = parent::RenduFormulaireFiltres() ;
				return $ctn ;
			}
			public function RenduResultatCommandeExecutee()
			{
				return PvFormulaireDonneesCordova::RenduResultatCommandeExecuteeForm($this) ;
			}
			protected function DeclarationSoumetFormulaireFiltres($filtres)
			{
				return PvFormulaireDonneesCordova::DeclarationSoumetFormulaireFiltresFrm($this, $filtres) ;
			}
		}
		
		class PvFormulaireAjoutRoleCordova extends PvFormulaireAjoutRoleMS
		{
			public $ActPrincCalculeRendu ;
			public $ActPrincSelectCommande ;
			public function DetecteParametresLocalisation()
			{
				parent::DetecteParametresLocalisation() ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesCordova() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesCordova() ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				PvFormulaireDonneesCordova::AdopteZoneForm($this, $nom, $zone) ;
			}
			protected function RenduFormulaireFiltres()
			{
				if($this->InclureElementEnCours == 1)
				{
					$this->ElementEnCoursTrouve = 1 ;
				}
				$ctn = parent::RenduFormulaireFiltres() ;
				return $ctn ;
			}
			public function RenduResultatCommandeExecutee()
			{
				return PvFormulaireDonneesCordova::RenduResultatCommandeExecuteeForm($this) ;
			}
			protected function DeclarationSoumetFormulaireFiltres($filtres)
			{
				return PvFormulaireDonneesCordova::DeclarationSoumetFormulaireFiltresFrm($this, $filtres) ;
			}
			protected function DeclareCompListeProfils()
			{
				$membership = & $this->ZoneParent->Membership ;
				$filtreLstProfils = & $this->FiltreListeProfilsRole ;
				$comp = $filtreLstProfils->DeclareComposant("PvZoneBoiteOptionsCocherBootstrap") ;
				$comp->FournisseurDonnees = new PvFournisseurDonneesSql() ;
				$comp->FournisseurDonnees->BaseDonnees = & $membership->Database ;
				if(! $form->InclureElementEnCours)
				{
					$comp->FournisseurDonnees->RequeteSelection = "(".$membership->SqlProfilesForNewRole().")" ;
				}
				else
				{
					$comp->FournisseurDonnees->RequeteSelection = "(".$membership->SqlProfilesForRole().")" ;
					$filtreIdRole = $form->ScriptParent->CreeFiltreHttpGet("idRole") ;
					$filtreIdRole->Obligatoire = 1 ;
					$filtreIdRole->ExpressionDonnees = 'ROLE_ID = <self>' ;
					$comp->FiltresSelection[] = $filtreIdRole ;
				}
				$comp->NomColonneValeur = "PROFILE_ID" ;
				$comp->NomColonneLibelle = "PROFILE_TITLE" ;
				$comp->NomColonneValeurParDefaut = "PRIVILEGE_ENABLED" ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvFormulaireDonneesCordova::ChargeConfigForm($this) ;
				$this->DeclareCompListeProfils() ;
			}
		}
		class PvFormulaireModifRoleCordova extends PvFormulaireModifRoleMS
		{
			public $ActPrincCalculeRendu ;
			public $ActPrincSelectCommande ;
			public function DetecteParametresLocalisation()
			{
				parent::DetecteParametresLocalisation() ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesCordova() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesCordova() ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				PvFormulaireDonneesCordova::AdopteZoneForm($this, $nom, $zone) ;
			}
			protected function RenduFormulaireFiltres()
			{
				if($this->InclureElementEnCours == 1)
				{
					$this->ElementEnCoursTrouve = 1 ;
				}
				$ctn = parent::RenduFormulaireFiltres() ;
				return $ctn ;
			}
			public function RenduResultatCommandeExecutee()
			{
				return PvFormulaireDonneesCordova::RenduResultatCommandeExecuteeForm($this) ;
			}
			protected function DeclarationSoumetFormulaireFiltres($filtres)
			{
				return PvFormulaireDonneesCordova::DeclarationSoumetFormulaireFiltresFrm($this, $filtres) ;
			}
			protected function DeclareCompListeProfils()
			{
				$membership = & $this->ZoneParent->Membership ;
				$filtreLstProfils = & $this->FiltreListeProfilsRole ;
				$comp = $filtreLstProfils->DeclareComposant("PvZoneBoiteOptionsCocherCordova") ;
				$comp->FournisseurDonnees = new PvFournisseurDonneesSql() ;
				$comp->FournisseurDonnees->BaseDonnees = & $membership->Database ;
				$comp->FournisseurDonnees->RequeteSelection = "(".$membership->SqlProfilesForRole().")" ;
				$filtreIdRole = $this->ScriptParent->CreeFiltreHttpGet("idRole") ;
				$filtreIdRole->Obligatoire = 1 ;
				$filtreIdRole->ExpressionDonnees = 'ROLE_ID = <self>' ;
				$comp->FiltresSelection[] = $filtreIdRole ;
				$comp->NomColonneValeur = "PROFILE_ID" ;
				$comp->NomColonneLibelle = "PROFILE_TITLE" ;
				$comp->NomColonneValeurParDefaut = "PRIVILEGE_ENABLED" ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvFormulaireDonneesCordova::ChargeConfigForm($this) ;
				$this->DeclareCompListeProfils() ;
			}
		}
		class PvFormulaireSupprRoleCordova extends PvFormulaireSupprRoleMS
		{
			public $ActPrincCalculeRendu ;
			public $ActPrincSelectCommande ;
			public function DetecteParametresLocalisation()
			{
				parent::DetecteParametresLocalisation() ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesCordova() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesCordova() ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				PvFormulaireDonneesCordova::AdopteZoneForm($this, $nom, $zone) ;
			}
			protected function RenduFormulaireFiltres()
			{
				if($this->InclureElementEnCours == 1)
				{
					$this->ElementEnCoursTrouve = 1 ;
				}
				$ctn = parent::RenduFormulaireFiltres() ;
				return $ctn ;
			}
			public function RenduResultatCommandeExecutee()
			{
				return PvFormulaireDonneesCordova::RenduResultatCommandeExecuteeForm($this) ;
			}
			protected function DeclarationSoumetFormulaireFiltres($filtres)
			{
				return PvFormulaireDonneesCordova::DeclarationSoumetFormulaireFiltresFrm($this, $filtres) ;
			}
			protected function DeclareCompListeProfils()
			{
				$membership = & $this->ZoneParent->Membership ;
				$filtreLstProfils = & $this->FiltreListeProfilsRole ;
				$comp = $filtreLstProfils->DeclareComposant("PvZoneBoiteOptionsCocherCordova") ;
				$comp->FournisseurDonnees = new PvFournisseurDonneesSql() ;
				$comp->FournisseurDonnees->BaseDonnees = & $membership->Database ;
				$comp->FournisseurDonnees->RequeteSelection = "(".$membership->SqlProfilesForRole().")" ;
				$filtreIdRole = $this->ScriptParent->CreeFiltreHttpGet("idRole") ;
				$filtreIdRole->Obligatoire = 1 ;
				$filtreIdRole->ExpressionDonnees = 'ROLE_ID = <self>' ;
				$comp->FiltresSelection[] = $filtreIdRole ;
				$comp->NomColonneValeur = "PROFILE_ID" ;
				$comp->NomColonneLibelle = "PROFILE_TITLE" ;
				$comp->NomColonneValeurParDefaut = "PRIVILEGE_ENABLED" ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvFormulaireDonneesCordova::ChargeConfigForm($this) ;
				$this->DeclareCompListeProfils() ;
			}
		}
		
		class PvFormulaireAjoutProfilCordova extends PvFormulaireAjoutProfilMS
		{
			public $ActPrincCalculeRendu ;
			public $ActPrincSelectCommande ;
			public function DetecteParametresLocalisation()
			{
				parent::DetecteParametresLocalisation() ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesCordova() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesCordova() ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				PvFormulaireDonneesCordova::AdopteZoneForm($this, $nom, $zone) ;
			}
			protected function RenduFormulaireFiltres()
			{
				if($this->InclureElementEnCours == 1)
				{
					$this->ElementEnCoursTrouve = 1 ;
				}
				$ctn = parent::RenduFormulaireFiltres() ;
				return $ctn ;
			}
			public function RenduResultatCommandeExecutee()
			{
				return PvFormulaireDonneesCordova::RenduResultatCommandeExecuteeForm($this) ;
			}
			protected function DeclarationSoumetFormulaireFiltres($filtres)
			{
				return PvFormulaireDonneesCordova::DeclarationSoumetFormulaireFiltresFrm($this, $filtres) ;
			}
			protected function DeclareCompListeRoles()
			{
				$membership = & $this->ZoneParent->Membership ;
				$filtreLstRoles = & $this->FiltreListeRolesProfil ;
				$comp = $filtreLstRoles->DeclareComposant("PvZoneBoiteOptionsCocherCordova") ;
				$comp->FournisseurDonnees = new PvFournisseurDonneesSql() ;
				$comp->FournisseurDonnees->BaseDonnees = & $membership->Database ;
				if(! $this->InclureElementEnCours)
				{
					$comp->FournisseurDonnees->RequeteSelection = "(".$membership->SqlRolesForNewProfile().")" ;
				}
				else
				{
					$comp->FournisseurDonnees->RequeteSelection = "(".$membership->SqlRolesForProfile().")" ;
					$filtreIdProfil = $form->ScriptParent->CreeFiltreHttpGet("idProfil") ;
					$filtreIdProfil->Obligatoire = 1 ;
					$filtreIdProfil->ExpressionDonnees = 'PROFILE_ID = <self>' ;
					$comp->FiltresSelection[] = $filtreIdProfil ;
				}
				$comp->NomColonneValeur = "ROLE_ID" ;
				$comp->NomColonneLibelle = "ROLE_TITLE" ;
				$comp->NomColonneValeurParDefaut = "PRIVILEGE_ENABLED" ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvFormulaireDonneesCordova::ChargeConfigForm($this) ;
				$this->DeclareCompListeRoles() ;
			}
		}
		class PvFormulaireModifProfilCordova extends PvFormulaireModifProfilMS
		{
			public $ActPrincCalculeRendu ;
			public $ActPrincSelectCommande ;
			public function DetecteParametresLocalisation()
			{
				parent::DetecteParametresLocalisation() ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesCordova() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesCordova() ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				PvFormulaireDonneesCordova::AdopteZoneForm($this, $nom, $zone) ;
			}
			protected function RenduFormulaireFiltres()
			{
				if($this->InclureElementEnCours == 1)
				{
					$this->ElementEnCoursTrouve = 1 ;
				}
				$ctn = parent::RenduFormulaireFiltres() ;
				return $ctn ;
			}
			public function RenduResultatCommandeExecutee()
			{
				return PvFormulaireDonneesCordova::RenduResultatCommandeExecuteeForm($this) ;
			}
			protected function DeclarationSoumetFormulaireFiltres($filtres)
			{
				return PvFormulaireDonneesCordova::DeclarationSoumetFormulaireFiltresFrm($this, $filtres) ;
			}
			protected function DeclareCompListeRoles()
			{
				$membership = & $this->ZoneParent->Membership ;
				$filtreLstRoles = & $this->FiltreListeRolesProfil ;
				$comp = $filtreLstRoles->DeclareComposant("PvZoneBoiteOptionsCocherCordova") ;
				$comp->FournisseurDonnees = new PvFournisseurDonneesSql() ;
				$comp->FournisseurDonnees->BaseDonnees = & $membership->Database ;
				if(! $this->InclureElementEnCours)
				{
					$comp->FournisseurDonnees->RequeteSelection = "(".$membership->SqlRolesForNewProfile().")" ;
				}
				else
				{
					$comp->FournisseurDonnees->RequeteSelection = "(".$membership->SqlRolesForProfile().")" ;
					$filtreIdProfil = $this->ScriptParent->CreeFiltreHttpGet("idProfil") ;
					$filtreIdProfil->Obligatoire = 1 ;
					$filtreIdProfil->ExpressionDonnees = 'PROFILE_ID = <self>' ;
					$comp->FiltresSelection[] = $filtreIdProfil ;
				}
				$comp->NomColonneValeur = "ROLE_ID" ;
				$comp->NomColonneLibelle = "ROLE_TITLE" ;
				$comp->NomColonneValeurParDefaut = "PRIVILEGE_ENABLED" ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvFormulaireDonneesCordova::ChargeConfigForm($this) ;
				$this->DeclareCompListeRoles() ;
			}
		}
		class PvFormulaireSupprProfilCordova extends PvFormulaireSupprProfilMS
		{
			public $ActPrincCalculeRendu ;
			public $ActPrincSelectCommande ;
			public function DetecteParametresLocalisation()
			{
				parent::DetecteParametresLocalisation() ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesCordova() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesCordova() ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				PvFormulaireDonneesCordova::AdopteZoneForm($this, $nom, $zone) ;
			}
			protected function RenduFormulaireFiltres()
			{
				if($this->InclureElementEnCours == 1)
				{
					$this->ElementEnCoursTrouve = 1 ;
				}
				$ctn = parent::RenduFormulaireFiltres() ;
				return $ctn ;
			}
			public function RenduResultatCommandeExecutee()
			{
				return PvFormulaireDonneesCordova::RenduResultatCommandeExecuteeForm($this) ;
			}
			protected function DeclarationSoumetFormulaireFiltres($filtres)
			{
				return PvFormulaireDonneesCordova::DeclarationSoumetFormulaireFiltresFrm($this, $filtres) ;
			}
			protected function DeclareCompListeRoles()
			{
				$membership = & $this->ZoneParent->Membership ;
				$filtreLstRoles = & $this->FiltreListeRolesProfil ;
				$comp = $filtreLstRoles->DeclareComposant("PvZoneBoiteOptionsCocherCordova") ;
				$comp->FournisseurDonnees = new PvFournisseurDonneesSql() ;
				$comp->FournisseurDonnees->BaseDonnees = & $membership->Database ;
				if(! $this->InclureElementEnCours)
				{
					$comp->FournisseurDonnees->RequeteSelection = "(".$membership->SqlRolesForNewProfile().")" ;
				}
				else
				{
					$comp->FournisseurDonnees->RequeteSelection = "(".$membership->SqlRolesForProfile().")" ;
					$filtreIdProfil = $this->ScriptParent->CreeFiltreHttpGet("idProfil") ;
					$filtreIdProfil->Obligatoire = 1 ;
					$filtreIdProfil->ExpressionDonnees = 'PROFILE_ID = <self>' ;
					$comp->FiltresSelection[] = $filtreIdProfil ;
				}
				$comp->NomColonneValeur = "ROLE_ID" ;
				$comp->NomColonneLibelle = "ROLE_TITLE" ;
				$comp->NomColonneValeurParDefaut = "PRIVILEGE_ENABLED" ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvFormulaireDonneesCordova::ChargeConfigForm($this) ;
				$this->DeclareCompListeRoles() ;
			}
		}

		class PvFormulaireRecouvreMPCordova extends PvFormulaireRecouvreMPMS
		{
			public $ActPrincCalculeRendu ;
			public $ActPrincSelectCommande ;
			public function DetecteParametresLocalisation()
			{
				parent::DetecteParametresLocalisation() ;
			}
			public function AdopteZone($nom, & $zone)
			{
				parent::AdopteZone($nom, $zone) ;
				PvFormulaireDonneesCordova::AdopteZoneForm($this, $nom, $zone) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvFormulaireDonneesCordova::ChargeConfigForm($this) ;
			}
			protected function RenduComposants()
			{
				$ctn = '' ;
				if(count($this->DispositionComposants))
				{
					$ctn .= '<form class="FormulaireDonnees'.(($this->NomClasseCSS != '') ? ' '.$this->NomClasseCSS : '').'" method="post" enctype="multipart/form-data" onsubmit="SoumetFormulaire'.$this->IDInstanceCalc.'(this); return false ;" role="form">'.PHP_EOL ;
					foreach($this->DispositionComposants as $i => $id)
					{
						if($i > 0)
						{
							$ctn .= PHP_EOL ;
						}
						switch($id)
						{
							case PvDispositionFormulaireDonnees::BlocEntete :
							{
								$ctn .= $this->RenduBlocEntete() ;
							}
							break ;
							case PvDispositionFormulaireDonnees::FormulaireFiltresEdition :
							{
								$ctn .= $this->RenduFormulaireFiltres() ;
							}
							break ;
							case PvDispositionFormulaireDonnees::ResultatCommandeExecutee :
							{
								$ctn .= $this->RenduResultatCommandeExecutee() ;
							}
							break ;
							case PvDispositionFormulaireDonnees::BlocCommandes :
							{
								$ctn .= $this->RenduBlocCommandes() ;
							}
							break ;
							default :
							{
								$ctn .= $this->RenduAutreComposantSupport($id) ;
							}
							break ;
						}
					}
					$ctn .= '</form>' ;
				}
				return $ctn ;
			}
			public function CtnJsActualiseFormulaireFiltres()
			{
				return parent::CtnJsActualiseFormulaireFiltres() ;
			}
			public function RenduResultatCommandeExecutee()
			{
				return PvFormulaireDonneesCordova::RenduResultatCommandeExecuteeForm($this) ;
			}
			public function DeclarationSoumetFormulaireFiltres($filtres)
			{
				return PvFormulaireDonneesCordova::DeclarationSoumetFormulaireFiltresFrm($this, $filtres) ;
			}
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresDonneesCordova() ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCommandesCordova() ;
			}
		}
	}
	
?>