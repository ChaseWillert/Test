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
		document.getElementById("search-result").innerHTML = "Logged in as " + firstName + " " + lastName;
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
	var srch = document.getElementById("search-bar").value;
	document.getElementById("search-result").innerHTML = "";

	var contactInfo = "";

	var jsonPayload = '{"search" : "' + srch + '","userId" : ' + userId + '}';
	var url = urlBase + '/SearchContacts.' + extension;

	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function()
		{
			if (this.readyState == 4 && this.status == 200)
			{
				// document.getElementById("colorSearchResult").innerHTML = "Color(s) has been retrieved";
				var jsonObject = JSON.parse( xhr.responseText );

				for( var i=0; i<jsonObject.results.length; i++ )
				{
					contactInfo += jsonObject.results[i];
					if( i < jsonObject.results.length - 1 )
					{
						contactInfo += "<br />\r\n";
					}
				}

				document.getElementsByTagName("p")[0].innerHTML = contactInfo;
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("search-result").innerHTML = err.message;
	}
}
