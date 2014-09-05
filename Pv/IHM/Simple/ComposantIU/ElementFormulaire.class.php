<?php
	
	if(! defined('PV_COMPOSANT_SIMPLE_IU_ELEM_FORM'))
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
		define('PV_COMPOSANT_SIMPLE_IU_ELEM_FORM', 1) ;
		
		class PvElementFormulaireHtml extends PvBaliseHtmlBase
		{
			public $Largeur = "" ;
			public $Hauteur = "" ;
			public $Valeur = "" ;
			public $EncodeHtmlEtiquette = 1 ;
			public function EncodeEtiquette($valeur)
			{
				return ($this->EncodeHtmlEtiquette) ? htmlentities($valeur) : $valeur ;
			}
			public function RenduEtiquette()
			{
				return $this->EncodeEtiquette($this->Valeur) ;
			}
			protected function RenduAttrStyleCSS()
			{
				$styleCSS = '' ;
				$ctn = '' ;
				if($this->Largeur != '')
				{
					$styleCSS .= 'width:'.$this->Largeur.';' ;
				}
				if($this->Hauteur != '')
				{
					$styleCSS .= 'height:'.$this->Hauteur.';' ;
				}
				if($this->StyleCSS != '')
				{
					$styleCSS .= $this->StyleCSS ;
				}
				if($styleCSS != '')
				{
					$ctn .= ' style="'.$styleCSS.'"' ;
				}
				return $ctn ;
			}
		}
		
		class PvZoneEtiquetteHtml extends PvElementFormulaireHtml
		{
			public $Libelle = "" ;
			public $UtiliserValeurSiLibelleVide = 1 ;
			protected function RenduDispositifBrut()
			{
				$this->CorrigeIDsElementHtml() ;
				$ctn = '' ;
				$styleCSS = '' ;
				$ctn .= '<input name="'.$this->NomElementHtml.'"' ;
				$ctn .= ' id="'.$this->IDInstanceCalc.'"' ;
				$ctn .= ' type="hidden"' ;
				$ctn .= ' value="'.htmlentities($this->Valeur).'"' ;
				$ctn .= ' />' ;
				$ctn .= '<span' ;
				if($this->Largeur != '')
				{
					$styleCSS .= 'width:'.$this->Largeur.';' ;
				}
				if($this->Hauteur != '')
				{
					$styleCSS .= 'height:'.$this->Hauteur.';' ;
				}
				if($this->StyleCSS != '')
				{
					$styleCSS .= $this->StyleCSS ;
				}
				if($styleCSS != '')
				{
					$ctn .= ' style="'.$styleCSS.'"' ;
				}
				$ctn .= '>' ;
				$ctn .= htmlentities($this->ObtientLibelle()) ;
				$ctn .= '</span>' ;
				return $ctn ;
			}
			public function ObtientLibelle()
			{
				$resultat = $this->Libelle ;
				if($this->Libelle == "" && $this->UtiliserValeurSiLibelleVide)
				{
					$resultat = $this->Valeur ;
				}
				return $resultat ;
			}
		}
		class PvZoneCorrespHtml extends PvZoneEtiquetteHtml
		{
			public $NomColonneValeur ;
			public $NomColonneLibelle ;
			public $FournisseurDonnees ;
			public $FiltresSelection = array() ;
			protected function CalculeLibelle()
			{
				$lignes = $this->FournisseurDonnees->RechExacteElements($this->FiltresSelection, $this->NomColonneValeur, $this->Valeur) ;
				$etiquette = '' ;
				if(count($lignes) > 0)
				{
					$this->Libelle = $lignes[0][$this->NomColonneLibelle] ;
				}
				else
				{
				}
				return $etiquette ;
			}
			protected function RenduDispositifBrut()
			{
				$this->CalculeLibelle() ;
				$ctn = parent::RenduDispositifBrut() ;
				return $ctn ;
			}
		}
		class PvZoneEntreeHtml extends PvElementFormulaireHtml
		{
			public $TypeElementFormulaire = "" ;
			protected function RenduDispositifBrut()
			{
				$this->CorrigeIDsElementHtml() ;
				$ctn = '' ;
				$styleCSS = '' ;
				$ctn .= '<input name="'.$this->NomElementHtml.'"' ;
				$ctn .= ' id="'.$this->IDInstanceCalc.'"' ;
				$ctn .= ' type="'.$this->TypeElementFormulaire.'"' ;
				$ctn .= $this->RenduAttrStyleCSS() ;
				$ctn .= ' value="'.htmlentities($this->Valeur).'"' ;
				$ctn .= ' />' ;
				return $ctn ;
			}
		}
		class PvZoneTexteHtml extends PvZoneEntreeHtml
		{
			public $TypeElementFormulaire = "text" ;
		}
		class PvZoneMultiligneHtml extends PvElementFormulaireHtml
		{
			public $TotalLignes = 0 ;
			public $TotalColonnes = 0 ;
			protected function RenduDispositifBrut()
			{
				$this->CorrigeIDsElementHtml() ;
				$ctn = '' ;
				$styleCSS = '' ;
				$ctn .= '<textarea name="'.$this->NomElementHtml.'"' ;
				$ctn .= ' id="'.$this->IDInstanceCalc.'"' ;
				$ctn .= $this->RenduAttrStyleCSS() ;
				if($this->TotalColonnes > 0)
					$ctn .= ' cols="'.$this->TotalColonnes.'"' ;
				if($this->TotalLignes > 0)
					$ctn .= ' rows="'.$this->TotalLignes.'"' ;
				$ctn .= '>' ;
				$ctn .= htmlentities($this->Valeur) ;
				$ctn .= '</textarea>' ;
				return $ctn ;
			}
		}
		class PvZoneInvisibleHtml extends PvZoneEntreeHtml
		{
			public $TypeElementFormulaire = "hidden" ;
		}
		class PvZoneCacheeHtml extends PvZoneInvisibleHtml
		{
		}
		class PvZoneMotPasseHtml extends PvZoneEntreeHtml
		{
			public $TypeElementFormulaire = "password" ;
		}
		class PvZoneUploadHtml extends PvElementFormulaireHtml
		{
			public $InclureErreurTelecharg = 1 ;
			public $InclureCheminCoteServeur = 1 ;
			public $CheminCoteServeurEditable = 1 ;
			public $InclureZoneSelectFichier = 1 ;
			public $TypeElementFormulaire = "file" ;
			public $NomEltCoteSrv = "CoteSrv_" ;
			public $LibelleCoteSrv = "Chemin sur le serveur" ;
			protected function RenduDispositifBrut()
			{
				$this->CorrigeIDsElementHtml() ;
				$ctn = '' ;
				if($this->InclureZoneSelectFichier)
				{
					$ctn .= $this->RenduZoneSelectFichier() ;
				}
				if($this->InclureZoneSelectFichier)
						$ctn .= '<br />' ;
				$ctn .= $this->RenduCheminCoteServeur() ;
				if($this->InclureErreurTelecharg)
				{
					$ctn .= '</td>'.PHP_EOL ;
					$ctn .= '<td>'.PHP_EOL ;
					if($this->FiltreParent->CodeErreurTelechargement != '')
					{
						$ctn .= $this->FiltreParent->LibelleErreurTelecharg ;
					}
					$ctn .= '</td>'.PHP_EOL ;
					$ctn .= '</tr>'.PHP_EOL ;
				}
				return $ctn ;
			}
			protected function RenduZoneSelectFichier()
			{
				$ctn = '' ;
				$ctn .= '<input name="'.$this->NomElementHtml.'"' ;
				$ctn .= ' id="'.$this->IDInstanceCalc.'"' ;
				$ctn .= ' type="'.$this->TypeElementFormulaire.'"' ;
				$ctn .= $this->RenduAttrStyleCSS() ;
				$ctn .= ' value="'.htmlentities($this->Valeur).'"' ;
				$ctn .= ' />' ;
				return $ctn ;
			}
			protected function RenduCheminCoteServeur()
			{
				$ctn = '' ;
				$nomEltCoteSrv = ($this->FiltreParent != '') ? $this->FiltreParent->NomEltCoteSrv : $this->NomEltCoteSrv ;
				if($this->InclureCheminCoteServeur)
				{
					$ctn .= $this->LibelleCoteSrv.' <input type="text" class="EditeurCheminCoteServeur" name="'.$nomEltCoteSrv.$this->NomElementHtml.'" value="'.htmlentities($this->Valeur).'" size="20" />' ;
				}
				else
				{
					$ctn .= '<input type="hidden" name="'.$nomEltCoteSrv.$this->NomElementHtml.'" value="'.htmlentities($this->Valeur).'" />'.htmlentities($this->Valeur) ;
				}
				return $ctn ;
			}
		}
		class PvZoneCocherHtml extends PvElementFormulaireHtml
		{
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= $this->RenduOption() ;
				return $ctn ;
			}
			public function RenduOption()
			{
				$ctn = '' ;
				$ctn .= '<input name="'.$this->NomElementHtml.'"' ;
				$ctn .= ' id="'.$this->IDInstanceCalc.'"' ;
				$ctn .= ' type="checkbox"' ;
				$ctn .= ' value="'.htmlentities($this->Valeur).'"' ;
				$ctn .= ' />' ;
				return $ctn ;
			}
		}
		class PvZoneBoiteChoixBaseHtml extends PvElementFormulaireHtml
		{
			public $FournisseurDonnees = null ;
			public $NomClasseFournisseurDonnees = "PvFournisseurDonneesBase" ;
			public $FiltresSelection = array() ;
			public $NomColonneValeur = "" ;
			public $NomColonneLibelle = "" ;
			public $NomColonneExtra = "" ;
			public $NomColonneValeurParDefaut = "" ;
			public $UtiliserColonneExtra = 0 ;
			protected $Elements = array() ;
			public $TotalElements = 0 ;
			public $LibelleEtiqVide = "(Non trouve)" ;
			public $StockerElements = 0 ;
			protected $RequeteSupport = false ;
			public $InclureElementHorsLigne = 0 ;
			public $ValeurElementHorsLigne = "" ;
			public $LibelleElementHorsLigne = "" ;
			public $ExtraElementHorsLigne = "" ;
			protected $ValeursSelectionnees = array() ;
			protected $ChoixMultiple = 0 ;
			public $InclureLienSelectTous = 0 ;
			public $CheminIconeLienSelectTous = "" ;
			public $LibelleLienSelectTous = "Cocher tout" ;
			public $InclureLienSelectAucun = 0 ;
			public $CheminIconeLienSelectAucun = "" ;
			public $LibelleLienSelectAucun = "Decocher Tout" ;
			public $InclureLiens = 1 ;
			public $InclureFoncJs = 1 ;
			protected function RenduFoncJs()
			{
				if(! $this->InclureFoncJs || $this->ChoixMultiple == 0)
					return '' ;
				$ctn = '' ;
				$ctn .= '<script language="javascript">
	function SelectElems_'.$this->IDInstanceCalc.'(mode)
	{
		var totalElems = '.svc_json_encode($this->TotalElements).' ;
		for(var i=1; i<=totalElems; i++)
		{
			var noeud = document.getElementById('.svc_json_encode($this->IDInstanceCalc.'_').' + i) ;
			if(noeud == null)
			{
				continue ;
			}
			'.PHP_EOL ;
				$ctn .= $this->InstrsJsSelectElement() ;
				$ctn .= "\t\t".'}
	}
</script>' ;
				return $ctn ;
			}
			protected function InstrsJsSelectElement()
			{
				return '' ;
			}
			protected function RenduLiens()
			{
				$ctn = '' ;
				if(! $this->InclureLiens || ! $this->ChoixMultiple || ! $this->InclureLienSelectAucun && ! $this->InclureLienSelectTous)
					return $ctn ;
				if($this->TotalElements == 0)
					return $ctn ;
				$lienRendu = 0 ;
				$ctn .= '<div class="liens">'.PHP_EOL ;
				if($this->InclureLienSelectTous != 0)
				{
					$libelleLien = htmlentities($this->LibelleLienSelectTous) ;
					$ctn .= '<a href="javascript:SelectElems_'.$this->IDInstanceCalc.'(1)">'.$libelleLien.'</a>' ;
					$lienRendu = 1 ;
				}
				if($this->InclureLienSelectAucun != 0)
				{
					if($lienRendu)
					{
						$ctn .= "&nbsp;&nbsp;&nbsp;&nbsp;" ;
					}
					$libelleLien = htmlentities($this->LibelleLienSelectAucun) ;
					$ctn .= '<a href="javascript:SelectElems_'.$this->IDInstanceCalc.'(0)">'.$libelleLien.'</a>' ;
				}
				$ctn .= '</div>'.PHP_EOL ;
				return $ctn ;
			}
			public function RenduEtiquette()
			{
				$lignes = $this->FournisseurDonnees->RechExacteElements($this->FiltresSelection, $this->NomColonneValeur, $this->Valeur) ;
				$etiquette = '' ;
				if(count($lignes) > 0)
				{
					$etiquette = $lignes[0][$this->NomColonneLibelle] ;
				}
				else
				{
					$etiquette = $this->LibelleEtiqVide ;
				}
				return $etiquette ;
			}
			protected function CalculeValeursSelectionnees()
			{
				if(! $this->ChoixMultiple)
				{
					if(is_array($this->Valeur))
						$this->ValeursSelectionnees = $this->Valeur ;
					else
						$this->ValeursSelectionnees = array($this->Valeur) ;
				}
				else
				{
					$this->ValeursSelectionnees = array($this->Valeur) ;
				}
			}
			protected function EstValeurSelectionnee($valeur)
			{
				// print $this->IDInstanceCalc ;
				return (in_array($valeur, $this->ValeursSelectionnees)) ? 1 : 0 ;
			}
			protected function ChargeConfigFournisseurDonnees()
			{
			}
			protected function CalculeElementsRendu()
			{
				$this->CalculeValeursSelectionnees() ;
				$this->TotalElements = 0 ;
				if(! $this->EstNul($this->FournisseurDonnees))
				{
					$this->TotalElements = $this->FournisseurDonnees->CompteElements(array(), $this->FiltresSelection) ;
				}
			}
			protected function InitFournisseurDonnees()
			{
				if($this->EstNul($this->FournisseurDonnees) && $this->NomClasseFournisseurDonnees != "")
				{
					$nomClasse = $this->NomClasseFournisseurDonnees ;
					if(class_exists($nomClasse))
					{
						$this->FournisseurDonnees = new $nomClasse() ;
					}
				}
				if(! $this->EstNul($this->FournisseurDonnees))
				{
					$this->ChargeConfigFournisseurDonnees() ;
					$this->FournisseurDonnees->ChargeConfig() ;
				}
			}
			protected function RenduListeElements()
			{
				$ctn = '' ;
				return $ctn ;
			}
			protected function RenduElement($valeur, $libelle, $ligne, $position=0)
			{
				$ctn = '' ;
				return $ctn ;
			}
			protected function OuvreRequeteSupport()
			{
				$this->TotalElements = 0 ;
				$this->RequeteSupport = $this->FournisseurDonnees->OuvreRequeteSelectElements($this->FiltresSelection) ;
				// print_r($this->FournisseurDonnees->BaseDonnees) ;
				$this->AfficheExceptionFournisseurDonnees() ;
				// echo get_class($this->FournisseurDonnees) ;
			}
			protected function LitRequeteSupport()
			{
				$val = $this->FournisseurDonnees->LitRequete($this->RequeteSupport) ;
				if($val != false)
					$this->TotalElements++ ;
				return $val ;
			}
			protected function FermeRequeteSupport()
			{
				$this->FournisseurDonnees->FermeRequete($this->RequeteSupport) ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$this->InitFournisseurDonnees() ;
				if(! $this->EstNul($this->FournisseurDonnees))
				{
					$this->ChargeConfigFournisseurDonnees() ;
					$this->CalculeElementsRendu() ;
					$ctn .= $this->RenduListeElements() ;
				}
				else
				{
					die("Le composant ".$this->IDInstanceCalc." nécessite un fournisseur de données.") ;
				}
				return $ctn ;
			}
			protected function ExtraitValeur($ligne, $nomColonne)
			{
				$valeur = isset($ligne[$nomColonne]) ? $ligne[$nomColonne] : "" ;
				return $valeur ;
			}
			public function PossedeValeursSelectionnees()
			{
				// print ($this->Valeur).'<br>' ;
				// if($this->EstPasNul($this->FormulaireDonneesParent))
				return ($this->Valeur != "" && count($this->ValeursSelectionnees) > 0) ? 1 : 0 ;
			}
		}
		class PvZoneBoiteSelectHtml extends PvZoneBoiteChoixBaseHtml
		{
			protected function RenduListeElements()
			{
				$this->CorrigeIDsElementHtml() ;
				$ctn = '' ;
				$ctn .= $this->RenduFoncJs() ;
				$ctn .= $this->RenduLiens() ;
				$ctn .= '<select' ;
				$ctn .= ' name="'.$this->NomElementHtml.'"' ;
				$ctn .= ' id="'.$this->IDInstanceCalc.'"' ;
				$ctn .= $this->RenduAttrStyleCSS() ;
				$ctn .= '>'.PHP_EOL ;
				if($this->InclureElementHorsLigne)
				{
					$ligne = array(
						$this->NomColonneValeur => $this->ValeurElementHorsLigne,
						$this->NomColonneLibelle => $this->LibelleElementHorsLigne,
						$this->NomColonneExtra => $this->ExtraElementHorsLigne,
					) ;
					$ctn .= $this->RenduElement($this->ValeurElementHorsLigne, $this->LibelleElementHorsLigne, $ligne, 0) ;
				}
				$this->OuvreRequeteSupport() ;
				while($ligne = $this->LitRequeteSupport())
				{
					$valeur = $this->ExtraitValeur($ligne, $this->NomColonneValeur) ;
					$libelle = $this->ExtraitValeur($ligne, $this->NomColonneLibelle) ;
					$ctn .= $this->RenduElement($valeur, $libelle, $ligne, $this->RequeteSupport->Position) ;
				}
				$this->FermeRequeteSupport() ;
				$ctn .= '</select>' ;
				return $ctn ;
			}
			protected function RenduElement($valeur, $libelle, $ligne, $position=0)
			{
				$ctn = '' ;
				$ctn .= '<option' ;
				$ctn .= ' value="'.htmlentities($valeur).'"' ;
				if($this->EstValeurSelectionnee($valeur))
				{
					$ctn .= ' selected' ;
				}
				$ctn .= '>' ;
				$ctn .= htmlentities($libelle) ;
				$ctn .= '</option>'.PHP_EOL ;
				return $ctn ;
			}
		}
		class PvZoneBoiteOptionsRadioHtml extends PvZoneBoiteChoixBaseHtml
		{
			public $MaxColonnesParLigne = 2 ;
			public $AlignLibelle = "right" ;
			public $LargeurOption = "" ;
			protected $CocherAutoPremiereOption = 1 ;
			protected function RenduListeElements()
			{
				$this->CorrigeIDsElementHtml() ;
				$ctn = '' ;
				$ctn .= $this->RenduFoncJs() ;
				$ctn .= $this->RenduLiens() ;
				$ctn .= '<table' ;
				$ctn .= ' name="Conteneur_'.$this->NomElementHtml.'"' ;
				$ctn .= ' id="Conteneur_'.$this->IDInstanceCalc.'"' ;
				$ctn .= $this->RenduAttrStyleCSS() ;
				$ctn .= '>'.PHP_EOL ;
				$totalLignes = 0 ;
				$indexLigne = 0 ;
				$pourcentageColonne = intval(100 / $this->MaxColonnesParLigne) ;
				$this->OuvreRequeteSupport() ;
				while($ligne = $this->LitRequeteSupport())
				{
					if($indexLigne % $this->MaxColonnesParLigne == 0)
					{
						$ctn .= '<tr>'.PHP_EOL ;
					}
					$ctn .= '<td' ;
					$ctn .= ' width="'.$pourcentageColonne.'%"' ;
					$ctn .= ' valign="top"' ;
					$ctn .= '>'.PHP_EOL ;
					$valeur = $this->ExtraitValeur($ligne, $this->NomColonneValeur) ;
					$libelle = $this->ExtraitValeur($ligne, $this->NomColonneLibelle) ;
					$ctn .= $this->RenduElement($valeur, $libelle, $ligne, $this->RequeteSupport->Position).PHP_EOL ;
					$ctn .= '</td>'.PHP_EOL ;
					if($indexLigne % $this->MaxColonnesParLigne == $this->MaxColonnesParLigne - 1)
					{
						$ctn .= '</tr>'.PHP_EOL ;
					}
					$indexLigne++ ;
				}
				if($indexLigne % $this->MaxColonnesParLigne != 0)
				{
					$colonnesFusionnees = $this->MaxColonnesParLigne - ($indexLigne % $this->MaxColonnesParLigne) ;
					$ctn .= '<td colspan="'.$colonnesFusionnees.'"></td>'.PHP_EOL ;
					$ctn .= '</tr>'.PHP_EOL ;
				}
				$this->FermeRequeteSupport() ;
				$ctn .= '</table>' ;
				// print_r($this->FournisseurDonnees->BaseDonnees) ;
				return $ctn ;
			}
			protected function RenduElement($valeur, $libelle, $ligne, $position=0)
			{
				$ctn = '' ;
				switch($this->AlignLibelle)
				{
					case "right" :
					case "droite" :
					{
						$ctn = $this->RenduOptionElement($valeur, $libelle, $ligne, $position)."".$this->RenduLibelleElement($valeur, $libelle, $ligne, $position) ;
					}
					break ;
					case "left" :
					case "gauche" :
					{
						$ctn = $this->RenduLibelleElement($valeur, $libelle, $ligne, $position)."".$this->RenduOptionElement($valeur, $libelle, $ligne, $position) ;
					}
					break ;
					case "hidden" :
					case "cache" :
					{
						$ctn = $this->RenduOptionElement($valeur, $libelle, $ligne, $position) ;
					}
					break ;
				}
				return $ctn ;
			}
			protected function RenduOptionElement($valeur, $libelle, $ligne, $position=0)
			{
				$forcerSelection = 0 ;
				if($position == 1 && $this->Valeur == "" && $this->CocherAutoPremiereOption)
				{
					$forcerSelection = 1 ;
				}
				$ctn = '' ;
				$nomElementHtml = $this->NomElementHtml ;
				$ctn .= '<input type="radio" name="'.$nomElementHtml.'" id="'.$this->IDInstanceCalc.'_'.$position.'"' ;
				$ctn .= ' value="'.htmlentities($valeur).'"' ;
				if($this->EstValeurSelectionnee($valeur) || $forcerSelection)
				{
					$ctn .= ' checked' ;
				}
				$ctn .= ' />' ;
				return $ctn ;
			}
			protected function RenduLibelleElement($valeur, $libelle, $ligne, $position=0)
			{
				$ctn = '<label for="'.$this->IDInstanceCalc.'_'.$position.'">'.htmlentities($libelle).'</label>' ;
				return $ctn ;
			}
		}
		class PvZoneBoiteOptionsCocherHtml extends PvZoneBoiteOptionsRadioHtml
		{
			public $ChoixMultiple = 1 ;
			protected function InstrsJsSelectElement()
			{
				$ctn = '' ;
				$ctn .= 'var sel = noeud.getAttribute("checked") ;
	switch(mode)
	{
		case 1 :
		{
			if(noeud.checked != undefined)
				noeud.checked = "checked" ;
			else
				noeud.setAttribute("checked", "checked") ;
		}
		break ;
		case 0 :
		{
			if(noeud.checked != undefined)
				noeud.checked = "" ;
			else
				noeud.removeAttribute("checked") ;
		}
		break ;
	}
' ;
				return $ctn ;
			}
			protected function RenduOptionElement($valeur, $libelle, $ligne, $position=0)
			{
				$forcerSelection = 0 ;
				if(! $this->PossedeValeursSelectionnees())
				{
					if($this->NomColonneValeurParDefaut == "" && $position == 1 && ($this->CocherAutoPremiereOption))
					{
						$forcerSelection = 1 ;
					}
				}
				if(! $forcerSelection && $this->NomColonneValeurParDefaut != "" && $ligne[$this->NomColonneValeurParDefaut] == 1)
				{
					$forcerSelection = 1 ;
				}
				// echo $valeur.' : '.$forcerSelection.'<br />' ;
				$ctn = '' ;
				$nomElementHtml = $this->NomElementHtml.'[]' ;
				$ctn .= '<input type="checkbox" name="'.$nomElementHtml.'" id="'.$this->IDInstanceCalc.'_'.$position.'"' ;
				$ctn .= ' value="'.htmlentities($valeur).'"' ;
				if($this->EstValeurSelectionnee($valeur) || $forcerSelection)
				{
					$ctn .= ' checked' ;
				}
				$ctn .= ' />' ;
				return $ctn ;
			}	
		}
		
		class PvZoneSelectBoolHtml extends PvZoneBoiteSelectHtml
		{
			public $LibelleVrai = "" ;
			public $LibelleFaux = "" ;
			public $ValeurVrai = "" ;
			public $ValeurFaux = "" ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvComposantIUBoolHtml::RemplitConfig($this) ;
			}
		}
		class PvComposantIUBoolHtml
		{
			public static function RemplitConfig(& $comp)
			{
				$comp->FournisseurDonnees = new PvFournisseurDonneesBool() ;
				if($comp->ValeurVrai != "")
					$comp->FournisseurDonnees->ValeurVrai = $comp->ValeurVrai ;
				if($comp->ValeurFaux != "")
					$comp->FournisseurDonnees->ValeurFaux = $comp->ValeurFaux ;
				if($comp->LibelleVrai != "")
					$comp->FournisseurDonnees->LibelleVrai = $comp->LibelleVrai ;
				if($comp->LibelleFaux != "")
					$comp->FournisseurDonnees->LibelleFaux = $comp->LibelleFaux ;
				$comp->FournisseurDonnees->ChargeConfig() ;
				$comp->FournisseurDonnees->RequeteSelection = $comp->FournisseurDonnees->NomCleBool ;
				$comp->NomColonneValeur = $comp->FournisseurDonnees->NomAttributValeur ;
				$comp->NomColonneLibelle = $comp->FournisseurDonnees->NomAttributLibelle ;
			}
		}
		
		class PvZoneDateHtml extends PvElementFormulaireHtml
		{
			public $ValeurJour = "" ;
			public $ValeurMois = "" ;
			public $ValeurAnnee = "" ;
			public $InfoJour = "Jour" ;
			public $InfoMois = "Mois" ;
			public $InfoAnnee = "Ann&eacute;e" ;
			public $SeparateurPartie = " / " ;
			public $DispositionComposants = array(1, 2, 3) ;
			public $FormatValeur = '${2}-${1}-${0}' ;
			protected $PortionsValeur = array() ;
			protected function NomFonctionRafraichitValeur()
			{
				return 'RafraichitValeur'.$this->IDInstanceCalc ;
			}
			protected function ContenuJsRafraichitValeur()
			{
				$ctn = '' ;
				$ctn .= '<script type="text/javascript">
	function '.$this->NomFonctionRafraichitValeur().'()
	{
		var formatRetenu = '.svc_json_encode($this->FormatValeur).' ;
		var resultat = formatRetenu ;
		for(var i=0; i<'.count($this->DispositionComposants).'; i++)
		{
			var indice = i.toString() ;
			var bloc = document.getElementById("'.$this->IDInstanceCalc.'_Partie" + indice) ;
			var valeurBloc = "" ;
			if(bloc.value != null)
			{
				try { valeurBloc = (bloc.value == "" || isNaN(bloc.value) == true) ? 1 : bloc.value ; } catch(ex) { }
			}
			resultat = resultat.split("${" + indice + "}").join(valeurBloc) ;
		}
		document.getElementsByName('.svc_json_encode($this->NomElementHtml).')[0].value = resultat ;
		// alert(resultat) ;
	}
</script>' ;
				return $ctn ;
			}
			protected function ExtraitPortionsValeur()
			{
				foreach($this->DispositionComposants as $i => $type)
				{
					$this->PortionsValeur[$i] = 1 ;
					if(preg_match('/\$\{'.$i.'\}/', $this->Valeur, $match))
					{
						$this->PortionsValeur[$i] = $match[0] ;
					}
				}
			}
			protected function RenduDispositifBrut()
			{
				$this->CorrigeIDsElementHtml() ;
				// $this->ExtraitPortionsValeur() ;
				$nomFonction = $this->NomFonctionRafraichitValeur() ;
				$ctn = '' ;
				$ctn .= '<input type="hidden" name="'.$this->NomElementHtml.'" id="'.$this->IDInstanceCalc.'" />'.PHP_EOL ;
				foreach($this->DispositionComposants as $i => $id)
				{
					if($i > 0)
					{
						$ctn .= $this->SeparateurPartie ;
					}
					switch($id)
					{
						case PvDispositionZoneDate::Jour :
						{
							$ctn .= '<input type="text" id="'.$this->IDInstanceCalc.'_Partie'.$i.'" value="'.htmlentities($this->PortionsValeur[$i]).'" title="'.$this->InfoJour.'" size="2" maxlength="2" onchange="'.$nomFonction.'(this)" />' ;
						}
						break ;
						case PvDispositionZoneDate::Mois :
						{
							$ctn .= '<input type="text" id="'.$this->IDInstanceCalc.'_Partie'.$i.'" maxlength="2" size="2" value="'.htmlentities($this->PortionsValeur[$i]).'" title="'.$this->InfoMois.'" onchange="'.$nomFonction.'(this)" />' ;
						}
						break ;
						case PvDispositionZoneDate::Annee :
						{
							$ctn .= '<input type="text" size="4" maxlength="4" id="'.$this->IDInstanceCalc.'_Partie'.$i.'" value="'.htmlentities($this->PortionsValeur[$i]).'" title="'.$this->InfoAnnee.'" onchange="'.$nomFonction.'(this)" />';
						}
						break ;
					}
				}
				$ctn .= $this->ContenuJsRafraichitValeur() ;
				return $ctn ;
			}
		}
		class PvDispositionZoneDate
		{
			const Jour = 1 ;
			const Mois = 2 ;
			const Annee = 3 ;
		}
	
	}
	
?>