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
    
        // Configure the request https://www.googleapis.com/books/v1/volumes?q=isbn:
        // xhr.open("GET", "https://openlibrary.org/api/books?bibkeys=ISBN:"+isbn_no+"&jscmd=details&format=json", true);
        xhr.open("GET", "https://www.googleapis.com/books/v1/volumes?q=isbn:"+isbn_no, true);
    
        // Set up the event handler for when the request is complete
        xhr.onload = function () {
            if (xhr.status === 200) {
                // Request was successful (status code 200)
                const responseData = JSON.parse(xhr.responseText);
                
                // save the data
                var isbn_data = responseData;
                
                if (isbn_data != undefined && isbn_data != null) {
                    if(isbn_data['totalItems'] > 0){
                        var book_data = isbn_data['items'][0];
                        // infor URL
                        cObj("according_to").href = book_data['volumeInfo']['previewLink'] != undefined ? book_data['volumeInfo']['previewLink'] : ".";
        
                        // Book weight
                        cObj("book_weight").innerText = "N/A";
        
                        // Book physical format
                        cObj("physical_format").innerText = book_data['volumeInfo']['printType'] != undefined ? book_data['volumeInfo']['printType'] : "N/A";
        
                        // Book edition name publishedDate
                        cObj("date_created").innerText = book_data['volumeInfo']['publishedDate'] != undefined ? book_data['volumeInfo']['publishedDate'] : "N/A";
        
                        // Book last modified
                        cObj("last_modified").innerText = book_data['volumeInfo']['publishedDate'] != undefined ? book_data['volumeInfo']['publishedDate'] : "N/A";
        
                        var authors = "<ul>";
                        if (book_data['volumeInfo']['authors'] != undefined) {
                            for (let index = 0; index < book_data['volumeInfo']['authors'].length; index++) {
                                const element = book_data['volumeInfo']['authors'][index];
                                authors+="<li>"+element+"</li>";
                            }
                        }
                        authors += "</ul>";
                        // Book last modified
                        cObj("book_authors").innerHTML = authors;
        
                        // edition name 
                        cObj("edition_name").innerHTML = book_data['volumeInfo']['contentVersion'] != undefined ? book_data['volumeInfo']['contentVersion'] : "N/A";;
                        
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
        
                        // edition name
                        cObj("edition_name").innerHTML = "N/A";
                    }
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