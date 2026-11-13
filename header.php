<?php
/**
 * Header: <head>, navbar.
 *
 * Gli attributi data-wf-page / data-wf-site e i data-w-id servono alle
 * interazioni di Webflow (webflow.js): navbar che sparisce/ricompare e cambia colore allo scroll.
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

$ti_menu = array();
$ti_locs = get_nav_menu_locations();
if ( ! empty( $ti_locs['principale'] ) ) {
	foreach ( (array) wp_get_nav_menu_items( $ti_locs['principale'] ) as $ti_item ) {
		if ( ! $ti_item->menu_item_parent ) {
			$ti_menu[] = array(
				'title'  => $ti_item->title,
				'url'    => $ti_item->url,
				'target' => $ti_item->target,
			);
		}
	}
}
if ( ! $ti_menu ) {
	foreach ( ti_defaults()['menu'] as $ti_title => $ti_url ) {
		$ti_menu[] = array(
			'title'  => $ti_title,
			'url'    => $ti_url,
			'target' => '',
		);
	}
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> data-wf-page="6a4509b75b34bdeab0d75f77" data-wf-site="6a4509b65b34bdeab0d75f22">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta content="width=device-width, initial-scale=1" name="viewport">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="page-main">
	<div data-animation="default" class="navbar w-nav" data-easing2="ease" data-easing="ease" navbar="" data-collapse="medium" style="-webkit-transform:translate3d(0, 0%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 0%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 0%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 0%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)" data-w-id="ef610f6c-c991-7da0-2e68-5ba650e97195" role="banner" data-duration="400">
		<div class="container">
			<div class="page-padding is-navbar">
				<div class="navbar_layout">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"<?php echo is_front_page() ? ' aria-current="page"' : ''; ?> class="w-nav-brand<?php echo is_front_page() ? ' w--current' : ''; ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
						<div class="embed-logo w-embed"><?php echo ti_svg( 'logo' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					</a>
					<nav role="navigation" class="nav-menu w-nav-menu">
						<div class="nav-container">
							<?php foreach ( $ti_menu as $ti_item ) : ?>
								<div class="link-group">
									<a data-underline-link="" href="<?php echo esc_url( ti_anchor_url( $ti_item['url'] ) ); ?>"<?php echo $ti_item['target'] ? ' target="' . esc_attr( $ti_item['target'] ) . '"' : ''; ?> class="underline-link text-s nav-link"><?php echo esc_html( $ti_item['title'] ); ?></a>
								</div>
							<?php endforeach; ?>
						</div>
					</nav>
					<div class="menu-button w-nav-button">
						<div class="icon w-icon-nav-menu"></div>
						<div data-w-id="dfb388a3-bb89-5690-eb83-3c78325ce2f9" class="menu_text">Menu</div>
					</div>
				</div>
			</div>
		</div>
	</div>
