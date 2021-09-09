<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WooLentorBlocks_Store_Feature{

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
		include WOOLENTOR_BLOCK_PATH . '/src/blocks/store-feature/block.json';
		$metadata = json_decode( ob_get_clean(), true );

		register_block_type(
			$metadata['name'],
			array(
				'attributes'  => $metadata['attributes'],
				'render_callback' => [ $this, 'render_content' ],
				'editor_style'    => ['woolentor-store-feature'],
				'style'			  => ['woolentor-store-feature']
			)
		);

	}

	public function render_content( $settings, $content ){
		
		$uniqClass = 'woolentor-'.$settings['blockUniqId'];
		$classes = array( $uniqClass, 'woolentor-blocks ht-feature-wrap' );

		!empty( $settings['className'] ) ? $classes[] = $settings['className'] : '';
        !empty( $settings['layout'] ) ? $classes[] = 'ht-feature-style-'.$settings['layout'] : 'ht-feature-style-1';
        !empty( $settings['textAlignment'] ) ? $classes[] = 'woolentor-text-align-'.$settings['textAlignment'] : 'woolentor-text-align-center';

		$store_image = !empty( $settings['featureImage']['id'] ) ? wp_get_attachment_image( $settings['featureImage']['id'], 'full' ) : '';

		/** Custom Styles */
		$areaBorderColor = woolentorBlocks_generate_css( $settings, 'areaBorderColor', 'border-color' );
		$areaHoverBorderColor = woolentorBlocks_generate_css( $settings, 'areaHoverBorderColor', 'border-color', ' !important' );
		$areaBackgroundColor = woolentorBlocks_generate_css( $settings, 'areaBackgroundColor', 'background-color' );
		$areaMargin = woolentorBlocks_Dimention_Control( $settings, 'areaMargin', 'margin' );
		$areaPadding = woolentorBlocks_Dimention_Control( $settings, 'areaPadding', 'padding' );

		$titleColor = woolentorBlocks_generate_css( $settings, 'titleColor', 'color' );
		$titleSize = woolentorBlocks_generate_css( $settings, 'titleSize', 'font-size' );
		$titleMargin = woolentorBlocks_Dimention_Control( $settings, 'titleMargin', 'margin' );

		$subTitleColor = woolentorBlocks_generate_css( $settings, 'subTitleColor', 'color' );
		$subTitleSize = woolentorBlocks_generate_css( $settings, 'subTitleSize', 'font-size' );
		$subTitleMargin = woolentorBlocks_Dimention_Control( $settings, 'subTitleMargin', 'margin' );

		$all_styles = "

			.{$uniqClass}.ht-feature-wrap{
				{$areaBackgroundColor}
			}
			.{$uniqClass}.ht-feature-wrap .ht-feature-inner{
				{$areaBorderColor}
				{$areaMargin}
				{$areaPadding}
			}
			.{$uniqClass}.ht-feature-wrap:hover .ht-feature-inner{
				{$areaHoverBorderColor}
			}
			.{$uniqClass}.ht-feature-wrap .ht-feature-content h4{
				{$titleColor}
				{$titleSize}
				{$titleMargin}
			}
			.{$uniqClass}.ht-feature-wrap .ht-feature-content p{
				{$subTitleColor}
				{$subTitleSize}
				{$subTitleMargin}
			}

		";

		ob_start();
		?>
			<style><?php echo $all_styles; ?></style>
			<div class="<?php echo implode(' ', $classes ); ?>">

				<div class="ht-feature-inner">
					<?php
						if( !empty( $store_image ) ){
							echo '<div class="ht-feature-img">'.$store_image.'</div>';
						}
					?>
					<div class="ht-feature-content">
						<?php
							if( !empty( $settings['title'] ) ){
								echo '<h4>'.$settings['title'].'</h4>';
							}
							if( !empty( $settings['subTitle'] ) ){
								echo '<p>'.$settings['subTitle'].'</p>';
							}
						?>
					</div>
				</div>

			</div>
		<?php
		return ob_get_clean();
	}

}
WooLentorBlocks_Store_Feature::instance();