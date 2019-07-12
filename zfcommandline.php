#!/usr/bin/env php
<?php
namespace rroyce\Zfcommandline;

require __DIR__ . '/../../autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\Mvc\Application as ZendApplication;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\PhpFileProvider;
use Zend\Code\Generator\ValueGenerator;

use rroyce\zfcommandline\Controller\Plugin\ConsoleParams;
use Zfcommandline\Controller\TsopSchedulerController;
use ZfcommandlineAPI\Model\TsopSchedulerModel;

use rroyce\zfcommandline\Command\ControllerCreateCommand;
use Zfcommandline\Command\ModelCreateCommand;
use Zfcommandline\Command\AppRouteCreateCommand;
use Zfcommandline\Command\ApiRouteCreateCommand;
use Zfcommandline\Command\ViewCreateCommand;
use Zfcommandline\Command\DbQueryGenerateCommand;

$appConfig = require __DIR__ . '/../../../config/application.config.php';
if (file_exists(__DIR__ . '/../../../config/development.config.php')) {
    $appConfig = ArrayUtils::merge(
    $appConfig, require __DIR__ . '/../../../config/development.config.php'
    );
}

if (file_exists(__DIR__ . '/../../../config/autoload/global.php')) {
    $appConfig = ArrayUtils::merge(
    $appConfig, require __DIR__ . '/../../../config/autoload/global.php' 
    );
}

if (file_exists(__DIR__ . '/../../../config/autoload/local.php')) {
    $appConfig = ArrayUtils::merge(
    $appConfig, require __DIR__ . '/../../../config/autoload/local.php'
    );
}

if (!array_key_exists("zfcommandline", $appConfig))
{
	echo "zfcommandlline configuration does not exist in global.php\n";
	exit(1);
}
	

if (!isset($appConfig['zfcommandline'])
    || !isset($appConfig['zfcommandline']['namespaces'])
    || !isset($appConfig['zfcommandline']['namespaces']['app'])
    || !isset($appConfig['zfcommandline']['namespaces']['api'])
    || !isset($appConfig['zfcommandline']['driver'])
   ) 
{
	echo "Invalid zfcommandline configuration\n";
	exit(1);
}

if (file_exists(__DIR__ . '/../../../module/' . $appConfig['zfcommandline']['namespaces']['app'] . '/config/module.config.php')) {
    $appConfig = ArrayUtils::merge(
    $appConfig, require __DIR__ . '/../../../module/' . $appConfig['zfcommandline']['namespaces']['app'] . '/config/module.config.php'
    );
}


if (file_exists(__DIR__ . '/../../../module/' . $appConfig['zfcommandline']['namespaces']['api'] . '/config/module.config.php')) {
    $appConfig = ArrayUtils::merge(
    $appConfig, require __DIR__ . '/../../../module/' . $appConfig['zfcommandline']['namespaces']['api'] . '/config/module.config.php'
    );
}

$zendApplication = ZendApplication::init($appConfig);
$serviceManager = $zendApplication->getServiceManager();

$projectRootDir = realpath(dirname(__FILE__) . "/../../../");

$controllerCreateCommand = new ControllerCreateCommand($serviceManager, $projectRootDir, $appConfig);
$modelCreateCommand = new ModelCreateCommand($serviceManager, $projectRootDir, $appConfig);
$appRouteCreateCommand = new AppRouteCreateCommand($serviceManager, $projectRootDir, $appConfig);
$apiRouteCreateCommand = new ApiRouteCreateCommand($serviceManager, $projectRootDir, $appConfig);
$viewCreateCommand = new ViewCreateCommand($serviceManager, $projectRootDir, $appConfig); 
$dbQueryGenerateCommand = new DbQueryGenerateCommand($serviceManager, $projectRootDir, $appConfig);

$application = new Application();
$application->add($controllerCreateCommand);
$application->add($modelCreateCommand);
$application->add($appRouteCreateCommand);
$application->add($apiRouteCreateCommand);
$application->add($viewCreateCommand);
$application->add($dbQueryGenerateCommand);

$application->run();
