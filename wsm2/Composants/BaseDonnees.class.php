<?php
	
	if(! defined('COMP_BASE_DONNEES_WSM'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/../../../../_PVIEW/Pv/IHM/Compose.class.php" ;
		}
		define('COMP_BASE_DONNEES_WSM', 1) ;
		
		class ExtracteurIntroWsm extends PvExtracteurValeursDonneesBase
		{
			public $MaxMots = 255 ;
			public $ExprPlus = "..." ;
			public $AccepteValeursVide = 1 ;
			protected function DecodeValeurs($texte, & $composant)
			{
				$valeurs = array("intro" => intro($texte, $this->MaxMots, $this->ExprPlus)) ;
				return $valeurs ;
			}
		}
		
		class GrilleDonneesBaseWsm extends PvGrilleDonneesHtml
		{
			public $BDWsm = null ;
			public $SystemeWsm = null ;
			public function AdopteScript($nom, & $script)
			{
				parent::AdopteScript($nom, $script) ;
				$this->SystemeWsm = & $this->ApplicationParent->Systeme ;
				$this->BDWsm = & $this->ApplicationParent->Systeme->BaseDonnees ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeConfigSpec() ;
			}
			protected function ChargeConfigSpec()
			{
			}
		}
		class GrilleDonneesPagesBaseWsm extends GrilleDonneesBaseWsm
		{
			public $TriPossible = 0 ;
			public $UtiliserPageEnCours = 1 ;
			public $IdPageEnCours = 0 ;
			public $PourIdPageAffich = 0 ;
			public $ExprIdPageEnCours = "" ;
			public $SeulesPagesPubliees = 1 ;
			public $AttrsPage = array('id_page', 'title_page', 'short_title_page', 'path_page', 'path_title_page', 'date_publish_page', 'search_text_page', 'summary_page') ;
			public $InscrireDefColsPage = 1 ;
			public $OrdrePagesParDefaut = 'date_publish_page desc, time_publish_page desc' ;
			public $UtiliserOrdrePagesParDefaut = 1 ;
			public $NePasTrier = 1 ;
			public $ExtracteursIntroPage = array() ;
			protected function RequeteSelectPages()
			{
				return 'select * from '.$this->BDWsm->Prefixe.'page' ;
			}
			public function & InsereFltSelectIdPageAffich($nomFlt, $expr='')
			{
				$flt = $this->InsereFltSelectFixe($nomFlt, $this->ScriptParent->PageAffich->Id, $expr) ;
				return $flt ;
			}
			public function & InsereFltSelectPage($nomFlt, & $page, $expr='')
			{
				$flt = $this->InsereFltSelectFixe($nomFlt, $page->Id, $expr) ;
				return $flt ;
			}
			protected function ChargeConfigSpec()
			{
				$this->ChargeDefColsAuto() ;
				$this->ChargeFltsSelectAuto() ;
				$this->ChargeFournDonnees() ;
				$this->ChargeNavigateurRangees() ;
				if(isset($this->ScriptParent->PageAffich))
				{
					$this->ParamsGetSoumetFormulaire[] = $this->ScriptParent->NomParamIdPageAffich ;
				}
			}
			protected function ChargeNavigateurRangees()
			{
				$this->NavigateurRangees = new PvNavTableauDonneesHtml() ;
			}
			protected function ChargeDefColsAuto()
			{
				if(! $this->InscrireDefColsPage)
					return ;
				$this->InsereTablDefsCol($this->AttrsPage) ;
				foreach($this->DefinitionsColonnes as $i => & $col)
				{
					if(in_array($col->NomDonnees, array('title_page', 'search_summary_page', 'search_text_page')))
					{
						$col->ExtracteurValeur = new ExtracteurIntroWsm() ;
						$this->ExtracteursIntroPage[$col->NomDonnees] = & $col->ExtracteurValeur ;
					}
				}
			}
			protected function ChargeFournDonnees()
			{
				$this->FournisseurDonnees = $this->SystemeWsm->CreeFournisseurDonnees() ;
				$sql = $this->RequeteSelectPages() ;
				if($this->UtiliserOrdrePagesParDefaut && $this->OrdrePagesParDefaut != '')
					$sql .= ' order by '.$this->OrdrePagesParDefaut ;
				$this->FournisseurDonnees->RequeteSelection = "(".$sql.")" ;
			}
			protected function ChargeFltsSelectAuto()
			{
				if($this->PourIdPageAffich && $this->ExprIdPageEnCours != "")
				{
					$this->InsereFltSelectIdPageAffich("idPageAffich", $this->ExprIdPageEnCours) ;
				}
				elseif($this->UtiliserPageEnCours && $this->IdPageEnCours > -1 && $this->ExprIdPageEnCours != "")
				{
					$this->InsereFltSelectFixe("idPageEnCours", $this->IdPageEnCours, $this->ExprIdPageEnCours) ;
				}
				if($this->SeulesPagesPubliees)
				{
					$this->InsereFltSelectFixe("statutPublOK", 1, 'is_publish_page=<self>') ;
				}
			}
			protected function RenduDispositifBrut()
			{
				$ctn = parent::RenduDispositifBrut() ;
				// print $this->FournisseurDonnees->BaseDonnees->LastSqlText ;
				return $ctn ;
			}
		}
		class GrilleDonneesSousPagesWsm extends GrilleDonneesPagesBaseWsm
		{
			public $ExprIdPageEnCours = 'id_page_parent_page=<self>' ;
			protected function RequeteSelectPages()
			{
				return 'select * from '.$this->BDWsm->Prefixe.'page' ;
			}
			protected function ChargeConfigSpec()
			{
				parent::ChargeConfigSpec() ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = parent::RenduDispositifBrut() ;
				return $ctn ;
			}
		}
		class GrilleDonneesPagesSecteurWsm extends GrilleDonneesPagesBaseWsm
		{
			public $ExprIdPageEnCours = 'id_page_parent_page=<self>' ;
			public $IdPageParentEnCours = 0 ;
			protected $PourIdPageAffichTemp = 0 ;
			public $UtiliserPageEnCours = 0 ;
			protected function ChargeConfigSpec()
			{
				$this->UtiliserPageEnCours = 0 ;
				$this->PourIdPageAffichTemp = $this->PourIdPageAffich ;
				if($this->PourIdPageAffich)
				{
					if(isset($this->ScriptParent->PageAffich->Id))
						$this->IdPageEnCours = $this->ScriptParent->PageAffich->Id ;
					$this->PourIdPageAffich = 0 ;
				}
				parent::ChargeConfigSpec() ;
				$this->PourIdPageAffich = $this->PourIdPageAffichTemp ;
			}
			protected function CalculeIdPageParent()
			{
				$sql = 'select * from '.$this->BDWsm->Prefixe.'page where id_page='.$this->BDWsm->ParamPrefix.'idPage' ;
				$pageTemp = $this->BDWsm->FetchSqlRow($sql, array('idPage' => $this->IdPageEnCours)) ;
				$this->IdPageParentEnCours = -1 ;
				if(count($pageTemp) > 0)
				{
					$this->IdPageParentEnCours = $pageTemp["id_page_parent_page"] ;
				}
				if($this->IdPageParentEnCours > -1)
				{
					$this->InsereFltSelectFixe('idPageParentEnCours', $this->IdPageParentEnCours, 'id_page_parent_page=<self>') ;
					return 1 ;
				}
				return 0 ;
			}
			protected function RenduDispositifBrut()
			{
				$this->CalculeIdPageParent() ;
				$ctn = parent::RenduDispositifBrut() ;
				return $ctn ;
			}
		}
		
		class CompResultatExecBDWsm extends PvComposantIUBase
		{
			public $NomBD = '' ;
			public $DetecterBDParNom = 1 ;
			public $BD ;
			public $MsgBDNonTrouvee = "La base de donnee n'a pas ete definie" ;
			public $CacherSiNonEchec = 1 ;
			protected function RenduBDNonTrouvee()
			{
				return '<div class="Erreur">'.$this->MsgBDNonTrouvee.'</div>' ;
			}
			protected function RenduDispositifBrut()
			{
				$bd = $this->BD ;
				// print count($this->ZoneParent->ApplicationParent->BaseDonnees).' jjj' ;
				if($this->DetecterBDParNom && $this->EstNul($this->BD) && (isset($this->ApplicationParent->BaseDonnees[$this->NomBD])))
				{
					$bd = $this->ApplicationParent->BaseDonnees[$this->NomBD] ;
				}
				if($this->EstNul($bd))
				{
					return $this->RenduBDNonTrouvee() ;
				}
				$ctn = '' ;
				if($this->CacherSiNonEchec && $bd->ConnectionException == "")
				{
					return $ctn ;
				}
				$ctn .= '<div class="Erreur">'.PHP_EOL ;
				$ctn .= '<div><b>Exception : </b>'.htmlentities($bd->ConnectionException).'</div>'.PHP_EOL ;
				$ctn .= '<div><b>SQL : </b>'.htmlentities($bd->LastSqlText).'</div>'.PHP_EOL ;
				$ctn .= '<div><b>Params : </b>'.htmlentities(svc_json_encode($bd->LastSqlParams)).'</div>'.PHP_EOL ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
	}
	
?>