# image-resizer
=============================

This app performs basic validation on image formats (jpg|jpeg|gif|png|webp) present in the server and renders those images cropped and resized. 


# Installation

Clone the repository to a configured webserver.

```
git clone https://github.com/jonatanSousa/image-resizer.git
```

This project  uses composer as dependency manager in the root directory install it,
following the instructions on https://getcomposer.org/doc/00-intro.md

After downloading composer execute this command:
```
composer install
```

## Using Docker

One should use 🐳 Docker to setup & install the project please kindly follow the instructions:
At this point i assume you have docker & running on your machine

```
docker compose up --build
```

A Simple HTML page should be available with example to use the image resize 

```
http://localhost:8080/display  
```

![image](https://github.com/jonatanSousa/image-resizer/assets/35583616/4d1eef90-fb7b-4adb-acf5-04b316772679)


Unit Testing
=============================

```
./vendor/bin/phpunit tests

```

Why Symfony components?
=============================
There is no particular reason except for curiosity and learning purposes.