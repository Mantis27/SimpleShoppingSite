$(document).ready(function(){
    show_page(1);
});

function show_page(page) {
    let cat = document.querySelector("#hid_currentCat").value;
    let perPage = document.querySelector("#hid_perPage").value;
    $.ajax({
        type: 'post',
        url: '/lib/show_items.php',
        data: {
            query_page: page,
            per_page: perPage,
            category: cat
        },
        success: function(response) {
            // change content
            document.querySelector(".itemtable").innerHTML = response;
        }
    });
}