<?php
$app->get('/dashboards', function ($request, $response, $args) {
	$phpef = ($request->getAttribute('phpef')) ?? new phpef();
	$phpef->api->setAPIResponseData($phpef->dashboard->buildDashboard($args['name']));
    // Return the response
    $response->getBody()->write($GLOBALS['api']['data']);
	return $response
    ->withHeader('Content-Type', 'text/html')
    ->withStatus($GLOBALS['responseCode']);
});

$app->get('/dashboards/page/{name}', function ($request, $response, $args) {
	$phpef = ($request->getAttribute('phpef')) ?? new phpef();
	$phpef->api->setAPIResponseData($phpef->dashboard->buildDashboard($args['name']));
    // Return the response
    $response->getBody()->write($GLOBALS['api']['data']);
	return $response
    ->withHeader('Content-Type', 'text/html')
    ->withStatus($GLOBALS['responseCode']);
});

$app->get('/dashboards/widgets', function ($request, $response, $args) {
	$phpef = ($request->getAttribute('phpef')) ?? new phpef();
	$phpef->api->setAPIResponseData($phpef->dashboard->getWidgets());

    $response->getBody()->write(jsonE($GLOBALS['api']));
	// Return the response
	return $response
		->withHeader('Content-Type', 'application/json')
		->withStatus($GLOBALS['responseCode']);
});