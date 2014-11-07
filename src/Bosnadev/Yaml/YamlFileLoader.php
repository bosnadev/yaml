<?php namespace Bosnadev\Yaml;

use Illuminate\Config\FileLoader;
use Symfony\Component\Yaml\Parser;

/**
 * Class YamlFileLoader
 * @package Bosnadev\Yaml
 */
class YamlFileLoader extends FileLoader
{
    /**
     * @return array
     */
    protected function getAllowedFileExtensions()
    {
        return ['php', 'yml', 'yaml'];
    }

    /**
     * @return array
     */
    protected function getAllowedPathsHelper()
    {
        return ['app_path', 'base_path', 'public_path', 'storage_path'];
    }

    /**
     * @param string $environment
     * @param string $group
     * @param null $namespace
     * @return array
     */
    public function load($environment, $group, $namespace = null)
    {
        $items = [];
        $path  = $this->getPath($namespace);

        if (is_null($path)) {
            return $items;
        }

        foreach ($this->getAllowedFileExtensions() as $extension) {
            $file = "{$path}/{$group}." . $extension;

            if ($this->files->exists($file)) {
                $items = $this->mergeEnvironmentWithYamlSupport($items, $file, $extension);
            }

            $file = "{$path}/{$environment}/{$group}." . $extension;

            if ($this->files->exists($file)) {
                $items = $this->mergeEnvironmentWithYamlSupport($items, $file, $extension);
            }
        }

        return $items;
    }

    /**
     * @param array $items
     * @param $file
     * @param $extension
     * @return array
     */
    protected function mergeEnvironmentWithYamlSupport(array $items, $file, $extension)
    {
        return array_replace_recursive($items, $this->parseContent($extension, $file));
    }

    /**
     * @param string $group
     * @param null $namespace
     * @return bool
     */
    public function exists($group, $namespace = null)
    {
        $key = $group . $namespace;

        if (isset($this->exists[$key])) {
            return $this->exists[$key];
        }

        $path = $this->getPath($namespace);

        if (is_null($path)) {
            return $this->exists[$key] = false;
        }

        foreach ($this->getAllowedFileExtensions() as $extension) {
            $file = "{$path}/{$group}." . $extension;

            if ($exists = $this->files->exists($file)) {
                return $this->exists[$key] = $exists;
            }
        }

        return $this->exists[$key] = false;
    }

    /**
     * @param string $env
     * @param string $package
     * @param string $group
     * @param array $items
     * @return array
     */
    public function cascadePackage($env, $package, $group, $items)
    {
        foreach ($this->getAllowedFileExtensions() as $extension) {
            $file = "packages/{$package}/{$group}." . $extension;

            if ($this->files->exists($path = $this->defaultPath . '/' . $file)) {
                $items = array_merge($items, $this->parseContent($extension, $path));
            }

            $path = $this->getPackagePath($env, $package, $group, $extension);

            if ($this->files->exists($path)) {
                $items = array_merge($items, $this->parseContent($extension, $path));
            }
        }

        return $items;
    }

    /**
     * @param string $env
     * @param string $package
     * @param string $group
     * @param string $extension
     * @return string
     */
    protected function getPackagePath($env, $package, $group, $extension = 'php')
    {
        $file   = "packages/{$package}/{$env}/{$group}." . $extension;
        $result = $this->defaultPath . '/' . $file;

        return $result;
    }

    /**
     * @param $format
     * @param $file
     * @return array|mixed|null
     */
    protected function parseContent($format, $file)
    {
        $content = null;

        switch ($format) {
            case 'php':
                $content = $this->files->getRequire($file);
                break;
            case 'yml':
            case 'yaml':
                $content = $this->parseYamlOrLoadFromCache($file);
                break;
        }

        return $content;
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function parsePathsHelpers($data)
    {
        foreach ($this->getAllowedPathsHelper() as $pathHelper) {
            $data = str_replace('%' . $pathHelper . '%', $pathHelper(), $data);
        }

        return $data;
    }

    /**
     * @param $file
     * @return array|mixed
     */
    protected function parseYamlOrLoadFromCache($file)
    {
        $cachefile = storage_path() . '/cache/yaml.config.cache.' . md5($file) . '.php';

        if (@filemtime($cachefile) < filemtime($file)) {
            $parser  = new Parser();
            $content = null === ($yaml = $parser->parse(file_get_contents($file))) ? [] : $yaml;
            $content = $this->parsePathsHelpers($content);

            file_put_contents($cachefile, "<?php" . PHP_EOL . PHP_EOL . "return " . var_export($content, true) . ";");
        } else {
            $content = $this->files->getRequire($cachefile);
        }

        return $content;
    }
} 