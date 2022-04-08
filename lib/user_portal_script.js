$(document).ready(function(){
    show_order(5);
});

function show_order(count) {
    let user = document.querySelector("#hid_user").value;
    $.ajax({
        type: 'post',
        url: '/lib/show_orders.php',
        data: {
            query_order: count,
            query_user: user
        },
        success: function(response) {
            // change content
            document.querySelector("#ordertable").innerHTML = response;
        }
    });
}