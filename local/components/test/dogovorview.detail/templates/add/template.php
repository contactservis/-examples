<h1><?=$arResult["ELEMENT"]["NAME"]?></h1>
 <div class="tender cardDogovor" style="margin-bottom: 100px;">
    <div class="row">
        <?if(empty($arResult['ERROR'])):?>
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="cardDogovor-boxTool cardPact">
                    <h5>Создать договор из наших шаблонов</h5>
                    <p>В правой части выберите тему договора:</p>
                    <a href="/my_pacts/add_my_dogovor/?ELEMENT_ID=<?=$arResult['ELEMENT_ID']?>&EDIT=ADD" class="btn btn-nfk" id="template_con">
                        Загрузить договор из шаблона
                    </a>
                    <?if(!empty($arResult["TEMPLATE_CONTENT"])){ ?>
                        <div class="steps">
                            <?if(!empty($arResult["TEMPLATE_CONTENT_PROPERTY"])){?>
                            <div class="t" id="step0"><div class="number">1</div>
                                <div id="head_c">
                                    <select class="form-control form-control-lg" id="select_type_user">
                                        <?
                                            $type_user = explode(",", $arResult["TEMPLATE_CONTENT_PROPERTY"][0]);
                                        ?>
                                        <option value="seller">Я <?=$type_user[0]?></option>
                                        <option value="customer">Я <?=$type_user[1]?></option>
                                    </select>
                                </div>
                            </div>
                            <?
                                $arrCount = count($arResult["TEMPLATE_CONTENT_PROPERTY"]);?>
                                <?for($x=1; $x<$arrCount; $x++){?>
                                    <div class="t" id="step<?=$x?>"><div class="number"><?=$x+1?></div><?=$arResult["TEMPLATE_CONTENT_PROPERTY"][$x]?></div>
                                <?}?>
                            <?}?>
                        </div>
                    <?}?>
                    <h5>Загрузить договор из вашего файла</h5>
                    <p31>поддерживаемые форматы(docx, txt, png, jpg)</p31>
                    <form enctype="multipart/form-data" method="post" name="loadcontract">
                        <label for="uploadbtn" class="btn btn-nfk" id="">Выберите собственный файл</label>
                        <input id="uploadbtn" type="file" name="file[]"  accept="docx/*" capture="camera" multiple required value="Сделать фото" style="display:none;"/>
                        <label for="load-contract" class="btn btn-nfk" id="btn-load">Загрузить в редактор</label>
                        <input type="submit" value="Обработать" id="load-contract" style="display:none;"/>
                    </form>
                    <h5>Создать договор используя наш редактор</h5>
                    <p>Нажмите на кнопку "Создать договор в редакторе" и в поле справа наберайте текст используя инструменты для форматирования.</p>
                    <button class="btn btn-nfk" id="create_con">Создать договор в редакторе</button>
                </div>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-12">
                <div class="tools_redactor" <?if(empty($arResult['TEMPLATE_CONTENT'])):?>style="display: none"<?endif?>>
                    <button type="button" class="btn btn-nfk btn-default" data-toggle="tooltip" data-placement="left" title="Сохранить" id="save_btn" data-id="<?=$arResult['ELEMENT_ID']?>">
                        <span class="glyphicon glyphicon-floppy-disk"></span>
                    </button>
                    <!--<button type="button" class="btn btn-nfk btn-default" data-toggle="tooltip" data-placement="left" title="Сохранить"><span class="glyphicon glyphicon-print"></span></button>-->
                    <button type="button" class="btn btn-nfk btn-default space_right" id="btn-edit" data-toggle="tooltip" data-placement="left" title="Включить редактирование текста" contenteditable="false"><span class="glyphicon glyphicon-pencil"></span></button>
                    <?/*<button type="button" class="btn btn-nfk btn-default" data-toggle="tooltip" data-placement="left" title="Вставить изображение"><span class="glyphicon glyphicon-picture"></span></button>*/?>
                    <button type="button" class="btn btn-nfk btn-default form_text js-disabled"  id="btn-nedittext" data-toggle="tooltip" data-placement="left" title="Запретить редактирование выделенного текста" disabled><span class="glyphicon glyphicon-ban-circle"></span></button>
                    <button type="button" class="btn btn-nfk btn-default space_right js-btn-data js-disabled" data-toggle="tooltip" data-placement="left" title="Вставить подстановку текущей даты" disabled><span class="glyphicon glyphicon-calendar"></span></button>
                    <button type="button" class="btn btn-nfk btn-default form_text js-disabled" id="btn-bold" data-toggle="tooltip" data-placement="left" title="Жирный текст" contenteditable="false" disabled><span class="glyphicon glyphicon-bold"></span></button>
                    <button type="button" class="btn btn-nfk btn-default form_text js-disabled" id="btn-italic" data-toggle="tooltip" data-placement="left" title="Курсив" contenteditable="false" disabled><span class="glyphicon glyphicon-italic"></span></button>
                    <button type="button" class="btn btn-nfk btn-default form_text space_right js-disabled" id="btn-title" data-toggle="tooltip" data-placement="left" title="Заголовок" contenteditable="false" disabled><span class="glyphicon glyphicon-font"></span></button>
                    <button type="button" class="btn btn-nfk btn-default" id="btn-question" data-toggle="tooltip" data-placement="left" title="Информация по инструментам" contenteditable="false"><span class="glyphicon glyphicon-question-sign"></span></button>
                </div>
            <div class="cardDogovor-boxViewText" id="canvas" contenteditable="false">
                <?if(!empty($arResult["TEMPLATE_CONTENT"])){ ?>
                    <? echo $arResult["TEMPLATE_CONTENT"]["DETAIL_TEXT"] ;
                }else {?>
                    <h3>Вы можете загрузить договор из шаблона или из вашего файла</h3>
                        <?foreach ($arResult["THREE_TEMPLATE"] as $Item_three){?>
                            <?if($Item_three["DEPTH_LEVEL"]==1){?>
                                <span class="link_template" data-id="<?=$Item_three["ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/img/folder_contract.png" /><?=$Item_three["NAME"]?></span>
                            <?}elseif ($Item_three["DEPTH_LEVEL"]==2){?>
                                <span class="link_template" data-id="<?=$Item_three["ID"]?>" style="padding-left: 35px;"><img src="<?=SITE_TEMPLATE_PATH?>/img/folder_contract.png" /><?=$Item_three["NAME"]?></span>
                            <?}elseif ($Item_three["DEPTH_LEVEL"]==3){?>
                                <span class="link_template" data-id="<?=$Item_three["ID"]?>" style="padding-left: 70px;"><img src="<?=SITE_TEMPLATE_PATH?>/img/folder_contract.png" /><?=$Item_three["NAME"]?></span>
                            <?}?>
                        <?}?>
                    <?}?>
                </div>
            </div>
        <?else:?>
            <div><?=$arResult['ERROR']?></div>
        <?endif?>
    </div>
</div>
<script>
    var re_url = '<?=$_SERVER['REQUEST_URI']?>';
    <?/*var full_name = {
        name: '<?= $arResult['JS_DATA']['USER']['NAME']?>',
        surname: '<?=$arResult['JS_DATA']['USER']['SURNAME']?>',
        midlname:'<?=$arResult['JS_DATA']['USER']['MIDLENAME']?>',
        phone: '<?=$arResult['JS_DATA']['USER']['PHONE']?>',
        passport: '<?=$arResult['JS_DATA']['USER']['PASSPORT']?>'
    }*/?>
    var user_req =   <?=CUtil::PhpToJSObject($arResult['JS_DATA']['USER'])?>;
</script>