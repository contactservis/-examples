<?php

/**
 * @var $arResult array
 */

if (!$arResult['ITEMS']) {
    return;
}

?>

<div class="main__content">
    <div class="quiz-list-page">
        <div class="quiz-list-page__title">Викторины</div>
        <div class="quiz-list-page__list">
            <?php foreach ($arResult['ITEMS'] as $arQuiz) { ?>
                <div class="quiz-list-page__list-item">
                    <a href="/quiz/<?= $arQuiz['UF_Q_CODE'] ?>/" class="quiz-preview">
                        <div class="quiz-preview__image">
                            <img src="<?= $arQuiz['UF_Q_PICTURE']['src'] ?>" alt="">
                        </div>
                        <div class="quiz-preview__title"><?= $arQuiz['UF_Q_NAME'] ?></div>
                        <?php if ($arQuiz['UF_Q_DESCRIPTION']) { ?>
                            <div class="quiz-preview__description"><?= $arQuiz['UF_Q_DESCRIPTION'] ?></div>
                        <?php } ?>
                    </a>
                </div>
            <?php } ?>
        </div>
        <div class="quiz-list-page__pagination">
            <?php
            $APPLICATION->IncludeComponent(
                "bitrix:main.pagenavigation",
                "",
                array(
                    "NAV_OBJECT" => $arResult['NAV_OBJECT'],
                    "SEF_MODE" => "N",
                ),
                false
            );
            ?>
        </div>
    </div>
</div>