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

cObj("input_fields").onchange = function () {
    var input_fields = document.getElementsByClassName("input_fields");
    for (let index = 0; index < input_fields.length; index++) {
        const element = input_fields[index];
        element.checked = this.checked;
    }
    getFieldValues();
}

window.onload = function () {
    setTimeout(() => {
        var input_fields = document.getElementsByClassName("input_fields");
        for (let index = 0; index < input_fields.length; index++) {
            const element = input_fields[index];
            element.addEventListener("change",getFieldValues);
        }
        getFieldValues();
    }, 1000);
}

function getFieldValues() {
    var input_fields = document.getElementsByClassName("input_fields");
    var data = [];
    var counter = 0;
    for (let index = 0; index < input_fields.length; index++) {
        const element = input_fields[index];
        if (element.checked) {
            data.push(element.value);
            counter++;
        }
    }
    if (counter == input_fields.length) {
        cObj("input_fields").checked = true;
        cObj("input_fields").indeterminate = false;
    }else{
        cObj("input_fields").checked = false;
        cObj("input_fields").indeterminate = true;
    }
    if (counter == 0) {
        cObj("input_fields").checked = false;
        cObj("input_fields").indeterminate = false;
    }
    cObj("store_privileges").value = JSON.stringify(data);
}