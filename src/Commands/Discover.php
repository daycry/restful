<?php

namespace Daycry\RestFul\Commands;

use CodeIgniter\CLI\BaseCommand;
use Daycry\RestFul\Models\ApiModel;
use Daycry\RestFul\Models\ControllerModel;
use Daycry\RestFul\Models\EndpointModel;
use Daycry\RestFul\Entities\Api;
use Daycry\RestFul\Entities\Controller;
use Daycry\RestFul\Entities\Endpoint;
use Daycry\ClassFinder\ClassFinder;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\I18n\Time;
use ReflectionClass;
use ReflectionMethod;

class Discover extends BaseCommand
{
    protected $group       = 'RestFul';
    protected $name        = 'restful:discover';
    protected $description = 'Discover classes from namespace to import in database.';

    protected Time $timeStart;
    protected BaseConfig $config;
    protected array $allClasses = [];

    public function run(array $params)
    {
        $this->timeStart = Time::now()->subSeconds(1);
        $finderConfig = config('ClassFinder');
        $finderConfig->finder['files'] = false;

        $api = $this->_checkApiModel();

        foreach(service('settings')->get('RestFul.namespaceScope') as $namespace) {
            //remove "\" for search in class-finder
            $namespace = (mb_substr($namespace, 0, 1) == '\\') ? mb_substr($namespace, 1) : $namespace;

            $classes = (new ClassFinder($finderConfig))->getClassesInNamespace($namespace, ClassFinder::RECURSIVE_MODE | ClassFinder::ALLOW_CLASSES);
            if ($classes) {
                foreach ($classes as $class) {
                    \array_push($this->allClasses, '\\' . $class);

                    $methods = $this->_getMethodsFromCLass($class);

                    $class = (mb_substr($class, 0, 1) !== '\\') ? '\\' . $class : $class;

                    $this->_checkClassController($api, $class, $methods);
                }

                unset($classes);
            }
        }

        $controllerModel = new ControllerModel();
        $allControllers = $controllerModel->where('api_id', $api->id)->findColumn('controller');
        if($allControllers) {
            $forRemove = array_diff($allControllers, $this->allClasses);

            foreach($forRemove as $remove) {
                $controller = $controllerModel->where('api_id', $api->id)->where('controller', $remove)->first();
                if($controller) {
                    $controllerModel->where('id', $controller->id)->delete();
                }
            }
        }

        CLI::write('**** FINISHED. ****', 'white', 'green');
    }

    private function _checkApiModel(): Api
    {
        $apiModel = new ApiModel();
        /** @var ?Api $api */
        $api = $apiModel->where('url', site_url())->first();

        if (!$api) {
            $api = new Api();
            $api->fill(array('url' => site_url()));
            $apiModel->save($api);
            $api->id = $apiModel->getInsertID();
        } else {
            $api->fill(array( 'checked_at' => Time::now() ));
            $apiModel->save($api);
        }

        return $api;
    }

    private function _getMethodsFromCLass($namespace): array
    {
        $f = new ReflectionClass($namespace);
        $methods = array();

        foreach ($f->getMethods(ReflectionMethod::IS_PUBLIC) as $m) {
            if (strpos($m->name, '__') !== 0) {
                $methods[] = $m->name;
            }
        }

        return $methods;
    }

    private function _checkClassController(Api $api, string $class, array $methods = [])
    {
        $controllerModel = new ControllerModel();
        $controller = $controllerModel->where('api_id', $api->id)->where('controller', $class)->first();

        if(!$controller) {
            $controller = new Controller();
            $controller->fill(array('api_id' => $api->id, 'controller' => $class));
            $controllerModel->save($controller);
            $controller->id = $controllerModel->getInsertID();
        }

        $endpointModel = new EndpointModel();

        $allMethods = (new EndpointModel())->where('controller_id', $controller->id)->findColumn('method');
        foreach($methods as $method) {
            $endpoint = $endpointModel->where('controller_id', $controller->id)->where('method', $method)->first();

            if(!$endpoint) {
                $endpoint = new Endpoint();
                $endpoint->fill(array('controller_id' => $controller->id, 'method' => $method));
                $endpointModel->save($endpoint);
                $endpoint->id = $endpointModel->getInsertID();
            }

            $endpoint->checked_at = Time::now();
            $endpointModel->save($endpoint);
        }

        if($allMethods) {
            $forRemove = array_diff($allMethods, $methods);

            foreach($forRemove as $remove) {
                $endpoint = $endpointModel->where('controller_id', $controller->id)->where('method', $remove)->first();
                if($endpoint) {
                    $endpointModel->where('id', $endpoint->id)->delete();
                }
            }
        }

        $controller->checked_at = Time::now();
        $controllerModel->save($controller);
    }
}
