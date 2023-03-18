## Blog Posts REST API

### Description
This is a project made for studying and exercising REST API 
development with PHP Slim framework, with API documentation created using OpenAPI (Swagger UI).
It has CRUD functionalities for, both, posts and categories, which have the many-to-many relationship.

### Requirements
- [ ] PHP ^8.1
- [ ] MySQL/MariaDB
- [ ] Composer
- [ ] File Upload enabled in php.ini


### Usage

#### Instructions
- Clone this repository
- Run the `composer install` to install all the dependencies
- Create a MySQL database in your local machine
- Copy .env-example to .env and insert your host and database info there
- Open terminal/gitbash in project root folder and run the following command to create the db:
    `php -S localhost:8000 -t public`

Now you can use the REST API to Create and Update blog posts and their categories using the url:
[localhost:8000](localhost:8000)

#### API documentation: 
It is available at [localhost:8000/apidocs](localhost:8000/apidocs)
where you can check out all the endpoints and parameter info.