<?php
	
	if(! defined('COMPOSANT_IU_BASE_SWS'))
	{
		if(! defined('COMMON_DB_INCLUDED'))
		{
			include dirname(__FILE__)."/../CommonDB/Base.class.php" ;
		}
		define('COMPOSANT_IU_BASE_SWS', 1) ;
		
		class ComposantIUBaseSws extends PvComposantIUBase
		{
			public $NomRef = "" ;
			public $NomClsCSS = "";
			public $CacherSiVide = 1 ;
			protected function RenduDebutTag()
			{
				$ctn = '<div id="'.$this->IDInstanceCalc.'"' ;
				if($this->NomClsCSS != '')
				{
					$ctn .= ' class="'.$this->NomClsCSS.'"' ;
				}
				$ctn .= '>' ;
				return $ctn ;
			}
			protected function RenduFinTag()
			{
				return '</div>' ;
			}
			public function EstVide()
			{
				return 0;
			}
			protected function RenduVideActif()
			{
				return ($this->CacherSiVide == 1 && $this->EstVide()) ? 1 : 0;
			}
		}
		
		class TableauDonneesBaseSws extends PvTableauDonneesHtml
		{
		}
		class GrilleDonneesBaseSws extends PvGrilleDonneesHtml
		{
		}
		class FormulaireDonneesBaseSws extends PvFormulaireDonneesHtml
		{
		}
		class TableauDonneesAdminSws extends PvTableauDonneesHtml
		{
			protected function InitDessinateurBlocCommandes()
			{
				parent::InitDessinateurBlocCommandes() ;
				$this->DessinateurBlocCommandes->InclureIcone = 1 ;
				$this->DessinateurBlocCommandes->InclureLibelle = 0 ;
			}
		}
		class FormulaireDonneesAdminSws extends PvFormulaireDonneesHtml
		{
		}
		
		class DefNiveauFilArianeSws
		{
			public $ModeleUrl = "";
			public $ModeleLibelle = "";
			public $CibleNiveau = "";
			public $NomClsCSSNiveau = "";
			public $AttrsSupplNiveau = "";
		}
		class FilArianeSws extends ComposantIUBaseSws
		{
			public $DonneesNiveaux = array();
			public $ContenuAvantNiveau = "<a href='?'>/</a>";
			public $SepLiens = " &gt; ";
			public $DefNiveaux = null ;
			public $IndNiveauMin = 0;
			public $IndNiveauMax = -1;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->DefNiveaux = new DefNiveauFilArianeSws() ;
			}
			protected function RenduLien($defNiveau, $donneesNv)
			{
				$url = _parse_pattern($defNiveau->ModeleUrl, array_map(htmlentities('urlencode', $donneesNv))) ;
				$libelle = _parse_pattern($defNiveau->ModeleLibelle, array_map('htmlentities', $donneesNv)) ;
				$ctn = '<a' ;
				if($defNiveau->CibleNiveau != "")
					$ctn .= ' target="'.$defNiveau->CibleNiveau.'"' ;
				if($defNiveau->NomClsCSSNiveau != "")
					$ctn .= ' class="'.$defNiveau->NomClsCSSNiveau.'"' ;
				$ctn .= ' href="'.$url.'"' ;
				if($defNiveau->AttrsSupplNiveau != "")
					$ctn .= ' '.$defNiveau->AttrsSupplNiveau ;
				$ctn .= '>' ;
				$ctn .= $libelle ;
				$ctn .= '</a>' ;
				return $ctn ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				if($this->RenduVideActif())
				{
					return $ctn ;
				}
				$min = $this->IndNiveauMin < 0 ? 0 : $this->IndNiveauMin ;
				$max = $this->IndNiveauMax < 0 ? count($this->DonneesNiveaux) - 1 : $this->IndNiveauMax ;
				$ctn .= $this->RenduDebutTag() ;
				$ctn .= $this->ContenuAvantNiveau ;
				for($i=$min; $i<$max; $i++)
				{
					if($i > 0)
						$ctn .= $this->SepLiens ;
					$ctn .= $this->RenduLien($this->DonneesNiveaux[$i]) ;
				}
				$ctn .= $this->RenduFinTag() ;
				return $ctn ;
			}
		}
		
	}
	
?>