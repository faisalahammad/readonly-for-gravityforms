=== Gravity Forms Read Only Fields ===
Contributors: faisalahammad
Tags: gravity forms, readonly, field, admin, frontend, plugin
Requires at least: 5.2
Tested up to: 6.8.1
Stable tag: 1.0.0
Requires PHP: 7.2
License: GPLv3 or later

== Description ==

Easily add a universal "Read Only" option to any Gravity Forms field. This plugin adds a checkbox to every supported field in the Gravity Forms editor, allowing you to make any field read-only on the frontend. Includes a tooltip warning to avoid using required or merge-tag default values with read-only fields.

== Features ==

* Adds a "Read Only" checkbox to all supported Gravity Forms fields in the form editor
* Shows a tooltip warning about not using "Required" or merge tags as default value
* Makes any supported field type (text, textarea, select, checkbox, radio, etc.) read-only/disabled on the frontend if enabled
* Excludes unsupported field types (hidden, html, captcha, page, section, form, fileupload) from the Read Only option
* Optimized for all supported field types and easy to maintain
* Adds subtle styling to read-only fields for better user experience
* Fully translatable and ready for localization

== Installation ==

1. Install and activate this plugin.
2. Make sure Gravity Forms is installed and activated.
3. Edit any Gravity Form. In the field settings, check the "Read Only" box to make a field read-only.
4. The field will be read-only/disabled on the frontend for all users.

== Usage ==

* Only supported field types will show the "Read Only" option.
* Read-only fields are visually styled and cannot be edited by users on the frontend.

== Frequently Asked Questions ==

= Why can't I make some fields read-only? =
The plugin intentionally excludes certain field types (hidden, html, captcha, page, section, form, fileupload) from the Read Only option, as these are not user-editable or not suitable for read-only status.

= Can I use this with required fields? =
It is not recommended to use the "Required" option or merge tags as the default value for read-only fields. This may cause required field errors if the value is empty.

== Changelog ==

= 1.0.0 =
* Initial release

== License ==
GPLv3 or later

== Languages/Localization ==
* Text domain: gravityforms-readonly
* Domain path: /languages

== Requirements ==
* WordPress 5.2 or higher
* PHP 7.2 or higher
* Gravity Forms plugin