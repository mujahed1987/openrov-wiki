<?php
/**
 * Mozilla cavendish theme
 * Modified by DaSch for MW 1.19 and WeCoWi
 *
 * Loosely based on the cavendish style by Gabriel Wicke
 *
 * @todo document
 * @package MediaWiki
 * @subpackage Skins
 */


if( !defined( 'MEDIAWIKI' ) )
	die( -1 );

require_once('includes/SkinTemplate.php');

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 * @todo document
 * @package MediaWiki
 * @subpackage Skins
 */
 
/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 * @todo document
 * @ingroup Skins
 */
class SkinCavendish extends SkinTemplate {
	/** Using cavendish. */

	function initPage( &$out ) {
		SkinTemplate::initPage( $out );
		$this->skinname = 'cavendish'; 
		$this->stylename = 'cavendish';
		$this->template = 'CavendishTemplate'; 
		$this->useHeadElement = true;
		}
	function setupSkinUserCss( OutputPage $out ) {
		global $wgHandheldStyle, $wgStyleVersion, $wgJsMimeType, $wgStylePath, $wgVersion;
		global $cavendishLogoURL, $cavendishLogoWidth, $cavendishLogoHeight, $cavendishLogoMargin, $cavendishSiteWith, $cavendishExtensionCSS, $cavendishSidebarSearchbox;
		parent::setupSkinUserCss( $out );
		// Append to the default screen common & print styles...
		$out->addStyle( 'cavendish/print.css', 'print' );
		$out->addStyle( 'cavendish/cavendish.css', 'screen' );
		if( $wgHandheldStyle ) {
			// Currently in testing... try 'chick/main.css'
			$out->addStyle( $wgHandheldStyle, 'handheld' );
		}
		$out->addStyle( 'cavendish/IE50Fixes.css', 'screen', 'lt IE 5.5000' );
		$out->addStyle( 'cavendish/IE55Fixes.css', 'screen', 'IE 5.5000' );
		$out->addStyle( 'cavendish/IE60Fixes.css', 'screen', 'IE 6' );
		$out->addStyle( 'cavendish/IE70Fixes.css', 'screen', 'IE 7' );
		
		$out->addStyle( 'cavendish/rtl.css', 'screen', '', 'rtl' );
		
		/* README for details */
		if(!isset($cavendishLogoURL)) {
			$cavendishLogoURL=$wgStylePath . "/cavendish/wiki_header_logo.png";
		}
		if(!isset($cavendishLogoWidth)) {
			$cavendishLogoWidth="250";
		}
		if(!isset($cavendishLogoHeight)) {
			$cavendishLogoHeight="93";
		}
		$cavendishLogoMarginToAdd="";
		if(isset($cavendishLogoMargin)) {
			$cavendishLogoMarginToAdd .= 'margin-top:'.$cavendishLogoMargin.'px;';
		}
		if(!isset($cavendishSidebarSearchbox)) {
			$cavendishSidebarSearchbox=false;
		}
		$cavendishSidebarSearchboxToAdd = "";
		if(!$cavendishSidebarSearchbox) {
			$cavendishSidebarSearchboxToAdd .= '#nav #p-search {display:none;}';
		} 
		$cavendishglobalWrapper="";
		if (isset($cavendishSiteWith)) {
			$cavendishglobalWrapper = '#globalWrapper {width:'. $cavendishSiteWith .'px;}';
		}
		if (!isset($cavendishExtensionCSS)) {
			$cavendishExtensionCSS = true;		
		}
		if ($cavendishExtensionCSS) {
			$out->addStyle( 'cavendish/extensions.css', 'screen' );	
		}
		$headStyle = '#header h6 a { background: transparent url("'.$cavendishLogoURL.'") no-repeat; width:'.$cavendishLogoWidth.'px;height:'.$cavendishLogoHeight.'px;'.$cavendishLogoMarginToAdd.'}'.$cavendishglobalWrapper.$cavendishSidebarSearchboxToAdd;
		$out->addInlineStyle($headStyle);
	}
}

class CavendishTemplate extends MonoBookTemplate {
	var $skin;
	/**
	 * Template filter callback for cavendish skin.
	 * Takes an associative array of data set from a SkinTemplate-based
	 * class, and a wrapper for MediaWiki's localization database, and
	 * outputs a formatted page.
	 *
	 * @access private
	 */
	function execute() {
		global $wgRequest, $wgLang;
		$styleversion = '1.5.0';
		$this->skin = $skin = $this->data['skin'];
		$action = $wgRequest->getText( 'action' );
		if ( $action == "") {
			$action = "view";
		}
		// Suppress warnings to prevent notices about missing indexes in $this->data
		wfSuppressWarnings();
		$this->html( 'headelement' );
?>
<div id="internal"></div>
<!-- Skin-Version: <?php echo $styleversion ?> //Please leave this for bugtracking purpose//-->
<div id="globalWrapper" class="<?php echo $action ?>">
	<div id="p-personal" class="portlet">
		<h5><?php $this->msg('personaltools') ?></h5>
		<div class="pBody">
			<ul <?php $this->html('userlangattributes') ?>>
			<?php foreach($this->data['personal_urls'] as $key => $item) {?>
			
			<li id="<?php echo Sanitizer::escapeId( "pt-$key" ) ?>" class="<?php
					if ($item['active']) { ?>active <?php } ?>top-nav-element">
				<span class="top-nav-left">&nbsp;</span>
				<a class="top-nav-mid <?php echo htmlspecialchars($item['class']) ?>" 
				   href="<?php echo htmlspecialchars($item['href']) ?>">
				   <?php echo htmlspecialchars($item['text']) ?></a>	
				<span class="top-nav-right">&nbsp;</span></li>
				<?php
				} ?>
			
			</ul>
		</div>
	</div>
	<div id="header">
		<a name="top" id="contentTop"></a>
		<h6>
		<a
		href="<?php echo htmlspecialchars($this->data['nav_urls']['mainpage']['href'])?>"
		title="<?php $this->msg('mainpage') ?>"><?php $this->text('pagetitle') ?></a></h6>
		<div id="p-cactions" class="portlet"><ul>
<?php			foreach($this->data['content_actions'] as $key => $tab) {
					echo '
				<li id="' . Sanitizer::escapeId( "ca-$key" ) . '"';
					if( $tab['class'] ) {
						echo ' class="'.htmlspecialchars($tab['class']).'"';
					}
					echo '><a href="'.htmlspecialchars($tab['href']).'"';
					# We don't want to give the watch tab an accesskey if the
					# page is being edited, because that conflicts with the
					# accesskey on the watch checkbox.  We also don't want to
					# give the edit tab an accesskey, because that's fairly su-
					# perfluous and conflicts with an accesskey (Ctrl-E) often
					# used for editing in Safari.
					if( in_array( $action, array( 'edit', 'submit' ) ) && in_array( $key, array( 'edit', 'watch', 'unwatch' ) ) ) {
						echo $skin->tooltip( "ca-$key" );
					}
					else {
						echo $skin->tooltip( "ca-$key" );
					}
					echo '>'.htmlspecialchars($tab['text']).'</a></li>';
				}
								
				?>
			</ul></div>
			<?php $this->searchBox(); ?>
	</div>
	
	<div id="mBody">
		<div id="side">
			<div id="nav">
<?php
		$sidebar = $this->data['sidebar'];
		if ( !isset( $sidebar['SEARCH'] ) ) $sidebar['SEARCH'] = true;
		if ( !isset( $sidebar['TOOLBOX'] ) ) $sidebar['TOOLBOX'] = true;
		if ( !isset( $sidebar['LANGUAGES'] ) ) $sidebar['LANGUAGES'] = true;
		foreach ($sidebar as $boxName => $cont) {
			if ( $boxName == 'SEARCH' ) {
//				$this->searchBox();	
			} elseif ( $boxName == 'TOOLBOX' ) {
				$this->toolbox();
			} elseif ( $boxName == 'LANGUAGES' ) {
				$this->languageBox();
			} else {
				$this->customBox( $boxName, $cont );
			}
		}
		?>
</div>
</div>
		</div><!-- end of SIDE div -->
		<div id="column-content">
			<div id="content">
				<a id="top"></a>
				<?php if($this->data['sitenotice']) { ?><div id="siteNotice"><?php $this->html('sitenotice') ?></div><?php } ?>
				<h1 id="firstHeading" class="firstHeading"><?php $this->html('title') ?></h1>
				<div id="bodyContent">
					<h3 id="siteSub"><?php $this->msg('tagline') ?></h3>
					<div id="contentSub"><?php $this->html('subtitle') ?></div>
					<?php if($this->data['undelete']) { ?><div id="contentSub2"><?php $this->html('undelete') ?></div><?php } ?>
					<?php if($this->data['newtalk'] ) { ?><div class="usermessage"><?php $this->html('newtalk')  ?></div><?php } ?>
					<?php if($this->data['showjumplinks']) { ?><div id="jump-to-nav"><?php $this->msg('jumpto') ?> <a href="#column-one"><?php $this->msg('jumptonavigation') ?></a>, <a href="#searchInput"><?php $this->msg('jumptosearch') ?></a></div><?php } ?>
					<!-- start content -->
					<?php $this->html('bodytext') ?>
					<?php if($this->data['catlinks']) { $this->html('catlinks'); } ?>
					<!-- end content -->
					<?php if($this->data['dataAfterContent']) { $this->html ('dataAfterContent'); } ?>
				</div>
			</div><!-- end of MAINCONTENT div -->	
		</div>
	</div><!-- end of MBODY div -->
	<div class="visualClear"></div>
	<div id="footer">
		<table>
			<!--<tr><td colspan="3"><a href="#internal">Up</a></td></tr>-->
			<tr>
				<td rowspan="2" class="f-iconsection">
		<?php if($this->data['copyrightico']) { ?><div id="f-copyrightico"><?php $this->html('copyrightico') ?></div><?php } ?>
				</td>
				<td align="center">
				Build One | <a href="http://openrov.com/page/openrov-kits">OpenROV Kits</a> | <a href="http://openrov.com/forum">Participate</a> | <a href="http://openrov.com/profiles/blog/list">Blog</a> | <a href="http://openrov.com/page/about">About</a>
<?php	// Generate additional footer links
		$footerlinks = array(
			/* 'lastmod', 'viewcount', 'numberofwatchingusers', 'credits', 'copyright',*/
			'privacy', 'about', 'disclaimer', 'tagline',
			'lastmod',
		);
		$validFooterLinks = array();
		foreach( $footerlinks as $aLink ) {
			if( isset( $this->data[$aLink] ) && $this->data[$aLink] ) {
				$validFooterLinks[] = $aLink;
			}
		}
		if ( count( $validFooterLinks ) > 0 ) {
?>			<ul id="f-list">
<?php
			foreach( $validFooterLinks as $aLink ) {
				if( isset( $this->data[$aLink] ) && $this->data[$aLink] ) {
?>					<li id="f-<?php echo$aLink?>"><?php $this->html($aLink) ?></li>
<?php 			}
			}
		}
?></ul></td>
				<td rowspan="2" class="f-iconsection">
					<?php
					$validFooterIcons = $this->getFooterIcons( "nocopyright" );
					foreach ( $validFooterIcons as $blockName => $footerIcons ) { ?>
							<div id="f-<?php echo htmlspecialchars($blockName); ?>ico"><?php
						foreach ( $footerIcons as $icon ) {
							echo $this->skin->makeFooterIcon( $icon );
							}
					}
					?></div>
				</td>
			</tr>
			<tr>
				<td><div id="skin-info">
					<!-- Mozilla Cavendish Theme based on Cavendish style by Gabriel Wicke modified by <a href="http://www.dasch-tour.de" target="_blank">DaSch</a> for the <a href="http://www.wecowi.de/">Web Community Wiki</a><br/>
					<a href="http://sourceforge.net/projects/wecowi/">Sourceforge Projectpage</a> &ndash; <a href="https://sourceforge.net/tracker/?group_id=256816&atid=3135768">Report Bug</a> &ndash; Skin-Version: <?php echo $styleversion ?> -->
				</div></td>
			</tr>
		</table>
	</div><!-- end of the FOOTER div -->
</div><!-- end of the CONTAINER div -->
<!-- scripts and debugging information -->
<?php

		$this->printTrail();
		echo Html::closeElement( 'body' );
		echo Html::closeElement( 'html' );
		wfRestoreWarnings();
	}
} // end of class
