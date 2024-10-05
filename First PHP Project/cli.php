<?php
echo "Begin script.\n";

if (!array_key_exists(1, $argv)) {
    die("currency-layer-api-key is missing");
}

parse_str($argv[1], $arg);

if (isset($arg['currency-layer-api-key'])) {
    $currency_layer_api_key = $arg['currency-layer-api-key'];
}
else {
    die("currency-layer-api-key is missing");
}

// http instead of https to avoid SSL certificate problem: unable to get local issuer certificate
$url_currency_layer = "http://api.currencylayer.com/convert?from=EUR&to=GBP&amount=1&access_key=" . $currency_layer_api_key;


$data_string = file_get_contents($url_currency_layer);



// When true, JSON objects will be returned as associative arrays; when false, JSON objects will be returned as objects
$associative = false;
$data_json = json_decode($data_string, $associative); // decode the JSON feed

if ($data_json->success) {
    file_put_contents("currency-layer-response.json", $data_string);
    echo "Success reading Currency Layer. \n";
}
else {
    file_put_contents("currency-layer-response-error.json", $data_string);
    echo "Fail to read Currency Layer. Using old currency rate. \n";
    echo "Error code: ", $data_json->error->code, "\n";
    echo "Error info: ", $data_json->error->info, "\n";
    $data_string = file_get_contents('currency-layer-response.json');
    $data_json = json_decode($data_string, $associative); // decode the JSON feed
}


$eur_gbp = $data_json->result;



$url_alko = 'https://www.alko.fi/INTERSHOP/static/WFS/Alko-OnlineShop-Site/-/Alko-OnlineShop/fi_FI/Alkon%20Hinnasto%20Tekstitiedostona/alkon-hinnasto-tekstitiedostona.xlsx';

// Use basename() function to return the base name of file
$file_name_alko = basename($url_alko);

// Use file_get_contents() function to get the file
// from url and use file_put_contents() function to
// save the file by using base name
if (file_put_contents($file_name_alko, file_get_contents($url_alko)))
{
    echo "Alko file downloaded successfully.\n";
}
else
{
    echo "Alko file downloading failed.\n";
}


// Set up connection to the MySQL database.
// But do not use clear text password in production system
$host="localhost";
$port=3306;
$socket="";
$user="alko1";
$password="alko1234";
$dbname="test1";

// Use the MySQL Improved Extension
$mysqli = new mysqli($host, $user, $password, $dbname, $port, $socket)
or die ('Could not connect to the database server' . mysqli_connect_error());

// Use a prepared statement to avoid SQL injection possibility
//$stmt = $mysqli->prepare("REPLACE INTO test1.alko(number, name, bottlesize, price, priceGBP) VALUES (?, ?, ?, ?, ?)");
$sql_insert = "INSERT INTO test1.alko(number, name, bottlesize, price, priceGBP) VALUES (?, ?, ?, ?, ?) "
. "ON DUPLICATE KEY UPDATE price=?, priceGBP=?";
$stmt = $mysqli->prepare($sql_insert);
$stmt->bind_param("issdddd", $number, $name, $bottlesize, $price, $price_gbp, $price, $price_gbp);



// Use PhpSpreadsheet to handle the downloaded Excel spreadsheet.
// To install: composer require phpoffice/phpspreadsheet
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// No memory limit because the spreadsheet needs a lot of memory. Should be handled differently in production.
ini_set('memory_limit', '-1');

$inputFileType = 'Xlsx';

$reader = IOFactory::createReader($inputFileType);

$reader->setReadDataOnly(true);
$reader->setReadEmptyCells(false);

$spreadsheet = $reader->load($file_name_alko);

echo "spreadsheet created successfully.\n";


// When the spreadsheet is loaded, iterate through the rows and read columns A B D E.

$worksheet = $spreadsheet->getSheetByName('Alkon Hinnasto Tekstitiedostona');

$rowIterator = $worksheet->getRowIterator();

// Row counter so we can ignore the first rows that have no data.
$nb = 0;
foreach ($rowIterator as $row) {
    $nb++;
    if ($row->isEmpty() || $nb < 5 ) {
        continue;
    }
    
    // The number may have leading zeroes. They are ignored in the script to consume less storage and improve performance.
    $number = $worksheet->getCell("A$nb")->getValue();
    $name = $worksheet->getCell("B$nb")->getValue();
    $bottlesize = $worksheet->getCell("D$nb")->getValue();
    $price = $worksheet->getCell("E$nb")->getValue();
    $price_gbp = $price * $eur_gbp;
    
    // Execute the prepared statement with the values in this row.
    $stmt->execute();
}

echo "\nEnd script.\n";

?>