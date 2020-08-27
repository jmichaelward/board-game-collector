/* global wp, bgcollector */
import { useEffect, useState } from 'react';
import { Button } from '@wordpress/components';
import { render } from '@wordpress/element';

const {
  apiFetch
} = wp;

const updateGames = async () => {
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

  return await loop();
};

const updateImages = async () => {
  const loop = () => {
    apiFetch.use( apiFetch.createNonceMiddleware( bgcollector.nonce ) );

    return apiFetch(
      {
        path: 'bgc/v1/collection/images',
        method: 'POST'
      }
    ).then( result => {
      console.info( result );
      if ( 0 !== result.length ) {
        return loop();
      }

      console.info( 'Finished processing images.' );
    } ).catch( error => {
      console.log( error );
    } );
  }

  return await loop();
};

const updateCollection = async ( e ) => {
  if ( e ) {
    e.preventDefault();
  }

  await updateGames().then(updateImages);
};

const data = {
  usernameVerified: bgcollector.usernameVerified,
}

const App = ( props ) => {
  const state = {
    usernameVerified: data.usernameVerified
  };

  const [verifiedUser, setVerifiedUser] = useState(data.usernameVerified);

  document.getElementById( 'bgg-username' ).addEventListener('keyup', function(e) {
    console.log('test');
  });

  return (
    <div>
      <Button onClick={ updateCollection } disabled={props.usernameVerified}>Update Collection</Button>
    </div>
  )
};


const settings = () => {
  const username = document.getElementById( 'bgg-username' );

  if ( !username.value ) {
    return;
  }

  render(
    <App verifieduser={data.usernameVerified}/>,
    document.getElementById( 'bgc-app' ) );
};

(function() {
    settings();
})();

export default settings;
