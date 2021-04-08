<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL; 
use Bitrix\Main\Entity;

class lastMagazine extends CBitrixComponent
{ 
    private $IBLOCK_ID = 39;
    private $IDHL = 24;
 
    /**
     * getMagazine - метод получает свойство последнего элемента в инфоблоке DOC
     * по этому свойству получаем путь до файла
     * если свойство пустое запрашиваем в HL 
     *
     * @return void
     */
    public function getMagazine()
    {
        try {
            Loader::includeModule('iblock');
        } catch (\Bitrix\Main\LoaderException $e) {
            throw new Exception('Не удалось загрузить модуль iblock');
        }

        $arItems = [];

        $arNavParams = [
            'nPageSize' => 1,
        ];

        $arSort     = Array("ID"=>"DESC");
        $arSelect   = Array("NAME", "PREVIEW_TEXT");
        $arFilter   = Array("IBLOCK_ID" => '39', "ELEMENT_ID"=>"", "ACTIVE"=>"Y");

        $res = CIBlockElement::GetList($arSort, $arFilter, false, Array("nPageSize"=>1), $arSelect);            
        $Element = $res->Fetch();
        
        // print_r($Element);

        $arPropertiElement = array();
        $db_props = CIBlockElement::GetProperty(
            $this->IBLOCK_ID, 
            $Element['ID'],
            "desc", 
            array("CODE"=> "DOC")
        );
        $arPropertiElement = $db_props->Fetch();
        $PathFileDoc = CFile::GetPath($arPropertiElement["VALUE"]);
        
        if( empty($PathFileDoc) ){
            $IDNamberMagazine= $this->getNamberMagazine( $Element['ID'] );
            $PathFileDoc = '/rubrics/edition-'.$IDNamberMagazine.'/';
        }

        return $PathFileDoc;
    }
    
    /**
     * getNamberMagazine - метод получает запись номера журнала
     *
     * @param  mixed $ID
     * @return void
     */
    private function getNamberMagazine($ID){

        $hlblock = HL\HighloadBlockTable::getById($this->IDHL)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock); 
        $entity_data_class = $entity->getDataClass();
        
        $rsData = $entity_data_class::getList(array(
            "select" => array("*"),
            "order" => array("ID" => "ASC"),
            "filter" => array("UF_ARCHIV" => $ID)  // Задаем параметры фильтра выборки
         ));
        
        $arData = $rsData->Fetch();
        //  print_r($arData);
        return $arData['UF_XML_ID'];
        
    }

    public function executeComponent()
    { 
        $this->arResult["Magazine"] = $this->getMagazine();
        $this->includeComponentTemplate();        
        return $this->arResult;
    }

}