<?php
	
	if(! defined('PV_NOYAU_SCRIPT_BOOTSTRAP'))
	{
		define('PV_NOYAU_SCRIPT_BOOTSTRAP', 1) ;
		
		class PvScriptBaseBoostrap extends PvScriptWebSimple
		{
		}
		
		class PvScriptConnexionBootstrap extends PvScriptConnexionWeb
		{
			public $UtiliserCorpsDocZone = 0 ;
			public function RenduTitre()
			{
				return '' ;
			}
			public function RenduTableauParametres()
			{
				$ctn = '' ;
				$ctn .= '<input type="text" class="form-control" placeholder="'.htmlentities($this->LibellePseudo).'" required autofocus name="'.$this->NomParamPseudo.'" id="'.$this->NomParamPseudo.'" value="'.htmlentities($this->ValeurParamPseudo).'">
				<input class="form-control" required type="password" name="'.$this->NomParamMotPasse.'" placeholder="'.htmlentities($this->LibelleMotPasse).'" id="'.$this->NomParamMotPasse.'" value="" />
		<input type="hidden" name="'.$this->NomParamSoumetTentative.'" value="'.htmlentities($this->ValeurParamSoumetTentative).'" />' ;
				return $ctn ;
			}
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->ZoneParent->InscritContenuCSS('body {
  padding-top: 40px;
  padding-bottom: 40px;
  background-color: #eee;
}

.form-signin {
  max-width: 330px;
  padding: 15px;
  margin: 0 auto;
}
.form-signin .form-signin-heading,
.form-signin .checkbox {
  margin-bottom: 10px;
}
.form-signin .checkbox {
  font-weight: normal;
}
.form-signin .form-control {
  position: relative;
  height: auto;
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
  padding: 10px;
  font-size: 16px;
}
.form-signin .form-control:focus {
  z-index: 2;
}
.form-signin input[type="email"] {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}
.form-signin input[type="password"] {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}
') ;
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= '<div class="container">'.PHP_EOL ;
				$ctn .= '<form class="form-signin" action="'.$this->ObtientUrl().'" method="post">'.PHP_EOL ;
				$ctn .= '<h2 class="form-signin-heading">'.$this->Titre.'</h2>'.PHP_EOL ;
				if($this->TentativeConnexionEnCours && $this->TentativeConnexionValidee == 0)
				{
					$ctn .= '<div class="erreur alert alert-danger" role="alert">'.$this->MessageConnexionEchouee.'</div>'.PHP_EOL ;
				}
				$ctn .= $this->RenduTableauParametres().PHP_EOL ;
				if($this->AfficherBoutonSoumettre)
				{
					$ctn .= '<p align="center"><button type="submit" class="btn btn-primary">'.$this->LibelleBoutonSoumettre.'</button></p>'.PHP_EOL ;
				}
				$ctn .= '</form>' ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		class PvScriptDeconnexionBootstrap extends PvScriptDeconnexionWeb
		{
		}
	}
	
?>