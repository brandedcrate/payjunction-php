<?php
header('Content-Type: application/json');
$data = array(
    'headers' => getallheaders(),
//    'server' => $_SERVER,
    'request_method' => $_SERVER['REQUEST_METHOD'],
    'get' => $_GET,
    'post' => $_POST,
    'put' => $_POST,
);
//If the request is a put then get the file contents and try to parse the string into an array
if($data['request_method'] == 'PUT')
{
    parse_str(file_get_contents("php://input"), $put_data);
    $data['put'] = $put_data;
}
echo json_encode($data);

