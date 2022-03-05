# Plugin Data ClearUp

A plugin to add an action to all **uninstallable** plugins to deactivate & clear the plugin data without deleting the plugin files.

## Features
- Adds an action to all **uninstallable** plugins to deactivate & clear the plugin data without deleting the plugin files.
- Define a token based webhook via ajax actions to clear plugins data via url ( helpfull with CI/CD solutions to clear data before git push)

## Setup
- Install & Activate :) nothing else required for regular admin usage.
- For **webhook** setup you need to define a contant variable to be used on token validation, the constant name must be `PLUGIN_DATA_CLEARUP_WEBHOOK_TOKEN`
- Webhook url example: `https://domain.test/wp-admin/admin-ajax.php?action=clear_plugin_data_webhook&plugins=easy-wp-smtp/easy-wp-smtp.php&clear-token=1234567890`
- Webhook url example multiple plugins: `https://domain.test/wp-admin/admin-ajax.php?action=clear_plugin_data_webhook&plugins=easy-wp-smtp/easy-wp-smtp.php,litespeed-cache/litespeed-cache.php&clear-token=1234567890`


## About Me
- Name: **Khaled Abu Alqomboz**, 
- Work: **Freelancer Full Stack WordPress & WooCommerce Developer**.
- Experience: **9+ years as Web Applications Developer includes 7+ Years of WordPress/WooCommerce Development experience**
- Availablity: **Available to work remotely, contact me please at eng.khaledb@gmail.com**

