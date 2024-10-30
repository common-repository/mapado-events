<?php

use Mapado\ImageUrlBuilder\Builder;
use Mapado\Sdk\Entity\Activity;
$template = $vars['template'];
$activity = $vars['activity'];
$template->reset();
$template['widgetActive'] = $vars['widgetActive'];
// ApiSlug & widgetApiSlug
$current_url = get_permalink(get_queried_object_id());
$template['apiSlug'] = $current_url . $activity->getApiSlug();
$template['widgetApiSlug'] = '/' . $vars['list_slug'] . '/' . $activity->getApiSlug();
// Title & shortDescription & description & shortDate
$template['title'] = $activity->getTitle();
$template['shortDescription'] = $activity->getShortDescription();
$template['description'] = apply_filters('the_content', $activity->getDescription(), true);
$template['shortDate'] = $activity->getShortDate();
// Address
$template['address'] = $activity->getAddress()['formattedAddress'];
$template['city'] = $activity->getAddress()['city'];
$template['frontPlaceName'] = $activity->getFrontPlaceName();
// PriceList
$priceList = $activity->getPriceList();
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
}
$pleinTarif = $pleinTarif ? 'Plein Tarif : ' . $pleinTarif . '<br>' : null;
$tarifReduit = $tarifReduit ? 'Tarif réduit : ' . $tarifReduit : null;
$template['price'] = $pleinTarif . $tarifReduit;
// Url link
$template['place_link'] = null;
if ($activity instanceof Activity) {
    $place = $activity->getPlace();
    if ($place instanceof Activity) {
        // only for detail of activity
        $template['place_link'] = $place->getUrlList()['mapado'];
    }
}
$url_list = $activity->getUrlList();
$template['ticket_link'] = $url_list['ticket'];
$template['facebook_link'] = $url_list['facebook'];
$template['official_link'] = $url_list['official'];
// Informations
$template['email'] = $activity->getEmailList()[0];
$phone = $activity->getPhoneList()[0] ?: null;
$template['phone'] = $phone ? '<a href="tel:' . $phone . '">' . $phone . '</a>' : null;
// if there is at least one information display the Infos/contacts div
$template['informations'] = $url_list['official_link'] || $url_list['facebook_link'] || $template['email'] || $template['phone'] ?: false;
// Image
$builder = new Builder();
$width = (int) $vars['card_thumb_design']['dimensions'][0] ?: 700;
// if no width dimension => default 700
$height = (int) $vars['card_thumb_design']['dimensions'][1] ?: 280;
// if no height dimension => default 280
if ($vars['imageDetailWidth'] && $vars['imageDetailHeight']) {
    // if detail of an activity
    $width = (int) $vars['imageDetailWidth'] ?: 500;
    // if no width dimension => default 500
    $height = (int) $vars['imageDetailHeight'] ?: 200;
    // if no height dimension => default 200
}
$activityImage = $activity->getImage();
$imageUrl = $activityImage ? $builder->buildUrl($activityImage, (int) $width, (int) $height) : null;
$imageUrl = "<img src='" . $imageUrl . "'>";
$template['thumb'] = $activityImage ? $imageUrl : null;
$template['thumb_position_type'] = @$vars['card_thumb_design']['position_type'];
$template['thumb_position_side'] = @$vars['card_thumb_design']['position_side'];
$template['thumb_orientation'] = @$vars['card_thumb_design']['orientation'];
$template['thumb_size'] = @$vars['card_thumb_design']['size']['value'];
$template['latitude'] = $activity->getAddress()['latitude'];
$template['longitude'] = $activity->getAddress()['longitude'];
echo $template->output();