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
cObj("more_information_data").onclick = function () {
    sendAjaxRequest();
}
function sendAjaxRequest() {
    var err = 0;

    err = (checkBlank("isbn_13_details_hidden") == 0 || checkBlank("isbn_10_details_hidden")) ? 0 : 1;

    // set error
    if (err == 0) {
        var isbn_no = checkBlank("isbn_13_details_hidden") == 0 ? valObj("isbn_10_details_hidden") : valObj("isbn_13_details_hidden");
        // Create a new XMLHttpRequest object
        const xhr = new XMLHttpRequest();
        
        // initialize the loader
        classRemoveClass("more_infor_spinners","d-none");
    
        // Configure the request
        xhr.open("GET", "https://openlibrary.org/api/books?bibkeys=ISBN:"+isbn_no+"&jscmd=details&format=json", true);
    
        // Set up the event handler for when the request is complete
        xhr.onload = function () {
            if (xhr.status === 200) {
                // Request was successful (status code 200)
                const responseData = JSON.parse(xhr.responseText);
                
                // save the data
                var isbn_data = responseData['ISBN:'+isbn_no+''];
                if (isbn_data != undefined && isbn_data != null) {
                    // infor URL
                    cObj("according_to").href = isbn_data['info_url'] != undefined ? isbn_data['info_url'] : ".";
    
                    // Book weight
                    cObj("book_weight").innerText = isbn_data['details']['weight'] != undefined ? isbn_data['details']['weight'] : "N/A";
    
                    // Book physical format
                    cObj("physical_format").innerText = isbn_data['details']['physical_format'] != undefined ? isbn_data['details']['physical_format'] : "N/A";
    
                    // Book edition name
                    cObj("date_created").innerText = isbn_data['details']['created'] != undefined ? isbn_data['details']['created']['value'] : "N/A";
    
                    // Book last modified
                    cObj("last_modified").innerText = isbn_data['details']['last_modified']['value'] != undefined ? isbn_data['details']['last_modified']['value'] : "N/A";
    
                    var my_subjects = "<ul>";
                    if (isbn_data['details']['subjects'] != undefined) {
                        for (let index = 0; index < isbn_data['details']['subjects'].length; index++) {
                            const element = isbn_data['details']['subjects'][index];
                            my_subjects+="<li>"+element+"</li>";
                        }
                    }
                    my_subjects += "</ul>";
                    // Book last modified
                    cObj("book_subjects").innerHTML = my_subjects;
    
                    // edition name
                    cObj("edition_name").innerHTML = isbn_data['details']['edition_name'] != undefined ? isbn_data['details']['edition_name'] : "N/A";;
                    
                    // alert("Book not found!","danger",cObj("liveAlertPlaceholder"));
                }else{
                    // infor URL
                    cObj("according_to").href = ".";
    
                    // Book weight
                    cObj("book_weight").innerText = "N/A";
    
                    // Book physical format
                    cObj("physical_format").innerText = "N/A";
    
                    // Book edition name
                    cObj("date_created").innerText = "N/A";
    
                    // Book last modified
                    cObj("last_modified").innerText = "N/A";
    
                    var my_subjects = "N/A";
                    // Book last modified
                    cObj("book_subjects").innerHTML = my_subjects;
    
                    // edition name
                    cObj("edition_name").innerHTML = "N/A";
                }
                // initialize the loader
                classAddClass("more_infor_spinners","d-none");
            } else {
                // Request failed with an error status code
                console.error("Request failed with status:", xhr.status);
                // You can add actions based on the error status code here
    
                // initialize the loader
                classAddClass("more_infor_spinners","d-none");
            }
        };
    
        // Set up the event handler for network errors
        xhr.onerror = function () {
            console.error("Network error occurred");
            // You can add actions for network errors here
    
            // initialize the loader
            classAddClass("more_infor_spinners","d-none");
        };
    
        // Send the request
        xhr.send();
    }
}

// add class
function classAddClass(class_name,class_to_add) {
    var c = document.getElementsByClassName(class_name);
    for (let index = 0; index < c.length; index++) {
        const element = c[index];
        element.classList.add(class_to_add);
    }
}

// remove class
function classRemoveClass(class_name,class_to_add) {
    var c = document.getElementsByClassName(class_name);
    for (let index = 0; index < c.length; index++) {
        const element = c[index];
        element.classList.remove(class_to_add);
    }
}