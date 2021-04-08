
<?
$PropertyQuiz = $arResult["quiz"];
?>

<div class="main" style="min-height: calc(78vh - 18px);">
    <div class="quiz-container-bg">
        <script src="https://yastatic.net/share2/share.js"></script>
        <div class="quiz-page" data-role="quiz" data-quiz-id="14" data-user-id="1">
            <div class="quiz-page__header">
                <img class="quiz-page__image" src="<?=$PropertyQuiz["UF_Q_PICTURE"]["src"]?>" data-role="quiz.image" alt="Викторина">
                <div class="quiz-page__header-inner">
                    <div class="konkurs-slide__type">Викторина</div>
                    <h1 class="quiz-page__title" data-role="quiz.title"><?=$PropertyQuiz["UF_Q_NAME"]?></h1>
                    <div class="quiz-page__description" data-role="quiz.description"><?=$PropertyQuiz["UF_Q_DESCRIPTION"]?></div>
                </div>
            </div>
            <div class="quiz-page__content">
                <div class="quiz-page__result-block" data-role="quiz.result" style="display: none;"></div>
                <script type="text/template" data-role="quiz.template.result">
                    <div class="quiz-page__result-block" data-role="quiz.result">
                    <span class="quiz-page__result-diagnostic-msg"><%= title %></span>
                    <span class="quiz-page__result-score"> <span class="quiz-page__result-score-value">Правильных ответов <%= rightQuestionsNum %> из <%= questionsNum %></span></span>
                    <p class="result-text">  <%= extraQuest %> </p>
                    <img class="fin_image" src='<%= fin_image %>' alt="финальная картинка" />
                    </div>
                </script>
                <a href="#" class="konkurs-slide__btn-go quiz-page__content-button--start" data-role="quiz.run">Начать</a>
                <div class="quiz-page__question" data-role="quiz.question" style="display: none;"></div>
                <a href="#" class="quiz-page__content-button quiz-page__content-button--next" data-role="quiz.next-question" style="display: none;">Следующий вопрос</a>
                <a href="#" class="quiz-page__content-button quiz-page__content-button--finish" data-role="quiz.finish" style="display: none;">Узнать результат</a>
                <div class="quiz-page__repost" data-role="quiz.repost" style="display: none;">
                    <span>Поделиться результатом:</span>
                    <div class="result-shar">
                        <div class="ya-share2" data-curtain data-size="l" data-services="vkontakte,facebook,odnoklassniki,twitter"></div>
                    </div>
                    <div id="ya-repost" data-style="circle"></div>
                </div>
                <a href="#" class="quiz-page__content-button" style="display: none;" data-role="quiz.run-again">
                    <svg class="svg-icon svg-icon--refresh">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-refresh"></use>
                    </svg>Пройти ещё раз</a>
                    <a href="/quiz_lists/" class="quiz-page__content-button" style="display: none; margin-top: 30px;" data-role="quiz.link_list">
                        <svg class="svg-icon svg-icon--refresh">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-refresh"></use>
                        </svg>Список всех викторин
                    </a>
            </div>
        </div>
        <script type="text/template" data-role="quiz.template.question">
            <div class="quiz-question"> 
            <div class="quiz-question__number">Вопрос <%= NUMBER %> из <%= ALLCOUNT %></div>
            <img alt="Картинка вопроса" class="quiz-page__image2" src='<%= UF_QQ_PICTURE_CONTENT.src %>' />
            <div class="quiz-question__title"><%= UF_QQ_NAME %></div>
            <div class="quiz-question__variants" data-role="quiz.question.variants">
            <% UF_QQ_VARIANTS.forEach(function (item, index) { %>
            <button class="quiz-question__variant" data-role="quiz.question.variant" data-variant-i="<%= index %>"><%= item.NAME %> 
            <span style="display: none;"><%= item.TEXT %></span>
            </button>
            <% }); %>
            </div>
            </div>
        </script>
    </div>
</div>
<!-- <script src="/static/js/main0ydejv.js"></script> -->