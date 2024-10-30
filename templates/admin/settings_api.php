<table class="mapado_table widefat" id="mapado_table_api_settings">
    <thead>
        <tr>
            <th colspan="2" class="mapado_api_settings_th_txt"><strong><?php 
echo __('Enter your API settings', 'mapado-events');
?></strong></th>
            <th id="mapado_api_settings_th_right_txt">
                <a href="//mag.mapado.com/api-keys-mapado/" title="<?php 
echo __('Find my API key', 'mapado-events');
?>" target="_blank">
                    <?php 
echo __('Where to find this informations ?', 'mapado-events');
?>
                </a>
            </th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>
                <label class="mapado_table__label" for="mapado_api_id">ID</label>
                <input name="mapado_api_id" id="mapado_api_id" class="mapado_api_settings" type="text" step="1" min="1"
                    value="<?php 
if (!empty($vars['api']['id'])) {
    echo $vars['api']['id'];
}
?>">
            </td>

            <td>
                <label class="mapado_table__label" for="mapado_api_secret"><?php 
echo __('Secret code', 'mapado-events');
?></label>
                <input name="mapado_api_secret" id="mapado_api_secret" class="mapado_api_settings" type="text" step="1" min="1"
                    value="<?php 
if (!empty($vars['api']['secret'])) {
    echo $vars['api']['secret'];
}
?>">
            </td>

            <td>
                <label class="mapado_table__label" for="mapado_api_auth"><?php 
echo __('Authentication token', 'mapado-events');
?></label>
                <input name="mapado_api_auth" id="mapado_api_auth" class="mapado_api_settings" type="text" step="1" min="1"
                    value="<?php 
if ($vars['auth']) {
    echo $vars['auth'];
}
?>">
            </td>
        </tr>
    </tbody>
</table>
