<?php
namespace Backend;

use Phalcon\Config;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use RuntimeException;
use Phalcon\Mvc\Router;
use Phalcon\DiInterface;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Error\Handler as ErrorHandler;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mvc\View\Exception as ViewException;
use Phalcon\Cache\Frontend\Output as FrontOutput;
use Phalcon\Logger\Formatter\Line as FormatterLine;
use Phalcon\Logger\AdapterInterface as LoggerInterface;

class Bootstrap
{
    private $app;

    private $loaders = [
        'cache',
        'session',
        'view',
        'database',
        'router',
        'url',
        'dispatcher',
        'flash',
    ];

    /**
     * Bootstrap constructor.
     */
    public function __construct()
    {
        $di = new FactoryDefault;

        $em = new EventsManager;
        $em->enablePriorities(true);

        $config = $this->initConfig();

        // Register the configuration itself as a service
        $di->setShared('config', $config);

        $this->app = new Application;

        $this->initLogger($di, $config, $em);
        $this->initLoader($di, $config, $em);

        foreach ($this->loaders as $service) {
            $serviceName = ucfirst($service);
            $this->{'init' . $serviceName}($di, $config, $em);
        }

        $di->setShared('eventsManager', $em);
        $di->setShared('app', $this->app);

        $this->app->setEventsManager($em);
        $this->app->setDI($di);
    }

    /**
     * Runs the Application
     *
     * @return $this|string
     */
    public function run()
    {
        if (ENV_TESTING === APPLICATION_ENV) {
            return $this->app;
        }

        return $this->getOutput();
    }

    /**
     * Get application output.
     *
     * @return string
     */
    public function getOutput()
    {
        if ($this->app instanceof Application) {
            return $this->app->handle()->getContent();
        }

        return $this->app->handle();
    }

    /**
     * Initialize the Logger.
     *
     * @param DiInterface   $di     Dependency Injector
     * @param Config        $config App config
     * @param EventsManager $em     Events Manager
     *
     * @return void
     */
    protected function initLogger(DiInterface $di, Config $config, EventsManager $em)
    {
        ErrorHandler::register();

        $di->set('logger', function ($filename = null, $format = null) use ($config) {
            $format   = $format ?: $config->get('logger')->format;
            $filename = trim($filename ?: $config->get('logger')->filename, '\\/');
            $path     = rtrim($config->get('logger')->path, '\\/') . DIRECTORY_SEPARATOR;

            $formatter = new FormatterLine($format, $config->get('logger')->date);
            $logger = new FileLogger($path . $filename);

            $logger->setFormatter($formatter);
            $logger->setLogLevel($config->get('logger')->logLevel);

            return $logger;
        });
    }

    /**
     * Initialize the Loader.
     *
     * Adds all required namespaces.
     *
     * @param DiInterface   $di     Dependency Injector
     * @param Config        $config App config
     * @param EventsManager $em     Events Manager
     *
     * @return Loader
     */
    protected function initLoader(DiInterface $di, Config $config, EventsManager $em)
    {
        $loader = new Loader;
        $loader->registerNamespaces(
            [
                'Backend\Models'      => $config->get('application')->modelsDir,
                'Backend\Controllers' => $config->get('application')->controllersDir,
                'Backend'             => $config->get('application')->libraryDir
            ]
        );

        $loader->setEventsManager($em);
        $loader->register();

        $di->setShared('loader', $loader);

        return $loader;
    }

    /**
     * Initialize the Cache.
     *
     * The frontend must always be Phalcon\Cache\Frontend\Output and the service 'viewCache'
     * must be registered as always open (not shared) in the services container (DI).
     *
     * @param DiInterface   $di     Dependency Injector
     * @param Config        $config App config
     * @param EventsManager $em     Events Manager
     *
     * @return void
     */
    protected function initCache(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->set('viewCache', function () use ($config) {
            $frontend = new FrontOutput(['lifetime' => $config->get('viewCache')->lifetime]);

            $config  = $config->get('viewCache')->toArray();
            $backend = '\Phalcon\Cache\Backend\\' . $config['backend'];
            unset($config['backend'], $config['lifetime']);

            return new $backend($frontend, $config);
        });

        $di->setShared('modelsCache', function () use ($config) {
            $frontend = '\Phalcon\Cache\Frontend\\' . $config->get('modelsCache')->frontend;
            $frontend = new $frontend(['lifetime' => $config->get('modelsCache')->lifetime]);

            $config  = $config->get('modelsCache')->toArray();
            $backend = '\Phalcon\Cache\Backend\\' . $config['backend'];
            unset($config['backend'], $config['lifetime'], $config['frontend']);

            return new $backend($frontend, $config);
        });

        $di->setShared('dataCache', function () use ($config) {
            $frontend = '\Phalcon\Cache\Frontend\\' . $config->get('dataCache')->frontend;
            $frontend = new $frontend(['lifetime' => $config->get('dataCache')->lifetime]);

            $config  = $config->get('dataCache')->toArray();
            $backend = '\Phalcon\Cache\Backend\\' . $config['backend'];
            unset($config['backend'], $config['lifetime'], $config['frontend']);

            return new $backend($frontend, $config);
        });
    }

    /**
     * Initialize the Session Service.
     *
     * Start the session the first time some component request the session service.
     *
     * @param DiInterface   $di     Dependency Injector
     * @param Config        $config App config
     * @param EventsManager $em     Events Manager
     *
     * @return void
     */
    protected function initSession(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->setShared('session', function () use ($config) {
            $adapter = '\Phalcon\Session\Adapter\\' . $config->get('session')->adapter;

            /** @var \Phalcon\Session\AdapterInterface $session */
            $session = new $adapter;
            $session->start();

            return $session;
        });
    }

    /**
     * Initialize the View.
     *
     * Setting up the view component.
     *
     *
     * @param DiInterface   $di     Dependency Injector
     * @param Config        $config App config
     * @param EventsManager $em     Events Manager
     *
     * @return Loader
     */
    protected function initView(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->set('view', function () use ($di, $config, $em) {
            $view  = new View;
            $view->registerEngines([
                // Setting up Volt Engine
                '.volt'  => function ($view, $di) use ($config) {
                    $volt   = new VoltEngine($view, $di);
                    $voltConfig = $config->get('volt')->toArray();

                    $options = [
                        'compiledPath'      => $voltConfig['cacheDir'],
                        'compiledExtension' => $voltConfig['compiledExt'],
                        'compiledSeparator' => $voltConfig['separator'],
                        'compileAlways'     => ENV_DEVELOPMENT === APPLICATION_ENV && $voltConfig['forceCompile'],
                    ];

                    $volt->setOptions($options);

                    $compiler = $volt->getCompiler();

                    $compiler->addFunction('number_format', function ($resolvedArgs) {
                        return 'number_format(' . $resolvedArgs . ')';
                    });

                    $compiler->addFunction('chr', function ($resolvedArgs) {
                        return 'chr(' . $resolvedArgs . ')';
                    });

                    return $volt;
                },
                // Setting up Php Engine
                '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
            ]);

            $view->setViewsDir($config->get('application')->viewsDir);

            $em->attach('view', function ($event, $view) use ($di, $config) {
                /**
                 * @var LoggerInterface $logger
                 * @var View $view
                 * @var Event $event
                 * @var DiInterface $di
                 */
                $logger = $di->get('logger');
                $logger->debug(sprintf('Event %s. Path: %s', $event->getType(), $view->getActiveRenderPath()));

                if ('notFoundView' == $event->getType()) {
                    $message = sprintf('View not found: %s', $view->getActiveRenderPath());
                    $logger->error($message);
                    throw new ViewException($message);
                }
            });

            $view->setEventsManager($em);

            return $view;
        });
    }

    /**
     * Initialize the Database connection.
     *
     * Database connection is created based in the parameters defined in the configuration file.
     *
     * @param DiInterface   $di     Dependency Injector
     * @param Config        $config App config
     * @param EventsManager $em     Events Manager
     *
     * @return void
     */
    protected function initDatabase(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->setShared('db', function () use ($config, $em, $di) {
            $config  = $config->get('database')->toArray();
            $adapter = '\Phalcon\Db\Adapter\Pdo\\' . $config['adapter'];
            unset($config['adapter']);

            /** @var \Phalcon\Db\Adapter\Pdo $connection */
            $connection = new $adapter($config);

            // Listen all the database events
            $em->attach(
                'db',
                function ($event, $connection) use ($di) {
                    /**
                     * @var \Phalcon\Events\Event $event
                     * @var \Phalcon\Db\AdapterInterface $connection
                     * @var \Phalcon\DiInterface $di
                     */
                    if ($event->getType() == 'beforeQuery') {
                        $variables = $connection->getSQLVariables();
                        $string    = $connection->getSQLStatement();

                        if ($variables) {
                            $string .= ' [' . join(',', $variables) . ']';
                        }

                        // To disable logging change logLevel in config
                        $di->get('logger', ['db.log'])->debug($string);

                    }
                }
            );

            // Assign the eventsManager to the db adapter instance
            $connection->setEventsManager($em);

            return $connection;
        });

        $di->setShared('modelsManager', function () use ($em) {
            $modelsManager = new ModelsManager;
            $modelsManager->setEventsManager($em);

            return $modelsManager;
        });

        $di->setShared('modelsMetadata', function () use ($config, $em) {
            $config = $config->get('metadata')->toArray();
            $adapter = '\Phalcon\Mvc\Model\Metadata\\' . $config['adapter'];
            unset($config['adapter']);

            $metaData = new $adapter($config);

            return $metaData;
        });
    }

    /**
     * Initialize the Router.
     *
     * @param DiInterface   $di     Dependency Injector
     * @param Config        $config App config
     * @param EventsManager $em     Events Manager
     *
     * @return void
     */
    protected function initRouter(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->setShared('router', function () use ($config, $em) {
            $router = new Router;

            if (!isset($_GET['_url'])) {
                $router->setUriSource(Router::URI_SOURCE_SERVER_REQUEST_URI);
            }

            $router->removeExtraSlashes(true);
            $router->setEventsManager($em);

            return $router;
        });
    }

    /**
     * Initialize the Url service.
     *
     * The URL component is used to generate all kind of urls in the application.
     *
     * @param DiInterface   $di     Dependency Injector
     * @param Config        $config App config
     * @param EventsManager $em     Events Manager
     *
     * @return void
     */
    protected function initUrl(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->setShared('url', function () use ($config) {
            $url = new UrlResolver;

            $url->setBaseUri($config->get('application')->baseUri);
            $url->setStaticBaseUri($config->get('application')->staticBaseUri);

            return $url;
        });
    }

    /**
     * Initialize the Dispatcher.
     * @link https://forum.phalconphp.com/discussion/106/difference-between-setdefaults-and-notfound
     *
     * @param DiInterface   $di     Dependency Injector
     * @param Config        $config App config
     * @param EventsManager $em     Events Manager
     *
     * @return void
     */
    protected function initDispatcher(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->setShared('dispatcher', function () use ($em) {
            $dispatcher = new Dispatcher;

            $dispatcher->setDefaultNamespace('Backend\Controllers');

            $em->attach("dispatch", function($event, $dispatcher, $exception) {

                if ($event->getType() == 'beforeException') {
                    switch ($exception->getCode()) {
                        case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                        case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                            $dispatcher->forward(array(
                                'controller' => 'error',
                                'action' => 'route404'
                            ));
                            return false;
                    }
                }
            });

            $dispatcher->setEventsManager($em);

            return $dispatcher;
        });
    }

    /**
     * Initialize the Flash Service.
     *
     * Register the Flash Service with the Twitter Bootstrap classes
     *
     * @param DiInterface   $di     Dependency Injector
     * @param Config        $config App config
     * @param EventsManager $em     Events Manager
     *
     * @return void
     */
    protected function initFlash(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->setShared('flash', function () {
            return new Flash(
                [
                    'error'   => 'alert alert-danger fade pnotify',
                    'success' => 'alert alert-success fade pnotify',
                    'notice'  => 'alert alert-info fade pnotify',
                    'warning' => 'alert alert-warning fade pnotify',
                ]
            );
        });

        $di->setShared(
            'flashSession',
            function () {
                return new FlashSession([
                    'error'   => 'alert alert-danger fade pnotify',
                    'success' => 'alert alert-success fade pnotify',
                    'notice'  => 'alert alert-info fade pnotify',
                    'warning' => 'alert alert-warning fade pnotify',
                ]);
            }
        );
    }

    /**
     * Prepare and return the Config.
     *
     * @param  string $path Config path [Optional]
     * @return Config
     *
     * @throws \RuntimeException
     */
    protected function initConfig($path = null)
    {
        $path = $path ?: BASE_DIR . 'app/config/';

        if (!is_readable($path . 'config.php')) {
            throw new RuntimeException(
                'Unable to read config from ' . $path . 'config.php'
            );
        }

        $config = include $path . 'config.php';

        if (is_array($config)) {
            $config = new Config($config);
        }

        if (!$config instanceof Config) {
            $type = gettype($config);
            if ($type == 'boolean') {
                $type .= ($type ? ' (true)' : ' (false)');
            } elseif (is_object($type)) {
                $type = get_class($type);
            }

            throw new RuntimeException(
                sprintf(
                    'Unable to read config file. Config must be either an array or Phalcon\Config instance. Got %s',
                    $type
                )
            );
        }

        if (is_readable($path . APPLICATION_ENV . '.php')) {
            $override = include_once $path . APPLICATION_ENV . '.php';

            if (is_array($override)) {
                $override = new Config($override);
            }

            if ($override instanceof Config) {
                $config->merge($override);
            }
        }

        return $config;
    }
}
