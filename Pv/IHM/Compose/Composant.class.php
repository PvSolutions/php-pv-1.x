<?php
	
	if(! defined('PV_COMPOSANT_COMPOSE_IHM'))
	{
		if(! defined('PV_NOYAU_SIMPLE_IHM'))
		{
			include dirname(__FILE__)."/../Simple/Script.class.php" ;
		}
		define('PV_COMPOSANT_COMPOSE_IHM', 1) ;
		
		class PvEnteteDocumentHtmlBase extends PvComposantIUBase
		{
			public $AttrsElemBody = '' ;
			public $TagDoctype = '<!doctype html>' ;
			public $AutresElemsHead = '' ;
			protected function RenduDispositifBrut()
			{
				$this->ZoneParent->InclutLibrairiesExternes() ;
				$ctn = '' ;
				$ctn .= $this->TagDoctype.PHP_EOL ;
				$ctn .= '<html lang="'.$this->ZoneParent->LangueDocument.'">'.PHP_EOL ;
				$ctn .= '<head>'.PHP_EOL ;
				if($this->ZoneParent->EncodageDocument != '')
					$ctn .= '<meta charset="'.$this->ZoneParent->EncodageDocument.'" />'.PHP_EOL ;
				$ctn .= '<title>'.htmlentities(($this->ZoneParent->ScriptPourRendu->TitreDocument != "") ? $this->ZoneParent->ScriptPourRendu->TitreDocument : $this->ZoneParent->TitreDocument).'</title>'.PHP_EOL ;
				$ctn .= '<meta name="keywords" value="'.htmlentities(($this->ZoneParent->ScriptPourRendu->MotsCleMeta != "") ? $this->ZoneParent->ScriptPourRendu->MotsCleMeta : $this->ZoneParent->MotsCleMeta).'" />'.PHP_EOL ;
				$ctn .= '<meta name="description" value="'.htmlentities(($this->ZoneParent->ScriptPourRendu->DescriptionMeta != "") ? $this->ZoneParent->ScriptPourRendu->DescriptionMeta : $this->ZoneParent->DescriptionMeta).'" />'.PHP_EOL ;
				for($i=0; $i<count($this->ZoneParent->ContenusCSS); $i++)
				{
					$ctnCSS = $this->ZoneParent->ContenusCSS[$i] ;
					$ctn .= $ctnCSS->RenduDispositif().PHP_EOL ;
				}
				for($i=0; $i<count($this->ZoneParent->ContenusJs); $i++)
				{
					$ctnJs = $this->ZoneParent->ContenusJs[$i] ;
					$ctn .= $ctnJs->RenduDispositif().PHP_EOL ;
				}
				if($this->AutresElemsHead != '')
				{
					$ctn .= $this->AutresElemsHead .PHP_EOL ;
				}
				$ctn .= '</head>'.PHP_EOL ;
				$ctn .= '<body' ;
				if($this->AttrsElemBody != '')
					$ctn .= ' '.$this->AttrsElemBody ;
				$ctn .= '>'.PHP_EOL ;
				return $ctn ;
			}
		}
		class PvPiedDocumentHtmlBase extends PvComposantIUBase
		{
			protected function RenduDispositifBrut()
			{
				return '</body>
</html>' ;
			}
		}
		
		class PvEnteteDocumentHtml5 extends PvEnteteDocumentHtmlBase
		{
		}
		class PvPiedDocumentHtml5 extends PvPiedDocumentHtmlBase
		{
		}
		
		class PvDefCellGrilleComposants
		{
			public $Align ;
			public $AlignV ;
			public $Largeur ;
			public $Hauteur ;
			public $TotalLignesFusion = 1 ;
			public $TotalColonnesFusion = 1 ;
		}
		class PvGrilleComposants extends PvComposantIUBase
		{
			public $MaxLignes = 0 ;
			public $MaxColonnes = 0 ;
			public $EspacementCell = "" ;
			public $MargesCell = "" ;
			public $Cellules = array() ;
			public $Largeur = "100%" ;
			public $Hauteur = "" ;
			public $LargeurBordure = "0" ;
			public $CouleurBordure = "black" ;
			public $MsgAucuneCellule = "" ;
			public $AlignCellule = "" ;
			public $AlignVCellule = "" ;
			public $LargeurCellule = "" ;
			public $HauteurCellule = "" ;
			public function InscritCellule(& $comp)
			{
				$nomCellule = "cellule_".count($this->Cellules).'_'.$this->IDInstanceCalc ;
				$comp->AdopteScript($nomCellule, $this->ScriptParent) ;
				$comp->ChargeConfig() ;
				$this->Cellules[] = & $comp ;
			}
			public function InscritNouvCellule($comp)
			{
				$this->InscritCellule($comp) ;
			}
			public function InscritCellules($comps = array())
			{
				foreach($comps as $i => & $comp)
				{
					$this->InscritCellule($comp) ;
				}
			}
			protected function RenduEnteteTabl()
			{
				$ctn = '' ;
				$ctn .= '<table' ;
				if($this->Largeur != "")
				{
					$ctn .= ' width="'.$this->Largeur.'"' ;
				}
				if($this->Hauteur != "")
				{
					$ctn .= ' height="'.$this->Hauteur.'"' ;
				}
				if($this->EspacementCell != "")
				{
					$ctn .= ' cellpadding="'.$this->EspacementCell.'"' ;
				}
				if($this->MargesCell != "")
				{
					$ctn .= ' cellspacing="'.$this->MargesCell.'"' ;
				}
				if($this->LargeurBordure != "")
				{
					$ctn .= ' border="'.$this->LargeurBordure.'"' ;
					if($this->CouleurBordure != "")
					{
						$ctn .= ' bordercolor="'.$this->CouleurBordure.'"' ;
					}
				}
				$ctn .= '>' ;
				return $ctn ;
			}
			protected function RenduPiedTabl()
			{
				$ctn = '' ;
				$ctn .= '</table>' ;
				return $ctn ;
			}
			protected function RenduCellule($i, &$comp)
			{
				$ctn = '' ;
				$ctn .= $comp->RenduDispositif() ;
				return $ctn ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				if(count($this->Cellules) == 0)
				{
					$ctn .= $this->MsgAucuneCellule ;
					return $ctn ;
				}
				$ctn .= $this->RenduEnteteTabl().PHP_EOL ;
				$pourcentCell = intval(100 / $this->MaxColonnes) ;
				foreach($this->Cellules as $i => & $comp)
				{
					if($this->MaxColonnes <= 1 || $i % $this->MaxColonnes == 0)
					{
						if($i != 0)
						{
							$ctn .= '</tr>'.PHP_EOL ;
						}
						$ctn .= '<tr>'.PHP_EOL ;
					}
					$ctn .= '<td width="'.$pourcentCell.'%"' ;
					if($this->LargeurCellule != '')
						$ctn .= ' width="'.$this->LargeurCellule.'"' ;
					if($this->HauteurCellule != '')
						$ctn .= ' height="'.$this->HauteurCellule.'"' ;
					if($this->AlignCellule != '')
						$ctn .= ' align="'.$this->AlignCellule.'"' ;
					if($this->AlignVCellule != '')
						$ctn .= ' valign="'.$this->AlignVCellule.'"' ;
					$ctn .= '>'.PHP_EOL ;
					$ctn .= $this->RenduCellule($i, $comp).PHP_EOL ;
					$ctn .= '</td>'.PHP_EOL ;
				}
				if(($this->MaxColonnes <= 1 || count($this->Cellules) % $this->MaxColonnes != 0))
				{
					if($this->MaxColonnes <= 1)
					{
						$ctn .= '</tr>'.PHP_EOL ;
					}
					else
					{
						$colFusionnees = $this->MaxColonnes - (count($this->Cellules) % $this->MaxColonnes) ;
						$ctn .= '<td colspan="'.$colFusionnees.'"></td>'.PHP_EOL ;
						$ctn .= '</tr>'.PHP_EOL ;
					}
				}
				$ctn .= $this->RenduPiedTabl() ;
				return $ctn ;
			}
		}
		
		class PvComposantsScriptPourRendu extends PvComposantIUBase
		{
			protected function RenduDispositifBrut()
			{
				$script = & $this->ZoneParent->ScriptPourRendu ;
				if($this->EstNul($script))
				{
					return "" ;
				}
				return $script->RenduDispositif() ;
			}
		}
	}
	
?>