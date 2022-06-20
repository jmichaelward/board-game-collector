/* global wp, bgcollector */
import { useEffect, useState } from 'react';
import { Button } from '@wordpress/components';
import { render } from '@wordpress/element';

const {
  apiFetch
} = wp;

const data = {
  usernameVerified: bgcollector.usernameVerified,
}

const App = ( props ) => {
  const [verifiedUser, setVerifiedUser] = useState(data.usernameVerified);

  /**
   * Make the games update request.
   *
   * @returns {Promise<*>}
   */
  const requestGamesUpdate = async () => {
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

  /**
   * Make the images update request.
   *
   * @returns {Promise<*>}
   */
  const requestImagesUpdate = async () => {
    if ( ! props.updateImages ) {
      return;
    }

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

  /**
   * Make the request to update the collection based on current field settings.
   *
   * @param e
   * @returns {Promise<void>}
   */
  const updateCollection = async ( e ) => {
    if ( e ) {
      e.preventDefault();
    }

    await requestGamesUpdate()
      .then(requestImagesUpdate)
      .finally(() => console.log('done'));
  };

  /**
   * Render the component.
   */
  return (
    <>
      <Button className="button button-secondary" onClick={ updateCollection } disabled={props.usernameVerified}>Update Collection</Button>
    </>
  )
};

const settings = () => {
  const username     = document.getElementById( 'bgg-username' );
  const updateImages = document.getElementById( 'bgg-update-with-images' );

  if ( !username.value ) {
    return;
  }

  render(
    <App verifieduser={data.usernameVerified} updateImages={updateImages.checked}/>,
    document.getElementById( 'bgc-app' )
  );
};

(function() {
    settings();
})();

export default settings;
