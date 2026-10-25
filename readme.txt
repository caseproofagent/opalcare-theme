=== Opal Care ===
Contributors: caseproofagent
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 8.0
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple block theme for opalcarewp.com in Opal's pen-and-watercolor ink-line style.

== Description ==

Opal Care is a full site editing theme. Colours, fonts and spaces are settings in theme.json, and every word and picture on the home page is a block pattern, so a change is one setting or one edit in the editor, with no code.

Home, a page, the blog, a post, search and the 404 page each have their own template. The home page is built from four patterns: Hero, A real check, Questions owners bring her, and Closing call to action.

To add the starter pages (Home, How it works, Pricing, Privacy, Terms, Contact) and the menu, run `wp eval-file wp-content/themes/opal-care/bin/seed.php` on the site. It can be run again safely.

== External services ==

The AI readiness check block (blocks/readiness-check) sends the website address a visitor types to Opal Care's free check at https://opalagent.app/api/readiness, only when the visitor presses "Check my site". Opal Care reads that site's robots.txt, sitemap, title and description, security headers, certificate and speed, and answers with the report. It keeps one count of checks per day and does not store the address. Terms: https://opalagent.app/terms/. Privacy: https://opalagent.app/privacy/. Nothing is sent until the visitor presses the button, and the block is only on pages where it is placed.

== Copyright ==

Opal Care WordPress Theme, (C) 2026 Caseproof, LLC.
Opal Care is distributed under the terms of the GNU General Public License v2 or later.

This theme bundles the following resources:

* Fraunces (assets/fonts/fraunces.woff2, fraunces-italic.woff2), (C) The Fraunces Project Authors, SIL Open Font License 1.1, https://github.com/undercasetype/Fraunces
* DM Sans (assets/fonts/dm-sans.woff2), (C) The DM Sans Project Authors, SIL Open Font License 1.1, https://github.com/googlefonts/dm-fonts
* Opal illustration (assets/images/opal.webp) and paper texture (assets/images/paper.webp), (C) Caseproof, LLC, from Opal's style guide. Distributed under the GNU General Public License v2 or later with this theme.

== Changelog ==

= 1.1.0 =
* The AI readiness check block, and a Home section that uses it.

= 1.0.6 =
* The 44px menu button applies on phones only; the desktop menu stays on the logo row.

= 1.0.5 =
* The logo link is a 44px tap target.

= 1.0.4 =
* The favicon is the still opal with its ink outline; the mobile menu button is 44px.

= 1.0.3 =
* The logo is the animated opal, drawn in place of the Site Logo image while it is the default.

= 1.0.2 =
* The footer carries the Gravatar verification link as a small icon (rel="me").

= 1.0.1 =
* Numbered sections: number beside the heading.

= 1.0.0 =
* First release.
