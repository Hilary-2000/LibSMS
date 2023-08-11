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

window.onload = function () {
    setTimeout(() => {
        cObj("select_staff_details").classList.add("d-none");
    }, 1000);
}

cObj("book_borrower").onchange = function () {
    if (this.value == "staff") {
        cObj("select_student_details").classList.add("d-none");
        cObj("select_staff_details").classList.remove("d-none");
    }else{
        cObj("select_student_details").classList.remove("d-none");
        cObj("select_staff_details").classList.add("d-none");
    }
}