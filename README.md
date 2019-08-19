# slim3auth
Slim framework 3 with Auth integration. Ready to use.

slim3auth includes:

- Illuminate/Database
- Twig
- CSRF protection
- Respect Validation
- Dotenv (.ENV) Enviromnent data
- Phinx for migration
- a 404 view


# Installation
### Pre-requisites 
Make sure you have php, mysql and composer installed on your machine.

###
```
Change your example.env to .env
Insert your DB credentials.
```

```
Change /public/example.htaccess to .htaccess
```

```
composer install
```

```
composer phinx migrate
```

all done.
