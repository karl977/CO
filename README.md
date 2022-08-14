# Cosmos odyssey

This repository contains the solution to the Cosmos odyssey task. 

[Task description](https://github.com/karl977/CO/edit/master/Cosmos-Odyssey%20excercise%202.pdf)


![Employee data](/public/images/solar-system.png "Employee Data title")

## Stack
* Laravel
* Inertia
* React

## Main concepts
* Periodically syncing data from webservice is achieved with Laravel Queue
* All rows saved to database have foreign keys to relative price-list. Thus, when a price-list is removed all relative data is also automatically deleted.

## Running in production mode
To run the application in production mode ensure you have Docker installed and in the project folder run:
```
docker compose up
```
Docker setup takes care of everything and application will be available at [127.0.0.1](127.0.0.1)

First launch can take up to 10 minutes.


## Testing
### Testing in production mode
1) Open shell to PHP container started in Docker
2) Run `cd /var/www`
3) Run `php artisan test`

### Testing in development mode
1) Go to project folder in WSL
2) Run `sail artisan test`

## Development
Development for this application is done by using Laravel Sail and WSL2
### Prerequisites
* Install Docker
* Enable Windows Subsystem for Linux version 2. Instructions [here](https://www.omgubuntu.co.uk/how-to-install-wsl2-on-windows-10)
* Install Ubuntu or other Linux distro from Microsoft Store.
* Configure Docker to use WSL2 backend
### Setup
1) Copy project folder to WSL filesystem
2) Go to project folder
3) In `.env` change `APP_DEBUG=false` to `APP_DEBUG=true`
4) Run `mv docker-compose.yml docker-compose-prod.yml`
5) Run `mv docker-compose-sail.yml docker-compose.yml`
6) Run `chmod u+x vendor/laravel/sail/bin/sail`
7) Configure alias for sail. Add line `alias sail='bash vendor/bin/sail'` to file `~/.bashrc`
8) Run command `sail up`
9) Ignore errors thrown and run in new terminal window `sail composer install`
10) Close process started by `sail up` with Ctrl+C
11) Run `sail up` again
12) In new terminal in project folder window run `sail artisan queue:work`
13) In new terminal in window run `sail artisan start-sync` to start data sync from webservice
14) Run `sail artisan migrate` to create database tables
15) Run `sail npm install`
16) Run `sail npm run dev`
17) Change `0.0.0.0` to `127.0.0.1` in file `public/hot`
18) Start developing with hot reload at `127.0.0.1`

NB!
After development make sure to delete `public/hot` file before running application in production mode

## TODO
* Improve booking view for mobile screens
* Cache already found routes for faster search requests
