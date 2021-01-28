<?php
	
	//Get deserialized json data.
	$inData = getRequestInfo();
	
	//Set local variables to deserialized json data.
	$first = $inData["first"];
	$last = $inData["last"];
	$id = $inData["id"];
	
	//Temporarily using Noah's login for MySQL.
	$conn = new mysqli("localhost", "Noah_API", "Noah_API_Password", "NOAH_TEST");
	
	//Check for connection error.
	if ($conn->connect_error)  {
		returnWithError( $conn->connect_error );
	}
	else {
		//Create update command for MySQL.
		$sql = $conn->prepare("update Contacts set first = ?, last = ? where id = ?");
		
		//Check if query could be completed.
		if ($sql->bind_param("ssi", $first, $last, $id) == false) {
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
		echo $obj;
	}
	
	//Function for setting up the return.
	function returnWithError( $err ) {
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
?>