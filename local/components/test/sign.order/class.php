<? 
/*
    Класс обработки входящих параметров и генерации Поручения на покупку продукта

*/

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
require_once $_SERVER['DOCUMENT_ROOT'].'/local/php_interface/Library/libPDFgen/tcpdf.php';

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

class MYPDF extends TCPDF {
    // Page footer

    public function Footer() {
        global $signHtml;
        // Position at 15 mm from bottom
        $this->SetY(-25);
        // Set font
        $this->SetFont('dejavusans', '', 10);

        $this->writeHTML($signHtml, true, false, true, 0);
    }
}

class signOrder extends CBitrixComponent
{
    Private $ParamsReplace = array();
    Private $ParamsReplaceTable = array();
    
    public function onPrepareComponentParams($arParams)
    {
        $result = array(
            "CACHE_TYPE"            => $arParams["CACHE_TYPE"],
            "CACHE_TIME"            => isset($arParams["CACHE_TIME"]) ?$arParams["CACHE_TIME"]: 36000000,                        
            "PARAMS_ORDER"          => $arParams["PARAMS_ORDER"]
        );
        return $result;
    }

    private function getObjectProperty($Params){
        if(CModule::IncludeModule("iblock")) {
            $get_Object    = CIBlockElement::GetByID($Params["ID_ELEMENT"]);
            $arr_Filds_Obj = $get_Object->Fetch(); 

            $get_Property  = CIBlockElement::GetProperty($Params["ID_BLOCK"], $Params["ID_ELEMENT"]);
            while ($ar_Property = $get_Property->GetNext(true, false))
            {
                
                $arr_Filds_Obj["PROPERTY"][$ar_Property["CODE"]] = array("VALUE" => $ar_Property["VALUE"], "VALUE_XML_ID" => $ar_Property["VALUE_XML_ID"]); 
            }
            return $arr_Filds_Obj;
        }
    }

    private function getItemHL($Params){
        $hlblock = HL\HighloadBlockTable::getById($Params["ID_HL"])->fetch(); // id highload блока
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entityClass = $entity->getDataClass();
        $rsData = $entityClass::getList($Params["FILTER"]);
        
        return $rsData->Fetch();
    }

    public function generateTableContent($THML_template, $Table, $ParamsReplace){

        $THML_template_formatte = str_replace('%TABLE_CONTENT%', $Table,  $THML_template);
        return  $THML_template_formatte;

    }

    private function replaceParamsTemplate($HTML_Template, $ParamsReplace){
        
        $THML_template_formatte = $HTML_Template;
        
        foreach($ParamsReplace as $key => $item){
            $find_text = '&'.$key.'&';                   
            $THML_template_formatte = str_replace($find_text, $item,  $THML_template_formatte);
        }
        //echo $THML_template_formatte;
        return $THML_template_formatte;
    }

    private function createPDF($html, $ID_User, $URL_Create){

        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        /*$pdf->SetAuthor('Nicola Asuni');
        $pdf->SetTitle('TCPDF Example 021');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/

        // set default header data
        //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Договор', PDF_HEADER_STRING);
        $pdf->SetPrintHeader(false);

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetMargins(PDF_MARGIN_LEFT, 10, 7, 0);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------

        // set font
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->AddPage();
        // output the HTML content
        $pdf->writeHTML($html, true, 0, true, 0);

        //Close and output PDF document
        $pdf->Output($URL_Create.'.pdf', 'F');
        //exit();

    }

    // метод для дефолтного подставления значения
    private function HelperNullVal($Value, $ReValue){
        if(!empty($Value)){
            $ReturnValue = $Value;
        }else {
            $ReturnValue = $ReValue;
        }
        return $ReturnValue;
    }

    private function getTableTemplate($Type_SP){
        $get_Template_Filter = array(
            "ID_HL" => 3,
            "FILTER" => array(
                "select" => array("*"),
                "order" => array(),
                "filter" => array("UF_TYPE"=> $Type_SP)  // Задаем параметры фильтра выборки [PROPERTY] 
            ));
        $HTML_Template_table = $this->getItemHL($get_Template_Filter); 
        return $HTML_Template_table;
    }

    /*
    *   Генератор контента таблиц в зависимости от типа бумаги 
    */

    private function replaseTableContent($Content_table, $ParamsReplace){
        
        foreach($ParamsReplace as $key => $item){
            $find_text = '&'.$key.'&';         
            $Content_table = str_replace($find_text, $item, $Content_table);
        }

        return $Content_table; 
    }

    private function generateTableBONDS($ParamsReplace){
        $Content_table = '	<tr>
                <td style="border: 1px solid #999999;">1</td>
                <td style="border: 1px solid #999999;">&NAME_EMITENT& <br>&INN_EM& <br>&OGR_EM&</td>
                <td style="border: 1px solid #999999;">&NAME_SP&</td>
                <td style="border: 1px solid #999999;">&DATA_END_V&</td>
                <td style="border: 1px solid #999999;">&NOMINAL_SP&</td>
                <td style="border: 1px solid #999999;">&RATE&</td>
                <td style="border: 1px solid #999999;">&COUNT_SP&</td>
                <td style="border: 1px solid #999999;">&PRICE_SP&</td>		 
        </tr>';

        $Content_table = $this->replaseTableContent($Content_table, $ParamsReplace);

        return $Content_table;
    }
                
    private function generateTableBILL($ParamsReplace){
        $Content_table = '	<tr>
        <td style="border: 1px solid #999999;width: 4%;">1</td>
        <td style="border: 1px solid #999999;width: 16%;">&NAME_EMITENT& <br>ИНН: &INN_EM& <br>ОГРН: &OGR_EM&</td>
        <td style="border: 1px solid #999999;width: 19%;">&NAME_SP&</td>
        <td style="border: 1px solid #999999;width: 18%;">&DATA_END_V&</td>
        <td style="border: 1px solid #999999;text-align:center;width: 11%;">&NOMINAL_SP&,00</td>
        <td style="border: 1px solid #999999;text-align:center;width: 10%;">&RATE&</td>
        <td style="border: 1px solid #999999;text-align:center;width: 11%;">&COUNT_SP&</td>
        <td style="border: 1px solid #999999;text-align:center;width: 11%;">&PRICE_SP&,00</td>		 
        </tr>';

        $Content_table = $this->replaseTableContent($Content_table, $ParamsReplace);

        return $Content_table;
    }
                
    private function generateTableMETALL($ParamsReplace){
        $Content_table = '	<tr>
        <td style="border: 1px solid #999999;">1</td>
        <td style="border: 1px solid #999999;">&NAME_EMITENT& <br>ИНН:&INN_EM& <br>ОГРН:&OGR_EM&</td>
        <td style="border: 1px solid #999999;">&NAME_SP&</td>
        <td style="border: 1px solid #999999;">&DATA_END_V&</td>
        <td style="border: 1px solid #999999;">&NOMINAL_SP&,00</td>
        <td style="border: 1px solid #999999;">&RATE&</td>
        <td style="border: 1px solid #999999;">&COUNT_SP&</td>
        <td style="border: 1px solid #999999;">&PRICE_SP&,00</td>		 
        </tr>';

        $Content_table = $this->replaseTableContent($Content_table, $ParamsReplace);

        return $Content_table;
    }
                
    private function generateTableSTOCKS(){
        $Content_Table = "";
        return ;
    }
                
    private function generateTableSOSTAV(){
        $Content_Table = "";
        return ;
    }
                
    private function generateTableOPTION(){
        $Content_Table = "";
        return ;
    }

    /*
    *   Расчет даты погашения векселя 
    */
    private function getDataEndBill($data_end, $type_template_doc){
        $Content = '';
        $arrYear = ['1 год', '1 год', '2 года', '3 года'];
        switch ($type_template_doc) {
            case 'maximum':
                $Content = 'Через '.$arrYear[$data_end].' от даты выдачи';
                break;
            case 'plain_bill':
                $Content = 'Не позднее 30(тридцать) рабочих дней от предьявления. Дата окончания срока
                обращения через 1(один)год с момента приобретения';
                break;
        }
        return $Content;
    }

    private function getNominalBill($summ, $rate, $data_end, $type_template_doc){
        $Nominal = 0;

        switch ($type_template_doc) {
            case 'maximum':               
                $Sum_prosent = (($summ/100) * $rate)*$data_end;
                $Nominal = $Sum_prosent+$summ;
                break;
            case 'plain_bill':
                $Nominal = $summ;
                break;
        }
        return $Nominal;
    }

    private function getRateBill($Rate, $type_template_doc){

        switch ($type_template_doc) {
            case 'maximum':               
                $Rate = 'Дисконтная ЦБ без начисления процентов';
                break;
            case 'plain_bill':
                $Rate = $Rate;
                break;
        }
        return $Rate;
    }

    public function executeComponent()
    {
        $this->arResult["PARAMS_ORDER"] = $this->arParams["PARAMS_ORDER"];
        
        $ID_PRODUCT = $this->arParams["PARAMS_ORDER"]["ID_PRODUCT"];
        $ID_BLOCK   = $this->arParams["PARAMS_ORDER"]["ID_BLOCK"];
        $Filter_Params = [
            "ID_BLOCK"      => $ID_BLOCK,
            "ID_ELEMENT"    => $ID_PRODUCT
        ];
        $Product_Property  = $this->getObjectProperty($Filter_Params);
        $this->arResult["PRODUCT"] = $Product_Property;
        
        // получить присвоенный шаблон
        $Code_Template = $Product_Property["PROPERTY"]["THEMPLATE_HTML"]["VALUE_XML_ID"];
        $get_Template_Filter = array(
        "ID_HL" => 2,
        "FILTER" => array(
            "select" => array("UF_HTML_CODE"),
            "order" => array("ID" => "ASC"),
            "filter" => array("UF_CODE"=> $Code_Template)  // Задаем параметры фильтра выборки [PROPERTY] 
        ));
        $HTML_Template = $this->getItemHL($get_Template_Filter); 
        
        // получить шаблон и сделать замену параметров в шаблоне
        
        // параметры шапки
        $ParamsReplace = [
            "NOW_DATE"          => date('d.m.Y').' г.',
            "FIO_PARTNER"       => $this->arParams["PARAMS_ORDER"]["FIO_PARTNER"],
            "NAME_CONTRACT"     => $this->arParams["PARAMS_ORDER"]["NAME_CONTRACT"],
            "DATE_CONTRACT"     => $this->arParams["PARAMS_ORDER"]["SELECTED_CONTRACT_DATE"].' г.',
            "PORTFEEL"          => '',
            "NAME_MARKET"       =>'Внебиржевой рынок',
        ];

        $replace_template = $this->replaceParamsTemplate($HTML_Template["UF_HTML_CODE"], $ParamsReplace);

        // параметры табличной части
        $Emitent_ID = $Product_Property["PROPERTY"]["EMITENT"]["VALUE"] ;
        $Filter_Params_emitent = [
            "ID_BLOCK"      => 8,
            "ID_ELEMENT"    => $Emitent_ID
        ];
        $Emitent_Property  = $this->getObjectProperty($Filter_Params_emitent);
        $this->arResult["EMITENT"] = $Emitent_Property;
        
        // echo "<pre>";
        // print_r($Product_Property);
        // echo "</pre>";
        //

        //тип ценной бумаги
        $Type_SP = $Product_Property["PROPERTY"]["TYPE"]["VALUE_XML_ID"];
        //тип таблицы
        
        switch ($Type_SP) {
            case 'BONDS':
                $Table_Content = $this->generateTableBONDS($ParamsReplaceTable);
                break;            
            case 'BILL':
                //получить строку в ячейку о сроке погашения векселя
                $data_end       = $Product_Property['PROPERTY']['REPLAYMENT']['VALUE'];
                $template_doc   = $Product_Property['PROPERTY']['TEMPLATE']['VALUE_XML_ID'];
                $Rate           = (int) $Product_Property['PROPERTY']['PROFITABILITY']['VALUE'];
                $summ           = (int) $this->arParams["PARAMS_ORDER"]["SUMM"];
                $DATA_END_V     = $this->getDataEndBill($data_end, $template_doc);
                $Nominal_V      = $this->getNominalBill($summ, $Rate, $data_end, $template_doc);
                $Rate_text      = $this->getRateBill($Rate, $template_doc);

                //глобальные Данные для таблицы
                $ParamsReplaceTable = [
                    "NAME_EMITENT"      => $Emitent_Property["PROPERTY"]["NAME_FULL"]["VALUE"],
                    "INN_EM"            => $Emitent_Property["PROPERTY"]["INN"]["VALUE"],
                    "OGR_EM"            => $Emitent_Property["PROPERTY"]["OGRN"]["VALUE"],
                    "NAME_SP"           => $Product_Property["PROPERTY"]["TEMPLATE_NAME_SP"]["VALUE"],
                    "SUMM"              => $this->arParams["PARAMS_ORDER"]["SUMM"],
                    "COUNT_SP"          => $this->arParams["PARAMS_ORDER"]["COUNT"],
                    "DATA_END_V"        => $DATA_END_V,
                    "RATE"              => $Rate_text,
                    "PRICE_SP"          => $this->arParams["PARAMS_ORDER"]["SUMM"],
                    "NOMINAL_SP"        => $Nominal_V
                ];

                $Table_Content = $this->generateTableBILL($ParamsReplaceTable);
                break;
            case 'METALL':
                $Table_Content = $this->generateTableMETALL($ParamsReplaceTable);
                break;
            case 'STOCKS':
                $Table_Content = $this->generateTableSTOCKS($ParamsReplaceTable);
                break;
            case 'SOSTAV':
                $Table_Content = $this->generateTableSOSTAV($ParamsReplaceTable);
                break;
            case 'OPTION':
                $Table_Content = $this->generateTableOPTION($ParamsReplaceTable);
                break;            
            default:
                $Table_Content = $this->generateTableBONDS($ParamsReplaceTable);
                break;
        }


        // генерация файла предпросмотра
        $Content_table = $this->generateTableContent($replace_template, $Table_Content, $ParamsReplaceTable);        
        $this->arResult["HTML_TEMPLATE"] = $Content_table; 
        
        // сгенерировать PDF файл
        $Global_Code_User = $this->arParams["PARAMS_ORDER"]["GLOBAL_CODE_USER"];
        $Name_file = md5($ID_PRODUCT.$Global_Code_User);
        $Url_create_order = $_SERVER['DOCUMENT_ROOT'].'/upload/order/'.$Name_file;
        $this->createPDF($Content_table, $Global_Code_User, $Url_create_order);
        
        // отдать ссылку 
        //$this->arResult["PARAMS"] = $this->arParams["PARAMS_ORDER"];
        $this->arResult["URL_PDF"] = '/upload/order/'.$Name_file.'.pdf';
        $this->arResult["URL_NAME_PDF"] = $Name_file;
        // создание временного шаблона
        file_put_contents($Url_create_order.'.txt', $Content_table);
        $this->includeComponentTemplate();        
        return $this->arResult;
    }

}
?>