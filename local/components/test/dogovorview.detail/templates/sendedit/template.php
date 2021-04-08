<?
switch ($arResult['SEND_CONTRACT']) {
// Процедура подписания
case 'N': ?>
<h1><?=$arResult["ELEMENT"]["NAME"]?></h1>
 <div class="tender cardDogovor" style="margin-bottom: 100px;">
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="cardDogovor-boxTool cardPact" style="margin-top: 8px;">
				<button class="btn btn-nfk <?if(!empty($arResult["DOGOVOR_IMG"][0]['URL'])):?>canvas-img<?endif?>" id="popup_send_contract" data="signed">
                	Подписать договор
                </button>
                <h5>Вставить в договор:</h5>
            </div>
        </div>               
        <div class="col-lg-9 col-md-9 col-sm-12">
        <div class="tools_redactor">                
            <button type="button" class="btn btn-nfk btn-default space_right js-disabled" id="btn-edit" data-toggle="tooltip" data-placement="left" title="Включить редактирование текста" contenteditable="false"><span class="glyphicon glyphicon-pencil"></span></button>
            <button type="button" class="btn btn-nfk btn-default form_text js-disabled"  id="btn-nedittext" data-toggle="tooltip" data-placement="left" title="Запретить редактирование выделенного текста" disabled><span class="glyphicon glyphicon-ban-circle"></span></button>
            <button type="button" class="btn btn-nfk btn-default form_text js-disabled" id="btn-bold" data-toggle="tooltip" data-placement="left" title="Жирный текст" contenteditable="false" disabled><span class="glyphicon glyphicon-bold"></span></button>
            <button type="button" class="btn btn-nfk btn-default form_text js-disabled" id="btn-italic" data-toggle="tooltip" data-placement="left" title="Курсив" contenteditable="false" disabled><span class="glyphicon glyphicon-italic"></span></button>
            <button type="button" class="btn btn-nfk btn-default form_text space_right js-disabled" id="btn-title" data-toggle="tooltip" data-placement="left" title="Заголовок" contenteditable="false" disabled><span class="glyphicon glyphicon-font"></span></button>
            <button type="button" class="btn btn-nfk btn-default" id="btn-question" data-toggle="tooltip" data-placement="left" title="Информация по инструментам" contenteditable="false"><span class="glyphicon glyphicon-question-sign"></span></button>
        </div>
            <?if(!empty($arResult["DOGOVOR_IMG"][0]['URL'])):?>
                <div class="cardDogovor-boxViewText">
                    <?foreach ($arResult["DOGOVOR_IMG"] as $item):?>
                        <div class="document-img" style="text-align: center">
                            <img src="<?=$item['URL']?>">
                        </div>
                        <br>
                    <?endforeach?>
                </div>
            <?else:?>
                <div class="cardDogovor-boxViewText" id="canvas" contenteditable="false">
                    <? echo $arResult["TEMPLATE_CONTENT"]["DETAIL_TEXT"] ;?>
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
							<!-- <a href="http://anypact.ru/profile/aut_esia.php" class="btn btn-nfk" id="ref_esia" style="width:45%;margin-right: 30px;">Подписать</a>-->
                            <?
                            $DATA_SER = $arResult["CONTRACT_PROPERTY"]["CONTRACT_PROPERTY"]["USER_A"]["VALUE"].','.$arResult["TEMPLATE_CONTENT"]["ID"].','.$arResult["USER_ID"];
                            $DATA_SER = base64_encode($DATA_SER);
                            ?>
                            <? //путь для возврата на эту страницу 
                                $returnURL = base64_encode($_SERVER['REQUEST_URI']);
                            ?>
							<button class="btn btn-nfk" id="sign_edit_contract" style="width:45%;margin-right: 30px;" data-returnurl=<?=$returnURL?> data="<?=$DATA_SER?>">Подписать</button>
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