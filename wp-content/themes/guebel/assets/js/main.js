/**
 * Guebel Theme – Main JavaScript
 *
 * @package Guebel
 * @since   1.0.0
 */

( function () {
	'use strict';

	/* -------------------------------------------------------
	 * Header scroll behaviour
	 * ------------------------------------------------------- */
	const header = document.querySelector( '.site-header' );

	if ( header ) {
		let lastScroll = 0;
		const threshold = 80;

		window.addEventListener( 'scroll', function () {
			const currentScroll = window.scrollY;

			if ( currentScroll > threshold ) {
				header.classList.add( 'is-scrolled' );
			} else {
				header.classList.remove( 'is-scrolled' );
			}

			if ( header.classList.contains( 'header--sticky' ) ) {
				if ( currentScroll > lastScroll && currentScroll > 300 ) {
					header.classList.add( 'is-hidden' );
				} else {
					header.classList.remove( 'is-hidden' );
				}
			}

			lastScroll = currentScroll;
		}, { passive: true } );
	}

	/* -------------------------------------------------------
	 * Mobile menu toggle
	 * ------------------------------------------------------- */
	const menuToggle   = document.querySelector( '[data-menu-toggle]' );
	const mobileMenu   = document.querySelector( '.mobile-nav' );
	const menuClose     = document.querySelector( '[data-menu-close]' );

	function openMenu() {
		if ( ! mobileMenu ) return;
		mobileMenu.classList.add( 'is-open' );
		document.body.classList.add( 'menu-open' );
		menuToggle?.setAttribute( 'aria-expanded', 'true' );
		const firstLink = mobileMenu.querySelector( 'a, button' );
		if ( firstLink ) firstLink.focus();
	}

	function closeMenu() {
		if ( ! mobileMenu ) return;
		mobileMenu.classList.remove( 'is-open' );
		document.body.classList.remove( 'menu-open' );
		menuToggle?.setAttribute( 'aria-expanded', 'false' );
		menuToggle?.focus();
	}

	menuToggle?.addEventListener( 'click', function () {
		const isOpen = mobileMenu?.classList.contains( 'is-open' );
		isOpen ? closeMenu() : openMenu();
	} );

	menuClose?.addEventListener( 'click', closeMenu );

	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' && mobileMenu?.classList.contains( 'is-open' ) ) {
			closeMenu();
		}
	} );

	/* -------------------------------------------------------
	 * Search overlay
	 * ------------------------------------------------------- */
	const searchToggle = document.querySelector( '[data-search-toggle]' );
	const searchOverlay = document.querySelector( '.search-overlay' );
	const searchClose   = document.querySelector( '[data-search-close]' );
	const searchInput   = searchOverlay?.querySelector( 'input[type="search"]' );

	function openSearch() {
		if ( ! searchOverlay ) return;
		searchOverlay.classList.add( 'is-open' );
		document.body.classList.add( 'search-open' );
		searchInput?.focus();
	}

	function closeSearch() {
		if ( ! searchOverlay ) return;
		searchOverlay.classList.remove( 'is-open' );
		document.body.classList.remove( 'search-open' );
		searchToggle?.focus();
	}

	searchToggle?.addEventListener( 'click', openSearch );
	searchClose?.addEventListener( 'click', closeSearch );

	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' && searchOverlay?.classList.contains( 'is-open' ) ) {
			closeSearch();
		}
	} );

	/* -------------------------------------------------------
	 * Scroll animations (IntersectionObserver)
	 * ------------------------------------------------------- */
	const prefersReducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' );

	function initScrollAnimations() {
		const targets = document.querySelectorAll( '.animate-on-scroll' );
		if ( ! targets.length ) return;

		if ( prefersReducedMotion.matches ) {
			targets.forEach( function ( el ) {
				el.classList.add( 'is-visible' );
			} );
			return;
		}

		const observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'is-visible' );
						observer.unobserve( entry.target );
					}
				} );
			},
			{
				threshold: 0.1,
				rootMargin: '0px 0px -40px 0px',
			}
		);

		targets.forEach( function ( el ) {
			observer.observe( el );
		} );
	}

	/* -------------------------------------------------------
	 * Back-to-top button
	 * ------------------------------------------------------- */
	const backToTop = document.querySelector( '.back-to-top' );

	if ( backToTop ) {
		window.addEventListener( 'scroll', function () {
			if ( window.scrollY > 600 ) {
				backToTop.classList.add( 'is-visible' );
			} else {
				backToTop.classList.remove( 'is-visible' );
			}
		}, { passive: true } );

		backToTop.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			window.scrollTo( { top: 0, behavior: 'smooth' } );
		} );
	}

	/* -------------------------------------------------------
	 * Announcement bar dismiss
	 * ------------------------------------------------------- */
	const announceClose = document.querySelector( '[data-announce-close]' );
	const announceBar   = document.querySelector( '.announcement-bar' );

	if ( announceClose && announceBar ) {
		announceClose.addEventListener( 'click', function () {
			announceBar.style.display = 'none';
			try {
				sessionStorage.setItem( 'guebel_announce_dismissed', '1' );
			} catch ( e ) { /* storage unavailable */ }
		} );

		try {
			if ( sessionStorage.getItem( 'guebel_announce_dismissed' ) === '1' ) {
				announceBar.style.display = 'none';
			}
		} catch ( e ) { /* storage unavailable */ }
	}

	/* -------------------------------------------------------
	 * Newsletter form (front-end only; actual handling via plugin)
	 * ------------------------------------------------------- */
	const newsletterForm = document.querySelector( '[data-newsletter-form]' );

	if ( newsletterForm ) {
		newsletterForm.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			const btn = this.querySelector( 'button[type="submit"]' );
			const input = this.querySelector( 'input[type="email"]' );

			if ( ! input?.value ) return;

			btn.textContent = btn.dataset.loadingText || '...';
			btn.disabled = true;

			if ( typeof guebelData !== 'undefined' && guebelData.ajaxUrl ) {
				const formData = new FormData();
				formData.append( 'action', 'guebel_newsletter_subscribe' );
				formData.append( 'email', input.value );
				formData.append( 'nonce', guebelData.nonce || '' );

				fetch( guebelData.ajaxUrl, {
					method: 'POST',
					body: formData,
				} )
					.then( function ( res ) { return res.json(); } )
					.then( function ( data ) {
						if ( data.success ) {
							newsletterForm.innerHTML =
								'<p class="newsletter-success">' +
								( guebelData.i18n?.subscribed || 'Thank you for subscribing!' ) +
								'</p>';
						} else {
							btn.textContent = guebelData.i18n?.subscribe || 'Subscribe';
							btn.disabled = false;
						}
					} )
					.catch( function () {
						btn.textContent = guebelData.i18n?.subscribe || 'Subscribe';
						btn.disabled = false;
					} );
			}
		} );
	}

	/* -------------------------------------------------------
	 * Product quick-view quantity buttons (WooCommerce)
	 * ------------------------------------------------------- */
	document.addEventListener( 'click', function ( e ) {
		const btn = e.target.closest( '.qty-btn' );
		if ( ! btn ) return;

		const input = btn.parentElement?.querySelector( 'input[type="number"]' );
		if ( ! input ) return;

		const min  = parseFloat( input.min ) || 1;
		const max  = parseFloat( input.max ) || Infinity;
		const step = parseFloat( input.step ) || 1;
		let val    = parseFloat( input.value ) || min;

		if ( btn.dataset.action === 'plus' ) {
			val = Math.min( val + step, max );
		} else {
			val = Math.max( val - step, min );
		}

		input.value = val;
		input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
	} );

	/* -------------------------------------------------------
	 * Init
	 * ------------------------------------------------------- */
	document.addEventListener( 'DOMContentLoaded', initScrollAnimations );
} )();
