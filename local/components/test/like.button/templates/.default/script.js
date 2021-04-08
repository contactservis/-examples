$(document).ready(function() { 
    console.log('Like!');   
    $('.btn_like').on("click",function(){
        let userId = $(this).children(".UserID").val();
        let ratingCountainer = $(".handup-likes__count");
        let IdElement = $(this).children(".ElementID").val();
        let Box_vote_count = $("#vote_count_post");  
        console.log('ID_EL', IdElement); 
            
        $.ajax({
            url: '/local/ajax/like_button.php',
            method: 'get',
            data: {
                userId: userId,
                typeLike: 'like',
                idElement: IdElement
            },
            dataType: 'json',
            success: function (result) {
                console.log("ResultRequest", result);
                ratingCountainer.html(result);
                Box_vote_count.html( Number(result.vote_sum) )
                // location.reload()
            }
        });
    });
});