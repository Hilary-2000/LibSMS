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

cObj("book_options_inside").onchange = function () {
    var this_value = this.value;
    if (this_value == "book_information") {
        // hide everything
        cObj("date_selection_option").classList.add("d-none");
        cObj("single_date").classList.add("d-none");
        cObj("from_date").classList.add("d-none");
        cObj("to_date").classList.add("d-none");
    }else if (this_value == "checked_out") {
        cObj("date_selection_option").classList.remove("d-none");
        if(valObj("date_type_selection") == "specific_date"){
            cObj("single_date").classList.remove("d-none");
            cObj("from_date").classList.add("d-none");
            cObj("to_date").classList.add("d-none");
        }else{
            cObj("single_date").classList.add("d-none");
            cObj("from_date").classList.remove("d-none");
            cObj("to_date").classList.remove("d-none");
        }
    }else if (this_value == "latest_acquisition") {
        cObj("date_selection_option").classList.remove("d-none");
        if(valObj("date_type_selection") == "specific_date"){
            cObj("single_date").classList.remove("d-none");
            cObj("from_date").classList.add("d-none");
            cObj("to_date").classList.add("d-none");
        }else{
            cObj("single_date").classList.add("d-none");
            cObj("from_date").classList.remove("d-none");
            cObj("to_date").classList.remove("d-none");
        }
    }else if (this_value == "checked_in") {
        cObj("date_selection_option").classList.remove("d-none");
        if(valObj("date_type_selection") == "specific_date"){
            cObj("single_date").classList.remove("d-none");
            cObj("from_date").classList.add("d-none");
            cObj("to_date").classList.add("d-none");
        }else{
            cObj("single_date").classList.add("d-none");
            cObj("from_date").classList.remove("d-none");
            cObj("to_date").classList.remove("d-none");
        }
    }else if (this_value == "due_checked_in") {
        cObj("date_selection_option").classList.remove("d-none");
        if(valObj("date_type_selection") == "specific_date"){
            cObj("single_date").classList.remove("d-none");
            cObj("from_date").classList.add("d-none");
            cObj("to_date").classList.add("d-none");
        }else{
            cObj("single_date").classList.add("d-none");
            cObj("from_date").classList.remove("d-none");
            cObj("to_date").classList.remove("d-none");
        }
    }else if (this_value == "to_be_due_checkin") {
        cObj("date_selection_option").classList.add("d-none");
        cObj("single_date").classList.add("d-none");
        cObj("from_date").classList.add("d-none");
        cObj("to_date").classList.add("d-none");
    }
}

cObj("date_type_selection").onchange = function () {
    if(valObj("date_type_selection") == "specific_date"){
        cObj("single_date").classList.remove("d-none");
        cObj("from_date").classList.add("d-none");
        cObj("to_date").classList.add("d-none");
    }else{
        cObj("single_date").classList.add("d-none");
        cObj("from_date").classList.remove("d-none");
        cObj("to_date").classList.remove("d-none");
    }
}