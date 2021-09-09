<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WooLentorBlocks_Image_Marker{

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
		include WOOLENTOR_BLOCK_PATH . '/src/blocks/image-marker/block.json';
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
		$classes = array( $uniqClass, 'wlb-marker-wrapper' );

		!empty( $settings['className'] ) ? $classes[] = $settings['className'] : '';
		!empty( $settings['style'] ) ? $classes[] = 'wlb-marker-style-'.$settings['style'] : 'wlb-marker-style-1';

		$background_image = woolentorBlocks_Background_Control( $settings, 'bgProperty' );

		/** Custom Styles */
		$markerColor = woolentorBlocks_generate_css( $settings, 'markerColor', 'color' );
		$markerBGColor = woolentorBlocks_generate_css( $settings, 'markerBGColor', 'background-color' );
		$markerBorderColor = woolentorBlocks_generate_css( $settings, 'markerBorderColor', 'border-color' );
		$markerBorderRadius = woolentorBlocks_Dimention_Control( $settings, 'markerBorderRadius', 'border-radius' );
		$markerPadding = woolentorBlocks_Dimention_Control( $settings, 'markerPadding', 'padding' );

		$markerContentBGColor = woolentorBlocks_generate_css( $settings, 'markerContentBGColor', 'background-color' );
		$markerContentBorderRadius = woolentorBlocks_Dimention_Control( $settings, 'markerContentBorderRadius', 'border-radius' );
		$markerContentPadding = woolentorBlocks_Dimention_Control( $settings, 'markerContentPadding', 'padding' );

		$markerTitleColor = woolentorBlocks_generate_css( $settings, 'markerTitleColor', 'color' );
		$markerTitleSize = woolentorBlocks_generate_css( $settings, 'markerTitleSize', 'font-size' );
		$markerTitleMargin = woolentorBlocks_Dimention_Control( $settings, 'markerTitleMargin', 'margin' );

		$markerDescriptionColor = woolentorBlocks_generate_css( $settings, 'markerDescriptionColor', 'color' );
		$markerDescriptionSize = woolentorBlocks_generate_css( $settings, 'markerDescriptionSize', 'font-size' );
		$markerDescriptionMargin = woolentorBlocks_Dimention_Control( $settings, 'markerDescriptionMargin', 'margin' );

		$all_styles = "
			.{$uniqClass} .wlb_image_pointer::before{
				{$markerColor}
			}
			.{$uniqClass} .wlb_image_pointer{
				{$markerBGColor}
				{$markerBorderColor}
				{$markerBorderRadius}
				{$markerPadding}
			}
			.{$uniqClass} .wlb_image_pointer .wlb_pointer_box{
				{$markerContentBGColor}
				{$markerContentBorderRadius}
				{$markerContentPadding}
			}
			.{$uniqClass} .wlb_image_pointer .wlb_pointer_box h4{
				{$markerTitleColor}
				{$markerTitleSize}
				{$markerTitleMargin}
			}
			.{$uniqClass} .wlb_image_pointer .wlb_pointer_box p{
				{$markerDescriptionColor}
				{$markerDescriptionSize}
				{$markerDescriptionMargin}
			}
		";

		ob_start();
		?>
			<style><?php echo $all_styles; ?></style>
			<div class="<?php echo implode(' ', $classes ); ?>" style="<?php echo $background_image; ?> position:relative;">

                <?php
                    foreach ( $settings['markerList'] as $item ):
						
						$horizontalPos = !empty( $item['horizontal'] ) ? 'left:'.$item['horizontal'].'%;' : 'left:50%;';
						$verticlePos = !empty( $item['verticle'] ) ? 'top:'.$item['verticle'].'%;' : '15%;';

                    ?>
                        <div class="wlb_image_pointer" style="<?php echo $horizontalPos.$verticlePos; ?>">
                            <div class="wlb_pointer_box">
                                <?php
                                    if( !empty( $item['title'] ) ){
                                        echo '<h4>'.esc_html__( $item['title'], 'woolentor' ).'</h4>';
                                    }
                                    if( !empty( $item['content'] ) ){
                                        echo '<p>'.esc_html__( $item['content'], 'woolentor' ).'</p>';
                                    }
                                ?>
                            </div>
                        </div>
						
                    <?php
                    endforeach;
                ?>
                    
            </div>

		<?php
		return ob_get_clean();
	}

}
WooLentorBlocks_Image_Marker::instance();