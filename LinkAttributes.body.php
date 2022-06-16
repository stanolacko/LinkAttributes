<?php

use MediaWiki\Linker\LinkRenderer;
use MediaWiki\Linker\LinkTarget;

class LinkAttributes {

	private static $attrsAllowed=[
		'rel',
		'rev',
		'charset ',
		'type',
		'hreflang',
		'itemprop',
		'itemscope',
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

		if ( $text instanceof HtmlArmor ) {
			$text = HtmlArmor::getHtml( $text );
		}
		/* No user input */
		if ( null === $text )
			return false;

		/* Extract attributes, separated by | or ¦. /u is for unicode, to recognize the ¦.*/
		$arr = preg_split( '/[|¦]/u', $text );
		$text = array_shift( $arr );

		foreach ( $arr as $a ) {

			$pair = explode( '=', $a );

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
	public static function ExternalLink (
		&$url,
		&$text,
		&$link,
		&$attribs
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
	public static function InternalLink(
		LinkRenderer $linkRenderer,
		LinkTarget $target,
		$isKnown,
		&$text,
		&$attribs,
		&$ret
	) {
		static::doExtractAttributes( $text, $attribs );
		return true;
	}
}
