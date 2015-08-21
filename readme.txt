=== UniPress API ===
Contributors: layotte, pericson, endocreative
Tags: mobile, app, api
Requires at least: 3.3
Tested up to: 4.3
Stable tag: 1.3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Put your publication in your reader’s pocket.

== Description ==

Put your publication in your reader’s pocket.

UniPress is the easiest way to launch iOS and Android apps for your WordPress readers. UniPress allows you to connect your...

1. WordPress blog posts
2. IssueM WordPress issues and
3. Leaky Paywall metered paywall subscriptions for WordPress

...to native iOS and Android apps.

Now news and magazine publishers can easily run their website and apps in one WordPress dashboard.

Check out our pricing plans at (GetUnipress.com)[https://getunipress.com/pricing-sign-up/].

See how UniPress works:
https://www.youtube.com/watch?v=dpSfiJdLYc8

Developers can use this plugin to make custom apps for UniPress. If you are not a developer, please see our website for more information about creating an app with WordPress.
This plugin is not an app-creator in itself, it serves as the core for all app development with UniPress.

[Follow the development of the UniPress API Plugin on Bitbucket](https://bitbucket.org/unipress/unipress-api).

Have questions? [Contact us here](https://getunipress.com/contact-us/).

App Features
* Publish WordPress blog posts and categories
* Publish IssueM WordPress issues
* Integrates with Leaky Paywall, a metered paywall for WordPress
* Design: Use your own image or video for your splash screen intro, custom header image and UI colors
* Send unlimited scheduled and manual push notifications 
* Customizable app menu with pages, categories, tags, current issue, past issue, login button, subscribe button, custom taxonomies and links
* User preferences for push notifications (by category or device type)
* Support for posts, pages, categories, custom post types and custom fields
* Advertising with Google DFP
* Banner and native ads supported
* Offline access to your content - background downloading
* Support for Google Analytics with complete app usage statistics
* Custom HTML/PHP/CSS in the article contents (add all the functionality you need)
* WordPress, Facebook and Disqus comments supported
* Social Sharing features (one tap to share to Facebook, Twitter, Whatsapp, email and more)
* Support for in Youtube video
* Soundcloud support
* CDN support to reduce bandwidth
* Support for image galleries
* Multi Language apps
* Build and App Submission service to App Store and Google Play (we do all for you)
* Publish with your own Developer accounts on Google Play and App Store

== Installation ==

1. Upload the entire `unipress-api` folder to your `/wp-content/plugins/` folder.
1. Go to the 'Plugins' page in the menu and activate the plugin.

== FAQ ==

**What are the minimum requirements for UniPress API?**
You must have:
* WordPress 3.3 or later
* PHP 5

**How is UniPress API Licensed?**
* UniPress API is GPL.


== Changelog ==
= 1.3.1 =
* Adding filters to attachment meta, attachments, and new arg to featured image filter for get content list and get article API calls 

= 1.3.0 =
* Allow get-article call to support article-url if article-id is not supplied
* Adding title, description, alt, and caption to image_meta values
* Adding support for anonymous and unregistered comments

= 1.2.5 =
* Adding post_url to post variable
* Fixing bug when offset is set and posts_per_page isn't
* Rekeying array values in posts get_content_list return

= 1.2.4 =
* Change how we remove posts that are in excluded cats

= 1.2.3 =
* Adding post date to push notification

= 1.2.2 =
* Fixing bug with post not being removed on an excluded category match

= 1.2.1 =
* Fixing typo in attachment baseurl

= 1.2.0 =
* Changed how tokens are stored in the database
* Added Custom JS Area
* Added CDN Support
* Added Excerpt/Content customizations
* Added ability to exclude certain categories from UniPress

= 1.1.0 =
Adding author arg to get-content-list

= 1.0.0 =
* Initial Release
