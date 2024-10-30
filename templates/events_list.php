<?php

/*
 * Mapado Plugin
 * Events list TPL
 */
?>

<!-- List -->
<div id="mapado-plugin">

    <?php 
if ($vars['display_search']) {
    ?>
        <div class="mpd-search-bar">
            Filtrer <br/>
            <form method="GET" action="<?php 
    echo MapadoUtils::getUserListUrl($vars['list_slug']);
    ?>">
                <div class="mpd-form-group">
                    <!-- <label>Ville</label> -->
                    <input placeholder="rechercher (ville, mot clé, ...)" type="text" name="mpd-address" id="search-filter-address"
                        value="<?php 
    echo $vars['search_filters']['address']['value'];
    ?>"
                        data-label="<?php 
    echo $vars['search_filters']['address']['label'];
    ?>"/>
                </div>
                <div class="mpd-form-group">
                    <!-- <label>Quand</label> -->
                    <select name="mpd-when" id="mpd-when">
                        <option value="soon">Prochainement</option>
                        <option value="chooseDate">Choisir une date</option>
                        <option value="today">Aujourd&#039;hui</option>
                        <option value="tomorrow">Demain</option>
                        <option value="weekend">Ce week-end</option>
                    </select>
                    <!-- script to detect if user select chooseDate -> display datepicker-->
                    <script>
                        document.getElementById('mpd-when').addEventListener('change', function (optionSelected) {
                        if (optionSelected.target.value === "chooseDate") {
                            document.getElementById('chooseDate').style.display = "block";
                        } else{
                            document.getElementById('chooseDate').style.display = "none";
                        }
                        });
                    </script>
                </div>
                <div class="mpd-form-group" id="chooseDate" style="display:none;width:100%;">
                    <input type="date" name="mpd-date" data-inline="true" require="false">
                </div>
                <div class="mpd-form-group">
                    <button type="submit">Valider</button>
                </div>
            </form>
        </div>
    <?php 
}
?>

    <?php 
$modifier = $vars['card_thumb_design']['size'];
if ('top' == $vars['card_thumb_design']['position_side']) {
    $modifier = 'top';
}
?>
    <div class="chew-row chew-row--<?php 
echo $vars['card_column_max'];
?> chew-row--thumb-<?php 
echo $modifier;
?>">
        <?php 
foreach ($vars['events'] as $activity) {
    $vars['activity'] = $activity;
    MapadoUtils::template('event_card', $vars);
}
$ghostSize = 5;
if ('auto' !== $vars['card_column_max']) {
    $ghostSize = $vars['card_column_max'] - 1;
}
for ($i = 0; $i < $ghostSize; ++$i) {
    ?>
            <li class="chew-cell chew-cell--ghost">
            </li>
        <?php 
}
?>
    </div>

    <div class="mpd-card-list__footer">

        <!-- Pagination -->
        <?php 
if ($vars['pagination']['nb_pages'] > 1) {
    $pagination_bounding = 2;
    $current_page = $vars['pagination']['page'];
    $start_page = 1;
    $end_page = $vars['pagination']['nb_pages'];
    $pagination = [];
    if ($current_page <= $start_page + ($pagination_bounding * 2 + 1)) {
        $begin_pagination = $start_page;
    } else {
        $begin_pagination = $current_page - $pagination_bounding;
    }
    if ($current_page >= $end_page - ($pagination_bounding * 2 + 1)) {
        $end_pagination = $end_page;
    } else {
        $end_pagination = $current_page + $pagination_bounding;
    }
    $list_slug = $vars['list_slug'];
    $pagination_link = function ($page, $label = false) use($list_slug) {
        if (!$label) {
            $label = $page;
        }
        $mpd_address = get_query_var('mpd-address') ? 'mpd-address=' . get_query_var('mpd-address') : null;
        $mpd_when = get_query_var('mpd-when') ? 'mpd-when=' . get_query_var('mpd-when') : null;
        $mpd_date = get_query_var('mpd-date') ? 'mpd-date=' . get_query_var('mpd-date') : null;
        if ($mpd_address || $mpd_when || $mpd_date) {
            echo '<a href="' . MapadoUtils::getUserListUrl($list_slug, $page) . '?' . $mpd_address . '&' . $mpd_when . '&' . $mpd_date . '"' . 'class="mpd-pagination__item">' . $label . '</a>';
        } else {
            echo '<a href="' . MapadoUtils::getUserListUrl($list_slug, $page) . '" class="mpd-pagination__item">' . $label . '</a>';
        }
    };
    $pagination_span = function ($label, $class = '') {
        if (!empty($class)) {
            $class = 'mpd-pagination__item--' . $class;
        }
        echo '<span class="mpd-pagination__item ' . $class . '">' . $label . '</span>';
    };
    ?>
            <div class="mpd-pagination">
                <?php 
    if ($current_page > $start_page) {
        $pagination_link($current_page - 1, '<');
    }
    if ($begin_pagination > $start_page) {
        for ($page = $start_page; $page < $start_page + $pagination_bounding; ++$page) {
            $pagination_link($page);
        }
        $pagination_span('...');
    }
    for ($page = $begin_pagination; $page < $current_page; ++$page) {
        $pagination_link($page);
    }
    $pagination_span($current_page, 'current');
    for ($page = $current_page + 1; $page <= $end_pagination; ++$page) {
        $pagination_link($page);
    }
    if ($end_pagination < $end_page) {
        $pagination_span('...');
        for ($page = $end_page - $pagination_bounding + 1; $page <= $end_page; ++$page) {
            $pagination_link($page);
        }
    }
    if ($current_page < $end_page) {
        $pagination_link($current_page + 1, '>');
    }
    ?>
            </div>
        <?php 
}
?>
        <div class="mpd-credits">
            <?php 
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$plugin_version = '0.5.0';
if (!isset($current_page) || $current_page <= 1) {
    echo get_the_title() . " avec <a href='https://www.mapado.com' target='_blank'>Mapado</a> (API et plugin Wordpress version " . $plugin_version . ')';
} else {
    echo 'Agenda avec Mapado (API et plugin Wordpress version ' . $plugin_version . ')';
}
?>
        </div>
    </div>
</div>

