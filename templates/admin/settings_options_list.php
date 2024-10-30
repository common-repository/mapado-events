<?php

$listChoose = $vars['paramListChoose'];
?>
<div class="mapado_container">
    <div class="mapado_container_left">
        <?php 
foreach ($vars['user_lists'] as $list) {
    ?>
            <?php 
    $isChoosenList = $vars['paramListChoose'] == 'param_' . $list;
    $choosenListClass = $isChoosenList ? 'mapado_admin_options__lists--selected' : '';
    ?>
                <div class="mapado_admin_options__lists <?php 
    echo $choosenListClass;
    ?>">
                    <a
                        href="<?php 
    echo admin_url('admin.php?page=mapado_settings_step_options&list_choose=param_' . $list);
    ?>"
                        class="mapado_admin_options__list_detail"
                    >
                        <?php 
    echo $list;
    ?>
                    </a>
                    <a
                        href="<?php 
    echo MapadoUtils::getUserListUrl($list);
    ?>" target="_blank" rel="noreferer noopener"
                        class="mapado_admin_options__list_external_link"
                    >
                        <img
                            class="mapado_admin_options__list_external_link_image--white"
                            src="<?php 
    echo plugins_url('mapado-events/assets/images/arrow_square_white.png');
    ?>"
                            height="16px"
                            width="18px"
                        />
                        <img
                            class="mapado_admin_options__list_external_link_image--black"
                            src="<?php 
    echo plugins_url('mapado-events/assets/images/arrow_square_black.png');
    ?>"
                            height="16px"
                            width="18px"
                        />
                    </a>
                </div>
        <?php 
}
?>
    </div>
    <div class="mapado_container_right">
        <table id="mapado_option_list_table_option" class="mapado_table widefat">
            <tbody>

            <!-- Column max -->
            <tr>
                <td>
                    <span class="mapado_table__label"><?php 
echo __('Maximum number of columns', 'mapado-events');
?> </span>
                </td>
                <td class="mapado_option_list_table_td_right">
                    <?php 
foreach ($vars[$listChoose]['card_column_max']['options'] as $key => $val) {
    ?>
                        <label class="mapado-input-radio">
                            <input type="radio" name="mapado_card_column_max" value="<?php 
    echo $key;
    ?>"
                                <?php 
    if ($vars[$listChoose]['card_column_max']['value'] == $key) {
        echo 'checked="checked"';
    }
    ?>
                                >
                            <span>
                                <img src="<?php 
    echo plugins_url('mapado-events/assets/images/' . $key . '-column.png');
    ?>" alt="<?php 
    echo $val;
    ?>" title="<?php 
    echo $val;
    ?>" />
                                <img src="<?php 
    echo plugins_url('mapado-events/assets/images/' . $key . '-column-active.png');
    ?>" alt="<?php 
    echo $val;
    ?>" title="<?php 
    echo $val;
    ?>" />
                            </span>
                        </label>
                    <?php 
}
?>
                </td>
            </tr>

            <!-- Event par page -->
            <tr>
                <td>
                    <span class="mapado_table__label"><?php 
echo __('Number of events per page', 'mapado-events');
?> </span>
                </td>
                <td class="mapado_option_list_table_td_right">
                    <?php 
foreach ($vars[$listChoose]['perpage']['options'] as $val) {
    ?>
                        <label class="mapado-input-radio">
                            <input type="radio" name="mapado_perpage" value="<?php 
    echo $val;
    ?>"
                                <?php 
    if ($vars[$listChoose]['perpage']['value'] == $val) {
        echo 'checked="checked"';
    }
    ?>
                                >
                            <span><?php 
    echo $val;
    ?></span>
                        </label>
                    <?php 
}
?>
                </td>
            </tr>

            <!-- Image position -->
            <tr>
                <td>
                    <span class="mapado_table__label"><?php 
echo __('Image position', 'mapado-events');
?> </span>
                </td>
                <td class="mapado_option_list_table_td_right">
                    <?php 
foreach ($vars[$listChoose]['card_thumb_position']['options'] as $key => $val) {
    ?>
                        <label class="mapado-input-radio">
                            <input type="radio" name="mapado_card_thumb_position" value="<?php 
    echo $key;
    ?>"
                                <?php 
    if ($vars[$listChoose]['card_thumb_position']['value'] == $key) {
        echo 'checked="checked"';
    }
    ?>
                                >
                            <span>
                                <img src="<?php 
    echo plugins_url('mapado-events/assets/images/img-' . $key . '.png');
    ?>" alt="<?php 
    echo $val;
    ?>" title="<?php 
    echo $val;
    ?>" />
                                <img src="<?php 
    echo plugins_url('mapado-events/assets/images/img-' . $key . '-active.png');
    ?>" alt="<?php 
    echo $val;
    ?>" title="<?php 
    echo $val;
    ?>" />
                            </span>
                        </label>
                    <?php 
}
?>
                </td>
            </tr>

            <!-- Image orientation -->
            <tr>
                <td>
                    <span class="mapado_table__label"><?php 
echo __('Image orientation', 'mapado-events');
?> </span>
                </td>
                <td class="mapado_option_list_table_td_right">
                        <label class="mapado-input-radio">
                            <input type="radio" name="mapado_card_thumb_orientation" value="portrait"
                                <?php 
if ($vars[$listChoose]['card_thumb_orientation']['value'] == 'portrait') {
    echo 'checked="checked"';
}
?>
                                >
                            <span><?php 
echo __('Portrait', 'mapado-events');
?></span>
                        </label>
                        <label class="mapado-input-radio">
                            <input type="radio" name="mapado_card_thumb_orientation" value="landscape"
                                <?php 
if ($vars[$listChoose]['card_thumb_orientation']['value'] == 'landscape') {
    echo 'checked="checked"';
}
?>
                                >
                            <span><?php 
echo __('Landscape', 'mapado-events');
?></span>
                        </label>
                        <label class="mapado-input-radio">
                            <input type="radio" name="mapado_card_thumb_orientation" value="square"
                                <?php 
if ($vars[$listChoose]['card_thumb_orientation']['value'] == 'square') {
    echo 'checked="checked"';
}
?>
                                >
                            <span><?php 
echo __('Square', 'mapado-events');
?></span>
                        </label>
                </td>
            </tr>

            <!-- Image size -->
            <tr>
                <td>
                    <span class="mapado_table__label"><?php 
echo __('Image size', 'mapado-events');
?></span>
                </td>
                <td class="mapado_option_list_table_td_right">
                        <label class="mapado-input-radio">
                            <input type="radio" name="mapado_card_thumb_size" value="l"
                                <?php 
if ($vars[$listChoose]['card_thumb_size']['value'] == 'l') {
    echo 'checked="checked"';
}
?>
                                >
                            <span><?php 
echo __('Large', 'mapado-events');
?></span>
                        </label>
                        <label class="mapado-input-radio">
                            <input type="radio" name="mapado_card_thumb_size" value="m"
                                <?php 
if ($vars[$listChoose]['card_thumb_size']['value'] == 'm') {
    echo 'checked="checked"';
}
?>
                                >
                            <span><?php 
echo __('Average', 'mapado-events');
?></span>
                        </label>
                        <label class="mapado-input-radio">
                            <input type="radio" name="mapado_card_thumb_size" value="s"
                                <?php 
if ($vars[$listChoose]['card_thumb_size']['value'] == 's') {
    echo 'checked="checked"';
}
?>
                                >
                            <span><?php 
echo __('Small', 'mapado-events');
?></span>
                        </label>
                </td>
            </tr>

            <!-- List sort setting -->
            <tr>
                <td>
                    <span class="mapado_table__label"><?php 
echo __('Sorting lists', 'mapado-events');
?></span>
                </td>
                <td class="mapado_option_list_table_td_right">
                    <label class="mapado-input-radio">
                        <input type="radio" name="mapado_list_sort" value="api_dateonly-noimg"
                            <?php 
if (!empty($vars[$listChoose]['list_sort']['value']) || 'api_dateonly' != $vars[$listChoose]['list_sort']['value'] && 'api_topevents-date-noimg' != $vars[$listChoose]['list_sort']['value'] && 'api_topevents-date' != $vars[$listChoose]['list_sort']['value']) {
    echo 'checked="checked"';
}
?>
                            >
                        <span>Date</span>
                    </label>
                    <label class="mapado-input-radio">
                        <input type="radio" name="mapado_list_sort" value="api_dateonly"
                            <?php 
if (!empty($vars[$listChoose]['list_sort']['value']) && 'api_dateonly' == $vars[$listChoose]['list_sort']['value']) {
    echo 'checked="checked"';
}
?>
                            >
                        <span><?php 
echo __('Date + Visuals', 'mapado-events');
?></span>
                    </label>
                    <label class="mapado-input-radio">
                        <input type="radio" name="mapado_list_sort" value="api_topevents-date-noimg"
                            <?php 
if (!empty($vars[$listChoose]['list_sort']['value']) && 'api_topevents-date-noimg' == $vars[$listChoose]['list_sort']['value']) {
    echo 'checked="checked"';
}
?>
                            >
                        <span>Date + Top</span>
                    </label>
                    <label class="mapado-input-radio">
                        <input type="radio" name="mapado_list_sort" value="api_topevents-date"
                            <?php 
if (!empty($vars[$listChoose]['list_sort']['value']) && 'api_topevents-date' == $vars[$listChoose]['list_sort']['value']) {
    echo 'checked="checked"';
}
?>
                            >
                        <span><?php 
echo __('Date + Tops + Visuals', 'mapado-events');
?></span>
                    </label>
                </td>
            </tr>

            <!-- List Depth setting -->
            <tr>
                <td>
                    <span class="mapado_table__label"><?php 
echo __('List Depth', 'mapado-events');
?></span>
                </td>
                <td class="mapado_option_list_table_td_right">
                    <label class="mapado-input-radio">
                        <input type="radio" name="mapado_list_depth" value="1"
                            <?php 
if (empty($vars[$listChoose]['list_depth']['value']) || 1 == $vars[$listChoose]['list_depth']['value']) {
    echo 'checked="checked"';
}
?>
                            >
                        <span><?php 
echo __('Show lists (level 1)', 'mapado-events');
?></span>
                    </label>
                    <label class="mapado-input-radio">
                        <input type="radio" name="mapado_list_depth" value="2"
                            <?php 
if (!empty($vars[$listChoose]['list_depth']['value']) && 2 == $vars[$listChoose]['list_depth']['value']) {
    echo 'checked="checked"';
}
?>
                            >
                        <span><?php 
echo __('Show the events of lists (level 2)', 'mapado-events');
?></span>
                    </label>
                </td>
            </tr>

            <!-- Display past events setting -->
            <tr>
                <td>
                    <label class="mapado_table__label" for="mapado_past_events"><?php 
echo __('Display past events ?', 'mapado-events');
?></label>
                </td>
                <td class="mapado_option_list_table_td_right">
                    <input name="mapado_past_events" id="mapado_past_events" type="checkbox"
                        value="1" <?php 
if (!empty($vars[$listChoose]['past_events']['value'])) {
    echo 'checked="checked"';
}
?>>
                </td>
            </tr>

            <!-- Display search -->
            <tr>
                <td>
                    <label class="mapado_table__label" for="mapado_display_search"><?php 
echo __('Show search bar?', 'mapado-events');
?></label>
                </td>
                <td class="mapado_option_list_table_td_right">
                    <input name="mapado_display_search" id="mapado_display_search" type="checkbox"
                        value="1" <?php 
if (!empty($vars[$listChoose]['display_search']['value'])) {
    echo 'checked="checked"';
}
?>>
                </td>
            </tr>
            <!-- Advanced settings -->
            <tr>
                <td colspan="2"><a href="javascript:;" class="mpd-table-dropdown__trigger" id="mapado_options_advanced_settings"><?php 
echo __('Advanced settings', 'mapado-events');
?></a></td>
            </tr>
            <tr class="mpd-table-dropdown__body" class="mapado_option_list_table_td_right">
                <td colspan="2">
                    <table id="mapado_option_list_advanced_table" class="mapado_table widefat striped">

                        <!-- Template -->
                        <tr>
                            <td colspan="2">
                                <button type="button" class="button button-delete right js-mapado_template_reset">
                                    <?php 
echo __('Reset', 'mapado-events');
?>
                                </button>
                                <label class="mapado_table__label" for="mapado_card_template"><?php 
echo __('Template', 'mapado-events');
?></label>
                                <!-- spellcheck false = no ortographic verification -->
                                <textarea name="mapado_card_template" id="mapado_card_template" spellcheck="false" class="js-mapado_template_input"><?php 
echo $vars[$listChoose]['card_template']['value'];
?>
                                </textarea>
                                <div style="display: none;" class="js-mapado_template_default"><?php 
echo $vars["settings"]->getCardTemplateDefault();
?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <!-- Style of events sheets (detail activtiy) -->
                <td colspan="2" id="mapado_option_list_table_td_in_large"><h2><?php 
echo __('Style of the event sheets', 'mapado-events');
?></h2></td>
            </tr>

            <!-- Image size (detail activity) -->
            <tr>
                <td>
                    <span class="mapado_table__label"><?php 
echo __('Image sizes (event sheets)', 'mapado-events');
?></span>
                </td>
                <td class="mapado_option_list_table_td_right">
                        <label class="mapado-input-radio">
                            <input type="radio" name="mapado_full_thumb_size" value="l"
                                <?php 
if ($vars[$listChoose]['full_thumb_size']['value'] == 'l') {
    echo 'checked="checked"';
}
?>
                                >
                            <span><?php 
echo __('Large', 'mapado-events');
?></span>
                        </label>
                        <label class="mapado-input-radio">
                            <input type="radio" name="mapado_full_thumb_size" value="m"
                                <?php 
if ($vars[$listChoose]['full_thumb_size']['value'] == 'm') {
    echo 'checked="checked"';
}
?>
                                >
                            <span><?php 
echo __('Average', 'mapado-events');
?></span>
                        </label>
                        <label class="mapado-input-radio">
                            <input type="radio" name="mapado_full_thumb_size" value="s"
                                <?php 
if ($vars[$listChoose]['full_thumb_size']['value'] == 's') {
    echo 'checked="checked"';
}
?>
                                >
                            <span><?php 
echo __('Small', 'mapado-events');
?></span>
                        </label>

                </td>
            </tr>

            <!-- Information detail in widget -->
            <tr>
                <td>
                    <label class="mapado_table__label" for="mapado_widget"><?php 
echo __('Display the main information in a widget?', 'mapado-events');
?></label>
                </td>
                <td class="mapado_option_list_table_td_right">
                    <input name="mapado_widget" id="mapado_widget" type="checkbox"
                        value="1"
                        <?php 
if (!empty($vars[$listChoose]['widget']['value'])) {
    echo 'checked="checked"';
}
?>>
                </td>
            </tr>

            <!-- Advanced settings -->
            <tr>
                <td colspan="2"><a href="javascript:;" class="mpd-table-dropdown__trigger"><?php 
echo __('Advanced settings', 'mapado-events');
?></a></td>
            </tr>
            <tr class="mpd-table-dropdown__body">
                <td colspan="2">
                    <table id="mapado_option_single_advanced_table" class="mapado_table widefat striped">

                        <!-- Template -->
                        <tr>
                            <td colspan="2">
                                <button type="button" class="button button-delete right js-mapado_template_reset">
                                    <?php 
echo __('Reset', 'mapado-events');
?>
                                </button>
                                <label class="mapado_table__label" for="mapado_full_template"><?php 
echo __('Template (event sheets)', 'mapado-events');
?></label>
                                <!-- spellcheck false = no ortographic verification -->
                                <textarea name="mapado_full_template" id="mapado_full_template" spellcheck="false" class="js-mapado_template_input"><?php 
echo $vars[$listChoose]['full_template']['value'];
?>
                                </textarea>
                                <div style="display: none;" class="js-mapado_template_default"><?php 
echo $vars["settings"]->getFullTemplateDefault();
?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
