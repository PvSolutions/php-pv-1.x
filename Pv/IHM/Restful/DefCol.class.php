<?php
	
	if(! defined('PV_DEF_COL_RESTFUL'))
	{
		define('PV_DEF_COL_RESTFUL', 1) ;
		
		class PvSrcValsSupplRestful
		{
			public $LignesDonneesBrutes ;
			public function Applique(& $composant, $ligneDonnees)
			{
				return $ligneDonnees ;
			}
		}
		
		class PvDefColRestful extends PvObjet
		{
			public $TriPrealable = 0 ;
			public $OrientationTri = "asc" ;
			public $NomDonneesTri = "" ;
			public $AliasDonneesTri = "" ;
			public $NomDonnees ;
			public $AliasDonnees ;
			public $Libelle ;
			public $Formatteur ;
			public $CorrecteurValeur ;
			public $TriPossible = 1 ;
			public $EncodeHtmlValeur = 1 ;
			public $ExtracteurValeur ;
			public $PrefixeValeursExtraites = "" ;
			public $Visible = 1 ;
			public $ExporterDonnees = 1 ;
			public $ExporterDonneesObligatoire = 0 ;
			public $FormatValeur ;
			public $StyleCSS ;
			public $NomClasseCSS ;
			public $RenvoyerValeurVide = 1 ;
			public $ValeurVide = "" ;
			public function EstVisible(& $zone)
			{
				return $this->Visible == 1 ;
			}
			public function DeclareFormatteurBool()
			{
				$this->Formatteur = new PvFormatteurColonneBooleen() ;
			}
			public function ObtientPrefixeValsExtraites()
			{
				$prefixe = $this->PrefixeValeursExtraites ;
				if($prefixe == '')
				{
					$prefixe = $this->NomDonnees ;
				}
				return $prefixe ;
			}
			public function PeutExporterDonnees()
			{
				return (($this->Visible == 1 && $this->ExporterDonnees == 1) || $this->ExporterDonneesObligatoire) ? 1 : 0 ;
			}
			public function ObtientLibelle()
			{
				$libelle = $this->NomDonnees ;
				if($this->Libelle != "")
				{
					$libelle = $this->Libelle ;
				}
				return $libelle ;
			}
			public function FormatteValeur(& $composant, $ligne)
			{
				$val = null ;
				if($this->EstNul($this->Formatteur))
				{
					$val = $this->FormatteValeurInt($composant, $ligne) ;
				}
				else
				{
					$val = $this->Formatteur->Encode($composant, $this, $ligne) ;
				}
				if($this->EstPasNul($this->CorrecteurValeur))
				{
					$val = $this->CorrecteurValeur->AppliquePourColonne($val, $this) ;
				}
				return $val ;
			}
		}
	}
	
?>