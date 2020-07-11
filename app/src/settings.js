/* global wp, bgcollector */
import { Button } from '@wordpress/components';
import { render } from '@wordpress/element';

const {
  apiFetch
} = wp;

const updateGames = () => {
  const loop = () => {
    apiFetch.use( apiFetch.createNonceMiddleware( bgcollector.nonce ) );

    return apiFetch(
      {
        path: 'bgc/v1/collection',
        method: 'POST',
        data: { username: document.getElementById( 'bgg-username' ).value }
      }
    ).then( async ( result ) => {
      const { games, status } = result;
      if ( 202 === status ) {
        setTimeout( async () => {
          await loop();
        }, 5000 );
        return;
      }

      if ( 200 !== status ) {
        // @TODO Create admin notification for status notification.
        console.log( 'Something went wrong' );
        return;
      }

      if ( 0 !== games.length ) {
        await loop();
      }
    } ).catch( error => {
      // @TODO Create admin notification for error message.
      console.log( error );
    } ).finally( () => {
      console.log( 'Done updating games.' );
    } );
  }

  return loop();
};

const updateImages = () => {
  const loop = () => {
    apiFetch.use( apiFetch.createNonceMiddleware( bgcollector.nonce ) );

    return apiFetch(
      {
        path: 'bgc/v1/collection/images',
        method: 'POST'
      }
    ).then( async result => {
      console.info( result );
      if ( 0 !== result.length ) {
        await loop();
      }

      console.info( 'Finished processing images.' );
    } ).catch( error => {
      console.log( error );
    } );
  }

  return loop();
};

const updateCollection = async ( e ) => {
  if ( e ) {
    e.preventDefault();
  }

  await updateGames().then(updateImages);
};

const settings = () => {
  const username = document.getElementById( 'bgg-username' );

  if ( !username.value ) {
    return;
  }

  render(
    <Button onClick={ updateCollection }>Update Collection</Button>,
    document.getElementById( 'bgc-app' ) );
};

settings();

export default settings;
