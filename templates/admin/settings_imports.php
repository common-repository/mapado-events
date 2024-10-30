<!-- AJAX user lists -->
<table id="mapado_user_lists_table" class="mapado_table widefat">
    <thead>
    <tr>
        <th colspan="2">
            <strong><?php 
echo __('Your lists', 'mapado-events');
?></strong>
        </th>
        <th style="text-align: right">
            <?php 
if (!empty($vars['api'])) {
    ?>
                <div id="mapado_import_header">
                    <a href="#" id="mapado_user_lists_refresh"><img src="<?php 
    echo plugins_url('mapado-events/assets/images/refresh.png');
    ?>" height="20px" width="20px"><strong><?php 
    echo __('Refresh', 'mapado-events');
    ?></strong></a>
                </div>
            <?php 
}
?>
        </th>
    </tr>
    </thead>

    <tbody></tbody>
</table>

<?php 
/* For the AJAX call; Inform that API settings are filled */
?>
<script>
    var ajaxUserLists;
    <?php 
if (!empty($vars['api']) && !empty($vars['api']['id']) && !empty($vars['api']['secret']) && !empty($vars['auth'])) {
    ?>
    ajaxUserLists = {
        'load': true,
        'msg': 'Loading...'
    };
    <?php 
} else {
    ?>
    ajaxUserLists = {
        'load': false,
        'msg': 'You have to set your API settings'
    };
    <?php 
}
?>
</script>
