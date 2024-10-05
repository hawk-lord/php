<?php

// Note that the array is not only populated for GET requests, but rather for all requests with a query string.
$method = $_GET['method'];


$host="localhost";
$port=3306;
$socket="";
$user="alko1";
$password="alko1234";
$dbname="test1";

// Use the MySQL Improved Extension
$mysqli = new mysqli($host, $user, $password, $dbname, $port, $socket)
or die ('Could not connect to the database server' . mysqli_connect_error());

if ($method === "loadTable") {
    $sql = "SELECT * FROM test1.alko";
    $data = $mysqli->execute_query($sql)->fetch_all(MYSQLI_ASSOC);
    
    echo "<table>";
    echo "<tr>";
    echo "<th>Numero</th>";
    echo "<th>Nimi</th>";
    echo "<th>Pullokoko</th>";
    echo "<th>Hinta/EUR</th>";
    echo "<th>Hinta/GBP</th>";
    echo "<th>Aikaleima</th>";
    echo "<th colspan='3'>Tilata</th>";
    echo "<tr>";
    foreach($data as $row) {
        echo "<tr>";
        echo "<td>" . $row['number'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['bottlesize'] . "</td>";
        echo "<td>" . $row['price'] . "</td>";
        echo "<td>" . $row['priceGBP'] . "</td>";
        echo "<td>" . $row['timestamp'] . "</td>";
        echo "<td id='orderamount" . $row['number'] . "'>" . $row['orderamount'] . "</td>";
        echo "<td><input type='submit' value='Add' onclick='addAmount(" . $row['number'] . ")' /></td>";
        echo "<td><input type='submit' value='Clear' onclick='clearAmount(" . $row['number'] . ")' /></td>";
        echo "<tr>";
    }
    echo "</table>";
}
elseif ($method === "addAmount") {
    $number = $_GET['number'];
    $stmt = $mysqli->prepare("UPDATE test1.alko SET orderamount = orderamount + 1 WHERE number = ?");
    $stmt->bind_param("i", $number);
    $stmt->execute();
    $sql = "SELECT orderamount FROM test1.alko WHERE number = ?";
    $data = $mysqli->execute_query($sql, [$number])->fetch_column(0);
    echo $data;
}

elseif ($method === "clearAmount") {
    $number = $_GET['number'];
    $number = $_GET['number'];
    $stmt = $mysqli->prepare("UPDATE test1.alko SET orderamount = 0 WHERE number = ?");
    $stmt->bind_param("i", $number);
    $stmt->execute();
    echo 0;
}
?>
