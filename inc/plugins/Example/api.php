<?php
// **
// USED TO DEFINE CUSTOM API ROUTES
// **
$app->get('/plugins/example/settings', function ($request, $response, $args) {
	$examplePlugin = new examplePlugin();
	// if ($examplePlugin->checkAccess('ACL-PLUGIN-...')) {
		$examplePlugin->api->setAPIResponseData($examplePlugin->_pluginGetSettings());
	// }
	$response->getBody()->write(jsonE($GLOBALS['api']));
	return $response
		->withHeader('Content-Type', 'application/json;charset=UTF-8')
		->withStatus($GLOBALS['responseCode']);
});