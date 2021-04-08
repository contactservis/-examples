<?
/*
    $SendUserProperty  данные ползователя еоторый подписывает договор
*/

$SendUserProperty = $arResult["USER_PROP"];

/*
    $arrayNameParams массив подстановки названий в реквизиты таблицы
*/

$arrayNameParams = array(
    "fio" => "ФИО",
    "adress" => "Адрес",
    "phone" => "Телефон",
    "passport" => "Паспорт",
    "pay_params" => "Платежные реквизиты"
);
?>
<?
switch ($arResult['SEND_CONTRACT']) {
    // Процедура подписания
    case 'N': ?>
    <h1><?=$arResult["ELEMENT"]["NAME"]?></h1>
    <div class="tender cardDogovor">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="cardDogovor-boxTool">
                    <?if ($arResult["USER_ID"] == $arResult["PROPERTY"]["PACT_USER"]["VALUE"]){
                    ?>
                        <h3>Это ваш договор:</h3>
                        <a href="/my_pacts/add_my_dogovor/?ELEMENT_ID=<?=$arResult['ELEMENT_ID']?>&EDIT=EDIT" class="btn btn-nfk" > Внести изменения </a>
                    <?
                    }else {?>
                        <? // блокировка кнопки от повторного подписания темже пользователем
                        //if($arResult["USER_ID"] != $arResult['SIGN_DOGOVOR']['UF_ID_USER_B']):                    
                        ?>
                            <? if(isset($arResult['PROPERTY']["INCLUDE_FILES"])){?>
                                <? foreach($arResult['PROPERTY']["INCLUDE_FILES"] as $Unclude_file){?>
                                    <a href="<?=$Unclude_file["URL"]?>" class="cardPact-rightPanel-url" target="_blank" style="padding: 20px 0; display:block;">
                                        <img src="<?=SITE_TEMPLATE_PATH?>/image/icon-contract.png"> Дополнительный файл
                                    </a>
                                <?}?>
                            <? }?>
                            <button class="btn btn-nfk <?if(!empty($arResult["DOGOVOR_IMG"][0]['URL'])):?>canvas-img<?endif?>" id="popup_send_contract" data="signed">
                                Подписать договор
                            </button>
                            <a href="/pacts/view_pact/edit_dogovor/?ELEMENT_ID=<?=$_GET['ELEMENT_ID']?>" class="btn btn-nfk" >Предложить свою редакцию</a>                            
                        <? // endif?>                    
                        <?if(empty($arResult['NEW_REDACTION'])):?>
                            <!--<button class="btn btn-nfk" id="new_redaction" data-id_element="<?=$_GET['ELEMENT_ID']?>">Предложить свою редакцию</button>-->
                        <?endif?>

                    <?}?>
                    <div class="container-img">
                        <img src="<?=SITE_TEMPLATE_PATH?>/img/write_contract.jpg">
                    </div>
                </div>            
            </div>               
            <div class="col-lg-9 col-md-9 col-sm-12 js-dogovor">
                <?/*
                <div class="tools_redactor">
                    <button type="button" class="btn btn-nfk btn-default" data-toggle="tooltip" data-placement="left" title="Сохранить" id="save_btn"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                    <button type="button" class="btn btn-nfk btn-default space_right" id="btn-edit" data-toggle="tooltip" data-placement="left" title="Включить редактирование текста" contenteditable="false"><span class="glyphicon glyphicon-pencil"></span></button>                                    
                </div>
                */?>
                <? // если договор в сканах или файлах изображений ?>
                <?if(!empty($arResult["DOGOVOR_IMG"][0]['URL'])):?>
                    <div class="cardDogovor-boxViewText" id="canvas" contenteditable="false">
                        <?foreach ($arResult["DOGOVOR_IMG"] as $item):?>
                            <div class="document-img" style="text-align: center">
                                <img src="<?=$item['URL']?>">
                            </div>
                            <br>
                        <?endforeach?>
                    </div>
                <?else:?>
                    <div class="cardDogovor-boxViewText" id="canvas" contenteditable="false">
                        <?=$arResult["CONTRACT_PROPERTY"]["CONTRACT"]["DETAIL_TEXT"]?>
                        <?
                        // вывод видимых реквизитов пользователя                    
                        $userProperty = json_decode($arResult["CONTRACT_PROPERTY"]["CONTRACT_PROPERTY"]["USER_PROPERTY"]["VALUE"], true);                    
                        ?>
                        <table cellpadding="5" border="1" bordercolor="#cecece" cellspacing="0" width="100%">
                            <?
                            if(!empty($userProperty)) {
                                print_r($userProperty);
                                foreach($userProperty as $key => $Item){
                                    if($Item["view"] == "Y"){	
                                        ?>
                                            <tr>
                                                <td style="width: 20%;padding: 5px;"><b><?=$arrayNameParams[$key]?></b></td>
                                                <td style="width: 30%;padding: 5px;">
                                                    <?
                                                        foreach ($Item["params"] as $value) {
                                                            echo $value.", ";
                                                        }
                                                    ?>
                                                </td>
                                                <td style="width: 20%;padding: 5px;"><b><?=$arrayNameParams[$key]?></b></td>
                                                <td style="width: 30%;padding: 5px;"></td>
                                            </tr>
                                        <? 
                                    }else { ?>
                                        <tr>
                                            <td style="width: 20%;padding: 5px;"><b><?=$arrayNameParams[$key]?></b></td>
                                            <td style="width: 30%;padding: 5px;">
                                            {данные будут доступны после подписания договора}
                                            </td>
                                            <td style="width: 20%;padding: 5px;"><b><?=$arrayNameParams[$key]?></b></td>
                                            <td style="width: 30%;padding: 5px;"></td>
                                        </tr>
                                    <? }
                                }
                            }?>
                        </table>
                    </div>
                <?endif?>
            </div>            
        </div>
    </div>
    <!-- окно предупреждения подписания по ЕСИА -- -->
    <noindex>
        <div id="send_sms" class="bgpopup" >
            <div class="container">
            <div class="row align-items-center justify-content-center">            
                <div class="col-sm-12 col-md-8 col-lg-6 col-xl-6">
                    <div class="regpopup_win">     
                    <div id="signpopup_close">Х</div>                                       
                            <!--форма подписания-->
                            <div class="regpopup_autorisation" id="regpopup_autarisation">
                                <?
                                    $APPLICATION->IncludeFile(SITE_TEMPLATE_PATH."/global_sign/attantion_sign.php", Array()); 
                                ?>
                                <? //путь для возврата на эту страницу
                                    $returnURL = base64_encode($_SERVER['REQUEST_URI']);
                                ?>
                                <a href="http://anypact.ru/profile/aut_esia.php?returnurl=<?=$returnURL?>" class="btn btn-nfk" id="ref_esia" style="width:45%;">Подписать</a>
                                <button class="btn btn-nfk" id="close_sign_popup" style="width:45%">Отклонить</button>
                            </div>                        
                            </div>
                        </div>
                    </div>
                </div>            
            </div>
        </div>
    </noindex>
    <!-- \\окно предупреждения подписания по ЕСИА -->   
<?break;
    // контракт подписан
    case 'Y':
?>
    <noindex>
        <div class="d-flex flex-column align-items-center text-center mt-5 pt-5 mb-5">
            <img src="<?=SITE_TEMPLATE_PATH?>/image/ok_send.png" alt="Необходима регистрация">
            <h3 class="text-uppercase font-weight-bold mt-3" style="max-width: 550px">Ваша подпись поставлена!</h3>
            <p>Сейчас автоматически откроется страница с вашими договорами.</p>
            <p>Если страница не открылась перейдите самостоятельно по ссылке <a href="/my_pacts/">/my_pacts/</a></p>      
        </div>
        <script>
           $(document).ready(function() {
                console.log('Редирект начало');
                setTimeout(function () {
                    replaceMypact();
                }, 7000);

                function replaceMypact(){
                    console.log('Редирект');
                    location.replace('/my_pacts/');
                }
            });
        </script>
    <noindex>    
<?break;
    // ошибка ид ЕСИА несовпадает с ИД в профиле
    case 'ERR_ID':
?>
    <noindex>
        <div class="d-flex flex-column align-items-center text-center mt-5 pt-5 mb-5">
            <img src="<?=SITE_TEMPLATE_PATH?>/image/err_send.png" alt="Необходима регистрация">
            <h3 class="text-uppercase font-weight-bold mt-3" style="max-width: 550px">Ошибка подписания!</h3>
            <p>Учетная запись на «Госуслугах» не совпадает с вашим профилем.</p>       
        </div>
    <noindex>
<? break; 
} 
?>
