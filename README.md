# GENERAL INFO #

Setting up this framework requires that you download and set up Yeah! DevTools. Download from https://bitbucket.org/palethorn/yeah-devtools/src

After setting up your alias:

```
#!bash
alias yeah="php /path/to/yeah/file/in/devtools/root"
```
you can then create folder and invoke

```
#!bash
yeah create_app project_name
```

Instruct your HTTP server to point to web folder as webroot.

Lighttpd rewrite rules:

```
#!
 url.rewrite-if-not-file = (
        "^/(.*)" => "/project_name.php/$1"
    )

```

Apache rewrite rules are similar. htaccess file example (NOT TESTED):

```
#!
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^/(.*)$ project_name.php/$1
```