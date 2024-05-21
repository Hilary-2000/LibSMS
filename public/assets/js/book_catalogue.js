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
        var book_details = document.getElementsByClassName("book_details");
        for (let index = 0; index < book_details.length; index++) {
            const element = book_details[index];
            element.addEventListener("click", deleteBooks);
        }
    }, 1000);
}

function deleteBooks() {
    var this_id = this.id.substr(13);
    var book_details = valObj("book_dets_"+this_id);
    
    // check if it has json structure
    if (hasJsonStructure(book_details)) {
        book_details = JSON.parse(book_details);
        cObj("book_location_overview").innerText = book_details.shelf_no_location == null ? "Not set" : book_details.shelf_no_location;
        cObj("book_call_no_overview").innerText = book_details.call_no == null ? "Not set" : book_details.call_no;
        cObj("book_edition_overview").innerText = book_details.edition == null ? "Not set" : book_details.edition;
        cObj("book_language_overview").innerText = book_details.language == null ? "Not set" : book_details.language;
        cObj("book_category_overview").innerText = book_details.book_category == null ? "Not set" : book_details.book_category;
        cObj("book_keywords_overview").innerText = book_details.keywords == null ? "Not set" : book_details.keywords;
        
        // fields
        cObj("book_location").value = book_details.shelf_no_location == null ? "" : book_details.shelf_no_location;
        cObj("book_call_number").value = book_details.call_no == null ? "" : book_details.call_no;
        cObj("book_edition").value = book_details.edition == null ? "" : book_details.edition;
        cObj("book_language").value = book_details.language == null ? "" : book_details.language;
        // cObj("book_category_overview").value = book_details.book_category == null ? "" : book_details.book_category;
        cObj("book_keywords").value = book_details.keywords == null ? "" : book_details.keywords;
        cObj("books_ids").value = book_details.book_id;
    }
}