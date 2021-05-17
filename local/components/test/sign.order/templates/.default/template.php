<!-- <pre>
<? //print_r($arResult); ?>
</pre> -->

<div class="container" style="padding-top: 50px">
    <?//$arResult["HTML_TEMPLATE"]?>
    <iframe src="https://shop.nfksber.ru<?=$arResult["URL_PDF"]?>" height="500px" width="100%" id="pdf">
    </iframe>
    <input type="hidden" id="ID-Client" value="<?=base64_encode($arResult["PARAMS_ORDER"]["GLOBAL_CODE_USER"])?>">
    <input type="hidden" id="Hash-contract" value="<?=$arResult["PARAMS_ORDER"]["SELECTED_CONTRACT_HASH"]?>">
    <input type="hidden" id="Name-file" value="<?=$arResult["URL_NAME_PDF"]?>">    
    <div class="view-card_reception view-card_button">
        <div id="box-input-sms">
        <div class="profit_title">
            <div id="box-input-sms-mess">Для подписания введите код из SMS</div>
        </div>
            <input type="text" id="input-sms" class="content_input" size="8" maxlength="10" placeholder="SMS" style="width: 250px;float: left;height: 60px;">
            <div class="profit_sub"><span id="max-time">90</span></div>
        </div>
        <button class="button-new button-new_green-yellow metrika metrika_sh_kupit" style="width: 250px; margin-left:20px;display: inline-block;" id="button-sign-order">
            <span>Подписать</span>
        </button>        
        <!-- <button class="button-new button-new_green-yellow metrika metrika_sh_kupit" style="display:none; width: 250px; margin-left:20px;display: inline-block;" id="button-create-sign">
            <span>Подписать</span>
        </button> -->
    </div>
</div>