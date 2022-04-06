function isNumeric(value) {
    return /^\d+$/.test(value);
}

function add_to_cart(pid) {
    //check if item in storage
    if ((itemCount = localStorage.getItem(pid)) != null) {
        // in set
        localStorage.setItem(pid, parseInt(itemCount) + 1);
        //alert("added " + localStorage.getItem(pid) + "in" + pid);
    }
    else {
        // not in set
        localStorage.setItem(pid, 1);
        //alert("added " + localStorage.getItem(pid) + "in" + pid);
    }
    modify_cart();
}

function getAllStorage() {
    let archive = {}, keys = Object.keys(localStorage), i = keys.length;
    while (i--) {
        if (isNumeric(keys[i])) {
            archive[keys[i]] = localStorage.getItem(keys[i]);
        }
    }
    return archive;
}

function show_cart() {
    let storage = getAllStorage();
    //console.log(storage);
    return storage;
}


$(document).ready(function(){
    modify_cart();
});


function modify_cart() {
    $.ajax({
        type: 'post',
        url: '/lib/store_items.php',
        data: {
            cart_storage: JSON.stringify(show_cart())
        },
        success: function(response) {
            // change cart. (shoppinglist)
            document.querySelector(".shoppinglist").innerHTML = response;
        }
    });
}



function btn_plus_prod(pid) {
    if ((oldValue_str = localStorage.getItem(pid)) != null && isNumeric(oldValue_str)) {
        localStorage.setItem(pid, parseInt(localStorage.getItem(pid)) + 1);
    }
    modify_cart();
}

function btn_minus_prod(pid) {
    if ((oldValue_str = localStorage.getItem(pid)) != null) {
        oldValue = parseInt(localStorage.getItem(pid));
        if (oldValue == 1) {
            localStorage.removeItem(pid);
        }
        else if (oldValue > 1) {
            localStorage.setItem(pid, oldValue - 1);
        }
    }
    modify_cart();
}

function input_prod_change(pid) {
    let input_src = document.querySelector("#input_prod_" + pid);
    if (isNumeric(input_src.value)) {
        if (input_src.value == 0) {
            // remove item
            localStorage.removeItem(pid);
        }
        else {
            // change quantity
            localStorage.setItem(pid, input_src.value);
        }
    }
    else {
        // force to 1
        input_src.value = 1;
        localStorage.setItem(pid, 1);
    }
    modify_cart();
}