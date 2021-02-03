$(document).ready(function () {
    // Получаем order

    $.ajax({
        url: '/assets/components/stripe/checkout.php',
        method: 'POST',
        dataType: 'json',
        data:{
            order: $.urlParam('order')
        },
        success: function (response) {
            console.log(response)
            stripe = new Stripe(response.apiKey);
            stripe.redirectToCheckout({
                sessionId: response.message,
            })
        }
    })

})

$.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    return results[1] || 0;
}