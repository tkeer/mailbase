# Laravel Mail Catcher Driver

This package include a new `mailbase` driver which will catch all the sent emails and save it to the database. 
It also exposes a route `/mailbase` which you can visit to preview all the mails. 

## Installation

To install run the following command in your terminal:

```bash
composer require tkeer/mailbase --dev
```

Then run the migration
```
php artisan migrate
```

Finally, change `MAIL_DRIVER` to `mailbase` in your `.env` file:

```
MAIL_DRIVER=mailbase
```

## Usage
### Clearing Saved Emails
If you want to clear any of the saved emails that are in the database, use the following command:
```
php artisan mailbase:clear
```
