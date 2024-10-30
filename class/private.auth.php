<?php

use Mapado\Sdk\Entity\User;
/**
 * Class MapadoPrivateAuth
 * For admin area
 */
class MapadoPrivateAuth extends MapadoPlugin
{
    private $auth;
    private $token;
    private $param_list = ['widget', 'perpage', 'card_thumb_position', 'card_thumb_orientation', 'card_thumb_size', 'card_column_max', 'full_thumb_size', 'list_sort', 'past_events', 'list_depth', 'display_map', 'card_template', 'full_template', 'display_search', 'paramListChoose', 'param_all-lists'];
    public function __construct()
    {
        $this->setAuth();
        $this->setToken();
        $this->setDatas();
        $this->setSelectedList();
        $this->setListParam();
        add_action('admin_menu', [$this, 'adminMenu']);
        add_action('init', [$this, 'mapadoevents_load_textdomain']);
        add_action('wp_ajax_ajaxGetUserLists', [$this, 'ajaxGetUserLists']);
        add_action('wp_ajax_ajaxUpdateListSettings', [$this, 'ajaxUpdateListSettings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueScriptsStyle']);
        add_action('admin_init', [$this, 'pluginCheck']);
        add_action('mapado_settings_api', [$this, 'adminSettingsApi']);
        /* Plugin settings link */
        add_filter('plugin_action_links_' . $this->pluginBasename, [$this, 'settingsPluginLink'], 10, 2);
    }
    public function mapadoevents_load_textdomain()
    {
        $domain = 'mapado-events';
        load_plugin_textdomain($domain, false, $domain . '/languages');
    }
    /**
     * Adding mapado in admin settings menu
     */
    public function adminMenu()
    {
        add_menu_page('Mapado plugin', 'Mapado Events', 'manage_options', 'mapado_settings', [$this, 'adminSettings'], MAPADO_PLUGIN_URL . 'assets/images/mapado-logo-icon-square.jpg');
        add_submenu_page('mapado_settings', __('API settings', 'mapado-events'), __('API settings', 'mapado-events'), 'manage_options', 'mapado_settings_step_api', [$this, 'adminSettingsApi']);
        // if there is API settings
        if (!empty($this->clientInfo['id']) && !empty($this->clientInfo['secret']) && !empty($this->auth)) {
            add_submenu_page('mapado_settings', __('Import your lists', 'mapado-events'), __('Import your lists', 'mapado-events'), 'manage_options', 'mapado_settings_step_imports', [$this, 'adminSettingsImports']);
            // if there is at least one list imported
            if ($this->importedLists) {
                add_submenu_page('mapado_settings', __('Display parameter', 'mapado-events'), __('Display parameter', 'mapado-events'), 'manage_options', 'mapado_settings_step_options', [$this, 'adminSettingsOptions']);
            }
        }
    }
    /**
     * Enqueue JS & CSS files in mapado admin settings page
     */
    public function enqueueScriptsStyle($hook)
    {
        /* If not mapado settings page */
        if ('toplevel_page_mapado_settings' != $hook && 'mapado-events_page_mapado_settings_step_api' != $hook && 'mapado-events_page_mapado_settings_step_imports' != $hook && 'mapado-events_page_mapado_settings_step_options' != $hook) {
            return;
        }
        wp_enqueue_script('jquery');
        wp_enqueue_script('mapado_admin_script', MAPADO_PLUGIN_URL . 'assets/admin.js');
        wp_register_style('mapado_admin_css', MAPADO_PLUGIN_URL . 'assets/admin.css');
        wp_enqueue_style('mapado_admin_css');
    }
    /**
     * Check plugin version
     */
    public function pluginCheck()
    {
        $plugin_datas = get_plugin_data(plugin_dir_path(dirname(__FILE__)) . 'mapado.php');
        /* Force flush rewrite if the plugin has been updated */
        if (isset($this->settings['mapado_version']) && $plugin_datas['Version'] != $this->settings['mapado_version']) {
            flush_rewrite_rules();
            $this->cleanRestClientCache();
            $this->migrateAPIV1ToAPIV2();
            /* Update last plugin version on this site */
            $this->settings['mapado_version'] = $plugin_datas['Version'];
            $this->settings->update();
        }
    }
    /**
     * Adding every imported lists in $param_list
     */
    public function setSelectedList()
    {
        if ($this->importedLists) {
            $defaultListChoose = null;
            foreach ($this->importedLists as $slug) {
                if (!$defaultListChoose) {
                    $defaultListChoose = $slug;
                }
                $this->param_list[] = 'param_' . $slug;
            }
            $getListChoose = isset($_GET['list_choose']) ? htmlspecialchars($_GET['list_choose']) : 'param_' . $defaultListChoose;
            $getValidator = strpos($getListChoose, "param_");
            if ($getValidator === 0) {
                $find = false;
                foreach ($this->importedLists as $slug) {
                    if ('param_' . $slug == $getListChoose) {
                        $find = true;
                    }
                }
            }
            $this->paramListChoose = $find == true ? $getListChoose : "param_" . $defaultListChoose;
        }
    }
    /**
     * Set list param
     */
    public function setListParam()
    {
        $this->listParameter = $this->getListParameter();
        $this->listParameter['paramListChoose'] = $this->paramListChoose;
        update_option(self::SETTINGS_WP_INDEX, $this->listParameter);
    }
    /**
     * Admin settings page
     */
    public function adminSettings()
    {
        if (!empty($this->clientInfo['id'])) {
            return $this->adminSettingsImports();
        }
        return $this->adminSettingsApi();
    }
    public function adminSettingsApi()
    {
        /* API Settings submit */
        if (!empty($_POST['mapado_settings_submit'])) {
            $api_settings = ['id' => $_POST['mapado_api_id'], 'secret' => $_POST['mapado_api_secret']];
            $api = false;
            $auth = false;
            /* Check if API settings have been changed */
            if (empty($api_settings['id']) || empty($api_settings['secret'])) {
                $api = false;
            } elseif ($this->clientInfo['id'] != $api_settings['id'] || $this->clientInfo['secret'] != $api_settings['secret']) {
                $this->clientInfo = $api_settings;
                $api = update_option(parent::API_WP_INDEX, $api_settings);
            } else {
                $api = true;
            }
            /* Check if auth key have been changed */
            if (empty($_POST['mapado_api_auth'])) {
                $auth = false;
            } elseif ($this->auth != $_POST['mapado_api_auth']) {
                $auth = update_option(parent::AUTH_WP_INDEX, $_POST['mapado_api_auth']);
            } else {
                $auth = true;
            }
            /* Refresh access, auth & token */
            $this->setAccess();
            $this->setAuth();
            $this->setToken();
            /* Something went wrong */
            if (!$api || !$auth) {
                MapadoNotification::error(__('There was a problem', 'mapado-events'));
                /* Success */
            } else {
                MapadoNotification::success(__('Saved settings', 'mapado-events'));
                if ($this->clientInfo['id'] && $this->clientInfo['secret'] && $this->auth) {
                    return $this->adminSettingRedirect('imports');
                }
            }
        }
        return $this->renderAdminSetting('api');
    }
    public function adminSettingsImports()
    {
        $hasApi = !empty($this->clientInfo['id']) && !empty($this->clientInfo['secret']) && !empty($this->auth);
        if (!$hasApi) {
            MapadoNotification::error(__('You have not entered your API settings', 'mapado-events'));
            return $this->adminSettingsApi();
        }
        if (!empty($_POST['mapado_settings_submit'])) {
            return $this->adminSettingRedirect('options');
        }
        return $this->renderAdminSetting('imports');
    }
    public function getListParameter()
    {
        $vars = [];
        if ($this->importedLists) {
            foreach ($this->param_list as $param) {
                // instanciation of $vars['all-list'] --> the default style foreach list
                $paramValidator = strpos($param, 'param_');
                // // used to exclude parameters like param_mylist and others param_($list)
                if ($paramValidator !== 0 && $param != $this->paramListChoose) {
                    $vars['all-list'][$param] = $this->settings->getDefinition($param);
                }
            }
            foreach ($this->importedLists as $list) {
                // instanciation of all list with their parameters
                $paramListName = 'param_' . $list;
                foreach ($this->param_list as $param) {
                    $paramValidator = strpos($param, 'param_');
                    // used to exclude parameters like param_mylist and others param_($list)
                    if ($paramValidator !== 0 && $param != $paramListName && $param != $this->paramListChoose) {
                        $vars[$paramListName][$param] = $this->listParameter[$paramListName][$param] ?: $this->settings->getDefinition($param);
                    }
                }
            }
        }
        return $vars;
    }
    public function adminSettingsOptions()
    {
        if (!$this->importedLists) {
            MapadoNotification::error(__('You have not uploaded a list yet', 'mapado-events'));
            return $this->adminSettingsImports();
        }
        $hasApi = !empty($this->clientInfo['id']) && !empty($this->clientInfo['secret']) && !empty($this->auth);
        if (!$hasApi) {
            MapadoNotification::error(__('You have not entered your API settings', 'mapado-events'));
            return $this->adminSettingsApi();
        }
        $this->listParameter = get_option(self::SETTINGS_WP_INDEX);
        $this->listParameter['importedLists'] = $this->getImportedLists($this->token);
        $this->listParameter['paramListChoose'] = $this->paramListChoose;
        /* Additional settings page submit */
        if (!empty($_POST['mapado_settings_submit'])) {
            $submitParameter = [];
            $upToDate = false;
            foreach ($this->param_list as $param) {
                $value = $_POST['mapado_' . $param] ?: '';
                $newValue = stripslashes($value);
                if ($vars[$this->paramListChoose][$param] != $newValue) {
                    // value set
                    if ('' != $newValue) {
                        $submitParameter[$param]['value'] = $newValue;
                        $paramValidator = strpos($param, 'param_');
                        // used to exclude parameters like param_mylist and others param_($list)
                        if ($paramValidator !== 0 && $param != $this->paramListChoose) {
                            if ($submitParameter[$param]['options'] == []) {
                                $submitParameter[$param]['options'] = $this->listParameter['all-list'][$param]['options'];
                            }
                            $paramToMerge = $this->listParameter[$this->paramListChoose][$param];
                            array_merge($paramToMerge, $submitParameter[$param]);
                        }
                        $upToDate = true;
                    }
                } else {
                    // value not set
                    $paramValidator = strpos($param, 'param_');
                    // used to exclude parameters like param_mylist and others param_($list)
                    if ($paramValidator !== 0 && $param != $this->paramListChoose) {
                        $submitParameter[$param]['value'] = $this->settings->getDefaultValue($param);
                        if ($submitParameter[$param]['options'] == []) {
                            $submitParameter[$param]['options'] = $this->listParameter['all-list'][$param]['options'];
                        }
                        $paramToMerge = $this->listParameter[$this->paramListChoose][$param];
                        array_merge($paramToMerge, $submitParameter[$param]);
                    }
                }
            }
            if ($submitParameter) {
                $this->listParameter[$this->paramListChoose] = array_merge($this->listParameter[$this->paramListChoose], $submitParameter);
                update_option(self::SETTINGS_WP_INDEX, $this->listParameter);
            }
            /* Something went wrong */
            if (!$upToDate) {
                MapadoNotification::error(__('There was a problem', 'mapado-events'));
                /* Success */
            } else {
                MapadoNotification::success(__('Saved settings', 'mapado-events'));
            }
        }
        return $this->renderAdminSetting('options', $this->listParameter);
    }
    /**
     * AJAX
     * Get user lists
     */
    public function ajaxGetUserLists()
    {
        $user_lists = $this->getImportedLists($this->token);
        MapadoUtils::template('admin/user_lists', ['user_lists' => $user_lists, 'importedLists' => $this->importedLists]);
        exit;
    }
    /**
     * AJAX
     * Save a user list settings
     */
    public function ajaxUpdateListSettings()
    {
        global $wpdb;
        if (empty($this->importedLists)) {
            $this->importedLists = [];
        }
        /* Add a list */
        if ('import' == $_POST['mapado_action']) {
            /* Slugify list slug */
            $slug = sanitize_title($_POST['slug']);
            /* Check if slug already exist */
            if ($wpdb->get_row('SELECT post_name FROM ' . $wpdb->prefix . "posts WHERE post_name = '" . $slug . "'", 'ARRAY_A')) {
                echo json_encode(['state' => 'error', 'msg' => __('The identifier is already used', 'mapado-events')]);
                exit;
            }
            $page = wp_insert_post(['post_title' => $_POST['title'], 'post_name' => $slug, 'post_content' => '[mapado_list]', 'post_status' => 'publish', 'post_type' => 'page', 'post_author' => 1, 'comment_status' => 'closed'], false);
            if (empty($page)) {
                echo json_encode(['state' => 'error', 'msg' => __('Trouble creating', 'mapado-events')]);
            } else {
                $this->importedLists[$_POST['uuid']] = $slug;
            }
            /* Delete a list & the associate page */
        } elseif ('delete' == $_POST['mapado_action']) {
            $page = get_page_by_path($_POST['slug']);
            wp_delete_post($page->ID, true);
            unset($this->importedLists[$_POST['uuid']]);
        }
        /* Something went wrong */
        if (!update_option('mapado_user_lists', $this->importedLists)) {
            echo json_encode(['state' => 'error', 'msg' => __('Error during the update', 'mapado-events')]);
            /* Success */
        } else {
            $this->registerRewriteRules();
            flush_rewrite_rules();
            echo json_encode(['state' => 'updated', 'msg' => __('Lists updated', 'mapado-events'), 'count' => count($this->importedLists)]);
        }
        exit;
    }
    /**
     * Adding plugin settings link in extensions list
     *
     * @param array $links of existing links
     * @param string $file plugin basename
     *
     * @return array of links updated
     */
    public function settingsPluginLink($links, $file)
    {
        array_unshift($links, '<a href="' . admin_url('admin.php?page=mapado_settings') . '">Réglages</a>');
        return $links;
    }
    protected function renderAdminSetting($step, $extraVars = [])
    {
        if (isset($_GET['noheader'])) {
            require_once ABSPATH . 'wp-admin/admin-header.php';
        }
        $hasApi = !empty($this->clientInfo['id']) && !empty($this->clientInfo['secret']) && !empty($this->auth);
        $hasImportedLists = $hasApi && $this->importedLists;
        $vars = ['step' => $step, 'notification_list' => MapadoNotification::pull(), 'settings' => $this->settings, 'api' => $this->clientInfo, 'auth' => $this->auth, 'has_api' => $hasApi, 'has_imported_lists' => $hasImportedLists, 'user_lists' => $this->importedLists, 'paramListChoose' => $this->paramListChoose];
        $vars = array_merge($extraVars, $vars);
        MapadoUtils::template('admin/settings', $vars);
        return true;
    }
    protected function adminSettingRedirect($step)
    {
        wp_redirect(admin_url('admin.php?page=mapado_settings_step_' . $step));
        return true;
    }
    /**
     * Get the private auth key from WP settings
     */
    private function setAuth()
    {
        $this->auth = get_option(parent::AUTH_WP_INDEX) ?: null;
    }
    /**
     * Generate token from auth key
     */
    private function setToken()
    {
        if ($this->auth) {
            $tab = ['access_token' => $this->auth];
            $this->token = new \League\OAuth2\Client\Token\AccessToken($tab);
        }
    }
    private function cleanRestClientCache()
    {
        $dirname = $this->getCacheDirectory();
        if (is_dir($dirname)) {
            array_map('unlink', glob($dirname . '/*.*'));
            rmdir($dirname);
        }
    }
    /**
     * migrateAPIV1ToAPIV2
     */
    private function migrateAPIV1ToAPIV2()
    {
        $headers = ['headers' => ['Authorization' => 'Bearer ' . $this->token]];
        $client = new GuzzleHttp\Client();
        $hasMigrate = false;
        $currentList = get_option(self::USERLISTS_WP_INDEX);
        // these are the lists imported from a user
        $newList = [];
        $uuidToSlug = [];
        // Browse user's imported lists
        foreach ($currentList as $key => $slug) {
            // Preg_match if $key is old version of API
            if (preg_match('#^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}$#', $key)) {
                $hasMigrate = true;
                $requestPluginV1 = $client->get('https://api.mapado.com/v1/activities/' . $key, $headers);
                // it has to be done in 2 parts
                $contents = json_decode($requestPluginV1->getBody()->getContents());
                $mapadoUrl = $contents->_links->mapado_url->href;
                if ($mapadoUrl) {
                    // Preg_split $mapadoUrl to get only the value we need (something like this : lists/282404-mylist)
                    $urlSplit = preg_split("#(?>^https:\\/\\/www\\.mapado\\.com(?>\\/[a-z]{2}\\/))|(?>^https:\\/\\/www\\.mapado\\.com\\/)#", $mapadoUrl);
                    if ($urlSplit) {
                        $newKey = preg_replace('#/#', '--', $urlSplit[1]);
                        $uuidToSlug[$key] = $newKey;
                        // add [$newKey => $slug] to newList[]
                        $newList[$newKey] = $slug;
                    }
                }
            }
        }
        if ($hasMigrate) {
            // migrate widget listing templates and slugs
            $eventWidgetOption = get_option('widget_mapado_event_widget');
            foreach ($eventWidgetOption as $key => &$value) {
                if (is_array($value) && isset($value['list_uuid'])) {
                    $value['list_name'] = $uuidToSlug[$value['list_uuid']];
                    unset($value['list_uuid']);
                    require_once MAPADO_PLUGIN_PATH . 'class/widget_default_template.php';
                    global $widget_card_template_default;
                    $value['widget_template'] = $widget_card_template_default;
                }
            }
            update_option('widget_mapado_event_widget', $eventWidgetOption);
            // if plugin has migrate from V1 to V2 --> reset value of template
            $this->settings->resetValue('card_template', $this->settings->getCardTemplateDefault());
            $this->settings->resetValue('full_template', $this->settings->getFullTemplateDefault());
            $this->settings->resetValue('widget_card_template', $this->settings->getWidgetCardTemplateDefault());
            delete_option(parent::TOKEN_WP_INDEX);
            update_option(self::USERLISTS_WP_INDEX, $newList);
        }
    }
    /**
     * Get the user lists
     *
     * @return ?Collection user's list
     */
    private function getLists($token)
    {
        $param = ['fields' => '@id,id,title,apiSlug,slug,visible,locale'];
        if (empty($this->user) && empty($token)) {
            return;
        } elseif (!empty($token)) {
            $userId = $this->getUserID($token);
            // retrieves user lists from user's ID
            return $this->getClient($token)->getRepository(User::class)->getListsImported($userId, $param);
        }
    }
    /**
     * Get user imported lists
     */
    private function getImportedLists($token)
    {
        $importedLists = [];
        $userLists = $this->getLists($token);
        if ($userLists) {
            foreach ($userLists as $list) {
                // import only visible lists
                if ($list->getVisible()) {
                    $title = $list->getTitle();
                    $slug = $list->getApiSlug();
                    $importedList['slug'] = $slug;
                    $importedList['title'] = $title;
                    $importedLists[] = $importedList;
                }
            }
        }
        return $importedLists;
    }
    /**
     * Get the User
     *
     * @return ?User
     */
    private function getUser($token)
    {
        $param = ['fields' => '@id'];
        if (empty($this->user) && empty($token)) {
            return;
        } elseif (empty($this->user) && !empty($token)) {
            try {
                $this->user = $this->getClient($token)->getRepository(User::class)->me($param);
            } catch (RestClientException $e) {
                $message = $e->getMessage();
                $previous = $e->getPrevious();
                if ($previous) {
                    $message .= "\n" . $previous->getMessage();
                }
                MapadoUtils::template('error', ['message' => $message]);
            }
        }
        return $this->user;
    }
    /**
     * Get the User ID
     *
     * @return ?int userId
     */
    private function getUserID($token)
    {
        $user = $this->getUser($token);
        if (!$user) {
            return;
        }
        $userId = $user->getId();
        $split = preg_split("#\\/v2\\/me\\?id\\=#", $userId);
        if (!$split[1]) {
            return;
        }
        if (!preg_match('#[0-9]{3,}$#', $split[1])) {
            return;
        }
        return (int) $split[1];
    }
}