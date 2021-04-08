<div class="like-vote" id="<?= $voteContainerId ?>">
    <div class="like-vote__counter">
        <?= $arResult['vote_sum'] ? $arResult['vote_sum'] : '0' ?>
        <svg class="svg-icon svg-icon--heart like-vote__counter-icon">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-heart"></use>
        </svg>
    </div>
</div>