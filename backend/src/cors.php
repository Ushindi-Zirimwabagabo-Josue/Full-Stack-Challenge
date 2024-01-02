<?php

// Set CORS headers
function handleCorsHeaders()
{
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
    header('Access-Control-Allow-Credentials: true');
}

// Handle preflight CORS requests
function handlePreflightRequest()
{
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        handleCorsHeaders();
        http_response_code(204);
        exit;
    }
}

handleCorsHeaders();
handlePreflightRequest();
