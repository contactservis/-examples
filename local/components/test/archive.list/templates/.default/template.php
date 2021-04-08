<?php 
/* 
 Comment Text 
*/
$Years = $arResult["arSelect"];
$indexPage = (int) $arResult["IndexPage"];
$SiteRoot = '/petrovich_news';
?>
<!-- <pre>
  <? //print_r($arResult["ArchiveItem"]);?>
</pre> -->
<script>
  var arYear = new Array();
  <? foreach ($Years as $Year => $value){?>
    arYear[<?=$Year?>] = [<? foreach($value as $namber) {?>'<?=$namber?>',<?}?>]
  <?}?>
  var NamMagazine = new Array();
  <? foreach ($Years as $Year => $value){?> 
    NamMagazine[<?=$Year?>] = [
      <? foreach($value as $namber) {?>
        {
            'name': '<?=$namber?>',
            'value': '<?=$namber?>'
          },
      <?}?>
    ]
  <?}?>

</script>
<div class="page">
          <div class="page__header">
            <h1 class="page__title">Архив</h1>
            <form data-role="form-sort" class="archive-sort">
              <div class="archive-sort__wrap">
                <div class="row">
                  <div class="col-12 col-md-4 archive-sort__col">
                    <select id="yearMagazine" name="year" tabindex="1" class="select-box">
                      <option value="">Год</option>
                      <? foreach ($Years as $Year => $value){?>
                      <option value="<?=$Year?>"><?=$Year?></option>
                      <?}?>
                    </select>
                  </div>
                  <div class="col-12 col-md-4 archive-sort__col">
                    <select id="number" name="number" tabindex="1" class="select-box selecter_custom scroll-select">
                      <option value="">Выпуск</option>
                      <? foreach ($Years as $Year => $value){
                         foreach($value as $namber) {?>
                            <option value="<?=$namber?>"><?=$namber?></option>
                          <?}
                        }?>
                      
                    </select></div>
                  <div class="col-12 col-md-4 archive-sort__col"><button type="button" class="btn btn_def archive-sort__btn">Искать</button></div>
                </div>
              </div>
            </form>
          </div>
          <div class="page__content">
            <div class="archive-list">
              <div class="archive-list__wrap">
                <div class="row" id="list-item_magazine">
                  <? foreach ($arResult["ArchiveItem"] as $Item){?>
                    <?
                     $CoverImg  = $Item["COVER_URL"];
                     $pdfURL    = $Item["PDF_URL"];
                     $NameMagazine = $Item["UF_NAME"];
                     $NamberMagazine = $Item["UF_XML_ID"];
                     $ActiveItems =  $Item["UF_ACTIVE_ITEMS"]; 
                    ?>
                    <!-- Номер журнала -->
                    <div class="col-12 col-sm-6 col-lg-4 archive-list__col">
                      <div class="archive-item">
                        <div class="archive-item__wrap">
                          <div style="background-image: url('<?=$CoverImg?>')" class="archive-item__media">
                            <img src="<?=SITE_TEMPLATE_PATH?>/img/archive-null.gif" alt="<?=$NameMagazine?>" class="archive-item__img"></div>
                          <div class="archive-item__content">
                            <div title="ИЮНЬ 2019 No 6 (177)" class="archive-item__title"><?=$NameMagazine?></div>
                            <div class="archive-item__control">
                              <div class="archive-item__control-item">
                                <a href="<?=$SiteRoot?>/?number=<?=$NamberMagazine?>" class="btn btn_def <?if(!$ActiveItems) echo "disabled";?>">Читать</a>
                              </div>
                              <? if( !empty($pdfURL) ){ ?>
                                <div class="archive-item__control-item"><a href="<?=$pdfURL?>" target="_blank" class="btn btn_grey">PDF-версия</a></div>
                              <?}?>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?}?>
                </div>
              </div>
              <? if($arResult["CountPage"]) {?>
              <div class="pager pager_padding pager_right">
                <div class="pager__wrap pager__wrap_scroll">
                    <ul class="pager__list">
                      <? 
                      $i = 1;
                      if($indexPage === 0){
                        $indexPage = 1;
                      }
                      while( $i <= $arResult["CountPage"]) {?>
                      <li class="pager__list-item <?if($indexPage === $i) echo 'is-active';?>" data="<?=$indexPage?>">
                        <a href="<?=$SiteRoot?>/archive/all-page<?=$i?>/" class="pager__link"><?=$i?></a>
                      </li>
                      <?
                      $i++;
                      }?>
                      <? if( $indexPage < $arResult["CountPage"] ){ ?>
                        <li class="pager__list-item">
                          <a href="<?=$SiteRoot?>/archive/all-page<?=$indexPage+1?>/" class="pager__link">Вперед</a>
                        </li>
                      <? } ?>
                    </ul>
                </div>
              </div>
              <?}?>
            </div>
          </div>
        </div>