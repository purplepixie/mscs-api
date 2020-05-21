<?php
/* API Scaffold Example in PHP */

// API Error - this is going to make an array containing an error message for us to send
function make_api_error($httpcode, $errorcode, $message)
{
    $a = array(
		"type" => "error",
        "error" => true,
		"httpcode" => $httpcode,
        "errorcode" => $errorcode,
        "message" => $message
    );
    return $a;
}

// Throw an error - make the error, JSON encode, send (echo), exit
function throw_api_error($httpcode, $errorcode, $message)
{
	$err = make_api_error($httpcode,$errorcode,$message);
	if ($httpcode != 200)
		http_response_code($httpcode);
	echo json_encode($err);
	exit();
}

// General API response (JSON encode, echo, exit)
function api_response($response)
{
	echo json_encode($response);
	exit();
}

// CORS allow - https://developer.mozilla.org/en-US/docs/Glossary/Preflight_request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS')
{
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: token, Content-Type');
    header('Access-Control-Max-Age: 1728000');
    header('Content-Length: 0');
    header('Content-Type: text/plain');
    exit();
}


ob_start();

// General response holder for consistency - this array will form the basis
// of all responses. It means for example we don't need to replicate an error
// flag in each response. We can extend the array simply i.e.:
// $response['newitem']=$newvalue;
$response = array(
	"type" => "response",
	"error" => false,
	"httpcode" => 200
);

// CORS
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$scriptname = "api.php"; // the name of our script (this script!)

$uri = $_SERVER['REQUEST_URI'];
$route = substr($uri,strpos($uri,$scriptname)+strlen($scriptname));
if ($route[0] == "/")
	$route=substr($route,1);
if (strpos($route,"?")!==false)
	$route=substr($route,0,strpos($route,"?"));
$routeparts = explode("/",$route);
$mainroute = $routeparts[0];

// So at this point we should have:
// $mainroute - the first route item i.e. api.php/X/Y would be // X
// $routeparts - array of each item in the route i.e. api.php/X/Y/Z would be [0]=>X, [1]=>Y, [2]=>Z
// $route - original route in string form

// ACTUAL API LOGIC BELOW

if ($mainroute == "error") // let's make an example error
{
    throw_api_error(403,"ERROR","Some error found");
}

if ($mainroute == "hello") // we want to say hello to the person passed in the "name" parameter
{
    $name = $_REQUEST['name'];
    $response['message']="Hello ".$name;
    api_response($response);
}

if ($mainroute == "addroute") // addition with them in the route /addroute/X/Y
{
    /* The add structure should be:
    /add/X/Y where we want to add X and Y together
    Note: if X or Y are not present or not numeric we'll return an error
    */
    if (count($routeparts)!=3) // 3 is /add/X/Y
        throw_api_error(400,"PARAMCOUNT","Wrong parameter count");
    $x=$routeparts[1];
    $y=$routeparts[2];

    if (!is_numeric($x))
        throw_api_error(400,"NONUMERIC","Not a numeric parameter");

    if (!is_numeric($y))
        throw_api_error(400,"NONUMERIC","Not a numeric parameter");

    $answer = $x+$y;
    $str = $x."+".$y."=".$answer;

    $response['x']=$x;
    $response['y']=$y;
    $response['answer']=$answer;
    $response['string']=$str;

    api_response($response);
}

if ($mainroute == "add") // addition as parameters
{
    /* The add structure should be:
    /add
    With parameters X and Y
    */
    if (!isset($_REQUEST['x']) || !isset($_REQUEST['y']))
        throw_api_error(400,"PARAMCOUNT","Wrong parameter count");
    $x=$_REQUEST['x'];
    $y=$_REQUEST['y'];

    if (!is_numeric($x))
        throw_api_error(400,"NONUMERIC","Not a numeric parameter");

    if (!is_numeric($y))
        throw_api_error(400,"NONUMERIC","Not a numeric parameter");

    $answer = $x+$y;
    $str = $x."+".$y."=".$answer;

    $response['x']=$x;
    $response['y']=$y;
    $response['answer']=$answer;
    $response['string']=$str;

    api_response($response);
}

// We get here so no valid route was found
throw_api_error(404,"NOROUTE","Route not found");
