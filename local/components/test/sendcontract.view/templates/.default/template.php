<? // print_r($arResult["PROPERTY"]) ;?>
<? // print_r($arResult["ID_USER"]) ;?>
<?
switch ($arResult['SEND_CONTRACT']) {
// Процедура подписания
    case 'N': ?>
<!--------------------------------------1-------------------------------------------------->
<div>
        <h1 class="mb-4">Договор ожидающий моей подписи</h1>
        <div class="row pt-2 mb-5 pb-5">
            <div class="col-md-4 col-sm-12">
                <h3 class="font-weight-bold">Файлы</h3>
                <ul class="list-document">
                    <li class="icon-document">
                        <span>Договор №1</span>                        
                        <button class="btn btn-nfk" id="sign_contract"  style="width:100%">Подписать договор</button>
                        <a class="btn btn-nfk" href="/my_pacts/send_contract/edit/?ID=<?=$arResult["ID"]?>" style="width:100%; min-height: 47px; margin-top: 26px; padding: 10px;">Внести изменения</a>
                        <button class="btn btn-nfk" id="recall_sign" data="<?=$_GET['ID']?>" data-user="1" style="width:100%">Отклонить</button>
                    </li>
                </ul>
            </div>
            <div class="col-md-8 col-sm-12">
                <div class="d-flex">
                    <h3 class="font-weight-bold flex-grow-1" style="flex-grow: 1!important;">Просмотр файла</h3>
                    <a href="contract_pdf.php?ID=<?=$_GET['ID']?>" target="_blank" class="btn-img" id="download_pdf"><img src="<?=SITE_TEMPLATE_PATH?>/image/icon-pdf-gray.png" alt=""></a>
                    <button class="btn-img"><img src="<?=SITE_TEMPLATE_PATH?>/image/icon-printer-gray.png" alt=""></button>
                </div>
                <!--Поле просмотра договора-->
                <div class="w-100 mt-4" style="height: 1000px; background-color: #f1f4f4">
                    <div style="wight:100%" id="canvas_view_text">
                        <?if(!empty($arResult["CONTRACT_TEXT"]['IMG'])):?>
                            <?foreach ($arResult["CONTRACT_TEXT"]['IMG'] as $item):?>
                                <div class="document-img" style="text-align: center">
                                    <img src="<?=CFile::GetPath($item)?>">
                                </div>
                                <br>
                            <?endforeach?>
                        <?else:?>
                            <?=$arResult['CONTRACT_TEXT']['TEXT']?>
                        <?endif?>
                        <?=$arResult['SEND_BLOCK']['TEXT']?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!---------------------------------------------------------------------------------------->
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
                            <button class="btn btn-nfk" id="recall_sign" data="<?=$_GET['ID']?>" data-user="1" style="width:45%">Отклонить</button>
                        </div>                        
                        </div>
                    </div>
                </div>
            </div>            
        </div>
    </div>
</noindex>
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