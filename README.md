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

**TODO: Add E/R diagram and tables schema**

<a name="license"></a>
## License

**TODO: Add AGPL3.0 license**

