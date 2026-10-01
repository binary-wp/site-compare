<?php
/**
 * SiteCompare — attaches the Site Compare screen to an admin-guide instance.
 *
 *     \BinaryWP\SiteCompare\SiteCompare::attach( 'my_prefix', array(
 *         'source_base'    => 'https://www.example.com',
 *         'source_sitemap' => 'https://www.example.com/sitemap_index.xml',
 *         'wrapper'        => '/path/to/bin/visual-compare.sh',
 *     ) );
 *
 * Call it before or after the guide instance boots: it either attaches at
 * once or waits for that instance's `booted` hook. Config keys are listed on
 * Compare::__construct().
 */

namespace BinaryWP\SiteCompare;

use BinaryWP\AdminGuide\Context;
use BinaryWP\AdminGuide\Plugin as GuidePlugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SiteCompare {

	/** @var Compare[] guide prefix => attached instance */
	private static $attached = array();

	/**
	 * Attach Site Compare to the admin-guide instance booted as $prefix.
	 *
	 * @param string $prefix Guide instance prefix (as passed to Plugin::boot()).
	 * @param array  $args   Compare config, see Compare::__construct().
	 */
	public static function attach( $prefix, array $args = array() ) {
		if ( ! class_exists( GuidePlugin::class ) ) {
			return;
		}
		$prefix = Context::sanitize_prefix( $prefix );

		$guide = GuidePlugin::get( $prefix );
		if ( $guide ) {
			self::create( $guide->context, $args );
			return;
		}

		add_action(
			$prefix . '/admin_guide_builder/booted',
			function ( $plugin, $context ) use ( $args ) {
				self::create( $context, $args );
			},
			10,
			2
		);
	}

	/**
	 * The Compare attached to a guide instance, if any.
	 *
	 * @param string $prefix
	 * @return Compare|null
	 */
	public static function get( $prefix ) {
		$prefix = Context::sanitize_prefix( $prefix );
		return isset( self::$attached[ $prefix ] ) ? self::$attached[ $prefix ] : null;
	}

	private static function create( Context $context, array $args ) {
		if ( ! isset( self::$attached[ $context->prefix ] ) ) {
			self::$attached[ $context->prefix ] = new Compare( $context, $args );
		}
	}
}
