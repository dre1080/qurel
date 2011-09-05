# Qurel

* https://bitbucket.org/heyinspire/qurel

## Overview

Qurel is a SQL AST manager for PHP inspired by Rails' Arel.
It's useful as a base for an ORM. It's still in development stage.
The current supported RDBMS's include MySql, SQLite and PostgreSQL.

## Installation

Download and unzip the contents of the `lib` folder into your project and require the
qurel base file.

    require_once 'qurel.php';

## Generating queries

Generating a query with Qurel is simple. For example, in order to produce

    SELECT * FROM users

you construct a table relation and convert it to sql:

    use Qurel\Table;

    $users = new Table('users');
    $query = $users->project(Qurel::sql('*'));
    echo $query->sql();
    // : SELECT * FROM users

The Table object is the base for Qurel's query API.
The Table instance attributes can be accessed as an array or object. We will go
through this below.

### More Sophisticated Queries

Let's go through the most common relational operators.

First is the 'restriction' operator, `where`:

    echo $users->where($users['name']->eq('amy'));
    // : SELECT * FROM users WHERE users.name = 'amy'

Or if you don't like using arrays:

    // same as above
    echo $users->where($users->name->eq('amy'));

What would, in SQL, be part of the `SELECT` clause is called in Qurel a `projection`:

    echo $users->project($users['id']); // : SELECT users.id FROM users

## Joins

Joins look very similar to SQL standard:

    $photos = new Table('photos');
    echo $users->join($photos)->on($users['id']->eq($photos['user_id']));
    // : SELECT * FROM users INNER JOIN photos ON users.id = photos.user_id

#### Complex Joins

    $comments = new Table('comments');

And this table has the following attributes: `id, body, parent_id`

The `parent_id` column is a foreign key from the `comments` table to itself. Now, joining a table to itself requires aliasing in SQL.

    $replies = $comments->alias();
    echo $comments_with_replies = $comments->join($replies)->on($replies['parent_id']->eq($comments['id']));
    // : SELECT * FROM comments INNER JOIN comments AS comments_2 WHERE comments_2.parent_id = comments.id

## Selections

### Limits & Offsets

What are called `LIMIT` and `OFFSET` in SQL are called `take`/`limit` and `skip`/`offset` in Qurel:

    echo $users->take(5); // : SELECT * FROM users LIMIT 5
    echo $users->skip(4); // : SELECT * FROM users OFFSET 4

### Groups

`GROUP BY` is called `group`

    echo $users->group($users['name']); // : SELECT * FROM users GROUP BY name

### Chaining methods

The best property of the Relational Algebra is its "composability", or closure under all operations. For example, to restrict AND project, just "chain" the method invocations:
    
    echo $users->where($users['name']->eq('amy'))->project($users['id']);         
    // : SELECT users.id FROM users WHERE users.name = 'amy'

All operators are chainable in this way, and they are chainable any number of times, in any order.

    $users->where($users['name']->eq('bob'))->where($users['age']->lt(25));

Of course, many of the operators take multiple arguments, so the last example can be written more tersely:
    
    $users->where($users['name']->eq('bob'), $users['age']->lt(25));

The `OR` operator works like this:

    $users->where($users['name']->eq('bob')->_or($users['age']->lt(25)));

The `AND` operator behaves similarly using `_and`;

#### Inline math operations

Suppose we have a table `products` with prices in different currencies. And we have a table currency_rates, of constantly changing currency rates.
Now, to order products by price in user preferred currency simply call:

    $products       = new Table('products');
    $currency_rates = new Table('currency_rates');

    $products
    ->join($currency_rates)->on($products['currency_id']->eq($currency_rates['from_id']))
    ->where($currency_rates['to_id']->eq('some_preferred_currency'), $currency_rates['date']->eq(date('Y-m-d'))
    ->order($products['price']->multiply($currency_rates['rate']));

    // : SELECT * FROM products INNER JOIN currency_rates ON products.currency_id = currency_rates.from_id
    //   WHERE currency_rates.to_id = 'some_preferred_currency' AND currency_rates.date = '2011-9-4'
    //   ORDER BY products.price * currency_rates.rate ASC

### Ordering

    echo $users->order($users['username']->asc());
    // : SELECT * FROM users ORDER BY users.username ASC

    echo $users->order($users['username']->desc());
    // : SELECT * FROM users ORDER BY users.username DESC

    echo $users->order($users['username']);
    // : SELECT * FROM users ORDER BY users.username ASC


#### TODO
- Add more docs
- Add support for additional RDBMS's
- Add autoloading of classes


License Information
===================

Copyright (c) 2011 Andrew Wayne

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
