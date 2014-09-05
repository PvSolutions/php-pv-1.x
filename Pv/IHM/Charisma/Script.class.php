<?php
	
	if(! defined('PV_SCRIPT_CHARISMA'))
	{
		define('PV_SCRIPT_CHARISMA', 1) ;
		
		class ScriptConnexionCharisma extends PvScriptConnexionWeb
		{
			public $Entete = 'Welcome to Charisma' ;
			public $InclureEntete = 1 ;
			public $NomClasseCSSPseudo = "icon-user" ;
			public $NomClasseCSSMotPasse = "icon-lock" ;
			public $InclureSeSouvenir = 0 ;
			public $LibelleSeSouvenir = "Remember" ;
			public $NomParamSeSouvenir = 'remember' ;
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= '<div class="container-fluid">'.PHP_EOL ;
				$ctn .= '<div class="row-fluid">'.PHP_EOL ;
				if($this->InclureEntete)
				{
					$ctn .= '<div class="row-fluid">
<div class="span12 center login-header">
<h2>'.$this->Entete.'</h2>
</div>
</div>'.PHP_EOL ;
				}
				$ctn .= '<div class="row-fluid">
<div class="well span5 center login-box">'.PHP_EOL ;
				if($this->TentativeConnexionEnCours && $this->TentativeConnexionValidee == 0)
				{
					$ctn .= '<div class="alert alert-info">'.$this->MessageConnexionEchouee.'</div>'.PHP_EOL ;
				}
				$ctn .= '<form class="form-horizontal" action="'.$this->ObtientUrl().'" method="post">'.PHP_EOL ;
				$ctn .= '<fieldset>'.PHP_EOL ;
				$ctn .= $this->RenduTableauParametres().PHP_EOL ;
				if($this->InclureSeSouvenir)
				{
					$ctn .= '<div class="input-prepend">
<label class="'.$this->NomParamSeSouvenir.'" for="'.$this->NomParamSeSouvenir.'"><input type="checkbox" id="'.$this->NomParamSeSouvenir.'" />'.$this->LibelleSeSouvenir.'</label>
</div>'.PHP_EOL ;
					$ctn .= '<div class="clearfix"></div>'.PHP_EOL ;
				}
				if($this->AfficherBoutonSoumettre)
				{
					$ctn .= '<p class="center span5"><button type="submit" class="btn btn-primary">'.$this->LibelleBoutonSoumettre.'</button></p>'.PHP_EOL ;
				}
				$ctn .= '</fieldset>'.PHP_EOL ;
				$ctn .= '</form>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				return $ctn ;
			}
			public function RenduTableauParametres()
			{
				$ctn = '' ;
				$ctn .= '<div class="input-prepend" title="'.$this->LibellePseudo.'" data-rel="tooltip">
<span class="add-on"><i class="'.$this->NomClasseCSSPseudo.'"></i></span><input autofocus class="input-large span10" name="'.$this->NomParamPseudo.'" id="'.$this->NomParamPseudo.'" type="text" value="'.htmlentities($this->ValeurParamPseudo).'" />
</div>'.PHP_EOL ;
				$ctn .= '<div class="clearfix"></div>'.PHP_EOL ;
				$ctn .= '<div class="input-prepend" title="'.$this->LibellePseudo.'" data-rel="tooltip">
<span class="add-on"><i class="'.$this->NomClasseCSSMotPasse.'"></i></span><input autofocus class="input-large span10" name="'.$this->NomParamMotPasse.'" id="'.$this->NomParamMotPasse.'" type="password" value="'.htmlentities($this->ValeurParamMotPasse).'" />
</div>'.PHP_EOL ;
				$ctn .= '<input type="hidden" name="'.$this->NomParamSoumetTentative.'" value="'.htmlentities($this->ValeurParamSoumetTentative).'" />' ;
				$ctn .= '<div class="clearfix"></div>'.PHP_EOL ;
				return $ctn ;
			}
		}
	}
	
?>