/* The AI readiness check: posts the typed address to Opal Care and shows the report. Text only, nothing stored. */
( function () {
	'use strict';

	var SAYS = {
		empty: 'I need the address of a public website. Could you check it?',
		offline: 'I could not reach the checker. Please try again in a minute.',
		checking: 'Checking. This takes a few seconds.',
		title: 'What I found on ',
		status: { pass: 'Passed.', fail: 'Needs a fix.', info: 'Good to know.', unavailable: 'Could not check.' },
		icon: { pass: '✓', fail: '!', info: 'i', unavailable: '–' }
	};

	function el( tag, cls, text ) {
		var node = document.createElement( tag );
		if ( cls ) {
			node.className = cls;
		}
		if ( text ) {
			node.textContent = text;
		}
		return node;
	}

	function show( out, nodes ) {
		out.replaceChildren.apply( out, nodes );
	}

	function message( text ) {
		return [ el( 'p', 'opal-readiness__message', text ) ];
	}

	function report( data ) {
		var nodes = [ el( 'h3', 'opal-readiness__title', SAYS.title + data.domain ) ];
		var list = el( 'ul', 'opal-readiness__items' );
		( data.items || [] ).forEach( function ( item ) {
			var status = SAYS.icon[ item.status ] ? item.status : 'unavailable';
			var row = el( 'li', 'opal-readiness__item is-' + status );
			var icon = el( 'span', 'opal-readiness__icon', SAYS.icon[ status ] );
			icon.setAttribute( 'aria-hidden', 'true' );
			var body = el( 'span', 'opal-readiness__text' );
			body.appendChild( el( 'span', 'screen-reader-text', SAYS.status[ status ] + ' ' ) );
			body.appendChild( el( 'strong', '', String( item.label || '' ) ) );
			body.appendChild( el( 'span', 'opal-readiness__line', String( item.line || '' ) ) );
			row.appendChild( icon );
			row.appendChild( body );
			list.appendChild( row );
		} );
		nodes.push( list );
		if ( data.offer && /^https:\/\//.test( String( data.offer.href ) ) ) {
			var offer = el( 'div', 'opal-readiness__offer' );
			offer.appendChild( el( 'p', '', String( data.offer.line || '' ) ) );
			var link = el( 'a', 'opal-readiness__cta wp-element-button', String( data.offer.label || '' ) );
			link.href = data.offer.href;
			offer.appendChild( link );
			nodes.push( offer );
		}
		return nodes;
	}

	function setup( root ) {
		var form = root.querySelector( '.opal-readiness__form' );
		var input = root.querySelector( '.opal-readiness__input' );
		var button = root.querySelector( '.opal-readiness__button' );
		var out = root.querySelector( '.opal-readiness__out' );
		var endpoint = root.getAttribute( 'data-endpoint' );
		var label = button.textContent;
		if ( ! form || ! input || ! out || ! endpoint ) {
			return;
		}

		form.addEventListener( 'submit', function ( event ) {
			event.preventDefault();
			var url = input.value.trim();
			if ( ! url ) {
				show( out, message( SAYS.empty ) );
				input.focus();
				return;
			}
			button.disabled = true;
			button.textContent = 'Checking…';
			show( out, message( SAYS.checking ) );

			fetch( endpoint, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify( { url: url } ),
				signal: AbortSignal.timeout( 30000 )
			} )
				.then( function ( res ) {
					return res.json().catch( function () {
						return {};
					} );
				} )
				.then( function ( data ) {
					if ( data && data.status === 'checked' && Array.isArray( data.items ) ) {
						show( out, report( data ) );
					} else {
						show( out, message( ( data && data.message ) || SAYS.offline ) );
					}
					out.focus();
				} )
				.catch( function () {
					show( out, message( SAYS.offline ) );
					out.focus();
				} )
				.then( function () {
					button.disabled = false;
					button.textContent = label;
				} );
		} );
	}

	document.querySelectorAll( '.opal-readiness' ).forEach( setup );
}() );
