var urlBase = 'LAMPAPI';
var extension = 'php';

var userId = 0;
var firstName = "";
var lastName = "";

function doLogin()
{
	userId = 0;
	firstName = "";
	lastName = "";

	var login = document.getElementById("username-field").value;
	var password = document.getElementById("password-field").value;
	var hash = md5(password); // good

	var jsonPayload = '{"login" : "' + login + '", "password" : "' + hash + '"}';
//	var jsonPayload = '{"login" : "' + login + '", "password" : "' + password + '"}';
	var url = urlBase + '/Login.' + extension;

	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, false);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.send(jsonPayload);

		var jsonObject = JSON.parse( xhr.responseText );

		userId = jsonObject.id;

		if( userId < 1 )
		{
			alert("Invalid username or password.");
			return;
		}

		firstName = jsonObject.firstName;
		lastName = jsonObject.lastName;

		saveCookie();

		window.location.href = "mainpage.html";
	}
	catch(err)
	{
		alert(err.message);
	}
}

function doRegister()
{
	var first = document.getElementById("first-name-field").value;
	var last = document.getElementById("last-name-field").value;
	var login = document.getElementById("username-field").value;
	var password = document.getElementById("password-field").value;
	var hash = md5(password);

	var jsonPayload =
      '{"first" : "' + first + '", "last" : "' + last +
	  '", "login" : "' + login + '", "password" : "' + hash + '"}';
	//	var jsonPayload = '{"login" : "' + login + '", "password" : "' + password + '"}';
	var url = urlBase + '/Register.' + extension;

	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function()
		{
			if (this.readyState == 4 && this.status == 200)
			{
				window.location.href = "index.html";
			}
		};

		xhr.send(jsonPayload);
	}
	catch(err)
	{
		alert(err.message);
	}
}


function saveCookie()
{
	var minutes = 20;
	var date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));
	document.cookie =
	"firstName=" + firstName + ",lastName=" + lastName +
	",userId=" + userId + ";expires=" + date.toGMTString();
}

function readCookie()
{
	userId = -1;
	var data = document.cookie;
	var splits = data.split(",");
	for(var i = 0; i < splits.length; i++)
	{
		var thisOne = splits[i].trim();
		var tokens = thisOne.split("=");
		if( tokens[0] == "firstName" )
		{
			firstName = tokens[1];
		}
		else if( tokens[0] == "lastName" )
		{
			lastName = tokens[1];
		}
		else if( tokens[0] == "userId" )
		{
			userId = parseInt( tokens[1].trim() );
		}
	}

	if( userId < 0 )
	{
		window.location.href = "index.html";
	}
	else
	{
		document.getElementById("login-result").innerHTML = "Logged in as " + firstName + " " + lastName;
	}
}

function doLogout()
{
	userId = 0;
	firstName = "";
	lastName = "";
	document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
	window.location.href = "index.html";
}

function addContact()
{
	var firstName = document.getElementById("first-name-field").value;
	var lastName = document.getElementById("last-name-field").value;
	var email = document.getElementById("email-field").value;
	var phoneNumber = document.getElementById("phone-field").value;
	var street = document.getElementById("street-field").value;
	var city = document.getElementById("city-field").value;
	var state = document.getElementById("state-field").value;
	var zip = document.getElementById("zip-field").value;
	var notes = document.getElementById("notes-field").value;
	// document.getElementById("colorAddResult").innerHTML = "";

	var jsonPayload =
      '{"first" : "' + firstName + '", "last" : "' + lastName +
	  '", "street" : "' + street + '", "city" : "' + city +
	  '", "zip" : "' + zip + '", "state" : "' + state +
	  '", "notes" : "' + notes + '", "userId" : "' +userId +
	  '", "email" : "' + email + '", "phone" : "' + phoneNumber + '"}';
	var url = urlBase + '/AddContact.' + extension;

	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function()
		{
			if (this.readyState == 4 && this.status == 200)
			{
				document.getElementById("add-result").innerHTML = "Contact has been added";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("add-result").innerHTML = err.message;
	}
}


function searchContact()
{
    readCookie();
    
    var jsonPayload = JSON.stringify({ userId: userId,
                                     firstName: document.getElementById("search-firstName").value,
                                     lastName: document.getElementById("search-lastName").value,
                                     email: document.getElementById("search-email").value,
                                     phoneNumber: document.getElementById("search-phoneNumber").value,
                                     streetAddress: document.getElementById("search-streetAddress").value,
                                     city: document.getElementById("search-city").value,
                                     state: document.getElementById("search-state").value,
                                     zipCode: document.getElementById("search-zipCode").value,
                                     notes: document.getElementById("search-notes").value});
	var url = urlBase + '/SearchContacts.' + extension;

    console.log(jsonPayload);
	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function()
		{
			if (this.readyState == 4 && this.status == 200)
			{
                console.log("responce received.");
				var responce = JSON.parse(xhr.responseText);

				console.log(xhr.responseText);
                
                var table = document.getElementById("searchResults");
                
                for (var i = table.rows.length - 1; i > 1; i--)
                {
                    table.deleteRow(i); 
                }
                
                if (responce.error == undefined)
                {
                    for (var i = 0; i < responce.length; i++)
                    {
                        var row = table.insertRow(-1);
                        
                        var firstName = row.insertCell(0);
                        var lastName = row.insertCell(1);
                        var email = row.insertCell(2);
                        var phoneNumber = row.insertCell(3);
                        var streetAddress = row.insertCell(4);
                        var city = row.insertCell(5);
                        var state = row.insertCell(6);
                        var zipCode = row.insertCell(7);
                        var notes = row.insertCell(8);
                        
                        var hiddenID = row.insertCell(9);
                        hiddenID.style.display = "none";

                        hiddenID.innerHTML = responce[i].contactId;
                        firstName.innerHTML = responce[i].firstName;
                        lastName.innerHTML = responce[i].lastName;
                        email.innerHTML = responce[i].email;
                        phoneNumber.innerHTML = responce[i].phoneNumber;
                        streetAddress.innerHTML = responce[i].streetAddress;
                        city.innerHTML = responce[i].city;
                        state.innerHTML = responce[i].state;
                        zipCode.innerHTML = responce[i].zipCode;
                        notes.innerHTML = responce[i].notes;
                        
                        row.onclick = function ()
                        {
                            console.log(row);
                            modal.style.display = "block";
                            updatingContactId = this.children[9].innerHTML;
                            console.log(updatingContactId);
                            document.getElementById("edit-firstName").value = this.children[0].innerHTML;
                            document.getElementById("edit-lastName").value = this.children[1].innerHTML;
                            document.getElementById("edit-email").value = this.children[2].innerHTML;
                            document.getElementById("edit-phoneNumber").value = this.children[3].innerHTML;
                            document.getElementById("edit-streetAddress").value = this.children[4].innerHTML;
                            document.getElementById("edit-city").value = this.children[5].innerHTML;
                            document.getElementById("edit-state").value = this.children[6].innerHTML;
                            document.getElementById("edit-zipCode").value = this.children[7].innerHTML;
                            document.getElementById("edit-notes").value = this.children[8].innerHTML;
                        };
                        
                    }
                }
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		console.log(err.message);
	}
}

var updatingContactId;
function doUpdate()
{
    
    var jsonPayload = JSON.stringify({ userId: userId,
                                      contactId: updatingContactId,
                                     firstName: document.getElementById("edit-firstName").value,
                                     lastName: document.getElementById("edit-lastName").value,
                                     email: document.getElementById("edit-email").value,
                                     phoneNumber: document.getElementById("edit-phoneNumber").value,
                                     streetAddress: document.getElementById("edit-streetAddress").value,
                                     city: document.getElementById("edit-city").value,
                                     state: document.getElementById("edit-state").value,
                                     zipCode: document.getElementById("edit-zipCode").value,
                                     notes: document.getElementById("edit-notes").value});

	var url = urlBase + '/UpdateContact.' + extension;

	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function()
		{
			if (this.readyState == 4 && this.status == 200)
			{
                console.log(xhr.responseText);
                searchContact();
				alert("Update successful");
			}

		};
		xhr.send(jsonPayload);
	}
	catch (err)
	{
		alert(err.message);
	}
    
}

function doDelete()
{
	var jsonPayload = JSON.stringify({ contactId: updatingContactId });
    
	var url = urlBase + '/DeleteContact.' + extension;

	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function()
		{
			if (this.readyState == 4 && this.status == 200)
			{
                console.log(xhr.responseText);
                searchContact();
				alert("Deletion successful");
			}

		};
		xhr.send(jsonPayload);
	}
	catch (err)
	{
		alert(err.message);
	}
}



// Update Modal logic
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
} 