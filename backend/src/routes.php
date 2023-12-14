<?php

define('HTTP_GET', 'GET');
define('HTTP_PUT', 'PUT');
define('HTTP_DELETE', 'DELETE');
define('HTTP_OPTIONS', 'OPTIONS');

function getReports()
{
    $reportsJson = file_get_contents(__DIR__ . '/../../data/reports.json');
    return json_decode($reportsJson, true);
}

$reports = getReports()['elements'];

// handle CORS headers
function handleCorsHeaders()
{
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type');
}

// send JSON response
function sendJsonResponse($data)
{
    handleCorsHeaders();
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}

// handle 404
function handleNotFound()
{
    http_response_code(404);
    sendJsonResponse(['error' => 'Report not found']);
}

// get report ID
function getReportIdFromPath()
{
    preg_match('/\/reports\/([^\/]+)/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), $matches);
    return isset($matches[1]) ? $matches[1] : null;
}

function findReportById($reports, $reportId)
{
    foreach ($reports as &$report) {
        if ($report['id'] === $reportId) {
            return $report;
        }
    }

    return null;
}

// get all reports
if ($_SERVER['REQUEST_METHOD'] === HTTP_GET && parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '/reports') {
    sendJsonResponse(['elements' => $reports]);
}

// block a report
if ($_SERVER['REQUEST_METHOD'] === HTTP_OPTIONS) {
    handleCorsHeaders();
    header('Access-Control-Allow-Methods: ' . HTTP_DELETE);
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === HTTP_DELETE && ($reportId = getReportIdFromPath()) !== null) {
    $report = findReportById($reports, $reportId);
    if (!$report) {
        handleNotFound();
    }

    sendJsonResponse(['message' => 'Content blocked successfully']);
}

// resolve a report
if ($_SERVER['REQUEST_METHOD'] === HTTP_OPTIONS) {
    handleCorsHeaders();
    header('Access-Control-Allow-Methods: ' . HTTP_PUT);
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === HTTP_PUT && ($reportId = getReportIdFromPath()) !== null) {
    $report = findReportById($reports, $reportId);
    if (!$report) {
        handleNotFound();
    }

    $report['state'] = 'CLOSED';

    sendJsonResponse($report);
} else {
    handleNotFound();
}
