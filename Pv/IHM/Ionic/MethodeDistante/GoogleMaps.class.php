<?php
	
	if(! defined('PV_METHODE_DISTANTE_GOOGLEMAPS_IONIC'))
	{
		define('PV_METHODE_DISTANTE_GOOGLEMAPS_IONIC', 1) ;
		
		class PvValeurQuartierIonic
		{
			public $quartier ;
			public $ville ;
			public $pays ;
			public $contenu ;
		}
		
		class PvMtdDistQuartierParCoordsIonic extends PvMethodeDistanteNoyauIonic
		{
			protected function ExecuteInstructions()
			{
				$sess = new HttpSession() ;
				$lat = $this->ParamArg("latitude") ;
				$long = $this->ParamArg("longitude") ;
				$resultat = $sess->GetPage("http://maps.googleapis.com/maps/api/geocode/json?latlng=".urlencode($lat).",".urlencode($long)."&sensor=false") ;
				if($resultat != '')
				{
					$val = svc_json_decode($resultat) ;
					$res = new PvValeurQuartierIonic() ;
					$this->ConfirmeSucces($res) ;
				}
				else
				{
					if($sess->RequestException != "")
					{
						$this->RenseigneErreur(1, $sess->RequestException) ;
					}
					else
					{
						$this->RenseigneErreur(1, "Contenu vide renvoy&eacute; par GoogleMaps") ;
					}
				}
			}
		}
		
	}
	
?>