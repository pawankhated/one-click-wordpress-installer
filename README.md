# Installation
Using this repo you can install wordpress and there required plugin using single command 

1. Clone the git repo - `git clone https://github.com/pawankhated/wordpress-installer.git`
2. Run `composer install`
3. Copy `.env.example` to `.env` and update environment variables:
  * `DB_NAME` - Database name
  * `DB_USER` - Database user
  * `DB_PASSWORD` - Database password
  * `DB_HOST` - Database host
  * `WP_HOME` - Full URL to WordPress home (http://test.com)
  * `WP_SITEURL` - Full URL to WordPress including subdirectory (http://test.com/wp)

4. Add theme(s) in `app/themes` as you would for a normal WordPress site....

