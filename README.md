# Website tracker

## Requirements
- PHP >= 8.2
- Composer
- MySQL 8

## Installation & Usage
- Clone the repo: `git clone https://github.com/vuchkov/track`
- Copy `cp .env.example .env`
- Add your database settings in `.env`
- Install dependencies `composer install`
- Import the DB schema from `mysql.sql` in your MySQL 
- Open in your browser the example webpage: `index.html`
- Check the results :)

## Third party usage
- Put the tracking script: 
```
<script src="https://your-server-domain.com/tracker.js"></script>
```
on the website & pages.

## Updates
- Add MySQL (or SQLite - optional) DB
- Create a RestAPI endpoint `api.php`
- Add `.env.example`
- Create the example webpage: `index.html`
- Create the remote `tracker.js`
- Add MySQL support
