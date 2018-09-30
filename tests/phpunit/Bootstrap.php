<?php
use Phalcon\Di;
use Phalcon\Loader;
use Phalcon\Mvc\View\Engine\Php;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Mvc\View\Simple;

ini_set("display_errors", 1);
error_reporting(E_ALL);

define("ROOT_PATH", __DIR__);

set_include_path(
  ROOT_PATH . PATH_SEPARATOR . get_include_path()
);

// Use the application autoloader to autoload the classes
// Autoload the dependencies found in composer
$loader = new Loader();

$loader->registerDirs(
  [
    ROOT_PATH,
  ]
);

$loader->register();

$di = new Di\FactoryDefault();

$di->setShared('view', function () {
  $view = new Simple();

  $view->setViewsDir(ROOT_PATH . '/_data/templates/');
  $view->registerEngines([
    '.phtml' => Php::class,
    '.volt' => function($view, $di)
    {
      $volt = new Volt($view, $di);
      $volt->setOptions([
        'compileAlways' => true,
        'compiledPath' => function($templatePath)
        {
          return ROOT_PATH . '/_data/cache/' . basename($templatePath) . '.php';
        }
      ]);
      return $volt;
    }
  ]);

  return $view;
});

Di::reset();
Di::setDefault($di);