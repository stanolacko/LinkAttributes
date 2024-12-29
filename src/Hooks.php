<?php

namespace MediaWiki\Extension\LinkAttributes;

use HtmlArmor;
use MediaWiki\Hook\LinkerMakeExternalLinkHook;
use MediaWiki\linker\Hook\HtmlPageLinkRendererEndHook;
use Parser;

class Hooks implements
	LinkerMakeExternalLinkHook,
	HtmlPageLinkRendererEndHook
	{

	private static $attrsAllowed=[
		'rel',
		'rev',
		'charset ',
		'type',
		'hreflang',
		'itemid', // Added 0.6
		'itemprop',
		'itemscope',
		'itemtype', // Added 0.6
		'media',
		'title',
		'accesskey',
		'target',
	];
	
	/**
	 * @param string|HtmlArmor &$text
	 * @param array &$attribs
	 */
	private static function doExtractAttributes ( &$text, &$attribs ) {
		
		global $wgRequest;
		if ( $wgRequest->getText( 'action' ) == 'edit' ) {
			return false;
		}
		
		if ($text instanceof HtmlArmor) {
			$_text = HtmlArmor::getHtml($text);
		} else {
			$_text = $text;
		}
		/* No user input */
		if ( null === $_text )
			return false;
			
		/* Extract attributes, separated by | or ¦. /u is for unicode, to recognize the ¦.*/
		$arr = preg_split( '/[|¦]/u', $_text);
		$_text = array_shift( $arr );
		if ($text instanceof HtmlArmor) {
			$text = new HtmlArmor($_text);
		} else {
			$text = $_text;
		}
		
		foreach ( $arr as $a ) {
			
			$pair = explode( '=', $a, 2);
			
			/* Only go ahead if we have a aaa=bbb pattern, and aaa i an allowed attribute */
			if ( isset( $pair[1] ) && in_array( $pair[0], self::$attrsAllowed ) ) {
				
				/* Add to existing attribute, or create a new */
				if ( isset( $attribs[$pair[0]] ) ) {
					$attribs[$pair[0]] = $attribs[$pair[0]] . ' ' . $pair[1];
				} else {
					$attribs[$pair[0]] = $pair[1];
				}
			}
			
		}
		return true;
			
	}
	
	/**
	 * @link https://www.mediawiki.org/wiki/Manual:Hooks/LinkerMakeExternalLink
	 * @param string &$url
	 * @param string &$text
	 * @param string &$link
	 * @param string[] &$attribs
	 * @param string $linktype
	 * @return bool
	 */
	public function onLinkerMakeExternalLink (
			&$url,
			&$text,
			&$link,
			&$attribs,
			$linktype
			) {
				self::doExtractAttributes ( $text, $attribs );
				return true;
	}
	
	/**
	 * @link https://www.mediawiki.org/wiki/Manual:Hooks/HtmlPageLinkRendererEnd
	 * @param LinkRenderer $linkRenderer
	 * @param LinkTarget $target
	 * @param bool $isKnown
	 * @param string &$text
	 * @param string[] &$attribs
	 * @param string &$ret
	 * @return bool
	 */
	public function onHtmlPageLinkRendererEnd (
			$linkRenderer,
			$target,
			$isKnown,
			&$text,
			&$attribs,
			&$ret
			) {
				self::doExtractAttributes( $text, $attribs );
				return true;
	}
}
