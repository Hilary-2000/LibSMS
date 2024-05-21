function cObj(params) {
    return document.getElementById(params);
}

function valObj(params) {
    return document.getElementById(params).value;
}

function stopInterval(id) {
    clearInterval(id);
}
function grayBorder(object) {
    object.style.borderColor = 'gray';
}
function redBorder(object) {
    object.style.borderColor = 'red';
}
function checkBlank(id){
    let err = 0;
    if(cObj(id).value.trim().length>0){
        if (cObj(id).value.trim()=='N/A') {
            redBorder(cObj(id));
            err++;
        }else{
            grayBorder(cObj(id));
        }
    }else{
        redBorder(cObj(id));
        err++;
    }
    return err;
}
function hasJsonStructure(str) {
    if (typeof str !== 'string') return false;
    try {
        const result = JSON.parse(str);
        const type = Object.prototype.toString.call(result);
        return type === '[object Object]' 
            || type === '[object Array]';
    } catch (err) {
        return false;
    }
}

cObj("borrower_stats").onchange = function () {
    cObj("borrowing_period").classList.remove("d-none");
    if (valObj("borrower_type_opt") == "students") {
        cObj("borrower_type").classList.remove("d-none");
        cObj("student_lists").classList.remove("d-none");
    }else{
        cObj("borrower_type").classList.remove("d-none");
        cObj("student_lists").classList.add("d-none");
    }

    if (cObj("borrower_stats").value == "books_borrowed_stats") {
        cObj("student_lists").classList.add("d-none");
    }else{
        if (valObj("borrower_type_opt") == "students") {
            cObj("student_lists").classList.remove("d-none");
        }else{
            cObj("student_lists").classList.add("d-none");
        }
    }
}
cObj("borrower_type_opt").onchange = function () {
    if (cObj("borrower_stats").value == "books_borrowed_stats") {
        cObj("student_lists").classList.add("d-none");
    }else{
        if (valObj("borrower_type_opt") == "students") {
            cObj("student_lists").classList.remove("d-none");
        }else{
            cObj("student_lists").classList.add("d-none");
        }
    }
}
window.onload = function () {
    if (valObj("student_dets") == "null") {
        cObj("student_lists").classList.add("d-none");
    }else{
        if (valObj("borrower_type_opt") == "students") {
            console.log(valObj("student_dets"));
            cObj("student_lists").classList.remove("d-none");
        }else{
            cObj("student_lists").classList.add("d-none");
        }
    }
}