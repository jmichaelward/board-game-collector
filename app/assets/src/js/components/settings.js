import axios from 'axios';
import { render, createElement } from '@wordpress/element';

const Button = () => (
	<button onClick={updateCollection}>Update Collection</button>
);

const triggerUpdate = () => {
	axios.post( bgcollector.apiRoot + '/collection', {
			nonce: bgcollector.nonce,
			username: document.getElementById('bgc-username').value,
		},
		{
			headers: {
				'X-WP-Nonce': bgcollector.nonce
			}
		})
		.then( ({data, status}) => {
			if (202 === data.status) {
				setTimeout(function() {
					updateCollection();
				},5000);
				return;
			}

			if (200 !== status) {
				console.log('Something went wrong');
			}

			if (0 !== data.length) {
				console.log(data.length);
				triggerUpdate();
			}
		} )
		.catch( error => {
			console.log(error);
		} );
};

const updateCollection = (e) => {
	if (e) {
		e.preventDefault();
	}

	triggerUpdate();
};

// what

const settings = () => {
	const username = document.getElementById('bgc-username');

	if (!username.value) {
		return;
	}

	render(
		createElement( Button ),
		document.getElementById( 'bgc-app' ) );
};
export default settings;
