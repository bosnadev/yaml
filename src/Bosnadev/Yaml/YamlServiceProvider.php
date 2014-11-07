<?php namespace Bosnadev\Yaml;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Config\Repository as Config;

/**
 * Class YamlServiceProvider
 * @package Bosnadev\Yaml
 */
class YamlServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('bosnadev/yaml');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->instance('config', $this->getConfig());
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('yaml');
    }

    /**
     * @return YamlFileLoader
     */
    private function getConfigLoader()
    {
        return new YamlFileLoader(new Filesystem(), $this->app['path'] . '/config');
    }

    /**
     * @return Config
     */
    private function getConfig()
    {
        return new Config(
            $this->getConfigLoader(), $this->app->environment()
        );
    }

}
