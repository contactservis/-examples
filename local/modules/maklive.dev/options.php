<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
/*
// если администратор
if (($USER->IsAdmin() || in_array(11, $USER->GetUserGroupArray())) && CModule::IncludeModule('nanocad.dev')) {

    \Bitrix\Main\Loader::includeModule("iblock");

    $request = Context::getCurrent()->getRequest(); // формируем объект запроса

    // если переданы данные из формы
    if ($request->getRequestMethod() == 'POST' && !empty($request->getPost('apply')) && check_bitrix_sessid()) {

        foreach ($request->getPost('blockwebservice') as $inputName => $inputValue) {
            Option::set('nanocad.dev', 'WEBSERVICE_XML_RPC_' . strtoupper($inputName), $inputValue);
        }

        //PRICE_LIST
        //title rub
        Option::set("maklive.dev", "PRICE_RUB_TITLE", $request->getPost("pricelist_rub_title"));
        Option::set("nanocad.dev", "PRICE_KAZ_TITLE", $request->getPost("pricelist_kaz_title"));

        Option::set("nanocad.dev", "NANODEV_ROISTAT_API_USER_ID", $request->getPost("NANODEV_ROISTAT_API_USER_ID"));

        Option::set("nanocad.dev", "NANODEV_ROISTAT_API_SECRET", $request->getPost("NANODEV_ROISTAT_API_SECRET"));

        Option::set("nanocad.dev", "NANODEV_ROISTAT_API_KEY", $request->getPost("NANODEV_ROISTAT_API_KEY"));

        if (!empty($request->getPost("pricelist_rub_file"))) {
            //file rub
            if(is_array($request->getPost("pricelist_rub_file"))) {

                $arRubPrice = CIBlock::makeFileArray(
                    $request->getPost("pricelist_rub_file"),
                    $request->getPost("pricelist_rub_file_del") === "Y",
                    ""
                );

                $priceFileID = CFile::SaveFile($arRubPrice, "/pricelist/" . date("Y") . "/");

                Option::set("nanocad.dev", "PRICE_RUB_FILE", $priceFileID);

            }

            if ($request->getPost("pricelist_rub_file") > 0 && $request->getPost("pricelist_rub_file_del") === "Y") {

                CFile::Delete($request->getPost("pricelist_rub_file"));
                Option::set("nanocad.dev", "PRICE_RUB_FILE", false);

            }
        }

        if (!empty($request->getPost("pricelist_kaz_file"))) {
            //file rub
            if(is_array($request->getPost("pricelist_kaz_file"))) {

                $arKazPrice = CIBlock::makeFileArray(
                    $request->getPost("pricelist_kaz_file"),
                    $request->getPost("pricelist_kaz_file_del") === "Y",
                    ""
                );

                $priceFileID = CFile::SaveFile($arKazPrice, "/pricelist/" . date("Y") . "/");

                Option::set("nanocad.dev", "PRICE_KAZ_FILE", $priceFileID);

            }

            if ($request->getPost("pricelist_kaz_file") > 0 && $request->getPost("pricelist_kaz_file_del") === "Y") {

                CFile::Delete($request->getPost("pricelist_kaz_file"));
                Option::set("nanocad.dev", "PRICE_KAZ_FILE", false);

            }
        }

    }

?>
    <form method="post" name="webhooks" action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($mid) ?>&amp;lang=<? echo LANGUAGE_ID ?>">
        <?= bitrix_sessid_post(); ?>

        <?php
        // опции в табах
        $arTabOptions = array(
            'blockwebservice' => array(

                0 => array(
                    'TITLE' => 'Сервер',
                    'NAME' => 'server'
                ),

                1 => array(
                    'TITLE' => 'Порт',
                    'NAME' => 'port'
                ),

                2 => array(
                    'TITLE' => 'Путь к методам',
                    'NAME' => 'PATH'
                ),

                3 => array(
                    'TITLE' => 'Кодировка текущего сервера',
                    'TYPE' => 'SELECT',
                    'NAME' => 'client_encoding',
                    'OPTIONS' => array(
                        array(
                            'VALUE' => 'utf-8',
                            'NAME' => 'utf-8',
                        ),
                        array(
                            'VALUE' => 'cp1251',
                            'NAME' => 'cp1251',
                        ),
                    )
                ),

                4 => array(
                    'TITLE' => 'Кодировка внешнего сервера',
                    'TYPE' => 'SELECT',
                    'NAME' => 'server_encoding',
                    'OPTIONS' => array(
                        array(
                            'VALUE' => 'utf-8',
                            'NAME' => 'utf-8',
                        ),
                        array(
                            'VALUE' => 'cp1251',
                            'NAME' => 'cp1251',
                        ),
                    )
                ),
            )
        );

        // табы
        $arTabs = array(
            array(
                'DIV' => 'catalog',
                'TAB' => 'Настройка каталога и биллинга',
                'TITLE' => 'Настройка каталога и биллинга'
            ),
            array(
                'DIV' => 'price-list',
                'TAB' => 'Настройки прайс-листа',
                'TITLE' => 'Настройки прайс-листа'
            ),
            array(
                'DIV' => 'blockwebservice',
                'TAB' => 'Конфигурация нано веб-сервисов и блоков',
                'TITLE' => '[XML-RPC] Подключение к удалённому серверу'
            ),

            array(
                'DIV' => 'markerting',
                'TAB' => 'Маркетинг',
                'TITLE' => 'Настройки маркетинга'
            ),

        );

        // инициализируем вывод табов
        $tabControl = new CAdminTabControl('tabControl', $arTabs);

        $tabControl->Begin(); ?>

        <?php $tabControl->BeginNextTab(); ?>

        <tr class="heading">
            <td colspan="2">Настройка типов услуг</td>
        </tr>

        <?php $tabControl->BeginNextTab(); ?>

        <tr class="heading">
            <td colspan="2">Настройка прайс-листа в рублях</td>
        </tr>

        <tr>
            <td width="40%" align="right">Заголовок прайс-листа:</td>
            <td width="60%" align="left">

                <input style="width:80%" width="100%" type="text" name="pricelist_rub_title" value="<?php echo Option::get("nanocad.dev", "PRICE_RUB_TITLE"); ?>" />

            </td>
        </tr>

        <tr>
            <td width="40%" align="right">Файл прайс-листа:</td>
            <td width="60%" align="left">

                <?php

                $file_id = Option::get("nanocad.dev", "PRICE_RUB_FILE", false);

                echo \Bitrix\Main\UI\FileInput::createInstance(
                    array(
                        "name" => "pricelist_rub_file",
                        "id" => "pricelist_rub_title",
                        "description" => false,
                        "allowUpload" => "Y",
                        "allowUploadExt" => ["xls, xlsx"],
                        "maxCount" => 1,
                        "upload" => true,
                        "medialib" => false,
                        "fileDialog" => true,
                        "cloud" => false
                    )
                )->show($file_id, false);

                ?>

            </td>
        </tr>

        <tr class="heading">
            <td colspan="2">Настройка прайс-листа для Казахстана</td>
        </tr>

        <tr>
            <td width="40%" align="right">Заголовок прайс-листа для Казахстана:</td>
            <td width="60%" align="left">

                <input style="width:80%" width="100%" type="text" name="pricelist_kaz_title" value="<?php echo Option::get("nanocad.dev", "PRICE_KAZ_TITLE"); ?>" />

            </td>
        </tr>

        <tr>
            <td width="40%" align="right">Файл прайс-листа для Казахстана:</td>
            <td width="60%" align="left">

                <?php

                $file_id = Option::get("nanocad.dev", "PRICE_KAZ_FILE", false);

                echo \Bitrix\Main\UI\FileInput::createInstance(
                    array(
                        "name" => "pricelist_kaz_file",
                        "id" => "pricelist_kaz_title",
                        "description" => false,
                        "allowUpload" => "Y",
                        "allowUploadExt" => ["xls, xlsx"],
                        "maxCount" => 1,
                        "upload" => true,
                        "medialib" => false,
                        "fileDialog" => true,
                        "cloud" => false
                    )
                )->show($file_id, false);

                ?>

            </td>
        </tr>

        <?php $tabControl->BeginNextTab(); ?>

        <? foreach ($arTabOptions['blockwebservice'] as $arTabOption) : ?>
            <tr>

                <td valign="middle" width="40%" class="adm-detail-content-cell-l">
                    <?= $arTabOption['TITLE'] ?>
                </td>

                <td valign="middle" width="60%" class="adm-detail-content-cell-r">
                    <? if ($arTabOption['TYPE'] == 'SELECT') : ?>
                        <select name="blockwebservice[<?= $arTabOption['NAME'] ?>]" data-option-id="<?= 'BX_WEBHOOK_' . strtoupper($arTabOption['NAME']) ?>">
                            <? foreach ($arTabOption['OPTIONS'] as $option) : ?>
                                <option value="<?= $option['VALUE'] ?>" <? if (Option::get('nanocad.dev', 'WEBSERVICE_XML_RPC_' . strtoupper($arTabOption['NAME'])) == $option['VALUE']) : ?>selected="selected" <<? endif; ?>><?= $option['NAME'] ?></option>
                            <? endforeach; ?>
                        </select>
                    <? else : ?>
                        <input type="text" size="40" maxlength="255" value="<?= Option::get('nanocad.dev', 'WEBSERVICE_XML_RPC_' . strtoupper($arTabOption['NAME'])) ?>" name="blockwebservice[<?= $arTabOption['NAME'] ?>]" data-option-id="<?= 'WEBSERVICE_XML_RPC_' . strtoupper($arTabOption['NAME']) ?>">
                    <? endif; ?>
                </td>

            </tr>
        <? endforeach; ?>


        <?php $tabControl->BeginNextTab(); ?>
        <tr class="heading">
            <td colspan="2">Настройка Roistat</td>
        </tr>
        <tr>
            <td width="40%" align="right">ROISTAT API KEY:</td>
            <td width="60%" align="left">

                <input style="width:80%" width="100%" type="text" name="NANODEV_ROISTAT_API_KEY" value="<?php echo Option::get("nanocad.dev", "NANODEV_ROISTAT_API_KEY"); ?>" />

            </td>
        </tr>

        <tr>
            <td width="40%" align="right">ROISTAT API USER_ID:</td>
            <td width="60%" align="left">

                <input style="width:80%" width="100%" type="text" name="NANODEV_ROISTAT_API_USER_ID" value="<?php echo Option::get("nanocad.dev", "NANODEV_ROISTAT_API_USER_ID"); ?>" />

            </td>
        </tr>

        <tr>
            <td width="40%" align="right">ROISTAT API SECRET:</td>
            <td width="60%" align="left">

                <input style="width:80%" width="100%" type="text" name="NANODEV_ROISTAT_API_SECRET" value="<?php echo Option::get("nanocad.dev", "NANODEV_ROISTAT_API_SECRET"); ?>" />

            </td>
        </tr>

        <?php $tabControl->Buttons();

        bitrix_sessid_post(); ?>

        <input type="submit" name="apply" value="<?= GetMessage('MAIN_SAVE') ?>" title="<?= GetMessage('MAIN_OPT_SAVE_TITLE') ?>" class="adm-btn-save">

        <?php $tabControl->End(); ?>
    </form>
<? }
