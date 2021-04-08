var button_send_contract = document.getElementById('send_contract');

function loadTextBox(el) {
    let variable = $(el);
    let parent_var = variable.parent('edbox');
    let text_box = parent_var.children('useredittext')
    let content_text_box = text_box.text();
    let context_input = variable.val();
    if (context_input == '') {
        context_input = content_text_box;
    }
    text_box.text(context_input);
    variable.remove();
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

$(document).ready(function() {


    $('useredittext').on('click', function() {
        let select_box = $(this);
        let dom_nodes = $($.parseHTML('<input type="text" class="input_text" />'));
        select_box.after(dom_nodes);
        $(dom_nodes).focus();

    });

    // попап с подписанием
    $('#popup_send_contract').on('click', function(){
        $('#send_sms').css('display', 'block');
    });
    // закрытие попапа с подписанием
    $('#close_sign_popup, #signpopup_close').on('click', function(){
        $('#send_sms').css('display', 'none');
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

    //своя редакция (подгрузка доски с интрументами)
    $(document).on('click', '#new_redaction', function(){
        let url = '/response/ajax/new_redaction.php';
        let id = $(this).attr('data-id_element');        
        $('#new_redaction').attr('href', '/'); 
        $('#send_contract').attr('data', 'edit')
        let data = {
            ELEMENT_ID: id
        };

        $(this).hide();

        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            success: function(result){
                $('.js-dogovor').html(result);
            },

        });

    });

});