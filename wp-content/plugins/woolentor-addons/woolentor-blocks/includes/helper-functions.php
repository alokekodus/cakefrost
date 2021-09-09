<?php
// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit();

// Generate CSS
function woolentorBlocks_generate_css( $settings, $attribute, $css_attr, $unit = '', $important = '' ){

    $value = !empty( $settings[$attribute] ) ? $settings[$attribute] : '';

    if( !empty( $value ) && 'NaN' !== $value ){
        $css_attr .= ":{$value}{$unit}";
        return $css_attr."{$important};";
    }else{
        return false;
    }

}

// Geterante Dimension
function woolentorBlocks_Dimention_Control( $settings, $attribute, $css_attr, $important = '' ){
    $dimensions = !empty( $settings[$attribute] ) ? $settings[$attribute] : array();

    if( isset( $dimensions['top'] ) || isset( $dimensions['right'] ) || isset( $dimensions['bottom'] ) || isset( $dimensions['left'] ) ){
        $unit = empty( $dimensions['unit'] ) ? 'px' : $dimensions['unit'];

        $top = ( $dimensions['top'] !== '' ) ? $dimensions['top'].$unit : null;
        $right = ( $dimensions['right'] !== '' ) ? $dimensions['right'].$unit : null;
        $bottom = ( $dimensions['bottom'] !== '' ) ? $dimensions['bottom'].$unit : null;
        $left = ( $dimensions['left'] !== '' ) ? $dimensions['left'].$unit : null;
        $css_dimension = ( ($top != null) || ($right !=null) || ($bottom != null) || ($left != '') ) ? ( $css_attr.":{$top} {$right} {$bottom} {$left};" ) : '';

        return $css_dimension;

    }else{
        return false;
    }

}

// Background Image control
function woolentorBlocks_Background_Control( $settings, $attribute ){
    $background_property = !empty( $settings[$attribute] ) ? $settings[$attribute] : array();
    
    if( !empty( $background_property['imageId'] ) ){
        $image_url = wp_get_attachment_image_src( $background_property['imageId'], 'full' );
        $background_css = "background-image:url({$image_url[0]});";

        if( !empty( $background_property['position'] ) ){
            $background_css .= "background-position:{$background_property['position']};";
        }
        if( !empty( $background_property['attachment'] ) ){
            $background_css .= "background-attachment:{$background_property['attachment']};";
        }
        if( !empty( $background_property['repeat'] ) ){
            $background_css .= "background-repeat:{$background_property['repeat']};";
        }
        if( !empty( $background_property['size'] ) ){
            $background_css .= "background-size:{$background_property['size']};";
        }

        return  $background_css;

    }else{
        return false;
    }
    
    
}