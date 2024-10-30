<?php

/**
 * Mapado Plugin
 * Admin AJAX user list TPL
 */
?>

<?php 
foreach ($vars['user_lists'] as $k => $list) {
    $exist = false;
    if (!empty($vars['importedLists'])) {
        $exist = array_key_exists($list['slug'], $vars['importedLists']);
    }
    ?>
    <tr id="list-<?php 
    echo $list['slug'];
    ?>" class="list">
        <td colspan="1" class="mapado_user_list_td_left">
            <?php 
    if ($exist) {
        ?>
                <a href="<?php 
        echo MapadoUtils::getUserListUrl($vars['importedLists'][$list['slug']]);
        ?>" title=""
                   target="_blank"><?php 
        echo $list['title'];
        ?></a>
            <?php 
    } else {
        echo $list['title'];
    }
    ?>
        </td>
        <td colspan="2" id="mapado_option_list_table_td_right">
            <form>
                <?php 
    if ($exist) {
        ?>
                    <span class="mpd-aligned-input">
                        <strong><?php 
        echo __('Current slug :', 'mapado-events');
        ?></strong>
                        <?php 
        bloginfo('url');
        ?>/<?php 
        echo $vars['importedLists'][$list['slug']];
        ?>
                    </span>
                    <input type="hidden" name="list-slug" value="<?php 
        echo $vars['importedLists'][$list['slug']];
        ?>"/>
                    <input type="hidden" name="action" value="delete" />
                    <button type="submit" class="button button-delete right"><?php 
        echo __('Delete the list', 'mapado-events');
        ?></button>
                <?php 
    } else {
        ?>
                    <label for="list-slug-<?php 
        echo $list['slug'];
        ?>">
                        <?php 
        echo __('Choose the slug :', 'mapado-events');
        ?>
                        <span style="font-weight: normal;"><?php 
        bloginfo('url');
        ?>/</span>
                    </label>
                    <input type="text" id="list-slug-<?php 
        echo $list['slug'];
        ?>"
                           name="list-slug" placeholder="<?php 
        echo __('my-list-slug', 'mapado-events');
        ?>" class="list-slug">
                    <input type="hidden" name="action" value="import" />
                    <button type="submit" class="button button-primary right" id="mapado-button-import"><?php 
        echo __('Import the list', 'mapado-events');
        ?></button>
                <?php 
    }
    ?>

                <input type="hidden" name="list-title" value="<?php 
    echo $list['title'];
    ?>">
                <input type="hidden" name="uuid" value="<?php 
    echo $list['slug'];
    ?>" />
            </form>
        </td>
    </tr>
<?php 
}