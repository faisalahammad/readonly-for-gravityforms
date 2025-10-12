<?php
/**
 * Plugin Name:       Read Only Fields for Gravity Forms
 * Description:       Adds a universal 'Read Only' option to all Gravity Forms fields, with a tooltip warning and frontend enforcement for all field types.
 * Version:           1.0.0
 * Author:            Faisal Ahammad
 * Author URI:        https://faisalahammad.com
 * Plugin URI:        https://github.com/faisalahammad/readonly-for-gravityforms
 * Text Domain:       readonly-for-gravityforms
 * Domain Path:       /languages
 * Requires PHP:      7.4
 * Requires at least: 5.6
 * Tested up to:      6.8
 * License:           GPLv3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) {
 exit;
}

if ( !defined( 'READONLY_FOR_GRAVITYFORMS_VERSION' ) ) {
 define( 'READONLY_FOR_GRAVITYFORMS_VERSION', '1.0.0' );
}

// Supported field types for Read Only
function readonly_for_gravityforms_supported_field_types()
{
 return array(
  'text', 'textarea', 'select', 'checkbox', 'radio', 'number', 'email', 'website', 'date', 'time', 'phone', 'address', 'name', 'list', 'multiselect', 'password', 'hidden', // hidden for completeness, but will be excluded below
 );
}

// Add the Read Only checkbox to supported fields in the field settings panel
add_action( 'gform_field_standard_settings', function ( $position, $form_id ) {
 if ( $position == 20 ) {
  ?>
        <li class="readonly_setting field_setting" style="display:none;">
            <input type="checkbox" id="field_gf_readonly_enable" onclick="SetFieldProperty('gf_readonly_enable', this.checked);">
            <label for="field_gf_readonly_enable" class="inline">Read Only
                <button onclick="return false;" onkeypress="return false;" class="gf_tooltip tooltip tooltip_form_field_readonly" aria-label="<?php esc_attr_e( 'Read Only: When enabled, this field will be read-only on the frontend. Recommendation: Do not make this field required or use merge tags as the default value, as this can cause required field errors.', 'readonly-for-gravityforms' ); ?>">
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
 $supported = readonly_for_gravityforms_supported_field_types();
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

/**
 * Enqueue shared style used for read-only fields.
 */
function readonly_for_gravityforms_enqueue_style()
{
 $handle = 'readonly-for-gravityforms-style';
 $src = plugins_url( 'assets/css/readonly-for-gravityforms.css', __FILE__ );

 if ( !wp_style_is( $handle, 'registered' ) ) {
  wp_register_style( $handle, $src, array(), READONLY_FOR_GRAVITYFORMS_VERSION );
 }

 wp_enqueue_style( $handle );
}

add_action(
 'wp_enqueue_scripts',
 function () {
  readonly_for_gravityforms_enqueue_style();
 }
);

/**
 * Determine whether we are currently on a Gravity Forms admin screen.
 *
 * @return bool
 */
function readonly_for_gravityforms_is_gravityforms_admin_page()
{
 if ( empty( $_GET[ 'page' ] ) ) {
  return false;
 }

 $page = sanitize_key( wp_unslash( $_GET[ 'page' ] ) );

 return strpos( $page, 'gf_' ) === 0;
}

add_action(
 'admin_enqueue_scripts',
 function () {
  if ( !readonly_for_gravityforms_is_gravityforms_admin_page() ) {
   return;
  }

  readonly_for_gravityforms_enqueue_style();

  $handle = 'readonly-for-gravityforms-admin';
  $src = plugins_url( 'assets/js/readonly-for-gravityforms-admin.js', __FILE__ );

  if ( !wp_script_is( $handle, 'registered' ) ) {
   wp_register_script( $handle, $src, array( 'jquery' ), READONLY_FOR_GRAVITYFORMS_VERSION, true );
  }

  wp_enqueue_script( $handle );
 }
);
