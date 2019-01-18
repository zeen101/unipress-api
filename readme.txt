=== UniPress API ===
Contributors: zeen101, layotte, pericson, endocreative
Tags: mobile, app, api
Requires at least: 3.3
Tested up to: 4.9
Stable tag: 1.18.4
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

Check out our pricing plans at [GetUniPress.com](https://getunipress.com/pricing-sign-up/).

See how UniPress works:
[youtube https://www.youtube.com/watch?v=dpSfiJdLYc]

Developers can use this plugin to make custom apps for UniPress. If you are not a developer, please see our website for more information about creating an app with WordPress.
This plugin is not an app-creator in itself, it serves as the core for all app development with UniPress.

[Follow the development of the UniPress API Plugin on Bitbucket](https://bitbucket.org/unipress/unipress-api).

Have questions? [Contact us here](https://getunipress.com/contact-us/).

App Features:

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

= 1.18.4 =
* Fixing bug caused when article doesn't have a featured ID

= 1.18.3 =
* Disabling autoload for UniPress Cookie option

= 1.18.2 =
* Getting rid of unused functionality

= 1.18.1 =
* Adding post data to unipress_api_after_wp_insert_user

= 1.18.0 =
* Changing how post/article notifications are determined, for more flexibility
* Adding unipress_api_after_wp_insert_user action

= 1.17.5 =
* Fixing bug causing Article Notifications not to work properly w/ IssueM Articles

= 1.17.4 =
* Fixing bug in leaky paywall login user integration

= 1.17.3 =
* Adding more info to authorize device and login user API calls for app 

= 1.17.2 =
* Adding post type filter in push notification for article notifications

= 1.17.1 =
* Fix for all pushes for users not using push categories

= 1.17.0 =
* Fix for push article-notifications

= 1.16.0 =
* Fixing bug in get_the_excerpt() call

= 1.15.0 =
* Adding Post ID Api Call
* Breaking out device limit checks

= 1.14.0 =
* Remove Post ID from Manual UniPress Push notifications

= 1.13.1 =
* Adding author to Ads and Push notifications
* Fix missing login method

= 1.13.0 =
* Adding LogOut API call
* Adding Show Content shortcode
* Adding filter for Leaky Paywall check

= 1.12.0 =
* Fix variable name in wp-authenticate error check
* Add unipress_excluded_terms filter to get_push_categories API call 

= 1.11.0 =
* Fix for duplicate devices on multiple user accounts
* Fix for Ajax calls for unusual WordPress setups
* Adding nest comments sorting
* Return comment object on success
* Adding Post ID to all push notifications
* Fix for category pushes
* Adding API call for login User w/ UN/PW credentials 
* Adding API call for offline reading mode

= 1.10.0 =
* Change settings so they save even when there is an error verifying the App ID
* Added an API call to create s user, not a subscriber
* Added Post ID to silent pushes
* Adding Push Categories
* Added filter to push taxonomy

= 1.9.0 =
* Get all Ads by default with get-ad-data API call

= 1.8.0 =
* Strip slashes of POST argument in push notifications
* Fixing bug caused when Leaky Paywall is enabled but no default restrictions are set

= 1.7.0 =
* Adding filters for post author and author meta

= 1.6.0 =
* Handling Duplicate and Flood triggers for comments appropriately

= 1.5.0 =
* Fix bug causing posts to overwrite global post when they shouldn't
* Fix bug in comment-email verification

= 1.4.0 =
* Trimming device-ID args
* Updating readme.txt

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
