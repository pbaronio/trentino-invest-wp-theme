<?php
/**
 * Footer: bottone "torna su" e script.
 *
 * Il footer visibile (contatti, copyright, link legali) è in template-parts/contatti.php.
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;
?>
	<div data-back-to-top="wrap" class="back-top__wrap"><button data-back-to-top="button" class="back-top__button" aria-label="Torna su">
		<div class="back-top__arrow-wrap">
			<div class="back-top__arrow-row"><?php echo ti_svg( 'back-top-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<div class="back-top__arrow-row"><?php echo ti_svg( 'back-top-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		</div>
	</button></div>
</div>
<?php wp_footer(); ?>
</body>
</html>
