# Layout Sketch

```mermaid
graph TD
    subgraph "App Layout"
        subgraph "Header"
            A[Logo] --- B[Search Bar]
            B --- C[User Controls]
        end
        
        subgraph "Navigation"
            D[Home] --- E[My Games]
            E --- F[My Lists]
        end
        
        subgraph "Breadcrumbs"
            G[Breadcrumb Trail]
        end
        
        subgraph "Main Content"
            H[Page Content]
        end
        
        Header --> Navigation
        Navigation --> Breadcrumbs
        Breadcrumbs --> "Main Content"
    end
```

## Implementation Details

The layout has been refactored to move the navigation links from the header to a dedicated navigation bar positioned between the header and breadcrumbs. Here's a breakdown of the components:

### Header Components

1. **Logo**: Located on the left side of the header, links to the home page.
2. **Search Bar**: Centered in the header, allows users to search for games.
3. **User Controls**: Located on the right side of the header, shows login/register buttons for unauthenticated users or user profile for authenticated users.

### Navigation Components

A dedicated navigation bar positioned below the header containing:
- Home: Links to the main page with game releases
- My Games: Links to the user's game collection (requires authentication)
- My Lists: Links to the user's game lists (requires authentication)

### Breadcrumbs Components

The breadcrumbs are now positioned below the navigation bar, providing clear context about the user's current location in the application.

### Main Content Components

The main content area changes based on the current page, displaying the appropriate content for each route.

### Mobile Experience

On mobile devices:
- The navigation links are collapsed into a hamburger menu
- The header adapts to the smaller screen size, with the search bar taking up less space
- The content area adjusts its layout for optimal viewing on smaller screens

### Authentication Handling

- All navigation links are visible regardless of authentication status
- When an unauthenticated user clicks on protected routes (My Games, My Lists), they are redirected to the login page
- The user section adapts based on authentication status, showing either a login button or the user's profile

This implementation creates a clear visual hierarchy with the header at the top, navigation below it, and then breadcrumbs followed by the main content, providing a consistent and intuitive user experience across all pages.
