<? // print_r($arResult["PROPERTY"]) ;?>
<? //print_r($arResult) ;?>
<!--------------------------------------1-------------------------------------------------->
<div>
        <h3 class="mb-4" style="padding: 0 0 30px 0;font-weight: 600;">Договор, подписанный с моей стороны и ожидающие подписания контрагентом</h3>
        <div class="row pt-2 mb-5 pb-5">
            <div class="col-md-4 col-sm-12">
                <?if($arResult['DATA_DOGOVOR']['UF_ID_USER_A']==$USER->GetID()):?>
                    <div>
                        <?=$arResult['SEND_BLOCK']['ID']?>
                    </div>
                <?endif?>
                
                <h3 class="font-weight-bold"></h3>
                <ul class="list-document">
                    <button class="btn btn-nfk-invert w-100" id="recall_sign" data="<?=$_GET['ID']?>">Отозвать подпись</button>
                    <!--
                    <li class="icon-document">
                         <span>Договор №1</span>
                        
                    </li>                    
                    <li class="icon-document">
                        <span>Спецификация №1</span>
                        <button class="btn btn-nfk-invert w-100">Подписан</button>
                    </li>
                    <li class="icon-document">
                        <span>Спецификация №2</span>
                        <button class="btn btn-nfk-invert w-100">Подписан</button>
                    </li>
                    <li class="icon-document">
                        <span>Доп. соглашение</span>
                        <button class="btn btn-nfk-invert w-100">Подписан</button>
                    </li> -->
                </ul>
                
            </div>
            <div class="col-md-8 col-sm-12">
                <div class="d-flex">
                    <h3 class="font-weight-bold flex-grow-1" style="flex-grow: 1!important;">Просмотр договора:</h3>

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
                        <!--форма подписания-->
                        <div class="regpopup_autorisation" id="regpopup_autarisation">
                            <label for="smscode">
                                <span>Вам отправлен sms-код</span>
                                <img src="https://shop.nfksber.ru/local/templates/main/images/card/clock.png" style = "width: 18px; margin: 0 5px 0 10px;" />
                                <span id="timer" class=""><span id="timer_n" id-con="<?=$arResult['DOGOVOR_ID']?>" id-cont="<?=$arResult['USER_ID']?>">80</span> сек.</span>
                            </label>                            
                            <input class="regpopup_content_form_submit" id="smscode" name="logout_butt" value="" maxlength="6">
                        </div>                        
                        </div>
                    </div>
                </div>
            </div>            
        </div>
    </div>
</noindex>