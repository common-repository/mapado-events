<?php

/**
 * Mapado Plugin
 * Single Event TPL
 */
?>
<div id="mapado-plugin" class="mapado_activity_single">

<?php 
MapadoUtils::template('event_template', $vars);
?>
    <div class="mpd-credits">
		<?php 
echo '<div class="">Retour à l\'accueil de l\'agenda : ';
MapadoUtils::link_back_to_event_list_home();
echo '</div>';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$plugin_datas = get_plugin_data(plugin_dir_path(__DIR__) . 'mapado.php');
$plugin_version = $plugin_datas['Version'];
?>
		<div>
			Agenda avec Mapado (API et plugin Wordpress version <?php 
echo $plugin_version;
?>)
		</div>
	</div>
</div>