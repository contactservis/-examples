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

// вставка текста на место курсора
function insertTextAtCursor(text) {
    let selection = window.getSelection();
    let range = selection.getRangeAt(0);
    range.deleteContents();
    let node = document.createTextNode(text);
    range.insertNode(node);

    for (let position = 0; position != text.length; position++) {
        selection.modify("move", "right", "character");
    };
}

function insertBoldAtCursor(text) {
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
}

function formatSelectText(id_name) {
    // получаем выделенный текст
    let selection = window.getSelection();
    let range = selection.getRangeAt(0);
    let sel_string = selection.toString();
    console.log(selection.focusNode.parentNode);
    // удаляем его, что бы замнить
    range.deleteContents();
    // на основе id выбираем подстановку    
    let key = id_name.replace('btn-', '');
    let arrTegs = {
        weight: 'b',
        italic: 'i',
        noedit: 'noedit'
    }

    let insert_space = document.createElement(arrTegs[key]);
    if (key == 'noedit') {
        insert_space.setAttribute('contenteditable', false);
    }
    insert_space.innerHTML = sel_string;
    range.insertNode(insert_space);

    for (let position = 0; position != text.length; position++) {
        selection.modify("move", "right", "character");
    };
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
/*
function setHeaderFullName(idname) {
    let seller = document.getElementsByClassName('fullnameseller');
    let customer = document.getElementsByClassName('fullnamecustomer');
    //let type_user = document.getElementById('step0_text');
    switch (idname) {
        case 'seller':
            for (var i = 0; i < seller.length; i++) {
                seller[i].innerText = full_name.surname + ' ' + full_name.name + ' ' + full_name.midlname;

            }
            for (var i = 0; i < customer.length; i++) {
                customer[i].innerText = '[ФИО Покупателя]'

            }
            //type_user.setAttribute('type', 'seller');
            break;
        case 'customer':
            for (var i = 0; i < seller.length; i++) {
                seller[i].innerText = '[ФИО Продавца]';

            }
            for (var i = 0; i < customer.length; i++) {
                customer[i].innerText = full_name.surname + ' ' + full_name.name + ' ' + full_name.midlname;

            }
            //type_user.setAttribute('type', 'customer');
            break;

        default:
            for (var i = 0; i < seller.length; i++) {
                seller[i].innerText = full_name.surname + ' ' + full_name.name + ' ' + full_name.midlname;
            }
            for (var i = 0; i < customer.length; i++) {
                customer[i].innerText = '[ФИО Покупателя]';

            }
            //type_user.setAttribute('type', 'seller');
            break;
    }

} */

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
    console.log(collection);
    collection[collection.length-1].remove();
}

////////////////////////////////////////////////////////////////////////////////////////

$(document).ready(function() {

    //setHeaderFullName();

    $('#select_type_user').on('change', function() {
        let value = $(this).val();
        setHeaderFullName(value);
    });

    $('.cardDogovor-boxViewText span').on('click', function() {
        var category = $(this);
        var id_category = category.attr('data-id');
        var canvas_contr = $('.cardDogovor-boxViewText');

        // загружаем содержимое категории
        $.post(
            "/response/ajax/get_template_contract.php", {
                idcontract: id_category
            },
            onAjaxSuccess
        );

        function onAjaxSuccess(data) {
            // Здесь мы получаем данные, отправленные сервером и выводим их на экран.            
            canvas_contr.html(data);
        }


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
                window.location.href = "/my_pacts/edit_my_pact/?ELEMENT_ID="+result['ID']+"&ACTION=EDIT";
            }

        }

    });
    
    // разрешение редактирование текста
    $('#btn-edit').on('click', function() {
        var canvas = $('#canvas');
        var onof = on_contenteditable(canvas);
        var span_icon = $(this).find('span');
        console.log('edit')
        if (onof) {
            $(this).css("backgroundColor", "#ff6416");
            $(span_icon).css("color", "#fff");
        } else {
            $(this).css("backgroundColor", "#fff");
            $(span_icon).css("color", "#ff6416");
        }

    });


    $('#btn-data').on('click', function() {
        var date_ins = '/%DATE%/';
        insertTextAtCursor(date_ins);
    });

    $('.form_text').on('click', function() {
        let id_name = $(this).attr('id');
        formatSelectText(id_name);
    });

    $('rededittext').on('click', function() {
        console.log('Выделенная часть текста');
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

    // подписание текста

    $('#sign_edit_contract').on('click', function(e){
        console.log('Подписание!');
        console.log(Contract);


        e.preventDefault();
        let url = '/response/ajax/send_edit_contract.php';
        let canvas_contr = $('.cardDogovor-boxViewText'); 
        let canvas_contr_context = String(canvas_contr.html());       
        let data = {
            IDItem: Contract.idItem,
            IDUser: Contract.idUser,
            Text: canvas_contr_context
        };

        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            success: function(result){
                console.log(result);
                document.location.replace('/profile/aut_esia.php?ID_SENDITEM='+result)
                // if(result==1){
                //     document.location.replace('/my_pacts/')
                // }
            },

        });

    });

    // попап с подписанием
    $('#send_edit_contract').on('click', function(){
        console.log('окно для подписания')
        $('#send_sms').css('display', 'block');
    });
    // закрытие попапа с подписанием
    $('#close_sign_popup, #signpopup_close').on('click', function(){
        $('#send_sms').css('display', 'none');
    });

});