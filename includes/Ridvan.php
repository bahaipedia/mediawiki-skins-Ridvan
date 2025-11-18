<?php

class SkinRidvan extends SkinMustache {

    public function getTemplateData() {
        // 1. Get the standard data provided by MediaWiki
        $data = parent::getTemplateData();

        // 2. Initialize our custom buckets
        $visibleButtons = [];
        $dropdownButtons = [];

        // 3. Define which IDs you want visible (The Allow List)
        // 'ca-edit' is standard edit, 'ca-talk' is discussion.
        // 'ca-view' is the "Page/Read" tab 
        $forceVisible = [ 'ca-edit', 'ca-talk' ];

        // 4. Merge the separate arrays (Views + Actions) to process them together
        $allPortlets = array_merge(
            $data['data-portlets']['data-views']['array-items'] ?? [],
            $data['data-portlets']['data-actions']['array-items'] ?? []
        );

        // 5. Sort them
        foreach ( $allPortlets as $key => $item ) {
            if ( in_array( $key, $forceVisible ) ) {
                // Add to visible bar
                $visibleButtons[] = $item;
            } else {
                // Add to dropdown
                $dropdownButtons[] = $item;
            }
        }

        // 6. Pass these new arrays back to Mustache
        $data['custom-bar-visible'] = $visibleButtons;
        $data['custom-bar-dropdown'] = $dropdownButtons;

        return $data;
    }
}
