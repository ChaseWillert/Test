<?php
	
	//Get deserialized json data.
	$json = file_get_contents('php://input');
    $data = json_decode($json);
		
	//Temporarily using test login for MySQL.
	$conn = new mysqli("localhost", "Test", "Test_Pass", "ContactManager");
	
	//Check for connection error.
	if ($conn->connect_error)  
    {
		returnWithError( $conn->connect_error );
	}
	else 
    {
		//Create delete command for MySQL.
		$sql = "delete from `Contact Table` where `Contact ID` = " . $data->contactId;
		
		$result = $conn->query($sql);
		
		returnWithError($result);
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