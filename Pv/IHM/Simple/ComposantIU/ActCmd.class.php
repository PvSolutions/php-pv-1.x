<?php
	
	if(! defined('PV_COMPOSANT_SIMPLE_IU_ACT_CMD'))
	{
		if(! defined('PV_COMPOSANT_SIMPLE_IU_BASE'))
		{
			include dirname(__FILE__)."/Base.class.php" ;
		}
		if(! defined('COMMON_GD_CONTROLS_INCLUDED'))
		{
			include dirname(__FILE__)."/../../../../Common/GD.class.php" ;
		}
		define('PV_COMPOSANT_SIMPLE_IU_ACT_CMD', 1) ;
		
		class PvActCmdBase extends PvElementCommandeBase
		{
			public $TypeElementCommande = "action" ;
			public $MessageErreur = "" ;
			public function Execute()
			{
			}
		}
		
		class PvActCmdRedirectionHttp extends PvActCmdBase
		{
			public $Url = "" ;
			public $NomScript = "" ;
			public $Parametres = array() ;
			protected function DetermineUrl()
			{
				if($this->NomScript != "" && ! $this->EstNul($this->ZoneParent) && isset($this->ZoneParent->Scripts[$this->NomScript]))
				{
					$this->Url = $this->ZoneParent->Scripts[$this->NomScript]->ObtientUrl() ;
				}
				if($this->Url != '' && count($this->Parametres) > 0)
				{
					$this->Url = update_url_params($this->Url, $this->Parametres) ;
				}
			}
			public function Execute()
			{
				$this->DetermineUrl() ;
				if($this->Url != "")
				{
					redirect_to($this->Url) ;
				}
				else
				{
					$this->MessageErreur = "URL vide trouvée" ;
				}
			}
		}
		class PvActCmdEditionElementDonnees extends PvActCmdBase
		{
			public $TableEdition = '' ;
			public $ModeEdition = 0 ;
			public function Execute()
			{
				if($this->TableEdition == "")
				{
					$this->TableEdition = $this->FormulaireDonneesParent->FournisseurDonnees->TableEdition ;
				}
				if($this->TableEdition == "" || $this->FormulaireDonneesParent->EstNul($this->FormulaireDonneesParent->FournisseurDonnees))
				{
					return ;
				}
				$ancTableEdition = $this->FormulaireDonneesParent->FournisseurDonnees->TableEdition ;
				$this->FormulaireDonneesParent->FournisseurDonnees->TableEdition = $this->TableEdition ;
				$succes = 0 ;
				switch($this->ModeEdition)
				{
					case PvModeEditionElement::Ajout :
					{
						if(count($this->FiltresCibles) > 0)
						{
							$succes = $this->FormulaireDonneesParent->FournisseurDonnees->AjoutElement($this->FiltresCibles) ;
						}
					}
					break ;
					case PvModeEditionElement::Modif :
					{
						if(count($this->FiltresCibles) > 0)
						{
							$succes = $this->FormulaireDonneesParent->FournisseurDonnees->ModifElement($this->FormulaireDonneesParent->ObtientFiltresSelection(), $this->FiltresCibles) ;
						}
					}
					break ;
					case PvModeEditionElement::Suppr :
					{
						$succes = $this->FormulaireDonneesParent->FournisseurDonnees->SupprElement($this->FormulaireDonneesParent->ObtientFiltresSelection()) ;
					}
					break ;
					default :
					{
						$this->FormulaireDonneesParent->RenseigneErreur("Le mode d'édition de la commande est inconnue") ;
					}
					break ;
				}
				if(! $succes && $this->FormulaireDonneesParent->FournisseurDonnees->BaseDonnees->ConnectionException != "")
				{
					$this->FormulaireDonneesParent->AfficheExceptionFournisseurDonnees() ;
				}
				$this->FormulaireDonneesParent->FournisseurDonnees->TableEdition = $ancTableEdition ;
			}
		}
		class PvActCmdAjoutElementDonnees extends PvActCmdEditionElementDonnees
		{
			public $ModeEdition = 1 ;
		}
		class PvActCmdModifElementDonnees extends PvActCmdEditionElementDonnees
		{
			public $ModeEdition = 2 ;
		}
		class PvActCmdSupprElementDonnees extends PvActCmdEditionElementDonnees
		{
			public $ModeEdition = 3 ;
		}
	
		class PvActCmdEnvoiMail extends PvActCmdBase
		{
			public $TypeMail = "html" ;
			public $De = "" ;
			public $A = "" ;
			public $Cc = "" ;
			public $Cci = "" ;
			public $FiltreA = null ;
			public $FiltreDe = null ;
			public $FiltreCc = null ;
			public $FiltreCci = null ;
			public $FormatSujetMessage = "" ;
			public $SujetMessage = "" ;
			public $FormatContenuMessage = "" ;
			public $ContenuMessage = "" ;
			public $PiecesJointes = array() ;
			protected function ConstruitContenuMessage()
			{
				$valeurFiltres = $this->FormulaireDonneesParent->ExtraitValeursFiltres($this->FiltresCibles) ;
				$this->SujetMessage = _parse_pattern($this->FormatSujetMessage, $valeursFiltres) ;
				$this->ContenuMessage = _parse_pattern($this->FormatContenuMessage, $valeursFiltres) ;
			}
			public function Execute()
			{
				if($this->FiltreDe != null)
				{
					$this->De = $this->FiltreDe->Lie() ;
				}
				if($this->FiltreA != null)
				{
					$this->A = $this->FiltreA->Lie() ;
				}
				if($this->FiltreCc != null)
				{
					$this->Cc = $this->FiltreCc->Lie() ;
				}
				if($this->FiltreCci != null)
				{
					$this->Cci = $this->FiltreCci->Lie() ;
				}
				$this->ConstruitMessage() ;
				if($this->TypeMail == 'text')
				{
					send_plain_mail($this->A, $this->SujetMessage, $this->ContenuMessage, $this->De, $this->Cc, $this->Cci) ;
				}
				else
				{
					send_mail_with_attachments($this->A, $this->SujetMessage, $this->ContenuMessage, $this->PiecesJointes, $this->De, $this->Cc, $this->Cci) ;
				}
			}
		}
		class PvActCmdFormMail extends PvActCmdEnvoiMail
		{
			protected function ConstruitContenuMessage()
			{
				$form = & $this->FormulaireDonneesParent ;
				$valeurFiltres = $form->ExtraitValeursFiltres($this->FiltresCibles) ;
				$this->SujetMessage = _parse_pattern($this->FormatSujetMessage, $valeursFiltres) ;
				$this->ContenuMessage = $form->DessinateurFiltresEdition->VersionTexte($form, $form->FiltresEdition) ;
			}
		}
		
		class PvActCmdTailleImageGd extends PvActCmdBase
		{
			public $DieSiNonDispo = 0 ;
			public $TaillesFiltre = array() ;
			protected $RessourceSupport = false ;
			public function CreeTailleFlt()
			{
				return new PvTailleFiltreImageGd() ;
			}
			public function & InsereTailleFlt($nomFiltre, $largeurMax=0, $hauteurMax=0, $operation="")
			{
				$tailleFlt = $this->CreeTailleFlt() ;
				$tailleFlt->NomFiltre = $nomFiltre ;
				$tailleFlt->LargeurMax = $largeurMax ;
				$tailleFlt->HauteurMax = $hauteurMax ;
				$tailleFlt->Operation = $operation ;
				return $this->InscritTailleFiltre($tailleFlt) ;
			}
			public function & InscritTailleFiltre(& $tailleFlt)
			{
				$this->TaillesFiltre[$tailleFlt->NomFiltre] = $tailleFlt ;
				return $tailleFlt ;
			}
			public function & InsereTailleFiltre($nomFiltre, $largeurMax=0, $hauteurMax=0, $operation="")
			{
				return $this->InsereTailleFlt($nomFiltre, $largeurMax, $hauteurMax, $operation) ;
			}
			public function Execute()
			{
				if(! function_exists("imagecreate"))
				{
					if($this->DieSiNonDispo == 1)
					{
						die("<p>La librairie GD n'est pas install&eacute;e, vous ne pouvez pas utiliser la classe ".get_class($this)."</p>") ;
					}
				}
				$nomFiltres = array_keys($this->FormulaireDonneesParent->FiltresEdition) ;
				// print_r($nomFiltres) ;
				$args = array_keys($this->TaillesFiltre) ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = & $this->FormulaireDonneesParent->FiltresEdition[$nomFiltre] ;
					if(in_array($filtre->NomElementScript, $args) || in_array($nomFiltre, $args, true))
					{
						if(isset($this->TaillesFiltre[$filtre->NomElementScript]))
						{
							$this->AnalyseTailleFiltre($this->TaillesFiltre[$filtre->NomElementScript], $this->FormulaireDonneesParent->FiltresEdition[$nomFiltre]) ;
						}
						else
						{
							$this->AnalyseTailleFiltre($this->TaillesFiltre[$nomFiltre], $this->FormulaireDonneesParent->FiltresEdition[$nomFiltre]) ;
						}
					}
				}
			}
			protected function AnalyseTailleFiltre(& $tailleFlt, & $filtre)
			{
				$cheminImage = $filtre->Lie() ;
				if($cheminImage == '' || (! file_exists($cheminImage) || is_dir($cheminImage)))
				{
					// $GLOBALS['CommonGDManipulator']->CopyFile($tailleFlt->CheminEchec, $cheminImage)
					return false ;
				}
				if(filesize($cheminImage) > 1024 * 1024 * 1024)
				{
					return false ;
				}
				$pathInfo = pathinfo($cheminImage) ;
				$cheminTemp = $pathInfo["dirname"]. DIRECTORY_SEPARATOR ."~".$pathInfo["basename"] ;
				$GLOBALS['CommonGDManipulator']->CopyAdjustedFile($cheminImage, $cheminTemp, $tailleFlt->LargeurMax, $tailleFlt->HauteurMax) ;
				if(file_exists($cheminTemp))
				{
					rename($cheminTemp, $cheminImage) ;
				}
			}
		}
		class PvTailleFiltreImageGd
		{
			public $NomFiltre ;
			public $LargeurMax ;
			public $HauteurMax ;
			public $Operation = "" ;
			public $Obligatoire = 0 ;
			public $CheminEchec = "images/non_trouve.png" ;
		}
		
	}
	
?>