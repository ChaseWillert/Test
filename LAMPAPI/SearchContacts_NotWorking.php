<?php
	
	//Get deserialized json data.
	$inData = getRequestInfo();
	
	//Set local variables to deserialized json data.
	$search = "%" . inData["search"] . "%";
	$user_id = inData["user_id"];
	
	//Declare local variables for searching.
	$searchResults = "";
	$searchCount = 0;
	
	//Temporarily using Noah's login for MySQL.
	$conn = new mysqli("localhost", "Noah_API", "Noah_API_Password", "NOAH_TEST");
	
	//Check for connection error.
	if ($conn->connect_error) {
		returnWithError( $conn->connect_error );
	} 
	else {
		//Create insert command for MySQL.
		$sql = $conn->prepare("select first from Contacts where first like ? and user_id = ?");
		
		//Check if query could be completed.
		if ($sql->bind_param("si", $search, $user_id) == false) {
			returnWithError("bind_param failed");
			end;
		}
		
		//Execute MySQL query.
		$sql->execute();
		
		//Search for search results.
		$result = $sql->get_result();
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				if( $searchCount > 0 ) {
					$searchResults .= ",";
				}
				$searchCount++;
				$searchResults .= '"' . $row["first"] . '"';
			}
			
			//Return with no errors.
			returnWithInfo( $searchResults );
		}
		
		//No results found.
		else {
			returnWithError( "No Records Found" );
		}
		
		//Close the connection.
		$conn->close();
	}
	
	//Function for deserializing input json data.
	function getRequestInfo() {
		return json_decode(file_get_contents('php://input'), true);
	}
	
	//Function for sending resultant json data.
	function sendResultInfoAsJson( $obj ) {
		header('Content-type: application/json');
		echo $obj;
	}
	
	//Function for setting up the return with error.
	function returnWithError( $err ) {
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	//Function for setting up the return.
	function returnWithInfo( $searchResults ) {
		$retValue = '{"results":[' . $searchResults . '],"error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>