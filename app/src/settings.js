/* global wp, bgcollector */
import { render, createElement } from '@wordpress/element';

const {
	apiFetch
} = wp;

const updateCollection = (e) => {
	if (e) {
		e.preventDefault();
	}

	triggerUpdate();
};

const Button = () => (
	<button onClick={updateCollection}>Update Collection</button>
);

const triggerUpdate = () => {
	apiFetch.use( apiFetch.createNonceMiddleware( bgcollector.nonce ) );

	apiFetch(
		{
			path: 'bgc/v1/collection',
			method: 'POST',
			data: { username: document.getElementById( 'bgg-username' ).value }
		}
	).then( result => console.log( result ) );
	// .then( result => console.log( result ) )
	// .error( error => console.log( error ) );

	// axios.post( bgcollector.apiRoot + '/collection', {
	// 		nonce: bgcollector.nonce,
	// 		username: document.getElementById('bgg-username').value,
	// 	},
	// 	{
	// 		headers: {
	// 			'X-WP-Nonce': bgcollector.nonce
	// 		}
	// 	})
	// 	.then( ({data, status}) => {
	// 		if (202 === data.status) {
	// 			setTimeout(function() {
	// 				updateCollection();
	// 			},5000);
	// 			return;
	// 		}
	//
	// 		if (200 !== status) {
	// 			console.log('Something went wrong');
	// 		}
	//
	// 		if (0 !== data.length) {
	// 			console.log(data.length);
	// 			triggerUpdate();
	// 		}
	// 	} )
	// 	.catch( error => {
	// 		console.log(error);
	// 	} );
};


// what

const settings = () => {
	const username = document.getElementById('bgg-username');

	if (!username.value) {
		return;
	}

	render(
		createElement( Button ),
		document.getElementById( 'bgc-app' ) );
};

settings();

export default settings;
