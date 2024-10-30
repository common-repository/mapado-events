<?php

ini_set('display_erros', true);
/*
 * Plugin Name: Mapado for Wordpress
 * Plugin URI: https://www.mapado.com/
 * Description: Official Mapado plugin for Wordpress. Display lists of events curated on Mapado into your Wordpress blog.
 * Version: 0.5.0
 * Author: Mapado
 * Text Domain: mapado-events
 * Author URI:  https://www.mapado.com/
 * License: GPL2 license
 */
session_start();
use GuzzleHttp\Client;
use Mapado\RestClientSdk\Mapping;
use Mapado\RestClientSdk\Mapping\Driver\AnnotationDriver;
use Mapado\RestClientSdk\RestClient;
use Mapado\RestClientSdk\SdkClient;
use Mapado\Sdk\Entity\Activity;
use Mapado\Sdk\Entity\User;
define('MAPADO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MAPADO_PLUGIN_URL', plugin_dir_url(__FILE__));
require_once MAPADO_PLUGIN_PATH . 'vendor/autoload.php';
/* Require classes */
require_once MAPADO_PLUGIN_PATH . 'class/microtemplate.class.php';
require_once MAPADO_PLUGIN_PATH . 'class/notification.class.php';
require_once MAPADO_PLUGIN_PATH . 'class/private.auth.php';
require_once MAPADO_PLUGIN_PATH . 'class/public.auth.php';
require_once MAPADO_PLUGIN_PATH . 'class/setting.class.php';
require_once MAPADO_PLUGIN_PATH . 'class/utils.class.php';
require_once MAPADO_PLUGIN_PATH . 'class/widget.class.php';
class MapadoPlugin
{
    /* Options names in settings array, stored in wp_options table */
    const API_WP_INDEX = 'mapado_api';
    const SETTINGS_WP_INDEX = 'mapado_settings';
    const AUTH_WP_INDEX = 'mapado_settings_auth';
    const TOKEN_WP_INDEX = 'mapado_token_cache';
    const USERLISTS_WP_INDEX = 'mapado_user_lists';
    public $paramListChoose;
    public $listParameter = [];
    /**
     * @var array
     */
    public $importedLists;
    /**
     * @var MapadoSetting
     */
    protected $settings;
    /**
     * @var SdkClient
     */
    protected $client;
    /**
     * @var string
     */
    protected $pluginBasename;
    /**
     * @var array
     */
    protected $clientInfo;
    /**
     * @var array<string,Activity>
     */
    private $activityCacheBySlug = [];
    /**
     * @var User
     */
    private $user;
    /**
     * Utils function to get & set settings from WP DB
     */
    public function setDatas()
    {
        $this->pluginBasename = plugin_basename(__FILE__);
        $this->setAccess();
        $this->setSettings();
        $this->setUserImportedLists();
        $this->initRewriteRules();
        $this->registerRewriteRules();
        $this->setParameterListSetting();
        add_action('widgets_init', [&$this, 'initDetailWidget']);
        add_action('widgets_init', [&$this, 'initEventListingWidget']);
    }
    /**
     * Get the Client
     */
    public function getClient($token = false, $forceGeneration = false)
    {
        if (empty($this->client) && empty($token)) {
            throw new \InvalidArgumentException('client is not set and $token is empty. This should not happen.');
        }
        if ((true === $forceGeneration || !$this->client) && $token) {
            $guzzleClient = new GuzzleHttp\Client(['headers' => ['Authorization' => 'Bearer ' . $token]]);
            $restClient = new RestClient($guzzleClient, 'https://api.mapado.net');
            $annotationDriver = new AnnotationDriver($this->getCacheDirectory());
            $mapping = new Mapping('/v2');
            // /v2 is the prefix of your routes
            $mapping->setMapping($annotationDriver->loadDirectory(dirname(__FILE__) . '/vendor/mapado/php-sdk/src/Entity/'));
            $this->client = new SdkClient($restClient, $mapping);
        }
        return $this->client;
    }
    /**
     * Detail Widget init
     * Class 'Mapado_Detail_Widget' in class/widget.class.php
     */
    public function initDetailWidget()
    {
        register_widget('Mapado_Detail_Widget');
    }
    /**
     * Event listing Widget init
     * Class 'Mapado_Event_Widget' in class/widget.class.php
     */
    public function initEventListingWidget()
    {
        register_widget('Mapado_Event_Widget');
    }
    /**
     * WP adding & flushing rewrite rules
     */
    public function registerRewriteRules()
    {
        global $wp_rewrite;
        /* Get pages for slug */
        if (!empty($this->importedLists)) {
            $rules = get_option('rewrite_rules');
            /* For each list page */
            foreach ($this->importedLists as $slug) {
                /* List pagination rules */
                add_rewrite_rule($slug . '/page/([0-9]+)/?$', 'index.php?pagename=' . $slug . '&paged=$matches[1]', 'top');
                /* Activity single page rules */
                add_rewrite_rule($slug . '/([^/]+)/?$', 'index.php?pagename=' . $slug . '&mapado_event=$matches[1]', 'top');
            }
        }
    }
    /**
     * Inserting custom query vars
     */
    public function insertQueryVars($vars)
    {
        array_push($vars, 'mapado_event');
        return $vars;
    }
    /**
     * Install on plugin activation
     * Create events page & event single page
     */
    public function install()
    {
        $settings = get_option(self::SETTINGS_WP_INDEX);
        /* Single event page */
        if (empty($settings['activity_page']) || !empty($settings['activity_page']) && false === get_post_status($settings['activity_page'])) {
            $activity_page = wp_insert_post(['post_title' => 'Événement', 'post_name' => 'evenement', 'post_content' => 'MAPADO_EVENEMENT', 'post_status' => 'publish', 'post_type' => 'page', 'post_author' => 1], false);
        }
        if (!empty($activity_page) && is_int($activity_page) && $activity_page > 0) {
            $settings['activity_page'] = $activity_page;
            if (!update_option(self::SETTINGS_WP_INDEX, $settings)) {
                /* Deleting pages to try again without duplicates */
                wp_delete_post($activity_page, true);
                die(__('Mapado for Wordpress : Problem to save settings, please try again.', 'mapado-events'));
            }
        }
        flush_rewrite_rules();
    }
    /**
     * Plugin uninstall
     * Delete posts & storage datas
     */
    public function uninstall()
    {
        $user_lists = get_option(self::USERLISTS_WP_INDEX);
        $settings = get_option(self::SETTINGS_WP_INDEX);
        /* Deleting pages */
        if (!empty($settings['activity_page'])) {
            wp_delete_post($settings['activity_page'], true);
        }
        /* Deleting lists pages */
        foreach ($user_lists as $list_slug) {
            $page = get_page_by_path($list_slug);
            wp_delete_post($page->ID, true);
        }
        delete_option(self::API_WP_INDEX);
        delete_option(self::SETTINGS_WP_INDEX);
        delete_option(self::AUTH_WP_INDEX);
        delete_option(self::TOKEN_WP_INDEX);
        delete_option(self::USERLISTS_WP_INDEX);
    }
    public function setParameterListSetting()
    {
        $this->listParameter = get_option(self::SETTINGS_WP_INDEX);
    }
    /**
     * Get & set the API settings from WP DB
     */
    protected function setAccess()
    {
        $this->clientInfo = get_option(self::API_WP_INDEX);
    }
    /**
     * Get & set the additionnal settings from WP DB
     */
    protected function setSettings()
    {
        $this->settings = new MapadoSetting(self::SETTINGS_WP_INDEX);
    }
    /**
     * Get & set imported user lists from WP DB
     */
    protected function setUserImportedLists()
    {
        $this->importedLists = get_option(self::USERLISTS_WP_INDEX);
    }
    protected function getCacheDirectory()
    {
        return get_temp_dir() . 'mapado-events/' . md5(__DIR__) . '/';
    }
    /**
     * Init rewrite rules
     */
    private function initRewriteRules()
    {
        add_filter('query_vars', [&$this, 'insertQueryVars']);
    }
}
/*
 * Plugin initialisation
 */
add_action('init', 'mapado_plugin', 0);
function mapado_plugin()
{
    global $mapado;
    if (is_admin()) {
        $mapado = new MapadoPrivateAuth();
    } else {
        $mapado = new MapadoPublicAuth();
    }
}
/* Register plugin install function */
$installCallable = [$mapado, 'install'];
if (is_callable($installCallable)) {
    register_activation_hook(__FILE__, $installCallable);
}
$uninstallCallable = [$mapado, 'uninstall'];
if (is_callable($uninstallCallable)) {
    register_uninstall_hook(__FILE__, $uninstallCallable);
}