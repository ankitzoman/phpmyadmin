<?php
/**
 * handles creation of the GIS visualizations.
 *
 * @package PhpMyAdmin
 */
declare(strict_types=1);

use PhpMyAdmin\Controllers\Table\GisVisualizationController;
use PhpMyAdmin\Core;
use PhpMyAdmin\Url;
use PhpMyAdmin\Util;
use Symfony\Component\DependencyInjection\Definition;

if (! defined('PHPMYADMIN')) {
    exit;
}

global $containerBuilder;

$sqlQuery = null;

if (isset($_GET['sql_query'], $_GET['sql_signature'])) {
    if (Core::checkSqlQuerySignature($_GET['sql_query'], $_GET['sql_signature'])) {
        $sqlQuery = $_GET['sql_query'];
    }
} elseif (isset($_POST['sql_query'])) {
    $sqlQuery = $_POST['sql_query'];
}

/* Define dependencies for the concerned controller */
$dependency_definitions = [
    'sql_query' => $sqlQuery,
    'url_params' => &$GLOBALS['url_params'],
    'goto' => Util::getScriptNameForOption(
        $GLOBALS['cfg']['DefaultTabDatabase'],
        'database'
    ),
    'back' => Url::getFromRoute('/sql'),
    'visualizationSettings' => [],
];

/** @var Definition $definition */
$definition = $containerBuilder->getDefinition(GisVisualizationController::class);
array_map(
    static function (string $parameterName, $value) use ($definition) {
        $definition->replaceArgument($parameterName, $value);
    },
    array_keys($dependency_definitions),
    $dependency_definitions
);

/** @var GisVisualizationController $controller */
$controller = $containerBuilder->get(GisVisualizationController::class);
$controller->indexAction();
