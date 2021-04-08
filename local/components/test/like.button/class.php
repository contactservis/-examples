<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
Loader::includeModule("highloadblock");    
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\Request;
use Bitrix\Main\Context;

/**
 * LikeCounter класс для голосования и вывода количества голосов
 * В инфоблоке нужно добавить свойство vote_sum (int)
 */
class LikeCounter extends CBitrixComponent
{        

    /**
     * onPrepareComponentParams метод получает входящие параметры при подключении компонента
     *
     * @param  mixed $arParams
     * @return void
     */
    public function onPrepareComponentParams($arParams)
    {
      $result = array(
          "CACHE_TYPE"   => $arParams["CACHE_TYPE"],
          "CACHE_TIME"   => isset($arParams["CACHE_TIME"]) ?$arParams["CACHE_TIME"]: 36000000,
          "IBLOCK_ID"    => intval($arParams["IBLOCK_ID"]),
          "ELEMENT_ID"   => intval($arParams["ELEMENT_ID"])
      );
      return $result;
    }
    
    /**
     * getRating - метод получает свойство элемента инфоблока (значение рейтинга)
     * используя API битрикса по id инфоблока и элемента получаем свойство по его коду
     *
     * @return void
     */
    private function getRating($FilterCode){
      
      if(CModule::IncludeModule("iblock")){
        $arPropertiElement = array();
        $db_props = CIBlockElement::GetProperty(
          $this->arParams['IBLOCK_ID'], 
          $this->arParams['ELEMENT_ID'], 
          "sort", 
          "asc", 
          array("CODE"=>$FilterCode)
        );
        
        while($ar_props = $db_props->Fetch()){
          $arPropertiElement = $ar_props['VALUE']; 
        }
        return $arPropertiElement;
      }

    }
    
    /**
     * checkUserVote - метод возвращает запись о голосовании пользователя
     *
     * @param  mixed $IDUser
     * @param  mixed $IDElement
     * @return void
     */
    private function checkUserVote($IDUser, $IDElement){
      $CODE_HL='LikeInfo' ;

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
          "select" => array("*"),
          "order" => array("ID" => "ASC"),
          "filter" => array(
            "UF_USER_ID"    => $IDUser,
            "UF_ID_ELEMENT" => $IDElement
            )
        ));

        $result = array();

        while($arData = $rsData->Fetch()){
          $result = $arData;
        }

        return $result;


    }
    
    /**
     * addUserVote - метод добавления записи о проголосовавшем пользователе
     *
     * @param  mixed $Params
     */
    private function addUserVote( $Params ){

      $hlblock = HL\HighloadBlockTable::getById($this->HL_ID)->fetch(); 
      $entity = HL\HighloadBlockTable::compileEntity($hlblock); 
      $entity_data_class = $entity->getDataClass();
      $result = $entity_data_class::add($Params);

    }

    
    /**
     * upUserVote - метод обновляет запись о голосовании
     *
     * @param  mixed $Params
     * @param  mixed $IDItem
     * @return void
     */
    private function upUserVote($Params, $IDItem){
      $hlblock = HL\HighloadBlockTable::getById($this->HL_ID)->fetch(); 
      $entity = HL\HighloadBlockTable::compileEntity($hlblock); 
      $entity_data_class = $entity->getDataClass();
      $result = $entity_data_class::update($IDItem, $Params);
    }

    /**
     * getIDIBlockForCode - метод для получения ID инфоблока по его коду
     *
     * @param  mixed $CodeIBlock
     * @return void
     */
    private function getIDIBlockForCode($CodeIBlock){
      try {
          Loader::includeModule('iblock');
      } catch (\Bitrix\Main\LoaderException $e) {
          throw new Exception('Не удалось загрузить модуль iblock');
      }
      $getIB =$resc = CIBlock::GetList(Array(), Array('=CODE' => $CodeIBlock), false);
      $section = $getIB->Fetch();
       
      return $section["ID"] ;
  }
    
    /**
     * executeComponent - метод определяет параметры передаваемые в шаблон и файл шаблона
     *
     * @return void
     */
    public function executeComponent()
    {
      $request = Context::getCurrent()->getRequest();
      $this->arResult["AjaxRequest"]  = false;
      // проверим голосовал ли пользователь
      global $USER;
      $UserID = $USER->GetID();
      $TypeLike = 'dislike';
      $arrVoteUser = $this->checkUserVote($UserID, $this->arParams['ELEMENT_ID']);
      $TypeLike = $arrVoteUser["UF_TYPE"];
      /** 
       * Если запрос Ajax то обновляем параметры и отдаем ajaxШаблон с новыми параметрами
      */
      if ($request->isAjaxRequest()) {
        
        // get параметры получаем из ajax запроса
        $this->arResult["AjaxRequest"]  = true;
        $UserID = $request->get('userId');
        $this->arParams['ELEMENT_ID'] = $request->get('idElement');
        $this->arParams['IBLOCK_ID'] = $this->getIDIBlockForCode('rubrics');
        $currentRating = $this->getRating('VOTE_COUNT');

        // проверим голосовал ли пользователь
        $arrVoteUser = $this->checkUserVote($UserID, $this->arParams['ELEMENT_ID']);
        
        if( !empty($arrVoteUser) ){
          // если массив не пустой то меняем только тип          
          // в зависимости от типа получаем рейтинг (если лайк - id:13 то уменшаем, если дизлайк id:14 то увеличиваем)
          // if( $arrVoteUser["UF_TYPE"] == 'like' || $arrVoteUser["UF_TYPE"] == ''  ){
          //   $id_type = 'dislike';
          //   $currentRating = $currentRating - 1;
          // }else {
          //   $id_type = 'like';
          //   $currentRating = $currentRating + 1;
          // }
          $TypeLike = $arrVoteUser["UF_TYPE"];
          switch ($arrVoteUser["UF_TYPE"]) {
            case '':
              $currentRating = $currentRating + 1;
              $id_type = 'dislike';
              break;

            case 'like':
                $currentRating = $currentRating + 1;
                $id_type = 'dislike';
              break;

            case 'dislike':
              $currentRating = $currentRating - 1;
              $id_type = 'like';
              break;

          }

          $Params = array(
            "UF_TYPE"       => $id_type,
            "UF_DATE_TIME"    => date("d.m.Y H:i:s")
          );
          // обновляем запись о голосовании
          $this->upUserVote($Params, $arrVoteUser["ID"]);
        
        }else {
          
          // добавляем новую запись
          // Массив полей для добавления
          $Params = array(
            "UF_ID_ELEMENT" => $this->arParams['ELEMENT_ID'],
            "UF_USER_ID"    => $UserID ,
            "UF_TYPE"       => 'dislike',
            "UF_DATE_TIME"    => date("d.m.Y")
          );
          $this->addUserVote($Params);
          $currentRating = $currentRating + 1;

        }
        //echo "<br> ID ElEMENT ".$this->arParams['ELEMENT_ID'];
        //обновление параметров
        CIBlockElement::SetPropertyValuesEx(
          $this->arParams['ELEMENT_ID'], 
          false, 
          array(
             'VOTE_COUNT' => $currentRating
          )
        );
        
      }

      $this->arResult["TypeLike"] = $TypeLike;
      $this->arResult["vote_sum"] = $this->getRating('VOTE_COUNT');
      
      $this->arResult["ID"] = $this->arParams['ELEMENT_ID'];
      $this->includeComponentTemplate();
      return $this->arResult;
    }
    
}

