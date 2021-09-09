<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WooLentorBlocks_Brand_Logo{

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
		include WOOLENTOR_BLOCK_PATH . '/src/blocks/brand-logo/block.json';
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
		$classes = array( $uniqClass, 'ht-brand-wrap' );

		!empty( $settings['className'] ) ? $classes[] = $settings['className'] : '';
        !empty( $settings['columns'] ) ? $classes[] = 'wl-columns-'.$settings['columns'] : 'wl-columns-6';

		$default_img = '<img src="'.WOOLENTOR_BLOCK_URL.'/src/assets/images/brand.png'.'" alt="'.esc_html__('Brand Logo','woolentor').'">';
		$brands = $settings['brandLogoList'];


		/** Custom Styles */
		$singleItemAreaBorderType = woolentorBlocks_generate_css( $settings, 'singleItemAreaBorderType', 'border-style' );
		$singleItemAreaBorderWidth = woolentorBlocks_Dimention_Control( $settings, 'singleItemAreaBorderWidth', 'border-width' );
		$singleItemAreaBorderRadius = woolentorBlocks_Dimention_Control( $settings, 'singleItemAreaBorderRadius', 'border-radius' );
		$singleItemAreaMargin = woolentorBlocks_Dimention_Control( $settings, 'singleItemAreaMargin', 'margin' );
		$singleItemAreaPadding = woolentorBlocks_Dimention_Control( $settings, 'singleItemAreaPadding', 'padding' );
		$brandAlignment = woolentorBlocks_generate_css( $settings, 'brandAlignment', 'text-align' );
		$singleItemAreaBorderColor = woolentorBlocks_generate_css( $settings, 'singleItemAreaBorderColor', 'border-color' );

		$brandImageBorderType = woolentorBlocks_generate_css( $settings, 'brandImageBorderType', 'border-style' );
		$brandImageBorderWidth = woolentorBlocks_Dimention_Control( $settings, 'brandImageBorderWidth', 'border-width' );
		$brandImageBorderRadius = woolentorBlocks_Dimention_Control( $settings, 'brandImageBorderRadius', 'border-radius' );
		$brandImageBorderColor = woolentorBlocks_generate_css( $settings, 'brandImageBorderColor', 'border-color' );

		$all_styles = "
			.{$uniqClass} .wl-single-brand{
				{$singleItemAreaBorderType}
				{$singleItemAreaBorderWidth}
				{$singleItemAreaBorderRadius}
				{$singleItemAreaMargin}
				{$singleItemAreaPadding}
				{$brandAlignment}
				{$singleItemAreaBorderColor}
			}
			.{$uniqClass} .wl-single-brand img{
				{$brandImageBorderType}
				{$brandImageBorderWidth}
				{$brandImageBorderRadius}
				{$brandImageBorderColor}
			}
			.{$uniqClass} .wl-row > [class*='col-']{
				padding: 0  {$settings['itemSpace']}px;
			}
		";

		ob_start();
		?>
			<style><?php echo $all_styles; ?></style>
			<div class="<?php echo implode(' ', $classes ); ?>">
				<?php
					$collumval = 'wl-col-6';
					if( !empty( $settings['columns'] ) ){
						$collumval = 'wl-col-'.$settings['columns'];
					}
					
					if( is_array( $brands ) ){
						echo '<div class="wl-row '.( $settings['noGutter'] === true ? 'wlno-gutters' : '' ).'">';
							foreach ( $brands as $key => $brand ) {
			
								$image = !empty( $brand['image']['id'] ) ? wp_get_attachment_image( $brand['image']['id'], 'full' ) : $default_img;
			
								if( !empty( $brand['link'] ) ){
									$logo = sprintf('<a href="%s" target="_blank">%s</a>',esc_url( $brand['link'] ), $image );
								}else{
									$logo = $image;
								}
			
								?>
									<div class="<?php echo esc_attr( esc_attr( $collumval ) ); ?>">
										<div class="wl-single-brand">
											<?php echo $logo; ?>
										</div>
									</div>
								<?php
							}
						echo '</div>';
					}
				?>
			</div>
		<?php
		return ob_get_clean();
	}

}
WooLentorBlocks_Brand_Logo::instance();