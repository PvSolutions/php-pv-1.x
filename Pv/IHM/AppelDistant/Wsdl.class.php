<?php
	
	if(! defined('PV_WSDL_APPEL_DISTANT'))
	{
		define('PV_WSDL_APPEL_DISTANT', 1) ;
		
		class PvWsdlAppelDistant
		{
			public $ZoneParent ;
			public $VersionXml = "1.0" ;
			public $AttrsDefinition = array() ;
			public $ContenusType = array() ;
			public $ContenusMessage = array() ;
			public $ContenusPortType = array() ;
			public $ContenusBinding = array() ;
			public $ContenusService = array() ;
			public function Rendu()
			{
				$ctn = '' ;
				$ctn .= '<?xml version="'.$this->VersionXml.'"?>'.PHP_EOL ;
				$ctn .= '<wsdl:definitions name="'.$this->ZoneParent->NomService.'"' ;
				foreach($this->AttrsDefinition as $n => $v)
				{
					$ctn .= ' '.$n.'="'.htmlspecialchars($v).'"' ;
				}
				$ctn .= '>'.PHP_EOL ;
				if(count($this->ContenusType) > 0)
				{
					$ctn .= '<wsdl:types>'.PHP_EOL ;
					$ctn .= '<s:schema elementFormDefault="qualified" targetNamespace="http://tempuri.org/">'.PHP_EOL ;
					if($this->ZoneParent->InclureFaultWsdl == 1)
					{
						$ctn .= '<s:complexType name="'.$this->ZoneParent->NomService.'Fault">
<s:sequence>
<s:element minOccurs="1" maxOccurs="1" name="errorCode" type="s:int" />
<s:element minOccurs="0" maxOccurs="1" name="errorMessage" type="s:string" />
</s:sequence>
</s:complexType>
<s:element name="'.$this->ZoneParent->NomService.'Fault" type="tns:'.$this->ZoneParent->NomService.'Fault"></s:element>'.PHP_EOL ;
					}
					foreach($this->ContenusType as $i => $contenuType)
					{
						$ctn .= $contenuType. PHP_EOL ;
					}
					$ctn .= '</s:schema>'.PHP_EOL ;
					$ctn .= '</wsdl:types>'.PHP_EOL ;
				}
				if(count($this->ContenusMessage) > 0)
				{
					foreach($this->ContenusMessage as $nom => $contenuMsg)
					{
						$ctn .= '<wsdl:message name="'.$nom.'">'.PHP_EOL ;
						$ctn .= $contenuMsg. PHP_EOL ;
						$ctn .= '</wsdl:message>'.PHP_EOL ;
					}
					if($this->ZoneParent->InclureFaultWsdl == 1)
					{
						$ctn .= '<wsdl:message name="'.$this->ZoneParent->NomService.'Fault">
<wsdl:part name="parameters" element="tns:'.$this->ZoneParent->NomService.'Fault"/>
</wsdl:message>'.PHP_EOL ;
					}
				}
				if(count($this->ContenusPortType) > 0)
				{
					$ctn .= '<wsdl:portType name="'.$this->ZoneParent->NomService.'Soap">'.PHP_EOL ;
					foreach($this->ContenusPortType as $i => $contenuPortType)
					{
						$ctn .= $contenuPortType. PHP_EOL ;
					}
					$ctn .= '</wsdl:portType>'.PHP_EOL ;
				}
				if(count($this->ContenusBinding) > 0)
				{
					foreach($this->ContenusBinding as $nom => $contenuBinding)
					{
						$ctn .= '<wsdl:binding name="'.$nom.'" type="tns:'.$this->ZoneParent->NomService.'Soap">'.PHP_EOL ;
						$ctn .= $contenuBinding ;
						$ctn .= '</wsdl:binding>'.PHP_EOL ;
					}
				}
				if(count($this->ContenusService) > 0)
				{
					$ctn .= '<wsdl:service name="'.$this->ZoneParent->NomService.'">'.PHP_EOL ;
					foreach($this->ContenusService as $i => $contenuService)
					{
						$ctn .= $contenuService. PHP_EOL ;
					}
					$ctn .= '</wsdl:service>'.PHP_EOL ;
				}
				$ctn .= '</wsdl:definitions>' ;
				return $ctn ;
			}
			protected function PrepareRendu()
			{
				$this->AttrsDefinition["xmlns:tm"] = "http://microsoft.com/wsdl/mime/textMatching/" ;
				$this->AttrsDefinition["xmlns:soapenc"] = "http://schemas.xmlsoap.org/soap/encoding/" ;
				$this->AttrsDefinition["xmlns:mime"] = "http://schemas.xmlsoap.org/wsdl/mime/" ;
				$this->AttrsDefinition["xmlns:tns"] = "http://tempuri.org/" ;
				$this->AttrsDefinition["xmlns:soap"] = "http://schemas.xmlsoap.org/wsdl/soap/" ;
				$this->AttrsDefinition["xmlns:http"] = "http://schemas.xmlsoap.org/wsdl/http/" ;
				$this->AttrsDefinition["xmlns:s"] = "http://www.w3.org/2001/XMLSchema" ;
				$this->AttrsDefinition["targetNamespace"] = "http://tempuri.org/" ;
				$this->AttrsDefinition["xmlns:wsdl"] = 'http://schemas.xmlsoap.org/wsdl/' ;
				// print count($this->ZoneParent->MethodesDistantes) ;
				foreach($this->ZoneParent->MethodesDistantes as $nom => $mtdDist)
				{
					$mtdDist->RemplitWsdl($this) ;
				}
				foreach($this->ZoneParent->Protocoles as $nom => $protoc)
				{
					$protoc->RemplitWsdl($this) ;
				}
			}
			public function Affiche()
			{
				$this->PrepareRendu() ;
				Header("Content-Type:text/xml") ;
				$ctnRendu = $this->Rendu() ;
				echo $ctnRendu ;
			}
		}
		
	}
	
?>