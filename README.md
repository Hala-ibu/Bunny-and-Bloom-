# Bunny & Bloom

Bunny & Bloom is a café-themed web application with a browser-based interface and a PHP REST API backed by MySQL. The repository includes both the client and server code, plus API routes for the main store features.

## Features

- Browse the home, menu, blog, about, profile, and sign-in pages.
- Work with products, inventory, orders, reviews, users, and contact requests through dedicated API routes and services.
- Use token-based request middleware for protected API routes; login, registration, public product reads, documentation, and contact routes are explicitly handled by the app.
- Manage order states, including cancelling pending orders and restoring their stock quantities.
- Explore the API documentation served through Swagger UI.

## Project structure

- `frontend/` — HTML/CSS/JavaScript application, page views, UI assets, API client, services, and controllers.
- `backend/index.php` — FlightPHP application entry point and route registration.
- `backend/rest/routes/` — API route definitions.
- `backend/rest/service/` — business logic for authentication, users, products, orders, reviews, inventory, and contact requests.
- `backend/rest/dao/` — database access code.
- `backend/config.php` — backend configuration, including database connection settings.
- `composer.json` and `backend/composer.json` — PHP dependency manifests.

## Run locally

You’ll need PHP, Composer, and MySQL. Install the Composer dependencies for the backend, configure the database connection in `backend/config.php`, and point a PHP-capable web server at the backend. Open the frontend through a local web server and configure its API base URL to match your backend. Database setup and local credentials depend on your environment.

## Technology

JavaScript, HTML, CSS, Bootstrap, PHP, FlightPHP, Composer, MySQL, and Swagger/OpenAPI.

This README describes the implementation present in the repository; configure and verify the local database and server before using the app.