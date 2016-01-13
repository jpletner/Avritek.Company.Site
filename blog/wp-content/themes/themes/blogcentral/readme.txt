=== BlogCentral ===
Author: 4bzthemes
Author url: http://4bzthemes.com/
Tags: black, blue, white, right-sidebar, one-column, two-columns, three-columns, four-columns, responsive-layout, custom-colors, custom-menu, featured-images, post-formats, sticky-post, editor-style, theme-options, threaded-comments, full-width-template, translation-ready
Requires at least: 3.8
Tested up to: 4.2.4
Stable tag: 1.1.3

A blog-central WordPress theme where the blog is foremost.

== Description ==

BlogCentral is a responsive wordpress theme that focuses on the blog. It is scalable, retina ready, highly customizable, SEO friendly, and translation ready. It comes with 3 predefined demos, 5 color schemes, unlimited color options, 3 page header layouts, 3 blog posts listing layouts, and much more. It includes advanced features for the 4bzCore plugin, which is recommended and is available for download from wordpress.org.

You can find [docs](http://4bzthemes.com/knowledgebase/), and more detailed information about BlogCentral on [4bzthemes.com](http://4bzthemes.com/theme/blogcentral).

If you have any questions about BlogCentral, consult the following in this order:

1. [Docs](http://4bzthemes.com/knowledgebase/
3. [Support Forum](https://wordpress.org/support/theme/blogcentral)

= Recommended Plugins =

The following are recommended plugins.

* [4bzCore](http://wordpress.org/extend/plugins/4bzcore/) - This plugin contains all of the shortcodes, widgets, shortcode builder, multiple featured images, video and audio embed input, and user profile data input supported by the BlogCentral theme.

= Translators =

BlogCentral needs translations. If you have created or updated a language pack, you can send [gettext PO and MO files](http://codex.wordpress.org/Translating_WordPress) to [4bzthemes](http://4bzthemes.com/about/) so that it can be bundled into BlogCentral.

== Installation ==

First you will need to download the theme files from WordPress.org. Once the files are obtained, you can install either through wordpress or ftp.

WordPress Installation

On the wordpress admin screen, go to Appearance->Themes, click on the Add New button, then click the Upload Theme button. Navigate to the location you saved the BlogCentral file, which should be in zip format. Click on the file, then click on the Install Now button. After the theme is uploaded, hover over the BlogCentral theme and click the activate button.

FTP Installation

After you login to your host, upload the folder in the extracted BlogCentral folder to /wp-content/themes. Then in the wordpress admin panel, go to Appearance->Themes, activate BlogCentral.

== Frequently Asked Questions ==

If you have any questions about BlogCentral, consult the following in this order:

1. [Docs](http://4bzthemes.com/knowledgebase/)
2. [Support Forum](https://wordpress.org/support/theme/blogcentral)

= Do the theme options apply to page templates? =

There are site-wide options, such as the ones for the header and footer, that will apply to page templates. The posts listing options will affect the appearance of the posts listing in the main area on all pages that display a posts listing in a div with id #main-area, eg. index, archive, author, search, category tags pages, etc..
The theme options are not guaranteed to work with custom page templates not provided with the theme.

= I've changed the options on the theme options page, but I don't see the changes on the front end. What is wrong? =

When choosing a demo, you must click the "Change Demo" button because this will save a copy of the demo options as the theme options. Also, whenever a demo is viewed on the front end, its options will be the active theme options, until the home page, or another demo is chosen on the front end, for example from the main navigation menu.
If you change the theme options on the admin side, and do not see these changes on the front end, you will have to navigate to the home page, effectively changing the options from the previously viewed demo, or the previously saved options, to the newly saved options from the theme options page, and always remember to click the 'Save Options' button to save your changes.

= Why don't I see the changes I've made on the theme options page on the demo pages? =

The page templates for the demos serve as a demonstration of the preset options for each demo. BlogCentral does not provide a way to change the options for the demos, they can only be changed by modifying the source code. The options saved on the theme options page do not apply to the demo pages.

= Report a bug =

You can report any bugs through the [Support Forum](https://wordpress.org/support/theme/blogcentral).

== Changelog ==
= 1.1.3 =
- minor code change.

= 1.1.2 =
- Fixed: blogcentral_choose_demo function.
- Fixed: linkedin share post link.
- Fixed: post quote layout2 to show post meta and social share on single post page.
- Fixed: search results page.
- Fixed: alternate color option.
- Changed: blogcentral_register_widgets_areas function, before_title & after_title options.
- Changed: $blogcentral_initial variable.
- New: option- no color scheme.
- Changed: blogcentral_enqueue_scripts function to only enqueue 4bzCore specific scripts if the plugin isset.
- Changed: blogcentral_construct_wrapper function.
- Changed: content-gallery.php file.
- Changed: single.php file. Do not automatically show post meta data.
- Changed: layout-wrapper-begin.php file.
- Updated: translation files.
- Updated: minor css changes and moved 4bzCore specific style from style.css to 4bzcore.css.
- Updated: style.css table of contents.
- Updated: responsive.css breakpoints.

= 1.1.1 =
- Updated: logo links to the homepage.
- Fixed: change demo functionality.
- Fixed: index.php blogcentral_sticky.
- Fixed: precontent/page header background image inline css.
- Fixed: blogcentral_validate_options function, added logo and fav_icon to check_url array.
- Fixed: page title display if there are any active widgets in the precontent/page header area.
- Fixed: do not display website label if author do not have a website saved.
- Fixed: post meta wrap.
- Fixed: search.php.
- Updated: blogcentral_enqueue_inline_css function.
- Fixed: blogcentral_construct_wrapper_classes_styles function.
- Updated: theme options page, changed Single Page tab to Single Post Page.
- Updated: BLOGCENTRAL_THEME_DOCS constant variable.
- Updated: minor changes in help instructions.
- Deleted: outdated code.
- Updated: minor style changes.
- Fixed: missing attributes in construction of shortcodes in admin.js.
- Fixed: changed fourbzcore_build_slideshow_specific_opts to blogcentral_build_slideshow_specific_opts.
- Fixed: slideshow code in front-end.js.
- Fixed: admin.js code for 4bzcore override functions.
- Updated: support forum to wordpress.org.

= 1.1.0 =
- Updated: theme uri.
- New: added header top widget area.
- New: added favicon option.
- New: added show_search option.
- New: added show_color_chooser option and updated all files that display the color chooser to check for this option.
- Deleted: remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' )
- Fixed: blog description not displayed on the index page, even if option is set.
- New: added filters and functions for the 4bzCore plugin.
- Updated: blogcentral_get_option_initial function with added initial options.
- Updated: custom_css value for demos.
- Updated: default user image for demos.- Updated: header.php file to display a search form, if the show_search option is set and no active widgets in the area.
- Updated: demos with default contact info.
- Updated: blogcentral_admin_scripts function to include the 4bzCore admin script before loading the blogcentral admin script, only if the plugin is installed and activated.
- Updated: changed BlogCentralPro to BlogCentral Pro.
- Updated: theme options page including: contact information option panel and its help tab, header panel, image for post layouts.
- Updated: blogcentral_enqueue_scripts function.
- Updated: blogcentral_admin_scripts function.
- Changed: background image for the slide templates.
- Updated: blogcentral_comment function to retrieve the user meta from the 4bzCore plugin, and other minor changes.
- Fixed: blogcentral_construct_wrapper_classes_styles function, tagline border to display default border if selected.
- New: blogcentral_get_styles function.
- Fixed: content-quote.php file to use the $media variable in the foreach statement.
- Deleted: dynamic style in the content-video file.
- Updated: content.php file to not check for unsupported options.
- Updated: contact-form template file.
- Updated: translation files.
- New: added the data-width attribute in facebook-comments template file.
- Fixed: progressbars template file.
- Updated: changed <h1> tag to <h2> in slide-template-1.php
- Updated: comments in the page-template-about-me file.
- New: blogcentral_add_slide function in admin.js.
- Changed: custom_uploader variable to blogcentral_custom_uploader.
- New: added shortcode builder functions in admin.js to override the functions provided by the 4bzCore plugin.
- Changed: in admin.js jquery selector $( '.blogcentral-color-field' ).wpColorPicker(), removed .theme-options.
- Updated: editor-style.css.
- Updated: minor css styles for header, navigation menu, .layout1.component .component-content-wrap, social-share and share-us, contact form, author-bio,
  progressbars, author-details, slide templates, contact form 7, contact info component, header-wrap h3
- Fixed: style for columns design.
- Updated: responsive.css- changed to display contact form component in one column, depending on the size of the screen.

= 1.0.4 =
- Approved and made live on wordpress.org

= 1.0.0 =
- Submitted for review

== Credits ==
* Image packaged with theme, source: unsplash.com, license information: Creative Commons Zero.