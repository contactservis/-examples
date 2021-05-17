function randomInteger(min, max) {    
    let rand = min - 0.5 + Math.random() * (max - min + 1);
    return Math.round(rand);
}

async function createSMSSignOrder(arrParams){
    var url = '/restjs/create_sms_sign_order.php'
    var formData = new FormData();
        formData.append( 'arrParams', arrParams);

    const response = await fetch(url, {
        method  : 'post',
        body    : formData,
        headers: {
            //'X-CSRF-Token': token
        }
    });
    const data = await response.text();
    return data
}

async function checkSMSSignOrder(arrParams){
    var url = '/restjs/check_sms_sign_order.php'
    var formData = new FormData();
        formData.append( 'arrParams', arrParams);

    const response = await fetch(url, {
        method  : 'post',
        body    : formData,
        headers: {
            //'X-CSRF-Token': token
        }
    });
    const data = await response.json();
    return data
}

async function createSignOrder(arrParams){
    var url = '/restjs/create_sign_order.php'
    var formData = new FormData();
        formData.append( 'arrParams', arrParams);

    const response = await fetch(url, {
        method  : 'post',
        body    : formData,
        headers: {
            //'X-CSRF-Token': token
        }
    });
    const data = await response.text();
    return data
}

function createSign(){
    console.log('Тест по созданию подписанного ордера')
    let SMS_CODE        = document.getElementById('input-sms').value
    let HASH_CODE       = document.getElementById('Name-file').value
    let HASH_CONTRACT   = document.getElementById('Hash-contract').value
    let ID_CLIENT       = document.getElementById('ID-Client').value
    
    let Params      = new Object()        
        Params.URL  = '/var/www/domains/shopnew.nfksber.ru/upload/order/'+HASH_CODE+'.txt'
        Params.SMS  = SMS_CODE
        Params.HASH = HASH_CODE
        Params.HASH_CONTRACT = HASH_CONTRACT
        Params.ID_CLIENT = ID_CLIENT 
    let arrParams   = JSON.stringify(Params)
    console.log(Params.URL)
    var res = createSignOrder(arrParams).then(function(data) {
        console.log(data)
        document.location.href = 'https://shop.nfksber.ru/catalog/orderok/'
    });
}

function createSMS(id_code_tr){
    let GC = document.getElementById('ID-Client')
    let Params      = new Object()        
        Params.CODE     = id_code_tr
        Params.IDClient = GC.value        
    let arrParams   = JSON.stringify(Params)
    var res = createSMSSignOrder(arrParams).then(function(data) {
        console.log(data)
    });
}

function checkSMS(id_code_tr){
    let GC = document.getElementById('ID-Client')
    let SMS_code = document.getElementById('input-sms')
    let Params      = new Object()        
        Params.SMSCODE  = SMS_code.value
        Params.VCODE    = id_code_tr        
    let arrParams   = JSON.stringify(Params)
    var res = checkSMSSignOrder(arrParams).then(function(data) {
        console.log(data)
        if(data.response == "OK"){
            console.log('Создаем поручение')
            createSign()
        }
    });
}

window.onload = function(){
    this.console.log('Страница подписания поручения');
    // let TestCreate = document.getElementById("button-create-sign")
    
    // TestCreate.onclick = function(){
    //     createSign()
    // }
    
    // нажатие кнопки подписать запускает формирование подписи
    let signButton = document.getElementById("button-sign-order");
        signButton.onclick = function(){
            this.style.display = 'none';
            let BoxInputSMS = document.getElementById('box-input-sms');
            let BoxInputSMSMess = document.getElementById('box-input-sms-mess');
            BoxInputSMS.style.display = 'block'
            // запускается счетчик времени
            let max_time    = document.getElementById("max-time");
            let SMS_input   = document.getElementById("input-sms");
            let id_code_tr  = randomInteger(100000, 999999) 
            var sec = max_time.innerHTML;
            if (sec < 1) {
                max_time.innerHTML = 90;
            }

           createSMS(id_code_tr)

            var t = setInterval(function() {

                function f(x) {
                    return (x / 100).toFixed(2).substr(2)
                }
                s = max_time.innerHTML;
                s--;
    
                if (s < 0) {
                    s = max_time.getAttribute('long');
                    clearInterval(t);
                        BoxInputSMSMess.innerHTML = 'Срок действия кода истек';
                    setTimeout(function() {
                        //setBlock({'contract':id_contract, 'contragent':id_contraegent, 'status':'', 'action':'set'});
                        signButton.innerHTML = 'Выслать повторно'
                        signButton.style.display = 'block' 
                    }, 30);
                }
                
                max_time.innerHTML = f(s);
                let get_SMS_Code = SMS_input.value
                console.log(get_SMS_Code)
                // //var input_sms_code = getValue();
                // var valid_code = Number(input_sms_code);
    
                 if (get_SMS_Code.length > 5) {                        
                    clearInterval(t);
                    console.log('Код подтвержден')
                    checkSMS(id_code_tr)                                        
                 }
            }, 1000);


        }
    
}