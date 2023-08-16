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

var searched = 0;
cObj("search_book_using_isbn").addEventListener("click",sendAjaxRequest);
function sendAjaxRequest() {
    searched = 1;
    var err = 0;
    err += checkBlank("hold_book_isbn_value");

    // set error
    if (err == 0) {
        // Create a new XMLHttpRequest object
        const xhr = new XMLHttpRequest();
    
        // initialize the loader
        cObj("book_isbn_loader").classList.remove("d-none");
        cObj("search_book_using_isbn").disabled = true;
    
        // Configure the request
        xhr.open("GET", "/Acquisitions/getBookDetails?isbn_number="+valObj("hold_book_isbn_value"), true);
    
        // Set up the event handler for when the request is complete
        xhr.onload = function () {
            if (xhr.status === 200) {
                // Request was successful (status code 200)
                const responseData = JSON.parse(xhr.responseText);
                // console.log("Data returned:", responseData);

                // disable search button
                cObj("search_book_using_isbn").disabled = false;

                // You can add actions based on the response data here
                cObj("book_title").value = responseData.book_title;
                cObj("book_author").value = responseData.book_author;
                cObj("isbn_10").value = responseData.isbn_10;
                cObj("isbn_13").value = responseData.isbn_13;
                cObj("no_of_pages").value = responseData.pages;
                // cObj("book_title").value = responseData.category;
                cObj("book_publishers").value = responseData.publishers;
                cObj("date_published").value = responseData.date_published;
                cObj("book_call_no").value = responseData.call_no;
                cObj("book_location").value = responseData.book_location;
                cObj("book_description").value = responseData.book_description;
                cObj("book_dimensions").value = responseData.physical_dimensions;
                cObj("book_language").value = responseData.language;
                cObj("no_of_revisions").value = responseData.revisions;
                cObj("prev_call_no").innerText = "Last Call No : {"+responseData.last_call_no+"}";

                // set the book image thumbnail
                var image_url = responseData.cover_url.length > 0 ? responseData.cover_url.substr(0,responseData.cover_url.length-5)+"L.jpg" : "";
                cObj("book_cover_image").src = image_url;
                cObj("book_cover_url").value = image_url;

                // alert to show book has been found
                if (responseData.found) {
                    if (responseData.present) {
                        alert("Found <b>\""+responseData.book_title+"\"</b> successfully! More records of the same book are present.","success",cObj("liveAlertPlaceholder"));
                    }else{
                        alert("Found <b>\""+responseData.book_title+"\"</b> successfully!","success",cObj("liveAlertPlaceholder"));
                    }
                }else{
                    alert("Book not found!","danger",cObj("liveAlertPlaceholder"));
                }
                // initialize the loader
                cObj("book_isbn_loader").classList.add("d-none");
            } else {
                // Request failed with an error status code
                console.error("Request failed with status:", xhr.status);
                // You can add actions based on the error status code here
    
                // initialize the loader
                cObj("book_isbn_loader").classList.add("d-none");
            }
        };
    
        // Set up the event handler for network errors
        xhr.onerror = function () {
            console.error("Network error occurred");
            // You can add actions for network errors here
    
            // initialize the loader
            cObj("book_isbn_loader").classList.add("d-none");
        };
    
        // Send the request
        xhr.send();
    }
}

cObj("hold_book_isbn_value").onkeydown = function (event) {
    if (searched == 1) {
        this.value = "";
        searched = 0;
    }
    // Check if the pressed key is the "Enter" key (key code 13)
    if (event.keyCode === 13) {
        // Execute your code here
        sendAjaxRequest();
      }
}

cObj("show_windows").onclick = function () {
    var inputElement = document.getElementById("hold_book_isbn_value");
    if (inputElement) {
        setTimeout(() => {
            inputElement.focus();
        }, 500);
    }
}

var check_13 = false;
var tenth_id = null;
// Add a keydown event listener to the document to capture scanned data
document.addEventListener('keyup', function (event) {
    if (event.key === "Control" || event.key === "Ctrl") {
        event.preventDefault();
        return 0;
      }
      if (event.altKey) {
        return 0
      }
    
      if (event.shiftKey) {
        return 0
      }
    // Check if the focus is on the book scanner input
    if (document.activeElement === cObj("hold_book_isbn_value")) {
        if (valObj("hold_book_isbn_value").trim().length == 10) {
            var runAjax = setTimeout(() => {
                if (!cObj("allow_typing").checked) {
                    sendAjaxRequest();
                    // console.log("10th");
                }
            }, 1000);
            tenth_id = runAjax;
        }
        if (valObj("hold_book_isbn_value").trim().length == 13) {
            if (!cObj("allow_typing").checked) {
                sendAjaxRequest();
                // console.log("13th");
            }
        }
        // check if more text is typed
        checkMoreText(tenth_id);
    }
});

function checkMoreText(timeout_id) {
    if (valObj("hold_book_isbn_value").trim().length > 10) {
        clearTimeout(timeout_id);
    }
}