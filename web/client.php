<?php /*

PHP API Client Example

*/ ?><!DOCTYPE html>
<html>
<head>
    <title>PHP API Client</title>
</head>
<body>
<?php

// Critical note: this is just an example, if you are making regular
// use of PHP to call external APIs you should use cURL or a similar
// proper library with error handling!

function GetPath($apiname) // this function gets the local HTTP path
{
    $ourscript = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $ourdir = substr($ourscript,0,strrpos($ourscript,"/")+1);
    return $ourdir.$apiname;
}

$api = "api.php";

// Testing add
$x = "10";
$y = "20";
echo "<h1>Add</h1>";

$url = GetPath($api) . "/add" . "?x=".$x."&y=".$y; // build the GET query string
echo "URL: ".$url."<br />";

$data=file_get_contents($url);
echo "Returned Raw Data: <pre>\n";
print_r($data);
echo "</pre>";

$json=json_decode($data);
echo "Returned Decoded Data (Object): <pre>\n";
print_r($json);
echo "</pre>";

echo "Answer = ".$json->answer."<br /><br />";

$jsona=json_decode($data,true);
echo "Returned Decoded Data (Array): <pre>\n";
print_r($jsona);
echo "</pre>";

echo "Answer = ".$json['answer']."<br /><br />";
