<?php namespace Bosnadev\Yaml;

use Illuminate\Translation\TranslationServiceProvider;

/**
 * Class YamlTranslationServiceProvider
 * @package Bosnadev\Yaml
 */
class YamlTranslationServiceProvider extends TranslationServiceProvider
{
    /**
     *  Register File Loader with Yaml file support
     */
    protected function registerLoader()
    {
        $this->app->bindShared('translation.loader', function ($app) {
            return new YamlTranslationFileLoader($app['files'], $app['path'] . '/lang');
        });
    }
}