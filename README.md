# PHP Disbursement API
a repository for coding test purposes only

## Getting Started

### Requirement

* MySQL 5.7
* PHP >= 7
* cURL extension
* MySQLi extension
* Composer

### Installation

1. Clone this repository.
2. Set database config in `config/database.php` to match with your local config and create a database exactly the same like you put inside `config/database.php`.
3. Run `composer dump-autoload`.
4. Run migration `php migration.php`.
5. Run `php disburse.php` to display this repository result