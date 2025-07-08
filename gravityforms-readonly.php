<?php
/*
Plugin Name: Gravity Forms Read Only Fields
Description: Adds a universal 'Read Only' option to all Gravity Forms fields, with a tooltip warning and frontend enforcement for all field types.
Version: 1.0.0
Author: Faisal Ahammad
License: GPLv3 or later
 */

if ( !defined( 'ABSPATH' ) ) {
 exit;
}

// Supported field types for Read Only
function gravityforms_readonly_supported_field_types()
{
 return array(
  'text', 'textarea', 'select', 'checkbox', 'radio', 'number', 'email', 'website', 'date', 'time', 'phone', 'address', 'name', 'list', 'multiselect', 'password', 'hidden', // hidden for completeness, but will be excluded below
 );
}

// Add the Read Only checkbox to supported fields in the field settings panel
add_action( 'gform_field_standard_settings', function ( $position, $form_id ) {
 if ( $position == 20 ) {
  ?>
        <script>
        jQuery(document).on('gform_load_field_settings', function(event, field, form){
            // Only show for supported types and not for unsupported types
            var gfro_supported_types = [ 'text', 'textarea', 'select', 'checkbox', 'radio', 'number', 'email', 'website', 'date', 'time', 'phone', 'address', 'name', 'list', 'multiselect', 'password' ];
            var gfro_unsupported_types = [ 'hidden', 'html', 'captcha', 'page', 'section', 'form', 'fileupload' ];
            if (gfro_supported_types.indexOf(field.type) !== -1 && gfro_unsupported_types.indexOf(field.type) === -1) {
                jQuery('.readonly_setting').show();
            } else {
                jQuery('.readonly_setting').hide();
            }
            jQuery('#field_gf_readonly_enable').prop('checked', field.gf_readonly_enable == true);
        });
        </script>
        <li class="readonly_setting field_setting" style="display:none;">
            <input type="checkbox" id="field_gf_readonly_enable" onclick="SetFieldProperty('gf_readonly_enable', this.checked);">
            <label for="field_gf_readonly_enable" class="inline">Read Only
                <button onclick="return false;" onkeypress="return false;" class="gf_tooltip tooltip tooltip_form_field_readonly" aria-label="<?php esc_attr_e( 'Read Only: When enabled, this field will be read-only on the frontend. Recommendation: Do not make this field required or use merge tags as the default value, as this can cause required field errors.', 'gravityforms-readonly' ); ?>">
                    <i class="gform-icon gform-icon--question-mark" aria-hidden="true"></i>
                </button>
            </label>
        </li>
        <?php
}
}, 10, 2 );

// Register the tooltip text
add_filter( 'gform_tooltips', function ( $tooltips ) {
 $tooltips[ 'form_field_readonly' ] = '<strong>Read Only</strong> When enabled, this field will be read-only on the frontend. <br><b>Recommendation:</b> Do not make this field required or use merge tags as the default value, as this can cause required field errors.';

 return $tooltips;
} );

// Add readonly/disabled attribute and class to supported fields on the frontend using gform_field_content
add_filter( 'gform_field_content', function ( $content, $field, $value, $lead_id, $form_id ) {
 if ( !rgar( $field, 'gf_readonly_enable' ) ) {
  return $content;
 }
 $supported = gravityforms_readonly_supported_field_types();
 if ( !in_array( $field->type, $supported ) ) {
  return $content;
 }
 // Don't apply to HTML, Section, Hidden
 if ( in_array( $field->type, array( 'html', 'section', 'hidden' ) ) ) {
  return $content;
 }
 // Add readonly/disabled and class to the first input/select/textarea in the field content
 $content = preg_replace_callback(
  '/(<input[^>]*type=["\']?(text|email|url|number|tel|date|time|password)[^>]*>)/i',
  function ( $matches ) {
   $input = $matches[ 1 ];
   // Add class, readonly, and disabled if not already present
   $input = preg_replace( '/class=["\']([^"\']*)["\']/', 'class="$1 gf_readonly"', $input, 1, $classCount );
   if ( !$classCount ) {
    $input = preg_replace( '/(<input)/', '$1 class="gf_readonly"', $input, 1 );
   }
   if ( strpos( $input, 'readonly' ) === false ) {
    $input = str_replace( '<input', '<input readonly', $input );
   }
   if ( strpos( $input, 'disabled' ) === false ) {
    $input = str_replace( '<input', '<input disabled', $input );
   }

   return $input;
  },
  $content
 );
 // For textarea
 $content = preg_replace_callback(
  '/(<textarea[^>]*>)/i',
  function ( $matches ) {
   $input = $matches[ 1 ];
   $input = preg_replace( '/class=["\']([^"\']*)["\']/', 'class="$1 gf_readonly"', $input, 1, $classCount );
   if ( !$classCount ) {
    $input = preg_replace( '/(<textarea)/', '$1 class="gf_readonly"', $input, 1 );
   }
   if ( strpos( $input, 'readonly' ) === false ) {
    $input = str_replace( '<textarea', '<textarea readonly', $input );
   }
   if ( strpos( $input, 'disabled' ) === false ) {
    $input = str_replace( '<textarea', '<textarea disabled', $input );
   }

   return $input;
  },
  $content
 );
 // For select
 $content = preg_replace_callback(
  '/(<select[^>]*>)/i',
  function ( $matches ) {
   $input = $matches[ 1 ];
   $input = preg_replace( '/class=["\']([^"\']*)["\']/', 'class="$1 gf_readonly"', $input, 1, $classCount );
   if ( !$classCount ) {
    $input = preg_replace( '/(<select)/', '$1 class="gf_readonly"', $input, 1 );
   }
   if ( strpos( $input, 'disabled' ) === false ) {
    $input = str_replace( '<select', '<select disabled', $input );
   }

   return $input;
  },
  $content
 );
 // For checkboxes and radios
 $content = preg_replace_callback(
  '/(<input[^>]*type=["\']?(checkbox|radio)[^>]*>)/i',
  function ( $matches ) {
   $input = $matches[ 1 ];
   $input = preg_replace( '/class=["\']([^"\']*)["\']/', 'class="$1 gf_readonly"', $input, 1, $classCount );
   if ( !$classCount ) {
    $input = preg_replace( '/(<input)/', '$1 class="gf_readonly"', $input, 1 );
   }
   if ( strpos( $input, 'disabled' ) === false ) {
    $input = str_replace( '<input', '<input disabled', $input );
   }

   return $input;
  },
  $content
 );

 return $content;
}, 10, 5 );

// Add CSS for .gf_readonly fields only
add_action( 'wp_head', function () {
 echo '<style>.gform_wrapper .gf_readonly { opacity: 0.6 !important; pointer-events: none !important; background: #f5f5f5 !important; }</style>';
} );
add_action( 'admin_head', function () {
 echo '<style>.gform_wrapper .gf_readonly { opacity: 0.6 !important; pointer-events: none !important; background: #f5f5f5 !important; }</style>';
} );