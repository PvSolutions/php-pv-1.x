<?php
	
	if(! defined('NOYAU_WSM'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/../Pv/IHM/Compose.class.php" ;
		}
		if(! defined('NOYAU_BD_WSM'))
		{
			include dirname(__FILE__)."/BaseDonnees/Base.class.php" ;
		}
		if(! defined('ZONE_WSM'))
		{
			include dirname(__FILE__)."/Zone.class.php" ;
		}
		define('NOYAU_WSM', 1) ;
		
		class SystemeWsm extends PvObjet
		{
			public $BaseDonnees = null ;
			public $ApplicationParent = null ;
			public $CheminIconePageParDefaut = 'images/default.png' ;
			public $CheminLogo = 'images/logo.png' ;
			public $TotalPages = 0 ;
			public $TotalRelPages = 0 ;
			public $StatsMdlsPageGlobaux = array() ;
			public $NomMdlsPageGlobaux = array() ;
			protected function ObtientNomMdlsPageGlobaux()
			{
				$mdlPageGlobaux = array() ;
				foreach($this->BaseDonnees->ModelesPage as $nomMdl => $mdl)
				{
					if($mdl->EstVarGlobal && ! in_array($nomMdl, $mdlPageGlobaux))
					{
						$mdlPageGlobaux[] = $nomMdl ;
					}
				}
				return $mdlPageGlobaux ;
			}
			protected function CalculeStatsMdlsPageGlobaux()
			{
				$this->NomMdlsPageGlobaux = $this->ObtientNomMdlsPageGlobaux() ;
				$this->StatsMdlsPageGlobaux = array() ;
				if(count($this->NomMdlsPageGlobaux) == 0)
					return ;
				$sql = 'select min(id_page) id_prem_page, count(0) total_occur, template_name_page nom_modele from '.$this->BaseDonnees->Prefixe.'page where template_name_page in (' ;
				$params = array() ;
				foreach($this->NomMdlsPageGlobaux as $i => $nomMdl)
				{
					$statMdlPageGlobal = new StatMdlPageGlobal() ;
					$statMdlPageGlobal->Nom = $nomMdl ;
					$this->StatsMdlsPageGlobaux[$nomMdl] = $statMdlPageGlobal ;
					$params['nomModele_'.$i] = $nomMdl ;
				}
				$sql .= $this->BaseDonnees->ParamPrefix."nomModele_".join(", ".$this->BaseDonnees->ParamPrefix."nomModele_", array_keys($this->NomMdlsPageGlobaux)) ;
				$sql .= ") group by template_name_page" ;
				$lignes = $this->BaseDonnees->FetchSqlRows($sql, $params) ;
				foreach($lignes as $i => $ligne)
				{
					$nomMdl = $ligne["nom_modele"] ;
					$this->StatsMdlsPageGlobaux[$nomMdl]->PremIdPage = $ligne["id_prem_page"] ;
					$this->StatsMdlsPageGlobaux[$nomMdl]->TotalOcurrences = $ligne["total_occur"] ;
				}
				// print_r($this->StatsMdlsPageGlobaux) ;
			}
			public function CalculeVarsGlobales()
			{
				$this->TotalPages = $this->BaseDonnees->fetchSqlValue('select count(0) total from '.$this->BaseDonnees->Prefixe.'page', array(), 'total') ;
				$this->TotalRelPages = $this->BaseDonnees->fetchSqlValue('select count(0) total from '.$this->BaseDonnees->Prefixe.'rel_page', array(), 'total') ;
				$this->CalculeStatsMdlsPageGlobaux() ;
			}
			public function CreeBaseDonnees()
			{
				return new BaseDonneesWsm() ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeBaseDonnees() ;
				$this->ChargeConfigSuppl() ;
				if($this->ApplicationParent->CompatibleV1)
				{
					$this->ChargeConfigV1() ;
				}
			}
			protected function ChargeConfigSuppl()
			{
			}
			public function ChargeConfigV1()
			{
				if(! isset($GLOBALS["__conf"]))
					return ;
				global $__conf ;
				$this->BaseDonnees->ConnectionParams["server"] = $__conf["db"]["host"] ;
				$this->BaseDonnees->ConnectionParams["user"] = $__conf["db"]["user"] ;
				$this->BaseDonnees->ConnectionParams["password"] = $__conf["db"]["password"] ;
				$this->BaseDonnees->ConnectionParams["schema"] = $__conf["db"]["dbname"] ;
				$this->BaseDonnees->Prefixe = $__conf["db"]["table_prefix"] ;
			}
			public function DeclareBaseDonnees($bd)
			{
				$this->BaseDonnees = $bd ;
				$this->BaseDonnees->AdopteSysteme($this) ;
				$this->BaseDonnees->ChargeConfig() ;
			}
			protected function ChargeBaseDonnees()
			{
				if($this->EstNul($this->BaseDonnees))
				{
					$this->DeclareBaseDonnees($this->CreeBaseDonnees()) ;
				}
			}
			public function AdopteApplication(& $app)
			{
				$this->ApplicationParent = & $app ;
			}
			public function CreeFournisseurDonnees()
			{
				$fourn = new PvFournisseurDonneesSql() ;
				$fourn->BaseDonnees = & $this->BaseDonnees ;
				return $fourn ;
			}
		}
		
		class StatMdlPageGlobal
		{
			public $Nom ;
			public $PremIdPage ;
			public $TotalOcurrences = 0 ;
		}
	}
	
?>