<?php
	
	//Get deserialized json data.
	$inData = getRequestInfo();
	
	//Set local variables to deserialized json data.
	$login = $inData["login"];
	$password = $inData["password"];
	
	//Declare local variables for login.
	$id = 0;
	$firstName = "";
	$lastName = "";
	
	//Temporarily using test login for MySQL.
	$conn = new mysqli("localhost", "Test", "Test_Pass", "ContactManager");
	
	//Check for connection error.
	if ($conn->connect_error)  {
		returnWithError( $conn->connect_error );
	}
	else {
		//Create select command for MySQL.
		$sql = $conn->prepare("select UserID,FirstName,LastName from UserTABLE where UserName = ? and Password = ?");
		
		//Check if query could be completed.
		if ($sql->bind_param("ss", $login, $password) == false) {
			returnWithError("bind_param failed");
			end;
		}
		
		//Execute MySQL query.
		$sql->execute();
		
		//Search for login result.
		$result = $sql->get_result();
		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$firstName = $row["FirstName"];
			$lastName = $row["LastName"];
			$id = $row["UserID"];
			
			returnWithInfo($firstName, $lastName, $id );
		}
		else {
			returnWithError( "No Records Found" );
		}
		$conn->close();
	}
	
	//Function for deserializing input json data.
	function getRequestInfo() {
		return json_decode(file_get_contents('php://input'), true);
	}

	//Function for sending resultant json data.
	function sendResultInfoAsJson( $obj ) {
		header('Content-type: application/json');
		echo json_encode($obj);
	}
	
	//Function for setting up the return with error.
	function returnWithError( $err ) {
		$retValue = array('id' => 0, 'firstName' => "", 'lastName' => "", 'error' => $err);
		sendResultInfoAsJson( $retValue );
	}
	
	//Function for setting up the return.
	function returnWithInfo( $firstName, $lastName, $id ) {
		$retValue = array('id' => $id, 'firstName' => $firstName, 'lastName' => $lastName, 'error' => "");
		sendResultInfoAsJson( $retValue );
	}
	
?>