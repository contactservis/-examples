<?
/**
 * Если запрос через Ajax то отдаем укороченный
 */
global $USER;
$USER->GetID();
$visualClass = [ 'like'=>'', 'dislike'=> 'active' ]
?>

<div class="article-header__controls animate-up animated">
  <div class="btn btn_like <?=$visualClass[$arResult["TypeLike"]]?>">Нравится
    <input type="hidden" class="UserID" value="<?=$USER->GetID()?>"/>
    <input type="hidden" class="ElementID" value="<?=$arResult["ID"]?>"/>
    <input type="hidden" class="RatingCount" value="<?=$arResult['vote_sum']?>"/>
  </div>
  <!--<div class="btn btn_icon btn_icon-comment"></div><div class="btn btn_icon btn_icon-share"></div><div class="btn btn_icon btn_icon-like"></div>-->
</div>
