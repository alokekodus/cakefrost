<?php
/**
 * The App details file
 */

namespace Extendify\ExtendifySdk;

use Extendify\ExtendifySdk\Plugin;

/**
 * Controller for handling various app data
 */
class App
{

    /**
     * Plugin name
     *
     * @var string
     */
    public static $name = '';

    /**
     * Plugin slug
     *
     * @var string
     */
    public static $slug = '';

    /**
     * Plugin version
     *
     * @var string
     */
    public static $version = '';

    /**
     * Plugin API REST version
     *
     * @var string
     */
    public static $apiVersion = 'v1';

    /**
     * Plugin text domain
     *
     * @var string
     */
    public static $textDomain = '';

    /**
     * Plugin environment
     *
     * @var string
     */
    public static $environment = '';

    /**
     * Host plugin
     *
     * @var string
     */
    public static $sourcePlugin = 'Not set';

    /**
     * Plugin config
     *
     * @var array
     */
    public static $config = [];

    /**
     * Process the readme file to get version and name
     *
     * @return void
     */
    public function __construct()
    {
        if (isset($GLOBALS['extendifySdkSourcePlugin'])) {
            self::$sourcePlugin = $GLOBALS['extendifySdkSourcePlugin'];
        }

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
        $readme = file_get_contents(dirname(__DIR__) . '/readme.txt');

        preg_match('/=== (.+) ===/', $readme, $matches);
        self::$name = $matches[1];
        self::$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', self::$name), '-'));

        preg_match('/Stable tag: ([0-9.:]+)/', $readme, $matches);
        self::$version = $matches[1];

        // An easy way to check if we are in dev mode is to look for a dev specific file.
        $isDev = is_readable(EXTENDIFYSDK_PATH . 'node_modules') || is_readable(EXTENDIFYSDK_PATH . '.devbuild');
        self::$environment = $isDev ? 'DEVELOPMENT' : 'PRODUCTION';

        self::$textDomain = Plugin::getPluginInfo('TextDomain', self::$slug);

        // Add the config.
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
        $config = file_get_contents(dirname(__DIR__) . '/config.json');
        self::$config = json_decode($config, true);
    }
}
