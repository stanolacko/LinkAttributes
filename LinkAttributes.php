<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__,
	'name' => 'LinkAttributes',
	'version' => '0.4',
	'url' => 'http://www.mediawiki.org/wiki/Extension:LinkAttributes',
	'author' => array( 'Stano Lacko, Leo Wallentin' ),
	'license-name' => 'BSD',
);

$wgAutoloadClasses['LinkAttributes'] = dirname( __FILE__ ) . '/LinkAttributes.body.php';
$wgExtensionMessagesFiles['LinkAttributes'] = dirname( __FILE__ ) . '/LinkAttributes.i18n.php';

global $wgHooks;
$wgHooks['LinkerMakeExternalLink'][] = 'LinkAttributes::ExternalLink';
$wgHooks['HtmlPageLinkRendererEnd'][] = 'LinkAttributes::InternalLink';
