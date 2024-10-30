<?php

/**
 * Class MapadoUtils
 * Utility functions
 */
class MapadoUtils
{
    /**
     * Mapado template call
     *
     * @param string $file path file in 'templates' folder
     * @param array $vars variables to send to the template
     */
    public static function template($file, $vars = [])
    {
        require MAPADO_PLUGIN_PATH . 'templates/' . $file . '.php';
    }
    /**
     * Build user list url based on WP permalink settings
     *
     * @param string $list_slug user list slug
     * @param int $page
     *
     * @return ?string url
     */
    public static function getUserListUrl($list_slug, $page = 1)
    {
        $url = get_permalink(get_page_by_path($list_slug));
        if (false === $url) {
            return;
        }
        if ($page > 1) {
            /* Rewrite url */
            if ('' != get_option('permalink_structure')) {
                /* Adding the last slash when permalink structure doesn't have it */
                $last_slash = substr($url, -1);
                if ('/' != $last_slash) {
                    $url .= '/';
                }
                $url .= 'page/' . $page;
            } else {
                $url = add_query_arg('paged', $page);
            }
        }
        return user_trailingslashit($url);
    }
    /**
     * Get event place URL or event URL if not
     *
     * @param array $links event links
     *
     * @return string url
     */
    public static function getPlaceUrl($links)
    {
        $url = '';
        if (!empty($links['mapado_place_url'])) {
            $url = $links['mapado_place_url']['href'];
        } elseif (!empty($links['mapado_url'])) {
            $url = $links['mapado_url']['href'];
        }
        return $url;
    }
    public static function link_back_to_event_list_home()
    {
        $postTitle = single_post_title('', false);
        if (!is_string($postTitle)) {
            return;
        }
        echo '<a href="';
        the_permalink();
        echo '">' . $postTitle . '</a> ';
    }
}