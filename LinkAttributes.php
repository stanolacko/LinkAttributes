<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__,
	'name' => 'LinkAttributes',
	'version' => '0.5',
	'url' => 'http://www.mediawiki.org/wiki/Extension:LinkAttributes',
	'author' => array( 'Stano Lacko, Leo Wallentin', 'Sinscerly' ),
	'license-name' => 'BSD',
	'descriptionmsg' => 'linkattr-desc',
);

$wgMessagesDirs['LinkAttributes'] = __DIR__ . '/i18n';

$wgAutoloadClasses['LinkAttributes'] = dirname( __FILE__ ) . '/LinkAttributes.body.php';

global $wgHooks;
$wgHooks['LinkerMakeExternalLink'][] = 'LinkAttributes::ExternalLink';
$wgHooks['HtmlPageLinkRendererEnd'][] = 'LinkAttributes::InternalLink';
