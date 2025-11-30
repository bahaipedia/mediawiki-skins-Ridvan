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

	return {
		active: { id: 'mwRestApi' }, // Mocking the active object for compatibility
		
		fetchByTitle: ( q, limit = config.wgCitizenMaxSearchResults, showDescription = true ) => {
			// Ensure we use the correct script path from MW config if the JSON is static
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
}

module.exports = searchClient;
