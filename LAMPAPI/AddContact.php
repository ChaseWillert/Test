<?php
	
	//Get deserialized json data.
	$inData = getRequestInfo();
	
	//Set local variables to deserialized json data.
	$first = $inData["first"];
	$last = $inData["last"];
	$email = $inData["email"];
	$phone = $inData["phone"];
	$street = $inData["street"];
	$city = $inData["city"];
	$state = $inData["state"];
	$zip = $inData["zip"];
	$notes = $inData["notes"];
	//$user_id = $inData["user_id"];
	
	//Temporarily using test login for MySQL.
	$conn = new mysqli("localhost", "Test", "Test_Pass", "ContactManager");
	
	//Check for connection error.
	if ($conn->connect_error)  {
		returnWithError( $conn->connect_error );
	}
	else {
		//Create insert command for MySQL. Temporarily have test values for DateCreated.
		$sql = $conn->prepare("insert into ContactTable (FirstName,LastName,Email,PhoneNumber,StreetAddress,City,State,ZipCode,Notes,DateCreated) VALUES (?,?,?,?,?,?,?,?,?,'1000-01-01 00:00:00')");
		
		//Check if query could be completed.
		if ($sql->bind_param("sssssssss", $first, $last, $email, $phone, $street, $city, $state, $zip, $notes) == false) {
			returnWithError("bind_param failed");
			end;
		}
		
		//Execute MySQL query.
		$sql->execute();
		
		//Close the connection.
		$conn->close();
	}
	
	//Return with no errors.
	returnWithError("");
	
	//Function for deserializing input json data.
	function getRequestInfo() {
		return json_decode(file_get_contents('php://input'), true);
	}
	
	//Function for sending resultant json data.
	function sendResultInfoAsJson( $obj ) {
		header('Content-type: application/json');
		echo json_encode($obj);
	}
	
	//Function for setting up the return.
	function returnWithError( $err ) {
		$retValue = array('error' => $err);
		sendResultInfoAsJson( $retValue );
	}
	
?>