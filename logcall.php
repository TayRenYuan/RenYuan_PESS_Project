<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Police Emergency Service System</title>
<link rel="stylesheet" href="styles.css">
	<link rel="shortcut icon" href="Icon.jpg"/>

<script>
function validateForm() {
  var x = document.forms["frmLogCall"]["rycontactNo"].value;
  if (isNaN(x) || x < 60000000 || x > 99999999) {
    alert("Contact Number is incorrect!\n Please re-enter a 8 Digital Numbers again.\n *The Number must start with 6-9!");
    return false;
  }
}
</script>
<style>
body {background-color: ;}	
</style>
	
</head>

<?php $page = 'rylogcall'; require 'nav.php';?>
<?php require 'db_config.php';
	
	$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	
	if($mysqli->connect_errno) {
		die("Failed to connect to MySQL: ".$mysqli->connect_errno);
	}
	
$sql = "SELECT * FROM incidenttype";
	//Run sql command in $sql
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
	
	$incidentType; // an array variable
	
	while  ($row = $resultset->fetch_assoc()) 
	{
		$incidentType[$row['incidentTypeId']] = $row['incidentTypeDesc'];
	}
	
	$stmt->close();
	$resultset->close();
	$mysqli->close();
	?>
	<fieldset>
	<legend>Log Call</legend>
		<form name="frmLogCall"	method="post" action="dispatch.php" onsubmit="return validateForm();">
			<table width="40%" border="1" align="center" cellpadding="4" cellspacing="4">
			<tr>
			<td width="50%">Caller's Name :</td>
			<td width="50%"><input type="text" name="rycallerName" id="rycallerName" placeholder="Please Enter the Caller's Name" size="28" pattern="[A-Za-z\s]{0,}" title="* Letters Only!"></td> <!--required-->
			</tr>
			<tr>
			<td width="50%">* Contact No :</td>
			<td width="50%"><input type="text" name="rycontactNo" id="rycontactNo" placeholder="Please Enter the Caller's Contact No" size="28"></td>
			</tr>
			<tr>
			<td width="50%">* Location :</td>
			<td width="50%"><input type="text" name="ryincidentLocation" id="ryincidentLocation" placeholder="Please Enter the Incident Location" size="28" required></td>
			</tr>
			<tr>
			<td width="50%">* Incident Type :</td>
			<td width="50%"><select name="ryincidentType" id="ryincidentType">
				<?php 
				foreach($incidentType as $key=> $value) {?>
				<option value="<?php echo $key?> " >
					<?php echo $value ?> </option>
				<?php } ?>
				</select>
				</td>
			</tr>
			<tr>
			<td width="50%">* Description :</td>
			<td width="50%"><textarea name="ryincidentDesc" id="ryincidentDesc" cols="50" rows="5" placeholder="Please Enter the Incident Description" required></textarea></td>
			</tr>
			</table>
				<div align="center" class="buttonB">
					
					<input class="button" type="reset" name="blankPage" id="blankPage" value="Reset">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				
					<input class="button" type="submit" name="nextPage" id="nextPage" value="Process Call">
				
				</div>
		</form>
	</fieldset>
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
