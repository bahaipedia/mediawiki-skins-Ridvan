const fetchJson = require( './fetch.js' );
const urlGenerator = require( './urlGenerator.js' );

/**
 * Simplified Search Client for MediaWiki REST API
 */
function searchClient( config ) {

	// Helper to format the API response
	function adaptApiResponse( query, response, showDescription ) {
		const urlGen = urlGenerator( config );
		return {
			query,
			results: response.pages.map( ( page ) => {
				const thumbnail = page.thumbnail;
				return {
					id: page.id,
					label: page.matched_title || page.title,
					key: page.key,
					title: page.title,
					description: showDescription ? page.description : undefined,
					url: urlGen.generateUrl( page ),
					thumbnail: thumbnail ? {
						url: thumbnail.url,
						width: thumbnail.width ?? undefined,
						height: thumbnail.height ?? undefined
					} : undefined
				};
			} )
		};
	}

	// This internal object mimics the API client
	const internalClient = {
		fetchByTitle: ( q, limit = config.wgCitizenMaxSearchResults, showDescription = true ) => {
			const scriptPath = mw.config.get( 'wgScriptPath' ) || config.wgScriptPath;
			const searchApiUrl = scriptPath + '/rest.php';
			
			const params = { q, limit: limit.toString() };
			const search = new URLSearchParams( params );
			const url = `${ searchApiUrl }/v1/search/title?${ search.toString() }`;
			
			const result = fetchJson( url, { headers: { accept: 'application/json' } } );
			
			const searchResponsePromise = result.fetch
				.then( ( res ) => adaptApiResponse( q, res, showDescription ) );
				
			return {
				abort: result.abort,
				fetch: searchResponsePromise
			};
		}
	};

	// RETURN OBJECT
	// This must match what typeahead.js expects!
	return {
		active: {
			id: 'mwRestApi',
			client: internalClient
		},
		// Dummy function to prevent crash
		setActive: function ( id ) {
			return;
		},
		// Dummy function to prevent crash
		getData: function ( key, value ) {
			return null;
		}
	};
}

module.exports = searchClient;
