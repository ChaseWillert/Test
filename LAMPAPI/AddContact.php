<?php
	
	//Get deserialized json data.
	$inData = getRequestInfo();
	
	//Set local variables to deserialized json data.
	$first = $inData["first"];
	$last = $inData["last"];
	$user_id = $inData["user_id"];
	
	//Temporarily using Noah's login for MySQL.
	$conn = new mysqli("localhost", "Noah_API", "Noah_API_Password", "NOAH_TEST");
	
	//Check for connection error.
	if ($conn->connect_error)  {
		returnWithError( $conn->connect_error );
	}
	else {
		//Create insert command for MySQL.
		$sql = "insert into Contacts (first,last,user_id) VALUES ('" . $first . "','" . $last . "'," . $user_id . ")";
		
		//Check if query could be completed.
		if( $result = $conn->query($sql) != TRUE ) {
			returnWithError( $conn->error );
		}
		
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
		echo $obj;
	}
	
	//Function for setting up the return.
	function returnWithError( $err ) {
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
?>