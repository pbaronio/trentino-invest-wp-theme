<?php
/**
 * Home.
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part( 'template-parts/home/hero' );
get_template_part( 'template-parts/home/intro' );
get_template_part( 'template-parts/home/chi-siamo' );
get_template_part( 'template-parts/home/approccio' );
get_template_part( 'template-parts/home/criteri' );
get_template_part( 'template-parts/home/team' );
get_template_part( 'template-parts/home/portfolio' );
get_template_part( 'template-parts/contatti' );

get_footer();
