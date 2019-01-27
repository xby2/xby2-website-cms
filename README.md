[![Build status](https://dev.azure.com/xby2/website/_apis/build/status/website-cms-CI)](https://dev.azure.com/xby2/website/_build/latest?definitionId=10)

# xby2-website-cms

The CMS used for the X by 2 website.

## Getting Started

### Requirements

- LAMP Server([download](http://www.wampserver.com/en/)).

### Importing Database

1. Connect to the Azure MySQL Server using server admin credentials ([suggest using MySQL Workbench](https://www.mysql.com/products/workbench/))
1. Copy the database for source environment (likely production) into your destination environment (likely local) using the Migration Wizard.
1. In `wp_options`, change the `siteurl` and `home` to reflect to destination URL (`http://localhost/xby2-website-cms`)
1. You may need to create an admin user inside the destination database. SQL statement is below:

```
INSERT INTO `wp_users` (`user_login`, `user_pass`, `user_nicename`, `user_email`, `user_status`)
VALUES ('YOUR_USERNAME', MD5('YOUR_PASSWORD'), 'FIRSTNAME LASTNAME', 'EMAIL@EXAMPLE.COM', '0');

INSERT INTO `wp_usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`)
VALUES (NULL, (Select max(id) FROM wp_users), 'wp_capabilities', 'a:1:{s:13:"administrator";s:1:"1";}');

INSERT INTO `wp_usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`)
VALUES (NULL, (Select max(id) FROM wp_users), 'wp_user_level', '10');
```

### Cloning and Running Codebase

1. Clone the repository to your local machine.
1. Using Admin cmd, create a symlink between the destination web server and your source code repo folder (`mklink /D TARGET SOURCE`). Example: `mklink /D c:\wamp64\www\xby2-website-cms d:\code\xby2-website-cms`
1. Access the WordPress instance using localhost. (http://localhost/xby2-website-cms)
1. Run through the 5-minute install, using the newly created database.
1. Log in while bypassing SSO configuration: (http://localhost/xby2-website-cms/wp-login.php?saml_sso=false).
