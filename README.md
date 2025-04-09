# Go-Games Project

This project is a web application for managing game lists and tracking game statuses.

## Features


## Admin Panel

An admin panel is available at `/admin` for users with the `Admin` role. It provides interfaces for managing core application data.

**Features:**

*   **User Management:** View, create, edit, and delete users.
*   **Game Management:** View, create, edit, and delete games.
*   **Game List Management:** View, create, edit, and delete game lists.

**Access:**

*   Only users with the `Admin` role (defined in `App\Enums\UserRole`) can access `/admin` and its sub-routes.
*   Access is controlled via the `App\Http\Middleware\EnsureUserIsAdmin` middleware.
