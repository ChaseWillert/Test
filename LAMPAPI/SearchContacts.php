<?php

	$inData = getRequestInfo();
	
	$search = "%" . inData["search"] . "%";
	$userId = $inData["userId"];
	
	$searchResults = "";
	$searchCount = 0;

	$conn = new mysqli("localhost", "Test", "Test_Pass", "ContactManager");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		/*
		//Create insert command for MySQL. Temporarily have test values for DateCreated and DateLastIn.
		$sql = $conn->prepare("select `First Name` from `Contact Table` where (`First Name` like ? or `Last Name` like ?) and `User ID` = ?");
		
		//Check if query could be completed.
		if ($sql->bind_param("ssi", $search, $search, $userId) == false) {
			returnWithError("bind_param failed");
			end;
		}
		
		//Execute MySQL query.
		$sql->execute();
		
		$result = $sql->get_result();
		*/
		
		$sql = "select `First Name` from `Contact Table` where (`First Name` like '%" . $inData["search"] . "%' or `Last Name` like '%" . $inData["search"] . "%') and `User ID`=" . $inData["user_id"];
		$result = $conn->query($sql);
		if ($result->num_rows > 0)
		{
			while($row = $result->fetch_assoc())
			{
				if( $searchCount > 0 )
				{
					$searchResults .= ",";
				}
				$searchCount++;
				$searchResults .= '"' . $row["First Name"] . '"';
			}
			
			returnWithInfo( $searchResults );
		}
		else
		{
			returnWithError( "No Records Found" );
		}
		$conn->close();
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $searchResults )
	{
		$retValue = '{"results":[' . $searchResults . '],"error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>