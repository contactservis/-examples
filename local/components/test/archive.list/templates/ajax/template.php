<?php 
/* 
 Comment Text 
*/
$Years = $arResult["arSelect"];
$indexPage = (int) $arResult["IndexPage"];
?>

  <? foreach ($arResult["ArchiveItem"] as $Item){?>
    <?
      $CoverImg  = $Item["COVER_URL"];
      $pdfURL    = $Item["PDF_URL"];
      $NameMagazine = $Item["UF_NAME"];
      $NamberMagazine = $Item["UF_XML_ID"];  
    ?>
    <!-- Номер журнала -->
    <div class="col-12 col-sm-6 col-lg-4 archive-list__col">
      <div class="archive-item">
        <div class="archive-item__wrap">
          <div style="background-image: url('<?=$CoverImg?>')" class="archive-item__media">
            <img src="/local/templates/petrovich_news/img/archive-null.gif" alt="<?=$NameMagazine?>" class="archive-item__img"></div>
          <div class="archive-item__content">
            <div title="ИЮНЬ 2019 No 6 (177)" class="archive-item__title"><?=$NameMagazine?></div>
            <div class="archive-item__control">
              <div class="archive-item__control-item"><a href="/petrovich_news/?number=<?=$NamberMagazine?>" class="btn btn_def">Читать</a></div>
              <? if( !empty($pdfURL) ){ ?>
                <div class="archive-item__control-item"><a href="<?=$pdfURL?>" target="_blank" class="btn btn_grey">PDF-версия</a></div>
              <?}?>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?}?>
