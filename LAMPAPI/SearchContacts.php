<?php
	
	//Get deserialized json data.
	$inData = getRequestInfo();
	
	//Set local variables to deserialized json data.
	$search = inData["search"];
	$userId = $inData["userId"];
	
	//Declare local variables for search.
	$searchResults = "";
	$searchCount = 0;
	
	//Temporarily using test login for MySQL.
	$conn = new mysqli("localhost", "Test", "Test_Pass", "ContactManager");
	if ($conn->connect_error) {
		returnWithError( $conn->connect_error );
	} 
	else {
		/*  Prepared statement temporarily not used as it won't work with the while loop.
		//Create insert command for MySQL. Temporarily have test values for DateCreated and DateLastIn.
		$sql = $conn->prepare("select `First Name`,`Last Name`,Email,`Phone Number`,`Street Address`,City,State,`Zip Code`,Notes from `Contact Table` where (`First Name` like ? or `Last Name` like ? or Email like ? or `Phone Number` like ? or `Street Address` like ? or City like ? or State like ? or `Zip Code` like ? or Notes like ?) and `User ID` = ?");
		
		//Check if query could be completed.
		if ($sql->bind_param("sssssssssi", $search, $search, $search, $search, $search, $search, $search, $search, $search, $userId) == false) {
			returnWithError("bind_param failed");
			end;
		}
		
		//Execute MySQL query.
		$sql->execute();
		
		$result = $sql->get_result();
		*/
		
		//MySQL query.
		$sql = "select `Contact ID`,`First Name`,`Last Name`,Email,`Phone Number`,`Street Address`,City,State,`Zip Code`,Notes from `Contact Table` where (`First Name` like '%" . $inData["search"] . "%' or `Last Name` like '%" . $inData["search"] . "%' or Email like '%" . $inData["search"] . "%' or `Phone Number` like '%" . $inData["search"] . "%' or `Street Address` like '%" . $inData["search"] . "%' or City like '%" . $inData["search"] . "%' or State like '%" . $inData["search"] . "%' or `Zip Code` like '%" . $inData["search"] . "%' or Notes like '%" . $inData["search"] . "%') and `User ID`=" . $inData["userId"];
		$result = $conn->query($sql);
		
		//Create contact array for result json.
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				if( $searchCount > 0 ) {
					$searchResults .= ",";
				}
				$searchCount++;
				$searchResults .= "[";
				$searchResults .= '"' . $row["Contact ID"] . '"';
				$searchResults .= ',"' . $row["First Name"] . '"';
				$searchResults .= ',"' . $row["Last Name"] . '"';
				$searchResults .= ',"' . $row["Email"] . '"';
				$searchResults .= ',"' . $row["Phone Number"] . '"';
				$searchResults .= ',"' . $row["Street Address"] . '"';
				$searchResults .= ',"' . $row["City"] . '"';
				$searchResults .= ',"' . $row["State"] . '"';
				$searchResults .= ',"' . $row["Zip Code"] . '"';
				$searchResults .= ',"' . $row["Notes"] . '"';
				$searchResults .= "]";
			}
			
			returnWithInfo( $searchResults );
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
		$retValue = array('results' => "[" . $searchResults . "]", 'error' => $err);
		sendResultInfoAsJson( $retValue );
	}
	
	//Function for setting up the return.
	function returnWithInfo( $searchResults ) {
		$retValue = array('results' => "[" . $searchResults . "]", 'error' => "");
		sendResultInfoAsJson( $retValue );
	}
	
?>