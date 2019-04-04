# Build A Laravel App With TDD
It's time to take the techniques we learned in Laravel From Scratch, and put them to good use building your first real-world application. Together, we'll leverage TDD to create Birdboard: a minimal Basecamp-like project management app.

This series will give us a wide range of opportunities to pull up our sleeves and test our Laravel chops. As always, we start from scratch: `laravel new birdboard`.

## 1. Meet Birdboard

Let's begin by reviewing the application that we plan to build. We'll then finish up by installing Laravel and performing the first commit.

*Необходимо освоить Valet*. Есть отдельное видео на эту тему.
- www.laragon.org
- https://github.com/cpriego/valet-linux
- https://github.com/cretueusebiu/valet-windows
- http://laradock.io Docker PHP development environment. 

Устанавливаем проект и добавляем Git.

## 2. Let's Begin With a Test

My hope is to demonstrate, as much as possible, my actual workflow when writing my own applications. With that in mind, let's begin with our first feature test.

Создаем тест для Projects:
php artisan make:test ProjectsTest

Пользователь:
- добавляет проекты (понадобится библиотека `Faker`);
- появляется в базе данных;
- видит его в браузере;

Запускаем тест и по ходу правим ошибки, делаем улучшения:
`vendor/bin/phpunit tests/Feature/ProjectsTest.php`

Настраивае базу данных:
- создаем базу;
- добавляем настройки в `.env`;
- настраиваем phpunit на использование sqlite в памяти;
- `RefreshDatabase` означает, что миграции будут создаваться "на лету";
- `php artisan make:migration create_projects_table` и добавляем поля;
- добавляем routes;
- `php artisan make:model Project`
- добавляем view;

Разобрался:
- почему не запускались раньше тесты, без слова `test`, просто добавил комментарий `/** @test */` перед методом;
