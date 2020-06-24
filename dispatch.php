<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Police Emergency Service System</title>
<link rel="stylesheet" href="styles.css">
	<link rel="shortcut icon" href="Icon.jpg"/>
<style>
	.patrolcar{
  padding: 35px;
}
</style>
</head>

<body>
<?php $page = 'rylogcall'; require 'nav.php';?>

	
<?php // if post back
if (isset($_POST["dispatchPage"]))
{
	require_once 'db_config.php';
	
	// create database connection
	$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	// check connection
	if($mysqli->connect_errno)
	{
		die("Failed to connect to MySQL: ".$mysqli->connect_errno);
	}
	$patrolcarDispatched = $_POST["chkPatrolcar"]; // array of patrolcar being dispatched from post back
	$numOfPatrolcarDispatched = count($patrolcarDispatched);
	
	// insert new incident
	$incidentStatus;
	if($numOfPatrolcarDispatched > 0)
	{
		$incidentStatus='2'; // incident status to be set as Dispatched
	} else
	{
		$incidentStatus='1'; // incident status to be set as Pending
	}
	
	$sql = "INSERT INTO incident (callerName, phoneNumber, incidentTypeId, incidentLocation, incidentDesc, incidentStatusId) VALUES (?, ?, ?, ?, ?, ?)";
	
	if (!($stmt = $mysqli->prepare($sql)))
	{
		die("Prepare failed: ".$mysqli->errno);
	}
	
	if(!$stmt->bind_param('ssssss', $_POST['rycallerName'], $_POST['rycontactNo'], $_POST['ryincidentType'], $_POST['ryincidentLocation'], $_POST['ryincidentDesc'], $incidentStatus))
	{
		die("Binding parameters failed: ".$stmt->errno);
	}
	
	if (!$stmt->execute())
	{
		die("Insert incident table failed: ".$stmt->errno);
	}
// retrieve incident_id for the newly inserted incident
		$incidentId=mysqli_insert_id($mysqli);;
		
		//update patrolcar status table and add into dispatch table
		for($i=0; $i < $numOfPatrolcarDispatched; $i++)
			
	{
		// update patrol car status
		$sql = "Update patrolcar SET patrolcarStatusId='1' WHERE patrolcarId = ?";
		
		if (!($stmt = $mysqli->prepare($sql)))
		{
			die("Prepare failed: ".$mysqli->errno);
		}
		
		if (!$stmt->bind_param('s', $patrolcarDispatched[$i]))
		{
			die("Binding parameters failed: ".$stmt->errno);
		}
			
		if (!$stmt->execute())
		{
			die("Update patrolcar_status table failed: ".$stmt->errno);
		}
			
		//insert dispatch data
		$sql = "INSERT INTO dispatch (incidentId, patrolcarId, timeDispatched) VALUES (?, ?, NOW())";
		
		if (!($stmt = $mysqli->prepare($sql)))
		{
			die("Prepare failed: ".$mysqli->errno);
		}
			
		if (!$stmt->bind_param('ss', $incidentId,
							  		$patrolcarDispatched[$i]))
		{
			die("Binding parameters failed: ".$stmt->errno);
		}
			
		if(!$stmt->execute())
		{
			die("Insert dispatch table failed: ".$stmt->errno);
		}
	}
	$stmt->close();
	$mysqli->close();
}
	
?>	
	
	
<!--display the incident information passed from logcall.php-->
	
<fieldset>
<legend>Log Call</legend>
<form name="form1" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']);?> ">
<table width="40%" border="1" align="center" cellpadding="4" cellspacing="4">

<tr>
<td colspan="2" align="center" bgcolor="#FF5F5F"><strong>Incident Detail</strong></td>	
</tr>

<tr>
<td width="25%">Caller's Name :</td>
<td width="75%"><?php echo $_POST['rycallerName']?> <input type="hidden" name="rycallerName" id="rycallerName" value="<?php echo $_POST['rycallerName']?>"></td>
</tr>
	
<tr>
<td width="25%">* Contact No :</td>
<td width="75%"><?php echo $_POST['rycontactNo']?> <input type="hidden" name="rycontactNo" id="rycontactNo" value="<?php echo $_POST['rycontactNo']?>"></td>
</tr>
	
<tr>
<td width="25%">* Location :</td>
<td width="75%"><?php echo $_POST['ryincidentLocation']?> <input type="hidden" name="ryincidentLocation" id="ryincidentLocation" value="<?php echo $_POST['ryincidentLocation']?>"></td>
</tr>
	
<tr>
<td width="25%">* Incident Type :</td>
<td width="75%"><?php echo $_POST['ryincidentType']?> <input type="hidden" name="ryincidentType" id="ryincidentType" value="<?php echo $_POST['ryincidentType']?>"></td>
</tr>

<tr>
<td width="25%">* Description :</td>
<td width="75%"><?php echo $_POST['ryincidentDesc']?> <input type="hidden" name="ryincidentDesc" id="ryincidentDesc" value="<?php echo $_POST['ryincidentDesc']?>"></td>
</tr>

</table>
	
	<?php 
// connect to a database
require_once'db_config.php';
	
// create database connection
$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
// check connection
if($mysqli->connect_errno) 
{
	die("Failed to connect to MySQL: ".$mysqli->connect_errno);
}

// retrieve from patrolcar table those patrol cars that are 2:Patrol or 3:Free
$sql = "SELECT patrolcarId, statusDesc FROM patrolcar JOIN patrolcar_status
ON patrolcar.patrolcarStatusId=patrolcar_status.StatusId
WHERE patrolcar.patrolcarStatusId='2' OR patrolcar.patrolcarStatusId='3'";

	if (!($stmt = $mysqli->prepare($sql)))
	{
		die("Prepare failed: ".$mysqli->errno);
	}
	if (!$stmt->execute())
	{
		die("Cannot run SQL command: ".$stmt->errno);
	}
	if(!($resultset = $stmt->get_result()))
	{
		die("No data in resultset: ".$stmt->errno);
	}
	
	$patrolcarArray; // an array variable
	
	while  ($row = $resultset->fetch_assoc()) 
	{
		$patrolcarArray[$row['patrolcarId']] = $row['statusDesc'];
	}
	
	$stmt->close();
	$resultset->close();
	$mysqli->close();
	?>
	
	<!-- populate table with patrol car data-->
	<br>
	<table width="40%" border="1" align="center" cellpadding="4" cellspacing="4">
	<tr>
	<td colspan="3" align="center" bgcolor="#FF5F5F"><strong>Dispatch Patrolcar Panel</strong></td>
	</tr>
	<?php
	foreach($patrolcarArray as $key=>$value){
		?>
	<tr>
	<td>
	<input type="checkbox" name="chkPatrolcar[]"
		  value="<?php echo $key?>"></td>
	<td><?php echo $key?></td>
		<td><?php echo $value?></td>
	</tr>
	<?php } ?>
	</table>
	
	<div align="center" class="buttonB">
	<td><input class="button" type="reset" name="blankPage" id="blankPage" value="Reset"></td>
	<td colspan="2"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="button" name="dispatchPage" id="dispatchPage" value="Dispatch"></td>
	</div>
</form>
</fieldset>
	<br><br><br><br>

	<footer>
	<div class="footer" style="border: none">
		
		<p align="center">
		Copyright &copy; 2020 PESS. All rights reserved.
		&nbsp;&nbsp;&nbsp;&nbsp;
		Created by Ren Yuan.
		&nbsp;&nbsp;&nbsp;&nbsp;
		Email: Send your enquires <a href="mailto:tay_ren_yuan@connect.ite.edu.sg">here</a>.</p>
	</footer>
</body>
</html>