<?php

if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = '';
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

header('Content-Type: application/json');

$data = array(
    'headers' => getallheaders(),
    'path' => $_SERVER['REQUEST_URI'],
    'request_method' => $_SERVER['REQUEST_METHOD'],
    'get' => $_GET,
    'post' => $_POST,
    'put' => $_POST,
);

//If the request is a put then get the file contents and try to parse the string into an array
if ($data['request_method'] == 'PUT') {
    parse_str(file_get_contents("php://input"), $put_data);
    $data['put'] = $put_data;
}
echo json_encode($data);
