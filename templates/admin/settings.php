<?php

/**
 * Admin
 * Settings page TPL
 */
?>
<div id="mapado-settings">
    <!-- Notifications -->
    <?php 
foreach ($vars['notification_list'] as $notification) {
    ?>
        <div id="mapado-settings-updated" class="mapado-notifications <?php 
    echo $notification['type'];
    ?> settings-error">
            <p><strong><?php 
    echo $notification['message'];
    ?></strong></p>
        </div>
    <?php 
}
?>
    <form class="mapado-settings-page" method="post" name="mapado_settings_form"
          action="?page=mapado_settings_step_<?php 
echo $vars['step'];
?>&noheader<?php 
echo $vars['paramListChoose'] ? '&list_choose=' . $vars['paramListChoose'] : '&list_choose=nolistchoose';
?>"
        >
        <div class="mapado-settings-page-content">
            <?php 
if ('api' == $vars['step']) {
    echo __('<div class="mapado-setting-welcome">
                    <h1>Welcome to Mapado Events Plugin</h1>
                    <p>You\'ll be able to import a list of events easily in a few clicks.</p>
                </div>', 'mapado-events');
} elseif ('imports' == $vars['step']) {
    echo __('<div class="mapado-setting-welcome">
                <h1>Import your lists</h1>
                <p>Choose the lists which you wish to import with the desired url.</p>
                </div>', 'mapado-events');
} else {
    echo __('<div class="mapado-setting-welcome">
                <h1>Display parameter</h1>
                <p>Choose a list and change its display settings.</p>
                </div>', 'mapado-events');
}
?>
            <div class="mapado-setting-nav">
                <table id="mapado-settings-table">
                <thead>
                    <tr>
                        <th>
                            <a href="?page=mapado_settings_step_api" class="mapado-setting-nav-item
                                <?php 
echo 'api' == $vars['step'] ? 'mapado-setting-nav-item--active' : '';
?>"
                                title="<?php 
echo __('Your API settings', 'mapado-events');
?>"
                            >
                                <?php 
echo __('API settings', 'mapado-events');
?>
                            </a>
                        </th>
                        <th>
                            <a href="?page=mapado_settings_step_imports" class="mapado-setting-nav-item
                                <?php 
echo 'imports' == $vars['step'] ? 'mapado-setting-nav-item--active' : '';
?>
                                <?php 
echo !$vars['has_api'] ? 'mapado-setting-nav-item--disable' : '';
?>"
                                title="<?php 
echo __('Your lists', 'mapado-events');
?>"
                            >
                                <?php 
echo __('Import your lists', 'mapado-events');
?>
                            </a>
                        </th>
                        <th>
                            <a href="<?php 
echo !$vars['has_imported_lists'] ? '' : '?page=mapado_settings_step_options';
?>" class="mapado-setting-nav-item mapado-setting-nav-item--options
                                <?php 
echo 'options' == $vars['step'] ? 'mapado-setting-nav-item--active' : '';
?>
                                <?php 
echo !$vars['has_imported_lists'] ? 'mapado-setting-nav-item--disable' : '';
?>"
                                title="<?php 
echo __('Display parameter', 'mapado-events');
?>"
                            >
                                <?php 
echo __('Display parameter', 'mapado-events');
?>
                            </a>
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
        <hr/>
        <?php 
if ('api' == $vars['step']) {
    include dirname(__FILE__) . '/settings_api.php';
} elseif ('imports' == $vars['step']) {
    include dirname(__FILE__) . '/settings_imports.php';
} else {
    include dirname(__FILE__) . '/settings_options_list.php';
}
?>
    </div>
        <div class="mapado-settings-page-footer">
            <?php 
if ('api' != $vars['step']) {
    ?>
                <a href = "?page=mapado_settings_step_<?php 
    echo 'imports' == $vars['step'] ? 'api' : 'imports';
    ?>" class="button-link">
                    <?php 
    echo __('Previous step', 'mapado-events');
    ?>
                </a>
            <?php 
}
?>
            &nbsp;
            <button name="mapado_settings_submit" value="submit" class="button button-primary">
                <?php 
if ('options' == $vars['step']) {
    echo __('Save changes', 'mapado-events');
} else {
    echo __('Continue', 'mapado-events');
}
?>
            </button>
        </div>
    </form>
</div>