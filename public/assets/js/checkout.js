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

cObj("keyword_search").onkeyup = function (e) {
    if (e.keyCode != 40 && e.keyCode != 38 && e.keyCode != 13) {
        if (this.value.trim().length > 0) {
            findKeyword(this.value.trim());
        }else{
            closeAllLists();
        }
    }
}

var currentFocus;
/*execute a function presses a key on the keyboard:*/
cObj("keyword_search").addEventListener("keydown", function (e) {
    var x = document.getElementById(this.id + "autocomplete-list");
    if (x) x = x.getElementsByTagName("div");
    if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
    } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
    } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        // e.preventDefault();
        if (currentFocus > -1) {
            /*and simulate a click on the "active" item:*/
            if (x) x[currentFocus].click();
        }
    }
});

function findKeyword(keyword_search) {
    // Create a new XMLHttpRequest object
    const xhr = new XMLHttpRequest();

    // initialize the loader
    cObj("book_isbn_loader_in").classList.remove("d-none");

    // Configure the request
    xhr.open("GET", "/Cataloguing/keyword_search/"+keyword_search, true);

    // Set up the event handler for when the request is complete
    xhr.onload = function () {
        if (xhr.status === 200) {
            // Request was successful (status code 200)
            const responseData = JSON.parse(xhr.responseText);

            var arr = [], arr2 = [], arr3 = [], arr4 = [];
            for (let index = 0; index < responseData.length; index++) {
                const element = responseData[index];
                arr.push(element.book_title);
                arr2.push(element.book_id);
                arr3.push(element.isbn_13);
                arr4.push(element.availability_status);
            }
            
            // !!!!!!!!!!!
            // this code displays the found data on the screen
            var a, b, i, val = keyword_search;
            /*close any already open lists of autocompleted values*/
            closeAllLists();
            if (!val) {
                return false;
            }
            currentFocus = -1;
            /*create a DIV element that will contain the items (values):*/
            a = document.createElement("DIV");
            a.setAttribute("id", cObj("keyword_search").id + "autocomplete-list");
            a.setAttribute("class", "autocomplete-items");
            /*append the DIV element as a child of the autocomplete container:*/
            cObj("keyword_search").parentNode.appendChild(a);
            /*for each item in the array...*/
            var counter = 0;
            for (i = 0; i < arr.length; i++) {
                if (counter > 10) {
                    break;
                }
                
                /*check if the item starts with the same letters as the text field value:*/
                if (true) {
                    /*create a DIV element for each matching element:*/
                    b = document.createElement("DIV");
                    /*make the matching letters bold:*/
                    b.innerHTML = "<span class='text-primary'><b>Name : </b>"+arr[i] + " || <b>ISBN-13 : </b> "+arr3[i]+""+"</span>";
                    // b.innerHTML += arr[i].substr(val.length);
                    /*insert a input field that will hold the current array item's value:*/
                    b.innerHTML += "<input type='hidden' value=\"" + arr[i] + "\">";
                    /*execute a function when someone clicks on the item value (DIV element):*/
                    b.addEventListener("click", function (e) {
                        /*insert the value for the autocomplete text field:*/
                        cObj("keyword_search").value = this.getElementsByTagName("input")[0].value;
                        /*close the list of autocompleted values,
                        (or any other open lists of autocompleted values:*/
                        closeAllLists();

                        // submit the form
                        cObj("submit_advance_search").click();
                    });
                    a.appendChild(b);
                    counter++;
                }
            }

            
            // ends here!!!

            // initialize the loader
            cObj("book_isbn_loader_in").classList.add("d-none");
        } else {
            // Request failed with an error status code
            console.error("Request failed with status:", xhr.status);
            // You can add actions based on the error status code here

            // initialize the loader
            cObj("book_isbn_loader_in").classList.add("d-none");
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


function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete-active");
}

function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
        x[i].classList.remove("autocomplete-active");
    }
}

function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
        if (elmnt != x[i] && elmnt != cObj("keyword_search")) {
            x[i].parentNode.removeChild(x[i]);
        }
    }
}
/*execute a function when someone clicks in the document:*/
document.addEventListener("click", function (e) {
    closeAllLists(e.target);
});