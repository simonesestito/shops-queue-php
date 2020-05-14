# Shops Queue (backend)

This repository is part of the **Shops Queue** project.

Other related repositories:
- [Android app repo](https://github.com/simonesestito/shops-queue-android)

## Table of contents

- [Introduction](#introduction)
- [Features](#features)
- [REST API endpoints](#endpoints)
- [Architecture and project structure](#architecture)
- [Database](#database)
- [Push Notifications](#push)
- [E-mail verification](#emailcheck)
- [License](#license)

<a name="introduction"></a>
## Introduction

An idea to save time and avoid queues outside supermarkets and other shops in general.

This is a project made for my graduation exam.
In the last year we study PHP, and that's what determined the language to use for the backend.

It was created during Coronavirus lockdown in Italy. The idea came from [a post on Facebook](https://m.facebook.com/story.php?story_fbid=2814783488643375&id=310949775693438) written in March 2020 by the Italian minister of Agricultural, Food and Forestry Policies. It's also inspired by the system most post offices already use to save time.

<a name="features"></a>
## Features

A user on this platform can cover different roles:
- Simple user
- Shop owner
- Administrator

### User

A user must sign up to use the platform.

He can discover all the shops nearby which use this system to manage queues. Of course, shops can be found by their name or address too.

There's the possibility to queue at the store when you're still at home, arriving at the shop only when it's your turn.

A user will receive a push notification (via Firebase Cloud Messaging) when it's almost his turn.

### Shop owner

A shop owner must reach out an administrator to receive an appropriate account and to register his business in the platform.

After that, he can manage the queue of his shop and call the next customer.

### Administrator

An administrator can manage all accounts (both users and shop owners) and all shops.


<a name="endpoints"></a>
## REST endpoints

A full API documentation, complete with examples, can be found on Postman:

[![API documentation on Postman](https://run.pstmn.io/button.svg)](https://documenter.getpostman.com/view/11169543/Szf9URou?version=latest)

### Authentication

The authentication is managed using an authentication token.

It's generated by the server using a crypto random function.

The client must securely store it, and send it to the server using the HTTP Authorization header.

**Discover more about the way a token is generated and managed.**
Check out [this commit](https://github.com/simonesestito/shops-queue-php/commit/8c9d05771d91fbf11604d2debf7d12eb0de85706).

The user's password is stored in the database after being hashed using the **Blowfish** algorithm.

No sensitive information is stored in plain text in the database!

<a name="architecture"></a>
## Architecture

This project has been organized according to the following diagram:

![Architecture diagram](https://github.com/simonesestito/shops-queue-php/blob/master/graphics/architecture.png?raw=true)

A Controller receives a request for a specific base URI.
Each of its methods can be registered to listen on a more specific endpoint. A method can, and often should, return an object. It'll be transformed to JSON and sent to the client.

Each controller must register itself to the Controller container.

A Controller communicates with a Service, which is responsible for handling a more complex business logic. In case using a Service may be redundant, a controller directly interacts with a DAO.

A DAO (Data Access Object), as the name implies, it's an object which allows accessing data of a specific database table, joining other tables when required.

DTOs (Data Transfer Object) represent the data structure of all inputs and outputs of the endpoints.

The main entry point is the `index.php` file. It is responsible for doing all the wrap up logic. It finds the most appropriate Controller, invokes the specific method and sends the response out.

A custom-made Dependency Injection system has also been included, which uses PHP reflection, to instantiate classes quickly and easily.

### Folder structure

Some files or folders may have been omitted

- **db.sql** (MySQL DDL file)
- **src** (PHP source files)
  - **controller** (REST controllers)
  - **dto** (All DTOs)
    - **input** (Only input DTOs)
    - **output** (Only output DTOs)
    - **validation** (Model validation logic)
  - **exceptions** (Custom app-defined exceptions for better error handling)
  - **service** (Services)
  - **env.php** (Environment variables)

<a name="database"></a>
## Database

![Database tables](https://github.com/simonesestito/shops-queue-php/blob/master/graphics/db_schema.png?raw=true)

<a name="push"></a>
## Push Notifications

Push Notifications are used to warn the user about the status of its booking. 2 types of notifications are provided: one about the number of people ahead in the queue, the other one it's about the cancellation of the booking by the shop ownwer.

Technically speaking, push notifications are implemented taking advantage of **FCM (Firebase Cloud Messaging)**.

The client app sends to the server its FCM token. It'll be associated to the currently logged in user. If a user has the same token, it is removed from the old user and assigned to the new one.

A user can have multiple FCM tokens assigned. They'll be used to send notifications to every device the user owns.

**The sending of push notifications is managed by** [FcmService.php](https://github.com/simonesestito/shops-queue-php/blob/master/src/service/FcmService.php)

<a name="emailcheck"></a>
## Email verification

When a user signs up, its account is in a "deactivated" state. A new verification code will be randomly generated by a CSPRNG function, then hashed, encoded in hexadecimal and saved into the dtabase.

After that, an email containing the previously generated token is sent to the user's e-mail address. Only when the user clicks on the link in the e-mail, its account will be activated.

To send e-mails, SendGrid is used, which offers a practical REST API.

**The sending of e-mails is managed by** [EmailService.php](https://github.com/simonesestito/shops-queue-php/blob/master/src/service/EmailService.php)

<a name="license"></a>
## License

    Copyright 2020 Simone Sestito
    
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

