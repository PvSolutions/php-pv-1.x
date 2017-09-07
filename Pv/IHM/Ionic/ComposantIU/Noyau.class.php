<?php
	
	if(! defined('PV_COMPOSANT_IU_NOYAU_IONIC'))
	{
		define('PV_COMPOSANT_IU_NOYAU_IONIC', 1) ;
		
		define('PV_MSG_EXEC_DEFAUT_COMMANDE_IONIC', 'Commande non execut&eacute;e') ;
		
		class PvElementAccessibleIonic extends PvObjet
		{
			public $Visible = 1 ;
			public $NomElementPageSrc = "" ;
			public $PageSrcParent ;
			protected $_NomsMtdDist = array() ;
			public function AdoptePageSrc($nom, & $pageSrc)
			{
				$this->PageSrcParent = & $pageSrc ;
				$this->NomElementPageSrc = $nom ;
			}
			public function & ZoneParent()
			{
				return $this->PageSrcParent->ZoneParent ;
			}
			public function ChargeConfig()
			{
			}
			public function PrepareDeploiement()
			{
			}
		}
		
		class PvComposantIUNoyauIonic extends PvElementAccessibleIonic
		{
			public $PageSrcParent ;
			public $NomElementPageSrc ;
			public $NgIf = "" ;
			public $NgFor = "" ;
			public function AttrsHtmlNg()
			{
				$ctn = '' ;
				if($this->NgIf != '')
				{
					$ctn .= ' *ngIf="'.$this->NgIf.'"' ;
				}
				if($this->NgFor != '')
				{
					$ctn .= ' *ngFor="'.$this->NgFor.'"' ;
				}
				return $ctn ;
			}
			public function & ZoneParent()
			{
				return $this->PageSrcParent->ZoneParent ;
			}
			public function & MtdDistSelect()
			{
				$mtdDist = new PvMtdDistNonTrouveeIonic() ;
				if($this->ZoneParent()->PossedeMtdDistSelect())
				{
					$mtdDist = & $this->ZoneParent()->MtdDistSelect ;
				}
				return $mtdDist ;
			}
			public function AdoptePageSrc($nom, & $pageSrc)
			{
				$this->PageSrcParent = & $pageSrc ;
				$this->NomElementPageSrc = $nom ;
			}
			public function FournitMethodesDistantes()
			{
			}
			public function NomMethodeDistante($nom)
			{
				return "ComposantIU_".$this->PageSrcParent->NomElementZone."_".$this->NomElementPageSrc."_".$nom ;
			}
			public function & InsereMethodeDistante($nom, $methodeDistante)
			{
				$this->_NomsMtdDist[] = $nom ;
				$result = $this->ZoneParent()->InsereMethodeDistante($this->NomMethodeDistante($nom), $methodeDistante) ;
				$result->AdopteComposantIU($nom, $this) ;
				return $result ;
			}
			public function & InsereMtdDist($nom, $mtdDist)
			{
				return $this->InsereMethodeDistante($nom, $methodeDistante) ;
			}
			public function & MethodesDistantes()
			{
				$mtdsDist = array() ;
				foreach($this->_NomsMtdDist as $i => $nom)
				{
					$mtdsDist[$nom] = & $this->ZoneParent()->MethodesDistantes[$this->NomElementPageSrc."_".$nom] ;
				}
				return $mtdsDist ;
			}
			public function & MtdsDists()
			{
				return $this->MethodesDistantes() ;
			}
			public function & MethodeDistante($nom)
			{
				$nomMtd = $this->NomMethodeDistante($nom) ;
				$mtdDist = new PvMtdDistNonTrouveeIonic() ;
				if(! isset($this->ZoneParent->MethodesDistantes[$nomMtd]))
				{
					return $mtdDist ;
				}
				return $this->ZoneParent->MethodesDistantes[$nomMtd] ;
			}
			public function UrlMethodeDistante($nom)
			{
				$mtd = $this->MethodeDistante($nom) ;
				return $mtd->ObtientUrl() ;
			}
			public function AppelTsMtdDist($nom, $args=array(), $fonctSucces=null, $fonctErreur=null)
			{
				$serviceUtils = & $this->ZoneParent()->ServiceSrcUtils ;
				$ctn = '' ;
				$ctn = $serviceUtils->AppelTsMtdDist($nom, $args, $fonctSucces, $fonctErreur) ;
				return $ctn ;
			}
			public function Deploie()
			{
			}
			protected function PrepareRendu()
			{
			}
		}
		class PvComposantIUIndefIonic extends PvComposantIUNoyauIonic
		{
		}
		
		class PvComposantDonneesBaseIonic extends PvComposantIUNoyauIonic
		{
			public $FournisseurDonnees ;
			public $SourceValeursSuppl ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->SourceValeursSuppl = new PvSrcValsSupplLgnDonnees() ;
			}
			protected function ExtraitValeursLgnDonnees(& $lgn)
			{
				if($this->EstNul($this->SourceValeursSuppl))
				{
					return $lgn ;
				}
				return $this->SourceValeursSuppl->Applique($this, $lgn) ;
			}
			public function & CreeFiltreFixe($nom, $valeur)
			{
				$filtre = new PvFiltreFixeIonic() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->ValeurParDefaut = $valeur ;
				$filtre->AdoptePageSrc($nom, $this->PageSrcParent) ;
				return $filtre ;
			}
			public function & CreeFiltreTs($nom, $corpsBrut='')
			{
				$filtre = new PvFiltreTsIonic() ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->CorpsBrutTs = $corpsBrut ;
				$filtre->AdoptePageSrc($nom, $this->PageSrcParent) ;
				return $filtre ;
			}
			public function & CreeFiltreHttpRequest($nom)
			{
				$filtre = new PvFiltreHttpRequestIonic() ;
				$filtre->AdoptePageSrc($nom, $this->PageSrcParent) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFltTs($nom, $corpsBrut)
			{
				return $this->CreeFiltreTs($nom, $corpsBrut) ;
			}
			public function & CreeFltFixe($nom, $valeur)
			{
				return $this->CreeFiltreFixe($nom, $valeur) ;
			}
			public function & CreeFltHttpRequest($nom)
			{
				return $this->CreeFiltreHttpRequest($nom) ;
			}
			public function CalculeElementsRendu()
			{
			}
			public function VerifiePreRequisRendu()
			{
				return 1 ;
			}
			public function ExtraitFiltresDeRendu(& $filtres, $filtresCaches=array())
			{
				$resultats = array() ;
				foreach($filtres as $i => $filtre)
				{
					// print $i.'- '.$filtre->NomParametreLie.' '.$filtre->RenduPossible().'<br />' ;
					if($filtre->RenduPossible() && ! in_array($filtre->NomParametreLie, $filtresCaches))
					{
						$resultats[$i] = & $filtres[$i] ;
					}
				}
				return $resultats ;
			}
			public function ExtraitFiltresAffichables(& $filtres)
			{
				$resultats = array() ;
				foreach($filtres as $i => $filtre)
				{
					if($filtre->RenduPossible() && ! $filtre->LectureSeule)
					{
						$resultats[$i] = & $filtres[$i] ;
					}
				}
				return $resultats ;
			}
			public function MsgPreRequisRenduNonVerifies()
			{
				return "(PRE REQUIS DU RENDU NON VERIFIES)" ;
			}
			public function LieFiltres(& $filtres)
			{
				$nomFiltres = array_keys($filtres) ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtres[$nomFiltre]->Lie() ;
				}
			}
			public function ArgsTsFiltres(& $filtres)
			{
				$args = array() ;
				foreach($filtres as $nom => $flt)
				{
					if($flt->Invisible == 1 || $flt->NomParametreLie == '' || $flt->NePasLierParametre == 1)
					{
						continue ;
					}
					$args[] = $flt->NomParametreLie.' : '.$flt->ValeurTs() ;
				}
				return '{'.join(", ", $args).'}' ;
			}
			public function ArgsTsFiltresDistants()
			{
				return '{}' ;
			}
			protected function DeploieContenuFiltresTs(& $filtres, & $classeTs)
			{
				foreach($filtres as $n => $filtre)
				{
					if($filtre->Invisible == 1)
					{
						continue ;
					}
					$filtre->DefinitionTs($classeTs) ;
				}
			}
			protected function DeploieContenuCommandesTs(& $commandes, & $classeTs)
			{
				$commandes = & $this->Commandes ;
				foreach($commandes as $i => $commande)
				{
					$methode = $classeTs->InsereMethode('execute'.$commande->IDInstanceCalc) ;
					$methode->CorpsBrut = $commande->CorpsBrutMethodeTs() ;
					$commande->DeploieContenuTs($classeTs) ;
				}
			}
			public function & ParamAppelDistant()
			{
				return $this->MtdDistSelect()->Param() ;
			}
			public function & ParamAppelDist()
			{
				return $this->MtdDistSelect()->Param() ;
			}
		}
		
		class PvDessinFiltresBaseIonic
		{
			public $PositionLibelle = "floating" ;
			public function Execute(& $pageSrc, & $composant, $parametres)
			{
				return '' ;
			}
			protected function RenduFiltre(& $filtre, & $composant)
			{
				$ctn = '' ;
				if($composant->Editable && $filtre->EstEtiquette == 0)
				{
					$ctn .= $filtre->Rendu($composant->PageSrcParent) ;
				}
				else
				{
					$ctn .= $filtre->Etiquette($composant->PageSrcParent) ;
				}
				return $ctn ;
			}
			protected function RenduMarquesFiltre(& $marques)
			{
				$ctn = '' ;
				/*
				foreach($marques as $i => $marque)
				{
					$ctn .= ' <span style="color:'.$marque->CouleurPolice.';">'.$marque->Contenu.'</span>' ;
				}
				*/
				return $ctn ;
			}
			protected function RenduLibelleFiltre(& $filtre)
			{
				$ctn = '' ;
				$comp = $filtre->ObtientComposant() ;
				if($comp->AccepterLibelle == 0)
				{
					return$ctn ;
				}
				$ctn .= '<ion-label '.$this->PositionLibelle.'>' ;
				$ctn .= $this->RenduMarquesFiltre($filtre->PrefixesLibelle) ;
				$ctn .= $filtre->ObtientLibelle() ;
				$ctn .= $this->RenduMarquesFiltre($filtre->SuffixesLibelle) ;
				$ctn .= '</ion-label>' ;
				return $ctn ;				
			}

		}
		class PvDessinFiltres1Ionic extends PvDessinFiltresBaseIonic
		{
			public function Execute(& $pageSrc, & $composant, $parametres)
			{
				$ctn = '' ;
				$filtres = $composant->ExtraitFiltresDeRendu($parametres) ;
				$ctn .= '<ion-list>'.PHP_EOL ;
				foreach($filtres as $n => $filtre)
				{
					if($filtre->RenduPossible() == 0)
					{
						continue ;
					}
					$ctn .= '<ion-item>'.PHP_EOL ;
					$ctn .= $this->RenduLibelleFiltre($filtre) ;
					$ctn .= $this->RenduFiltre($filtre, $composant) ;
					$ctn .= '</ion-item>'.PHP_EOL ;
				}
				$ctn .= '</ion-list>' ;
				return $ctn ;
			}
		}
		
		class PvDessinBlocCommandesBaseIonic
		{
			public function Execute(& $pageSrc, & $composant, $parametres)
			{
				$ctn = '' ;
				return $ctn ;
			}
		}
		class PvDessinBlocCommandes1Ionic extends PvDessinBlocCommandesBaseIonic
		{
			public function Execute(& $pageSrc, & $composant, $parametres)
			{
				$ctn = '' ;
				$commandes = $parametres ;
				$ctn .= '<div align="center">'.PHP_EOL ;
				foreach($commandes as $nom => $commande)
				{
					$ctn .= $commande->RenduTagIonic($this).PHP_EOL ;
				}
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		
		class PvComposantFiltrableIonic extends PvComposantDonneesBaseIonic
		{
			public $FiltresSelection = array() ;
			public function & InsereFltSelectFixe($nom, $valeur, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreFixe($nom, $valeur) ;
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
			public function CalculeElementsRendu()
			{
			}
			public function VerifiePreRequisRendu()
			{
				return 1 ;
			}
			public function ObtientFiltresSelection()
			{
				return $this->FiltresSelection ;
			}
			protected function RenduDispositifBrutSpec()
			{
			}
		}
		
		class PvInfoExecutionIonic
		{
			public $Statut = 0 ;
			public $Message = "" ;
		}
		
		class PvCommandeBaseIonic extends PvElementAccessibleIonic
		{
			public $StatutExecution = -1 ;
			public $MessageExecution = PV_MSG_EXEC_DEFAUT_COMMANDE_IONIC ;
			public $MessageSuccesExecution = "Commande execut&eacute;e avec succ&egrave;s" ;
			public $AttrIonSize = "" ;
			public $AttrIonColor = "" ;
			public $AttrIonOutligne = false ;
			public $AttrIonRound = false ;
			public $AttrIonClear = false ;
			public $AttrIonBlock = false ;
			public $AttrIonFull = false ;
			public $NomIcone = "" ;
			public $AlignIcone = "" ;
			public $InclureIcone = 1 ;
			public $Libelle = "Commande" ;
			public $Criteres = array() ;
			public $Actions = array() ;
			public $NomElementComposantIU ;
			public $ComposantIUParent ;
			public $FormulaireDonneesParent ;
			public $NomElementTableauDonnees ;
			public $TableauDonneesParent ;
			public $SeparateurCriteresNonRespectes = "," ;
			public function InscritCritere(& $critere)
			{
				$this->Criteres[] = & $critere ;
				$critere->AdopteCommande(count($this->Criteres), $this) ;
			}
			public function InscritCritr(& $critere)
			{
				$this->InscritCritere($critere) ;
			}
			public function & InsereCritereFormatUrl($nomFiltres = array())
			{
				$critere = new PvCritereFormatUrl() ;
				$this->InscritCritere($critere) ;
				call_user_func_array(array(& $critere, 'CibleFiltres'), $nomFiltres) ;
				return $critere ;
			}
			public function & InsereCritereFormatMotPasse($nomFiltres = array())
			{
				$critere = new PvCritereFormatMotPasse() ;
				$this->InscritCritere($critere) ;
				call_user_func_array(array(& $critere, 'CibleFiltres'), $nomFiltres) ;
				return $critere ;
			}
			public function & InsereCritereFormatLogin($nomFiltres = array())
			{
				$critere = new PvCritereFormatLogin() ;
				$this->InscritCritere($critere) ;
				call_user_func_array(array(& $critere, 'CibleFiltres'), $nomFiltres) ;
				return $critere ;
			}
			public function & InsereCritereFormatEmail($nomFiltres = array())
			{
				$critere = new PvCritereFormatEmail() ;
				$this->InscritCritere($critere) ;
				call_user_func_array(array(& $critere, 'CibleFiltres'), $nomFiltres) ;
				return $critere ;
			}
			public function & InsereCritereNonVide($nomFiltres = array())
			{
				$critere = new PvCritereNonVide() ;
				$this->InscritCritere($critere) ;
				call_user_func_array(array(& $critere, 'CibleFiltres'), $nomFiltres) ;
				return $critere ;
			}
			public function & InsereCritrNonVide($nomFiltres = array())
			{
				$critere = $this->InsereCritereNonVide($nomFiltres) ;
				return $critere ;
			}
			public function & InscritNouvActCmd($actCmd, $nomFiltresCibles=array())
			{
				return $this->InscritActCmd($actCmd, $nomFiltresCibles) ;
			}
			public function & InsereCritere($nomClasse, $nomFiltresCibles=array())
			{
				if(! class_exists($nomClasse))
				{
					die("La classe '$nomClasse' n'existe pas") ;
				}
				$critere = new $nomClasse() ;
				$this->InsereNouvCritere($critere, $nomFiltresCibles) ;
				return $critere ;
			}
			public function & InsereActCmd($nomClasse, $nomFiltresCibles=array())
			{
				if(! class_exists($nomClasse))
				{
					die("La classe '$nomClasse' n'existe pas") ;
				}
				$actCmd = new $nomClasse() ;
				$this->InscritNouvActCmd($actCmd, $nomFiltresCibles) ;
				return $actCmd ;
			}
			public function & InsereAction($nomClasse, $nomFiltresCibles=array())
			{
				$action = $this->InsereActCmd($nomClasse, $nomFiltresCibles) ;
				return $action ;
			}
			public function & InsereNouvCritere($critere, $nomFiltresCibles=array())
			{
				$this->InscritCritere($critere) ;
				call_user_func_array(array($critere, 'CibleFiltres'), $nomFiltresCibles) ;
				return $critere ;
			}
			public function & InsereNouvActCmd($actCmd, $nomFiltresCibles=array())
			{
				$this->InscritAction($actCmd) ;
				call_user_func_array(array($actCmd, 'CibleFiltres'), $nomFiltresCibles) ;
				return $actCmd ;
			}
			public function & InsereNouvAction($action, $nomFiltresCibles=array())
			{
				$action = $this->InsereActCmd($nomClasse, $nomFiltresCibles) ;
				return $action ;
			}
			public function InscritNouvAction($actCmd)
			{
				$this->InscritActCmd($actCmd) ;
			}
			public function InscritActCmd(& $actCmd, $nomFiltresCibles=array())
			{
				$this->Actions[] = & $actCmd ;
				$actCmd->AdopteCommande(count($this->Actions), $this) ;
				call_user_func_array(array($actCmd, 'CibleFiltres'), $nomFiltresCibles) ;
				return $actCmd ;
			}
			public function InscritAction(& $actCmd)
			{
				$this->InscritActCmd($actCmd) ;
			}
			protected function AdopteComposantIU($nom, & $composantIU)
			{
				$this->NomElementComposantIU = $nom ;
				$this->ComposantIUParent = & $composantIU ;
			}
			public function AdopteFormulaireDonnees($nom, & $formulaireDonnees)
			{
				$this->NomElementFormulaireDonnees = $nom ;
				$this->FormulaireDonneesParent = & $formulaireDonnees ;
				$this->AdopteComposantIU($nom, $formulaireDonnees) ;
			}
			public function AdopteTableauDonnees($nom, & $tableauDonnees)
			{
				$this->NomElementTableauDonnees = $nom ;
				$this->TableauDonneesParent = & $tableauDonnees ;
				$this->AdopteComposantIU($nom, $tableauDonnees) ;
			}
			public function & FichTsParent()
			{
				return $this->PageSrcParent()->FichTs ;
			}
			public function & PageSrcParent()
			{
				if($this->EstNul($this->ComposantIUParent))
				{
					$pageSrc = new PvPageSrcIndefIonic() ;
					return $pageSrc ;
				}
				return $this->ComposantIUParent->PageSrcParent ;
			}
			public function & ZoneParent()
			{
				return $this->PageSrcParent()->ZoneParent ;
			}
			protected function RenduAttrsIonic()
			{
				$ctn = '' ;
				if($this->AttrIonSize != "")
				{
					$ctn .= ' '.$this->AttrIonSize ;
				}
				if($this->AttrIonColor != "")
				{
					$ctn .= ' color="'.$this->AttrIonColor.'"' ;
				}
				if($this->AttrIonOutline == true)
				{
					$ctn .= ' outline' ;
				}
				if($this->AttrIonRound == true)
				{
					$ctn .= ' round' ;
				}
				if($this->AttrIonClear == true)
				{
					$ctn .= ' clear' ;
				}
				if($this->AttrIonBlock == true)
				{
					$ctn .= ' block' ;
				}
				if($this->AttrIonFull == true)
				{
					$ctn .= ' full' ;
				}
				return $ctn ;
			}
			public function RenduTagIonic(& $dessin)
			{
				$ctn = '' ;
				$inclureIcone = ($this->InclureIcone == 1 && $this->NomIcone != "") ? 1 : 0 ;
				$ctn .= '<button ion-button'.(($inclureIcone == 1 && $this->AlignIcone == "") ? ' icon-'.$this->AlignIcone : '').$this->RenduAttrsIonic().' (click)="execute'.$this->IDInstanceCalc.'()">
'.(($inclureIcone == 1) ? '<ion-icon name="'.$this->NomIcone.'"></ion-icon> ' : '').$this->Libelle.'
</button>' ;
				return $ctn ;
			}
			protected function VideStatutExecution()
			{
				$this->StatutExecution = -1 ;
				$this->MessageExecution = PV_MSG_EXEC_DEFAUT_COMMANDE_IONIC ;
			}
			public function PrepareRendu(& $composant)
			{
			}
			protected function VerifiePreRequis()
			{
				$this->ConfirmeSucces() ;
			}
			protected function RespecteCriteres()
			{
				$indCriteres = array_keys($this->Criteres) ;
				$messageErreurs = array() ;
				foreach($indCriteres as $i => $indCritere)
				{
					$critere = & $this->Criteres[$indCritere] ;
					if($critere->EstRespecte() == 0)
					{
						$messageErreurs[] = $critere->MessageErreur ;
					}
				}
				$ok = 1 ;
				if(count($messageErreurs) > 0)
				{
					$this->RenseigneErreur(join($this->SeparateurCriteresNonRespectes, $messageErreurs)) ;
					$ok = 0 ;
				}
				return $ok ;
			}
			protected function ExecuteActions()
			{
				$nomActions = array_keys($this->Actions) ;
				if(count($nomActions) > 0)
				{
					if($this->MessageExecution == '')
					{
						$this->MessageExecution = $this->MessageSuccesExecution ;
					}
					foreach($nomActions as $i => $nomAction)
					{
						$action = & $this->Actions[$nomAction] ;
						$action->Execute() ;
					}
				}
			}
			public function Execute()
			{
				$this->VideStatutExecution() ;
				if(! $this->RespecteCriteres())
				{
					return ;
				}
				$this->VerifiePreRequis() ;
				if($this->EstErreur())
				{
					return ;
				}
				$this->ExecuteInstructions() ;
				if($this->EstErreur())
				{
					return ;
				}
			}
			public function EstSucces()
			{
				return $this->StatutExecution == 1 ;
			}
			public function EstErreur()
			{
				return $this->StatutExecution != 1 ;
			}
			public function ConfirmeSucces($ctn='')
			{
				$this->StatutExecution = 1 ;
				if($ctn == '')
				{
					$ctn = $this->MessageSuccesExecution ;
				}
				$this->MessageExecution = $ctn ;
			}
			public function RenseigneErreur($ctn)
			{
				$this->StatutExecution = 0 ;
				$this->MessageExecution = $ctn ;
			}
			protected function ExecuteInstructions()
			{
			}
			public function DeploieContenuTs(& $classeTs)
			{
			}
			public function CorpsBrutMethodeTs()
			{
				return '' ;
			}
			public function AppelTsMtdDist($nom, $args=array(), $fonctSucces=null, $fonctErreur=null)
			{
				$serviceUtils = & $this->ZoneParent()->ServiceSrcUtils ;
				$ctn = '' ;
				$ctn = $serviceUtils->AppelTsMtdDist($nom, $args, $fonctSucces, $fonctErreur) ;
				return $ctn ;
			}
			public function FournitMethodesDistantes()
			{
			}
			public function & InsereMethodeDistante($nom, $mtd)
			{
				$mtd->CommandeParent = & $this ;
				return $this->ComposantIUParent->InsereMethodeDistante("Cmd_".$this->NomElementComposantIU."_".$nom, $mtd) ;
			}
		}
		class PvCommandeTsIonic extends PvCommandeBaseIonic
		{
			public $ContenuTs = '' ;
			public function CorpsBrutMethodeTs()
			{
				return $this->ContenuTs ;
			}
		}
		class PvCommandeAppelDistantIonic extends PvCommandeBaseIonic
		{
			public $NomMtdDist = "" ;
			public $ContenuTsDlgSucces = "" ;
			public $ContenuTsSucces = "" ;
			public $ContenuTsEchec = "" ;
			public $ContenuTsErreur = "" ;
			public $TitreDlgEchec = "Echec execution" ;
			public $TitreDlgSucces = "Succ&egrave;s" ;
			public $TitreDlgErreur = "Erreur survenue" ;
			public function CorpsBrutMethodeTs()
			{
				return 'let _self:any = this ;'."\n".$this->AppelTsMtdDist(
					$this->NomMtdDist,
					$this->ComposantIUParent->ArgsTsFiltresDistants(),
					'function(result:any) {
if(result.erreur.code === 0) {
'.(($this->ContenuTsSucces != '') ? $this->ContenuTsSucces. PHP_EOL : '_self.afficheMsg('.svc_json_encode($this->TitreDlgSucces).', result.valeur, function() {'.$this->ContenuTsDlgSucces.'}) ;').'} else {
'.(($this->ContenuTsEchec != '') ? $this->ContenuTsEchec : '_self.afficheMsg('.svc_json_encode($this->TitreDlgEchec).', result.erreur.message) ;').PHP_EOL
.'}'.PHP_EOL
.'}',
					'function (erreur:any) {
'.(($this->ContenuTsErreur != '') ? $this->ContenuTsErreur. PHP_EOL : '_self.afficheMsg('.svc_json_encode($this->TitreDlgErreur).', erreur.toString()) ;').'}'
				) ;
			}
		}
		
		class PvCritereBaseIonic extends PvCritereBase
		{
			public function & PageSrcParent()
			{
				return $this->FormulaireDonneesParent->PageSrcParent ;
			}
		}
		
		class PvTabBaseIonic
		{
			public function Titre()
			{
				return "" ;
			}
			public function Icone()
			{
				return "document" ;
			}
			public function Deploie(& $tabs, $nom)
			{
			}
		}
		class PvTabIndefIonic extends PvTabBaseIonic
		{
		}
		class PvTabPageSrcIonic extends PvTabBaseIonic
		{
			public $NomPageSrc ;
			public function & ObtientPageSrc(& $tabs, $nom)
			{
				$pageSrcParent = & $tabs->PageSrcParent ;
				$pageSrc = new PvPageSrcIndefIonic() ;
				if(! isset($pageSrcParent->ZoneParent->PagesSrc[$this->NomPageSrc]))
				{
					return $pageSrc ;
				}
				return $pageSrcParent->ZoneParent->PagesSrc[$this->NomPageSrc] ;
			}
			public function Deploie(& $tabs, $nom)
			{
				$pageSrcSelect = $this->ObtientPageSrc() ;
				$tagTab = $this->TagTabs->InsereTagFils(new PvTagIonTab()) ;
				$tagTab->DefinitAttrs(array("[root]" => "tabRacine".$nom, "tabTitle" => $pageSrcSelect->Titre, "tabIcon" => $tab->Icone)) ;
				$tabs->PageSrcParent->FichTs->InsereImportLocal(array($pageSrcSelect->NomClasse()), "../".$pageSrcSelect->CheminRelatif()) ;
				$tabs->PageSrcParent->ClasseTs->InsereMembre("tabRacine".$nom, $pageSrcSelect->NomClasse()) ;
			}
		}
		class PvTabPageSrcAccueilIonic extends PvTabBaseIonic
		{
			public function & ObtientPageSrc(& $tabs, $nom)
			{
				return $pageSrcParent->ZoneParent->PagesSrcAccueil ;
			}
		}
		
		class PvTagIonTabs extends PvTagBaseHtmlIonic
		{
			protected $_NomTag = "ion-tabs" ;
		}
		class PvTagIonTab extends PvTagBaseHtmlIonic
		{
			protected $_NomTag = "ion-tab" ;
		}
		
		class PvTabsIonic extends PvComposantIUNoyauIonic
		{
			public $Tabs = array() ;
			public function & InsereTab($nom, $tab)
			{
				$this->Tabs[$nom] = & $tab ;
				return $tab ;
			}
			public function & InsereTabPageSrc($nom)
			{
				$tab = new PvTabPageSrcIonic() ;
				$tab->NomPageSrc = $nom ;
				return $this->InsereTab($nom, $tab) ;
			}
			public function & InsereTabPageSrcAccueil()
			{
				$tab = new PvTabPageSrcAccueilIonic() ;
				return $this->InsereTab($nom, $tab) ;
			}
			public function Deploie()
			{
				$pageSrc = & $this->PageSrcParent ;
				$this->TagTabs = $pageSrc->FichHtml->DefinitTagRacine(new PvTagIonTabs()) ;
				$idx = 1 ;
				foreach($this->Tabs as $nom => $tab)
				{
					$tab->Deploie($this, $nom) ;
				}
			}
		}
		
	}
	
?>