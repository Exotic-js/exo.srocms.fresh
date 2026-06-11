<?php

return [
    'enabled' => true,
    'tax' => 5, // Commission tax percentage (e.g. 5%)
    'currencies' => ['gold', 'silk', 'points'], // Supported currencies
    'silk_type' => 3, // APH_ChangedSilk SilkType used by this CMS for premium silk
    'claim_delivery' => 'chest_box', // chest_box or filter_add_item
    'claim_storage_type' => 2, // 1 = character inventory, 2 = storage/warehouse
    'min_level' => 1, // Minimum character level to list items
    'max_active_listings' => 5, // Maximum active listings per character
    'listing_duration_days' => 7, // Default listing duration in days
];
