<?php
	
	if(! defined('NOYAU_BD_WSM'))
	{
		if(! defined('PV_NOYAU'))
		{
			include dirname(__FILE__)."/../../Pv/IHM/Simple.class.php" ;
		}
		define('NOYAU_BD_WSM', 1) ;
		
		class BaseDonneesWsm extends MysqlDB
		{
			public $Prefixe = "" ;
			public $IdPageRacine = 0 ;
			public $IdPageCorbeille = 0 ;
			public $ModelesPage = array() ;
			public $ModelePageParDefaut = null ;
			public $SystemeParent ;
			public $ServeurCnx = 'localhost' ;
			public $SchemaCnx = 'wsm2' ;
			public $LoginCnx = 'root' ;
			public $MotPasseCnx = '' ;
			public function AdopteSysteme(& $systeme)
			{
				$this->SystemeParent = & $systeme ;
			}
			public function ChargeConfig()
			{
				$this->ChargeModelesPage() ;
			}
			public function CreeModelePageParDefaut()
			{
				return new ModelePageDefautWsm() ;
			}
			public function CreeModelePageRacine()
			{
				return new ModelePageRacineWsm() ;
			}
			protected function ChargeModelesPage()
			{
				$this->DeclareModelePageParDefaut($this->CreeModelePageParDefaut()) ;
				$this->DeclareModelePage("__default_page", $this->CreeModelePageParDefaut()) ;
				$this->DeclareModelePage("__top_page", $this->CreeModelePageRacine()) ;
				$this->DeclareModelePage("index", $this->CreeModelePageParDefaut()) ;
				$this->DeclareModelePage("reviews", new MdlPageSystCommentsWsm()) ;
				$this->DeclareModelePage("review", new MdlPageCommentaireWsm()) ;
			}
			public function DeclareModelePageParDefaut($modele)
			{
				$this->ModelePageParDefaut = $modele ;
			}
			public function DeclareModelePage($nomModele, $modele)
			{
				$modele->NomElementBD = $nomModele ;
				$this->ModelesPage[$nomModele] = $modele ;
			}
			public function DeclareModelesPage($defModeles = array())
			{
				foreach($defModeles as $nom => $modele)
				{
					$this->DeclareModelePage($nom, $modele) ;
				}
			}
			public function InitConnectionParams()
			{
				parent::InitConnectionParams() ;
				$this->ConnectionParams["server"] = $this->ServeurCnx ;
				$this->ConnectionParams["user"] = $this->LoginCnx ;
				$this->ConnectionParams["password"] = $this->MotPasseCnx ;
				$this->ConnectionParams["schema"] = $this->SchemaCnx ;
			}
			protected function SqlTablePages()
			{
				$sql = '' ;
				if(! $this->SystemeParent->ApplicationParent->CompatibleV1)
				{
					$sql = 'select t1.*, t3.id id_template, t3.name name_template, t3.title title_template, t3.class_name class_name_template, t3.max_instance max_instance_template, t3.max_child max_child_template, t3.register_family_page register_family_page_template, t3.register_meta_from_child_page register_meta_from_child_page_template, t3.register_range_child_page register_range_child_page_template, t3.child_page_count_for_range child_page_count_for_range_template, t3.register_range_rel_page_src register_range_rel_page_src_template, t3.rel_page_src_count_for_range rel_page_src_count_for_range_template, t3.register_range_rel_page_dest register_range_rel_page_dest_template, t3.rel_page_dest_count_for_range rel_page_dest_count_for_range_template, t3.child_page_start_param_name child_page_start_param_name_template, t3.rel_page_src_start_param_name rel_page_src_start_param_name_template, t3.rel_page_dest_start_param_name rel_page_dest_start_param_name_template from '.$this->EscapeTableName($this->Prefixe.'page').' t1' ;
				}
				else
				{
					$sql = 'select t1.* from '.$this->EscapeTableName($this->Prefixe.'page').' t1' ;
				}
				return $sql ;
			}
			public function SqlObtientPage()
			{
				$sql = 'select t1.* from ('.$this->SqlTablePages().') t1 where id_page='.$this->ParamPrefix.'idPage' ;
				// echo $sql ;
				return $sql ;
			}
			public function ObtientModelePage($nomModele)
			{
				$modele = $this->ModelePageParDefaut ;
				if(isset($this->ModelesPage[$nomModele]))
				{
					$modele = $this->ModelesPage[$nomModele] ;
				}
				return $modele ;
			}
			public function ObtientPageNonTrouve()
			{
				return new LignePageWsm() ;
			}
			public function ObtientPage($idPage)
			{
				$sql = $this->SqlObtientPage() ;
				$entite = $this->FetchSqlEntity($sql, array('idPage' => $idPage), "LignePageWsm") ;
				return $entite ;
			}
			public function SqlObtientPageFillesParId()
			{
				$sql = 'select t1.* from ('.$this->SqlTablePages().') t1 where id_page_parent_page='.$this->ParamPrefix.'idPage' ;
				return $sql ;
			}
			public function SqlObtientPageParIds($params)
			{
				$sql = 'select t1.* from ('.$this->SqlTablePages().') t1 where id_page in ('.$this->ExtractExprFromParams($params, ", ").')' ;
				return $sql ;
			}
			public function ObtientPageFillesParId($idPage)
			{
				$sql = $this->SqlObtientPageFillesParId() ;
				$entite = $this->FetchSqlEntities($sql, array('idPage' => $idPage), "LignePageWsm") ;
				return $entite ;
			}
			public function InsereLigne($ligne)
			{
				return $this->InsertRow($this->Prefixe.$ligne->TableEdition, $ligne->ValeursEdition) ;
			}
			public function AjouteLigne($ligne)
			{
				return $this->InsereLigne($ligne) ;
			}
			public function MetAJourLigne($ligne)
			{
				return $this->UpdateRow(
					$this->Prefixe.$ligne->TableEdition,
					$ligne->ValeursEdition,
					$ligne->ExpressionIdentifiant(),
					$ligne->ValeursIdentifiant()
				) ;
			}
			public function ModifLigne($ligne)
			{
				return $this->MetAJourLigne($ligne) ;
			}
			public function SupprimeLigne($ligne)
			{
				return $this->DeleteRow($this->Prefixe.$ligne->TableEdition, $ligne->ExpressionIdentifiant(), $ligne->ValeursIdentifiant()) ;
			}
			public function SupprLigne($ligne)
			{
				return $this->SupprimeLigne($ligne) ;
			}
			public function RecupLgnPageParId($idPage)
			{
				$sql = 'select * from '.$this->Prefixe.'page where id_page='.$this->ParamPrefix.'idPage' ;
				return $this->FetchSqlRow($sql, array('idPage' => $idPage)) ;
			}
		}
	
		class LigneBaseWsm extends CommonEntityRow
		{
			public $BaseDonneesParent = null ;
			public $PrefixeLigne = "" ;
			public $SuffixeLigne = "" ;
			public $TableEdition = "" ;
			public $StoreRawData = 1 ;
			public function ValeursEdition()
			{
				return array() ;
			}
			public function EstNul($objet)
			{
				if($objet == null)
					return 1 ;
				// return (get_class($objet) == "PvNul") ? 1 : 0 ;
				return (get_class($objet) == "PvNul" or get_class($objet) == get_class($this)) ? 1 : 0 ;
			}
			public function EstNonNul($objet)
			{
				return ($this->EstNul($objet)) ? 0 : 1 ;
			}
			public function EstPasNul($objet)
			{
				return $this->EstNonNul($objet) ;
			}
			public function ValeursIdentifiant()
			{
				return array() ;
			}
			public function ExpressionIdentifiant()
			{
				return "1=0" ;
			}
			public function AjoutDansBD()
			{
				return $this->BaseDonneesParent->InsereLigne($this) ;
			}
			public function ModifDansBD()
			{
				return $this->BaseDonneesParent->MetAJourLigne($this) ;
			}
			public function SupprDansBD()
			{
				return $this->BaseDonneesParent->SupprimeLigne($this) ;
			}
			public function ImportConfigParLigne($row)
			{
				$this->ImportConfigFromRow($row) ;
			}
			public function ImportConfigParValeurs($row)
			{
				$this->ImportConfigFromRow($row) ;
			}
			public function SetParentDatabase(& $database)
			{
				parent::SetParentDatabase($database) ;
				$this->BaseDonneesParent = & $database ;
			}
		}
	}
	
?>