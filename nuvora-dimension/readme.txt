=== Nuvora Dimension ===
Contributors: meerab123
Requires at least: 5.9
Tested up to: 6.9
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: custom-background, custom-logo, theme-options, blog, portfolio

A cinematic one-page WordPress theme with animated slide-in panels, a bold hero header, and a live blog feed — built for portfolios, agencies, and personal brands.

== Description ==

Nuvora Dimension is a visually striking one-page WordPress theme inspired by the award-winning Dimension layout by HTML5 UP. It transforms your site into a rich, full-screen experience where content lives inside animated slide-in panels — smooth, elegant, and completely keyboard-accessible.

Whether you're building a personal portfolio, a freelance agency site, or a creative landing page, Nuvora Dimension gives you a polished, professional result with almost no setup effort.

**Key Features**

* Full-screen hero header with customizable site title, subtitle, and icon (FontAwesome 5)
* Custom logo support via the WordPress Customizer
* Animated slide-in content panels linked to your WordPress nav menu pages
* Live blog panel with AJAX-powered load-more pagination and sticky post support
* Social media icon links (Twitter/X, Facebook, Instagram, GitHub, LinkedIn)
* Custom background color and image, configurable from the Theme Options panel
* Accent color control for buttons and links
* Custom footer copyright text with link support
* Fully responsive — works beautifully on mobile, tablet, and desktop
* Keyboard-navigable and screen-reader-friendly close/back controls
* Translation-ready with full gettext support and a .pot file

**How Panels Work**

Nuvora Dimension uses your WordPress navigation menu to build the front page. Each menu item that points to a page becomes a slide-in panel. The page's featured image and content appear inside that panel. This means everything is managed with standard WordPress tools — no shortcodes, no page builders.

**Blog Panel**

Add a page called "Blog" to your navigation menu and it automatically becomes a live blog feed panel with AJAX-powered pagination, sticky post support, and category badges.

**Theme Options**

A dedicated settings page under Appearance → Dimension Options lets you control:
- Header title, subtitle, and FontAwesome icon
- Background color and background image
- Accent/highlight color
- Social profile URLs
- Footer copyright text

== Installation ==

**Automatic Installation (Recommended)**

1. Log in to your WordPress admin dashboard.
2. Go to Appearance → Themes → Add New.
3. Search for "Nuvora Dimension".
4. Click Install, then Activate.

**Manual Installation (ZIP upload)**

1. Download the theme ZIP file.
2. In your WordPress admin, go to Appearance → Themes → Add New → Upload Theme.
3. Choose the ZIP file and click Install Now.
4. After installation completes, click Activate.

**After Activation**

1. Go to Appearance → Menus and create a new menu.
2. Create pages for each section you want (e.g. About, Work, Contact).
3. Add those pages to your menu and assign it to the "Nuvora Dimension Header Nav" location.
4. Set your front page to a static page under Settings → Reading (select any page; the front-page template handles the display).
5. Visit Appearance → Dimension Options to customize your header, colors, social links, and footer.

== Frequently Asked Questions ==

= How do I create the slide-in panels? =

Each panel corresponds to a page in your WordPress navigation menu. Simply:
1. Create pages (e.g. About, Portfolio, Contact) under Pages → Add New.
2. Add content and optionally a featured image to each page.
3. Go to Appearance → Menus, add those pages, and assign the menu to the "Nuvora Dimension Header Nav" location.
The theme automatically renders each page as a slide-in panel on the front page.

= How do I set up the blog panel? =

Add a page with the slug "blog" (or title "Blog", "News", "Articles", or "Posts") to your navigation menu. Nuvora Dimension will automatically treat it as a live blog feed panel with pagination. No extra setup needed.

= Can I use a custom background image? =

Yes. Go to Appearance → Dimension Options → Background & Colors and use the image picker to upload or choose a background image from your Media Library.

= How do I add social media icons? =

Go to Appearance → Dimension Options → Social Links. Enter the full URL for any profiles you want to display (Twitter/X, Facebook, Instagram, GitHub, LinkedIn). Leave a field blank to hide that icon.

= How do I change the header icon? =

Go to Appearance → Dimension Options → Header & Identity and enter any FontAwesome 5 solid icon class (e.g. fa-rocket, fa-gem, fa-star). You can browse free icons at https://fontawesome.com/icons?d=gallery&s=solid&m=free

= Can I add a custom logo instead of an icon? =

Yes. Use Appearance → Customize → Site Identity to upload a custom logo. When a custom logo is set, it replaces the FontAwesome icon automatically.

= Is the theme translation-ready? =

Yes. All user-facing strings use gettext with the text domain nuvora-dimension. A .pot template file is included in the /languages/ folder for use with tools like Poedit or Loco Translate.

= How do I change the footer copyright text? =

Go to Appearance → Dimension Options → Footer. You can enter HTML links in the footer text field using the built-in link helper.

= Where do the X / close buttons on panels go? =

On the front page, the X button closes the panel and returns to the hero header. On standalone pages (single posts, pages opened directly), the X button navigates back to the site home page.

= Does this theme support the block editor (Gutenberg)? =

Yes. The theme supports core blocks, wide and full-width alignment, block styles, responsive embeds, and editor styles. The front page panels are powered by standard WordPress pages with full block editor support for their content.

= What WordPress and PHP versions does this theme require? =

The theme requires WordPress 5.9 or higher and PHP 8.0 or higher. It has been tested up to WordPress 6.9.

= Does the theme include a contact form? =

No. Contact form functionality is plugin territory. We recommend using a dedicated WordPress plugin such as Contact Form 7 (https://wordpress.org/plugins/contact-form-7/) or WPForms Lite (https://wordpress.org/plugins/wpforms-lite/).

== Technical Notes ==

**Template Hierarchy**

* front-page.php — Main one-page layout; renders the hero and all slide-in panels
* page.php — Used for standalone WordPress pages (e.g. Privacy Policy)
* single.php — Used for individual blog posts
* index.php — Fallback template for all other views
* comments.php — Comment thread template

**Scripts & Styles**

All scripts are loaded in the footer with the defer attribute for optimal performance. The following assets are included:

* main.css / main.js — Core Dimension layout engine (HTML5 UP, CCA 3.0)
* nuvora-dimension.css — Theme-specific styles and overrides
* nuvora-animations.css — Panel transition animations
* fontawesome-all.min.css — FontAwesome 5.15.4 icon library (local, woff2 only)
* browser.min.js / breakpoints.min.js / util.js — Supporting libraries (HTML5 UP, MIT)
* blog.js — AJAX blog panel pagination

Critical woff2 font files are preloaded via <link rel="preload"> for faster rendering.

**Navigation & Panel System**

The front page reads the assigned nav menu and renders each page link as a slide-in <article> element. The JavaScript in main.js intercepts hash-based navigation (#panel-id) to show and hide panels with animated transitions. No page reloads occur during panel navigation.

**Options Storage**

All theme options are stored as a single serialized array in the WordPress options table under the key nuvora_options, prefixed with the theme slug. Options are saved and retrieved using the standard WordPress Settings API (update_option / get_option).

**Accessibility**

* The close/back button on all panels is a semantic <a> element with an aria-label
* Social icon links include visible <span class="label"> text for screen readers
* The blog panel uses semantic <time datetime=""> for post dates
* Keyboard focus is returned correctly after panel transitions

**Browser Support**

Tested and supported in: Chrome, Firefox, Safari, Edge (all current versions). IE11 support is included via a flexbox min-height fix in main.js.

== Changelog ==

= 1.0.0 =
* Initial public release
* Full-screen hero header with FontAwesome icon and custom logo support
* Animated slide-in panel system driven by WordPress nav menus
* AJAX blog panel with sticky post support and load-more pagination
* Theme Options panel: Header, Background & Colors, Social Links, Navigation, Footer
* Custom background color and image support
* Accent color control
* Social media links: Twitter/X, Facebook, Instagram, GitHub, LinkedIn
* Keyboard-accessible close/back navigation on all panels and standalone pages
* Translation-ready with .pot file
* Responsive layout for mobile, tablet, and desktop
* WordPress 6.9 compatibility

== Upgrade Notice ==

= 1.0.0 =
Initial release. No upgrade steps required.

== Credits ==

= Design Base =
* Dimension by HTML5 UP
  Source: https://html5up.net/dimension
  Author: @ajlkn (https://html5up.net)
  License: Creative Commons Attribution 3.0 Unported (CCA 3.0)
  License URI: https://html5up.net/license

= Icon Library =
* Font Awesome Free 5.15.4
  Source: https://fontawesome.com
  License: Icons — CC BY 4.0 | Fonts — SIL OFL 1.1 | Code — MIT
  License URI: https://fontawesome.com/license/free

= JavaScript Libraries =
* browser.min.js, breakpoints.min.js, util.js
  Part of the HTML5 UP Dimension package
  License: MIT (https://html5up.net/license)

* jQuery
  Source: https://jquery.com
  License: MIT
  License URI: https://opensource.org/licenses/MIT

= Bundled Images =
The following demo images are sourced from HTML5 UP and are licensed under
Creative Commons Attribution 3.0 Unported (CCA 3.0):

* bg.jpg        — Background photograph, HTML5 UP, CCA 3.0
* overlay.png   — Background overlay texture, HTML5 UP, CCA 3.0
* pic01.jpg     — Demo panel image 1, HTML5 UP, CCA 3.0
* pic02.jpg     — Demo panel image 2, HTML5 UP, CCA 3.0
* pic03.jpg     — Demo panel image 3, HTML5 UP, CCA 3.0

CCA 3.0 License URI: https://creativecommons.org/licenses/by/3.0/