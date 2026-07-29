<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Temporal\Client\ClientOptions;
use Temporal\SampleUtils\DeclarationLocator;
use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowClient;
use Symfony\Component\Console\Application;
use Temporal\SampleUtils\Command;

require __DIR__ . '/vendor/autoload.php';

// finds all available workflows, activity types and commands in a given directory
$declarations = DeclarationLocator::create(__DIR__ . '/src/');

$host = getenv('TEMPORAL_ADDRESS')
    ?: getenv('TEMPORAL_CLI_ADDRESS')
    ?: 'localhost:7233';
$namespace = getenv('TEMPORAL_NAMESPACE') ?: ClientOptions::DEFAULT_NAMESPACE;

$workflowClient = WorkflowClient::create(
    ServiceClient::create($host),
    (new ClientOptions())->withNamespace($namespace),
);

$app = new Application('Temporal PHP-SDK Samples');

foreach ($declarations->getCommands() as $command) {
    $app->add(Command::create($command, $workflowClient));
}

$app->run();
