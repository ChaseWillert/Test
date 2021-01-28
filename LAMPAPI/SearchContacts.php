<?php

	$inData = getRequestInfo();
	
	$searchResults = "";
	$searchCount = 0;

	$conn = new mysqli("localhost", "Noah_API", "Noah_API_Password", "NOAH_TEST");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$sql = "select first from Contacts where (first like '%" . $inData["search"] . "%' or last like '%" . $inData["search"] . "%') and user_id=" . $inData["user_id"];
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
				$searchResults .= '"' . $row["first"] . '"';
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