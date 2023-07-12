 image-resizer
=============================

This app performs basic validation on image formats (jpg|jpeg|gif|png|webp) present in the server and renders those images cropped and resized. 


 Installation
=============================

## The Machine must have:
- Composer
- Docker 

Clone the repository to a configured webserver.

```
git clone https://github.com/jonatanSousa/image-resizer.git
```

This project  uses composer as dependency manager in the root directory install it,
following the instructions on https://getcomposer.org/doc/00-intro.md

After downloading composer execute this command:

```
cd image-resizer/ && sudo chmod 777 images -R && composer install
```

## Using Docker

One should use 🐳 Docker compose to setup & install the project,please kindly follow the instructions:
https://docs.docker.com/compose/install/

after having docker & running on your machine execute: 

```
docker compose up --build
```

A Simple HTML page should be available with example on how to use the image resize 

```
http://localhost:8080/display  
```

![image](https://github.com/jonatanSousa/image-resizer/assets/35583616/579c75c3-1996-424c-94a3-e7bd552db780)


Unit Testing
=============================

```
./vendor/bin/phpunit tests

```

Why Symfony components?
=============================
There is no particular reason except for curiosity and learning purposes.
