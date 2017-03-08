<?php
if(!isset($_GET['latitude']) || !isset($_GET['longitude']) || !isset($_GET['radius'])) {
  http_response_code(401);
  $resultArray = array("error" => "Missing parameters. Parameters needed: latitude, longitude and radius");
  echo json_encode($resultArray);
  die();
}

$latitude = $_GET['latitude'];
$longitude = $_GET['longitude'];
$radius = $_GET['radius'];
$user = 'root';
$password = 'root';
$db = 'mysqldb';
$host = 'localhost';

$link = mysqli_connect(
   $host,
   $user,
   $password,
   $db
);

if (mysqli_connect_errno()) {
    printf("Connect failed: %s<br>", mysqli_connect_error());
    die();
}

$sql = "SELECT *, (
      6373000 * acos (
      cos ( radians( $latitude ) )
      * cos( radians( latitude ) )
      * cos( radians( longitude ) - radians( $longitude ) )
      + sin ( radians( $latitude ) )
      * sin( radians( latitude ) )
    )
) AS distance
FROM positions
HAVING distance < $radius";
$result = mysqli_query($link, $sql);
$resultArray = array();
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      http_response_code(202);
      // echo '{"result":"'.$row["path"].'"}';
      array_push($resultArray,$row);
    }
    echo json_encode($resultArray);
}
else {
  http_response_code(401);
  $resultArray = array("error" => "No location found");
  echo json_encode($resultArray);
}
$conn->close();
?>
