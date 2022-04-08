function handleFiles(src, target) {
    let reader = new FileReader();
    reader.onload = function(e) { 
        target.src = reader.result; 
        target.style.width = "150px";
        target.style.height = "150px";
    };
    src.addEventListener("change", function() {
        reader.readAsDataURL(src.files[0]);
    });
}

let src_insert = document.querySelector("#prod_image_insert");
let target_insert = document.querySelector("#target_insert");
handleFiles(src_insert, target_insert);

let src_edit = document.querySelector("#prod_image_edit");
let target_edit = document.querySelector("#target_edit");
handleFiles(src_edit, target_edit);

// --- Drag-and-Drop ---
// Taking reference from:
// https://developer.mozilla.org/zh-TW/docs/Web/API/File/Using_files_from_web_applications
// https://www.smashingmagazine.com/2018/01/drag-drop-file-uploader-vanilla-js/

let dropArea_insert = document.querySelector("#field_prod_insert");
let dropArea_edit = document.querySelector("#field_prod_edit");
dropArea_insert.addEventListener('drop', handleDrop_insert, false);
dropArea_edit.addEventListener('drop', handleDrop_edit, false);

function handleDrop_insert(e) {
    let file = e.dataTransfer.files[0];
    let imgType = /image.*/;
    if (!file.type.match(imgType)) {
        console.log("Wrong type");
        return;
    }
    let reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = function() {
        target_insert.src = reader.result;
        target_insert.style.width = "150px";
        target_insert.style.height = "150px";
    };
    src_insert.files = e.dataTransfer.files;
}

function handleDrop_edit(e) {
    let file = e.dataTransfer.files[0];
    let imgType = /image.*/;
    if (!file.type.match(imgType)) {
        console.log("Wrong type");
        return;
    }
    let reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = function() {
        target_edit.src = reader.result;
        target_edit.style.width = "150px";
        target_edit.style.height = "150px";
    };
    src_edit.files = e.dataTransfer.files;
}

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropArea_insert.addEventListener(eventName, preventDefaults, false);
    dropArea_edit.addEventListener(eventName, preventDefaults, false);
});
  
function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

$(document).ready(function(){
    show_order(10);
});

function show_order(count) {
    //let cat = document.querySelector("#hid_currentCat").value;
    //let perPage = document.querySelector("#hid_perPage").value;
    $.ajax({
        type: 'post',
        url: '/lib/show_orders.php',
        data: {
            query_order: count,
        },
        success: function(response) {
            // change content
            document.querySelector("#ordertable").innerHTML = response;
        }
    });
}