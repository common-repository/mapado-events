<?php

/**
 * Mapado Plugin
 * Widget TPL
 */
use Mapado\Sdk\Entity\Activity;
?>

<aside id="mapado-widget" class="widget mapado-widget">
<?php 
if (!empty($vars['event'])) {
    ?>
    <h2 class="widget-title">
        <?php 
    if ($title = $vars['event']->getTitle()) {
        ?>
            Informations pratiques pour <?php 
        echo $title;
        ?>
            <hr/>
        <?php 
    }
    ?>
    </h2>
    <div>
    <?php 
    if ($date = $vars['event']->getShortDate()) {
        ?>
        <p style="margin: 0 0 0.3em;"><?php 
        echo $date;
        ?></p>
    <?php 
    }
    ?>
        <?php 
    if ($vars['event'] instanceof Activity && $vars['event']->getPlace() instanceof Activity) {
        $place = $vars['event']->getPlace();
        $placeUrl = $place->getUrlList()['mapado'];
        if ($placeUrl) {
            echo '<a style="text-decoration: underline;" href="' . $place->getUrlList()['mapado'] . '" target="_blank">' . $vars['event']->getFrontPlaceName() . '</a> - ';
            echo $vars['event']->getAddress()['formattedAddress'];
        }
    } else {
        echo $vars['event']->getFrontPlaceName() . ' - ';
        if ($vars['event']->getAddress()) {
            echo $vars['event']->getAddress()['formattedAddress'];
        }
    }
    ?>
    </div>

    <!-- urlList -->
    <?php 
    if ($link = $vars['event']->getUrlList()) {
        ?>   
        <hr/>
        <div>
            <?php 
        if ($officialLink = $vars['event']->getUrlList()['official']) {
            ?>
                <div>
                    <?php 
            echo '<a href="' . $officialLink . '" target="_blank">Site officiel</a>';
            ?>
                </div>
            <?php 
        }
        ?>
            <?php 
        if ($link['facebook']) {
            ?>
                <div>
                    <?php 
            echo '<a href="' . $link['facebook'] . '" target="_blank">Facebook de l\'événement</a>';
            ?>
                </div>
            <?php 
        }
        ?>
        </div>
    <?php 
    }
    ?>
    <?php 
    if ($contact = $vars['event']->getEmailList()) {
        ?>
        <div>
            <?php 
        echo 'Email : <a href="mailto:' . $contact[0] . '">' . $contact[0] . '</a>';
        ?>
        </div>
    <?php 
    }
    ?>
    <?php 
    if ($contact = $vars['event']->getPhoneList()) {
        ?>
        <div>
            <?php 
        echo 'Téléphone : <a href="telto:' . $contact[0] . '">' . $contact[0] . '</a>';
        ?>
        </div>
    <?php 
    }
    ?>

    <!-- PriceList -->
    <?php 
    $priceList = $vars['event']->getPriceList();
    $pleinTarif = '';
    $tarifReduit = '';
    if ($priceList) {
        $compteurPlein = 0;
        $compteurReduit = 0;
        foreach ($priceList as $price) {
            $price_name = $price['name'] ?: null;
            $price_description = $price['description'] ? ' <i>(' . $price['description'] . ' ' . $price_name . ')</i>' : null;
            $price_currency = $price['currency_code'] ?: '€';
            $price_value = 0 === $price['value'] ? 'Gratuit' : $price['value'] && $price_currency ? $price['value'] . $price_currency : false;
            if ('default' == $price['type']) {
                ++$compteurPlein;
                $separator = $compteurPlein > 1 ? ', ' : null;
                $pleinTarif .= $separator . $price_value . $price_description;
            } else {
                ++$compteurReduit;
                $separator = $compteurReduit > 1 ? ', ' : null;
                $tarifReduit .= $separator . $price_value . $price_description;
            }
        }
        $pleinTarif = $pleinTarif ? 'Plein Tarif : ' . $pleinTarif . '<br>' : null;
        $tarifReduit = $tarifReduit ? 'Tarif réduit : ' . $tarifReduit : null;
        ?>
        <div>
            <?php 
        echo $pleinTarif . $tarifReduit;
        ?>
        </div>
        <?php 
        if ($ticket_link = $vars['event']->getUrlList()['ticket']) {
            echo '<a style="
            font: bold 11px Arial;text-decoration: none;background-color: #eeeeee;color: #333333;
            padding: 2px 6px 2px 6px;border-top: 1px solid #cccccc;border-right: 1px solid #333333;
            border-bottom: 1px solid #333333;border-left: 1px solid #cccccc;"
            href=' . $ticket_link . ' target="_blank">Réserver</a>';
        }
    }
    ?>
    
<?php 
}
?>
<hr/>
</aside>