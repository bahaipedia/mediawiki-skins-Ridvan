// We don't need to import config or urlGenerator anymore since we aren't generating actions
function searchAction() {
	return {
		// EMPTY RENDER FUNCTION
		render: function ( searchQuery, templates ) {
			// Ensure the action group stays hidden
			const actionGroup = document.getElementById( 'citizen-typeahead-group-action' );
			if ( actionGroup ) {
				actionGroup.hidden = true;
			}
		},
		clear: function () {
			const actionList = document.getElementById( 'citizen-typeahead-list-action' );
			if ( actionList ) actionList.innerHTML = '';

			const actionGroup = document.getElementById( 'citizen-typeahead-group-action' );
			if ( actionGroup ) actionGroup.hidden = true;
		},
		init: function () {
			// No initialization needed
		}
	};
}

module.exports = searchAction;
