<?php
	
	if(! defined('PV_FORMULAIRE_DONNEES_SB_ADMIN2'))
	{
		define('PV_FORMULAIRE_DONNEES_SB_ADMIN2', 1) ;
		
		class PvFormulaireDonneesSbAdmin2 extends PvFormulaireDonneesHtml
		{
			protected function InitDessinateurFiltresEdition()
			{
				$this->DessinateurFiltresEdition = new PvDessinFiltresSbAdmin2() ;
				$this->DessinateurFiltresEdition->MaxFiltresParLigne = $this->MaxFiltresEditionParLigne ;
			}
			protected function InitDessinateurBlocCommandes()
			{
				$this->DessinateurBlocCommandes = new PvDessinCmdsSbAdmin2() ;
			}
			protected function OuvreBoiteDlgUrlCmdExec($titre, $url, $largeur=null, $hauteur=null)
			{
				$this->RemplaceCommandeExecuter("PvCmdOuvreBoiteDlgUrl") ;
				$cmdExec = & $this->CommandeExecuter ;
				$cmdExec->TitreDlg = $titre ;
				$cmdExec->UrlDlg = $url ;
				$cmdExec->LargeurDlg = $largeur ;
				$cmdExec->HauteurDlg = $hauteur ;
			}
		}
	}

?>