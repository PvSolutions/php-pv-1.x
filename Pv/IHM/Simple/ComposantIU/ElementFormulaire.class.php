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
		
		class PvFmtLblBase
		{
			public function Rendu($valeur, & $composant)
			{
				return $valeur ;
			}
		}
		class PvFmtLblWeb extends PvFmtLblBase
		{
			public function Rendu($valeur, & $composant)
			{
				return ($composant->EncodeHtmlEtiquette) ? htmlentities($valeur) : $valeur ;
			}
		}
		class PvFmtLblDateFr extends PvFmtLblBase
		{
			public function Rendu($valeur, & $composant)
			{
				return date_fr($valeur) ;
			}			
		}
		class PvFmtLblDateTimeFr extends PvFmtLblBase
		{
			public function Rendu($valeur, & $composant)
			{
				return date_time_fr($valeur) ;
			}			
		}
		class PvFmtMonnaie extends PvFmtLblBase
		{
			public $MaxDecimals = 3 ;
			public $MinChiffres = 1 ;
			public function Rendu($valeur, & $composant)
			{
				return format_money($valeur, $this->MaxDecimals, $this->MinChiffres) ;
			}
		}
		
		class PvElementFormulaireHtml extends PvBaliseHtmlBase
		{
			public $Largeur = "" ;
			public $Hauteur = "" ;
			public $Valeur = "" ;
			public $EspaceReserve = "" ;
			public $FmtLbl ;
			public $EncodeHtmlEtiquette = 1 ;
			public $AttrsSupplHtml = array() ;
			protected function CreeFmtLbl()
			{
				return new PvFmtLblWeb() ;
			}
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->FmtLbl = $this->CreeFmtLbl() ;
			}
			public function EncodeEtiquette($valeur)
			{
				return $this->FmtLbl->Rendu($valeur, $composant) ;
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
				// echo $this->NomElementHtml." : ".count($this->ClassesCSS)."<br />" ;
				if(count($this->ClassesCSS) > 0)
				{
					$ctn .= ' class="'.join(" ", $this->ClassesCSS).'"' ;
				}
				return $ctn ;
			}
			protected function RenduAttrsSupplHtml()
			{
				$ctn = '' ;
				if($this->EspaceReserve != "")
				{
					$ctn .= ' placeholder="'.htmlspecialchars(html_entity_decode($this->EspaceReserve)).'"' ;
				}
				if(count($this->AttrsSupplHtml) > 0)
				{
					foreach($this->AttrsSupplHtml as $attr => $val)
					{
						$ctn .= ' '.htmlspecialchars($attr).'="'.htmlspecialchars($val).'"' ;
					}
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
				$ctn .= $this->RenduAttrStyleCSS() ;
				$ctn .= $this->RenduAttrsSupplHtml() ;
				$ctn .= '>' ;
				$ctn .= htmlentities($this->ObtientLibelle()) ;
				$ctn .= '</span>' ;
				return $ctn ;
			}
			public function RenduEtiquette()
			{
				return htmlentities($this->ObtientLibelle()) ;
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
			public $LibelleNonTrouve = "" ;
			public $FiltresSelection = array() ;
			protected function CalculeLibelle()
			{
				$lignes = $this->FournisseurDonnees->RechExacteElements($this->FiltresSelection, $this->NomColonneValeur, $this->Valeur) ;
				// print_r($this->FournisseurDonnees) ;
				$etiquette = '' ;
                // print_r($lignes) ;
				if(count($lignes) > 0)
				{
					$this->Libelle = $lignes[0][$this->NomColonneLibelle] ;
				}
				else
				{
					if($this->FournisseurDonnees->ExceptionTrouvee())
					{
						$this->Libelle = "Erreur : ".$this->DerniereException->Message ;
					}
					else
					{
						$this->Libelle = $this->LibelleNonTrouve ;
					}
				}
				return $etiquette ;
			}
			public function RenduEtiquette()
			{
				return $this->RenduDispositifBrut() ;
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
				$ctn .= '<input name="'.htmlspecialchars($this->NomElementHtml).'"' ;
				$ctn .= ' id="'.$this->IDInstanceCalc.'"' ;
				$ctn .= ' type="'.$this->TypeElementFormulaire.'"' ;
				$ctn .= $this->RenduAttrStyleCSS() ;
				$ctn .= $this->RenduAttrsSupplHtml() ;
				$ctn .= ' value="'.htmlspecialchars($this->Valeur).'"' ;
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
				$ctn .= $this->RenduAttrStyleCSS() ;
				$ctn .= $this->RenduAttrsSupplHtml() ;
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
			public $TailleEditeurCoteServeur = "40" ;
			public $TypeElementFormulaire = "file" ;
			public $NomEltCoteSrv = "CoteSrv_" ;
			public $LibelleCoteSrv = "Chemin sur le serveur" ;
			public $InclureApercu = 1 ;
			public $LibelleApercu = "Aper&ccedil;u" ;
			public $CibleApercu = "_blank" ;
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
				$ctn .= '<table>' ;
				$ctn .= '<tr>' ;
				$ctn .= '<td>'.PHP_EOL ;
				$ctn .= $this->RenduCheminCoteServeur() ;
				$ctn .= '</td>'.PHP_EOL ;
				if($this->InclureErreurTelecharg)
				{
					if($this->FiltreParent->CodeErreurTelechargement != '')
					{
						$ctn .= '<td>'.PHP_EOL ;
						$ctn .= $this->FiltreParent->LibelleErreurTelecharg ;
						$ctn .= '</td>'.PHP_EOL ;
					}
				}
				$ctn .= '</tr>'.PHP_EOL ;
				$ctn .= '</table>' ;
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
					if($this->CheminCoteServeurEditable)
					{
						$ctn .= $this->LibelleCoteSrv.' <input type="text" class="EditeurCheminCoteServeur" name="'.$nomEltCoteSrv.$this->NomElementHtml.'" value="'.htmlspecialchars(trim($this->Valeur)).'" size="'.$this->TailleEditeurCoteServeur.'" />' ;
					}
					else
					{
						$ctn .= htmlentities($this->Valeur) ;
					}
				}
				else
				{
					$ctn .= '<input type="hidden" name="'.$nomEltCoteSrv.$this->NomElementHtml.'" value="'.htmlspecialchars(trim($this->Valeur)).'" />' ;
				}
				if($this->InclureApercu && trim($this->Valeur) != '')
				{
					if($this->InclureCheminCoteServeur)
						$ctn .= '&nbsp;&nbsp;' ;
					$ctn .= '<a href="'.htmlspecialchars($this->Valeur).'" target="'.$this->CibleApercu.'">'.$this->LibelleApercu.'</a>' ;
				}
				return $ctn ;
			}
			public function RenduEtiquette()
			{
				return '<a href="'.htmlspecialchars($this->Valeur).'" target="'.$this->CibleApercu.'">'.$this->EncodeEtiquette($this->Valeur).'</a>' ;
			}
		}
		class PvZoneCocherHtml extends PvElementFormulaireHtml
		{
			public $ValeurVrai = 1 ;
			public $LibelleVrai = "Oui" ;
			public $LibelleFaux = "Non" ;
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= $this->RenduOption() ;
				return $ctn ;
			}
			public function RenduEtiquette()
			{
				return ($this->Valeur == $this->ValeurVrai) ? $this->LibelleVrai : $this->LibelleFaux ;
			}
			public function RenduOption()
			{
				$this->CorrigeIDsElementHtml() ;
				$ctn = '' ;
				$ctn .= '<input name="'.$this->NomElementHtml.'"' ;
				$ctn .= ' id="'.$this->IDInstanceCalc.'"' ;
				$ctn .= ' type="checkbox"' ;
				$ctn .= ' value="'.htmlspecialchars($this->ValeurVrai).'"' ;
				if($this->Valeur == $this->ValeurVrai)
				{
					$ctn .= ' checked' ;
				}
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
			public $SelectionStricte = false ;
			public $SeparateurLibelleEtiqVide = ", " ;
			public function & CreeFiltreRef($nom, & $filtreRef)
			{
				$filtre = new PvFiltreDonneesRef() ;
				$filtre->Source = & $filtreRef ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreFixe($nom, $valeur)
			{
				$filtre = new PvFiltreDonneesFixe() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->ValeurParDefaut = $valeur ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				return $filtre ;
			}
			public function & CreeFiltreCookie($nom)
			{
				$filtre = new PvFiltreDonneesCookie() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				return $filtre ;
			}
			public function & CreeFiltreSession($nom)
			{
				$filtre = new PvFiltreDonneesSession() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				return $filtre ;
			}
			public function & CreeFiltreMembreConnecte($nom, $nomParamLie='')
			{
				$filtre = new PvFiltreDonneesMembreConnecte() ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->NomParametreLie = $nomParamLie ;
				return $filtre ;
			}
			public function & CreeFiltreHttpUpload($nom, $cheminDossierDest="")
			{
				$filtre = new PvFiltreDonneesHttpUpload() ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->CheminDossier = $cheminDossierDest ;
				return $filtre ;
			}
			public function & CreeFiltreHttpGet($nom)
			{
				$filtre = new PvFiltreDonneesHttpGet() ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreHttpPost($nom)
			{
				$filtre = new PvFiltreDonneesHttpPost() ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreHttpRequest($nom)
			{
				$filtre = new PvFiltreDonneesHttpRequest() ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function CreeFltRef($nom, & $filtreRef)
			{
				return $this->CreeFiltreRef($nom, $filtreRef) ;
			}
			public function CreeFltFixe($nom, $valeur)
			{
				return $this->CreeFiltreRef($nom, $valeur) ;
			}
			public function CreeFltCookie($nom)
			{
				return $this->CreeFiltreCookie($nom) ;
			}
			public function CreeFltSession($nom)
			{
				return $this->CreeFiltreSession($nom) ;
			}
			public function CreeFltMembreConnecte($nom, $nomParamLie='')
			{
				return $this->CreeFiltreMembreConnecte($nom, $nomParamLie) ;
			}
			public function CreeFltHttpUpload($nom, $cheminDossierDest="")
			{
				return $this->CreeFiltreHttpUpload($nom, $cheminDossierDest) ;
			}
			public function CreeFltHttpGet($nom)
			{
				return $this->CreeFiltreHttpGet($nom) ;
			}
			public function CreeFltHttpPost($nom)
			{
				return $this->CreeFiltreHttpPost($nom) ;
			}
			public function CreeFltHttpRequest($nom)
			{
				return $this->CreeFiltreHttpRequest($nom) ;
			}
			public function & InsereFltSelectRef($nom, & $filtreRef, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreRef($nom, $filtreRef) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectFixe($nom, $valeur, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreFixe($nom, $valeur) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectCookie($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreCookie($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectSession($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreSession($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectMembreConnecte($nom, $nomParamLie='', $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreMembreConnecte($nom, $nomParamLie) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpUpload($nom, $cheminDossierDest="", $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpUpload($nom, $cheminDossierDest) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpGet($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpGet($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpPost($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpPost($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpRequest($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpRequest($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
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
				$this->InitFournisseurDonnees() ;
				$this->CalculeValeursSelectionnees() ;
				$lignes = array() ;
				if($this->ChoixMultiple == 0)
				{
					$lignes = $this->FournisseurDonnees->RechExacteElements($this->FiltresSelection, $this->NomColonneValeur, $this->Valeur) ;
					// print_r($this->FournisseurDonnees) ;
				}
				else
				{
					$lignes = $this->FournisseurDonnees->SelectElements(array(), $this->FiltresSelection) ;
				}
				// print_r($this->FournisseurDonnees) ;
				$etiquette = '' ;
				if(count($lignes) > 0)
				{
					foreach($lignes as $i => $ligne)
					{
						$estSelectionnee = 0 ;
						if($this->ChoixMultiple == 0)
						{
							$estSelectionnee = 1 ;
						}
						else
						{
							if($this->NomColonneValeurParDefaut != "" && $ligne[$this->NomColonneValeurParDefaut] == 1)
							{
								$estSelectionnee = 1 ;
							}
							elseif($this->EstValeurSelectionnee($ligne[$this->NomColonneValeur]))
							{
								$estSelectionnee = 1 ;
							}
						}
						if($estSelectionnee)
						{
							if($etiquette != "")
							{
								$etiquette .= $this->SeparateurLibelleEtiqVide ;
							}
							$etiquette .= $ligne[$this->NomColonneLibelle] ;
						}
					}
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
				return (in_array($valeur, $this->ValeursSelectionnees, $this->SelectionStricte)) ? 1 : 0 ;
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
					die("Le composant ".$this->IDInstanceCalc." necessite un fournisseur de donnees.") ;
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
				$ctn .= $this->RenduAttrsSupplHtml() ;
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
			public $CocherAutoPremiereOption = 1 ;
			protected $CalculerValeurParJs = 1 ;
			public $SeparateurLibelleOption = "&nbsp;&nbsp;" ;
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
				$ctn .= $this->RenduAttrsSupplHtml() ;
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
				if($this->CalculerValeurParJs == 1)
				{
					$ctn .= PHP_EOL .'<input type="hidden" name="'.$this->NomElementHtml.'" id="'.$this->IDInstanceCalc.'" value="'.htmlentities($this->Valeur).'" />' ;
				}
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
						$ctn = $this->RenduOptionElement($valeur, $libelle, $ligne, $position).$this->SeparateurLibelleOption.$this->RenduLibelleElement($valeur, $libelle, $ligne, $position) ;
					}
					break ;
					case "left" :
					case "gauche" :
					{
						$ctn = $this->RenduLibelleElement($valeur, $libelle, $ligne, $position).$this->SeparateurLibelleOption.$this->RenduOptionElement($valeur, $libelle, $ligne, $position) ;
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
				$ctn .= '<input type="radio" id="'.$this->IDInstanceCalc.'_'.$position.'"' ;
				$ctn .= ' value="'.htmlentities($valeur).'"' ;
				if($this->EstValeurSelectionnee($valeur) || $forcerSelection)
				{
					$ctn .= ' checked' ;
				}
				$ctn .= ' onclick="document.getElementById(&quot;'.$this->IDInstanceCalc.'&quot;).value = this.value;"' ;
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
			protected $CalculerValeurParJs = 0 ;
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
		
		class PvZoneCadreOptionsRadioHtml extends PvZoneBoiteOptionsRadioHtml
		{
			public $NomParamCadre = "Cadre" ;
			public $ValeurDefautParamCadre = "" ;
			public $ValeurParamCadre = "" ;
			public $ValeurOuvreCadre = "1" ;
			public $SepOuvreCadre = "  " ;
			public $LibelleOuvreCadre = "Parcourir" ;
			public $LibelleAnnuleSelection = "Annuler" ;
			public $LibelleValideSelection = "Valider" ;
			public $StyleIncorporation = "POPUP" ;
			public $TitreDocument ;
			public $ContenusCSS = array() ;
			public $ContenusJs = array() ;
			public $CtnExtraHead ;
			public $InclureCtnJsEntete = 0 ;
			public $LargeurCadre = "100%" ;
			public $HauteurCadre = "300" ;
			public function InscritContenuCSS($contenu)
			{
				$ctnCSS = new PvBaliseCSS() ;
				$ctnCSS->Definitions = $contenu ;
				$this->ContenusCSS[] = $ctnCSS ;
			}
			public function InscritLienCSS($href)
			{
				$ctnCSS = new PvLienFichierCSS() ;
				$ctnCSS->Href = $href ;
				$this->ContenusCSS[] = $ctnCSS ;
			}
			public function InscritContenuJs($contenu)
			{
				$ctnJs = new PvBaliseJs() ;
				$ctnJs->Definitions = $contenu ;
				$this->ContenusJs[] = $ctnJs ;
			}
			public function InscritContenuJsCmpIE($contenu, $versionMin=9)
			{
				$ctnJs = new PvBaliseJsCmpIE() ;
				$ctnJs->Definitions = $contenu ;
				$ctnJs->VersionMin = $versionMin ;
				$this->ContenusJs[] = $ctnJs ;
			}
			public function InscritLienJs($src)
			{
				$ctnJs = new PvLienFichierJs() ;
				$ctnJs->Src = $src ;
				$this->ContenusJs[] = $ctnJs ;
			}
			public function InscritLienJsCmpIE($src, $versionMin=9)
			{
				$ctnJs = new PvLienFichierJsCmpIE() ;
				$ctnJs->Src = $src ;
				$ctnJs->VersionMin = $versionMin ;
				$this->ContenusJs[] = $ctnJs ;
			}
			public function RenduLienCSS($href)
			{
				$ctnCSS = new PvLienFichierCSS() ;
				$ctnCSS->Href = $href ;
				return $ctnCSS->RenduDispositif() ;
			}
			public function RenduContenuCSS($contenu)
			{
				$ctnCSS = new PvBaliseCSS() ;
				$ctnCSS->Definitions = $contenu ;
				return $ctnCSS->RenduDispositif() ;
			}
			public function RenduContenuJsInclus($contenu)
			{
				$ctn = '' ;
				$ctnJs = new PvBaliseJs() ;
				$ctnJs->Definitions = $contenu ;
				if(! $this->InclureCtnJsEntete)
				{
					$this->ContenusJs[] = $ctnJs ;
				}
				else
				{
					$ctn = $ctnJs->RenduDispositif() ;
				}
				return $ctn ;
			}
			public function RenduContenuJsCmpIEInclus($contenu, $versionMin=9)
			{
				$ctn = '' ;
				$ctnJs = new PvBaliseJsCmpIE() ;
				$ctnJs->Definitions = $contenu ;
				$ctnJs->VersionMin = $versionMin ;
				if(! $this->InclureCtnJsEntete)
				{
					$this->ContenusJs[] = $ctnJs ;
				}
				else
				{
					$ctn = $ctnJs->RenduDispositif() ;
				}
				return $ctn ;
			}
			public function RenduLienJsInclus($src)
			{
				$ctn = '' ;
				$ctnJs = new PvLienFichierJs() ;
				$ctnJs->Src = $src ;
				if(! $this->InclureCtnJsEntete)
				{
					$this->ContenusJs[] = $ctnJs ;
				}
				else
				{
					$ctn = $ctnJs->RenduDispositif() ;
				}
				return $ctn ;
			}
			public function RenduLienJsCmpIEInclus($src, $versionMin=9)
			{
				$ctn = '' ;
				$ctnJs = new PvLienFichierJsCmpIE() ;
				$ctnJs->Src = $src ;
				$ctnJs->VersionMin = $versionMin ;
				$this->ContenusJs[] = $ctnJs ;
				if(! $this->InclureCtnJsEntete)
				{
					$this->ContenusJs[] = $ctnJs ;
				}
				else
				{
					$ctn = $ctnJs->RenduDispositif() ;
				}
				return $ctn ;
			}
			protected function RenduCtnsCSS()
			{
				$ctn = '' ;
				for($i=0; $i<count($this->ContenusCSS); $i++)
				{
					$ctnCSS = $this->ContenusCSS[$i] ;
					$ctn .= $ctnCSS->RenduDispositif().PHP_EOL ;
				}
				return $ctn ;
			}
			protected function RenduCtnsJs()
			{
				$ctn = '' ;
				for($i=0; $i<count($this->ContenusJs); $i++)
				{
					$ctnJs = $this->ContenusJs[$i] ;
					$ctn .= $ctnJs->RenduDispositif().PHP_EOL ;
				}
				return $ctn ;
			}
			protected function RenduEnteteDocCadre()
			{
				$ctn = '' ;
				$ctn .= '<!doctype html>'.PHP_EOL ;
				$ctn .= '<head>'.PHP_EOL ;
				$ctn .= '<title>'.$this->TitreDocument.'</title>'.PHP_EOL ;
				$ctn .= $this->RenduCtnsCSS() ;
				if($this->InclureCtnJsEntete == 1)
				{
					$ctn .= $this->RenduCtnsJs() ;
				}
				$ctn .= $this->CtnExtraHead ;
				$ctn .= '</head>'.PHP_EOL ;
				$ctn .= '<body>' ;
				return $ctn ;
			}
			protected function RenduPiedDocCadre()
			{
				$ctn = '' ;
				if($this->InclureCtnJsEntete == 0)
				{
					$ctn .= $this->RenduCtnsJs() ;
				}
				$ctn .= '</body>'.PHP_EOL ;
				$ctn .= '</html>' ;
				return $ctn ;
			}
			protected function RenduValeurCadre()
			{
				return $this->RenduEtiquette() ;
			}
			protected function DetecteCadre()
			{
				$this->ValeurParamCadre = $this->ValeurDefautParamCadre ;
				if(isset($_GET[$this->IDInstanceCalc.'_'.$this->NomParamCadre]))
				{
					$valBrute = $_GET[$this->IDInstanceCalc.'_'.$this->NomParamCadre] ;
					if($valBrute == $this->ValeurOuvreCadre)
					{
						$this->ValeurParamCadre = $this->ValeurOuvreCadre ;
					}
				}
				return $this->ValeurParamCadre == $this->ValeurOuvreCadre ;
			}
			protected function RenduCadre()
			{
				$ctn = '' ;
				$ctn .= $this->RenduEnteteDocCadre() ;
				$ctn .= $this->RenduCorpsDocCadre() ;
				$ctn .= $this->RenduPiedDocCadre() ;
				return $ctn ;
			}
			protected function RenduCorpsDocCadre()
			{
				$ctn = '' ;
				$this->InitFournisseurDonnees() ;
				if(! $this->EstNul($this->FournisseurDonnees))
				{
					$this->ChargeConfigFournisseurDonnees() ;
					$this->CalculeElementsRendu() ;
					$ctn .= $this->RenduListeElements() ;
					$ctn .= $this->RenduBtnsExec() ;
				}
				else
				{
					die("Le composant ".$this->IDInstanceCalc." n�cessite un fournisseur de donn�es.") ;
				}				
				return $ctn ;
			}
			protected function RenduBtnsExec()
			{
				$ctn = '' ;
				$ctn .= '<script language="javascript">
	function annuleSelect() {
		window.close() ;
	}
	function valideSelect() {
		var cibleFenetre = '.(($this->StyleIncorporation == "POPUP") ? 'window.opener' : 'window.parent').' ;
		var valeurChoisie = "" ;
		var libelleChoisi = "" ;
		// alert(cibleFenetre.document.getElementById("'.$this->IDInstanceCalc.'")) ;
		// alert(document.getElementById("'.$this->IDInstanceCalc.'")) ;
		var lstElems = document.getElementsByName("'.htmlentities($this->NomElementHtml).'") ;
		for(var i=0; i<lstElems.length; i++)
		{
			var elem = lstElems[i] ;
			if(elem.checked){
				valeurChoisie = elem.value ;
				libelleChoisi = elem.title ;
				break ;
			}
		}
		cibleFenetre.document.getElementById("'.$this->IDInstanceCalc.'").value = valeurChoisie ;
		cibleFenetre.document.getElementById("'.$this->IDInstanceCalc.'_Libelle").firstChild.data = libelleChoisi ;
		window.close() ;
	}
</script>'.PHP_EOL ;
				$ctn .= '<div class="Bloc_Commandes">';
				if($this->StyleIncorporation == "POPUP")
				{
					$ctn .= '<input type="button" onclick="annuleSelect()" value="'.htmlentities($this->LibelleAnnuleSelection).'" />' ;
					$ctn .= '&nbsp;&nbsp;&nbsp;&nbsp;' ;
				}
				$ctn .= '<input type="button" onclick="valideSelect()" value="'.htmlentities($this->LibelleValideSelection).'" />' ;
				$ctn .= '</div>' ;
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
				$ctn .= ' title="'.htmlentities($libelle).'"' ;
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
			protected function AfficheCadre()
			{
				if(! $this->DetecteCadre())
					return 0 ;
				$ctn = $this->RenduCadre() ;
				echo $ctn ;
				exit ;
			}
			protected function RenduDispositifBrut()
			{
				// echo $this->StyleIncorporation ;
				$this->AfficheCadre() ;
				$url = get_current_url() ;
				$this->CorrigeIDsElementHtml() ;
				$ctn = '' ;
				$ctn .= '<div class="Conteneur'.$this->IDInstanceCalc.'"><input type="hidden" name="'.$this->NomElementHtml.'" id="'.$this->IDInstanceCalc.'" value="'.htmlentities($this->Valeur).'" /><span id="'.$this->IDInstanceCalc.'_Libelle">'.htmlentities($this->RenduValeurCadre()).'</span></div>' ;
				$urlCadre = update_url_params($url, array($this->IDInstanceCalc.'_'.$this->NomParamCadre => 1)) ;
				switch(strtoupper($this->StyleIncorporation))
				{
					case "POPUP" :
					{
						$ctn .= '<div><a href="'.$urlCadre.'" target="popup'.$this->IDInstanceCalc.'">'.$this->LibelleOuvreCadre.'</a></div>' ;
					}
					break ;
					case "IFRAME" :
					case "CADRE" :
					{
						$ctn .= '<iframe src="'.$urlCadre.'" width="'.$this->LargeurCadre.'" frameborder="0" height="'.$this->HauteurCadre.'"></iframe>' ;
					}
					break ;
				}
				return $ctn ;
			}
		}
		
		class PvZoneSelectHtml extends PvZoneBoiteSelectHtml
		{
		}
		class PvZoneSelectBoolHtml extends PvZoneBoiteSelectHtml
		{
			public $SelectionStricte = true ;
			public $LibelleVrai = "" ;
			public $LibelleFaux = "" ;
			public $ValeurVrai = "" ;
			public $ValeurFaux = "" ;
			protected function EstValeurSelectionnee($valeur)
			{
				// print $this->IDInstanceCalc ;
				return (in_array($valeur, $this->ValeursSelectionnees, $this->SelectionStricte)) ? 1 : 0 ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				PvComposantIUBoolHtml::RemplitConfig($this) ;
			}
		}
		class PvZoneCocherBoolHtml extends PvElementFormulaireHtml
		{
			public $ValeurVrai = "1" ;
			public $ValeurFaux = "0" ;
			public $LibelleVrai = "Oui" ;
			public $LibelleFaux = "Non" ;
			public function RenduEtiquette()
			{
				return ($this->Valeur == $this->ValeurVrai) ? $this->LibelleVrai : $this->LibelleFaux ;
			}
			protected function RenduDispositifBrut()
			{
				$this->CorrigeIDsElementHtml() ;
				$ctn = '' ;
				$styleCSS = '' ;
				$ctn .= '<input id="'.$this->IDInstanceCalc.'_Support" type="checkbox" value="'.htmlentities($this->ValeurVrai).'" onclick="document.getElementById(\''.$this->IDInstanceCalc.'\').value = (this.checked) ? '.htmlentities(svc_json_encode($this->ValeurVrai)).' : '.htmlentities(svc_json_encode($this->ValeurFaux)).';"'.(($this->Valeur == $this->ValeurVrai) ? ' checked' : '').' />' ;
				$ctn .= '<input name="'.$this->NomElementHtml.'"' ;
				$ctn .= ' id="'.$this->IDInstanceCalc.'"' ;
				$ctn .= ' type="hidden"' ;
				$ctn .= ' value="'.htmlentities($this->Valeur).'"' ;
				$ctn .= ' />' ;
				return $ctn ;
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
		
		class PvZoneBoiteOptionsRadioBootstrap extends PvZoneBoiteOptionsRadioHtml
		{
			protected function RenduListeElements()
			{
				$this->CorrigeIDsElementHtml() ;
				$ctn = '' ;
				$ctn .= $this->RenduFoncJs() ;
				$ctn .= $this->RenduLiens() ;
				$ctn .= '<table' ;
				$ctn .= ' name="Conteneur_'.$this->NomElementHtml.'"' ;
				$ctn .= ' id="Conteneur_'.$this->IDInstanceCalc.'"' ;
				$ctn .= $this->RenduAttrsSupplHtml() ;
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
				// $ctn .= '<input type="hidden" name="'.$this->NomElementHtml.'" id="'.$this->IDInstanceCalc.'" value="'.htmlentities($this->Valeur).'" />' ;
				// print_r($this->FournisseurDonnees->BaseDonnees) ;
				return $ctn ;
			}
		}
		class PvZoneBoiteOptionsCocherBootstrap extends PvZoneBoiteOptionsCocherHtml
		{
			protected function RenduListeElements()
			{
				$this->CorrigeIDsElementHtml() ;
				$ctn = '' ;
				$ctn .= $this->RenduFoncJs() ;
				$ctn .= $this->RenduLiens() ;
				$ctn .= '<table' ;
				$ctn .= ' name="Conteneur_'.$this->NomElementHtml.'"' ;
				$ctn .= ' id="Conteneur_'.$this->IDInstanceCalc.'"' ;
				$ctn .= $this->RenduAttrsSupplHtml() ;
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
				// $ctn .= '<input type="hidden" name="'.$this->NomElementHtml.'" id="'.$this->IDInstanceCalc.'" value="'.htmlentities($this->Valeur).'" />' ;
				// print_r($this->FournisseurDonnees->BaseDonnees) ;
				return $ctn ;
			}
		}

	}
	
?>