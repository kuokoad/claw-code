/**
 * Volta Hotel – Rooms Block: client-side category filtering.
 *
 * Works with the markup produced by vhr_rooms_shortcode().
 * No dependencies; runs after DOMContentLoaded.
 */
( function () {
    'use strict';

    function initBlock( block ) {
        const pills = block.querySelectorAll( '.vhr-pill' );
        const cards = block.querySelectorAll( '.vhr-card' );

        if ( ! pills.length ) return;

        pills.forEach( function ( pill ) {
            pill.addEventListener( 'click', function () {
                const filter = this.dataset.filter;

                // Update pill states
                pills.forEach( function ( p ) {
                    p.classList.remove( 'vhr-pill--active' );
                    p.setAttribute( 'aria-selected', 'false' );
                } );
                this.classList.add( 'vhr-pill--active' );
                this.setAttribute( 'aria-selected', 'true' );

                // Show / hide cards
                cards.forEach( function ( card ) {
                    if ( filter === 'all' ) {
                        card.removeAttribute( 'hidden' );
                    } else {
                        const cats = ( card.dataset.cat || '' ).split( ' ' );
                        if ( cats.indexOf( filter ) !== -1 ) {
                            card.removeAttribute( 'hidden' );
                        } else {
                            card.setAttribute( 'hidden', '' );
                        }
                    }
                } );
            } );
        } );
    }

    document.addEventListener( 'DOMContentLoaded', function () {
        document.querySelectorAll( '.vhr-rooms-block' ).forEach( initBlock );
    } );

    // Elementor editor live preview re-render
    if ( window.elementorFrontend ) {
        window.elementorFrontend.hooks.addAction(
            'frontend/element_ready/vhr_rooms.default',
            function ( $scope ) {
                $scope[0].querySelectorAll( '.vhr-rooms-block' ).forEach( initBlock );
            }
        );
    }
} )();
