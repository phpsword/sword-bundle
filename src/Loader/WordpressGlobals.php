<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Loader;

final class WordpressGlobals
{
    public const GLOBALS = [
        '_links_add_base',
        '_links_add_target',
        '_menu_item_sort_prop',
        '_nav_menu_placeholder',
        '_new_bundled_files',
        '_old_files',
        '_parent_pages',
        '_registered_pages',
        '_updated_user_settings',
        '_wp_additional_image_sizes',
        '_wp_admin_css_colors',
        '_wp_default_headers',
        '_wp_deprecated_widgets_callbacks',
        '_wp_last_object_menu',
        '_wp_last_utility_menu',
        '_wp_menu_nopriv',
        '_wp_nav_menu_max_depth',
        '_wp_post_type_features',
        '_wp_real_parent_file',
        '_wp_registered_nav_menus',
        '_wp_sidebars_widgets',
        '_wp_submenu_nopriv',
        '_wp_suspend_cache_invalidation',
        '_wp_theme_features',
        '_wp_using_ext_object_cache',
        'action',
        'active_signup',
        'admin_body_class',
        'admin_page_hooks',
        'all_links',
        'allowedentitynames',
        'allowedposttags',
        'allowedtags',
        'auth_secure_cookie',
        'authordata',
        'avail_post_mime_types',
        'avail_post_stati',
        'blog_id',
        'blog_title',
        'blogname',
        'cat',
        'cat_id',
        'charset_collate',
        'comment',
        'comment_alt',
        'comment_depth',
        'comment_status',
        'comment_thread_alt',
        'comment_type',
        'comments',
        'compress_css',
        'compress_scripts',
        'concatenate_scripts',
        'current_screen',
        'current_site',
        'current_user',
        'currentcat',
        'currentday',
        'currentmonth',
        'custom_background',
        'custom_image_header',
        'default_menu_order',
        'descriptions',
        'domain',
        'editor_styles',
        'error',
        'errors',
        'EZSQL_ERROR',
        'feeds',
        'GETID3_ERRORARRAY',
        'hook_suffix',
        'HTTP_RAW_POST_DATA',
        'id',
        'in_comment_loop',
        'interim_login',
        'is_apache',
        'is_chrome',
        'is_gecko',
        'is_IE',
        'is_IIS',
        'is_iis7',
        'is_macIE',
        'is_NS4',
        'is_opera',
        'is_safari',
        'is_winIE',
        'l10n',
        'link',
        'link_id',
        'locale',
        'locked_post_status',
        'lost',
        'm',
        'map',
        'menu',
        'menu_order',
        'merged_filters',
        'mode',
        'monthnum',
        'more',
        'multipage',
        'names',
        'nav_menu_selected_id',
        'new_whitelist_options',
        'numpages',
        'one_theme_location_no_menus',
        'opml',
        'option_page',
        'order',
        'orderby',
        'overridden_cpage',
        'page',
        'paged',
        'pagenow',
        'pages',
        'parent_file',
        'pass_allowed_html',
        'pass_allowed_protocols',
        'path',
        'per_page',
        'PHP_SELF',
        'phpmailer',
        'plugin_page',
        'plugins',
        'post',
        'post_default_category',
        'post_default_title',
        'post_ID',
        'post_id',
        'post_mime_types',
        'post_type',
        'post_type_object',
        'posts',
        'preview',
        'previouscat',
        'previousday',
        'previousweekday',
        'redir_tab',
        'required_mysql_version',
        'required_php_version',
        'rnd_value',
        'role',
        's',
        'search',
        'self',
        'shortcode_tags',
        'show_admin_bar',
        'sidebars_widgets',
        'status',
        'submenu',
        'submenu_file',
        'super_admins',
        'tab',
        'table_prefix',
        'tabs',
        'tag',
        'targets',
        'tax',
        'taxnow',
        'taxonomy',
        'term',
        'text_direction',
        'theme_field_defaults',
        'themes_allowedtags',
        'template',
        'timeend',
        'timestart',
        'tinymce_version',
        'title',
        'totals',
        'type',
        'typenow',
        'updated_timestamp',
        'upgrading',
        'urls',
        'user_email',
        'user_ID',
        'user_id',
        'user_identity',
        'user_level',
        'user_login',
        'user_url',
        'userdata',
        'usersearch',
        'whitelist_options',
        'withcomments',
        'wp',
        'wp_actions',
        'wp_admin_bar',
        'wp_cockneyreplace',
        'wp_current_db_version',
        'wp_current_filter',
        'wp_customize',
        'wp_dashboard_control_callbacks',
        'wp_db_version',
        'wp_did_header',
        'wp_embed',
        'wp_file_descriptions',
        'wp_filesystem',
        'wp_filter',
        'wp_hasher',
        'wp_header_to_desc',
        'wp_http_referer',
        'wp_importers',
        'wp_json',
        'wp_list_table',
        'wp_local_package',
        'wp_locale',
        'wp_meta_boxes',
        'wp_object_cache',
        'wp_plugin_paths',
        'wp_post_statuses',
        'wp_post_types',
        'wp_queries',
        'wp_query',
        'wp_registered_sidebars',
        'wp_registered_widget_controls',
        'wp_registered_widget_updates',
        'wp_registered_widgets',
        'wp_rewrite',
        'wp_rich_edit',
        'wp_rich_edit_exists',
        'wp_roles',
        'wp_scripts',
        'wp_settings_errors',
        'wp_settings_fields',
        'wp_settings_sections',
        'wp_smiliessearch',
        'wp_styles',
        'wp_taxonomies',
        'wp_the_query',
        'wp_theme_directories',
        'wp_themes',
        'wp_user_roles',
        'wp_version',
        'wp_widget_factory',
        'wp_xmlrpc_server',
        'wpcommentsjavascript',
        'wpcommentspopupfile',
        'wpdb',
        'wpsmiliestrans',
        'year',
    ];
}
