<?php namespace Bosnadev\Yaml;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Symfony\Component\Yaml\Parser;

/**
 * Class YamlTranslationFileLoader
 * @package Bosnadev\Yaml
 */
class YamlTranslationFileLoader extends FileLoader
{

    /**
     * @return array
     */
    protected function getAllowedFileExtensions()
    {
        return ['php', 'yml', 'yaml'];
    }

    /**
     * @param array $lines
     * @param string $locale
     * @param string $group
     * @param string $namespace
     * @return array
     */
    protected function loadNamespaceOverrides(array $lines, $locale, $group, $namespace)
    {
        foreach ($this->getAllowedFileExtensions() as $extension) {
            $file = "{$this->path}/packages/{$locale}/{$namespace}/{$group}." . $extension;
            if ($this->files->exists($file)) {
                return $this->replaceLines($extension, $lines, $file);
            }
        }
        return $lines;
    }

    /**
     * @param $format
     * @param $lines
     * @param $file
     * @return array
     */
    protected function replaceLines($format, $lines, $file)
    {
        return array_replace_recursive($lines, $this->parseContent($format, $file));
    }

    /**
     * @param $format
     * @param $file
     * @return mixed|null
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
     * @param string $path
     * @param string $locale
     * @param string $group
     * @return array|mixed|null
     */
    protected function loadPath($path, $locale, $group)
    {
        foreach ($this->getAllowedFileExtensions() as $extension) {
            if ($this->files->exists($full = "{$path}/{$locale}/{$group}." . $extension)) {
                return $this->parseContent($extension, $full);
            }
        }
        return [];
    }

    /**
     * @param $file
     * @return mixed
     */
    protected function parseYamlOrLoadFromCache($file)
    {
        $cachefile = storage_path() . '/cache/yaml.lang.cache.' . md5($file) . '.php';
        if (@filemtime($cachefile) < filemtime($file)) {
            $parser = new Parser();
            $content = $parser->parse(file_get_contents($file));
            file_put_contents($cachefile, "<?php" . PHP_EOL . PHP_EOL . "return " . var_export($content, true) . ";");
        } else {
            $content = $this->files->getRequire($cachefile);
        }
        return $content;
    }
} 