<?php
	
	if(! defined('PV_CATEG_MDL_DOCUMENT'))
	{
		define('PV_CATEG_MDL_DOCUMENT', 1) ;
		
		class PvCategMdlDocumentEditDocs extends PvObjet
		{
			public $MdlsDocument = array() ;
			public $Titre = "" ;
			public $Description = "" ;
			public $CheminIcone = "" ;
			public $CheminMiniature = "" ;
			public function AdopteZone($nom, & $zone)
			{
				$this->NomElementZone = $nom ;
				$this->ZoneParent = & $zone ;
			}
			public function ObtientTitre()
			{
				return $this->Titre ;
			}
			public function & InsereMdlDocument($nom, $mdlDoc)
			{
				$this->InscritMdlDocument($nom, $mdlDoc) ;
				return $mdlDoc ;
			}
			public function InscritMdlDocument($nom, & $mdlDoc)
			{
				$this->MdlsDocument[$nom] = & $mdlDoc ;
				$mdlDoc->AdopteCateg($nom, $this) ;
			}
		}
	}
	
?>