<?php
require_once("DataController.php");

function GetParseParams($params, $assoc = false) {
    $result = NULL;
    if (is_array($params)) {
        $result = array();
        foreach ($params as $key => $value) {
            $result[$key] = json_decode($params[$key], $assoc);
            if ($result[$key] === NULL) {
                $result[$key] = $params[$key];
            }
        }
    } 
    else {
        $result = $params;
    }
    return $result;
}
function GetParamsFromInput() {
    $result = NULL;
    $content = file_get_contents("php://input");
    if ($content !== false) {
        $params = array();
        parse_str($content, $params);
        $result = GetParseParams($params, true); 
    } 
    return $result;
}
$response = NULL;
$controller = new DataController();
$controller->FillDbIfEmpty();
switch($_SERVER["REQUEST_METHOD"]) {
    case "GET": {
        $params = GetParseParams($_GET);
        $response = $controller->Get($params); 
        break;
    }
    case "POST": {
        $params = GetParamsFromInput();
        $response = $controller->Post($params["values"]); 
        break;
    }
    case "PUT": {
        $params = GetParamsFromInput();
        $response = $controller->Put($params["key"], $params["values"]); 
        break;
    }
    case "DELETE": {
        $params = GetParamsFromInput();       
        $response = $controller->Delete($params["key"]); 
        break;
    }
}
unset($controller);
if (isset($response) && !is_string($response)) {
    header("Content-type: application/json");
    echo json_encode($response);
}
else {
    header("HTTP/1.1 500 Internal Server Error");
    header("Content-Type: application/json");
    echo json_encode(array("message" => $response, "code" => 500));
}
