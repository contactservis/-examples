<div class="tools_redactor">
    <!--<button type="button" class="btn btn-nfk btn-default" data-toggle="tooltip" data-placement="left" title="Сохранить" id="save_btn" data-id="<?=$arResult['ELEMENT_ID']?>">
        <span class="glyphicon glyphicon-floppy-disk"></span>
    </button>-->
    <button type="button" class="btn btn-nfk btn-default space_right" id="btn-edit" data-toggle="tooltip" data-placement="left" title="Включить редактирование текста" contenteditable="false">
        <span class="glyphicon glyphicon-pencil"></span>
    </button>
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
        <?=$arResult["CONTRACT_PROPERTY"]["CONTRACT"]["DETAIL_TEXT"]?>
    </div>
<?endif?>

<script>
    $(document).ready(function () {

        // разрешение редактирование курсора
        function on_contenteditable(element) {
            var element_atr = element.attr('contenteditable');
            console.log(element_atr);
            if (element_atr == 'true') {
                element.attr('contenteditable', false);
                return false;
            } else {
                element.attr('contenteditable', true);
                return true;
            }
        }

        // разрешение редактирование текста
        $('#btn-edit').on('click', function() {
            var canvas = $('#canvas');
            var onof = on_contenteditable(canvas);
            var span_icon = $(this).find('span');

            if (onof) {
                $(this).css("backgroundColor", "#ff6416");
                $(span_icon).css("color", "#fff");
            } else {
                $(this).css("backgroundColor", "#fff");
                $(span_icon).css("color", "#ff6416");
            }
        });

        $(document).on('click touchstart', '#save_btn', function() {
            let canvas_contr = $('.cardDogovor-boxViewText');
            let canvas_contr_context = String(canvas_contr.html());
            let id = $(this).attr('data-id');
            // загружаем содержимое категории
            $.post(
                "/response/ajax/up_contract_redaction_text.php", {
                    contect: canvas_contr_context,
                    id: id
                },
                onAjaxSuccess
            );

            function onAjaxSuccess(data) {
                console.log(data);
                // Здесь мы получаем данные, отправленные сервером и выводим их на экран.
                let result = JSON.parse(data);
                if(result['TYPE']=='ERROR'){
                    console.log(result['VALUE']);
                    alert(result['VALUE']);
                }
                if(result['TYPE']=='SUCCESS'){
                    console.log(result['VALUE']);
                    //alert(result['VALUE']);
                    window.location.href = "/my_pacts/";
                }

            }

        });

    });

</script>

