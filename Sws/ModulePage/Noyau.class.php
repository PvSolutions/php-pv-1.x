<?php
	
	if(! defined('NOYAU_MODULE_PAGE_SWS'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/../Pv/IHM/Noyau.class.php" ;
		}
		if(! defined('COMPOSANT_IU_SWS'))
		{
			include dirname(__FILE__).'/../ComposantIU.class.php' ;
		}
		if(! defined('SCRIPT_SWS'))
		{
			include dirname(__FILE__).'/../Script.class.php' ;
		}
		define('NOYAU_MODULE_PAGE_SWS', 1) ;
		
		class ElementRenduBaseSws extends PvObjet
		{
			public $PrefixeTitreMenu = "" ;
			public $TitreMenu = "" ;
			public $Titre = "" ;
			public $Active = 1 ;
			public $PrivilegesConsult = array() ;
			public $PrivilegesEdit = array() ;
			public $CheminDossierIcones = "images/sws" ;
			public $CheminIcone = "" ;
			public $PrefixeNomFichier = "icone-" ;
			public $NomClsCSSIcone = "" ;
			public $AutoDetecterCheminIcone = 1 ;
			public $NumeroVersion = "0.0.1" ;
			public $StadeDev = "Beta" ;
			public $AutoriserMailsEdition = 1 ;
			public $EstIndefini = 0 ;
			protected $DefFluxRSS ;
			protected $PresentDansFluxRSS = 1 ;
			protected $DefRech ;
			protected $PresentDansRech = 1 ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DefFluxRSS = new DefFluxRSSElemRenduSws() ;
				$this->DefFluxRSS->Active = $this->PresentDansFluxRSS ;
				$this->DefRech = new DefRechElemRenduSws() ;
				$this->DefRech->Active = $this->PresentDansRech ;
			}
			public function ObtientVersion()
			{
				return $this->NumeroVersion.' '.$this->StadeDev ;
			}
			public function ObtientUrlPubl()
			{
				return "" ;
			}
			public function ObtientUrlAdmin()
			{
				return "" ;
			}
			public function ObtientNomFichier()
			{
				return "element" ;
			}
			public function ObtientCheminIcone()
			{
				$cheminIcone = $this->CheminIcone ;
				if($this->AutoDetecterCheminIcone && $cheminIcone == "")
				{
					$cheminIconePossible = $this->CheminDossierIcones."/".$this->PrefixeNomFichier.$this->ObtientNomFichier().".png" ;
					// echo "$cheminIconePossible<br />" ;
					if(file_exists($cheminIconePossible))
					{
						$cheminIcone = $cheminIconePossible ;
					}
				}
				return $cheminIcone ;
			}
			public function ObtientTitreMenu()
			{
				return ($this->TitreMenu == "") ? ($this->Titre == "") ? $this->PrefixeTitreMenu." ".$this->NomElementSyst : $this->Titre : $this->TitreMenu ;
			}
			public function RemplitTableauBordAdmin(& $comp, & $script)
			{
			}
			public function EstAccessible()
			{
				return ($this->Active && $this->EstIndefini == 0) ;
			}
			protected function ChargeBarreElemsRendu(& $barreMenu)
			{
				$barreMenu->InclureRenduIcone = 0 ;
				$barreMenu->MenuRacine->InscritSousMenuUrl("Espace publique", ReferentielSws::$SystemeEnCours->ObtientUrlZonePubl($barreMenu->ZoneParent)) ;
				$barreMenu->MenuRacine->InscritSousMenuUrl("Administration", "?") ;
			}
			public function ObtientReqSqlFluxRSS()
			{
				if($this->DefFluxRSS->Active == 0)
				{
					return '' ;
				}
				$bd = $this->ObtientBDSupport() ;
				$sql = 'select '.$this->DefFluxRSS->SqlListeCols($this, $bd) ;
				$sql .= ' from '.$bd->EscapeVariableName($this->DefFluxRSS->NomTable) ;
				$sql .= ' where '.$bd->EscapeVariableName($this->DefFluxRSS->NomColStatutPubl).' = 1' ;
				return $sql ;
			}
			public function ObtientReqSqlRech($motsRech)
			{
				if($this->DefRech->Active == 0)
				{
					return '' ;
				}
				$bd = $this->ObtientBDSupport() ;
				$sql = 'select '.$this->DefRech->SqlListeCols($this, $bd) ;
				$sql .= ' from '.$bd->EscapeVariableName($this->DefRech->NomTable) ;
				$sql .= ' where '.$bd->EscapeVariableName($this->DefRech->NomColStatutPubl).' = 1' ;
				$sql .= ' and ('.$this->DefRech->SqlCond($this, $bd, $motsRech).')' ;
				return $sql ;
			}
			public function FormatElemLienLgnRSS(& $lgn)
			{
			}
			public function FormatElemLienLgnRech(& $lgn)
			{
			}
			public function RemplitMenuPlanSite(& $menu)
			{
			}
			protected function CreeDefFichJoint($nomCol, $titre)
			{
				$defFichJoint = new DefFichJointElemRenduSws() ;
				$defFichJoint->NomCol = $nomCol ;
				$defFichJoint->Titre = $titre ;
				return $defFichJoint ;
			}
			public function DefsFichsJoints()
			{
				return array() ;
			}
		}
		
		class ModulePageBaseSws extends ElementRenduBaseSws
		{
			public $PrefixeTitreMenu = "Module" ;
			public $NomElementSyst ;
			public $SystemeParent ;
			public $NomRef = "" ;
			public $Entites = array() ;
			public $RemplZoneMembrePossible = 1 ;
			public $RemplZonePublPossible = 1 ;
			public $RemplZoneAdminPossible = 1 ;
			public $RemplApplicationPossible = 1 ;
			public $MenuRacine ;
			public $NomActionFluxRSS = "rss" ;
			public $ActionFluxRSS ;
			public $FournitFluxRSS = 0 ;
			public $MaxElemsFluxRSS = 30 ;
			protected function CreeActionFluxRSS()
			{
				return new ActionEnvoiFichierBaseZoneSws() ;
			}
			public function ObtientNomActionFluxRSS()
			{
				return $this->NomActionFluxRSS."_".$this->NomElementSyst ;
			}
			protected function RemplitFluxRSS(& $zone)
			{
				if($this->FournitFluxRSS == 0)
					return ;
				$this->ActionFluxRSS = $this->InsereAction($this->ObtientNomActionFluxRSS(), $this->CreeActionFluxRSS(), $zone) ;
				// print get_class($this->ActionFluxRSS).'<br>' ;
			}
			public function ObtientUrlAdmin()
			{
				$url = parent::ObtientUrlAdmin() ;
				if($url != '' && $url != 'javascript:;')
				{
					return $url ;
				}
				$nomEntites = array_keys($this->Entites) ;
				if(count($nomEntites) > 0)
				{
					$url = $this->Entites[$nomEntites[0]]->ObtientUrlAdmin() ;
				}
				return $url ;
			}
			public function ObtientUrlPubl()
			{
				$url = parent::ObtientUrlPubl() ;
				if($url != '' && $url != 'javascript:;')
				{
					return $url ;
				}
				$nomEntites = array_keys($this->Entites) ;
				if(count($nomEntites) > 0)
				{
					$url = $this->Entites[$nomEntites[0]]->ObtientUrlPubl() ;
				}
				return $url ;
			}
			public function CreeFournDonnees()
			{
				return $this->SystemeParent->CreeFournDonnees() ;
			}
			public function ObtientNomFichier()
			{
				return $this->NomRef ;
			}
			public function ObtientPrivilegesConsult()
			{
				$privs = $this->PrivilegesConsult ;
				if(count($this->SystemeParent->PrivilegesConsult))
				{
					array_splice($privs, 0, 0, $this->SystemeParent->PrivilegesConsult) ;
				}
				return $privs ;
			}
			public function ObtientPrivilegesEdit()
			{
				$privs = $this->PrivilegesConsult ;
				if(count($this->SystemeParent->PrivilegesEdit))
				{
					array_splice($privs, 0, 0, $this->SystemeParent->PrivilegesEdit) ;
				}
				return $privs ;
			}
			public function DefinitSysteme($nom, & $systeme)
			{
				$this->NomElementSyst = $nom ;
				$this->SystemeParent = & $systeme ;
			}
			public function & ObtientBDSupport()
			{
				$bd = & $this->SystemeParent->BDSupport ;
				return $bd ;
			}
			public function EstDefini()
			{
				return $this->EstIndefini == 0 ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeEntites() ;
			}
			protected function ChargeEntites()
			{
			}
			public function RemplitMenu(& $menu, $inclureEntites=0)
			{
				if($this->EstAccessible() == 0)
					return ;
				$this->MenuRacine = $menu->InscritSousMenuUrl($this->ObtientTitreMenu(), $this->ObtientUrlAdmin()) ;
				$this->MenuRacine->CheminMiniature = $this->ObtientCheminIcone() ;
				// echo "Chemin : ".$this->MenuRacine->CheminIcone."<br />" ;
				$this->MenuRacine->Url = $this->ObtientUrlAdmin() ;
				$this->RemplitMenuInt($this->MenuRacine) ;
				if($inclureEntites)
				{
					$this->RemplitSousMenus($this->MenuRacine) ;
				}
			}
			public function RemplitSousMenus(& $menu)
			{
				foreach($this->Entites as $nom => & $entite)
				{
					$entite->RemplitMenu($menu) ;
				}
			}
			protected function RemplitMenuInt(& $menu)
			{
			}
			public function RemplitApplication(& $app)
			{
				if(! $this->RemplApplicationPossible || ! $this->EstAccessible())
				{
					return ;
				}
				foreach($this->Entites as $nom => & $entite)
				{
					$entite->RemplitApplication($app) ;
				}
				$this->RemplitApplicationValide($app) ;
			}
			protected function RemplitApplicationValide(& $app)
			{
			}
			public function RemplitZonePubl(& $zone)
			{
				if(! $this->RemplZonePublPossible || ! $this->EstAccessible())
				{
					return ;
				}
				foreach($this->Entites as $nom => & $entite)
				{
					$entite->RemplitZonePubl($zone) ;
				}
				$this->RemplitZonePublValide($zone) ;
				$this->RemplitFluxRSS($zone) ;
			}
			protected function RemplitZonePublValide(& $zone)
			{
			}
			public function RemplitZonePublique(& $zone)
			{
				$this->RemplitZonePubl($zone) ;
			}
			public function RemplitZoneAdministration(& $zone)
			{
				$this->RemplitZoneAdmin($zone) ;
			}
			protected function RemplitZoneAdminValide(& $zone)
			{
			}
			public function RemplitZoneAdmin(& $zone)
			{
				if(! $this->RemplZoneAdminPossible || ! $this->EstAccessible())
				{
					return ;
				}
				foreach($this->Entites as $nom => & $entite)
				{
					$entite->RemplitZoneAdmin($zone) ;
				}
				$this->RemplitZoneAdminValide($zone) ;
			}
			protected function RemplitZoneMembreValide(& $zone)
			{
			}
			public function RemplitZoneMembre(& $zone)
			{
				if(! $this->RemplZoneMembrePossible || ! $this->EstAccessible())
				{
					return ;
				}
				foreach($this->Entites as $nom => & $entite)
				{
					$entite->RemplitZoneMembre($zone) ;
				}
				$this->RemplitZoneMembreValide($zone) ;
			}
			public function & InsereEntite($nom, $entite)
			{
				$this->InscritEntite($nom, $entite) ;
				return $entite ;
			}
			public function InscritEntites($entites=array())
			{
				foreach($entites as $nom => $entite)
				{
					$this->InscritNouvEntite($nom, $entite) ;
				}
			}
			public function InscritNouvEntite($nom, $entite)
			{
				$this->InscritEntite($nom, $entite) ;
			}
			public function InscritEntite($nom, & $entite)
			{
				$this->Entites[$nom] = & $entite ;
				$entite->DefinitModuleParent($nom, $this) ;
			}
			public function InsereAction($nomAction, $action, & $zone)
			{
				$action->NomModulePage = $this->NomElementSyst ;
				$zone->InscritActionAvantRendu($nomAction, $action) ;
				return $action ;
			}
			public function InsereActionAvantRendu($nomAction, $action, & $zone)
			{
				return $this->InsereAction($nomAction, $action, $zone) ;
			}
			public function InsereActionApresRendu($nomAction, $action, & $zone)
			{
				$action->NomModulePage = $this->NomElementSyst ;
				$zone->InscritActionApresRendu($nomAction, $action) ;
				return $action ;
			}
			public function & InsereScript($nom, $script, & $zone, $privs=array())
			{
				$this->InscritScript($nom, $script, $zone, $privs) ;
				return $script;
			}
			public function & InscritNouvScript($nom, $script, & $zone, $privs=array())
			{
				$this->InscritScript($nom, $script, $zone, $privs) ;
				return $script ;
			}
			public function & InscritScript($nom, & $script, & $zone, $privs=array())
			{
				$script->NomModulePage = $this->NomElementSyst;
				if(count($privs) > 0)
				{
					$script->NecessiteMembreConnecte = 1 ;
					$script->Privileges = $privs ;
				}
				$zone->InscritScript($nom, $script);
				return $script ;
			}
			public function & InsereTacheWeb($nom, $tache, & $zone)
			{
				$this->InscritTacheWeb($nom, $tache, $zone) ;
				return $tache;
			}
			public function InscritNouvTacheWeb($nom, $tache, & $zone)
			{
				$this->InscritTacheWeb($nom, $tache, $zone) ;
				return $tache ;
			}
			public function & InscritTacheWeb($nom, & $tache, & $zone)
			{
				$tache->NomModulePage = $this->NomElementSyst;
				$zone->InscritTacheWeb($nom, $tache);
				return $tache ;
			}
			public function RemplitMenuPlanSite(& $menu)
			{
				foreach($this->Entites as $nomEntite => $entite)
				{
					$entite->RemplitMenuPlanSite($menu) ;
				}
			}
		}
		class TacheWebBaseSws extends PvTacheWebBaseSimple
		{
			public $NomEntitePage ;
			public $NomImplemPage ;
			public $NomModulePage ;
			public function CreeFournDonnees()
			{
				return ReferentielSws::$SystemeEnCours->CreeFournDonnees() ;
			}
			public function ObtientBDSupport()
			{
				return ReferentielSws::$SystemeEnCours->BDSupport ;
			}
			public function & ObtientSystemeSws()
			{
				return ReferentielSws::$SystemeEnCours ;
			}
			public function & ObtientModulePage()
			{
				$modulePage = new ModulePageIndefiniSws();
				if($this->NomModulePage != '')
				{
					$modulePage = ReferentielSws::$SystemeEnCours->ObtientModulePageParNom($this->NomModulePage) ;
				}
				return $modulePage ;
			}
			public function & ObtientEntitePage()
			{
				$entitePage = new EntitePageIndefSws() ;
				$modulePage = $this->ObtientModulePage() ;
				if($modulePage->EstDefini() && $this->NomEntitePage != '' && isset($modulePage->Entites[$this->NomEntitePage]))
				{
					$entitePage = & $modulePage->Entites[$this->NomEntitePage] ;
				}
				return $entitePage ;
			}
			public function & ObtientImplemPage()
			{
				$implPage = new ImplemPageIndefSws() ;
				if($this->NomImplemPage != '')
				{
					$implPage = ReferentielSws::$SystemeEnCours->ObtientImplemPageParNom($this->NomImplemPage) ;
				}
				return $implPage ;
			}
		}
		
		class ModulePageIndefiniSws extends ModulePageBaseSws
		{
			public $NomRef = "indefini" ;
			public $EstIndefini = 1 ;
		}
		
		class DefFluxRSSElemRenduSws
		{
			public $Active = 0 ;
			public $NomTable = "" ;
			public $NomColId = "id" ;
			public $NomColTitre = "titre" ;
			public $ExprColTitre = "" ;
			public $NomColDescription = "description" ;
			public $ExprColDescription = "" ;
			public $NomColCheminImage = "chemin_image" ;
			public $ExprColCheminImage = "" ;
			public $NomColDatePubl = "date_publication" ;
			public $NomColHeurePubl = "heure_publication" ;
			public $NomColStatutPubl = "statut_publication" ;
			public $NomColCheminVideo = "" ;
			public $ExprColCheminVideo = "" ;
			public $NomColCheminFichier = "" ;
			public $ExprColCheminFichier = "" ;
			public $ValeurColNatureRendu = "base" ;
			public $ValeurColGroupeRendu ;
			public $ValeurColElemRendu ;
			public function SqlListeCols(& $elemRendu, & $bd)
			{
				$sql = '' ;
				$sql .= "'".$this->ValeurColNatureRendu."' nature_rendu, " ;
				$sql .= "'".$this->ValeurColGroupeRendu."' groupe_rendu, " ;
				$sql .= "'".$this->ValeurColElemRendu."' elem_rendu, " ;
				$sql .= (($this->NomColId != "") ? $bd->EscapeVariableName($this->NomColId) : "''").' id, ' ;
				if($this->ExprColId == '')
				{
					$sql .= (($this->NomColTitre != "") ? $bd->EscapeVariableName($this->NomColTitre) : "''").' titre, ' ;
				}
				else
				{
					$sql .= $this->ExprColTitre.' titre, ' ;
				}
				if($this->ExprColDescription == '')
				{
					$sql .= (($this->NomColDescription != "") ? $bd->EscapeVariableName($this->NomColDescription) : "''").' description, ' ;
				}
				else
				{
					$sql .= $this->ExprColDescription.' description, ' ;
				}
				if($this->ExprColCheminImage == '')
				{
					$sql .= (($this->NomColCheminImage != "") ? $bd->EscapeVariableName($this->NomColCheminImage) : "''").' chemin_image, ' ;
				}
				else
				{
					$sql .= $this->ExprColCheminImage.' chemin_image, ' ;
				}
				if($this->ExprColCheminVideo == '')
				{
					$sql .= (($this->NomColCheminVideo != "") ? $bd->EscapeVariableName($this->NomColCheminVideo) : "''").' chemin_video, ' ;
				}
				else
				{
					$sql .= $this->ExprColCheminVideo.' chemin_video, ' ;
				}
				if($this->ExprColCheminFichier == '')
				{
					$sql .= (($this->NomColCheminFichier != "") ? $bd->EscapeVariableName($this->NomColCheminFichier) : "''").' chemin_fichier, ' ;
				}
				else
				{
					$sql .= $this->ExprColCheminFichier.' chemin_fichier, ' ;
				}
				$sql .= (($this->NomColDatePubl != "") ? $bd->EscapeVariableName($this->NomColDatePubl) : "''").' date_publication, ' ;
				$sql .= (($this->NomColHeurePubl != "") ? $bd->EscapeVariableName($this->NomColHeurePubl) : "''").' heure_publication, ' ;
				$sql .= (($this->NomColStatutPubl != "") ? $bd->EscapeVariableName($this->NomColStatutPubl) : "''").' statut_publication, ' ;
				$sql .= "'' url" ;
				return $sql ;
			}
		}
		class DefRechElemRenduSws
		{
			public $Active = 0 ;
			public $NomTable = "" ;
			public $NomColsSurlign = array() ;
			public $NomColId = "id" ;
			public $NomColTitre = "titre" ;
			public $NomColDescription = "description" ;
			public $NomColMotsClesMeta = "" ;
			public $NomColDescriptionMeta = "" ;
			public $NomColsExtra = array() ;
			public $FormatUrl = "" ;
			public $ValeurColNatureRendu = "base" ;
			public $ValeurColGroupeRendu ;
			public $ValeurColElemRendu ;
			public $NomColStatutPubl = "statut_publication" ;
			public $NomColDatePubl = "" ;
			public $NomColHeurePubl = "" ;
			public function SqlListeCols(& $elemRendu, & $bd)
			{
				$sql = '' ;
				$sql .= "'".$this->ValeurColNatureRendu."' nature_rendu, " ;
				$sql .= "'".$this->ValeurColGroupeRendu."' groupe_rendu, " ;
				$sql .= "'".$this->ValeurColElemRendu."' elem_rendu, " ;
				$sql .= (($this->NomColId != "") ? $bd->EscapeVariableName($this->NomColId) : "''").' id, ' ;
				if($this->ExprColId == '')
				{
					$sql .= (($this->NomColTitre != "") ? $bd->EscapeVariableName($this->NomColTitre) : "''").' titre, ' ;
				}
				else
				{
					$sql .= $this->ExprColTitre.' titre, ' ;
				}
				$nomColsDesc = array() ;
				if($this->ExprColDescription != '')
				{
					$nomColsDesc[] = $this->ExprColDescription ;
				}
				elseif($this->NomColDescription != "")
				{
					$nomColsDesc[] = $this->NomColDescription ;
				}
				if(count($this->NomColsSurlign) > 0)
				{
					array_splice($nomColsDesc, count($this->NomColsSurlign), 0, $this->NomColsSurlign) ;
				}
				if(count($nomColsDesc) > 0)
				{
					$sql .= $bd->SqlConcat($nomColsDesc)." description, " ;
				}
				else
				{
					$sql .= "'' description, " ;
				}
				$sql .= (($this->NomColDatePubl != "") ? $bd->EscapeVariableName($this->NomColDatePubl) : "''").' date_publication, ' ;
				$sql .= (($this->NomColHeurePubl != "") ? $bd->EscapeVariableName($this->NomColHeurePubl) : "''").' heure_publication, ' ;
				$sql .= (($this->NomColStatutPubl != "") ? $bd->EscapeVariableName($this->NomColStatutPubl) : "''").' statut_publication, ' ;
				$sql .= "'' url" ;
				return $sql ;
			}
			public function SqlCond(& $elemRendu, & $bd, & $motsRech)
			{
				$cond = "" ;
				$nomCols = array() ;
				if($this->NomColTitre != '')
				{
					$nomCols[] = $this->NomColTitre ;
				}
				if($this->NomColDescription != '')
				{
					$nomCols[] = $this->NomColDescription ;
				}
				if($this->NomColMotsClesMeta != '')
				{
					$nomCols[] = $this->NomColMotsClesMeta ;
				}
				if($this->NomColDescriptionMeta != '')
				{
					$nomCols[] = $this->NomColDescriptionMeta ;
				}
				array_splice($nomCols, count($nomCols), 0, $this->NomColsSurlign) ;
				$symbolesPonct = array(" ", "\t", "\r", "\n", ";", ",", "?", "/", "!", '$', "*") ;
				$chainePonct = "'".join("', '", $symbolesPonct)."'" ;
				foreach($nomCols as $i => $nomCol)
				{
					if($i > 0)
					{
						$cond .= " or " ;
					}
					$cond .= "(" ;
					foreach($motsRech as $j => $motRech)
					{
						if($j > 0)
						{
							$cond .= " or " ;
						}
						$cond .= $bd->SqlIndexOf('upper('.$bd->EscapeVariableName($nomCol).')', 'upper('.$bd->ParamPrefix."motCle".$j.')').' > 0' ;
					}
					$cond .= ")" ;
				}
				return $cond ;
			}
		}
		
		class EntitePageBaseSws extends ElementRenduBaseSws
		{
			public $PrefixeTitreMenu = "Entit&eacute;" ;
			public $EstIndefinie = 1 ;
			public $NomElementModule ;
			public $PresentDansMenu = 1 ;
			public $RemplZoneMembreAdmin = 1 ;
			public $ModuleParent ;
			public $BarreMenu ;
			public $BarreElemsRendu ;
			public $MenuRacine ;
			public $AdaptFltsEdition ;
			public $NomScriptsInscrits = array() ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->InitAdaptFltsEdition() ;
			}
			protected function InitAdaptFltsEdition()
			{
				$this->AdaptFltsEdition = new AdaptFltsInactifEntiteSws() ;
			}
			public function ObtientReqSqlFluxRSS()
			{
				$this->DefFluxRSS->ValeurColGroupeRendu = $this->ModuleParent->NomElementSyst ;
				$this->DefFluxRSS->ValeurColElemRendu = $this->NomElementModule ;
				$this->DefFluxRSS->ValeurColNatureRendu = "entite" ;
				return parent::ObtientReqSqlFluxRSS() ;
			}
			public function ObtientReqSqlRech($motsRech)
			{
				$this->DefRech->ValeurColGroupeRendu = $this->ModuleParent->NomElementSyst ;
				$this->DefRech->ValeurColElemRendu = $this->NomElementModule ;
				$this->DefRech->ValeurColNatureRendu = "entite" ;
				return parent::ObtientReqSqlRech($motsRech) ;
			}
			public function ObtientTitreMenu()
			{
				return ($this->TitreMenu == "") ? ($this->Titre == "") ? $this->PrefixeTitreMenu." ".$this->NomElementModule : $this->Titre : $this->TitreMenu ;
			}
			public function CreeFournDonnees()
			{
				return $this->ModuleParent->CreeFournDonnees() ;
			}
			public function ObtientNomFichier()
			{
				return $this->NomElementModule ;
			}
			protected function CreeBarreMenuModules()
			{
				return ReferentielSws::$SystemeEnCours->CreeBarreMenuModulesPage() ;
			}
			protected function & InsereBarreMenu(& $script)
			{
				$barreMenu = $this->CreeBarreMenuModules($script) ;
				$barreMenu->AdopteScript("barreMenu", $script) ;
				$barreMenu->ChargeConfig() ;
				$this->ChargeBarreMenu($barreMenu) ;
				return $barreMenu ;
			}
			protected function ChargeBarreMenu(& $barreMenu)
			{
				$barreMenu->InclureRenduIcone = 0 ;
				/*
				$this->MenuAccueil = $barreMenu->MenuRacine->InscritSousMenuScript($barreMenu->ZoneParent->NomScriptParDefaut) ;
				$this->MenuAccueil->Titre = "Accueil" ;
				*/
				foreach($this->ModuleParent->SystemeParent->ModulesPage as $nom => & $module)
				{
					$module->RemplitMenu($barreMenu->MenuRacine) ;
				}
			}
			protected function InscritBarreMenu(& $script)
			{
				$this->BarreMenu = $this->InsereBarreMenu($script) ;
			}
			protected function CreeBarreElemsRendu()
			{
				return ReferentielSws::$SystemeEnCours->CreeBarreElemsRendu() ;
			}
			protected function & InsereBarreElemsRendu(& $script)
			{
				$barreMenu = $this->CreeBarreElemsRendu($script) ;
				$barreMenu->AdopteScript("barreElemsRendu", $script) ;
				$barreMenu->ChargeConfig() ;
				$this->ChargeBarreElemsRendu($barreMenu) ;
				return $barreMenu ;
			}
			protected function InscritBarreElemsRendu(& $script)
			{
				$this->BarreElemsRendu = $this->InsereBarreElemsRendu($script) ;
			}
			public function RemplitMenu(& $menu)
			{
				if($this->PresentDansMenu == 0)
				{
					return ;
				}
				$this->MenuRacine = $menu->InscritSousMenuUrl($this->ObtientTitreMenu(), $this->ObtientUrlAdmin()) ;
				$this->RemplitMenuInt($this->MenuRacine) ;
			}
			protected function RemplitMenuInt(& $menu)
			{
			}
			public function EstDefinie()
			{
				return $this->EstIndefinie == 0 ;
			}
			public function DefinitModuleParent($nom, & $moduleParent)
			{
				$this->NomElementModule = $nom ;
				$this->ModuleParent = & $moduleParent ;
			}
			public function RemplitApplication(& $app)
			{
			}
			public function RemplitZonePubl(& $zone)
			{
			}
			public function RemplitZoneAdmin(& $zone)
			{
			}
			public function RemplitZoneMembre(& $zone)
			{
				if($this->RemplZoneMembreAdmin)
				{
					$this->RemplitZoneAdmin($zone) ;
				}
			}
			protected function InscritEntite($nom, & $entite)
			{
				$this->Entites[$nom] = & $entite ;
				$entite->DefinitModuleParent($nom, $this) ;
			}
			protected function InsereScript($nom, $script, & $zone, $privs=array())
			{
				$this->InscritScript($nom, $script, $zone, $privs) ;
				return $script;
			}
			protected function InscritNouvScript($nom, $script, & $zone, $privs=array())
			{
				$this->InscritScript($nom, $script, $zone, $privs) ;
			}
			protected function InscritScript($nom, & $script, & $zone, $privs=array())
			{
				$script->NomEntitePage = $this->NomElementModule ;
				$this->NomScriptsInscrits[] = $nom ;
				$this->ModuleParent->InscritScript($nom, $script, $zone, $privs);
			}
			protected function & ScriptsInscrits($zone)
			{
				$scripts = array() ;
				foreach($this->NomScriptsInscrits as $i => $nomScript)
				{
					if(isset($zone->Scripts[$nomScript]))
					{
						$scripts[$nomScript] = & $zone->Scripts[$nomScript] ;
					}
				}
				return $scripts ;
			}
			protected function InsereTacheWeb($nom, $tache, & $zone)
			{
				$this->InscritTacheWeb($nom, $tache, $zone) ;
				return $tache;
			}
			protected function InscritNouvTacheWeb($nom, $tache, & $zone)
			{
				$this->InscritTacheWeb($nom, $tache, $zone) ;
			}
			protected function InscritTacheWeb($nom, & $tache, & $zone)
			{
				$tache->NomEntitePage = $this->NomElementModule ;
				$this->ModuleParent->InscritTacheWeb($nom, $tache, $zone);
			}
			public function & ObtientBDSupport()
			{
				$bd = null ;
				if($this->EstNul($this->ModuleParent))
				{
					return $bd ;
				}
				$bd = $this->ModuleParent->ObtientBDSupport() ;
				return $bd ;
			}
			public function & ObtientImplemsPage()
			{
				$nomImplemsPage = array_keys($this->ModuleParent->SystemeParent->ImplemsPage) ;
				$implemsPage = array() ;
				foreach($nomImplemsPage as $i => $nom)
				{
					$implemPage = & $this->ModuleParent->SystemeParent->ImplemsPage[$nom] ;
					if($implemPage->SupporteEntite($this))
					{
						$implemsPage[$nom] = & $implemPage ;
					}
				}
				return $implemsPage ;
			}
			public function AppliqueImplemsAvantCmd($nomAction, & $cmd)
			{
				$implems = $this->ObtientImplemsPage() ;
				foreach($implems as $i => $implem)
				{
					$implems[$i]->AppliqueAvantCmdEntite($nomAction, $cmd, $this) ;
				}
			}
			public function AppliqueImplemsApresCmd($nomAction, & $cmd)
			{
				$implems = $this->ObtientImplemsPage() ;
				foreach($implems as $i => $implem)
				{
					$implems[$i]->AppliqueApresCmdEntite($nomAction, $cmd, $this) ;
				}
			}
		}
		class EntitePageIndefinieSws extends EntitePageBaseSws
		{
			public $EstIndefinie = 1 ;
		}
		class EntitePageIndefSws extends EntitePageIndefinieSws
		{
		}
		
		class ColSommaireEntiteSws
		{
			public $NomCol ;
			public $Titre ;
			public $AliasDonnees ;
			public function __construct($nomCol, $titre, $aliasDonnees='')
			{
				$this->NomCol = $nomCol ;
				$this->Titre = $titre ;
				$this->AliasDonnees = $aliasDonnees ;
			}
		}
		class EntiteTableSws extends EntitePageBaseSws
		{
			public $NomEntite = "entite" ;
			public $LibEntite = "entite" ;
			public $NomTable = "entite" ;
			public $NomColId = "id" ;
			public $AutoFixeAttrsMetaVide = 1 ;
			public $TitreAjoutEntite = "Ajout entit&eacute;" ;
			public $TitreModifEntite = "Modification entit&eacute;" ;
			public $TitreSupprEntite = "Suppression entit&eacute;" ;
			public $TitreVideEntite = "Vidage des entit&eacute;s" ;
			public $TitreListageEntite = "Liste des entit&eacute;s" ;
			public $TitreConsultEntite = "D&eacute;tails entit&eacute;" ;
			public $TitreEnumEntite = "Entit&eacute;s" ;
			public $TitreDocAjoutEntite = "" ;
			public $TitreDocModifEntite = "" ;
			public $TitreDocSupprEntite = "" ;
			public $TitreDocListageEntite = "" ;
			public $TitreDocConsultEntite = "" ;
			public $TitreDocEnumEntite = "" ;
			public $NomColIdCtrl = "id_ctrl" ;
			public $AccepterAttrsPubl = 1 ;
			public $TrierParPubl = 1 ;
			public $LibActions = "Actions" ;
			public $LibAjoutTblList = "Ajouter" ;
			public $LibModifTblList = "Modifier" ;
			public $LibSupprTblList = "Supprimer" ;
			public $ChemIconAjoutTblList = "images/icones/ajout.png" ;
			public $ChemIconModifTblList = "images/icones/modif.png" ;
			public $ChemIconSupprTblList = "images/icones/suppr.png" ;
			public $ChemIconVidageTblList = "images/icones/vide.png" ;
			public $ChemIconConsultTblList = "images/icones/consult.png" ;
			public $ChemIconChgPublOkTblList = "images/icones/publier_ok.png" ;
			public $ChemIconChgPublKoTblList = "images/icones/publier_ko.png" ;
			public $ChemIconFichsJointsTblList = "images/icones/fichiers_joints.png" ;
			public $ClsCSSLienTblList = "ui-widget" ;
			public $LibConsultTblList = "Consulter" ;
			public $LibChgPublOkTblList = "Publier" ;
			public $LibChgPublKoTblList = "Rejeter" ;
			public $LibFichsJointsTblList = "Fichiers joints" ;
			public $LibId = "ID" ;
			public $LibIdMembreModif = "Modifi&eacute; par" ;
			public $LibDateModif = "Date modif." ;
			public $LibDatePubl = "Date publication" ;
			public $LibHeurePubl = "Heure publication" ;
			public $LibStatutPubl = "Statut publication" ;
			public $LibTitre = "Titre" ;
			public $NomColDatePubl = "date_publication" ;
			public $NomColHeurePubl = "heure_publication" ;
			public $NomColStatutPubl = "statut_publication" ;
			public $NomColTitre = "titre" ;
			public $AccepterAttrsEdition = 1 ;
			public $AccepterTitre = 0 ;
			public $NomColDateCreation = "date_creation" ;
			public $NomColDateModif = "date_modif" ;
			public $NomColIdMembreCreation = "id_membre_creation" ;
			public $NomColIdMembreModif = "id_membre_modif" ;
			public $InclureScriptEdit = 1 ;
			public $InclureScriptConsult = 1 ;
			public $InclureScriptEnum = 0 ;
			public $InclureScriptLst = 1 ;
			public $InclureScriptSuppr = 1 ;
			public $NomScriptConsult = "consult" ;
			public $ScriptConsult ;
			public $NomScriptListage = "liste" ;
			public $ScriptListage ;
			public $NomScriptAjout = "ajout" ;
			public $ScriptAjout ;
			public $NomScriptModif = "modif" ;
			public $ScriptModif ;
			public $NomScriptPositionPubl = "position_publ" ;
			public $ScriptPositionPubl ;
			public $NomScriptEnum = "enum" ;
			public $ScriptEnum ;
			public $NomScriptSuppr = "suppr" ;
			public $ScriptSuppr ;
			public $NomScriptVidage = "vide" ;
			public $ScriptVidage ;
			public $ScriptChgPubl ;
			public $PrivilegesConsult = array() ;
			public $PrivilegesEdit = array() ;
			public $NomParamId = "id" ;
			public $NomParamStatutPubl = "statut_publication" ;
			public $NomParamDatePubl = "date_publication" ;
			public $NomParamHeurePubl = "heure_publication" ;
			public $NomParamTitre = "titre" ;
			public $NomParamCaptcha = "code_securite" ;
			public $FltFrmElemId ;
			public $FltFrmElemIdCtrl ;
			public $FltFrmElemDatePubl ;
			public $FltFrmElemHeurePubl ;
			public $FltFrmElemStatutPubl ;
			public $FltFrmElemDateCreation ;
			public $FltFrmElemIdMembreCreation ;
			public $FltFrmElemDateModif ;
			public $FltFrmElemIdMembreModif ;
			public $FltFrmElemTitre ;
			public $SecuriserEdition = 0 ;
			public $FltCaptcha ;
			public $FltLstDatePublMin ;
			public $FltLstDatePublMax ;
			public $NomClasseCmdAjout = "CmdAjoutEntiteSws" ;
			public $LibelleCmdAjout = "Ajouter" ;
			public $NomClasseCmdModif = "CmdModifEntiteSws" ;
			public $LibelleCmdModif = "Modifier" ;
			public $NomClasseCmdPositionPubl = "" ;
			public $NomClasseCmdSuppr = "CmdSupprEntiteSws" ;
			public $LibelleCmdSuppr = "Supprimer" ;
			public $LibelleCmdVidage = "Vider" ;
			public $FrmElem ;
			public $ValidScriptEdit = 0 ;
			public $ValidScriptEnum = 0 ;
			public $ValidScriptConsult = 0 ;
			public $ValidScriptLst = 0 ;
			public $RedirScriptEditIndisp = 1 ;
			public $MsgScriptEditIndisp = "L'entite n'est pas disponible" ;
			public $RedirTblListIndisp = 1 ;
			public $MsgTblListIndisp = "La liste des entites n'est pas disponible" ;
			public $RedirScriptConsultIndisp = 1 ;
			public $MsgScriptConsultIndisp = "La liste des entites n'est pas disponible" ;
			public $MsgAlerteVideScript = "Attention, vous vous appr&ecirc;tez &agrave; tout vider. Confirmez en cliquant sur ce bouton." ;
			public $TblList ;
			public $FltTblListId ;
			public $FltTblListStatutPubl ;
			public $FltTblListDatePublMin ;
			public $FltTblListDatePublMax ;
			public $FltTblListTitre ;
			public $NomParamTblListId = "pId" ;
			public $NomParamTblListDatePublMin = "pDateMin" ;
			public $NomParamTblListDatePublMax = "pDateMax" ;
			public $NomParamTblListTitre = "pTitre" ;
			public $LibTblListId = "ID" ;
			public $LibTblListDatePublMin = "Date min" ;
			public $LibTblListDatePublMax = "Date max" ;
			public $DefColTblListId ;
			public $DefColTblListDatePubl ;
			public $DefColTblListDateModif ;
			public $DefColTblListIdMembreModif ;
			public $DefColTblListStatutPubl ;
			public $DefColTblListTitre ;
			public $DefColTblListActs ;
			public $CmdAjoutTblList ;
			public $LienModifTblList ;
			public $LienSupprTblList ;
			public $LienConsultTblList ;
			public $BlocConsult ;
			public $BlocEnum ;
			public $SousMenuListage ;
			public $SousMenuAjout ;
			public $ValeurParamId = 0 ;
			public $LibSousMenuListage = "Lister" ;
			public $LibSousMenuAjout = "Ajouter" ;
			public $FilArianeScript ;
			public $BarreMenu ;
			public $BarreMenuEntite ;
			public $LgnEnCours = array() ;
			public $InclureFltsTblList = 1 ;
			public $InclureFltsPrdPubl = 0 ;
			public $LargeurFenEditEntite = 750 ;
			public $HauteurFenEditEntite = 525 ;
			public $ActiverFenEditEntite = 0 ;
			protected $SommaireElem ;
			protected $MenuPlanSite ;
			protected $InclureScriptEnumPlanSite = 0 ;
			protected $InclureScriptConsultPlanSite = 0 ;
			protected $NomColIdConsultPlanSite = "id" ;
			protected $NomColTitreConsultPlanSite = "id" ;
			public function ObtientCheminPubl($chemin)
			{
				return $this->ModuleParent->SystemeParent->ObtientCheminPubl($chemin) ;
			}
			public function ObtientUrlPubl()
			{
				$url = parent::ObtientUrlPubl() ;
				if($url != '')
				{
					return $url ;
				}
				if($this->InclureScriptConsult)
				{
					$url = $this->ScriptConsult->ObtientUrl() ;
				}
				return $url ;
			}
			public function ObtientUrlAdmin()
			{
				$url = parent::ObtientUrlAdmin() ;
				if($url != '' && $url != 'javascript:;')
				{
					return $url ;
				}
				if($this->InclureScriptLst && $this->EstPasNul($this->ScriptListage))
				{
					$url = $this->ScriptListage->ObtientUrl() ;
				}
				return $url ;
			}
			protected function DetecteLgnEnCours()
			{
				$this->ValeurParamId = intval((isset($_GET[$this->NomParamId])) ? $_GET[$this->NomParamId] : 0) ;
				$this->LgnEnCours = $this->SelectLgn($this->ValeurParamId) ;
				return (is_array($this->LgnEnCours) && count($this->LgnEnCours)) ;
			}
			protected function VerifPreReqsScriptConsult(& $script)
			{
				$ok = $this->DetecteLgnEnCours() ;
				if($ok && $this->LgnEnCours["statut_publication"] != 1)
					$ok = 0 ;
				return $ok ;
			}
			public function ObtientPrivilegesConsult()
			{
				$privsModule = $this->ModuleParent->ObtientPrivilegesConsult() ;
				$privs = $this->PrivilegesConsult ;
				if(count($privsModule) > 0)
				{
					array_splice($privs, 0, 0, $privsModule) ;
				}
				return $privs ;
			}
			public function ObtientPrivilegesEdit()
			{
				$privsModule = $this->ModuleParent->ObtientPrivilegesEdit() ;
				$privs = $this->PrivilegesEdit ;
				if(count($privsModule) > 0)
				{
					array_splice($privs, 0, 0, $privsModule) ;
				}
				return $privs ;
			}
			protected function RemplitMenuInt(& $menu)
			{
				if($this->InclureScriptLst)
				{
					$this->SousMenuListage = $menu->InscritSousMenuScript($this->NomScriptListage."_".$this->NomEntite) ;
					$this->SousMenuListage->Titre = $this->LibSousMenuListage ;
				}
				if($this->InclureScriptEdit)
				{
					$this->SousMenuAjout = $menu->InscritSousMenuScript($this->NomScriptAjout."_".$this->NomEntite) ;
					$this->SousMenuAjout->Titre = $this->LibSousMenuAjout ;
				}
			}
			protected function ObtientTitreAjoutEntite()
			{
				return $this->TitreAjoutEntite ;
			}
			protected function ObtientTitreModifEntite()
			{
				return $this->TitreModifEntite ;
			}
			protected function ObtientTitreSupprEntite()
			{
				return $this->TitreSupprEntite ;
			}
			protected function ObtientTitreListageEntite()
			{
				return $this->TitreListageEntite ;
			}
			protected function ObtientTitreConsultEntite()
			{
				return $this->TitreConsultEntite ;
			}
			protected function ObtientTitreEnumEntite()
			{
				return $this->TitreEnumEntite ;
			}
			protected function ObtientTitreDocAjoutEntite()
			{
				return ($this->TitreDocAjoutEntite == '') ? $this->TitreAjoutEntite : $this->TitreDocAjoutEntite ;
			}
			protected function ObtientTitreDocModifEntite()
			{
				return ($this->TitreDocModifEntite == '') ? $this->TitreModifEntite : $this->TitreDocModifEntite ;
			}
			protected function ObtientTitreDocSupprEntite()
			{
				return ($this->TitreDocSupprEntite == '') ? $this->TitreSupprEntite : $this->TitreDocSupprEntite ;
			}
			protected function ObtientTitreDocListageEntite()
			{
				return ($this->TitreDocListageEntite == '') ? $this->TitreListageEntite : $this->TitreDocListageEntite ;
			}
			protected function ObtientTitreDocConsultEntite()
			{
				return ($this->TitreDocConsultEntite == '') ? $this->TitreConsultEntite : $this->TitreDocConsultEntite ;
			}
			protected function ObtientTitreDocEnumEntite()
			{
				return ($this->TitreDocEnumEntite == '') ? $this->TitreEnumEntite : $this->TitreDocEnumEntite ;
			}
			public function SqlListeColsSelect(& $bd)
			{
				$sql = '' ;
				$sql .= $bd->EscapeVariableName($this->NomColId).' id' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColId).' id' ;
				$sql .= ', '.$bd->EscapeVariableName($this->NomColIdCtrl).' id_ctrl' ;
				if($this->AccepterAttrsPubl)
				{
					$sql .= ', '.$bd->EscapeVariableName($this->NomColDatePubl).' date_publication' ;
					$sql .= ', '.$bd->EscapeVariableName($this->NomColHeurePubl).' heure_publication' ;
					$sql .= ', '.$bd->EscapeVariableName($this->NomColStatutPubl).' statut_publication' ;
				}
				if($this->AccepterAttrsEdition)
				{
					$sql .= ', '.$bd->EscapeVariableName($this->NomColIdMembreCreation).' id_membre_creation' ;
					$sql .= ', '.$bd->EscapeVariableName($this->NomColDateCreation).' date_creation' ;
					$sql .= ', '.$bd->EscapeVariableName($this->NomColIdMembreModif).' id_membre_modif' ;
					$sql .= ', '.$bd->EscapeVariableName($this->NomColDateModif).' date_modif' ;
				}
				if($this->AccepterTitre)
				{
					$sql .= ', '.$bd->EscapeVariableName($this->NomColTitre).' titre' ;
				}
				return $sql ;
			}
			protected function TableSelectLgn()
			{
				return $this->NomTable ;
			}
			protected function SqlSelectLgn()
			{
				$bd = $this->ObtientBDSupport() ;
				$sql = 'select ' ;
				$sql .= $this->SqlListeColsSelect($bd) ;
				$sql .= ' from' ;
				$sql .= $bd->EscapeVariableName($this->TableSelectLgn()).' t1' ;
				$sql .= ' where '.$bd->EscapeVariableName($this->NomColId).' = '.$bd->ParamPrefix.'idEntite' ;
				return $sql ;
			}
			public function SelectLgn($id)
			{
				$bd = $this->ObtientBDSupport() ;
				$lgn = $bd->FetchSqlRow($this->SqlSelectLgn(), array("idEntite" => $id)) ;
				return $lgn ;
			}
			public function VideLgns()
			{
				$bd = $this->ObtientBDSupport() ;
				$sql = 'truncate table '.$bd->EscapeTableName($this->NomTable) ;
				$ok = $bd->RunSql($sql) ;
				return $ok ;
			}
			protected function SqlSelectLgnCtrl()
			{
				$bd = $this->ObtientBDSupport() ;
				$sql = 'select ' ;
				$sql .= $this->SqlListeColsSelect($bd) ;
				$sql .= ' from' ;
				$sql .= $bd->EscapeVariableName($this->NomTable) ;
				$sql .= ' where '.$bd->EscapeVariableName($this->NomColIdCtrl).' = '.$bd->ParamPrefix.'idCtrlEntite' ;
				return $sql ;
			}
			public function SelectLgnCtrl($idCtrl)
			{
				$bd = $this->ObtientBDSupport() ;
				$lgn = $bd->FetchSqlRow($this->SqlSelectLgnCtrl(), array("idCtrlEntite" => $idCtrl)) ;
				return $lgn ;
			}
			protected function CreeScriptEnum()
			{
				return new ScriptEnumEntiteTableSws() ;
			}
			protected function CreeScriptConsult()
			{
				return new ScriptConsultEntiteTableSws() ;
			}
			protected function CreeScriptListage()
			{
				return new ScriptListageEntiteTableSws() ;
			}
			protected function CreeScriptAjout()
			{
				return new ScriptAjoutEntiteTableSws() ;
			}
			protected function CreeScriptModif()
			{
				return new ScriptModifEntiteTableSws() ;
			}
			protected function CreeScriptPositionPubl()
			{
				return new ScriptPositionPublEntiteTableSws() ;
			}
			protected function CreeScriptVidage()
			{
				return new ScriptVideEntiteTableSws() ;
			}
			protected function CreeScriptSuppr()
			{
				return new ScriptSupprEntiteTableSws() ;
			}
			protected function RemplitZonePublEdit(& $zone)
			{
				if($this->ModuleParent->SystemeParent->InclureAdminPubl)
				{
					$this->RemplitZoneAdmin($zone) ;
				}
			}
			public function RemplitZonePubl(& $zone)
			{
				$this->RemplitZonePublEdit($zone) ;
				if($this->InclureScriptConsult)
				{
					$this->ScriptConsult = $this->InsereScript($this->NomScriptConsult.'_'.$this->NomEntite, $this->CreeScriptConsult(), $zone, $this->ObtientPrivilegesConsult()) ;
				}
				if($this->InclureScriptEnum)
				{
					$this->ScriptEnum = $this->InsereScript($this->NomScriptEnum.'_'.$this->NomEntite, $this->CreeScriptEnum(), $zone, $this->ObtientPrivilegesConsult()) ;
				}
			}
			public function RemplitZoneAdmin(& $zone)
			{
				if($this->InclureScriptLst)
				{
					$this->ScriptListage = $this->InsereScript($this->NomScriptListage.'_'.$this->NomEntite, $this->CreeScriptListage(), $zone, $this->ObtientPrivilegesEdit()) ;
					if($this->InclureScriptSuppr == 1)
					{
						$this->ScriptVidage = $this->InsereScript($this->NomScriptVidage.'_'.$this->NomEntite, $this->CreeScriptVidage(), $zone, $this->ObtientPrivilegesEdit()) ;
					}
				}
				if($this->InclureScriptEdit)
				{
					$this->ScriptAjout = $this->InsereScript($this->NomScriptAjout.'_'.$this->NomEntite, $this->CreeScriptAjout(), $zone, $this->ObtientPrivilegesEdit()) ;
					$this->ScriptModif = $this->InsereScript($this->NomScriptModif.'_'.$this->NomEntite, $this->CreeScriptModif(), $zone, $this->ObtientPrivilegesEdit()) ;
					$this->ScriptPositionPubl = $this->InsereScript($this->NomScriptPositionPubl.'_'.$this->NomEntite, $this->CreeScriptPositionPubl(), $zone, $this->ObtientPrivilegesEdit()) ;
					if($this->InclureScriptSuppr == 1)
					{
						$this->ScriptSuppr = $this->InsereScript($this->NomScriptSuppr.'_'.$this->NomEntite, $this->CreeScriptSuppr(), $zone, $this->ObtientPrivilegesEdit()) ;
					}
				}
			}
			protected function & InsereFrmElem(& $script)
			{
				$frm = $this->CreeFrmElem() ;
				$this->InitFrmElem($frm, $script) ;
				$frm->AdopteScript('frmElem', $script) ;
				$frm->ChargeConfig() ;
				$this->ChargeFrmElem($frm) ;
				$this->AppliqueSecurFrmElem($frm) ;
				$this->FinalFrmElem($frm) ;
				return $frm ;
			}
			protected function & InsereBlocConsult(& $script)
			{
				$bloc = $this->CreeBlocConsult() ;
				$this->InitBlocConsult($bloc, $script) ;
				$bloc->AdopteScript('blocConsult', $script) ;
				$bloc->ChargeConfig() ;
				$this->ChargeBlocConsult($bloc) ;
				return $bloc ;
			}
			protected function & InsereBlocEnum(& $script)
			{
				$bloc = $this->CreeBlocEnum() ;
				$this->InitBlocEnum($bloc, $script) ;
				$bloc->AdopteScript('blocEnum', $script) ;
				$bloc->ChargeConfig() ;
				$this->ChargeBlocEnum($bloc) ;
				return $bloc ;
			}
			protected function & InsereTblList(& $script)
			{
				$tbl = $this->CreeTblList() ;
				$this->InitTblList($tbl, $script) ;
				$tbl->AdopteScript('tblList', $script) ;
				$tbl->ChargeConfig() ;
				$this->ChargeTblList($tbl) ;
				$this->DefColTblListActs = $tbl->InsereDefColActions($this->LibActions) ;
				$this->FinalTblList($tbl) ;
				return $tbl ;
			}
			protected function VerifPreReqsScriptEdit(& $script)
			{
				return 1 ;
			}
			protected function VerifPreReqsScriptLst(& $script)
			{
				return 1 ;
			}
			protected function VerifPreReqsScriptEnum(& $script)
			{
				return 1 ;
			}
			protected function InscritFilAriane(& $script)
			{
				$this->FilArianeScript = $this->ModuleParent->SystemeParent->CreeFilAriane() ;
			}
			protected function InscritBarreMenuEntite(& $script)
			{
				$this->BarreMenuEntite = $this->ModuleParent->SystemeParent->CreeBarreMenuEntitesPage() ;
				$this->BarreMenuEntite->AdopteScript("barreMenuEntite", $script) ;
				$this->BarreMenuEntite->ChargeConfig() ;
				// print get_class($this->BarreMenuEntite->MenuRacine) ;
				$this->ModuleParent->RemplitSousMenus($this->BarreMenuEntite->MenuRacine) ;
			}
			public function PrepareScriptAdmin(& $script)
			{
				if(! $script->UtiliserCorpsDocZone)
				{
					return ;
				}
				if(method_exists($script->ZoneParent, 'NiveauAdmin') && $script->ZoneParent->NiveauAdmin() == "admin")
				{
					$this->InscritBarreMenu($script) ;
					$this->InscritBarreElemsRendu($script) ;
					$this->InscritBarreMenuEntite($script) ;
				}
			}
			protected function PrepareScriptEdit(& $script)
			{
				if(method_exists($script->ZoneParent, 'NiveauAdmin') && $script->ZoneParent->NiveauAdmin() == "admin")
				{
					$this->PrepareScriptAdmin($script) ;
				}
				// $this->InscritFilAriane($script) ;
				if($script->EstListage() == 0)
				{
					$this->SommaireElem = $this->InsereSommaireElem($script) ;
				}
				switch($script->InitFrmElem->Role)
				{
					case "Ajout" :
					{
						$script->Titre = $this->ObtientTitreAjoutEntite() ;
						$script->TitreDocument = $this->ObtientTitreDocAjoutEntite() ;
					}
					break ;
					case "Modif" :
					{
						$script->Titre = $this->ObtientTitreModifEntite() ;
						$script->TitreDocument = $this->ObtientTitreDocModifEntite() ;
					}
					break ;
					case "Suppr" :
					{
						$script->Titre = $this->ObtientTitreSupprEntite() ;
						$script->TitreDocument = $this->ObtientTitreDocSupprEntite() ;
					}
					break ;
				}
			}
			protected function PrepareScriptEnum(& $script)
			{
				$script->TitreDocument = $this->ObtientTitreDocEnumEntite() ;
				$script->Titre = $this->ObtientTitreEnumEntite() ;
			}
			protected function PrepareScriptConsult(& $script)
			{
				$script->TitreDocument = $this->ObtientTitreDocConsultEntite() ;
				$script->Titre = $this->ObtientTitreConsultEntite() ;
				if($this->AccepterTitre == 1)
				{
					$script->TitreDocument = $this->LgnEnCours[$this->NomColTitre] ;
					$script->Titre = $this->LgnEnCours[$this->NomColTitre] ;
				}
			}
			protected function PrepareScriptLst(& $script)
			{
				$this->PrepareScriptAdmin($script) ;
				$script->TitreDocument = $this->ObtientTitreDocListageEntite() ;
				$script->Titre = $this->ObtientTitreListageEntite() ;
			}
			public function RemplitScriptEdit(& $script)
			{
				$this->ValidScriptEdit = 1 ;
				if($this->VerifPreReqsScriptEdit($script))
				{
					if($this->ActiverFenEditEntite)
					{
						$script->UtiliserCorpsDocZone = 0 ;
					}
					$this->PrepareScriptEdit($script) ;
					$this->FrmElem = $this->InsereFrmElem($script) ;
				}
				else
				{
					$this->NotifieScriptEditIndisp($script) ;
				}
			}
			public function RemplitScriptSommaire(& $script)
			{
				$this->ValidScriptEdit = 1 ;
				if($this->VerifPreReqsScriptEdit($script))
				{
					if(method_exists($script->ZoneParent, 'NiveauAdmin') && $script->ZoneParent->NiveauAdmin() == "admin")
					{
						$this->PrepareScriptAdmin($script) ;
					}
					// $this->InscritFilAriane($script) ;
					if($script->EstListage() == 0)
					{
						$this->SommaireElem = $this->InsereSommaireElem($script) ;
					}
					$this->SommaireElem = $this->InsereSommaireElem($script) ;
				}
				else
				{
					$this->NotifieScriptEditIndisp($script) ;
				}
			}
			public function RemplitScriptConsult(& $script)
			{
				$this->ValidScriptConsult = 1 ;
				if($this->VerifPreReqsScriptConsult($script))
				{
					$this->PrepareScriptConsult($script) ;
					$this->PrepareImplemsScriptConsult($script) ;
					$this->BlocConsult = $this->InsereBlocConsult($script) ;
				}
				else
				{
					$this->NotifieScriptConsultIndisp($script) ;
				}
			}
			public function RemplitScriptEnum(& $script)
			{
				$this->ValidScriptEnum = 1 ;
				if($this->VerifPreReqsScriptEnum($script))
				{
					$this->PrepareScriptEnum($script) ;
					$this->PrepareImplemsScriptEnum($script) ;
					$this->BlocEnum = $this->InsereBlocEnum($script) ;
				}
				else
				{
					$this->NotifieScriptEnumIndisp($script) ;
				}
			}
			public function RemplitScriptLst(& $script)
			{
				$this->ValidScriptList = 1 ;
				if($this->VerifPreReqsScriptLst($script))
				{
					$this->PrepareScriptLst($script) ;
					$this->TblList = $this->InsereTblList($script) ;
				}
				else
				{
					$this->NotifieScriptLstIndisp($script) ;
				}
			}
			public function RemplitScriptVue(& $script)
			{
				$this->ValidScriptList = 1 ;
				if($this->VerifPreReqsScriptLst($script))
				{
					$this->PrepareScriptAdmin($script) ;
				}
				else
				{
					$this->NotifieScriptLstIndisp($script) ;
				}
			}
			protected function PrepareImplemsScriptConsult(& $script)
			{
				$nomImplemsPage = array_keys($this->ModuleParent->SystemeParent->ImplemsPage) ;
				foreach($nomImplemsPage as $i => $nom)
				{
					$implemPage = & $this->ModuleParent->SystemeParent->ImplemsPage[$nom] ;
					if($implemPage->SupporteEntite($this))
					{
						$implemPage->PrepareScriptConsult($script, $this) ;
					}
				}
			}
			protected function PrepareImplemsScriptEnum(& $script)
			{
				$nomImplemsPage = array_keys($this->ModuleParent->SystemeParent->ImplemsPage) ;
				foreach($nomImplemsPage as $i => $nom)
				{
					$implemPage = & $this->ModuleParent->SystemeParent->ImplemsPage[$nom] ;
					if($implemPage->SupporteEntite($this))
					{
						$implemPage->PrepareScriptEnum($script, $this) ;
					}
				}
			}
			protected function NotifieScriptEditIndisp(& $script)
			{
				$this->ValidScriptEdit = 0 ;
				if($this->RedirScriptEditIndisp == 1)
				{
					$script->ZoneParent->RedirigeVersScript($script->ZoneParent->ObtientScriptParDefaut()) ;
				}
				else
				{
					$this->FrmElem = new PvPortionRenduHtml() ;
					$this->FrmElem->AdopteScript("frmElem", $this) ;
					$this->FrmElem->Contenu = $this->MsgScriptEditIndisp ;
				}
			}
			protected function NotifieScriptLstIndisp(& $script)
			{
				$this->ValidScriptLst = 0 ;
				if($this->RedirScriptLstIndisp == 1)
				{
					$script->ZoneParent->RedirigeVersScript($script->ZoneParent->ObtientScriptParDefaut()) ;
				}
				else
				{
					$this->TblList = new PvPortionRenduHtml() ;
					$this->TblList->AdopteScript("tblList", $this) ;
					$this->TblList->Contenu = $this->MsgScriptLstIndisp ;
				}
			}
			protected function NotifieScriptConsultIndisp(& $script)
			{
				$this->ValidScriptConsult = 0 ;
				if($this->RedirScriptConsultIndisp == 1)
				{
					$script->ZoneParent->RedirigeVersScript($script->ZoneParent->ObtientScriptParDefaut()) ;
				}
				else
				{
					$this->BlocConsult = new PvPortionRenduHtml() ;
					$this->BlocConsult->AdopteScript("blocConsult", $this) ;
					$this->BlocConsult->Contenu = $this->MsgScriptConsultIndisp ;
				}
			}
			protected function CreeFrmElem()
			{
				return new PvFormulaireDonneesHtml() ;
			}
			protected function InitFrmElem(& $frm, & $script)
			{
				$frm->MaxFiltresEditionParLigne = 1 ;
				if(isset($script->InitFrmElem))
				{
					$script->InitFrmElem->Applique($frm, $script) ;
					switch($script->InitFrmElem->Role)
					{
						case "Ajout" : { $frm->NomClasseCommandeExecuter = $this->NomClasseCmdAjout ; $frm->LibelleCommandeExecuter = $this->LibelleCmdAjout ; } break ;
						case "Modif" : { $frm->NomClasseCommandeExecuter = $this->NomClasseCmdModif ; $frm->LibelleCommandeExecuter = $this->LibelleCmdModif ; } break ;
						case "Suppr" : { $frm->NomClasseCommandeExecuter = $this->NomClasseCmdSuppr ; $frm->LibelleCommandeExecuter = $this->LibelleCmdSuppr ; } break ;
					}
				}
			}
			protected function ChargeFrmElem(& $frm)
			{
				$bd = $this->ObtientBDSupport() ;
				// Fournisseur de donn?es
				$frm->FournisseurDonnees = $this->ModuleParent->CreeFournDonnees() ;
				$frm->FournisseurDonnees->TableEdition = $this->NomTable ;
				$frm->FournisseurDonnees->RequeteSelection = $this->NomTable ;
				// Filtres de base
				$this->FltFrmElemId = $frm->InsereFltLgSelectHttpGet($this->NomParamId, $bd->EscapeVariableName($this->NomColId).' = <self>') ;
				$this->FltFrmElemId->Obligatoire = 1 ;
				// echo $this->FltFrmElemId->NomParametreDonnees.' hhed<br />' ;
				$this->FltFrmElemIdCtrl = $frm->InsereFltEditFixe('idCtrl', uniqid(), $this->NomColIdCtrl) ;
				if($this->AccepterAttrsPubl)
				{
					// Statut publication
					$this->FltFrmElemStatutPubl = $frm->InsereFltEditHttpPost($this->NomParamStatutPubl, $this->NomColStatutPubl) ;
					$this->FltFrmElemStatutPubl->ValeurParDefaut = 1 ;
					$this->FltFrmElemStatutPubl->Libelle = $this->LibStatutPubl ;
					$this->FltFrmElemStatutPubl->DeclareComposant("PvZoneSelectBoolHtml") ;
					// Date publication
					$this->FltFrmElemDatePubl = $frm->InsereFltEditHttpPost($this->NomParamDatePubl, $this->NomColDatePubl) ;
					$this->FltFrmElemDatePubl->Libelle = $this->LibDatePubl ;
					$this->FltFrmElemDatePubl->DeclareComposant("PvCalendarDateInput") ;
					// Heure publication
					$this->FltFrmElemHeurePubl = $frm->InsereFltEditHttpPost($this->NomParamHeurePubl, $this->NomColHeurePubl) ;
					$this->FltFrmElemHeurePubl->Libelle = $this->LibHeurePubl ;
					$this->FltFrmElemHeurePubl->DeclareComposant("PvTimeInput") ;
				}
				if($this->AccepterAttrsEdition)
				{
					// Date creation
					$this->FltFrmElemDateCreation = $frm->InsereFltEditFixe("dateCreation", 0, $this->NomColDateCreation) ;
					$this->FltFrmElemDateCreation->ExpressionColonneLiee = $bd->SqlAddDays($bd->SqlNow(), '<self>') ;
					// Id Membre creation
					$this->FltFrmElemIdMembreCreation = $frm->InsereFltEditFixe("idMembreCreation", $frm->ZoneParent->IdMembreConnecte(), $this->NomColIdMembreCreation) ;
					// Date modif
					$this->FltFrmElemDateModif = $frm->InsereFltEditFixe("dateModif", 0, $this->NomColDateModif) ;
					$this->FltFrmElemDateModif->ExpressionColonneLiee = $bd->SqlAddDays($bd->SqlNow(), '<self>') ;
					// Id Membre modif
					$this->FltFrmElemIdMembreModif = $frm->InsereFltEditFixe("idMembreModif", $frm->ZoneParent->IdMembreConnecte(), $this->NomColIdMembreModif) ;
				}
				if($this->AccepterTitre)
				{
					// Titre
					$this->FltFrmElemTitre = $frm->InsereFltEditHttpPost($this->NomParamTitre, $this->NomColTitre) ;
					$this->FltFrmElemTitre->Libelle = $this->LibTitre ;
					$compTitre = $this->FltFrmElemTitre->ObtientComposant() ;
					$compTitre->Largeur = "300px" ;
				}
			}
			protected function FinalFrmElem(& $frm)
			{
				$rediriger = 0 ;
				if(isset($script->InitFrmElem))
				{
					if($script->InitFrmElem->Role == "Ajout")
					{
						$rediriger = 1 ;
					}
				}
				else
				{
					$rediriger = 1 ;
				}
				if($rediriger)
				{
					$frm->RedirigeAnnulerVersScript($this->NomScriptListage."_".$this->NomEntite) ;
				}
			}
			protected function FigeFiltresPubl($valeurPubl=0)
			{
				$this->FltFrmElemStatutPubl->Invisible = 1 ;
				$this->FltFrmElemStatutPubl->ValeurParDefaut = $valeurPubl ;
				$this->FltFrmElemDatePubl->Invisible = 1 ;
				$this->FltFrmElemDatePubl->ValeurParDefaut = date("Y-m-d") ;
				$this->FltFrmElemHeurePubl->Invisible = 1 ;
				$this->FltFrmElemHeurePubl->ValeurParDefaut = date("H:i:s") ;
			}
			protected function SecuriserFrmElem(& $frm)
			{
				return $this->SecuriserEdition && $frm->InscrireCommandeExecuter == 1 ;
			}
			protected function AppliqueSecurFrmElem(& $frm)
			{
				if($this->SecuriserFrmElem($frm))
				{
					$this->FltCaptcha = $frm->InsereFltEditHttpPost($this->NomParamCaptcha) ;
					$this->FltCaptcha->Libelle = "Code de s&eacute;curit&eacute;" ;
					$comp = $this->FltCaptcha->DeclareComposant("PvZoneCommonCaptcha") ;
					$comp->ActionAffichImg->Params = $this->ObtientParamsUrlFrmElem($frm) ;
					$frm->CommandeExecuter->InsereNouvCritere(new CritrCodeSecurValideEntiteSws()) ;
				}
			}
			protected function ObtientParamsUrlFrmElem(& $frm)
			{
				if($frm->ScriptParent->InitFrmElem->Role == "Ajout")
				{
					return array() ;
				}
				return array($this->NomParamId => $this->FltFrmElemId->Lie()) ;
			}
			protected function CreeBlocConsult()
			{
				return new PvPortionRenduFmt() ;
			}
			protected function InitBlocConsult(& $bloc, & $script)
			{
			}
			protected function ChargeBlocConsult(& $bloc)
			{
				$bloc->Contenu = '' ;
				if($this->AccepterAttrsPubl == 1)
				{
					$bloc->InsereEncodeurDateFr(array(
					$this->NomColDatePubl, $this->NomColHeurePubl)) ;
				}
				if($this->AccepterAttrsTexte == 1)
				{
					if($this->AccepterSommaire == 1)
					{
						$bloc->InsereEncodeurNonVide(array("sommaire"), '<div class="sommaire">${luimeme}</div>', "bloc_") ;
						$bloc->Contenu .= '${bloc_sommaire}' ;
					}
					$bloc->InsereEncodeurNonVide(array("description"), '<div class="description">${luimeme}</div>', "bloc_") ;
					$bloc->Contenu .= '${bloc_description}' ;
				}
				$bloc->Params = $this->LgnEnCours ;
			}
			protected function CreeBlocEnum()
			{
				$tabl = new PvGrilleDonneesHtml() ;
				$tabl->ToujoursAfficher = 1 ;
				$tabl->MaxColonnes = 1 ;
				return $tabl ;
			}
			protected function InitBlocEnum(& $bloc, & $script)
			{
			}
			protected function ChargeBlocEnum(& $bloc)
			{
				$bd = $this->ObtientBDSupport() ;
				$ctnModele = '${'.$this->NomColId.'}. ' ;
				$bloc->InsereDefColCachee($this->NomColId) ;
				if($this->AccepterTitre == 1)
				{
					$bloc->InsereDefCol($this->NomColTitre) ;
					$ctnModele .= '${'.$this->NomColTitre.'}' ;
				}
				if($this->AccepterTexte == 1)
				{
					$bloc->InsereDefCol($this->NomColTexte) ;
				}
				if($this->AccepterAttrsEdition == 1)
				{
					$bloc->InsereDefCol($this->NomColDatePubl) ;
					$bloc->InsereDefCol($this->NomColHeurePubl) ;
				}
				$fltPubl = $bloc->InsereFltSelectFixe("statutPubl", 1, $bd->EscapeVariableName($this->NomColStatutPubl).' = <self>') ;
				$bloc->FournisseurDonnees = $this->CreeFournDonnees() ;
				$bloc->FournisseurDonnees->RequeteSelection = '(select * from '.$bd->EscapeTableName($this->NomTable).' order by '.$bd->EscapeVariableName($this->NomColDatePubl).' desc, '.$bd->EscapeVariableName($this->NomColHeurePubl).' desc)' ;
				$bloc->ContenuLigneModele = $ctnModele ;
			}
			protected function CreeTblList()
			{
				return new TableauDonneesAdminSws() ;
			}
			protected function InitTblList(& $tbl, & $script)
			{
				$tbl->ToujoursAfficher = 1 ;
				if($this->ActiverFenEditEntite)
				{
					$tbl->ContenuAvantRendu .= '<div id="'.$this->IDInstanceCalc.'_FenEdit" class="ui-dialog"><iframe id="'.$this->IDInstanceCalc.'_CadreEdit" src="about:blank" style="width:100%; height:'.($this->HauteurFenEditEntite - 45).'px" frameborder="0"></iframe></div>
<script type="text/javascript">
	function '.$tbl->IDInstanceCalc.'_AffichFenEdit(titre, url) {
		jQuery("#'.$this->IDInstanceCalc.'_CadreEdit").attr("src", url) ;
		jQuery("#'.$this->IDInstanceCalc.'_FenEdit").dialog({
			width:'.intval($this->LargeurFenEditEntite).',
			height:'.intval($this->HauteurFenEditEntite).',
			resizable:false,
			modal:true,
			title:titre,
			autoOpen:true
		}) ;
	}
	function '.$tbl->IDInstanceCalc.'_CacheFenEdit() {
		jQuery("#'.$this->IDInstanceCalc.'_FenEdit").dialog("close") ;
	}
</script>' ;
				}
			}
			protected function ChargeTblList(& $tbl)
			{
				$bd = $this->ObtientBDSupport() ;
				$membership = & $tbl->ZoneParent->Membership ;
				// Fournisseur de donn?es
				$tbl->FournisseurDonnees = $this->ModuleParent->CreeFournDonnees() ;
				/*
				$tbl->FournisseurDonnees->RequeteSelection = "(select t1.*, t2.".$bd->EscapeTableName($membership->LoginMemberColumn)." login_membre_modif from ".$bd->EscapeTableName($this->NomTable)." t1 left join ".$bd->EscapeTableName($membership->MemberTable)." t2 on t1.".$bd->EscapeTableName($this->NomColIdMembreModif)." = ".$bd->EscapeTableName($membership->IdMemberColumn).")" ;
				*/
				$tbl->FournisseurDonnees->RequeteSelection = $this->NomTable ;
				$this->DefColTblListId = $tbl->InsereDefCol($this->NomColId, $this->LibId) ;
				$this->DefColTblListId->Largeur = "5%" ;
				$this->DefColTblListId->AlignElement = "right" ;
				if($this->AccepterAttrsEdition)
				{
					$this->DefColTblListDatePubl = $tbl->InsereDefCol($this->NomColDatePubl, $this->LibDatePubl, $bd->SqlConcat(array($bd->SqlDateToStrFr($bd->EscapeVariableName($this->NomColDatePubl)), "' '", $bd->EscapeVariableName($this->NomColHeurePubl)))) ;
					$this->DefColTblListDatePubl->Largeur = "10%" ;
					$this->DefColTblListDatePubl->AlignElement = "center" ;
					$this->DefColTblListStatutPubl = $tbl->InsereDefColBool($this->NomColStatutPubl, $this->LibStatutPubl) ;
					$this->DefColTblListStatutPubl->Largeur = "8%" ;
					$this->DefColTblListStatutPubl->AlignElement = "center" ;
					$this->DefColTblListDateModif = $tbl->InsereDefCol($this->NomColDateModif, $this->LibDateModif, $bd->SqlDateToStrFr($bd->EscapeVariableName($this->NomColDateModif), 1)) ;
					$this->DefColTblListDateModif->Largeur = "10%" ;
					$this->DefColTblListDateModif->AlignElement = "center" ;
					/*
					$this->DefColTblListMembreModif = $tbl->InsereDefCol("login_membre_modif", $this->LibIdMembreModif) ;
					$this->DefColTblListMembreModif->Largeur = "10%" ;
					$this->DefColTblListMembreModif->AlignElement = "center" ;
					*/
				}
				if($this->AccepterTitre == 1)
				{
					$this->DefColTblListTitre = $tbl->InsereDefCol($this->NomColTitre) ;
					$this->DefColTblListTitre->Largeur = "30%" ;
					$this->DefColTblListTitre->Libelle = $this->LibTitre ;
				}
				if($this->InclureFltsTblList == 1)
				{
					$this->FltTblListId = $tbl->InsereFltSelectHttpGet($this->NomParamTblListId, $bd->EscapeVariableName($this->NomColId).' = <self>') ;
					$this->FltTblListId->Libelle = $this->LibTblListId ;
					$this->FltTblListId->DeclareComposant("PvZoneTexteHtml") ;
					$this->FltTblListId->Composant->Largeur = "40px" ;
					if($this->AccepterAttrsEdition && $this->InclureFltsPrdPubl)
					{
						$this->FltTblListDatePublMin = $tbl->InsereFltSelectHttpGet($this->NomParamTblListDatePublMin, $bd->SqlDatePart($bd->EscapeVariableName($this->NomColDatePubl)).' >= '.$bd->SqlStrToDate('<self>')) ;
						$this->FltTblListDatePublMin->DeclareComposant("PvCalendarDateInput") ;
						$this->FltTblListDatePublMin->Libelle = $this->LibTblListDatePublMin ;
						$this->FltTblListDatePublMin->ValeurParDefaut = date("Y-m-d", date("U") - 12 * 30 * 86400) ;
						$this->FltTblListDatePublMax = $tbl->InsereFltSelectHttpGet($this->NomParamTblListDatePublMax,$bd->SqlDatePart($bd->EscapeVariableName($this->NomColDatePubl)).' <= '.$bd->SqlStrToDate('<self>')) ;
						$this->FltTblListDatePublMax->DeclareComposant("PvCalendarDateInput") ;
						$this->FltTblListDatePublMax->Libelle = $this->LibTblListDatePublMax ;
					}
					if($this->AccepterTitre)
					{
						$this->FltTblListTitre = $tbl->InsereFltSelectHttpGet($this->NomParamTblListTitre, $bd->SqlIndexOf('UPPER('.$bd->EscapeVariableName($this->NomColTitre).')', 'UPPER(<self>)').' > 0') ;
						$this->FltTblListTitre->Libelle = $this->LibTitre ;
					}
				}
			}
			protected function FinalTblList(& $tabl)
			{
				if($this->AccepterAttrsEdition == 1)
				{
					$this->LienModifTblList = $tabl->InsereIconeAction($this->DefColTblListActs, $this->ScriptModif->ObtientUrlFmt(array($this->NomParamId => '${'.$this->NomColId.'}')), $this->ChemIconModifTblList, $this->LibModifTblList) ;
					$this->LienModifTblList->ClasseCSS = $this->ClsCSSLienTblList ;
					if($this->AccepterAttrsPubl == 1)
					{
						// Valider une publication
						$this->LienChgPublOkTblList = $tabl->InsereIconeAction(
							$this->DefColTblListActs,
							update_current_url_params(
								array(
									"idPubl" => '${'.$this->NomColId.'}',
									$tabl->ZoneParent->NomParamActionAppelee => $tabl->ScriptParent->ActChgPubl->NomElementZone
								),
								0
							),
							$this->ChemIconChgPublOkTblList,
							$this->LibChgPublOkTblList
						) ;
						$this->LienChgPublOkTblList->NomDonneesValid = $this->NomColStatutPubl ;
						$this->LienChgPublOkTblList->ValeurVraiValid = 0 ;
						// Rejeter une publication
						// echo "Mmm : ".$this->LibChgPublKoTblList."<br>" ;
						$this->LienChgPublKoTblList = $tabl->InsereIconeAction(
							$this->DefColTblListActs,
							update_current_url_params(
								array(
									"idPubl" => '${'.$this->NomColId.'}',
									$tabl->ZoneParent->NomParamActionAppelee => $tabl->ScriptParent->ActChgPubl->NomElementZone
								),
								0
							),
							$this->ChemIconChgPublKoTblList,
							$this->LibChgPublKoTblList
						) ;
						$this->LienChgPublKoTblList->NomDonneesValid = $this->NomColStatutPubl ;
						$this->LienChgPublKoTblList->ValeurVraiValid = 1 ;
					}
					$this->LienSupprTblList = $tabl->InsereIconeAction($this->DefColTblListActs, $this->ScriptSuppr->ObtientUrlFmt(array($this->NomParamId => '${'.$this->NomColId.'}')), $this->ChemIconSupprTblList, $this->LibSupprTblList) ;
					$this->LienSupprTblList->ClasseCSS = $this->ClsCSSLienTblList ;
					if($this->InclureScriptConsult == 1 && $this->ModuleParent->SystemeParent->ObtientUrlZonePubl($tabl->ZoneParent) != '')
					{
						$this->LienConsultTblList = $tabl->InsereIconeAction($this->DefColTblListActs, $this->ModuleParent->SystemeParent->ObtientUrlZonePubl($tabl->ZoneParent).'?'.urlencode($tabl->ZoneParent->NomParamScriptAppele).'='.urlencode($this->NomScriptConsult).'_'.urlencode($this->NomEntite).'&'.urlencode($this->NomParamId).'=${'.$this->NomColId.'}', $this->ChemIconConsultTblList, $this->LibConsultTblList) ;
						$this->LienConsultTblList->Cible = "_blank" ;
						$this->LienConsultTblList->NomDonneesValid = $this->NomColStatutPubl ;
					}
					if(count($this->DefsFichsJoints()) > 0)
					{
						$this->LienListFichsJoints = $tabl->InsereIconeAction(
							$this->DefColTblListActs,
							'javascript:AfficheBoiteDlgCadre_'.$tabl->ScriptParent->IDInstanceCalc.'('.	svc_json_encode(update_current_url_params(
								array(
									"idEntite" => '${'.$this->NomColId.'}',
									$tabl->ZoneParent->NomParamActionAppelee => $tabl->ScriptParent->ActListFichsJoints->NomElementZone
								),
								0
							)).', "450px", 300)',
							$this->ChemIconFichsJointsTblList,
							$this->LibFichsJointsTblList
						) ;
					}
					$this->CmdAjoutTblList = new PvCommandeRedirectionHttp() ;
					$this->CmdAjoutTblList->NomScript = $this->ScriptAjout->NomElementZone ;
					$this->CmdAjoutTblList->CheminIcone = $this->ChemIconAjoutTblList ;
					$tabl->InscritCommande("ajoutEntite", $this->CmdAjoutTblList) ;
					if($this->InclureScriptSuppr == 1)
					{
						$cmdVidage = $tabl->InsereCmdRedirectScript("videEntite", $this->ScriptVidage->NomElementZone, $this->LibelleCmdVidage) ;
						$cmdVidage->CheminIcone = $this->ChemIconVidageTblList ;
					}
					if($this->ActiverFenEditEntite)
					{
						$this->LienModifTblList->FormatURL = 'javascript:'.$tabl->IDInstanceCalc.'_AffichFenEdit('.svc_json_encode($this->TitreModifEntite).', '.svc_json_encode($this->ScriptModif->ObtientUrlFmt(array($this->NomParamId => '${'.$this->NomColId.'}'))).')' ;
						/*
						$this->LienSupprTblList->FormatURL = 'javascript:'.$tabl->IDInstanceCalc.'_AffichFenEdit('.svc_json_encode($this->TitreSupprEntite).', '.svc_json_encode($this->ScriptSuppr->ObtientUrlFmt(array($this->NomParamId => '${'.$this->NomColId.'}'))).')' ;
						*/
					}
				}
			}
			public function RenduAvantCtnSpec(& $script)
			{
				$ctn = '' ;
				if(! method_exists($script->ZoneParent, 'NiveauAdmin') || $script->ZoneParent->NiveauAdmin() != 'admin')
				{
					return '' ;
				}
				$ctn .= '<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top">'.PHP_EOL ;
				if(! $script->UtiliserCorpsDocZone)
				{
					return $ctn ;
				}
				$ctn .= $this->BarreElemsRendu->RenduDispositif().PHP_EOL ;
				$ctn .= $this->BarreMenu->RenduDispositif().PHP_EOL ;
				$ctn .= $this->BarreMenuEntite->RenduDispositif().PHP_EOL ;
				return $ctn ;
			}
			public function RenduApresCtnSpec(& $script)
			{
				if(! method_exists($script->ZoneParent, 'NiveauAdmin') || $script->ZoneParent->NiveauAdmin() != 'admin')
				{
					return '' ;
				}
				$ctn = '</td>
</tr>
</table>'.PHP_EOL ;
				return $ctn ;
			}
			public function RenduTitreScript(& $script)
			{
				$ctn = '' ;
				// $ctn .= $this->RenduFilArianeScript($script) ;
				$ctn .= '<h3 class="titre">'.$script->Titre.'</h3>' ;
				return $ctn ;
			}
			public function RenduFilArianeScript(& $script)
			{
				if($this->EstPasNul($this->FilArianeScript))
				{
					return $this->FilArianeScript->RenduDispositif() ;
				}
				return "" ;
			}
			public function RenduScriptEdit(& $script)
			{
				$ctn = '' ;
				$ctn .= $this->RenduAvantCtnSpec($script) ;
				$ctn .= $this->RenduTitreScript($script).PHP_EOL ;
				if($script->EstListage() == 0)
				{
					$ctn .= $this->SommaireElem->RenduDispositif().'<br />'.PHP_EOL ;
				}
				$ctn .= $this->FrmElem->RenduDispositif().PHP_EOL ;
				$ctn .= $this->RenduApresCtnSpec($script) ;
				return $ctn ;
			}
			public function RenduScriptSommaire(& $script)
			{
				$ctn = '' ;
				$ctn .= $this->RenduAvantCtnSpec($script) ;
				$ctn .= $this->RenduTitreScript($script).PHP_EOL ;
				if($script->EstListage() == 0)
				{
					$ctn .= $this->SommaireElem->RenduDispositif().'<br />'.PHP_EOL ;
				}
				$ctn .= $script->RenduSpecifique().PHP_EOL ;
				$ctn .= $this->RenduApresCtnSpec($script) ;
				return $ctn ;
			}
			public function RenduScriptConsult(& $script)
			{
				$ctn = '' ;
				$ctn .= $this->RenduTitreScript($script) ;
				$ctn .= $this->BlocConsult->RenduDispositif() ;
				return $ctn ;
			}
			public function RenduScriptEnum(& $script)
			{
				$ctn = '' ;
				$ctn .= $this->RenduTitreScript($script).PHP_EOL ;
				$ctn .= $this->BlocEnum->RenduDispositif() ;
				return $ctn ;
			}
			protected function & InsereSommaireElem(& $script)
			{
				$sommaire = $this->CreeSommaireElem() ;
				$this->InitSommaireElem($sommaire, $script) ;
				$sommaire->AdopteScript('sommaireElem', $script) ;
				$sommaire->ChargeConfig() ;
				$this->ChargeSommaireElem($sommaire) ;
				$this->FinalSommaireElem($sommaire) ;
				return $sommaire ;
			}
			protected function CreeSommaireElem()
			{
				return new PvFormulaireDonneesHtml() ;
			}
			protected function InitSommaireElem(& $sommaire, & $script)
			{
				$sommaire->MaxFiltresEditionParLigne = 1 ;
				$sommaire->Editable = 0 ;
				$sommaire->InclureElementEnCours = 1 ;
				$sommaire->InclureTotalElements = 1 ;
				$sommaire->InscrireCommandeExecuter = 0 ;
			}
			protected function ChargeSommaireElem(& $sommaire)
			{
				$bd = $this->ObtientBDSupport() ;
				// Fournisseur de donn?es
				$sommaire->FournisseurDonnees = $this->ModuleParent->CreeFournDonnees() ;
				$sommaire->FournisseurDonnees->TableEdition = $this->NomTable ;
				$sommaire->FournisseurDonnees->RequeteSelection = $this->NomTable ;
				// Filtres de base
				$this->FltSommaireElemId = $sommaire->InsereFltLgSelectHttpGet($this->NomParamId, $bd->EscapeVariableName($this->NomColId).' = <self>') ;
				$this->FltSommaireElemId->Obligatoire = 1 ;
				// Colonnes sommaire
				$nomCols = $this->NomColsSommaire() ;
				foreach($nomCols as $i => $col)
				{
					$flt = $sommaire->InsereFltEditHttpPost($col->NomCol, $col->NomCol) ;
					$flt->Libelle = $col->Titre ;
				}
			}
			protected function FinalSommaireElem(& $sommaire)
			{
				$rediriger = 0 ;
				$sommaire->RedirigeAnnulerVersScript($this->NomScriptListage."_".$this->NomEntite) ;
			}
			protected function NomColsSommaire()
			{
				$result = array(new ColSommaireEntiteSws($this->NomColId, $this->LibId)) ;
				if($this->AccepterTitre == 1)
				{
					$result[] = new ColSommaireEntiteSws($this->NomColTitre, $this->LibTitre) ;
				}
				return $result ;
			}
			public function ColsSommaire()
			{
				return $this->NomColsSommaire() ;
			}
			public function RenduScriptLst(& $script)
			{
				$ctn = '' ;
				$ctn .= $this->RenduAvantCtnSpec($script) ;
				$ctn .= $this->RenduTitreScript($script).PHP_EOL ;
				$ctn .= $this->TblList->RenduDispositif() ;
				$ctn .= $this->RenduApresCtnSpec($script) ;
				return $ctn ;
			}
			public function ObtientReqSqlFluxRSS()
			{
				$this->DefFluxRSS->NomTable = $this->NomTable ;
				return parent::ObtientReqSqlFluxRSS() ;
			}
			public function ObtientReqSqlRech($motsRech)
			{
				$this->DefRech->NomTable = $this->NomTable ;
				if($this->AccepterAttrsPubl == 1)
				{
					$this->DefRech->NomColDatePubl = $this->NomColDatePubl ;
					$this->DefRech->NomColHeurePubl = $this->NomColHeurePubl ;
				}
				if($this->AccepterTitre == 1)
				{
					$this->DefRech->NomColTitre = $this->NomColTitre ;
				}
				if($this->AccepterAttrsMeta == 1)
				{
					$this->DefRech->NomColDescriptionMeta = $this->NomColDescriptionMeta ;
					$this->DefRech->NomColMotsClesMeta = $this->NomColMotsClesMeta ;
				}
				return parent::ObtientReqSqlRech($motsRech) ;
			}
			public function FormatElemLienLgnRSS(& $lgn)
			{
				if($this->InclureScriptConsult == 1)
				{
					$lgn["url"] = $this->ScriptConsult->ObtientUrlParam(array($this->NomParamId => $lgn["id"])) ;
				}
			}
			public function FormatElemLienLgnRech(& $lgn)
			{
				if($this->InclureScriptConsult == 1)
				{
					$lgn["url"] = $this->ScriptConsult->ObtientUrlParam(array($this->NomParamId => $lgn["id"])) ;
				}
			}
			public function NomParamsTexte()
			{
				return array($this->NomColTitre) ;
			}
			public function RemplitMenuPlanSite(& $menu)
			{
				if($this->InclureScriptEnumPlanSite == 1 && $this->InclureScriptEnum == 1)
				{
					$this->MenuScriptEnumPlanSite = $menu->InscritSousMenuScript($this->ScriptEnum->NomElementZone) ;
					$this->MenuScriptEnumPlanSite->Titre = $this->TitreEnumEntite ;
				}
				else
				{
					$this->MenuScriptEnumPlanSite = & $menu ;
				}
				if($this->InclureScriptConsultPlanSite == 1 && $this->InclureScriptConsult == 1)
				{
					$bd = $this->ObtientBDSupport() ;
					$lgns = $bd->FetchSqlRows($this->SqlConsultPlanSite()) ;
					foreach($lgns as $i => $lgn)
					{
						$sousMenu = $this->MenuScriptEnumPlanSite->InscritSousMenuUrl($lgn["titre"], $this->ScriptConsult->ObtientUrlParam(array($this->NomParamId => $lgn["id"]))) ;
					}
				}
			}
			protected function SqlConsultPlanSite()
			{
				$bd = $this->ObtientBDSupport() ;
				$sql = "select ".$bd->EscapeVariableName($this->NomColIdConsultPlanSite)." id" ;
				$sql .= ", ".$bd->EscapeVariableName($this->NomColTitreConsultPlanSite)." titre" ;
				$sql .= " from ".$bd->EscapeTableName($this->NomTable) ;
				if($this->AccepterAttrsPubl == 1)
				{
					$sql .= " where ".$bd->EscapeVariableName($this->NomColStatutPubl)." = 1" ;
					$sql .= " order by ".$bd->EscapeVariableName($this->NomColDatePubl)." desc, ".$bd->EscapeVariableName($this->NomColHeurePubl)." desc" ;
				}
				return $sql ;
			}
		}
		class EntitePageWebSws extends EntiteTableSws
		{
			public $NomParamTitre = "titre" ;
			public $NomParamUrl = "url" ;
			public $NomParamSommaire = "sommaire" ;
			public $NomParamDescription = "description" ;
			public $CheminTelechargIcones = "images/icones" ;
			public $CheminTelechargImages = "images" ;
			public $CheminTelechargBannieres = "images/bannieres" ;
			public $NomParamCheminIcone = "chemin_icone" ;
			public $NomParamCheminImage = "chemin_image" ;
			public $NomParamCheminBanniere = "chemin_banniere" ;
			public $NomParamMotsClesMeta = "mots_cles_meta" ;
			public $NomParamDescriptionMeta = "description_meta" ;
			public $AccepterSommaire = 1 ;
			public $AccepterAttrsTexte = 1 ;
			public $AccepterAttrsGraphique = 1 ;
			public $AccepterAttrsMeta = 1 ;
			public $AccepterUrl = 0 ;
			public $LibTitre = "Titre" ;
			public $LibUrl = "Url" ;
			public $LibSommaire = "Sommaire" ;
			public $LibDescription = "Description" ;
			public $LibMotsClesMeta = "Mots cl&eacute;s Meta" ;
			public $LibDescriptionMeta = "Description Meta" ;
			public $LibCheminIcone = "Icone" ;
			public $LibCheminImage = "Image" ;
			public $LibCheminBanniere = "Banni&egrave;re" ;
			public $NomColTitre = "titre" ;
			public $NomColUrl = "url" ;
			public $NomColSommaire = "sommaire" ;
			public $NomColCheminIcone = "chemin_icone" ;
			public $NomColCheminImage = "chemin_image" ;
			public $NomColCheminBanniere = "chemin_banniere" ;
			public $NomColDescription = "description" ;
			public $NomColMotsClesMeta = "mots_cles_meta" ;
			public $NomColDescriptionMeta = "description_meta" ;
			public $FltFrmElemUrl ;
			public $FltFrmElemTitre ;
			public $FltFrmElemSommaire ;
			public $FltFrmElemIcone ;
			public $FltFrmElemImage ;
			public $FltFrmElemBanniere ;
			public $FltFrmElemDescription ;
			public $FltFrmElemMotsClesMeta ;
			public $FltFrmElemDescriptionMeta ;
			public $LargeurTitre = "300px" ;
			public $LargeurUrl = "320px" ;
			public $TotalColonnesSommaire = 60 ;
			public $TotalLignesSommaire = 6 ;
			public $TotalColonnesMotsClesMeta = 60 ;
			public $TotalLignesMotsClesMeta = 3 ;
			public $TotalColonnesDescriptionMeta = 60 ;
			public $TotalLignesDescriptionMeta = 4 ;
			public $FltTblListContenu ;
			public $NomParamTblListContenu = "pContenu" ;
			public $LibTblListContenu = "Contenu" ;
			public $DefColTblListTitre ;
			protected function InitAdaptFltsEdition()
			{
				$this->AdaptFltsEdition = new AdaptFltsEntitePageWebSws() ;
			}
			public function SqlListeColsSelect(& $bd)
			{
				$systeme = & $this->ModuleParent->SystemeParent ;
				$sql = parent::SqlListeColsSelect($bd) ;
				if($this->AccepterAttrsTexte == 1)
				{
					$sql .= ', '.$bd->EscapeVariableName($this->NomColTitre).' titre' ;
					if($this->AccepterSommaire == 1)
					{
						$sql .= ', '.$bd->EscapeVariableName($this->NomColSommaire).' sommaire' ;
					}
					$sql .= ', '.$bd->EscapeVariableName($this->NomColDescription).' description' ;
				}
				if($this->AccepterAttrsGraphique == 1)
				{
					$sql .= ', '.$bd->EscapeVariableName($this->NomColCheminIcone).' chemin_icone' ;
					$sql .= ', '.$bd->EscapeVariableName($this->NomColCheminImage).' chemin_image' ;
					$sql .= ', '.$bd->EscapeVariableName($this->NomColCheminBanniere).' chemin_banniere' ;
					$sql .= ', '.$systeme->SqlCheminPubl($bd->EscapeVariableName($this->NomColCheminIcone)).' chemin_icone_publ' ;
					$sql .= ', '.$systeme->SqlCheminPubl($bd->EscapeVariableName($this->NomColCheminImage)).' chemin_image_publ' ;
					$sql .= ', '.$systeme->SqlCheminPubl($bd->EscapeVariableName($this->NomColCheminBanniere)).' chemin_banniere_publ' ;
				}
				if($this->AccepterAttrsMeta == 1)
				{
					$sql .= ', '.$bd->EscapeVariableName($this->NomColMotsClesMeta).' mots_cles_meta' ;
					$sql .= ', '.$bd->EscapeVariableName($this->NomColDescriptionMeta).' description_meta' ;
				}
				return $sql ;
			}
			protected function PrepareScriptConsult(& $script)
			{
				parent::PrepareScriptConsult($script) ;
				$script->TitreDocument = $this->LgnEnCours["titre"] ;
				$script->Titre = $this->LgnEnCours["titre"] ;
				if($this->AccepterAttrsMeta == 1)
				{
					if($this->LgnEnCours["mots_cles_meta"] != "")
						$script->MotsCleMeta = $this->LgnEnCours["mots_cles_meta"] ;
					if($this->LgnEnCours["description_meta"] != "")
						$script->DescriptionMeta = $this->LgnEnCours["description_meta"] ;
				}
			}
			protected function InitBlocConsult(& $bloc, & $script)
			{
			}
			protected function ChargeBlocConsult(& $bloc)
			{
				parent::ChargeBlocConsult($bloc) ;
			}
			protected function ChargeFrmElem(& $frm)
			{
				parent::ChargeFrmElem($frm) ;
				if($this->AccepterUrl == 1)
				{
					$this->FltFrmElemUrl = $frm->InsereFltEditHttpPost($this->NomParamUrl, $this->NomColUrl) ;
					$compUrl = $this->FltFrmElemUrl->ObtientComposant() ;
					$compUrl->Largeur = $this->LargeurUrl ;
				}
				if($this->AccepterAttrsTexte == 1)
				{
					// Titre
					$this->FltFrmElemTitre = $frm->InsereFltEditHttpPost($this->NomParamTitre, $this->NomColTitre) ;
					$this->FltFrmElemTitre->Libelle = $this->LibTitre ;
					$comp = $this->FltFrmElemTitre->DeclareComposant("PvZoneTexteHtml") ;
					$comp->Largeur = $this->LargeurTitre ;
					// Sommaire
					if($this->AccepterSommaire)
					{
						$this->FltFrmElemSommaire = $frm->InsereFltEditHttpPost($this->NomParamSommaire, $this->NomColSommaire) ;
						$this->FltFrmElemSommaire->Libelle = $this->LibSommaire ;
						$comp = $this->FltFrmElemSommaire->DeclareComposant("PvZoneMultiligneHtml") ;
						$comp->TotalColonnes = $this->TotalColonnesSommaire ;
						$comp->TotalLignes = $this->TotalLignesSommaire ;
					}
					// Description
					$this->FltFrmElemDescription = $frm->InsereFltEditHttpPost($this->NomParamDescription, $this->NomColDescription) ;
					$this->FltFrmElemDescription->Libelle = $this->LibDescription ;
					$this->FltFrmElemDescription->DeclareComposant("PvCkEditor") ;
				}
				if($this->AccepterAttrsGraphique == 1)
				{
					// Icone
					$this->FltFrmElemIcone = $frm->InsereFltEditHttpUpload($this->NomParamCheminIcone, $this->ModuleParent->SystemeParent->CheminAdminVersPubl."/".$this->CheminTelechargIcones, $this->NomColCheminIcone) ;
					$this->FltFrmElemIcone->Libelle = $this->LibCheminIcone ;
					$frm->InsereTailleFiltreImageRef($this->FltFrmElemIcone, 48, 48) ;
					// Image
					$this->FltFrmElemImage = $frm->InsereFltEditHttpUpload($this->NomParamCheminImage, $this->ModuleParent->SystemeParent->CheminAdminVersPubl."/".$this->CheminTelechargImages, $this->NomColCheminImage) ;
					$this->FltFrmElemImage->Libelle = $this->LibCheminImage ;
					$frm->InsereTailleFiltreImageRef($this->FltFrmElemImage, 800, 450) ;
					// Banni?re
					$this->FltFrmElemBanniere = $frm->InsereFltEditHttpUpload($this->NomParamCheminBanniere, $this->ModuleParent->SystemeParent->CheminAdminVersPubl."/".$this->CheminTelechargBannieres, $this->NomColCheminBanniere) ;
					$this->FltFrmElemBanniere->Libelle = $this->LibCheminBanniere ;
					$frm->InsereTailleFiltreImageRef($this->FltFrmElemBanniere, 1024, 768) ;
				}
				if($this->AccepterAttrsMeta == 1)
				{
					// Mots cles Meta
					$this->FltFrmElemMotsClesMeta = $frm->InsereFltEditHttpPost($this->NomParamMotsClesMeta, $this->NomColMotsClesMeta) ;
					$this->FltFrmElemMotsClesMeta->Libelle = $this->LibMotsClesMeta ;
					$comp = $this->FltFrmElemMotsClesMeta->DeclareComposant("PvZoneMultiligneHtml") ;
					$comp->TotalColonnes = $this->TotalColonnesMotsClesMeta ;
					$comp->TotalLignes = $this->TotalLignesMotsClesMeta ;
					// Description Meta
					$this->FltFrmElemDescriptionMeta = $frm->InsereFltEditHttpPost($this->NomParamDescriptionMeta, $this->NomColDescriptionMeta) ;
					$this->FltFrmElemDescriptionMeta->Libelle = $this->LibDescriptionMeta ;
					$comp = $this->FltFrmElemDescriptionMeta->DeclareComposant("PvZoneMultiligneHtml") ;
					$comp->TotalColonnes = $this->TotalColonnesDescriptionMeta ;
					$comp->TotalLignes = $this->TotalLignesDescriptionMeta ;
				}
			}
			protected function ChargeBlocEnum(& $bloc)
			{
				parent::ChargeBlocEnum($bloc) ;
				$bd = $this->ObtientBDSupport() ;
				$bloc->InsereDefCol($this->NomColTitre) ;
				if($this->InclureScriptConsult == 1)
				{
					$bloc->ContenuLigneModele = '<a href="'.$this->ScriptConsult->ObtientUrlFmt(array($this->NomParamId => '${'.$this->NomColId.'}')).'">${'.$this->NomColTitre.'}</a>' ;
				}
				else
				{
					$bloc->ContenuLigneModele = '${'.$this->NomColTitre.'}' ;
				}
			}
			protected function ExtraitCtnPubl()
			{
				$ctn = '' ;
				if($this->AccepterTexte == 1)
				{
					if($this->EstPasNul($this->FltFrmElemTitre))
					{
						$ctn .= $this->FltFrmElemTitre->Lie() ;
					}
					if($this->EstPasNul($this->FltFrmElemSommaire))
					{
						if($ctn != '')
							$ctn .= ' ' ;
						$ctn .= $this->FltFrmElemSommaire->Lie() ;
					}
					if($this->EstPasNul($this->FltFrmElemDescription))
					{
						if($ctn != '')
							$ctn .= ' ' ;
						$ctn .= $this->FltFrmElemDescription->Lie() ;
					}
				}
				return $ctn ;
			}
			protected function ExtraitTextePubl()
			{
				$ctn = $this->ExtraitCtnPubl() ;
				$result = "" ;
				if($ctn != '')
				{
					$elemHtml = str_get_html($ctn) ;
					$result = $elemHtml->plaintext ;
				}
				return $result ;
			}
			protected function FixeAttrsMeta()
			{
				if(! $this->AutoFixeAttrsMetaVide)
					return ;
				
				if($this->FltFrmElemDescriptionMeta == '')
				{
					$this->FltFrmElemDescriptionMeta->ValeurParametre = substr($this->ExtraitTextePubl(), 0, 255) ;
				}
				if($this->FltFrmElemMotsClesMeta == '')
				{
					$this->FltFrmElemMotsClesMeta->ValeurParametre = $this->ExtraitTextePubl() ;
				}
			}
			public function PossedePrivileges($privs=array())
			{
				return $this->ZoneParent->PossedePrivileges($privs) ;
			}
			public function PossedePrivilege($priv)
			{
				return $this->ZoneParent->PossedePrivilege($priv) ;
			}
			public function & ObtientModulePage()
			{
				$module = $this->ModuleParent ;
				return $module ;
			}
			public function NomParamsTexte()
			{
				$params = parent::NomParamsTexte() ;
				if($this->AccepterAttrsTexte)
				{
					if($this->AccepterSommaire)
						$params[] = $this->NomParamSommaire ;
					$params[] = $this->NomParamDescription ;
				}
				return $params ;
			}
			protected function ChargeTblList(& $tbl)
			{
				parent::ChargeTblList($tbl) ;
				$bd = $this->ObtientBDSupport() ;
				if($this->AccepterAttrsTexte == 1)
				{
					$this->DefColTblListTitre = $tbl->InsereDefCol($this->NomColTitre, $this->LibTitre) ;
					$this->DefColTblListTitre->Largeur = "30%" ;
					if($this->InclureFltsTblList == 1)
					{
						$this->FltTblListContenu = $tbl->InsereFltSelectHttpGet($this->NomParamTblListContenu, $bd->SqlIndexOf('UPPER('.$bd->EscapeVariableName($this->NomColTitre).')', 'UPPER(<self>)').' > 0 OR '.$bd->SqlIndexOf('UPPER('.$bd->EscapeVariableName($this->NomColDescription).')', 'UPPER(<self>)').' > 0') ;
						if($this->AccepterSommaire)
						{
							$this->FltTblListContenu->ExpressionDonnees .= ' OR '.$bd->SqlIndexOf('UPPER('.$bd->EscapeVariableName($this->NomColSommaire).')', 'UPPER(<self>)').' > 0' ;
						}
						$this->FltTblListContenu->Libelle = $this->LibTblListContenu ;
						$this->FltTblListContenu->DeclareComposant("PvZoneTexteHtml") ;
						$this->FltTblListContenu->Composant->Largeur = "200px" ;
					}
				}
			}
			public function ObtientReqSqlFluxRSS()
			{
				$bd = $this->ObtientBDSupport() ;
				if($this->AccepterAttrsMeta == 1)
				{
					$this->DefFluxRSS->ExprColDescription = 'case when '.$bd->SqlConcat(array($bd->EscapeVariableName($this->NomColMotsClesMeta), "' '", $bd->EscapeVariableName($this->NomColDescriptionMeta))).' is not null then '.$bd->SqlConcat(array($bd->EscapeVariableName($this->NomColMotsClesMeta), "' '", $bd->EscapeVariableName($this->NomColDescriptionMeta))).' else '.$bd->EscapeVariableName($this->NomColDescription).' end' ;
					
				}
				return parent::ObtientReqSqlFluxRSS() ;
			}
			public function DefsFichsJoints()
			{
				$fichs = array() ;
				if($this->AccepterAttrsGraphique == 1)
				{
					$fichs[] = $this->CreeDefFichJoint($this->NomColCheminIcone, $this->LibCheminIcone) ;
					$fichs[] = $this->CreeDefFichJoint($this->NomColCheminImage, $this->LibCheminImage) ;
					$fichs[] = $this->CreeDefFichJoint($this->NomColCheminBanniere, $this->LibCheminBanniere) ;
				}
				return $fichs ;
			}
		}
		
		class InitBaseFrmEntiteTableSws
		{
			public $Role = '' ;
			public function Applique(& $frm, & $script)
			{
			}
		}
		class InitAjoutFrmEntiteTableSws extends InitBaseFrmEntiteTableSws
		{
			public $Role = 'Ajout' ;
			public function Applique(& $frm, & $script)
			{
				$frm->InscrireCommandeExecuter = 1 ;
				$frm->InclureElementEnCours = 0 ;
				$frm->InclureTotalElements = 0 ;
				$frm->Editable = 1 ;
			}
		}
		class InitModifFrmEntiteTableSws extends InitBaseFrmEntiteTableSws
		{
			public $Role = 'Modif' ;
			public function Applique(& $frm, & $script)
			{
				$frm->InscrireCommandeExecuter = 1 ;
				$frm->InclureElementEnCours = 1 ;
				$frm->InclureTotalElements = 1 ;
				$frm->Editable = 1 ;
				$frm->NomClasseCommandeAnnuler = "PvCommandeRedirectScriptSession" ;
			}
		}
		class InitSupprFrmEntiteTableSws extends InitBaseFrmEntiteTableSws
		{
			public $Role = 'Suppr' ;
			public function Applique(& $frm, & $script)
			{
				$frm->InscrireCommandeExecuter = 1 ;
				$frm->InclureElementEnCours = 1 ;
				$frm->InclureTotalElements = 1 ;
				$frm->Editable = 0 ;
				$frm->NomClasseCommandeAnnuler = "PvCommandeRedirectScriptSession" ;
			}
		}

		class ScriptEntiteTableBaseSws extends ScriptBaseSws
		{
			public $EstLienSommaire = 0 ;
			protected function RenduBoiteDlgCadre()
			{
				$ctn = '' ;
				$ctn .= '<div id="BoiteDlgCadre_'.$this->IDInstanceCalc.'" style="display:none"><iframe src="about:blank" style="width:98%; border:0px" frameborder="0"></iframe></div>
<script type="text/javascript">
	jQuery(function() {
		jQuery("#BoiteDlgCadre_'.$this->IDInstanceCalc.'").dialog({ autoOpen: false, modal:true, resizable:false, draggable:false }) ;
	}) ;
	function AfficheBoiteDlgCadre_'.$this->IDInstanceCalc.'(url, largeur, hauteur) {
		var jqBoiteDlg = jQuery("#BoiteDlgCadre_'.$this->IDInstanceCalc.'") ;
		jqBoiteDlg.find("iframe").css((hauteur - 18) + "px") ;
		jqBoiteDlg.find("iframe").attr("src", url) ;
		jqBoiteDlg.dialog("option", "width", largeur) ;
		jqBoiteDlg.dialog("option", "height", hauteur) ;
		jqBoiteDlg.dialog("open") ;
	} ;
	function FixeTitreBoiteDlgCadre_'.$this->IDInstanceCalc.'(titre) {
		var jqBoiteDlg = jQuery("#BoiteDlgCadre_'.$this->IDInstanceCalc.'") ;
		jqBoiteDlg.dialog("option", "title", titre) ;
	}
</script>' ;
				return $ctn ;
			}
			public function EstListage()
			{
				return 1 ;
			}
		}
		class ScriptListageEntiteTableSws extends ScriptEntiteTableBaseSws
		{
			public $NecessiteMembreConnecte = 1 ;
			public $EstScriptSession = 1 ;
			public $ActChgPubl ;
			public $ActListFichsJoints ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				if($this->EntitePage->AccepterAttrsPubl == 1)
				{
					$this->ActChgPubl = $this->InsereActionAvantRendu('change_publication', new ActChgPublEntiteTableSws()) ;
				}
				if(count($this->EntitePage->DefsFichsJoints()) > 0)
				{
					$this->ActListFichsJoints = $this->InsereActionAvantRendu('liste_fichs_joints', new ActListFichsJointsEntiteSws()) ;
				}
				$this->EntitePage->RemplitScriptLst($this) ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= $this->RenduBoiteDlgCadre().PHP_EOL ;
				$ctn .= $this->EntitePage->RenduScriptLst($this) ;
				return $ctn ;
			}
		}
		class ScriptVueEntiteTableSws extends ScriptEntiteTableBaseSws
		{
			public $NecessiteMembreConnecte = 1 ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->EntitePage->RemplitScriptVue($this) ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$entite = $this->ObtientEntitePage() ;
				$ctn .= $entite->RenduAvantCtnSpec($this) ;
				$ctn .= $entite->RenduTitreScript($this).PHP_EOL ;
				$ctn .= $this->RenduSpecifique() ;
				$ctn .= $entite->RenduApresCtnSpec($this) ;
				return $ctn ;
			}
		}
		class ScriptVideEntiteTableSws extends ScriptVueEntiteTableSws
		{
			public $NecessiteMembreConnecte = 1 ;
			protected $ConfirmVidageSoumis = 0 ;
			protected $MsgErrVidage = "" ;
			protected $MsgSuccesVidage = "" ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineConfirmVidage() ;
				$entite = $this->ObtientEntitePage() ;
				$this->Titre = $entite->TitreVideEntite ;
				$this->TitreDocument = $entite->TitreVideEntite ;
			}
			protected function DetermineConfirmVidage()
			{
				if(! isset($_POST["ConfirmVidage"]))
				{
					return ;
				}
				$this->ConfirmVidageSoumis = 1 ;
				$entite = $this->ObtientEntitePage() ;
				$ok = $entite->VideLgns() ;
				if(! $ok)
				{
					$this->MsgErrVidage = $entite->ObtientBDSupport()->ConnectionException ;
				}
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$entite = $this->ObtientEntitePage() ;
				if($this->ConfirmVidageSoumis == 0)
				{
					$ctn .= '<div class="ui-state-highlight">'.$entite->MsgAlerteVideScript.'</div><br />'.PHP_EOL ;
					$ctn .= '<div class="ui-state-active sws-ui-padding-4"><form action="'.$this->ObtientUrl().'" method="post">'.PHP_EOL ;
					$ctn .= '<input type="hidden" name="ConfirmVidage" value="1">'.PHP_EOL ;
					$ctn .= '<button type="submit" class="ui-widget ui-widget-content ui-state-hover">Confirmer</button>'.PHP_EOL ;
					$ctn .= '</div><br />'.PHP_EOL ;
				}
				else
				{
					if($this->MsgErrVidage == "")
					{
						$ctn .= '<div class="ui-state-error">'.$this->MsgErrVidage.'</div>' ;
					}
					else
					{
						$ctn .= '<div class="ui-state-highlight">'.$this->MsgSuccesVidage.'</div>' ;
					}
				}
				return $ctn ;
			}
		}
		
		class ScriptElemEntiteTableSws extends ScriptEntiteTableBaseSws
		{
			public function EstListage()
			{
				return 0 ;
			}
		}
		class ScriptConsultEntiteTableSws extends ScriptElemEntiteTableSws
		{
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->EntitePage->RemplitScriptConsult($this) ;
			}
			protected function RenduDispositifBrut()
			{
				return $this->EntitePage->RenduScriptConsult($this) ;
			}
		}
		class ScriptEnumEntiteTableSws extends ScriptElemEntiteTableSws
		{
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->EntitePage->RemplitScriptEnum($this) ;
			}
			protected function RenduDispositifBrut()
			{
				return $this->EntitePage->RenduScriptEnum($this) ;
			}
		}
		
		class ScriptEditEntiteTableSws extends ScriptEntiteTableBaseSws
		{
			public $EstLienSommaire = 0 ;
			public $NecessiteMembreConnecte = 1 ;
			public $InitFrmElem ;
			public function EstListage()
			{
				return 0 ;
			}
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->InitFrmElem = $this->CreeInitFrmElem() ;
				$this->EntitePage->RemplitScriptEdit($this) ;
			}
			protected function CreeInitFrmElem()
			{
				return new InitBaseFrmEntiteTableSws() ;
			}
			protected function RenduDispositifBrut()
			{
				return $this->EntitePage->RenduScriptEdit($this) ;
			}
		}
		class ScriptSommEntiteTableSws extends ScriptEntiteTableBaseSws
		{
			public $NecessiteMembreConnecte = 1 ;
			protected $CompPrinc ;
			public function EstListage()
			{
				return 0 ;
			}
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->EntitePage->RemplitScriptSommaire($this) ;
				$this->DetermineCompPrinc() ;
			}
			protected function DetermineCompPrinc()
			{
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				if($this->EstPasNul($this->CompPrinc))
				{
					$ctn .= $this->CompPrinc->RenduDispositif() ;
				}
				return $ctn ;
			}
			protected function RenduDispositifBrut()
			{
				return $this->EntitePage->RenduScriptSommaire($this) ;
			}
		}
		class ScriptAjoutEntiteTableSws extends ScriptEditEntiteTableSws
		{
			public function EstListage()
			{
				return 1 ;
			}
			protected function CreeInitFrmElem()
			{
				return new InitAjoutFrmEntiteTableSws() ;
			}
		}
		class ScriptModifEntiteTableSws extends ScriptEditEntiteTableSws
		{
			protected function CreeInitFrmElem()
			{
				return new InitModifFrmEntiteTableSws() ;
			}
		}
		class ScriptPositionPublEntiteTableSws extends ScriptElemEntiteTableSws
		{
		}
		class ScriptSupprEntiteTableSws extends ScriptEditEntiteTableSws
		{
			protected function CreeInitFrmElem()
			{
				return new InitSupprFrmEntiteTableSws() ;
			}
		}
		
		class ActChgPublEntiteTableSws extends PvActionBaseZoneWebSimple
		{
			public function Execute()
			{
				$this->DetermineActionChgPubl() ;
			}
			protected function DetermineActionChgPubl()
			{
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				if($entite->AccepterAttrsPubl == 0)
				{
					return ;
				}
				$bd = $this->ScriptParent->ObtientBDSupport() ;
				$id = _GET_def("idPubl") ;
				if($id == "")
				{
					return ;
				}
				$idMembreConnecte = $this->ZoneParent->IdMembreConnecte() ;
				$sql = "update ".$bd->EscapeTableName($entite->NomTable)."
set ".$bd->EscapeVariableName($entite->NomColStatutPubl)."=case when ".$bd->EscapeVariableName($entite->NomColStatutPubl)." = 0 then 1 else 0 end,
".$bd->EscapeVariableName($entite->NomColDateModif)." = :dateModif,
".$bd->EscapeVariableName($entite->NomColIdMembreModif)." = :idMembre
where ".$bd->EscapeVariableName($entite->NomColId)." = :idEntite" ;
				$ok = $bd->RunSql(
					$sql,
					array(
						"idEntite" => $id,
						"idMembre" => $idMembreConnecte,
						"dateModif" => date("Y-m-d H:i:s"),
					)
				) ;
			}
		}
		
		class DefFichJointElemRenduSws
		{
			public $NomCol ;
			public $Titre ;
		}
		class ActListFichsJointsEntiteSws extends PvActionRenduPageWeb
		{
			protected $ValeurParamId ;
			protected $LgnEnCours ;
			protected $MsgErreur ;
			public function Execute()
			{
				$this->DetermineLgnEnCours() ;
				parent::Execute() ;
			}
			protected function DetermineLgnEnCours()
			{
				$script = & $this->ScriptParent ;
				$entite = $script->ObtientEntitePage() ;
				$bd = $script->ObtientBDSupport() ;
				$this->ValeurParamId = _GET_def("idEntite") ;
				$this->LgnEnCours = $bd->FetchSqlRow(
					"select * from ".$bd->EscapeVariableName($entite->NomTable)." where ".$bd->EscapeVariableName($entite->NomColId)." = ".$bd->ParamPrefix."idEntite",
					array("idEntite" => $this->ValeurParamId)
				) ;
				if(count($entite->DefsFichsJoints()) == 0)
				{
					$this->MsgErreur = "Aucun fichier trouv&eacute;" ;
					return ;
				}
				if(is_array($this->LgnEnCours))
				{
					if(count($this->LgnEnCours) == 0)
					{
						$this->MsgErreur = "L'information que vous cherchez n'a pas &eacute;t&eacute; trouv&eacute;e" ;
					}
				}
				else
				{
					$this->MsgErreur = "Erreur Sql : ".$bd->ConnectionException ;
				}
			}
			protected function PrepareDoc()
			{
				parent::PrepareDoc() ;
				$this->InscritLienCSS($this->ZoneParent->CheminCSSJQueryUi) ;
				$this->InscritContenuCSS('body, p, div, form, table, td, th {
	font-size:12px ;
}') ;
				$this->InscritLienJs($this->ZoneParent->CheminJQuery) ;
				$this->InscritLienJs($this->ZoneParent->CheminJQueryMigrate) ;
				$this->InscritLienJs($this->ZoneParent->CheminJsJQueryUi) ;
			}
			protected function RenduCorpsDoc()
			{
				$ctn = '' ;
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				if($this->MsgErreur != "")
				{
					$ctn .= '<p class="ui-widget ui-state-error">'.$this->MsgErreur.'</p>' ;
				}
				else
				{
					// Fixer le sommaire
					$titreSommaire = "" ;
					foreach($entite->ColsSommaire() as $i => $col)
					{
						if($i > 0)
						{
							$titreSommaire .= " - " ;
						}
						$titreSommaire .= htmlentities($this->LgnEnCours[$col->NomCol]) ;
					}
					$this->InscritContenuJs('jQuery(function() {
	window.top.FixeTitreBoiteDlgCadre_'.$this->ScriptParent->IDInstanceCalc.'('.svc_json_encode($titreSommaire).') ;
}) ;') ;
					// Rendu des fichiers joints
					$ctn .= '<table width="100%" class="ui-widget">'.PHP_EOL ;
					foreach($entite->DefsFichsJoints() as $i => $fichBrut)
					{
						$valeurFich = $this->LgnEnCours[$fichBrut->NomCol] ;
						$ctn .= '<tr>'.PHP_EOL ;
						$ctn .= '<th class="ui-widget-content">'.$fichBrut->Titre.'</th>'.PHP_EOL ;
						$ctn .= '<td class="ui-widget-content">'.(($valeurFich != '') ? '<a href="'.htmlspecialchars($valeurFich).'" target="apercu">'.htmlentities($valeurFich).'</a>' : '(Vide)').'</th>'.PHP_EOL ;
						$ctn .= '</tr>'.PHP_EOL ;
					}
					$ctn .= '</table>' ;
				}
				return $ctn ;
			}
		}
		
		class ValeurMetaCalculeeSws
		{
			public $MotsCle ;
			public $Description ;
		}
		
		class AdaptFltsEntiteBaseSws
		{
			public function AppliqueAvantExecution(& $cmd, & $form, & $entite)
			{
			}
			public function AppliqueApresExecution(& $cmd, & $form, & $entite)
			{
			}
		}
		class AdaptFltsInactifEntiteSws extends AdaptFltsEntiteBaseSws
		{
			public function AppliqueAvantExecution(& $cmd, & $form, & $entite)
			{
			}
			public function AppliqueApresExecution(& $cmd, & $form, & $entite)
			{
			}
		}
		class AdaptFltsEntitePageWebSws extends AdaptFltsEntiteBaseSws
		{
			protected function CalculeMetas(& $form, & $entite)
			{
				$val = new ValeurMetaCalculeeSws() ;
				$nomParamsFlts = $entite->NomParamsTexte() ;
				$texte = '' ;
				foreach($form->FiltresEdition as $nom => & $flt)
				{
					if(in_array($flt->NomParametreLie, $nomParamsFlts))
					{
						if($texte != '')
							$texte .= ' ' ;
						$valTemp = $flt->FormatTexte() ;
						$texte .= $valTemp ;
					}
				}
				$val->MotsCle = popularKeywords($texte) ;
				$val->Description = trim(intro($texte, 250)) ;
				return $val ;
			}
			public function AppliqueAvantExecution(& $cmd, & $form, & $entite)
			{
				if($form->Editable == 1 && $entite->AccepterAttrsMeta)
				{
					$val = $this->CalculeMetas($form, $entite) ;
					if($entite->FltFrmElemMotsClesMeta->Lie() == '')
					{
						$entite->FltFrmElemMotsClesMeta->ValeurParDefaut = join(", ", $val->MotsCle) ;
						$entite->FltFrmElemMotsClesMeta->DejaLie = 0 ;
						$entite->FltFrmElemMotsClesMeta->NePasLierParametre = 1 ;
					}
					if($entite->FltFrmElemDescriptionMeta->Lie() == '')
					{
						$entite->FltFrmElemDescriptionMeta->ValeurParDefaut = $val->Description ;
						$entite->FltFrmElemDescriptionMeta->DejaLie = 0 ;
						$entite->FltFrmElemDescriptionMeta->NePasLierParametre = 1 ;
						
					}
					// echo "Mots cles - ".$entite->FltFrmElemDescriptionMeta->IDInstanceCalc." : ".$entite->FltFrmElemDescriptionMeta->Lie()."<br>" ;
				}
			}
			public function AppliqueApresExecution(& $cmd, & $form, & $entite)
			{
			}
		}
		
		class CmdEditEntiteBaseSws extends PvCommandeEditionElementBase
		{
			public $InscrireLienAnnuler = 0 ;
			public $InscrireLienReprendre = 1 ;
			public function & ObtientEntitePage()
			{
				return $this->ScriptParent->ObtientEntitePage() ;
			}
			public function & ObtientModulePage()
			{
				return $this->ScriptParent->ObtientModulePage() ;
			}
			public function CreeFournDonnees()
			{
				return ReferentielSws::$SystemeEnCours->CreeFournDonnees() ;
			}
			public function & ObtientBDSupport()
			{
				return ReferentielSws::$SystemeEnCours->BDSupport ;
			}
			public function ExecuteInstructions()
			{
				$entite = $this->ObtientEntitePage() ;
				$entite->AdaptFltsEdition->AppliqueAvantExecution($this, $this->FormulaireDonneesParent, $entite) ;
				$entite->AppliqueImplemsAvantCmd("edit_entite_".$this->Mode, $this) ;
				parent::ExecuteInstructions() ;
				$entite->AppliqueImplemsApresCmd("edit_entite_".$this->Mode, $this) ;
				$entite->AdaptFltsEdition->AppliqueApresExecution($this, $this->FormulaireDonneesParent, $entite) ;
				if($this->StatutExecution == 1)
				{
					$this->CacherFormulaireFiltresSiSucces = 1 ;
					if($entite->InclureScriptLst == 1)
					{
						$this->InscrireLienAnnuler = 1 ;
						$this->UrlLienAnnuler = $entite->ScriptListage->ObtientUrl() ;
					}
				}
			}
		}
		class CmdAjoutEntiteSws extends CmdEditEntiteBaseSws
		{
			public $Mode = 1 ;
		}
		class CmdModifEntiteSws extends CmdEditEntiteBaseSws
		{
			public $Mode = 2 ;
		}
		class CmdSupprEntiteSws extends CmdEditEntiteBaseSws
		{
			public $Mode = 3 ;
		}
		
		class ActionEnvoiFichierBaseZoneSws extends PvActionEnvoiFichierBaseZoneWeb
		{
			public $NomModulePage ;
			public $NomEntitePage ;
			protected function & ObtientModulePage()
			{
				$modulePage = ReferentielSws::$SystemeEnCours->ObtientModulePageParNom($this->NomModulePage) ;
				return $modulePage ;
			}
		}
		class ActionFluxRSSBaseSws extends ActionEnvoiFichierBaseZoneSws
		{
			public $TypeMime = "application/rss+xml" ;
			public $Titre = "" ;
			public $ExtensionFichierAttache = "rss" ;
			public $VersionXML = "1.0" ;
			public $VersionRSS = "2.0" ;
			public $Encodage = "utf-8" ;
			public $UtiliserFichierSource = 1 ;
			public $ElemsLien = array() ;
			protected function AfficheEntetes()
			{
				$modulePage = $this->ObtientModulePage() ;
				$this->NomFichierAttache = $modulePage->ObtientNomActionFluxRSS() ;
				parent::AfficheEntetes() ;
			}
			protected function AfficheContenu()
			{
				$this->PrepareDoc() ;
				$this->AfficheDebutDoc() ;
				$this->AfficheChaineZone() ;
				// $this->AfficheModulePage() ;
				$this->AfficheCorpsDoc() ;
				$this->AfficheFinDoc() ;
			}
			public function CreeElemLien()
			{
				return new ElemLienFluxRSSSws() ;
			}
			public function InscritElemLienLgn($lgn)
			{
				$elem = $this->CreeElemLien() ;
				$elem->ImporteLgn($lgn) ;
				$this->ElemsLien[] = $elem ;
			}
			protected function PrepareDoc()
			{
			}
			protected function AfficheDebutDoc()
			{
				echo '<?xml version="'.$this->VersionXML.'" encoding="'.$this->Encodage.'"?>
<rss version="'.$this->VersionRSS.'">
<channel>'.PHP_EOL ;
			}
			protected function AfficheCorpsDoc()
			{
				foreach($this->ElemsLien as $i => $elem)
				{
					echo $elem->ContenuRSS().PHP_EOL ;
				}
			}
			protected function AfficheFinDoc()
			{
				echo '</channel>
</rss>' ;
			}
			protected function AfficheChaineZone()
			{
				$titre = '' ;
				if($this->ZoneParent->ScriptAppele->TitreDocument != '')
					$titre = $this->ZoneParent->ScriptAppele->TitreDocument ;
				if($titre == '' && $this->ZoneParent->Titre != '')
					$titre = $this->ZoneParent->Titre ;
				if($titre == '' && $this->Titre != '')
					$titre = $this->Titre ;
				if($titre != "")
				{
					echo '<title><![CDATA['.strip_tags($titre).']]></title>'.PHP_EOL ;
				}
				$description = '' ;
				if($this->ZoneParent->ScriptAppele->MotsCleMeta != '')
				{
					$description .= $this->ZoneParent->ScriptAppele->MotsCleMeta ;
				}
				if($this->ZoneParent->ScriptAppele->DescriptionMeta != '')
				{
					if($description != '')
						$description .= ' : ' ;
					$description .= $this->ZoneParent->ScriptAppele->DescriptionMeta ;
				}
				if($description != "")
				{
					echo '<description><![CDATA['.$description.']]></description>'.PHP_EOL ;
				}
				echo '<link>'.htmlentities($this->ZoneParent->ObtientUrl()).'</link>'.PHP_EOL ;
			}
		}
		class ActionFluxRSSModuleSws extends ActionFluxRSSBaseSws
		{
		}
		
		class ElemLienFluxRSSSws
		{
			public $Titre ;
			public $Description ;
			public $Url ;
			public $DatePubl ;
			public $Image ;
			public function ImporteLgn($lgn)
			{
				if(isset($lgn["titre"]))
					$this->Titre = $lgn["titre"] ;
				$this->Description = "" ;
				if(isset($lgn["description"]))
				{
					if($this->Description != "")
					{
						$this->Description .= " " ;
					}
					$this->Description .= $lgn["description"] ;
				}
				if(isset($lgn["url"]))
					$this->Url = $lgn["url"] ;
				if(isset($lgn["image"]))
					$this->Image = $lgn["image"] ;
				if(isset($lgn["date_publication"]))
				{
					$this->DatePubl = $lgn["date_publication"] ;
					if(isset($lgn["heure_publication"]))
					{
						$this->DatePubl .= " ".$lgn["heure_publication"] ;
					}
				}
			}
			public function ContenuRSS()
			{
				$ctn = '' ;
				$ctn .= '<item>'.PHP_EOL ;
				$ctn .= '<title><![CDATA['.$this->ObtientTitreRSS().']]></title>'.PHP_EOL ;
				$ctn .= '<description><![CDATA['.$this->ObtientDescriptionRSS().']]></description>'.PHP_EOL ;
				$ctn .= '<link>'.htmlentities($this->Url).'</link>'.PHP_EOL ;
				if($this->DatePubl != '')
				{
					$ctn .= '<pubDate>'.$this->DatePubl.'</pubDate>'.PHP_EOL ;
				}
				if($this->Image != '')
				{
					$ctn .= '<image>'.$this->Image.'</image>'.PHP_EOL ;
				}
				$ctn .= '</item>' ;
				return $ctn ;
			}
			protected function ObtientTitreRSS()
			{
				$val = strip_tags($this->Titre) ;
				if(strlen($val) > 255)
					$val = substr($val, 0, 255)."..." ;
				// return utf8_encode($val) ;
				return $val	;
			}
			protected function ObtientDescriptionRSS()
			{
				$description = strip_tags($this->Description) ;
				if(strlen($description) > 255)
					$description = substr($description, 0, 255)."..." ;
				return $description ;
			}
		}
		
		class CritrCodeSecurValideEntiteSws extends PvCritereBase
		{
			public $MessageErreur = "Le code de s&eacute;curit&eacute; saisi est incorrect" ;
			public function EstRespecte()
			{
				if($this->FormulaireDonneesParent->Editable == 0)
				{
					return 1 ;
				}
				if($this->ScriptParent->EntitePage->SecuriserEdition == 0)
				{
					return 1 ;
				}
				$ok = $this->ScriptParent->EntitePage->FltCaptcha->Composant->ActionAffichImg->VerifieValeurSoumise($this->ScriptParent->EntitePage->FltCaptcha->Lie()) ;
				return $ok ;
			}
		}
	}
	
?>