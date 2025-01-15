<?php
$app->get('/dashboards', function ($request, $response, $args) {
	$phpef = ($request->getAttribute('phpef')) ?? new phpef();
	if ($phpef->auth->checkAccess("ADMIN-CONFIG")) {
		$phpef->api->setAPIResponseData(array_values($phpef->dashboard->getDashboards()));
	}
    // Return the response
    $response->getBody()->write(jsonE($GLOBALS['api']));
	return $response
    ->withHeader('Content-Type', 'application/json')
    ->withStatus($GLOBALS['responseCode']);
});

$app->get('/dashboards/settings', function ($request, $response, $args) {
	$phpef = ($request->getAttribute('phpef')) ?? new phpef();
	if ($phpef->auth->checkAccess("ADMIN-CONFIG")) {
		$phpef->api->setAPIResponseData($phpef->dashboard->getDashboardSettings());
	}
    // Return the response
    $response->getBody()->write(jsonE($GLOBALS['api']));
	return $response
    ->withHeader('Content-Type', 'application/json')
    ->withStatus($GLOBALS['responseCode']);
});

$app->get('/dashboards/widgets', function ($request, $response, $args) {
	$phpef = ($request->getAttribute('phpef')) ?? new phpef();
	if ($phpef->auth->checkAccess("ADMIN-CONFIG")) {
		$phpef->api->setAPIResponseData(array_values($phpef->dashboard->getWidgets()));
	}

    $response->getBody()->write(jsonE($GLOBALS['api']));
	// Return the response
	return $response
		->withHeader('Content-Type', 'application/json')
		->withStatus($GLOBALS['responseCode']);
});

$app->get('/dashboards/widgets/{widget}/settings', function ($request, $response, $args) {
	$phpef = ($request->getAttribute('phpef')) ?? new phpef();
	if ($phpef->auth->checkAccess("ADMIN-CONFIG")) {
		$phpef->api->setAPIResponseData($phpef->dashboard->getWidgetSettings($args["widget"]));
	}

    $response->getBody()->write(jsonE($GLOBALS['api']));
	// Return the response
	return $response
		->withHeader('Content-Type', 'application/json')
		->withStatus($GLOBALS['responseCode']);
});