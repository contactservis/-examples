<? // print_r($arResult["PROPERTY"]) ;?>
<? //print_r($arResult) ;?>
<!--------------------------------------1-------------------------------------------------->
<div>
        <h1 class="mb-4">Подписанные договора</h1>
        <div class="row pt-2 mb-5 pb-5">
            <div class="col-md-4 col-sm-12">
                <h3 class="font-weight-bold">Файлы</h3>
                <ul class="list-document">
                    <li class="icon-document">
                        <span>Договор №1</span>                        
                    </li>
                    <!--
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