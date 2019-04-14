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
`php artisan make:test ProjectsTest`

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

Начинаем рефакторить:
- `php artisan make:controller ProjectsController`

Разобрался:
- почему не запускались раньше тесты, без слова `test`, просто добавил комментарий `/** @test */` перед методом;

## 3. Testing Request Validation

We haven't yet written any request validation logic. As before, let's use a TDD approach for specifying each validation requirement.

Начинаем тестировать валидацию данных:
- добавляем тест `a_project_requires_a_title()`;
- тестируем только этот метод `vendor/bin/phpunit --filter a_project_requires_a_title`;
- `alias pf="vendor/bin/phpunit --filter"`;
- создадим фабрику проектов `php artisan make:factory ProjectFactory --model="App\Project"`
- проверим работу фабрики:
```
php artisan tinker
factory('App\Project')->make() // возвращает новый объект
factory('App\Project')->raw() // возвращает новый объект->массив
factory('App\Project')->create() // создает запись в БД
```

## 4. Model Tests

We must next ensure that a user can visit any project page. Though we should start with a feature test, this episode will provide a nice opportunity to pause and drop down a level to a model test.

Задача - реализовать тест - "Пользователь может видеть конкретный проект".

Приступаем:
- создадим метод `a_project_requires_a_title()`;
- подключаем `$this->withoutExceptionHandling()`, чтобы не были ошибки HTTP;
- настраиваем контроллер, добавляем путь в routes и view;
- почему то не проходит тест description?!, ааа потому что написал `'description'`, а правильно так: `$project->description`;

Начинаем рефакторить:
- используем data binding в контроллерах;
- хардкодерные пути - неправильно, добавляем пути в модель;
- добавим еще один тест `php artisan make:test ProjectTest --unit` (а чем он отличается от обычного? - там мы будем тестировать модели);
- добавим метод `it_has_a_path()`;
- добавим в модели метод `path()`;
- тест работает `vendor/bin/phpunit --filter it_has_a_path`;
- поменяем путь, там где он захардкоден - в тестах и на главной странице;
- а как это будет выглядеть в браузере?
```
php artisan migrate
php artisan serve
```
- используем Blade директиву @forelse;
- создадим *Tinker* несколько записей `factory('App\Project', 5)->create()`;

## 5. A Project Requires An Owner

It's true that we can now create and persist projects to the database, but they aren't currently associated with any user. This isn't practical. To fix this, we'll write a test to confirm that the authenticated user is always assigned as the owner of any new project that is created during their session.

Приступим:
- создадим тест проверки наличия владельца;
- добавим в миграцию дополнительное поле;
- пропишем в миграции связь owner_id с таблицей user и каскадное удаление, при удалении пользователя;
- `php artisan migrate:refresh` - *какие то непонятные проблемы с миграцией... попробовать создать таблицы в БД напрямую*;
- добавляем в фабрику и контроллер поле;
- тест работает `vendor/bin/phpunit --filter 'a_project_requires_an_owner'`.

Приступим к рефакторингу:
- обновляем валидацию в контроллере, пользователь должен быть авторизованным;
- полезный прием, не работает тест, а как он проходит? поставь в контроллер заглушку `dd('here we are')`;
- в роуты добавляем требование - постить проекты может только авторизованный пользователь;
- переделываем тест - как проверить, что юзер не авторизован? правильно - его должно перекинуть на страницу ввода пароля;
- создадим страницу авторизации `php artisan make:auth`;
- что мы видим? Контроллеры авторизации, в ресурсах появились шаблоны страниц авторизации, в роутах появились пути;
- тест работает! `vendor/bin/phpunit --filter 'a_project_requires_an_owner'`.
- переименуем тест на `only` и поместим его в начало класса;
- после тест всего класса идет с ошибками, почему? потому что в остальных методах пользователь должен быть авторизован;
- добавляем авторизованного пользователя `$this->actingAs(factory('App\User')->create());`;
- тест класса работает!

Приступим ко второму этапу валидации:
- в контроллере сделаем более красивый код для сохраненния проекта;
- в тестах ошибка!
- создадим отдельный тест `php artisan make:test UserTest --unit` и напишем тест;
- добавим метод `projects()` в класс `User`;
- тест работает `vendor/bin/phpunit --filter 'a_user_has_projects'`;
- все тесты работают! `vendor/bin/phpunit`

Что сделать?
- алиасы на запуск теста `pf`;
- настроить Sublime на запуск тестов из него;
- как подключать используемые классы? `use ...\...\...`;

## 6. Scoping Projects

In this episode, we'll continue tweaking which projects are displayed to the user. We'll also begin implementing the appropriate page authorization.

Приступим:
- сделаем, чтобы список проектов могли видеть авторизованные пользователи;
- добавим:
```
//ProjectsController.php
public function index()
{
    $projects = auth()->user()->projects;

```
- и в роутах надо добавить `middleware('auth')`;
- авторизуемся и можно смотреть проекты, но если указать id проекта другого пользователя, то мы ее увидим, не порядок;
- обновим метод `show()`, работает;
- пора написать тесты... `guests_cannot_view_projects()` и другие... работает! (потому что в роутах стоит middleware);
- а остальные тесты перестали работать!, исправляем, работает!;
- переделаем файл роутов;

Второй раунд дописания тестов:
- делаем тест получения ошибки 403 при посещении чужого проекта;
- но все тесты так и не идут, почему? Потому что `$project->owner_id` вовращает не число, поэтому сравнение делаем не строгое;
- добавляем в модель связь Проект\Пользователь и пишем под него тест `it_belongs_to_an_owner()`;
- отрефакторим проверку ТекПользователь\ПроектПользователь в контроллере (посмотри, как легко читается эта конструкция);

## 7. The Create Project View

We already have the necessary logic to persist new projects, however, we haven't yet created the "create project" page, itself. Let's take care of that quickly in this episode.

Приступим:
- переименуем файл теста в то, что он делает - `ManageProjectTest.php`;
- объеденим 3 похожих теста в один;
- запускаем тесты, работают;

Теперь перейдем к созданию проекта:
- добавим метод создания проекта в контроллер;
- добавим роут;
- напишем тест - что мы тестируем? не форму как таковую, а что происходит при ее заполнении и отправке;
- [Available Assertions](https://laravel.com/docs/5.7/http-tests#available-assertions)
- добавим view;
- запускаем тесты, работают;
