<?php 
use Bitrix\Main\Loader;
Loader::includeModule("highloadblock");     
use Bitrix\Highloadblock as HL; 
use Bitrix\Main\Entity;

class QuizComponent extends CBitrixComponent
{
    private $HL_ID;

    public function onPrepareComponentParams($arParams)
    {
        $result = array(
            "CACHE_TYPE"  => $arParams["CACHE_TYPE"],
            "CACHE_TIME"  => isset($arParams["CACHE_TIME"]) ?$arParams["CACHE_TIME"]: 36000000,            
            "IBLOCK_ID"   => intval($arParams["IBLOCK_ID"]),
            "SECTION_ID"  => intval($arParams["SECTION_ID"]),
            "ELEMENT_ID"  => intval($arParams["ELEMENT_ID"]),
            "ACTION_AJAX"  => $arParams["ACTION_AJAX"],
            "QUIZ_ID"     => $arParams["QUIZ_ID"]
        );
        return $result;
    }

  
    /**
     * getItemArchive
     *
     * @return void
     */
    private function getQuestuons( $select=array(), $order=array(), $filter=array(), $CODE_HL='QuizQuestions', $limit=array()){
        
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
            $tVariants = array();
            
            if( empty($arData["UF_QQ_PICTURE"]) ){
                $arr["UF_QQ_PICTURE"]   = [
                    "src"   => "static/img/general/start-bg.jpg",
                    "width"  => 1060,
                    "height" => 340,
                    "size"   => "58166"
                ];
            }else {
                $arr["UF_QQ_PICTURE"]  = [ 
                    "src"   => CFile::GetPath($arData["UF_QQ_PICTURE"])
                ];
            }

            if( empty($arData["UF_QQ_PICTURE_CONTENT"]) ){
                $arr["UF_QQ_PICTURE_CONTENT"]   = [ 
                    "src"   => '/local/components/mlgr/quiz/templates/.default/quest1.jpg',
                    "width"  => 1060,
                    "height" => 340,
                    "size"   => "58166"
                ];
            }else {
                $arr["UF_QQ_PICTURE_CONTENT"]  = [ 
                    "src"   => CFile::GetPath($arData["UF_QQ_PICTURE_CONTENT"])
                ];
            }
            
            
            foreach ($arData['UF_QQ_VARIANTS'] as $variantKey => $variant) {
                $tVariants[] = unserialize($variant);
            }
            
            $arr["UF_QQ_VARIANTS"]   = $tVariants;
            $arItem[] = $arr;
        }

        return $arItem;
    }
    
    /**
     * getQuiz
     *
     * @param  mixed $select
     * @param  mixed $order
     * @param  mixed $filter
     * @param  mixed $CODE_HL
     * @param  mixed $limit
     * @return void
     */
    private function getQuiz( $select=array(), $order=array(), $filter=array(), $CODE_HL='ViktorinaList', $limit=array()){
        
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
            if( empty($arData["UF_Q_PICTURE"]) ){
                $arr["UF_Q_PICTURE"]   = [
                    "src"   => "static/img/general/start-bg.jpg",
                    "width"  => 1060,
                    "height" => 340,
                    "size"   => "58166"
                ];
            }else {
                $arr["UF_Q_PICTURE"]  = [ 
                    "src"   => CFile::GetPath($arData["UF_Q_PICTURE"])
                ];
            }

            $arItem = $arr;
        }

        return $arItem;
    }
    
    /**
     * getQuiz
     *
     * @param  mixed $select
     * @param  mixed $order
     * @param  mixed $filter
     * @param  mixed $CODE_HL
     * @param  mixed $limit
     * @return void
     */
    private function getQuizTreatments( $select=array(), $order=array(), $filter=array(), $CODE_HL='QuizTreatments', $limit=array()){
        
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
            if( empty($arData["UF_QT_PICTURE"]) ){
                $arr["UF_QT_PICTURE"]   = [
                    "src"   => "static/img/general/start-bg.jpg",
                    "width"  => 1060,
                    "height" => 340,
                    "size"   => "58166"
                ];
            }else {
                $arr["UF_QT_PICTURE"]  = [ 
                    "src"   => CFile::GetPath($arData["UF_QT_PICTURE"])
                ];
            }

            $arItem[] = $arr;
        }

        return $arItem;
    }

    /**
     * метод для подсчета суммы ответов для вопросов.
     */
    public function countAnswers($answers)
    {
        if (!is_array($answers)) {
            throw new Exception('аргумент не является массивом');
        }

        $sum = 0;
        foreach ($answers as $answer) {
            $sum += $answer;
        }

        return $sum;
    }

    public function getTreatment($arTreatments, $sum)
    {
        // $arTreatments = \ORM\Table\QuizTreatmentsTable::getList([
        //     'filter' => [
        //         'UF_QT_QUIZID' => $quizId
        //     ]
        // ])->fetchAll();

        // $resTreatment = [];

        foreach ($arTreatments as $arTreatment) {
            preg_match('/(\d+)-(\d+)/', $arTreatment['UF_QT_RANGE'], $matches);
            $min = intval($matches[1]);
            $max = intval($matches[2]);

            if (
                $sum >= $min &&
                $sum <= $max
            ) {
                $resTreatment = $arTreatment;
                break;
            }
        }

        return $resTreatment;
    }

    /**
     * executeComponent - подключает шаблон
     *
     * @return void
     */
    public function executeComponent()
    {
        $Action = $this->arParams["ACTION_AJAX"];
        
        switch ($Action) {
            case 'getquiz':
                $filter = [
                    'ID' => $this->arParams["QUIZ_ID"]
                ];
                $this->arResult["quiz"] = $this->getQuiz(array("*"), array("ID" => "ASC"), $filter);
                $this->includeComponentTemplate('quizTitle');        
                return $this->arResult;
                break;
            case 'getquiz_ajax':
                $filter = [
                    'ID' => $this->arParams["QUIZ_ID"]
                ];
                $json["quiz"] = $this->getQuiz(array("*"), array("ID" => "ASC"), $filter);
                echo json_encode($json);
                break;

            case 'QuizTreatments':
                $arrAnswers = [1, 0, 0];
                
                if( !empty($_GET["answers"]) ){
                    $arrAnswers = $_GET["answers"];
                }
                $SummAnswers = $this->countAnswers($arrAnswers);
                //echo json_encode($SummAnswers);
                
                $filter = [
                    'UF_QT_QUIZID' => $this->arParams["QUIZ_ID"]
                ];
                $arrgetQuizTreatments = $this->getQuizTreatments(array("*"), array("ID" => "ASC"), $filter);
                $jsonResult["treatment"] = $this->getTreatment($arrgetQuizTreatments, $SummAnswers);
                $jsonResult["treatment"]["answer"] = $SummAnswers;
                //$jsonResult["treatment"]["All"] = $arrgetQuizTreatments ;
                
                echo json_encode($jsonResult);
                break;
        
            case 'getquestions':
                // получить все вопросы
                $filter = [
                    'UF_QQ_QUIZID' => 1
                ];
                $arQuesions["arQuestions"] = $this->getQuestuons(array("*"), array("ID" => "ASC"), $filter);
                echo json_encode($arQuesions);
                break;

            default:
                # code...
                break;
        }
    }
}