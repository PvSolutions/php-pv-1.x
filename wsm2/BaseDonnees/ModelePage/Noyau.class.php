<?php
	
	if(! defined('MDL_PAGE_BASE'))
	{
		if(! defined('NOYAU_BD_WSM'))
		{
			include dirname(__FILE__)."/../Noyau.class.php" ;
		}
		define('MDL_PAGE_BASE', 1) ;
		
		class LigneModelePageWsm extends LigneBaseWsm
		{
			public $EstVarGlobal ;
			public $AffichImpossible = 0 ;
			public $ScriptParent ;
			public $ZoneParent ;
			public $SystemeParent ;
			public $ApplicationParent ;
			public $BaseDonneesParent ;
			public $PageInstanciee ;
			public $Id ;
			public $Nom ;
			public $Titre ;
			public $NomClasse ;
			public $MaxInstancesPages ;
			public $MaxPagesFilles ;
			public $DefinitFamillePage = 1 ;
			public $DefinitMetaParPagesFilles = 1 ;
			public $DefinitRangeePagesFilles = 1 ;
			public $MaxPagesFillesRangee = 1 ;
			public $DefinitRelPagesSources ;
			public $MaxRelPagesSourcesRangee ;
			public $DefinitRelPagesDest ;
			public $MaxRelPagesDestRangee ;
			public $NomParamDebutRangeePagesFilles = "child_page_start" ;
			public $NomParamDebutRangeeRelPagesSources = "rel_page_src_start" ;
			public $NomParamDebutRangeeRelPagesDest = "rel_page_dest_start" ;
			public $TableEdition = "template" ;
			public $ColonnesExtra = array() ;
			public $Affichages = array() ;
			public $NomParamAffichageSelect = "user_action" ;
			public $ValeurParamAffichageSelect = "" ;
			public $AffichageSelect = null ;
			public $AffichageParDefaut = null ;
			public $AccepteValeursExtra = 1 ;
			public $AccepteValeursSupplExtra = 1 ;
			public $SourceParams = array() ;
			public $AutoDetecteSourceParams = 1 ;
			public $InclureCompTitre = 0 ;
			public $InclureCompChemin = 0 ;
			public $RemplFiltresPage ;
			public function __construct()
			{
				$this->InitConfig() ;
			}
			protected function InitConfig()
			{
				// parent::InitConfig() ;
				$this->RemplFiltresPage = $this->CreeRemplFiltresPage() ;
			}
			protected function CreeRemplFiltresPage()
			{
			}
			public function ChargeConfig()
			{
				$this->ChargeAffichages() ;
				$this->ChargeListeValeursExtra() ;
				$this->ChargeConfigSuppl() ;
			}
			protected function ChargeAffichages()
			{
			}
			protected function ChargeListeValeursExtra()
			{
			}
			protected function ChargeConfigSuppl()
			{
			}
			public function InscritNouvAffichage($nomAffich, $affich)
			{
				$this->InscritAffichage($nomAffich, $affich) ;
			}
			public function InscritAffichage($nomAffich, & $affich)
			{
				$this->Affichages[$nomAffich] = & $affich ;
				$affich->AdopteModelePage($nomAffich, $this) ;
			}
			public function DetecteAffichageSelect()
			{
				if($this->AutoDetecteSourceParams)
				{
					$this->SourceParams = & $_GET ;
				}
				if($this->AffichageParDefaut == null && ! count($this->Affichages))
					return 0 ;
				$nomAffichs = array_keys($this->Affichages) ;
				if($this->AffichageParDefaut == null)
					$this->AffichageParDefaut = $this->Affichages[$nomAffichs[0]] ;
				$this->AffichageSelect = & $this->AffichageParDefaut ;
				if(count($this->Affichages) == 0)
					return 0 ;
				$valeurAffichSelect = "" ;
				if(isset($this->SourceParams[$this->NomParamAffichageSelect]))
				{
					$valeurAffichSelect = $this->SourceParams[$this->NomParamAffichageSelect] ;
				}
				if(isset($this->Affichages[$valeurAffichSelect]))
				{
					$this->ValeurParamAffichageSelect = $valeurAffichSelect ;
				}
				else
				{
					return 0 ;
				}
				$this->AffichageSelect = $this->Affichages[$this->ValeurParamAffichageSelect] ;
				return 1 ;
			}
			public function InscritAffichageParDefaut($affich)
			{
				$this->AffichageParDefaut = & $affich ;
			}
			public function & InscritAffichDeCompsParDefaut($comps=array(), $compEnteteDoc=null, $comPiedDoc=null)
			{
				$affich = new AffichMdlPageBaseWsm() ;
				$this->InscritAffichageParDefaut($affich) ;
				$affich->PiedCorpsDocument = $compEnteteDoc ;
				$affich->CompsCorpsDocument = $comps ;
				$affich->EnteteCorpsDocument = $compPiedDoc ;
				return $affich ;
			}
			public function & InscritNouvAffichDeComps($nomAffich='', $comps=array(), $compEnteteDoc=null, $comPiedDoc=null)
			{
				$affich = new AffichMdlPageBaseWsm() ;
				$this->InscritNouvAffichage($nomAffich, $affich) ;
				$affich->PiedCorpsDocument = $compEnteteDoc ;
				$affich->CompsCorpsDocument = $comps ;
				$affich->EnteteCorpsDocument = $compPiedDoc ;
				return $affich ;
			}
			public function DeclarePageInstanciee(& $page)
			{
				$this->PageInstanciee = & $page ;
				$this->BaseDonneesParent = & $page->BaseDonneesParent ;
				$this->SystemeParent = & $page->BaseDonneesParent->SystemeParent ;
				if($page->EstPasNul($page->ScriptParent))
				{
					$this->ScriptParent = & $page->ScriptParent ;
					$this->ZoneParent = & $page->ScriptParent->ZoneParent ;
					$this->ApplicationParent = & $page->ScriptParent->ApplicationParent ;
				}
				else
				{
					$valeurNulle = null ;
					$this->ScriptParent = & $valeurNulle ;
					$this->ZoneParent = & $valeurNulle ;
					$this->ApplicationParent = & $valeurNulle ;
				}
			}
			public function CreeListeValeursExtra()
			{
				return new ListeValeursExtraBaseWsm() ;
			}
			protected function ImportConfigFromRowValue($name, $value)
			{
				$success = parent::ImportConfigFromRowValue($name, $value) ;
				if($success)
				{
					return 1 ;
				}
				$success = 1 ;
				switch(strtolower($name))
				{
					case $this->PrefixeLigne."id".$this->SuffixeLigne : { $this->Id = $value ; } break ;
					case $this->PrefixeLigne."name".$this->SuffixeLigne : { $this->Nom = $value ; } break ;
					case $this->PrefixeLigne."title".$this->SuffixeLigne : { $this->Titre = $value ; } break ;
					case $this->PrefixeLigne."class_name".$this->SuffixeLigne : { $this->NomClasse = $value ; } break ;
					case $this->PrefixeLigne."max_instance".$this->SuffixeLigne : { $this->MaxInstancesPages = $value ; } break ;
					case $this->PrefixeLigne."max_child".$this->SuffixeLigne : { $this->MaxPagesFilles = $value ; } break ;
					case $this->PrefixeLigne."register_family_page".$this->SuffixeLigne : { $this->DefinitFamillePage = $value ; } break ;
					case $this->PrefixeLigne."register_meta_from_child_page".$this->SuffixeLigne : { $this->DefinitMetaParPagesFilles = $value ; } break ;
					case $this->PrefixeLigne."register_range_child_page".$this->SuffixeLigne : { $this->DefinitRangeePagesFilles = $value ; } break ;
					case $this->PrefixeLigne."child_page_count_for_range".$this->SuffixeLigne : { $this->MaxPagesFillesRangee = $value ; } break ;
					case $this->PrefixeLigne."register_range_rel_page_src".$this->SuffixeLigne : { $this->DefinitRelPagesSources = $value ; } break ;
					case $this->PrefixeLigne."rel_page_src_count_for_range".$this->SuffixeLigne : { $this->MaxRelPagesSourcesRangee = $value ; } break ;
					case $this->PrefixeLigne."register_range_rel_page_dest".$this->SuffixeLigne : { $this->DefinitRelPagesDest = $value ; } break ;
					case $this->PrefixeLigne."rel_page_dest_count_for_range".$this->SuffixeLigne : { $this->MaxRelPagesDestRangee = $value ; } break ;
					case $this->PrefixeLigne."child_page_start_param_name".$this->SuffixeLigne : { $this->NomParamDebutRangeePagesFilles = $value ; } break ;
					case $this->PrefixeLigne."rel_page_src_start_param_name".$this->SuffixeLigne : { $this->NomParamDebutRangeeRelPagesSources = $value ; } break ;
					case $this->PrefixeLigne."rel_page_dest_start_param_name".$this->SuffixeLigne : { $this->NomParamDebutRangeeRelPagesDest = $value ; } break ;
					default : { $success = 0 ; } break ;
				}
				return $success ;
			}
			public function ExpressionIdentifiant()
			{
				return "id=".$this->BaseDonneesParent->ParamPrefix."id" ;
			}
			public function ValeursIdentifiant()
			{
				$values = array() ;
				$values["id"] = $this->Id ;
				return $values ;
			}
			public function ValeursEdition()
			{
				$values = array() ;
				$values["id_template"] = $this->Id ;
				$values["name_template"] = $this->Nom ;
				$values["title_template"] = $this->Titre ;
				$values["class_name_template"] = $this->NomClasse ;
				$values["max_instance_template"] = $this->MaxInstancesPages ;
				$values["max_child_template"] = $this->MaxPagesFilles ;
				$values["register_family_page_template"] = $this->DefinitFamillePage ;
				$values["register_meta_from_child_page_template"] = $this->DefinitMetaParPagesFilles ;
				$values["register_range_child_page_template"] = $this->DefinitRangeePagesFilles ;
				$values["child_page_count_for_range_template"] = $this->MaxPagesFillesRangee ;
				$values["register_range_rel_page_src_template"] = $this->DefinitRelPagesSources ;
				$values["rel_page_src_count_for_range_template"] = $this->MaxRelPagesSourcesRangee ;
				$values["register_range_rel_page_dest_template"] = $this->DefinitRelPagesDest ;
				$values["rel_page_dest_count_for_range_template"] = $this->MaxRelPagesDestRangee ;
				$values["child_page_start_param_name_template"] = $this->NomParamDebutRangeePagesFilles ;
				$values["rel_page_src_start_param_name_template"] = $this->NomParamDebutRangeeRelPagesSources ;
				$values["rel_page_dest_start_param_name_template"] = $this->NomParamDebutRangeeRelPagesDest ;
				return $values ;
			}
		}
		
		class RemplisseurFiltresBaseWsm
		{
			const ModeNonDefini = 0 ;
			const ModeAjout = 1 ;
			const ModeModif = 2 ;
			const ModeSuppr = 3 ;
			const ModeAutre = 4 ;
			public $ModeActuel = 0 ;
			public $FormActuel ;
			public $ScriptActuel ;
			public $ZoneActuelle ;
			public $ModeleActuel ;
			public $PageInstActuelle ;
			public $ApplicationActuelle ;
			public function RemplitFormAjout(& $form, & $modele)
			{
				$this->PrepareRemplForm($form, $modele, 1) ;
				$this->RemplitFormDirect($form, $modele, 1) ;
				$this->TermineRemplForm($form, $modele, 1) ;
			}
			public function RemplitFormModif(& $form, & $modele)
			{
				$this->PrepareRemplForm($form, $modele, 2) ;
				$this->RemplitFormDirect($form, $modele, 2) ;
				$this->TermineRemplForm($form, $modele, 2) ;
			}
			public function RemplitFormSuppr(& $form, & $modele)
			{
				$this->PrepareRemplForm($form, $modele, 3) ;
				$this->RemplitFormDirect($form, $modele, 3) ;
				$this->TermineRemplForm($form, $modele, 3) ;
			}
			public function TermineRemplForm(& $form, & $modele, $mode)
			{
				$this->FormActuel->FournisseurDonnees = new PvFournisseurDonneesSql() ;
				$this->FormActuel->FournisseurDonnees->BaseDonnees = $this->ApplicationActuelle->BDWsm ;
				$this->FormActuel->FournisseurDonnees->RequeteSelection = 'page' ;
				$this->FormActuel->FournisseurDonnees->TableEdition = 'page' ;
			}
			public function PourAjout()
			{
				return $this->ModeActuel == RemplisseurFiltresBaseWsm::ModeAjout ;
			}
			public function PourModif()
			{
				return $this->ModeActuel == RemplisseurFiltresBaseWsm::ModeModif ;
			}
			public function PourSuppr()
			{
				return $this->ModeActuel == RemplisseurFiltresBaseWsm::ModeSuppr ;
			}
			public function PourAutre()
			{
				return $this->ModeActuel == RemplisseurFiltresBaseWsm::ModeAutre ;
			}
			protected function PrepareRemplForm(& $form, & $modele, $mode)
			{
				$this->FormActuel = & $form ;
				$this->ScriptActuel = & $form->ScriptParent ;
				$this->ZoneActuelle = & $form->ZoneParent ;
				$this->ApplicationActuelle = & $form->ApplicationParent ;
				$this->ModeleActuel = & $modele ;
				$this->PageInstActuelle = & $modele->PageInstanciee ;
			}
			protected function RemplitFormDirect(& $form, & $modele, $mode)
			{
			}
		}
		
		class AffichMdlPageBaseWsm extends PvObjet
		{
			public $NomElementMdlPage = "" ;
			public $ModelePageParent ;
			public $PageInstanciee ;
			public $BaseDonneesParent ;
			public $ScriptParent ;
			public $ZoneParent ;
			public $ApplicationParent ;
			public $Visible = 1 ;
			public $LectureSeule = 0 ;
			public $CompEnteteDocument = null ;
			public $CompsCorpsDocument = array() ;
			public $CompPiedDocument = null ;
			public $UtiliserRenduComposants = 1 ;
			public $ResultRenduDispositif = '' ;
			public $ContenuAvantRendu = '' ;
			public $ContenuApresRendu = '' ;
			public function Accepte($nom)
			{
				return ($this->NomElementMdlPage == $nom) ? 1 : 0 ;
			}
			public function RespectePreRequis()
			{
				return 1 ;
			}
			public function EstAccessible()
			{
				return 1 ;
			}
			public function EstBienRefere()
			{
				return 1 ;
			}
			public function AdopteModelePage($nomAffich, & $modele)
			{
				$this->ModelePageParent = & $modele ;
				$this->NomElementMdlPage = $nomAffich ;
				$this->PageInstanciee = & $modele->PageInstanciee ;
				$this->BaseDonneesParent = & $modele->BaseDonneesParent ;
				$this->SystemeParent = & $modele->SystemeParent ;
				$this->ApplicationParent = & $modele->BaseDonneesParent->SystemeParent->ApplicationParent ;
				if($modele->EstPasNul($modele->ScriptParent))
				{
					$this->ScriptParent = & $modele->ScriptParent ;
					$this->ZoneParent = & $modele->ZoneParent ;
				}
			}
			public function AdopteScript($nom, & $script)
			{
				$this->ScriptParent = & $script ;
				$this->ZoneParent = & $script->ZoneParent ;
			}
			protected function ChargeComposants()
			{
			}
			public function ChargeConfig()
			{
				$this->ChargeComposants() ;
			}
			public function EstBienAppele()
			{
				if(! $this->EstAccessible() || ! $this->RespectePreRequis() || ! $this->EstBienRefere())
				{
					return 0 ;
				}
				return 1 ;
			}
			public function RemplitScript(& $script)
			{
				$this->ChargeConfig() ;
				if(! $this->EstBienAppele())
				{
					return ;
				}
				$this->PrepareRemplissage($script) ;
				return $this->RemplitScriptValide($script) ;
			}
			protected function PrepareRemplissage(& $script)
			{
			}
			protected function ChargeConfigComps()
			{
				foreach($this->CompsEnteteDocument as $i => & $comp)
				{
					$comp->AdopteScript($comp->IDInstanceCalc, $this->ScriptParent) ;
					$comp->ChargeConfig() ;
				}
				foreach($this->CompsCorpsDocument as $i => & $comp)
				{
					$comp->AdopteScript($comp->IDInstanceCalc, $this->ScriptParent) ;
					$comp->ChargeConfig() ;
				}
				foreach($this->CompPiedDocument as $i => & $comp)
				{
					$comp->AdopteScript($comp->IDInstanceCalc, $this->ScriptParent) ;
					$comp->ChargeConfig() ;
				}
			}
			protected function RemplitScriptValide(& $script)
			{
				if($this->EstPasNul($this->CompEnteteDocument))
				{
					$script->InscritCompEnteteDocument($this->CompEnteteDocument) ;
				}
				$script->CompsCorpsDocument = array() ;
				foreach($this->CompsCorpsDocument as $nom => $comp)
				{
					// echo $comp->IDInstanceCalc.' llll<br>' ;
					$script->InscritCompCorpsDocument($this->IDInstanceCalc."_".$nom, $this->CompsCorpsDocument[$nom]) ;
				}
				// print count($script->CompsCorpsDocument) ;
				if($this->EstPasNul($this->CompPiedDocument))
				{
					$script->InscritCompPiedDocument($this->CompPiedDocument) ;
				}
			}
			protected function RenduAccesInterdit()
			{
			}
			public function RenduDispositif()
			{
				$ctn = '' ;
				if(! $this->EstBienAppele())
				{
					return $this->RenduAccesInterdit() ;
				}
				$ctn .= $this->ContenuAvantRendu ;
				$ctn .= $this->RenduDispositifBrut() ;
				$ctn .= $this->RenduApresRendu ;
				return $ctn ;
			}
			protected function RenduDispositifBrut()
			{
			}
		}
		
		class ListeValeursExtraWsm
		{
			public static function ParContenu($contenu)
			{
				$listeVals = new ListeValeursExtraBaseWsm() ;
				$listeVals->ImportParContenu($contenu) ;
				return $listeVals ;
			}
			public static function ParValeurs($valeurs)
			{
				$listeVals = new ListeValeursExtraBaseWsm() ;
				$listeVals->ImportParValeurs($valeurs) ;
				return $listeVals ;
			}
		}
		class ListeValeursExtraBaseWsm extends PvObjet
		{
			var $Valeurs = array() ;
			var $Contenu ;
			var $AutoMAJ = 1 ;
			function ImportParContenu($contenu)
			{
				$this->Contenu = $contenu ;
				if($this->AutoMAJ)
				{
					$this->RafraichitValeurs() ;
				}
			}
			function ImportParValeurs($valeurs)
			{
				$this->Valeurs = $valeurs ;
				if($this->AutoMAJ)
				{
					$this->RafraichitValeurs() ;
				}
			}
			function FixeValeurs($valeurs)
			{
				foreach($valeurs as $n => $v)
				{
					if($this->ExisteCle($n))
					{
						$this->FixeValeur($n, $v) ;
					}
				}
			}
			function RafraichitValeurs()
			{
				$this->Valeurs = $this->ConvertValeursParContenu($this->Contenu) ;
			}
			function RafraichitContenu()
			{
				$this->Contenu = $this->ConvertValeursEnContenu($this->Valeurs) ;
			}
			function ConvertValeursEnContenu($listeValeurs)
			{
				$keys = array_keys($listeValeurs) ;
				$str_res = '' ;
				for($i=0; $i<count($keys); $i++)
				{
					if($i)
					{
						$str_res .= "\r\n" ;
					}
					$key = $keys[$i] ;
					$value = $this->convert_value_to_content($listeValeurs[$keys[$i]]) ;
					$str_res .= $key.':'.$value ;
				}
				return $str_res ;
			}
			function ConvertValeursParContenu($ctn)
			{
				if($ctn == '')
				{
					return array() ;
				}
				$ctn = str_replace("\r", "", $ctn) ;
				$lignes = explode("\n", $ctn) ;
				$listeValeurs = array() ;
				foreach($lignes as $i => $ligne)
				{
					$data = explode(':', $ligne) ;
					$key = $data[0] ;
					$value = _value_def($data, 1, '') ;
					$listeValeurs[$key] = $this->ConvertContenuEnValeur($value) ;
				}
				return $listeValeurs ;
			}
			function ConvertValeurEnContenu($value)
			{
				$result = $value ;
				$result = str_replace(':', ' --- ', $result) ;
				$result = str_replace("\r\n", ' -/- ', $result) ;
				return $result ;
			}
			function ConvertContenuEnValeur($str)
			{
				$result = $str ;
				$result = str_replace(' --- ', ':', $result) ;
				$result = str_replace(' -/- ', "\r\n", $result) ;
				$result = preg_replace("/[[:space:]]+$/", "", $result) ;
				return $result ;
			}
			public function RecupValeurs($cles=array(), $valeurDefaut=null)
			{
				$valeurs = array() ;
				foreach($cles as $i => $cle)
				{
					$valeurs[$cle] = $this->RecupValeur($cle, $valeurDefaut) ;
				}
				return $valeurs ;
			}
			function ExisteCle($key)
			{
				return (isset($this->Valeurs[$key])) ;
			}
			function RecupValeur($key, $default_value=NULL, $as='')
			{
				$value = $default_value ;
				if($this->ExisteCle($key))
				{
					$value = _cast_value($this->Valeurs[$key], $as) ;
				}
				return $value ;
			}
			function ObtientValeur($key, $default_value=NULL, $as='')
			{
				return $this->RecupValeur($key, $default_value, $as) ;
			}
			function FixeValeur($key, $value)
			{
				if($value == null)
				{
					unset($this->Valeurs[$key]) ;
				}
				else
				{
					$this->Valeurs[$key] = $value ;
				}
				if($this->AutoMAJ)
				{
					$this->RafraichitContenu() ;
				}
			}
		}
		class ValeurExtraBaseWsm
		{
			public $Nom = "" ;
			public $Titre = "" ;
			public $ValeurDefaut = "" ;
			public $Editeur = null ;
			public $ModeleParent = null ;
			public $PageInstanciee = null ;
			public function DeclareModeleParent(& $modele)
			{
				$this->ModeleParent = & $modele ;
				$this->PageInstanciee = & $modele->PageInstanciee ;
			}
		}
	}
	
?>