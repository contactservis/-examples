<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

/*
    Класс выводит информацию в карточку по сделке
*/
include_once 'autoedittext.php';

class CDemoSqr extends CBitrixComponent
{       
    public $ID_CONTRACT;
    public $USER_PROPERTY;
    private $ID_BLOCK_OFFERS = 3; // ID Инфоблока с объявлениями Клиентов
    private $ID_BLOCK_TEMPLATES_CONTRACTS = 5; // ID Инфоблока шаблона договоров
    
    public function onPrepareComponentParams($arParams)
    {
        $result = array(
            "CACHE_TYPE"            => $arParams["CACHE_TYPE"],
            "CACHE_TIME"            => isset($arParams["CACHE_TIME"]) ?$arParams["CACHE_TIME"]: 36000000,
            "X"                     => intval($arParams["X"]),
            "IBLOCK_ID"             => intval($arParams["IBLOCK_ID"]),
            "SECTION_ID"            => intval($arParams["SECTION_ID"]),
            "ELEMENT_ID"            => intval($arParams["ELEMENT_ID"]),
            "IBLOCK_ID_CONTRACT"    => intval($arParams["IBLOCK_ID_CONTRACT"]),
            "TYPE_USER_PROF"        => intval($arParams["TYPE_USER_PROF"])
        );
        return $result;
    }

    private function getElement($id_element) {
        $arPact = array();        
        if(CModule::IncludeModule("iblock"))
            {
                $res = CIBlockElement::GetByID($id_element);
                if($ar_res = $res->GetNext()){
                    return $ar_res;
                }
                
            }        
    }

    // Все свойства элемента
    private function getProperty($id_iblok, $id_element){        
        $db_props           = CIBlockElement::GetProperty($id_iblok, $id_element, "sort", "asc", array());         
        $array_props        = array();        
        $array_img          = array();
        $array_unclude_file = array();

        while($ar_props = $db_props->Fetch()){ 
            
            $array_props[$ar_props["CODE"]] = $ar_props ;
            
            if ($ar_props["CODE"] == "INPUT_FILES"){
                $file_path      = CFile::GetPath($ar_props["VALUE"]);
                $array_img[]    = array("URL" => $file_path, "PROPERTY" => $ar_props);
            }

            if ($ar_props["CODE"] == "DOGOVOR_IMG"){
                if(!empty($ar_props["VALUE"])) {
                    $file_path = CFile::GetPath($ar_props["VALUE"]);
                    $array_img_dogovor[] = array("URL" => $file_path, "PROPERTY" => $ar_props);
                }
            }

            if ($ar_props["CODE"] == "MAIN_FILES"){
                if(!empty($ar_props["VALUE"])){
                    $file_path = CFile::GetPath($ar_props["VALUE"]);
                    $array_unclude_file[] = array("URL" => $file_path, "PROPERTY" => $ar_props);
                }
            }

        }
        
        $array_props["IMG_FILE"]        = $array_img;
        $array_props["DOGOVOR_IMG"]     = $array_img_dogovor;
        $array_props["INCLUDE_FILES"]   = $array_unclude_file;

        if(!empty($array_props["ID_DOGOVORA"]["VALUE"])){
            $this->ID_CONTRACT = $array_props["ID_DOGOVORA"]["VALUE"];            
        }

        return $array_props;
    }

    private function convertContent($Content){

        $userID = $this->arResult["CONTRACT_PROPERTY"]['CONTRACT_PROPERTY']['USER_A']['VALUE'];
        $userA = CUser::GetByID($userID)->GetNext();

        $regexp         = "/%DATE%/ui";
        $replacement    = date("d m Y") ;
        $Content = preg_replace($regexp, $replacement, $Content);

        /*$regexp         = "/%REQUISITE%/ui";
        $replacement    = '';
        $Content = preg_replace($regexp, $replacement, $Content);*/

        /*$regexp         = "/%FIO%/ui";
        $replacement    = $userA['LAST_NAME'].' '.$userA['NAME'].' '.$userA['SECOND_NAME'];
        $Content = preg_replace($regexp, $replacement, $Content);

        $regexp         = "/%ADDRESS%/ui";
        $replacement    = [
            $userA['PERSONAL_ZIP'],
            $userA['USER_PROP']['PERSONAL_STATE'],
            $userA['USER_PROP']['PERSONAL_CITY'],
            $userA['USER_PROP']['UF_STREET'],
            $userA['USER_PROP']['UF_N_HOUSE']
        ];

        $replacement = array_filter($replacement, function($element) {
            return !empty($element);
        });
        $replacement = implode(', ', $replacement);

        $Content = preg_replace($regexp, $replacement, $Content);*/

        #если создатель договора и контрагент оно и то же лицо
        if($userID!=$this->arResult['USER_PROP']['ID']){
            $regexp         = "/%FIO_CONTRAGENT%/ui";
            $replacement    = $this->arResult['JS_DATA']['USER']['NAME']['VALUE'];
            $Content = preg_replace($regexp, $replacement, $Content);

            $regexp         = "/%ADDRESS_CONTRAGENT%/ui";
            $replacement    = [
                $this->arResult['JS_DATA']['USER']['INN']['VALUE'],
                $this->arResult['JS_DATA']['USER']['REGION']['VALUE'],
                $this->arResult['JS_DATA']['USER']['CITY']['VALUE'],
                $this->arResult['JS_DATA']['USER']['STREET']['VALUE'],
                $this->arResult['JS_DATA']['USER']['HOUSE']['VALUE']
            ];

            $replacement = array_filter($replacement, function($element) {
                return !empty($element);
            });

            $replacement = implode(', ', $replacement);

            $Content = preg_replace($regexp, $replacement, $Content);
        }

        /*$regexp         = '/<recont fio.*recont>/ui';
        $replacement    = 'Соловьёв Игорь Владимирович' ;
        $Content = preg_replace($regexp, $replacement, $Content);*/
        return $Content;
    }

    //Cвойства контракта текст контаракта
    private function getPropertyContract($id_infobloc_contract){        
        // объект
        $array_props    = array(); 
        $res            = CIBlockElement::GetByID($this->ID_CONTRACT);
        
        if($ar_res = $res->GetNext(true, false)){
            $array_props["CONTRACT"] = $ar_res;
        }

        // свойства контракта
        $db_props       = CIBlockElement::GetProperty($id_infobloc_contract, $this->ID_CONTRACT, "sort", "asc", array());

        while($ar_props = $db_props->Fetch()){
            $array_props["CONTRACT_PROPERTY"][$ar_props["CODE"]] = $ar_props;
        }
        
        return $array_props;
    }

    private function getListCategory(){
        $items          = GetIBlockSectionList(5, 0, Array("sort"=>"asc"), 10);
        $arr_sections   = array();

        while($arResult = $items->GetNext()){       
            $arr_sections[] = array("NAME" => $arResult["NAME"], "ID" => $arResult["ID"]);
        }

        return $arr_sections;
    }

    private function getTemplateContractCategote(){
        $IBLOCK_ID    = $this->ID_BLOCK_TEMPLATES_CONTRACTS;
        if(CModule::IncludeModule("iblock")){
            $arFilter    = Array(
                'IBLOCK_ID'=>$IBLOCK_ID,
                'GLOBAL_ACTIVE'=>'Y');
            $obSection    = CIBlockSection::GetTreeList($arFilter);
            $arThree = array();
            while($arResult = $obSection->GetNext()){
                $arThree[]= $arResult;
            }
            return $arThree;
        }
    }

    private function getMultyProperty($ID_IBLOCK, $ID_EL){
        $VALUES = array();
        $res = CIBlockElement::GetProperty($ID_IBLOCK, $ID_EL, "sort", "asc", array("CODE" => "STEPS"));
        while ($ob = $res->GetNext())
        {
            if(!empty($ob['VALUE'])){
                $VALUES[] = $ob['VALUE'];
            }
        }
        return $VALUES;
    }

    function paramsUser($arParams){        
        $arResult["INFOBLOCK_ID"]       = $arParams["IBLOCK_ID"];
        $arResult["INFOBLOCK_C_ID"]     = $arParams["IBLOCK_ID_CONTRACT"];
        $arResult["SECTION_ID"]         = $arParams["SECTION_ID"];
        $arResult["ELEMENT_ID"]         = $arParams["ELEMENT_ID"];
        
        return $arResult;
    }

    public function getSendContractItem($IDContract, $userID){
        global $USER;
        CModule::IncludeModule("highloadblock");
        // получить все подписанны сделки
        $ID_hl_send_contract_text = 3;
        $hlblock = HL\HighloadBlockTable::getById($ID_hl_send_contract_text)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $rsData = $entity_data_class::getList(array(
            "select" => array("*"),
            "order" => array("ID" => "ASC"),
            "filter" => array("UF_ID_CONTRACT" => $IDContract, "UF_ID_USER_B"=> $userID)
        ));

        while($arData = $rsData->Fetch()){
            $arSendItem  = $arData;
        }

        return $arSendItem;
    }

    // подписание контракта
    private function sendContract($hlbl, $Params){

        $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $result = $entity_data_class::add($Params);  
        // возвращаем id записи
        if (!$result->isSuccess()) {
            $errors = $result->getErrorMessages();
        } else {
            $id = $result->getId();
        }

        return $id;
    }

    // подписание измененного контракта
    private function sendEditContract($hlbl, $ID, $Params){

        $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $result = $entity_data_class::update($ID, $Params);

        // возвращаем id записи
        if (!$result->isSuccess()) {
            $errors = $result->getErrorMessages();
        } else {
            $id = $result->getId();
        }
        //return $id;
    }


    private function getNewRedaction($userId, $arSdelka){
        $arFilter = [
            'IBLOCK_ID'=>6,
            'CODE'=>$arSdelka['CODE'].'_'.$arSdelka['ID'].'_user_'.$userId,
            'ACTIVE'=>'Y',
        ];
        $arSelect = [
            'IBLOCK_ID',
            'ID'
        ];
        $res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
        if($obj = $res->GetNext(true, false)){
            $arNewRedaction = $obj;
        }
        else{
            $arNewRedaction = false;
        }
        return $arNewRedaction;
    }

    private function getJsRequisit(){
        if(!empty($this->arResult['COMPANY_PROP'])){
            foreach ($this->arResult['COMPANY_PROP'] as $code=>$prop){
                if(!is_array($prop)){
                    if($code=='NAME'){
                        $this->arResult['JS_DATA']['USER'][$code] = [
                            'NAME'=>'Название компании',
                            'VALUE'=>$prop
                        ];
                    }
                }
                else{
                    $this->arResult['JS_DATA']['USER'][$code] = $prop;
                }
            }
        }
        else{
            $this->arResult['JS_DATA']['USER'] = [
                'NAME' => [
                    'NAME'=>'ФИО',
                    'VALUE'=> $this->arResult["USER_PROP"]["LAST_NAME"].' '.$this->arResult["USER_PROP"]["NAME"].' '.$this->arResult["USER_PROP"]["SECOND_NAME"],
                ],
                'PHONE' => [
                    'NAME'=>'Телефон',
                    'VALUE'=> $this->arResult["USER_PROP"]["PERSONAL_PHONE"],
                ],
                'PASSPORT' => [
                    'NAME'=>'Пасопрт',
                    'VALUE'=> $this->arResult["USER_PROP"]["UF_PASSPORT"].' '.$this->arResult["USER_PROP"]["UF_KEM_VPASSPORT"],
                ],
                'INDEX' => [
                    'NAME'=>'Индекс',
                    'VALUE'=> $this->arResult["USER_PROP"]["PERSONAL_ZIP"],
                ],
                'CITY' => [
                    'NAME'=>'Город',
                    'VALUE'=> $this->arResult["USER_PROP"]["PERSONAL_CITY"],
                ],
                'REGION' => [
                    'NAME'=>'Область',
                    'VALUE'=> $this->arResult["USER_PROP"]["UF_REGION"],
                ],
                'STREET' => [
                    'NAME'=>'Улица',
                    'VALUE'=> $this->arResult["USER_PROP"]["UF_STREET"],
                ],
                'HOUSE' => [
                    'NAME'=>'Дом',
                    'VALUE'=> $this->arResult["USER_PROP"]["UF_N_HOUSE"],
                ]
            ];
        }
    }

    private function getCompany($type){
        //если выбран профиль компании
        $resultDataProfile = [];
        if($type == 1){
            //при создании договора компания береться из сделки и сравниваеться с текущим пользователем
            if(
                !empty($this->arResult['PROPERTY']['ID_COMPANY']['VALUE']) &&
                $this->arResult['USER_PROP']['UF_CUR_COMPANY'] == $this->arResult['PROPERTY']['ID_COMPANY']['VALUE']
            ){
                $idCompany =  $this->arResult['USER_PROP']['UF_CUR_COMPANY'];
            }
        }
        elseif(!empty($this->arResult['USER_PROP']['UF_CUR_COMPANY'])){
            //полуение данных компании если выбран профиль "компания"
            $idCompany =  $this->arResult['USER_PROP']['UF_CUR_COMPANY'];
        }

        if(!empty($idCompany)){
            $res = CIBlockElement::GetList(
                [],
                [
                    'IBLOCK_ID' => 8,
                    'ID' => $this->arResult['USER_PROP']['UF_CUR_COMPANY'],
                    'ACTIVE' => 'Y'
                ],
                false,
                false,
                ['IBLOCK_ID', 'ID', 'NAME']
            );
            if ($obj = $res->GetNextElement()) {
                $arCompany = $obj->GetFields();
                $arCompany['PROPERTY'] = $obj->GetProperties();
            }
            if (!empty($arCompany)) {
                $resultDataProfile = [
                    'IBLOCK_ID' => $arCompany['IBLOCK_ID'],
                    'ID' => $arCompany['ID'],
                    'NAME' => $arCompany['NAME']
                ];
                foreach ($arCompany['PROPERTY'] as $prop) {
                    $resultDataProfile[$prop['CODE']] = [
                        'NAME' => $prop['NAME'],
                        'VALUE' => $prop['VALUE']
                    ];
                }
            }
        }

        return $resultDataProfile;
    }

    public function executeComponent()
    {
        global $USER;
        $userId = CUser::GetID();
        $this->arResult["USER_ID"] = $userId;
        //if($this->startResultCache($this->arParams['CACHE_TIME'], $_GET['SECTION_ID'].$_GET['ELEMENT_ID'].$_GET['ID_TEMPLATE'].$userId))
        //{
            $this->arResult                         = array_merge($this->arResult, $this->paramsUser($this->arParams));
            // данные владельца сделки           
            $UserContractHolder                     = CUser::GetByID(CUser::GetID());
            $arrUserContractHolder                  = $UserContractHolder->Fetch();
            $this->USER_PROPERTY                    = $arrUserContractHolder;
            $this->arResult["USER_PROP"]            = $arrUserContractHolder;
            $this->arResult["USER_LOGIN"]           = CUser::GetLogin();
            $this->arResult["ELEMENT"]              = $this->getElement($this->arResult["ELEMENT_ID"]);
            $this->arResult["PROPERTY"]             = $this->getProperty($this->arResult["INFOBLOCK_ID"], $this->arResult["ELEMENT_ID"]);


            if($this->arParams['TYPE_USER_PROF'] == 1){
                if(!empty($this->arResult['PROPERTY']['ID_COMPANY']['VALUE']) || !empty($this->arResult['USER_PROP']['UF_CUR_COMPANY'])){
                    if( $this->arResult['USER_PROP']['UF_CUR_COMPANY'] != $this->arResult['PROPERTY']['ID_COMPANY']['VALUE']){
                        $this->arResult['ERROR'] = 'Ошибка. Выберите профиль, указанный в сделке';
                        $this->includeComponentTemplate();
                        return;
                    }
                }
                else{
                    if( $this->arResult['USER_PROP']['ID'] != $this->arResult['PROPERTY']['PACT_USER']['VALUE']){
                        $this->arResult['ERROR'] = 'Ошибка. Выберите профиль, указанный в сделке';
                        $this->includeComponentTemplate();
                        return;
                    }
                }
            }

            $this->arResult["COMPANY_PROP"]         = $this->getCompany($this->arParams['TYPE_USER_PROF']);
            $this->arResult["LIST_CATEGORY"]        = $this->getListCategory();
            $this->arResult["THREE_TEMPLATE"]       = $this->getTemplateContractCategote();

            if(!empty($this->ID_CONTRACT)){
                $this->arResult["TEMPLATE_CONTENT"] = $this->getElement($this->ID_CONTRACT);
                //$this->arResult["TEMPLATE_CONTENT_PROPERTY"]    = $this->getMultyProperty(4, $this->arResult['TEMPLATE_CONTENT']['ID']);
                $this->arResult["DOGOVOR_IMG"] = $this->getProperty(4, $this->ID_CONTRACT)['DOGOVOR_IMG'];
            }

            //ополучаем данные по контракту
            $this->arResult["CONTRACT_PROPERTY"]    = $this->getPropertyContract($this->arResult["INFOBLOCK_C_ID"]);

            if(!empty($_GET["ID_TEMPLATE"])){                
                $this->arResult["TEMPLATE_CONTENT"] = $this->getElement($_GET["ID_TEMPLATE"]);
                $this->arResult["TEMPLATE_CONTENT_PROPERTY"]    = $this->getMultyProperty(5, $_GET["ID_TEMPLATE"]);
                $this->arResult["DOGOVOR_IMG"] = $this->getProperty(4, $this->arResult["ELEMENT_ID"])['DOGOVOR_IMG'];
            }

            #поиск имеющихся своих редакций для этой сделки по пользователю
            $this->arResult['NEW_REDACTION'] = $this->getNewRedaction($userId, $this->arResult["ELEMENT"]);


            #формирование масива для js (подстановка реквизитов)
            $this->getJsRequisit();

            //замена реквизитов в тексте контракта
            $Contract_template_Text = $this->convertContent($this->arResult["CONTRACT_PROPERTY"]["CONTRACT"]["DETAIL_TEXT"]);
            $clear_text = new autoedittext();
            $Contract_template_Text                 = $clear_text->replaceTag($Contract_template_Text, $this->arResult['JS_DATA']['USER']);
            $this->arResult["CONTRACT_PROPERTY"]["CONTRACT"]["DETAIL_TEXT"] = str_replace("&nbsp;", "", $Contract_template_Text);


            $this->EndResultCache();
        //}
        // статус подписанного контракта
        $this->arResult['SEND_CONTRACT'] = 'N';
        // если пользователь вернулся после авторизации ЕСИА        
        if($_GET['code']){
            /*
                подписываем контракт
                статусы подписания 1-подписан с одной стороны, 2- подписан с двух сторон, 3- изменен
                контракт может подписываться с измененым текстом процедура подписания немного другая, в этом случае ЕСИА возвращает
                GET - ID_SENDITEM уже созданной записи с измененным текстом
            */
            if(!empty($_GET['ID_SENDITEM'])){
                $urlEsia = $_SERVER['DOCUMENT_ROOT'] . "/esia_test";
                include $urlEsia . "/Esia.php";
                include $urlEsia . "/EsiaOmniAuth.php";
                include $urlEsia . "/config_esia.php";

                $config_esia = new ConfigESIA();

                $esia = new EsiaOmniAuth($config_esia->config);
                $info = array();
                $token = $esia->get_token($_GET['code']);
                $info = $esia->get_info($token);

                $rsUser = CUser::GetByID($userId);
                $UserParams = $rsUser->Fetch();
                $ESIA_ID = $UserParams['UF_ESIA_ID'];

                // проверим идентификаторы из есиа и из профиля пользователя
                if ($info['user_id'] == $ESIA_ID) {
                    $hash_key = hash('md5', $info['user_info']['eTag'] . time());

                    $Params = array(
                        'UF_TIME_SEND_USER_A' => ConvertTimeStamp(time(), "FULL"),
                        'UF_VER_CODE_USER_B' => $info['user_info']['eTag'],
                        'UF_TIME_SEND_USER_B' => ConvertTimeStamp(time(), "FULL"),
                        'UF_STATUS' => 3,
                        'UF_HASH_SEND' => $hash_key,
                    );

                    $this->sendEditContract(3, $_GET['ID_SENDITEM'], $Params);
                    $this->arResult['SEND_CONTRACT'] = 'Y';
                } else {
                    // выводим ошибку
                    $this->arResult['SEND_CONTRACT'] = 'ERR_ID';
                }
            }else{
                $urlEsia = $_SERVER['DOCUMENT_ROOT'] . "/esia_test";
                include $urlEsia . "/Esia.php";
                include $urlEsia . "/EsiaOmniAuth.php";
                include $urlEsia . "/config_esia.php";

                $config_esia = new ConfigESIA();

                $esia = new EsiaOmniAuth($config_esia->config);
                $info = array();
                $token = $esia->get_token($_GET['code']);
                $info = $esia->get_info($token);

                $rsUser = CUser::GetByID($userId);
                $UserParams = $rsUser->Fetch();
                $ESIA_ID = $UserParams['UF_ESIA_ID'];
                // проверим идентификаторы из есиа и из профиля пользователя
                if ($info['user_id'] == $ESIA_ID) {
                    /*
                        подписываем контракт
                        статусы подписания 1-подписан с одной стороны, 2- подписан с двух сторон, 3- изменен
                        контракт может подписываться с измененым текстом процедура подписания немного другая, в этом случае ЕСИА возвращает
                        GET - ID_SEND уже созданной записи с измененным текстом
                    */
                    if (!empty($_GET['ID_SEND'])) {
                        echo "Договор подписан с изменениями" . $_GET['ID_SEND'];
                    } else {
                        $hash_key = hash('md5', $info['user_info']['eTag'] . time());
                        $Params = array(
                            'UF_VER_CODE_USER_A' => '',
                            'UF_ID_USER_A' => $this->arResult["CONTRACT_PROPERTY"]["CONTRACT_PROPERTY"]["USER_A"]["VALUE"], // владелец договора
                            'UF_ID_COMPANY_A'=>$this->arResult["CONTRACT_PROPERTY"]["CONTRACT_PROPERTY"]["COMPANY_A"]["VALUE"],
                            'UF_TEL_CODE_USER_A' => '', //пока не заполняем авторизация через ЕСИА
                            'UF_TIME_SEND_USER_A' => ConvertTimeStamp(time(), "FULL"),
                            'UF_ID_CONTRACT' => $this->ID_CONTRACT,
                            'UF_ID_USER_B' => $userId, // подписавшая сторона
                            'UF_ID_COMPANY_B'=> $this->arResult['USER_PROP']['UF_CUR_COMPANY'],
                            'UF_VER_CODE_USER_B' => $info['user_info']['eTag'],
                            'UF_TEL_CODE_USER_B' => '',
                            'UF_TIME_SEND_USER_B' => ConvertTimeStamp(time(), "FULL"),
                            'UF_STATUS' => 1,
                            'UF_HASH_SEND' => $hash_key,
                            'UF_ID_SEND_USER' => $userId
                        );
                        // создание записи подписания контрака
                        $id_add_item = $this->sendContract(3, $Params);
                        // создание записи с текстом
                        $Contract_params = array(
                            'UF_ID_CONTRACT' => $this->ID_CONTRACT,
                            'UF_ID_SEND_ITEM' => $id_add_item,
                            'UF_TEXT_CONTRACT' => $this->arResult["CONTRACT_PROPERTY"]["CONTRACT"]["DETAIL_TEXT"],
                            'UF_HASH' => $hash_key,
                            'UF_CANTRACT_IMG' => '',
                            'UF_ID_USER_SEND' => $userId,
                        );
                        $id_add_item = $this->sendContract(7, $Contract_params);
                    }
                    $this->arResult['SEND_CONTRACT'] = 'Y';
                } else {
                    // выводим ошибку
                    $this->arResult['SEND_CONTRACT'] = 'ERR_ID';
                }
            }
        }

        $this->arResult["SIGN_DOGOVOR"] = $this->getSendContractItem($this->arResult['PROPERTY']['ID_DOGOVORA']['VALUE'], $this->arResult['USER_ID']);
        $this->includeComponentTemplate();
        
        return $this->arResult;
    }
};

?>