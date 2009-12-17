<?php
/*
Plugin Name: XML Sitemap Feed
Plugin URI: http://4visions.nl/en/index.php?section=57
Description: Creates a dynamic XML feed that complies with the XML Sitemap protocol to aid Google, Yahoo, MSN, Ask.com indexing your blog. Based on the Standard XML Sitemap Generator by Patrick Chia.
Version: 3.4
Author: RavanH
Author URI: http://4visions.nl/
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=XML%20Sitemap%20Feed&item_number=2%2e6%2e2%2e9&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8
*/

/*  Copyright 2009 RavanH  (http://4visions.nl/ email : ravanhagen@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* --------------------
       VALUES
   -------------------- */

// set version
define('XMLSFVERSION','3.4');

// dir
$xmlsf_dir = dirname(__FILE__);

// check if xml-sitemap.php is moved one dir up like in WPMU's /mu-plugins/
if (file_exists($xmlsf_dir.'/xml-sitemap-feed'))
	$xmlsf_dir = $xmlsf_dir . '/xml-sitemap-feed';

/* --------------------
       FUNCTIONS
   -------------------- */

// FEEDS //
// set the XML feeds up
function xml_sitemap_add_feeds() {
	add_feed('sitemap.xml','do_feed_sitemapxml');
	add_feed('sitemap.xsl','do_feed_sitemapxsl');
}
// load XML template
if ( !function_exists(do_feed_sitemapxml) ) {
	function do_feed_sitemapxml() {
		global $xmlsf_dir;
		load_template( $xmlsf_dir . '/feed-sitemap.xml.php' );
	}
}
// load XSL (style) template
if ( !function_exists(do_feed_sitemapxsl) ) {
	function do_feed_sitemapxsl() {
		global $xmlsf_dir;
		load_template( $xmlsf_dir . '/feed-sitemap.xsl.php' );
	}
}
// add the rewrite rules
function xml_sitemap_feed_rewrite($wp_rewrite) {
	$feed_rules = array(
		'^sitemap.xml$' => 'index.php?feed=sitemap.xml',
		'^sitemap.xsl$' => 'index.php?feed=sitemap.xsl'
	);
	$wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;
}
// recreate rewrite rules (only needed upon plugin activation)
function xml_sitemap_activate() {
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

// ROBOTSTXT //
// get sitemap location in robots.txt generated by WP
function xml_sitemap_robots() {
	echo "Sitemap: ".get_option('home')."/sitemap.xml\n\n";
}

/* --------------------
       HOOKS 
   -------------------- */

if ( $wpdb->blogid && function_exists('get_site_option') && get_site_option('tags_blog_id') == $wpdb->blogid ) {
	// we are on wpmu and this is a tags blog!
	// create NO sitemap since it will be full 
	// of links outside the blogs own domain...
	return;
} else {
	// FEEDS
	add_action('init', 'xml_sitemap_add_feeds');

	// REWRITES
	add_filter('generate_rewrite_rules', 'xml_sitemap_feed_rewrite');

	// ROBOTSTXT
	add_action('do_robotstxt', 'xml_sitemap_robots');
}

// ACTIVATION
register_activation_hook( __FILE__, 'xml_sitemap_activate' );
?>
