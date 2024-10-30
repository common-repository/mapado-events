<?php

/**
 * Class MapadoSetting
 */
class MapadoSetting extends ArrayObject
{
    private $index;
    private $perpage_options = [3, 5, 10, 20, 30, 40];
    private $perpage_default = 10;
    private $card_thumb_position_options = ['left' => 'left', 'right' => 'right', 'top' => 'top'];
    private $card_thumb_position_default = 'right';
    private $card_thumb_orientation_options = ['portrait' => 'portrait', 'landscape' => 'landscape', 'square' => 'square'];
    private $card_thumb_orientation_default = 'portrait';
    private $card_thumb_size_options = ['l' => 'large', 'm' => 'average', 's' => 'small'];
    private $card_thumb_size_default = 'l';
    private $full_thumb_size_options = ['l' => 'large', 'm' => 'average', 's' => 'small'];
    private $full_thumb_size_default = 'm';
    private $card_column_max_options = ['1' => 'always 1', '2' => 'up to 2', '3' => 'up to 3', '4' => 'up to 4'];
    private $card_column_max_default = '2';
    private $mapado_list_sort_options = ['api_dateonly-noimg' => 'Date', 'api_dateonly' => 'Date + Visuels', 'api_topevents-date-noim' => 'Date + Top', 'api_topevents-date' => 'Date + Tops + Visuels'];
    private $mapado_list_sort_default = 'api_topevents-date';
    private $widget_default = false;
    private $display_search_default = false;
    private $display_map_default = false;
    private $card_template_default = '[%thumb]
        <div class="mpd-card__thumb
                    mpd-card__thumb--[[thumb_position_type]]
                    mpd-card__thumb--[[thumb_position_side]]
                    mpd-card__thumb--[[thumb_orientation]]
                    mpd-card__thumb--[[thumb_size]]">
            <a href="[[apiSlug]]">
            [[thumb]]
            </a>
        </div>
    [thumb%]


    <div class="mpd-card__body">
        [%title]
            <h3 class="mpd-card__title">
                <a href="[[apiSlug]]">
                    [[title]]
                </a>
            </h3>
        [title%]

        [%shortDate]
            <p class="mpd-card__date">
                [[shortDate]]
            </p>
        [shortDate%]

        [%city]
            <p class="mpd-card__address">
                        [[frontPlaceName]]
                <span class="mpd-card__city">
                    - [[city]]
                </span>
            </p>
        [city%]

        [%shortDescription]
            <p class="mpd-card__description">
                [[shortDescription]]
                <a href="[[apiSlug]]" class="mpd-card__read-more-link">→ Lire la suite</a>
            </p>
        [shortDescription%]
    </div>';
    private $full_template_default = '[%thumb]
        <div class="mapado_activity_thumb">
                [[thumb]]
        </div>
    [thumb%]

    [%!widgetActive]
        <div class="mapado_activity_infos">
            [%shortDate]
                <div>
                    <div class="mapado_activity_label">Dates</div>
                    <div class="mapado_activity_value">[[ shortDate ]]</div>
                </div>
            [shortDate%]

            [%address]
                <div>
                    <div class="mapado_activity_label">Lieu</div>
                    <div class="mapado_activity_value">
                        [%place_link]
                                <a href="[[place_link]]" target="_blank">[[frontPlaceName]]</a>
                        [place_link%]
                        [%!place_link]
                                [[frontPlaceName]]
                        [place_link%]
                        <div class="mpd-card__city">
                                [[address]]
                        </div>
                    </div>
                </div>
            [address%]

            [%informations]
            <div>
                <div class="mapado_activity_label">Infos / Contact</div>
                <div class="mapado_activity_value">
                    [%official_link]
                        <div class="mapado_activity_link_official">
                            <a href="[[ official_link ]]" target="_blank">Site officiel</a>
                        </div>
                    [official_link%]

                    [%email]
                        <div class="mapado_activity_email">
                            <a href="mailto:[[ email ]]" target="_blank">Envoyer un email</a>
                        </div>
                    [email%]

                    [%phone]
                        <div class="mapado_activity_phone">
                            Téléphone : [[ phone ]]
                        </div>
                    [phone%]
                </div>
            </div>
            [informations%]

            [%price]
                <div>
                    <div class="mapado_activity_label">Tarif</div>
                    <div class="mapado_activity_value">
                        <div class="mapado_activity_price">
                            [[price]]
                        </div>
                        [%ticket_link]
                        <br>
                        <div class="mapado_activity_buy">
                            <a href="[[ticket_link]]" target="_blank">Réserver</a>
                        </div>
                        [ticket_link%]
                    </div>
                </div>
            [price%]

        </div>
    [widgetActive%]

    [%description]
        <div class="mapado_activity_desc">
            [[description]]
        </div>
    [description%]';
    private $widget_card_template_default = '[%thumb]
    <div class="mpd-card__thumb
                mpd-card__thumb--[[thumb_position_type]]
                mpd-card__thumb--[[thumb_position_side]]
                mpd-card__thumb--[[thumb_orientation]]
                mpd-card__thumb--[[thumb_size]]">
        <a href="[[widgetApiSlug]]">
        [[thumb]]
        </a>
    </div>
    [thumb%]


    <div class="mpd-card__body">
    [%title]
        <h3 class="mpd-card__title">
            <a href="[[widgetApiSlug]]">
                [[title]]
            </a>
        </h3>
    [title%]

    [%shortDate]
        <p class="mpd-card__date" style="margin: 10px 0 0;">
            [[shortDate]]
        </p>
    [shortDate%]

    [%city]
        <p class="mpd-card__address">
                    [[frontPlaceName]]
            <span class="mpd-card__city">
                - [[city]]
            </span>
        </p>
    [city%]

    [%shortDescription]
        <p class="mpd-card__description">
            [[shortDescription]]
            <a href="[[widgetApiSlug]]" class="mpd-card__read-more-link">→ Lire la suite</a>
        </p>
    [shortDescription%]
    <hr/>
    </div>';
    /**
     * Initialization
     */
    public function __construct($index)
    {
        $this->index = $index;
        $settings = get_option($index);
        $settings = is_array($settings) ? $settings : [];
        parent::__construct($settings);
    }
    /**
     * Wordpress methods
     */
    public function update()
    {
        return update_option($this->index, $this->getArrayCopy());
    }
    public function delete($index)
    {
        unset($this[$index]);
        return $this;
    }
    /**
     * Administration methods
     */
    public function getDefinition($name)
    {
        return ['options' => $this->getOptions($name), 'value' => $this->getValue($name)];
    }
    public function getOptions($name)
    {
        $optionsAttribute = $name . '_options';
        if (isset($this->{$optionsAttribute})) {
            return $this->{$optionsAttribute};
        }
        return [];
    }
    public function getValue($name)
    {
        if (isset($this[$name])) {
            return $this[$name];
        }
        return $this->getDefaultValue($name);
    }
    public function resetValue($name, $value)
    {
        $this[$name] = $value;
    }
    public function getDefaultValue($name)
    {
        $defaultAttribute = $name . '_default';
        if (isset($this->{$defaultAttribute})) {
            return $this->{$defaultAttribute};
        }
        $options = $this->getOptions($name);
        return reset($options);
    }
    public function getCardTemplateDefault()
    {
        return $this->card_template_default;
    }
    public function getFullTemplateDefault()
    {
        return $this->full_template_default;
    }
    public function getWidgetCardTemplateDefault()
    {
        return $this->widget_card_template_default;
    }
}