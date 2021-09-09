<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WooLentorBlocks_Special_Day_Offer_Banner{

	/**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Actions]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

	/**
	 * The Constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'init' ] );
	}

	public function init(){

		// Return early if this function does not exist.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// Load attributes from block.json.
		ob_start();
		include WOOLENTOR_BLOCK_PATH . '/src/blocks/special-day-offer/block.json';
		$metadata = json_decode( ob_get_clean(), true );

		register_block_type(
			$metadata['name'],
			array(
				'attributes'  => $metadata['attributes'],
				'render_callback' => [ $this, 'render_content' ],
				'editor_style'    => ['woolentor-widgets'],
			)
		);

	}

	public function render_content( $settings, $content ){
		
		$uniqClass = 'woolentor-'.$settings['blockUniqId'];
		$classes = array( $uniqClass, 'wlspcial-banner' );

		!empty( $settings['className'] ) ? $classes[] = $settings['className'] : '';
		!empty( $settings['contentPosition'] ) ? $classes[] = 'woolentor-banner-content-pos-'.$settings['contentPosition'] : '';

		$banner_url = !empty( $settings['bannerLink'] ) ? $settings['bannerLink'] : '#';
		$banner_image = !empty( $settings['bannerImage']['id'] ) ? wp_get_attachment_image( $settings['bannerImage']['id'], 'full' ) : ( function_exists('wc_placeholder_img') ? wc_placeholder_img('full') : '' );
		$badge_image = !empty( $settings['badgeImage']['id'] ) ? wp_get_attachment_image( $settings['badgeImage']['id'], 'full' ) : '';

		/** Custom Styles */
		$badgeHorizontalPos = woolentorBlocks_generate_css( $settings, 'badgeHorizontalPos', 'left', '%' );
		$badgeVerticlePos = woolentorBlocks_generate_css( $settings, 'badgeVerticlePos', 'top', '%' );

		$contentAlignment = woolentorBlocks_generate_css( $settings, 'contentAlignment', 'text-align' );
		$contentPadding = woolentorBlocks_Dimention_Control( $settings, 'contentAreaPadding', 'padding' );
		$contentMargin = woolentorBlocks_Dimention_Control( $settings, 'contentAreaMargin', 'margin' );

		$titleColor = woolentorBlocks_generate_css( $settings, 'titleColor', 'color' );
		$titleSize = woolentorBlocks_generate_css( $settings, 'titleSize', 'font-size' );
		$titleMargin = woolentorBlocks_Dimention_Control( $settings, 'titleMargin', 'margin' );
		$titlePadding = woolentorBlocks_Dimention_Control( $settings, 'titlePadding', 'padding' );

		$titleSubColor = woolentorBlocks_generate_css( $settings, 'titleSubColor', 'color' );
		$titleSubSize = woolentorBlocks_generate_css( $settings, 'titleSubSize', 'font-size' );
		$subTitleMargin = woolentorBlocks_Dimention_Control( $settings, 'subTitleMargin', 'margin' );
		$subTitlePadding = woolentorBlocks_Dimention_Control( $settings, 'subTitlePadding', 'padding' );

		$desColor = woolentorBlocks_generate_css( $settings, 'desColor', 'color' );
		$desSize = woolentorBlocks_generate_css( $settings, 'desSize', 'font-size' );
		$desMargin = woolentorBlocks_Dimention_Control( $settings, 'desMargin', 'margin' );
		$desPadding = woolentorBlocks_Dimention_Control( $settings, 'desPadding', 'padding' );

		$offerColor = woolentorBlocks_generate_css( $settings, 'offerColor', 'color' );
		$offerSize = woolentorBlocks_generate_css( $settings, 'offerSize', 'font-size' );
		$offerMargin = woolentorBlocks_Dimention_Control( $settings, 'offerMargin', 'margin' );

		$offerTagColor = woolentorBlocks_generate_css( $settings, 'offerTagColor', 'color' );
		$offerTagSize = woolentorBlocks_generate_css( $settings, 'offerTagSize', 'font-size' );
		$offerTagMargin = woolentorBlocks_Dimention_Control( $settings, 'offerTagMargin', 'margin' );

		$buttonColor = woolentorBlocks_generate_css( $settings, 'buttonColor', 'color' );
		$buttonHoverColor = woolentorBlocks_generate_css( $settings, 'buttonHoverColor', 'color' );
		$buttonSize = woolentorBlocks_generate_css( $settings, 'buttonSize', 'font-size' );
		$buttonMargin = woolentorBlocks_Dimention_Control( $settings, 'buttonMargin', 'margin' );
	
		$all_styles = "
			
			.{$uniqClass} .banner-content{
				{$contentAlignment}
				{$contentPadding}
				{$contentMargin}
			}
			.{$uniqClass} .wlbanner-badgeimage{
				{$badgeHorizontalPos}
				{$badgeVerticlePos}
			}
			.{$uniqClass} .banner-content h2{
				{$titleColor}
				{$titleSize}
				{$titleMargin}
				{$titlePadding}
			}
			.{$uniqClass} .banner-content h6{
				{$titleSubColor}
				{$titleSubSize}
				{$subTitleMargin}
				{$subTitlePadding}
			}
			.{$uniqClass} .banner-content p{
				{$desColor}
				{$desSize}
				{$desMargin}
				{$desPadding}
			}
			.{$uniqClass} .banner-content h5{
				{$offerColor}
				{$offerSize}
				{$offerMargin}
			}
			.{$uniqClass} .banner-content h5 span{
				{$offerTagColor}
				{$offerTagSize}
				{$offerTagMargin}
			}
			.{$uniqClass} .banner-content a{
				{$buttonColor}
				{$buttonSize}
				{$buttonMargin}
			}
			.{$uniqClass} .banner-content a:hover{
				{$buttonHoverColor}
			}
		";

		ob_start();
		?>
			<style><?php echo $all_styles; ?></style>
			<div class="<?php echo implode(' ', $classes ); ?>">
				
				<div class="banner-thumb">
                    <a href="<?php echo esc_url( $banner_url ); ?>">
						<?php echo $banner_image; ?>
                    </a>
                </div>

				<?php
                    if( !empty( $badge_image ) ){
                        echo '<div class="wlbanner-badgeimage">'.$badge_image.'</div>';
                    }
                ?>

				<div class="banner-content">
                    <?php
                        if( !empty( $settings['title'] ) ){
                            echo '<h2>'.$settings['title'].'</h2>';
                        }
                        if( !empty( $settings['subTitle'] ) ){
                            echo '<h6>'.$settings['subTitle'].'</h6>';
                        }
                        if( !empty( $settings['offerAmount'] ) ){
                            echo '<h5>'.$settings['offerAmount'].'<span>'.$settings['offerTagLine'].'</span></h5>';
                        }
                        if( !empty( $settings['bannerDescription'] ) ){
                            echo '<p>'.$settings['bannerDescription'].'</p>';
                        }

                        if( !empty( $settings['buttonText'] ) ){
                            echo '<a href="'.esc_url( $banner_url ).'">'.esc_html__( $settings['buttonText'],'woolentor' ).'</a>';
                        }
                    ?>
                </div>

			</div>
		<?php
		return ob_get_clean();
	}

}
WooLentorBlocks_Special_Day_Offer_Banner::instance();