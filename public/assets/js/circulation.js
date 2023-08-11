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
        var view_check_out_details = document.getElementsByClassName("view_check_out_details");
        for (let index = 0; index < view_check_out_details.length; index++) {
            const element = view_check_out_details[index];
            element.addEventListener("click",function () {
                var this_id = this.id.substr(23);
                var check_in_value = valObj("checkin_values_"+this_id);
                if (hasJsonStructure(check_in_value)) {
                    check_in_value = JSON.parse(check_in_value);
                    // console.log(check_in_value);
                    cObj("book_title").innerText = check_in_value.book_title;
                    cObj("this_book").innerText = check_in_value.book_title;
                    cObj("this_book_call_no").innerText = check_in_value.book_call_number;
                    cObj("book_call_number").innerText = check_in_value.book_call_number;
                    cObj("user_fullname").innerText = check_in_value.user_fullname;
                    cObj("edit_date_checked_out").innerText = check_in_value.edit_date_checked_out;
                    cObj("edit_return_date").innerText = check_in_value.edit_return_date;
                    cObj("edit_expected_return_date").innerText = check_in_value.edit_expected_return_date;
                    cObj("fullname_check_in").innerText = check_in_value.fullname_check_in;
                    cObj("fullname_check_out").innerText = check_in_value.fullname_check_out;
                    cObj("comments").innerText = check_in_value.comments == null ? "No comments!" : check_in_value.comments;
                    cObj("book_thumbnails").src = check_in_value.book_details != null ? check_in_value.book_details.thumbnail_location : "/images/book_cover.jpg";
                    cObj("confirm_cancel_check_in").href = "/Circulation/Cancel/check-in/"+check_in_value.circulation_id;
                    cObj("tool_tip_borrower").innerText = check_in_value.user_borrowing;
                }else{
                    console.error("Does not work");
                }
            });
        }
    }, 1000);
}