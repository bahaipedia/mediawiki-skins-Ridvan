$( function () {

    /* ==================================================================
       1. MENU LOGIC (Close on click-away, Exclusive opening)
       ================================================================== */
    $( window ).on( 'click', function () {
        $( '.mw-portlet input[type="checkbox"]' ).prop( 'checked', false );
    } );

    $( '.mw-portlet' ).on( 'click', function ( e ) {
        e.stopPropagation();
    } );

    $( '.mw-portlet input[type="checkbox"]' ).on( 'change', function () {
        if ( $( this ).is( ':checked' ) ) {
            $( '.mw-portlet input[type="checkbox"]' ).not( this ).prop( 'checked', false );
        }
    } );

    /* ==================================================================
       2. MODERN SEARCH (Vector-Style with Thumbnails)
       ================================================================== */
    var $searchInput = $( '#searchInput' );
    var $searchForm = $( '#p-search form' );
    
    // Create the results container dynamically
    var $resultsBox = $( '<div>' ).addClass( 'ridvan-search-results' ).hide();
    $searchForm.append( $resultsBox );

    var searchTimeout;

    // A. THE INPUT HANDLER
    $searchInput.on( 'input focus', function () {
        var query = $( this ).val();
        clearTimeout( searchTimeout );

        if ( query.length === 0 ) {
            $resultsBox.hide();
            return;
        }

        // Debounce: Wait 300ms after typing stops before hitting the server
        searchTimeout = setTimeout( function() {
            fetchResults( query );
        }, 300 );
    } );

    // B. THE API FETCHER (Using the Modern REST API)
    function fetchResults( query ) {
        var apiLink = mw.util.wikiScript('rest') + '/v1/search/title?q=' + encodeURIComponent(query) + '&limit=10';

        $.ajax( {
            url: apiLink,
            dataType: 'json',
            success: function ( data ) {
                renderResults( data.pages );
            }
        } );
    }

    // C. THE RENDERER
    function renderResults( pages ) {
        $resultsBox.empty();

        if ( !pages || pages.length === 0 ) {
            $resultsBox.hide();
            return;
        }

        // Loop through results
        pages.forEach( function ( page ) {
            var $row = $( '<a>' )
                .attr( 'href', mw.util.getUrl( page.key ) ) // Page Link
                .addClass( 'ridvan-result-row' );

            // 1. THUMBNAIL (Check if exists)
            if ( page.thumbnail ) {
                var $thumb = $( '<img>' )
                    .attr( 'src', page.thumbnail.url )
                    .addClass( 'ridvan-result-thumb' );
                $row.append( $thumb );
            } else {
                // Placeholder icon for pages without images
                var $icon = $( '<span>' ).addClass('ridvan-result-icon');
                $row.append( $icon );
            }

            // 2. TEXT CONTAINER
            var $textDiv = $( '<div>' ).addClass( 'ridvan-result-text' );
            
            // Title
            var $title = $( '<div>' ).addClass( 'ridvan-result-title' ).text( page.title );
            $textDiv.append( $title );

            // Description (if exists)
            if ( page.description ) {
                var $desc = $( '<div>' ).addClass( 'ridvan-result-desc' ).text( page.description );
                $textDiv.append( $desc );
            }

            $row.append( $textDiv );
            $resultsBox.append( $row );
        } );

        // Add "Search containing..." link at bottom
        var query = $searchInput.val();
        var $footer = $( '<a>' )
            .addClass( 'ridvan-result-footer' )
            .attr( 'href', mw.util.getUrl( 'Special:Search' ) + '?search=' + encodeURIComponent(query) )
            .text( 'Search for pages containing "' + query + '"' );
        
        $resultsBox.append( $footer );
        $resultsBox.show();
    }

    // D. CLOSE SEARCH ON CLICK AWAY
    $( window ).on( 'click', function ( e ) {
        if ( !$( e.target ).closest( '#p-search' ).length ) {
            $resultsBox.hide();
        }
    } );
} );
