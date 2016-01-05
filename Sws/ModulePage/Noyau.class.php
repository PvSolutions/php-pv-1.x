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
			public $MenuRacine ;
			public $NomActionFluxRSS = "rss" ;
			public $ActionFluxRSS ;
			public $FournitFluxRSS = 0 ;
			public $MaxElemsFluxRSS = 30 ;
			public function SqlSelectFluxRSS(& $bd)
			{
			}
			public function ParamsSelectFluxRSS(& $bd)
			{
				return array() ;
			}
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
			}
			public function EnvoieContenuFluxRSS(& $action)
			{
				$bd = $this->ObtientBDSupport() ;
				$sql = $this->SqlSelectFluxRSS($bd) ;
				if($sql != "")
				{
					$lgns = $bd->FetchSqlRows($sql, $this->ParamsSelectFluxRSS($bd)) ;
					foreach($lgns as $i => $lgn)
					{
					}
				}
				// foreach($this->)
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
			}
			public function InsereActionAvantRendu($nomAction, $action, & $zone)
			{
				$this->InsereAction($nomAction, $action, $zone) ;
			}
			public function InsereActionApresRendu($nomAction, $action, & $zone)
			{
				$action->NomModulePage = $this->NomElementSyst ;
				$zone->InscritActionApresRendu($nomAction, $action) ;
			}
			public function & InsereScript($nom, $script, & $zone, $privs=array())
			{
				$this->InscritScript($nom, $script, $zone, $privs) ;
				return $script;
			}
			public function InscritNouvScript($nom, $script, & $zone, $privs=array())
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
		}
		class ModulePageIndefiniSws extends ModulePageBaseSws
		{
			public $NomRef = "indefini" ;
			public $EstIndefini = 1 ;
		}
		
		class DefFluxRSSEntiteSws
		{
			public $Active = 0 ;
			public $NomTable ;
			public $NomColId ;
			public $NomColTitre ;
			public $NomColDescription ;
			public $NomColCheminImage ;
			public $NomColCheminVideo ;
			public $NomColCheminFichier ;
			public function SqlListeCols(& $entite, & $bd)
			{
				$sql = '' ;
				$sql .= "'".$entite->ModuleParent->NomElementSyst."' nom_module" ;
				$sql .= "'".$entite->NomElementModule."' nom_entite" ;
				$sql .= (($this->NomColTitre == "") ? $bd->EscapeVariableName($this->NomColTitre) : "''").' titre' ;
				$sql .= (($this->NomColDescription == "") ? $bd->EscapeVariableName($this->NomColDescription) : "''").' description' ;
				$sql .= (($this->NomColCheminImage == "") ? $bd->EscapeVariableName($this->NomColCheminImage) : "''").' chemin_image' ;
				$sql .= (($this->NomColCheminVideo == "") ? $bd->EscapeVariableName($this->NomColCheminVideo) : "''").' chemin_video' ;
				$sql .= (($this->NomColCheminFichier == "") ? $bd->EscapeVariableName($this->NomColCheminFichier) : "''").' chemin_fichier' ;
				return $sql ;
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
			public $DefFluxRSS ;
			public $AdaptFltsEdition ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DefFluxRSS = new DefFluxRSSEntiteSws() ;
				$this->InitAdaptFltsEdition() ;
			}
			protected function InitAdaptFltsEdition()
			{
				$this->AdaptFltsEdition = new AdaptFltsInactifEntiteSws() ;
			}
			public function ObtientTitreMenu()
			{
				return ($this->TitreMenu == "") ? ($this->Titre == "") ? $this->PrefixeTitreMenu." ".$this->NomElementModule : $this->Titre : $this->TitreMenu ;
			}
			public function ObtientReqSqlFluxRSS()
			{
				$bd = $this->ObtientBDSupport() ;
				$sql = 'select '.$this->DefFluxRSS->SqlListeCols($this, $bd) ;
				$sql .= ' from '.$bd->EscapeVariableName($this->DefFluxRSS->NomTable) ;
				return $sql ;
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
			protected function ChargeBarreElemsRendu(& $barreMenu)
			{
				$barreMenu->InclureRenduIcone = 0 ;
				$barreMenu->MenuRacine->InscritSousMenuUrl("Espace publique", $this->ModuleParent->SystemeParent->ObtientUrlZonePubl($barreMenu->ZoneParent)) ;
				$barreMenu->MenuRacine->InscritSousMenuUrl("Modules", $this->ModuleParent->SystemeParent->ObtientUrlAdminPremModule()) ;
				$barreMenu->MenuRacine->InscritSousMenuUrl("Impl&eacute;mentations", $this->ModuleParent->SystemeParent->ObtientUrlAdminPremImplem()) ;
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
				$this->ModuleParent->InscritScript($nom, $script, $zone, $privs);
			}
			protected function & ObtientBDSupport()
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
						$implemsPage[$nomImplemsPage] = & $implemPage ;
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
			public $LibConsultTblList = "Consulter" ;
			public $LibId = "ID" ;
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
			public $DefColTblListTitre ;
			public $DefColTblListActs ;
			public $CmdAjoutTblList ;
			public $LienModifTblList ;
			public $LienSupprTblList ;
			public $LienConsultTblList ;
			public $BlocConsult ;
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
			public $LargeurFenEditEntite = 750 ;
			public $HauteurFenEditEntite = 525 ;
			public $ActiverFenEditEntite = 0 ;
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
				$sql .= $bd->EscapeVariableName($this->TableSelectLgn()) ;
				$sql .= ' where '.$bd->EscapeVariableName($this->NomColId).' = '.$bd->ParamPrefix.'idEntite' ;
				return $sql ;
			}
			public function SelectLgn($id)
			{
				$bd = $this->ObtientBDSupport() ;
				$lgn = $bd->FetchSqlRow($this->SqlSelectLgn(), array("idEntite" => $id)) ;
				return $lgn ;
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
				}
				if($this->InclureScriptEdit)
				{
					$this->ScriptAjout = $this->InsereScript($this->NomScriptAjout.'_'.$this->NomEntite, $this->CreeScriptAjout(), $zone, $this->ObtientPrivilegesEdit()) ;
					$this->ScriptModif = $this->InsereScript($this->NomScriptModif.'_'.$this->NomEntite, $this->CreeScriptModif(), $zone, $this->ObtientPrivilegesEdit()) ;
					$this->ScriptPositionPubl = $this->InsereScript($this->NomScriptPositionPubl.'_'.$this->NomEntite, $this->CreeScriptPositionPubl(), $zone, $this->ObtientPrivilegesEdit()) ;
					$this->ScriptSuppr = $this->InsereScript($this->NomScriptSuppr.'_'.$this->NomEntite, $this->CreeScriptSuppr(), $zone, $this->ObtientPrivilegesEdit()) ;
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
			protected function & InsereTblList(& $script)
			{
				$tbl = $this->CreeTblList() ;
				$this->InitTblList($tbl, $script) ;
				$tbl->AdopteScript('tblList', $script) ;
				$tbl->ChargeConfig() ;
				$this->ChargeTblList($tbl) ;
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
				$this->InscritBarreMenu($script) ;
				$this->InscritBarreElemsRendu($script) ;
				$this->InscritBarreMenuEntite($script) ;
			}
			protected function PrepareScriptEdit(& $script)
			{
				$this->PrepareScriptAdmin($script) ;
				// $this->InscritFilAriane($script) ;
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
				// Fournisseur de données
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
				$frm->RedirigeAnnulerVersScript($this->NomScriptListage."_".$this->NomEntite) ;
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
				$ctnModele = '' ;
				foreach($this->LgnEnCours as $nom => $val)
				{
					$ctnModele .= '<div>${'.$nom.'}</div>' ;
				}
				$bloc->Params = $this->LgnEnCours ;
				$bloc->Contenu = $ctnModele ;
			}
			protected function CreeTblList()
			{
				return new PvTableauDonneesHtml() ;
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
				// Fournisseur de données
				$tbl->FournisseurDonnees = $this->ModuleParent->CreeFournDonnees() ;
				$tbl->FournisseurDonnees->RequeteSelection = $this->NomTable ;
				$this->DefColTblListId = $tbl->InsereDefCol($this->NomColId, $this->LibId) ;
				$this->DefColTblListId->Largeur = "5%" ;
				$this->DefColTblListId->AlignElement = "right" ;
				if($this->AccepterAttrsEdition)
				{
					$this->DefColTblListDatePubl = $tbl->InsereDefCol($this->NomColDatePubl, $this->LibDatePubl, $bd->SqlConcat(array($bd->SqlDateToStrFr($bd->EscapeVariableName($this->NomColDatePubl)), "' '", $bd->EscapeVariableName($this->NomColHeurePubl)))) ;
					$this->DefColTblListDatePubl->Largeur = "10%" ;
					$this->DefColTblListDatePubl->AlignElement = "center" ;
					$this->DefColTblListDateModif = $tbl->InsereDefCol($this->NomColDateModif, $this->LibDateModif, $bd->SqlDateToStrFr($bd->EscapeVariableName($this->NomColDateModif), 1)) ;
					$this->DefColTblListDateModif->Largeur = "10%" ;
					$this->DefColTblListDateModif->AlignElement = "center" ;
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
					if($this->AccepterAttrsEdition)
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
				$this->DefColTblListActs = $tabl->InsereDefColActions($this->LibActions) ;
				if($this->AccepterAttrsEdition == 1)
				{
					$this->LienModifTblList = $tabl->InsereLienAction($this->DefColTblListActs, $this->ScriptModif->ObtientUrlFmt(array($this->NomParamId => '${'.$this->NomColId.'}')), $this->LibModifTblList) ;
					$this->LienSupprTblList = $tabl->InsereLienAction($this->DefColTblListActs, $this->ScriptSuppr->ObtientUrlFmt(array($this->NomParamId => '${'.$this->NomColId.'}')), $this->LibSupprTblList) ;
					if($this->InclureScriptConsult && $this->ModuleParent->SystemeParent->ObtientUrlZonePubl($tabl->ZoneParent) != '')
					{
						$this->LienConsultTblList = $tabl->InsereLienAction($this->DefColTblListActs, $this->ModuleParent->SystemeParent->ObtientUrlZonePubl($tabl->ZoneParent).'?'.urlencode($tabl->ZoneParent->NomParamScriptAppele).'='.urlencode($this->NomScriptConsult).'_'.urlencode($this->NomEntite).'&'.urlencode($this->NomParamId).'=${'.$this->NomColId.'}', $this->LibConsultTblList) ;
						$this->LienConsultTblList->FenetreCible = "_blank" ;
					}
					$this->CmdAjoutTblList = new PvCommandeRedirectionHttp() ;
					$this->CmdAjoutTblList->NomScript = $this->ScriptAjout->NomElementZone ;
					$this->CmdAjoutTblList->Libelle = $this->LibAjoutTblList ;
					$tabl->InscritCommande("ajoutEntite", $this->CmdAjoutTblList) ;
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
				$ctn .= $this->FrmElem->RenduDispositif().PHP_EOL ;
				$ctn .= $this->RenduApresCtnSpec($script) ;
				return $ctn ;
			}
			public function RenduScriptConsult(& $script)
			{
				$ctn = '' ;
				$ctn .= $this->BlocConsult->RenduDispositif() ;
				return $ctn ;
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
			public function NomParamsTexte()
			{
				return array($this->NomColTitre) ;
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
			public $ContenuModeleConsult = '<div>${titre}</div>
<div>${description}</div>' ;
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
				$bloc->ContenuModele = $this->ContenuModeleConsult ;
			}
			protected function ChargeBlocConsult(& $bloc)
			{
				$bloc->FournisseurDonnees = $bloc->ScriptParent->CreeFournDonnees() ;
				$bloc->RequeteSelection = $this->SqlSelectLgn() ;
				$bloc->ParamsSelection = array('idEntite' => $this->LgnEnCours["id"]) ;
			}
			public function RemplitScriptConsult(& $script)
			{
				if($this->VerifPreReqsScriptConsult($script))
				{
					parent::RemplitScriptConsult($script) ;
				}
				else
				{
					if($this->RedirScriptConsultIndisp)
					{
						$script->ZoneParent->RedirigeVersScript($script->ZoneParent->ObtientScriptParDefaut()) ;
					}
					else
					{
						$this->BlocConsult = new PvPortionRenduHtml() ;
						$this->BlocConsult->AdopteScript("blocConsult", $script) ;
						$this->BlocConsult->Contenu = $this->MsgScriptConsultIndisp ;
					}
				}
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
					// Image
					$this->FltFrmElemImage = $frm->InsereFltEditHttpUpload($this->NomParamCheminImage, $this->ModuleParent->SystemeParent->CheminAdminVersPubl."/".$this->CheminTelechargImages, $this->NomColCheminImage) ;
					$this->FltFrmElemImage->Libelle = $this->LibCheminImage ;
					// Bannière
					$this->FltFrmElemBanniere = $frm->InsereFltEditHttpUpload($this->NomParamCheminBanniere, $this->ModuleParent->SystemeParent->CheminAdminVersPubl."/".$this->CheminTelechargBannieres, $this->NomColCheminBanniere) ;
					$this->FltFrmElemBanniere->Libelle = $this->LibCheminBanniere ;
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
			public function & ObtientEntitePage()
			{
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				return $entite ;
			}
			public function & ObtientModulePage()
			{
				$module = $this->ScriptParent->ObtientModulePage() ;
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
				if($this->AccepterAttrsTexte)
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
			}
		}

		class ScriptEntiteTableBaseSws extends ScriptBaseSws
		{
		}
		class ScriptListageEntiteTableSws extends ScriptEntiteTableBaseSws
		{
			public $NecessiteMembreConnecte = 1 ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->EntitePage->RemplitScriptLst($this) ;
			}
			protected function RenduDispositifBrut()
			{
				return $this->EntitePage->RenduScriptLst($this) ;
			}
		}
		
		class ScriptElemEntiteTableSws extends ScriptEntiteTableBaseSws
		{
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
			public $NecessiteMembreConnecte = 1 ;
			public $InitFrmElem ;
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
		class ScriptAjoutEntiteTableSws extends ScriptEditEntiteTableSws
		{
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
				$this->AfficheModulePage() ;
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
			protected function AfficheModulePage()
			{
				$modulePage = $this->ObtientModulePage() ;
				$modulePage->EnvoieContenuFluxRSS($this) ;
			}
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
				if(isset($lgn["description"]))
					$this->Description = $lgn["description"] ;
				if(isset($lgn["url"]))
					$this->Url = $lgn["url"] ;
				if(isset($lgn["image"]))
					$this->Image = $lgn["image"] ;
				if(isset($lgn["date_publication"]))
					$this->DatePubl = $lgn["date_publication"] ;
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
				return utf8_encode($val) ;
			}
			protected function ObtientDescriptionRSS()
			{
				$description = strip_tags($this->Description) ;
				if(strlen($description) > 255)
					$description = substr($description, 0, 255)."..." ;
				return utf8_encode($description) ;
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