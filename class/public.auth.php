<?php

use GuzzleHttp\Client;
use League\OAuth2\Client\Token\AccessToken;
use Mapado\Sdk\Entity\Activity;
use Mapado\Sdk\Entity\User;
/**
 * Class MapadoPublicAuth
 * For public area
 */
class MapadoPublicAuth extends MapadoPlugin
{
    private $token;
    private $event_displayed = false;
    private $eventListingWidgetDisplayed = false;
    public function __construct()
    {
        $this->setDatas();
        $this->setToken();
        add_action('wp_enqueue_scripts', [&$this, 'enqueuePublicStyleandScript'], 15);
        add_filter('the_content', [&$this, 'mapadoPagesFactory'], 10);
        add_filter('the_title', [&$this, 'eventPageTitle'], 15);
        add_filter('pre_get_document_title', [&$this, 'eventWpTitle'], 30);
        add_filter('wp_title', [&$this, 'eventWpTitle'], 30);
        add_filter('query_vars', [&$this, 'initQueryVars']);
        add_action('init', [&$this, 'canonical_init']);
    }
    public function canonical_init()
    {
        remove_action('wp_head', 'rel_canonical');
        add_action('wp_head', [&$this, 'mapado_rel_canonical']);
    }
    public function mapado_rel_canonical()
    {
        // Patched wordpress rel_canonical function
        global $wp_query, $post;
        if (!is_singular()) {
            return;
        }
        if (!($id = get_queried_object_id())) {
            return;
        }
        // Specific canonical rules for mapado events
        if (is_page() && !empty($this->importedLists) && false !== ($list_name = array_search($post->post_name, $this->importedLists))) {
            if (!empty($wp_query->query_vars['mapado_event'])) {
                $current_event = $this->getActivity($wp_query->query_vars['mapado_event'], $this->token);
                if (!empty($current_event)) {
                    $links = $current_event->getUrlList()['ticket'];
                    if (!empty($links)) {
                        echo '<link rel="canonical" href="' . $links . '"/>';
                    }
                }
            }
            return;
        }
        $url = get_permalink($id);
        if (false === $url) {
            return;
        }
        $page = get_query_var('page');
        if ($page >= 2) {
            if ('' == get_option('permalink_structure')) {
                $url = add_query_arg('page', $page, $url);
            } else {
                $url = trailingslashit($url) . user_trailingslashit($page, 'single_paged');
            }
        }
        $cpage = get_query_var('cpage');
        if ($cpage) {
            $url = get_comments_pagenum_link($cpage);
        }
        echo '<link rel="canonical" href="' . esc_url($url) . "\" />\n";
    }
    public function initQueryVars($queryVars)
    {
        $queryVars[] = 'mpd-address';
        $queryVars[] = 'mpd-when';
        $queryVars[] = 'mpd-date';
        return $queryVars;
    }
    /**
     * Enqueue style in public area
     */
    public function enqueuePublicStyleandScript()
    {
        wp_enqueue_style('mapado-plugin', MAPADO_PLUGIN_URL . 'assets/mapado_plugin.css');
        wp_enqueue_style('mapado-card', MAPADO_PLUGIN_URL . 'assets/mapado_card.css');
        if ($this->settings->getValue('display_search')) {
            wp_enqueue_script('typeahead.jquery.min.js', '//cdn.jsdelivr.net/typeahead.js/0.10.5/typeahead.jquery.min.js', ['jquery']);
            wp_enqueue_script('algoliasearch.min.js', '//cdn.jsdelivr.net/algoliasearch/3/algoliasearch.min.js', ['jquery']);
            wp_enqueue_script('mapado-search.js', MAPADO_PLUGIN_URL . 'assets/js/search.js', ['algoliasearch.min.js', 'typeahead.jquery.min.js']);
        }
    }
    /**
     * Select template renderer bases on page type
     *
     * @param string $content post content
     *
     * @return string list html
     */
    public function mapadoPagesFactory($content)
    {
        global $wp_query, $post;
        $template_output = null;
        if (!empty($this->importedLists)) {
            $list_name = array_search($post->post_name, $this->importedLists);
            $listChoosen = empty($this->listParameter['paramListChoose']) ? 'all-list' : 'param_' . $post->post_name;
            if (is_page() && in_the_loop() && false !== $list_name) {
                if (empty($wp_query->query_vars['mapado_event'])) {
                    // Listing page
                    $template_output = $this->eventListingFactory($list_name, $this->listParameter[$listChoosen]);
                    if (empty($wp_query->query_vars['paged']) || 0 == $wp_query->query_vars['paged']) {
                        // 1st listing page : display listing + post content
                        $template_output = str_replace('[mapado_list]', $template_output, $content);
                    }
                } else {
                    // Single event page
                    $template_output = $this->eventSinglePageFactory($this->listParameter[$listChoosen]);
                }
            }
        }
        if (empty($template_output)) {
            return $content;
        } else {
            return $template_output;
        }
    }
    /**
     * Render event listing template
     *
     * @param string $list_name
     *
     * @return ?string rendered template
     */
    public function eventListingFactory($list_name, $paramList)
    {
        global $wp_query, $post;
        if (empty($this->event_displayed)) {
            $this->event_displayed = true;
        } else {
            return;
        }
        /* Check token validity */
        try {
            $client = $this->getClient($this->token);
        } catch (\InvalidArgumentException $e) {
            return __('Listing page: Unauthorized access, check your Mapado credentials.', 'mapado-events');
        }
        /* Pagination */
        $page = 1;
        // default value
        if (!empty($paramList['perpage']['value'])) {
            $perpage = $paramList['perpage']['value'];
        } else {
            $perpage = 10;
            // default value
        }
        /* Sort */
        if (!empty($paramList['list_sort']['value'])) {
            $sort = $paramList['list_sort']['value'];
        } else {
            $sort = 'api_topevents-date';
        }
        /* List Depth */
        if (!empty($paramList['list_depth']['value'])) {
            $list_depth = $paramList['list_depth']['value'];
        } else {
            $list_depth = 1;
        }
        if (!empty($wp_query->query_vars['paged'])) {
            $page = $wp_query->query_vars['paged'];
        }
        $searchFilters = ['address' => null];
        $pagination = ['perpage' => $perpage, 'page' => $page];
        if ($paramList['past_events']['value']) {
            $accept_past_events = $paramList['past_events']['value'];
        } else {
            $accept_past_events = false;
            // default
        }
        $pastEvents = $paramList['past_events']['value'] ? 'all' : false;
        /* search by parameter */
        $address = get_query_var('mpd-address') ?: null;
        $when = $pastEvents != false ? 'all' : get_query_var('mpd-when');
        $date = get_query_var('mpd-date') ?: null;
        if ($date) {
            // if user select a date in dateTime picker -> priority to datetime Picker
            $when = $date;
        }
        if (!empty($paramList['list_sort']['value'])) {
            $listSort = $paramList['list_sort']['value'];
        } else {
            $listSort = 'api_dateonly-noimg';
            // default
        }
        $results = $this->getActivities($pagination['perpage'], $pagination['page'], $this->token, $list_name, $address, $when, $listSort);
        if (!$results) {
            return;
        }
        $totalHits = $results->getExtraProperties()['hydra:totalItems'];
        $pagination['nb_pages'] = ceil($totalHits / $pagination['perpage']);
        /* Card design */
        $card_thumb_design = $this->getCardThumbDesign($paramList);
        if (!empty($paramList['card_column_max']['value'])) {
            $card_column_max = $paramList['card_column_max']['value'];
        } else {
            $card_column_max = 2;
            // default
        }
        if (!empty($paramList['card_template']['value'])) {
            $template = new MapadoMicroTemplate($paramList['card_template']['value']);
        } else {
            $template = new MapadoMicroTemplate($this->settings->getCardTemplateDefault());
            // if no template value --> get the default value
        }
        if (preg_match('#rubric#', $template->getTemplate())) {
            // template contains old variable "rubric" --> it's Plugin V1 --> need to get the default template
            $template = new MapadoMicroTemplate($this->settings->getCardTemplateDefault());
        }
        $displaySearch = $paramList['display_search']['value'];
        ob_start();
        MapadoUtils::template('events_list', ['list_name' => $list_name, 'list_slug' => $post->post_name, 'events' => $results, 'pagination' => $pagination, 'card_column_max' => $card_column_max, 'card_thumb_design' => $card_thumb_design, 'template' => $template, 'accept_past_events' => $accept_past_events, 'display_search' => $displaySearch, 'search_filters' => $searchFilters]);
        $template_output = ob_get_contents();
        ob_end_clean();
        return $template_output;
    }
    /**
     * Filtering post content for event single page
     * Replace page content by event content
     *
     * @return ?string filtered content
     */
    public function eventSinglePageFactory($paramList)
    {
        global $wp_query;
        $current_event = $this->getActivity($wp_query->query_vars['mapado_event'], $this->token);
        if (empty($current_event)) {
            return __('Listing page: Unauthorized access, check your Mapado credentials.', 'mapado-events');
        }
        if (empty($this->event_displayed)) {
            $this->event_displayed = true;
        } else {
            return;
        }
        if (!empty($paramList['full_template']['value'])) {
            $template = new MapadoMicroTemplate($paramList['full_template']['value']);
        } else {
            $template = new MapadoMicroTemplate($this->settings->getFullTemplateDefault());
        }
        $widgetActive = $paramList['widget']['value'];
        // Instanciate activity detail image Size from mapado settings
        if ($paramList['full_thumb_size']['value'] == 's') {
            $imageWidth = "500";
            $imageHeight = "120";
        } elseif ($paramList['full_thumb_size']['value'] == 'm') {
            $imageWidth = "500";
            $imageHeight = "200";
        } else {
            $imageWidth = "500";
            $imageHeight = "280";
        }
        ob_start();
        MapadoUtils::template('event_single', ['activity' => $current_event, 'template' => $template, 'widgetActive' => $widgetActive, 'imageDetailWidth' => $imageWidth, 'imageDetailHeight' => $imageHeight]);
        $template_output = ob_get_contents();
        ob_end_clean();
        return $template_output;
    }
    /**
     * Show event details in a widget
     */
    public function eventDetailWidget()
    {
        global $wp_query, $post;
        $listChoosen = 'param_' . $post->post_name;
        $widget = $this->listParameter[$listChoosen]['widget']['value'] ?: false;
        if (is_page() && $widget && $wp_query->query_vars['mapado_event']) {
            $current_event = $this->getActivity($wp_query->query_vars['mapado_event'], $this->token);
            MapadoUtils::template('widget', ['event' => $current_event]);
        }
    }
    /**
     * Show event listing in a widget
     */
    public function eventListingWidget($widget_template, $thumbnail_width, $thumbnail_height, $list_name, $nb_displayed, $list_depth)
    {
        if ($this->eventListingWidgetDisplayed) {
            return;
        }
        $this->eventListingWidgetDisplayed = true;
        /* Check token validity */
        try {
            $client = $this->getClient($this->token);
        } catch (\InvalidArgumentException $e) {
            return __('Listing page: Unauthorized access, check your Mapado credentials.', 'mapado-events');
        }
        /* Sort */
        $sort_model = 'api_topevents-date';
        $list_slug = $this->importedLists[$list_name];
        // get Activies depending on the selected list
        $results = $this->getActivities($nb_displayed, 1, $this->token, $list_name);
        $template = new MapadoMicroTemplate($widget_template);
        /* Card design */
        $card_thumb_design = ['position_type' => 'bandeau', 'position_side' => 'top', 'orientation' => 'landscape', 'size' => 'm', 'dimensions' => [$thumbnail_width, $thumbnail_height], 'id' => '300x200'];
        MapadoUtils::template('events_list_widget', ['name' => $list_name, 'list_slug' => $list_slug, 'events' => $results, 'card_thumb_design' => $card_thumb_design, 'template' => $template]);
    }
    /**
     * Filtering WP title for event single page
     *
     * @param string $title original title
     *
     * @return string filtered title
     */
    public function eventWpTitle($title)
    {
        global $post, $wp_query;
        if (is_page() && !empty($this->importedLists) && false !== ($list_name = array_search($post->post_name, $this->importedLists)) && !empty($wp_query->query_vars['mapado_event'])) {
            $current_event = $this->getActivity($wp_query->query_vars['mapado_event'], $this->token);
            if (!empty($current_event)) {
                $title = $current_event->getTitle() . ' | ' . get_bloginfo('name');
            }
        }
        return $title;
    }
    /**
     * Filtering post title for event single page
     *
     * @param string $title original title
     *
     * @return string filtered title
     */
    public function eventPageTitle($title)
    {
        global $post, $wp_query;
        if (is_page() && $title == single_post_title('', false) && !empty($this->importedLists) && false !== ($list_name = array_search($post->post_name, $this->importedLists)) && !empty($wp_query->query_vars['mapado_event'])) {
            $current_event = $this->getActivity($wp_query->query_vars['mapado_event'], $this->token);
            if (!empty($current_event)) {
                $title = $current_event->getTitle();
            }
        }
        return $title;
    }
    /**
     * Calculate the thumb size in card listing according to admin settings
     */
    protected function getCardThumbDesign($paramList)
    {
        $card_thumb_position_type = 'side';
        $card_thumb_position_side = $paramList['card_thumb_position']['value'];
        $card_thumb_orientation = $paramList['card_thumb_orientation']['value'];
        $card_thumb_size = $paramList['card_thumb_size']['value'];
        $card_thumb_ratio = 2;
        if ('m' == $card_thumb_size) {
            $card_thumb_ratio = 1;
        } elseif ('s' == $card_thumb_size) {
            $card_thumb_ratio = 0;
        }
        if ('top' == $card_thumb_position_side) {
            $card_thumb_position_type = 'bandeau';
            $card_thumb_dimensions = [500, 120 + $card_thumb_ratio * 80];
            $card_thumb_id = implode('x', $card_thumb_dimensions);
        } else {
            $card_thumb_dimensions = [200, 300];
            if ('landscape' == $card_thumb_orientation) {
                $card_thumb_dimensions = [300, 200];
            } elseif ('square' == $card_thumb_orientation) {
                $card_thumb_dimensions = [300, 300];
            }
            $card_thumb_id = implode('x', $card_thumb_dimensions);
            foreach ($card_thumb_dimensions as $dimension => $val) {
                $card_thumb_dimensions[$dimension] = $val * (2 + $card_thumb_ratio) / 4;
            }
        }
        return ['position_type' => $card_thumb_position_type, 'position_side' => $card_thumb_position_side, 'orientation' => $card_thumb_orientation, 'size' => $card_thumb_size, 'dimensions' => $card_thumb_dimensions, 'id' => $card_thumb_id];
    }
    /**
     * getActivity
     * Get an activity based on parameter apiSlug
     *
     * @return ?Activity
     */
    private function getActivity($apiSlug, $token)
    {
        $param = ['fields' => '@id,description,title,image,shortDate,address,frontPlaceName,urlList,emailList,phoneList,place{title,urlList},priceList'];
        if (empty($this->activityCacheBySlug[$apiSlug])) {
            $client = $this->getClient($token);
            try {
                $this->activityCacheBySlug[$apiSlug] = $this->getClient($token)->getRepository(Activity::class)->find($apiSlug, $param);
            } catch (RestException $e) {
                $message = $e->getMessage();
                $previous = $e->getPrevious();
                if ($previous) {
                    $message .= "\n" . $previous->getMessage();
                }
                MapadoUtils::template('error', ['message' => $message]);
            }
        }
        return $this->activityCacheBySlug[$apiSlug];
    }
    /**
     * Get a list of Activity
     *
     * @param int $perpage
     * @param int $page
     * @param string $token
     * @param ?string $list_name
     * @param ?string $search
     * @param ?string $when
     * @param ?string $listSort
     *
     * @return ?Collection
     */
    private function getActivities($perpage, $page = 1, $token, $list_name = null, $search = null, $when = null, $listSort = null)
    {
        $param = ['fields' => '@id,title,apiSlug,image,address,shortDate,shortDescription,frontPlaceName,place{title,urlList}', 'itemsPerPage' => $perpage, 'parent' => $list_name ?: null, 'page' => $page, 'search' => $search ?: null, 'when' => $when ?: null, 'sortModel' => $listSort ?: null];
        try {
            $activityList = $this->getClient($token)->getRepository(Activity::class)->findBy($param);
        } catch (RestException $e) {
            $message = $e->getMessage();
            $previous = $e->getPrevious();
            if ($previous) {
                $message .= "\n" . $previous->getMessage();
            }
            MapadoUtils::template('error', ['message' => $message]);
            return;
        }
        return $activityList;
    }
    /**
     * Settings token
     * Cached or not in WP database
     */
    private function setToken($forceRefresh = false)
    {
        $token_cache = get_option(parent::TOKEN_WP_INDEX);
        /* Get cached token */
        if (!$forceRefresh && $token_cache instanceof AccessToken && !$token_cache->hasExpired()) {
            $this->token = $token_cache;
        } elseif (!empty($this->clientInfo['id']) && !empty($this->clientInfo['secret'])) {
            try {
                $provider = new \Mapado\LeagueOAuth2Provider\Provider\MapadoOAuth2Provider([
                    'clientId' => $this->clientInfo['id'],
                    // The client ID assigned to you by the provider
                    'clientSecret' => $this->clientInfo['secret'],
                ]);
                $this->token = $provider->getAccessToken('client_credentials', ['scope' => 'activity:all:read']);
                update_option(parent::TOKEN_WP_INDEX, $this->token);
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                error_log($e->getMessage());
            }
        }
    }
}