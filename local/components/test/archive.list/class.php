<?php 
use Bitrix\Main\Loader;
Loader::includeModule("highloadblock");     
use Bitrix\Highloadblock as HL; 
use Bitrix\Main\Entity;
/* 
 Комопнент фильтрует и выводит новости на главной
*/
class ArchiveList extends CBitrixComponent
{   
    private $countInPage = 5;
    private $HL_ID;

    public function onPrepareComponentParams($arParams)
    {
        $result = array(
            "CACHE_TYPE"  => $arParams["CACHE_TYPE"],
            "CACHE_TIME"  => isset($arParams["CACHE_TIME"]) ?$arParams["CACHE_TIME"]: 36000000,            
            "IBLOCK_ID"   => intval($arParams["IBLOCK_ID"]),
            "SECTION_ID"  => intval($arParams["SECTION_ID"]),
            "ELEMENT_ID"  => intval($arParams["ELEMENT_ID"]),
            "FILTR_NEWS"  => $arParams["FILTR_NEWS"],
            "PAGE_NUMBER" => $arParams["PAGE_NUMBER"],
            "COUNT_ELEMENT_PAGE" => $arParams["COUNT_ELEMENT_PAGE"]
        );
        return $result;
    }
    
    /**
     * getItemArchive - метод получает записи  архивов журналов
     *
     * @return void
     */
    private function getItemArchive( $select=array(), $order=array(), $filter=array(), $CODE_HL='Classifaer', $limit=array()){
        
        $result = \Bitrix\Highloadblock\HighloadBlockTable::getList(array(
            'filter' => array(
                '=NAME'=>$CODE_HL)
            )
        );

        if($row = $result->fetch())
        {
            $this->HL_ID = $row["ID"];
        }
        $hlblock = HL\HighloadBlockTable::getById($this->HL_ID)->fetch(); 
        
        $entity = HL\HighloadBlockTable::compileEntity($hlblock); 
        $entity_data_class = $entity->getDataClass(); 
        
        $rsData = $entity_data_class::getList(array(
            "select"  => $select,
            "order"   => $order,
            "filter"  => $filter
        ));
        
        while($arData = $rsData->Fetch()){
            $arr = $arData;
            $arr["COVER_URL"]   = CFile::GetPath($arData["UF_COVER_MAGAZINE"]);
            $arr["PDF_URL"]     = CFile::GetPath($arData["UF_PDF_FILE"]);
            $index = (int)$arData["UF_XML_ID"];
            $arItem[$index] = $arr;
        }
        asort($arItem);
        unset($arr, $index);

        return $arItem;
    }
    
    /**
     * GetDateParams - метод получает массив по годам и номерам
     *
     * @return void
     */
    private function GetDateParams($arItems){        
        // массив данных
        $arrYear = array();
        $Year = 0;
        foreach($arItems as $item){
            //echo "<br>".$item["UF_YEAR"]."-".$item["UF_XML_ID"];
            $arrYear[$item["UF_YEAR"]][] = $item["UF_XML_ID"] ;
        }
        return $arrYear;
    }

    /**
     * getParamsPage - метод получает параметры страницы из строки
     * /petrovich_news/archive/SECTION_CODE
     * SECTION_CODE code-year[n]-page[n]-element[n]
     * @param  mixed $URL
     * @return void
     */
    private function getParamsPage($URL=''){
        if( !empty($URL) ){
            $arrPageParams = array();
            $PageParams = explode('page', $URL);

            // [0] code-yearN-   [1] N-elementN
            // [0] code-         [1] N 
            if( count($PageParams) > 1 ){
                $SectionCode = substr($PageParams[0], 0, -1);
                $IndexPage   = $PageParams[1];
                
                //проверим на элементы
                $elementsParams = explode('element', $PageParams[1]);

                if( count($elementsParams) > 1 ){
                    $IndexPage = substr($elementsParams[0], 0, -1);
                    $arrPageParams['IndexPage'] = $IndexPage; 
                    $arrPageParams['element']   = $elementsParams[1];
                }else{
                    $arrPageParams['IndexPage'] = $IndexPage;
                }

                //разбиваем и проверяем на года
                $yearParams = explode('year', $SectionCode);
                if( count($yearParams) ){
                    $arrPageParams['SectionCode'] = $SectionCode; 
                    $arrPageParams['year'] = $yearParams[1];
                }else {
                    $arrPageParams['SectionCode'] = $SectionCode;
                }
                unset($SectionCode, $IndexPage, $PageParams );
                return $arrPageParams;
            }else {
                //проверим на элементы
                $elementsParams = explode('element', $PageParams[0]);
                if( count($elementsParams) > 1 ){
                    $arrPageParams['element']   = $elementsParams[1];
                }
                $arrPageParams['SectionCode'] = $PageParams[0];
                $arrPageParams['IndexPage'] = 0;
                return $arrPageParams;
            }
        }
    }
    
    
    /**
     * SplitIntoPage - метод для пагинации. 
     * $arrayItems -массив записей,
     * $currentPage - текущая страница, 
     * $countPage - количество элементов на странице
     *
     * @return void
     */
    private function SplitIntoPage($arrayItems, $currentPage, $countPage){
      if( $currentPage == 0) $currentPage = 1;
      $currentPage = $currentPage - 1;
      $StartNamber  = $currentPage * $countPage;
      $arrPageItems = array_slice($arrayItems, $StartNamber, $countPage);
      unset($currentPage, $StartNamber,  $countPage, $arrayItems);
      return $arrPageItems ;     
    }

    /**
     * executeComponent - подключает шаблон
     *
     * @return void
     */
    public function executeComponent()
    {
        // параметры сортировки из адресной сортировки
        $URL = '';
        if( !empty( $_GET["SECTION_CODE"] ) ){
            $URL = $_GET["SECTION_CODE"];
        }
        $PageParams = $this->getParamsPage($URL);
        $this->arResult["IndexPage"] = $PageParams["IndexPage"];

        // если есть год добавляем фильтр 
        if( !empty($PageParams["year"]) ){
          // все элементы архива
          $select = [ "*" ];
          $order  = [ "UF_XML_ID" => "DESC" ]; 
          if( !empty($PageParams["element"]) ){
            $filter = [ "UF_XML_ID" => $PageParams["element"] ]; 
          }else {
            $filter = [ "UF_YEAR" => $PageParams["year"] ]; 
          }
               
          $AllElement = $this->getItemArchive($select, $order, $filter); 
        }else{
          // все элементы архива
          $select = [ "*" ];
          $order = [ "UF_XML_ID" => "DESC" ];
          if( !empty($PageParams["element"]) ){
            $filter = [ "UF_XML_ID" => $PageParams["element"] ]; 
          }else {
            $filter = array(); 
          }        
          $AllElement = $this->getItemArchive($select, $order, $filter);
        }
        
        
        krsort($AllElement);
        // количество страниц
        $CountAllElement = count($AllElement);
        $CountPage = $CountAllElement / $this->arParams["COUNT_ELEMENT_PAGE"];
        $this->arResult["CountPage"] = (int) $CountPage;

        // Разбивка на страницы
        $ElementsPage = $this->SplitIntoPage($AllElement, $PageParams["IndexPage"], $this->arParams["COUNT_ELEMENT_PAGE"]);
        // выборка всех годов
        $this->arResult["arSelect"] = $this->GetDateParams($AllElement);

        $this->arResult["ArchiveItem"] = $ElementsPage;
        $this->includeComponentTemplate();        
        return $this->arResult;
    }
}