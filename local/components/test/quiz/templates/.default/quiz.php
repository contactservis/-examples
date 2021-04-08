<?php

/**
 * @var $arResult array
 * @global $USER CUser
 */

$arQuiz = $arResult['arQuiz'];
$arQuestions = $arResult['arQuestions'];

$request = \Bitrix\Main\Context::getCurrent()->getRequest();

if ($request->get('img')) {
    $treatmentImage = CFile::ResizeImageGet($request->get('img'), [
        'width' => 1200,
        'height' => 630
    ], BX_RESIZE_IMAGE_EXACT);

    \Utils\OpenGraphHelper::setImage( \Utils\Settings::SERVER_NAME . $treatmentImage['src'] );
}

$gameTime = count($arQuestions) * 2;

if ($arQuiz['ID'] == 12) {
    $gameTime = 4;
} else if ($arQuiz['ID'] == 6) {
    $gameTime = 5;
}

?>
<!--||-->
<div class="quiz-page" data-role="quiz" data-quiz-id="<?= $arQuiz['ID'] ?>" data-user-id="<?= $USER->GetID() ?>">
    <div class="quiz-page__header">
        <img src="<?= $arQuiz['UF_Q_PICTURE']['src'] ?>" data-role="quiz.image" alt="Викторина" class="quiz-page__image">
        <div class="quiz-page__header-inner">
            <? if( $arQuiz["UF_TYPE"] == "TEST" ) {?>
                <div class="quiz-page__pre-title">Тест</div>
            <? }else {?>
                <div class="quiz-page__pre-title">Викторина</div>
            <? } ?>
            <h1 class="quiz-page__title" data-role="quiz.title"><?= $arQuiz['UF_Q_NAME'] ?></h1>

            <?php if ($arQuiz['UF_Q_DESCRIPTION']) { ?>
                <div class="quiz-page__description" data-role="quiz.description"><?= $arQuiz['UF_Q_DESCRIPTION'] ?></div>
            <?php } ?>

            <? if( $arQuiz["UF_TYPE"] == "TEST" ) {
                    $TypeQuiz = "теста";
                }else {
                    $TypeQuiz = "викторины";
                } 
            ?>

            <?php if ($gameTime) { ?>
                <div class="quiz-page__game-time"
                     data-role="quiz.game-time">Прохождение <?=$TypeQuiz ?> займёт <?= \Sprint\Site\Utils\Tools::pluralForm(
                    $gameTime,
                    [ 'минуту', 'минуты', 'минут' ]
                ) ?></div>
            <?php } ?>

            <div class="quiz-page__result-block" data-role="quiz.result" style="display: none;"></div>

            <?php if ($arQuestions) { ?>
                <div class="quiz-page__questions-carousel-wrap" data-role="quiz.carousel" style="display: none;">
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="quiz-page__content">
            <? if( $arQuiz["UF_TYPE"] == "TEST" ) {?>
                <a href="#" class="quiz-page__content-button" data-role="quiz.run">Начать тест</a>
            <? }else {?>
                <a href="#" class="quiz-page__content-button" data-role="quiz.run">Начать викторину</a>
            <? } ?>

        <div class="quiz-page__question" data-role="quiz.question" style="display: none;"></div>

        <a href="#" class="quiz-page__content-button quiz-page__content-button--next" data-role="quiz.next-question" style="display: none;">Следующий вопрос</a>

        <a href="#" class="quiz-page__content-button quiz-page__content-button--finish" data-role="quiz.finish" style="display: none;">Узнать результат</a>

        <div class="quiz-page__repost" data-role="quiz.repost" style="display: none;">
            <span>Поделиться:</span>
            <div id="ya-repost" data-style="circle"></div>
        </div>

        <a href="#" class="quiz-page__content-button" style="display: none;" data-role="quiz.run-again">
            <svg class="svg-icon svg-icon--refresh">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-refresh"></use>
            </svg>
            Пройти ещё раз
        </a>
    </div>
</div>


<script type="text/template" data-role="quiz.template.qcarousel">
    <div class="quiz-page__questions-carousel owl-carousel">
        <% questions.forEach(function (item, index) { %>
            <a href="#" class="quiz-page__questions-carousel-item" data-role="quiz.carousel.item"><%= (index + 1) %></a>
        <% }); %>
    </div>
</script>

<? if( $arQuiz["UF_TYPE"] == "TEST" ) {?>
    <script type="text/template" data-role="quiz.template.result">
        <div class="quiz-page__result-block" data-role="quiz.result">
            <span class="quiz-page__result-diagnostic-msg"><%= title %></span>
            <span class="quiz-page__result-diagnostic-extramsg"><%= extraQuest %></span>
        </div>
    </script>
<? }else {?>
    <script type="text/template" data-role="quiz.template.result">
        <div class="quiz-page__result-block" data-role="quiz.result">
            <span class="quiz-page__result-diagnostic-msg"><%= title %></span>
            <span class="quiz-page__result-score">Ваш результат: <span class="quiz-page__result-score-value"><%= rightQuestionsNum %> / <%= questionsNum %></span></span>
        </div>
    </script>
<? } ?>
<script type="text/template" data-role="quiz.template.question">
    <div class="quiz-question">
        <div class="quiz-question__number">Вопрос <%= NUMBER %></div>
        <div class="quiz-question__title"><%= UF_QQ_NAME %></div>
        <div class="quiz-question__variants" data-role="quiz.question.variants">
        <% UF_QQ_VARIANTS.forEach(function (item, index) { %>
            <button class="quiz-question__variant"
                    data-role="quiz.question.variant"
                    data-variant-i="<%= index %>"><%= item.NAME %> 
                    <span style="display: none;"><%= item.TEXT %></span>
            </button>
        <% }); %>
        </div>
    </div>
</script>
