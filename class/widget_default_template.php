<?php

$widget_card_template_default = '[%thumb]
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