<?php


namespace rmatil\cms\Handler;


use Symfony\Component\Yaml\Yaml;

class ConfigurationHandler {

    /**
     * Returns the content from the configuration file
     *
     * @param $path string Path to configuration file
     *
     * @return array An associative array representing the configuration
     */
    public static function readConfiguration($path) {
        return Yaml::parse(file_get_contents($path));
    }

    /**
     * Writes the given configuration to the config file.
     *
     * @param array $config The array which should be used as configuration
     * @param $path string THe path to the config file
     *
     * @return bool True, on success, false otherwise
     */
    public static function writeConfiguration(array $config, $path) {
        return (false !== file_put_contents($path, Yaml::dump($config, 2, 4, true, false))) ? true : false;
    }

    /**
     * @param array $config The specific section as array
     * @param $configSection string The key of the config to overwrite
     * @param $path string The path to the config file
     *
     * @return bool True on success, false otherwise
     */
    public static function writeConfigurationSection(array $config, $configSection, $path) {
        $currentConfig = self::readConfiguration($path);

        $currentConfig[$configSection] = $config;

        return self::writeConfiguration($config, $path);
    }

}