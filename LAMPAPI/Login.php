<?php

	$inData = getRequestInfo();
	
	$id = 0;
	$firstName = "";
	$lastName = "";

	$conn = new mysqli("localhost", "Noah_API", "Noah_API_Password", "NOAH_TEST");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
        
        
		$sql = $conn->prepare("SELECT id, user_id FROM Users WHERE user_id=? AND user_password=?");
        #$sql = $conn->prepare("SELECT * FROM Users");
        
        $user = $_POST['username'];
        $pass = $_POST['password'];
        
        if ($sql->bind_param("ss", $user, $pass) == false)
        {
            returnWithError("bind_param failed");
            end;
        }
        
        #echo $conn->error;die;
        $sql->execute();
        
		$result = $sql->get_result();
		
        if ($result->num_rows > 0)
		{
			$row = $result->fetch_array();
			$id = $row["id"];
			$user_id = $row["user_id"];
			
			returnWithInfo($id, $user_id);
		}
		else
		{
			returnWithError( "No Records Found" );
		}
        
        
        $sql->close();
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
	
	function returnWithInfo( $firstName, $lastName)
	{
		$retValue = '{"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>