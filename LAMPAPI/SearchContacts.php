<?php
	
	//Get deserialized json data.
	$json = file_get_contents('php://input');
    $data = json_decode($json);
	
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
		$sql = "select `Contact ID`,`First Name`,`Last Name`,Email,`Phone Number`,`Street Address`,City,State,`Zip Code`,Notes from `Contact Table` where (`First Name` like '%" . $data->firstName . "%' and `Last Name` like '%" . $data->lastName . "%' and Email like '%" . $data->email . "%' and `Phone Number` like '%" . $data->phoneNumber . "%' and `Street Address` like '%" . $data->streetAddress . "%' and City like '%" . $data->city . "%' and State like '%" . $data->state . "%' and `Zip Code` like '%" . $data->zipCode . "%' and Notes like '%" . $data->notes . "%') and `User ID`=" . $data->userId;
		$result = $conn->query($sql);
		
        $resultArray = array();
        $tmp;
		//Create contact array for result json.
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				                
				$tmp->row = $row["Contact ID"];
				$tmp->firstName = $row["First Name"];
				$tmp->lastName = $row["Last Name"];
				$tmp->email = $row["Email"];
				$tmp->phoneNumber = $row["Phone Number"];
				$tmp->streetAddress = $row["Street Address"];
				$tmp->city = $row["City"];
				$tmp->state = $row["State"];
				$tmp->zipCode = $row["Zip Code"];
				$tmp->notes = $row["Notes"];
                array_push($resultArray, $tmp);
			}
			
			sendResultInfoAsJson($resultArray);
		}
		else 
        {
            $errormessage = "No Records Founds >>>" . $data->firstName . "<<<";
			returnWithError($errormessage);
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