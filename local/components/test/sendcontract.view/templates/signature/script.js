
$(document).ready(function(){
    // отозвать подпись
    $('#recall_sign').on('click', function(e){
        console.log('Отзыв подписи')
        let id = $(this).attr('data');
        
        e.preventDefault();
        let url = '/response/ajax/deactive_send.php';        
        let data = {
            IDItem: id            
        };

        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            success: function(result){
                console.log(result);
                if(result==1){
                    document.location.replace('/my_pacts/')
                }
            },

        });

        return false;
    });
});




