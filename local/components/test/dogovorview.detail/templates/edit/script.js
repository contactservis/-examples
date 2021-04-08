// разрешение редактирование курсора
function on_contenteditable(element) {
    var element_atr = element.attr('contenteditable');
    if (element_atr == 'true') {
        element.attr('contenteditable', false);
        return false;
    } else {
        element.attr('contenteditable', true);
        return true;
    }
}

// вставка текста на место курсора
function insertTextAtCursorRedact(text) {
    let selection = window.getSelection();
    let range = selection.getRangeAt(0);
    range.deleteContents();
    let node = document.createElement('nedittext');
    node.innerHTML = text;
    range.insertNode(node);

    selection.modify("move", "right", "character");
    /*for (let position = 0; position != text.length; position++) {
        selection.modify("move", "right", "character");
    };*/
}
function insertTextAtCursor(text) {
    let selection = window.getSelection();
    let range = selection.getRangeAt(0);
    range.deleteContents();
    let node = document.createElement('nedittext');
    node.setAttribute('contenteditable', false);
    node.innerHTML = text;
    range.insertNode(node);

    selection.modify("move", "right", "character");
    /*for (let position = 0; position != text.length; position++) {
        selection.modify("move", "right", "character");
    };*/
}

/*function insertBoldAtCursor(text) {
    let selection = window.getSelection();
    let range = selection.getRangeAt(0);
    let string = selection.toString();
    range.deleteContents();

    let insert_space = document.createElement('b');
    //insert_space.className = "alert alert-success";
    insert_space.innerHTML = string;
    range.insertNode(insert_space);

    for (let position = 0; position != text.length; position++) {
        selection.modify("move", "right", "character");
    };
}*/

function rangeCompareNode(range, node) {
    var nodeRange = node.ownerDocument.createRange();
    try {
        nodeRange.selectNode(node);
    }
    catch (e) {
        nodeRange.selectNodeContents(node);
    }
    var nodeIsBefore = range.compareBoundaryPoints(Range.START_TO_START, nodeRange) == 1;
    var nodeIsAfter = range.compareBoundaryPoints(Range.END_TO_END, nodeRange) == -1;

    if (nodeIsBefore && !nodeIsAfter)
        return 0;
    if (!nodeIsBefore && nodeIsAfter)
        return 1;
    if (nodeIsBefore && nodeIsAfter)
        return 2;

    return 3;
}

function formatSelectText(id_name) {
    // получаем выделенный текст
    let selection = window.getSelection();
    let range = selection.getRangeAt(0);
    let sel_string = selection.toString();
    let sel_html = range.cloneContents();
    // на основе id выбираем подстановку    
    let key = id_name.replace('btn-', '');
    let arrTegs = {
        bold: 'b',
        italic: 'i',
        nedittext: 'nedittext'
    }

    if(sel_string.length == 0) return;

    if(key == 'nedittext'){

       //проверка находимся ли мы в диапазоне запрета редактирования
        let parentStartN = $(range.startContainer.parentElement).parents('nedittext').eq(0);
        let parentEndN = $(range.endContainer.parentElement).parents('nedittext').eq(0);

        if(
            range.startContainer.parentElement.tagName != key.toUpperCase() &&
            range.endContainer.parentElement.tagName != key.toUpperCase() &&
            (parentStartN.length!==1 && parentEndN.length!==1)
        ){
            let nedittext = document.createElement('nedittext'),
                wrap_sel = document.createElement('text');


            //если количестов 1 и равняеться nededitex
            //отмена запрета редактирование для всего
            if(
                sel_html.children.length==1 &&
                sel_html.children[0].tagName=='NEDITTEXT' &&
                $(sel_html.children[0]).text() == sel_string
            ){
                console.log('удаление всего');
                sel_html = sel_html.querySelector('nedittext');
                sel_html = sel_html.innerHTML;
                range.deleteContents();
                let insert_space = document.createElement('text');
                insert_space.innerHTML = sel_html;
                range.insertNode(insert_space);
            }
            else{
                console.log('добавление');
                //добавление запрета редактирования
                //удаление внутрених nedittext
                let arNedittext = sel_html.querySelectorAll('nedittext');
                for(let i=0; i<arNedittext.length; i++){
                    if(arNedittext[i].innerHTML.length>0){
                        let sp1 = document.createElement("text");
                        sp1.innerHTML = arNedittext[i].innerHTML;
                        $(arNedittext[i]).replaceWith(sp1);
                    }
                }

                if(range.startContainer.parentNode.innerText == range.endContainer.parentNode.innerText && range.startContainer.parentNode.innerText == sel_string){
                    //если какой либо селектор совподает с выделением
                    console.log('test');
                    //$(nedittext).append(range.startContainer.parentNode);
                    $(range.startContainer.parentNode).wrapInner(nedittext);
                    range.insertNode(range.startContainer.parentNode);
                }
                else{
                    //для запрета редактирования внутри выделеного жирного или курсива
                    console.log('test2');
                    if(
                        range.startContainer.parentNode.tagName == range.endContainer.parentNode.tagName &&
                        (range.startContainer.parentNode.tagName == 'B' || range.startContainer.parentNode.tagName == 'I')
                    ){
                        let wrap_text_format = document.createElement(range.startContainer.parentNode.tagName);
                        sel_html = $(wrap_text_format).append(sel_html);
                    }
                    $(nedittext).append(sel_html);

                    //заглушка
                    //покачто не коректно добавлнеи с блочнми элементами
                    let bung = false;
                    $(nedittext).children().each(function(i, element){
                        if(element.tagName == 'P' || element.tagName == 'DIV'){
                            bung = true;
                            return false;
                        }
                    });

                    if(!bung){
                        $(wrap_sel).append(nedittext);
                        range.deleteContents();
                        range.insertNode(wrap_sel);

                        //удаление временного селектора text
                        $('.cardDogovor-boxViewText text').replaceWith(function(){
                            return $(this).html();
                        });
                        //document.execCommand('insertHTML', null, $(wrap_sel).html());
                    }
                    else{
                        showResult('#popup-error','Запрет редактирование отменен. Запрет редактирования возможен тоько в пределах абзаца');
                    }
                }
            }
        }
        else if(range.startContainer.parentNode.innerText == sel_string){
            console.log('удаление2');
        }
        else {
            console.log('удаление части');
            let arrayText = range.startContainer.parentNode.innerText.split(sel_string);
            if(range.startContainer.parentNode.tagName == key.toUpperCase()){
                //если родительский елемент nedittext
                $(range.startContainer.parentNode).remove();
                if (range.startContainer.parentNode.innerText != sel_string) {
                    let text = document.createElement(key);
                    if (text && arrayText[1] != undefined) {
                        text.innerHTML = arrayText[1];
                        range.insertNode(text);
                    }
                    if (sel_string && sel_string != undefined) range.insertNode(document.createTextNode(sel_string));
                    let text2 = document.createElement(key);
                    if (text2 && arrayText[0] != undefined) {
                        text2.innerHTML = arrayText[0];
                        range.insertNode(text2);
                    }
                }
            }
            else{
                //если родительский елемент не nedittext
                let text = document.createElement('text'),
                    textWrap = document.createElement('text');

                //1й этап добавляем text для диапазона
                $(text).append(sel_html);
                range.deleteContents();
                range.insertNode(text);

                //2-й этап парсим html и добавляем nedittext
                let delimetr = text;
                text = $(parentStartN).html();
                let arrayText = text.split(delimetr.outerHTML);
                for(let i=0; i<arrayText.length; i++){
                    arrayText[i] = '<nedittext>'+arrayText[i]+'</nedittext>';
                }
                text = arrayText[0] + delimetr.innerHTML + arrayText[1];
                $(textWrap).html(text);
                $(parentStartN).remove();
                range.insertNode(textWrap);
            }
        }
        //добавление атрибута запрета редактирования
        $('nedittext').attr('contenteditable', false);
        //удаление временного селектора text
        $('.cardDogovor-boxViewText text').replaceWith(function(){
            return $(this).html();
        });
        //премешение курсора направо
        selection.modify("move", "right", "character");
    }else{
        formatDoc(key);
    }
}

function formatSelectTitle(id_name) {
    // получаем выделенный текст
    let selection = window.getSelection();
    let range = selection.getRangeAt(0);
    let sel_string = selection.toString();
    // на основе id выбираем подстановку
    let key = id_name.replace('btn-', '');
    let arrTegs = {
        title: 'h4',
    }
    if(range.startContainer.parentElement.tagName != arrTegs[key].toUpperCase()){
        // удаляем его, что бы замнить
        range.deleteContents();
        let insert_space = document.createElement(arrTegs[key]);
        insert_space.setAttribute('class', 'subtitle_contract');
        insert_space.setAttribute('style', 'font-family: Roboto, sans-serif;');
        insert_space.innerHTML = sel_string;
        range.insertNode(insert_space);
    }else{
        let text = range.startContainer.parentNode.innerText;
        $(range.startContainer.parentNode).remove();
        range.insertNode(document.createTextNode(text));
    }

    selection.modify("move", "right", "character");
}

function loadTextCanvas(text, canvas_name) {
    let canvas = document.getElementById(canvas_name);
    canvas.innerHTML = text;

}

// подстановка текста из всплывающего инпута
function loadTextBox(el) {
    let variable = $(el);
    let parent_var = variable.parent('edbox');
    let text_box = parent_var.children('rededittext')
    let content_text_box = text_box.text();
    let context_input = variable.val();
    if (context_input == '') {
        context_input = content_text_box;
    }
    text_box.text(context_input);
    variable.remove();
}

// автоподстановка реквизитов пользователя
function setHeaderFullName(idname) {
    let seller =
        {
            NAME : document.getElementsByClassName('fullnameseller'),
            PHONE : document.getElementsByClassName('seller_PHONE'),
            PASSPORT : document.getElementsByClassName('seller_PASSPORT'),
            DIRECTOR_ID : document.getElementsByClassName('seller_DIRECTOR_ID'),
            INN : document.getElementsByClassName('seller_INN'),
            KPP : document.getElementsByClassName('seller_KPP'),
            OGRN : document.getElementsByClassName('seller_OGRN'),
            ADRESS : document.getElementsByClassName('seller_ADRESS'),
            INDEX : document.getElementsByClassName('seller_INDEX'),
            REGION : document.getElementsByClassName('seller_REGION'),
            CITY : document.getElementsByClassName('seller_CITY'),
            DISTRICT : document.getElementsByClassName('seller_DISTRICT'),
            LOCALITY : document.getElementsByClassName('seller_LOCALITY'),
            HOUSE : document.getElementsByClassName('seller_HOUSE'),
            OFFICE : document.getElementsByClassName('seller_OFFICE'),
            BANK :  document.getElementsByClassName('seller_BANK'),
            BIK : document.getElementsByClassName('seller_BIK'),
            RAS_ACCOUNT : document.getElementsByClassName('seller_RAS_ACCOUNT'),
            KOR_ACCOUNT :  document.getElementsByClassName('seller_KOR_ACCOUNT'),
            INN_BANK : document.getElementsByClassName('seller_INN_BANK'),
            DIRECTOR_NAME : document.getElementsByClassName('seller_DIRECTOR_NAME'),
            STAFF : document.getElementsByClassName('seller_STAFF')
        };

    let customer =
        {
            NAME : document.getElementsByClassName('fullnamecustomer'),
            PHONE : document.getElementsByClassName('customer_PHONE'),
            PASSPORT : document.getElementsByClassName('customer_PASSPORT'),
            DIRECTOR_ID : document.getElementsByClassName('customer_DIRECTOR_ID'),
            INN : document.getElementsByClassName('customer_INN'),
            KPP : document.getElementsByClassName('customer_KPP'),
            OGRN : document.getElementsByClassName('customer_OGRN'),
            ADRESS : document.getElementsByClassName('customer_ADRESS'),
            INDEX : document.getElementsByClassName('customer_INDEX'),
            REGION : document.getElementsByClassName('customer_REGION'),
            CITY : document.getElementsByClassName('customer_CITY'),
            DISTRICT : document.getElementsByClassName('customer_DISTRICT'),
            LOCALITY : document.getElementsByClassName('customer_LOCALITY'),
            HOUSE : document.getElementsByClassName('customer_HOUSE'),
            OFFICE : document.getElementsByClassName('customer_OFFICE'),
            BANK :  document.getElementsByClassName('customer_BANK'),
            BIK : document.getElementsByClassName('customer_BIK'),
            RAS_ACCOUNT : document.getElementsByClassName('customer_RAS_ACCOUNT'),
            KOR_ACCOUNT :  document.getElementsByClassName('customer_KOR_ACCOUNT'),
            INN_BANK : document.getElementsByClassName('customer_INN_BANK'),
            DIRECTOR_NAME : document.getElementsByClassName('customer_DIRECTOR_NAME'),
            STAFF : document.getElementsByClassName('customer_STAFF')
        };


    //let type_user = document.getElementById('step0_text');
    switch (idname) {
        case 'seller':
            $.each(seller, function(code, arObj){
                if(seller[code].length>0 && user_req[code]) {
                    for (var i = 0; i < seller[code].length; i++) {
                        seller[code][i].innerText = user_req[code].VALUE;
                    }
                }
                if(customer[code].length>0 && user_req[code]){
                    for (var i = 0; i < customer[code].length; i++) {
                        customer[code][i].innerText = '['+ user_req[code].NAME +' Покупателя]'
                    }
                }
            });
            break;
        case 'customer':
            $.each(customer, function(code, arObj){
                if(seller[code].length>0 && user_req[code]) {
                    for (var i = 0; i < seller[code].length; i++) {
                        seller[code][i].innerText = '[' + user_req[code].NAME + ' Продавца]';

                    }
                }
                if(customer[code].length>0 && user_req[code]) {
                    for (var i = 0; i < customer[code].length; i++) {
                        customer[code][i].innerText = user_req[code].VALUE;

                    }
                }
            });
            break;
        default:
            $.each(seller, function(code, arObj){
                if(seller[code].length>0 && user_req[code]) {
                    for (var i = 0; i < seller[code].length; i++) {
                        seller[code][i].innerText = user_req[code].VALUE;
                    }
                }
                if(customer[code].length>0 && user_req[code]) {
                    for (var i = 0; i < customer[code].length; i++) {
                        customer[code][i].innerText = '[' + user_req[code].NAME + ' Покупателя]'
                    }
                }

            });
            break;
    }

}

function setBackgraundStepBox() {
    let box = document.getElementsByClassName('t');
    for (var i = 0; i < box.length; i++) {
        box[i].style.backgroundColor = '#fff';

    }
}

function addRow(thisBtn, n){
    let tbody = thisBtn.parentElement.previousElementSibling.tBodies[0];
    let tr = document.createElement('tr');
    const num = tbody.rows.length + 1;
    const numTextNode = document.createTextNode(num);
    const td = document.createElement('td');
    td.append(numTextNode);
    tr.append(td);
    for (var i = 1; i < n; i++) {
        const td = document.createElement('td');
        tr.append(td);
    }
    tbody.append(tr);
}
function deleteRow(thisBtn){
    let collection = thisBtn.parentElement.previousElementSibling.tBodies[0].rows;
    collection[collection.length-1].remove();
}

//для кнопок редактирования тектса
function formatDoc(sCmd, sValue) {
    if (validateMode()) {
        document.execCommand(sCmd, false, sValue);
    }
}

//проверка что редактируемая обоасть активна и курсор в зоне редактора
function validateMode() {
    if($('.cardDogovor-boxViewText').attr('contenteditable') == 'true' && $(window.getSelection().focusNode).parents('.cardDogovor-boxViewText').length) {
        return true;
    }
    else{
        return true
    }
}

////////////////////////////////////////////////////////////////////////////////////////

$(document).ready(function() {

    //setHeaderFullName();

    $('#select_type_user').on('change', function() {
        let value = $(this).val();
        setHeaderFullName(value);
    });


    $(document).on('click touchstart', '.cardDogovor-boxViewText spandoc', function() {
        var category = $(this);
        var id_category = category.attr('data-id');
        var canvas_contr = $('.cardDogovor-boxViewText');
        // загружаем содержимое категории
        $.post(
            "/response/ajax/get_template_text_contract.php", {
                idcontract: id_category
            },
            onAjaxSuccess
        );

        function onAjaxSuccess(data) {
            // Здесь мы получаем данные, отправленные сервером и выводим их на экран.             
            canvas_contr.html(data);
        }

    });

    $(document).on('click touchstart', '#save_btn', function() {
        let canvas_contr = $('.cardDogovor-boxViewText');
        let canvas_contr_context = String(canvas_contr.html());
        let id = $(this).attr('data-id');
        // загружаем содержимое категории
        $.post(
            "/response/ajax/up_contract_text.php", {
                contect: canvas_contr_context,
                id: id
            },
            onAjaxSuccess
        );

        function onAjaxSuccess(data) {
            // Здесь мы получаем данные, отправленные сервером и выводим их на экран.
            let result = JSON.parse(data);
            if(result['TYPE']=='ERROR'){
                console.log(result['VALUE']);
                alert(result['VALUE']);
            }
            if(result['TYPE']=='SUCCESS'){
                console.log(result['VALUE']);
                //alert(result['VALUE']);
                window.location.href = "/my_pacts/edit_my_pact/?ELEMENT_ID="+result['ID']+"&ACTION=EDIT";
            }

        }

    });

    // прыжки по шагам заполнения
    $(document).on('click touchstart', '.steps .t', function() {
        var box = $(this);
        setBackgraundStepBox();
        box.css('backgroundColor', '#ff64160f');
        var text_box_id = box.attr("id");
        var text_box = $("#" + text_box_id + "_text");
        var text_box_js = document.getElementById(text_box_id + "_text");
        var canvas_contr = $('.cardDogovor-boxViewText');
        var scroll_position = text_box_js.offsetTop;
        canvas_contr.scrollTop(scroll_position - 60);
        text_box.css('backgroundColor', '#ff64160f');
    });

    // разрешение редактирование текста
    $('#btn-edit').on('click', function() {
        var canvas = $('#canvas');
        var onof = on_contenteditable(canvas);
        var span_icon = $(this).find('span');

        if (onof) {
            $(this).css("backgroundColor", "#ff6416");
            $(span_icon).css("color", "#fff");
            $('.js-disabled').attr('disabled', false);
        } else {
            $(this).css("backgroundColor", "#fff");
            $(span_icon).css("color", "#ff6416");
            $('.js-disabled').attr('disabled', true);
        }
    });


    //установка даты
    $(document).on('click', '.js-btn-data', function() {
        if($('.cardDogovor-boxViewText').attr('contenteditable') == 'true' && $(window.getSelection().focusNode).parents('.cardDogovor-boxViewText').length) {
            /*var date_ins = new Date();
            insertTextAtCursor(date_ins.getDate() + '.' + date_ins.getMonth() + '.' + date_ins.getFullYear());*/
            //var data_ins = "<edbox contenteditable='false'>%DATE%</edbox>";
            var data_ins = "%DATE%";
            insertTextAtCursor(data_ins);
        }
    });

    //таблица с реквизитами
    $(document).on('click', '.js-btn-rquised', function() {
        if($('.cardDogovor-boxViewText').attr('contenteditable') == 'true' && $(window.getSelection().focusNode).parents('.cardDogovor-boxViewText').length) {
            var data_ins = '<table class="detail_contract" border="1" cellspacing="0" cellpadding="0" style="border-collapse: collapse; width: 90%;margin: 5%;">' +
                '<thead>' +
                '<tr>' +
                '<td colspan="2">Исполнитель</td>' +
                '<td colspan="2">Заказчик</td>' +
                '</tr>' +
                '</thead>' +
                '<tbody>' +
                '<tr>' +
                '<td style="width: 25%;">ФИО</td>' +
                '<td style="width: 25%;">[____]</td>' +
                '<td style="width: 25%;">ФИО</td>' +
                '<td style="width: 25%;">[____]</td>' +
                '</tr>' +
                '<tr>' +
                '<td>Адрес</td>' +
                '<td>[____]</td>' +
                '<td>Адрес</td>' +
                '<td>[____]</td>' +
                '</tr>' +
                '<tr>' +
                '<td>Телефон</td>' +
                '<td>[____]</td>' +
                '<td>Телефон</td>' +
                '<td>[____]</td>' +
                '</tr>' +
                '<tr>' +
                '<td>Расчетные реквизиты</td>' +
                '<td>[____]</td>' +
                '<td>Расчетные реквизиты</td>' +
                '<td>[____]</td>' +
                '</tr>' +
                '</tbody>' +
                '</table>';
            insertTextAtCursorRedact(data_ins);
        }
    });

    //ФИО
    $(document).on('click', '.js-btn-fio', function() {
        if($('.cardDogovor-boxViewText').attr('contenteditable') == 'true' && $(window.getSelection().focusNode).parents('.cardDogovor-boxViewText').length) {
            var data_ins = user_req.NAME.VALUE;
            insertTextAtCursor(data_ins);
        }
    });

    //Адрес
    $(document).on('click', '.js-btn-address', function() {
        if($('.cardDogovor-boxViewText').attr('contenteditable') == 'true' && $(window.getSelection().focusNode).parents('.cardDogovor-boxViewText').length) {
            var data_ins = user_req.INDEX.VALUE+', '+user_req.REGION.VALUE+', '+user_req.CITY.VALUE+', '+user_req.STREET.VALUE+', '+user_req.HOUSE.VALUE;
            insertTextAtCursor(data_ins);
        }
    });

    //ФИО Контрагента
    $(document).on('click', '.js-btn-fio-contr', function() {
        if($('.cardDogovor-boxViewText').attr('contenteditable') == 'true' && $(window.getSelection().focusNode).parents('.cardDogovor-boxViewText').length) {
            var data_ins = '%FIO_CONTRAGENT%';
            insertTextAtCursor(data_ins);
        }
    });

    //ФИО Контрагента
    $(document).on('click', '.js-btn-adress-contr', function() {
        if($('.cardDogovor-boxViewText').attr('contenteditable') == 'true' && $(window.getSelection().focusNode).parents('.cardDogovor-boxViewText').length) {
            var data_ins = '%ADDRESS_CONTRAGENT%';
            insertTextAtCursor(data_ins);
        }
    });


    //кнопки редактирования текста
    $('.form_text').on('click', function() {
        if($('.cardDogovor-boxViewText').attr('contenteditable') == 'true' && $(window.getSelection().focusNode).parents('.cardDogovor-boxViewText').length) {
            let id_name = $(this).attr('id');
            formatSelectText(id_name);
        }
    });

    $(document).on('click', '#btn-title', function(){
        if($('.cardDogovor-boxViewText').attr('contenteditable') == 'true' && $(window.getSelection().focusNode).parents('.cardDogovor-boxViewText').length) {
            let id_name = $(this).attr('id');
            formatSelectTitle(id_name);
        }
    });

    $('rededittext').on('click', function() {
        let select_box = $(this);
        let dom_nodes = $($.parseHTML('<input type="text" class="input_text" />'));
        select_box.after(dom_nodes);
        $(dom_nodes).focus();

    });

    // ввод текста во всплывающем окне
    $(document).on('focusout', '.input_text', function() {
        let variable = $(this);
        loadTextBox(variable);
    });
    $("body").keypress(function(e) {
        if (e.which == 13) {
            let variable = $('.input_text');
            loadTextBox(variable);
        }
    });

    /*var form = document.forms.namedItem("loadcontract");
    form.addEventListener('submit', function(ev) {
        var oOutput = document.querySelector("div"),
            oData = new FormData(form);

        $('.cardDogovor').prepend("<div class='document-load'></div>");

        oData.append("CustomField", "This is some extra data");

        var oReq = new XMLHttpRequest();
        oReq.open("POST", "/response/ajax/phpword.php?uploadfiles", true);
        oReq.onload = function(oEvent) {
            if (oReq.status == 200) {
                let canvas = document.getElementById('canvas');
                canvas.innerHTML = oEvent.srcElement.response;

            } else {
                oOutput.innerHTML = "Error " + oReq.status + " occurred when trying to upload your file.<br \/>";
            }
            $('.document-load').remove();
        };

        oReq.send(oData);
        ev.preventDefault();
    }, false);*/

    $(document).on('click touchstart', '.cardDogovor-boxViewText', function() {
        $('.tools_redactor .btn-nfk-invert').removeClass('btn-nfk-invert');
        // получаем выделенный текст
        let selection = window.getSelection();
        let range = selection.getRangeAt(0);
        let sel_string = selection.toString();
        if(sel_string){
            if(range.startContainer.parentElement.tagName == 'B' && range.endContainer.parentElement.tagName == 'B'){
                $('#btn-bold').addClass('btn-nfk-invert');
            }
            if(range.startContainer.parentElement.tagName == 'I' && range.endContainer.parentElement.tagName == 'I'){
                $('#btn-italic').addClass('btn-nfk-invert');
            }
            if(range.startContainer.parentElement.tagName == 'NEDITTEXT' && range.endContainer.parentElement.tagName == 'NEDITTEXT'){
                $('#btn-nedittext').addClass('btn-nfk-invert');
            }
            if(range.startContainer.parentElement.tagName == 'H4' && range.endContainer.parentElement.tagName == 'H4'){
                $('#btn-title').addClass('btn-nfk-invert');
            }
        }
    });

});