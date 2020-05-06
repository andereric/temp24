<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "temp24";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if($_GET['temperature']){
    $sql ="INSERT INTO temperatures (uid,sid,temperature)
VALUES ('".$_GET["uid"]."','".$_GET["sid"]."','".$_GET["temperature"]."')";

if (mysqli_query($conn, $sql)) {
        //echo "New record created successfully";
    } else {
        //echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
$conn->close();
?>