<?php

require_once 'cors.php';

handleCorsHeaders();

handlePreflightRequest();

define('REPORTS_FILE', '../../data/reports.json');

function readReports()
{
    try {
        $reports = json_decode(file_get_contents(REPORTS_FILE), true);
    } catch (Exception $e) {
        error_log('Error reading reports file: ' . $e->getMessage());
        sendJsonResponse(['error' => 'Failed to read reports file'], 500);
        return [];
    }

    return $reports ?? [];
}

function writeReports($reports)
{
    file_put_contents(REPORTS_FILE, json_encode($reports, JSON_PRETTY_PRINT));
}

function sendJsonResponse($data, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// handle 404
function handleNotFound()
{
    sendJsonResponse(['error' => 'Report not found'], 404);
}

// Find a report by ID
function findReportById($reports, $reportId)
{
    foreach ($reports['elements'] as &$report) {
        if ($report['id'] === $reportId) {
            return $report;
        }
    }

    return null;
}

// handle GET /reports
function getReports()
{
    $reports = readReports();
    sendJsonResponse($reports['elements']);
}

// handle PUT /reports/{id}
function updateReport($reportId)
{
    $reports = readReports();

    $requestBody = json_decode(file_get_contents('php://input'), true);
    $ticketState = $requestBody['ticketState'] ?? 'OPEN';

    $report = findReportById($reports, $reportId);

    if (!$report) {
        handleNotFound();
    }

    $report['state'] = $ticketState;

    writeReports($reports);

    sendJsonResponse($report);
}

// handle DELETE /reports/block/{id}
function blockReport($reportId)
{
    $reports = readReports();

    $report = findReportById($reports, $reportId);

    if (!$report) {
        handleNotFound();
    }

    $report['state'] = 'BLOCKED';

    writeReports($reports);

    sendJsonResponse($report);
}

// router
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

if ($method === 'GET' && $path === '/reports') {
    getReports();
} elseif ($method === 'PUT' && preg_match('/\/reports\/(.+)/', $path, $matches)) {
    $reportId = $matches[1];
    updateReport($reportId);
} elseif ($method === 'DELETE' && preg_match('/\/reports\/block\/(.+)/', $path, $matches)) {
    $reportId = $matches[1];
    blockReport($reportId);
} else {
    handleNotFound();
}
