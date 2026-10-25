/**
 * Ridvan Core Skin Logic
 * Handles Menus, Toggles, and Layout interactions.
 */
( function ( $ ) {
    $( function () {
        /* ==================================================================
           MENU LOGIC (Close on click-away, Exclusive opening)
           ================================================================== */
        var $menuContainers = $( '.mw-portlet, .ridvan-dropup' );
        var $menuCheckboxes = $menuContainers.find( 'input[type="checkbox"]' );

        // 1. If a click hits the window (background), close ALL menus.
        $( window ).on( 'click', function () {
            $menuCheckboxes.prop( 'checked', false );
        } );

        // 2. If the user clicked INSIDE a menu, stop the bubble 
        // (so Rule #1 doesn't run).
        $menuContainers.on( 'click', function ( e ) {
            e.stopPropagation();
        } );

        // 3. If a menu is opened, close all OTHER menus.
        $menuCheckboxes.on( 'change', function () {
            if ( $( this ).is( ':checked' ) ) {
                $menuCheckboxes.not( this ).prop( 'checked', false );
            }
        } );

    } );
}( jQuery ) );
