# 🐰 Bunny & Bloom Web App

## What this project is about
Bunny & Bloom is a **single-page web application (SPA)** built using **HTML, JavaScript, and MySQL**.

For **Milestone 1**, the focus was on **frontend development** and **database planning** (draft ERD). Later milestones will include full backend features, CRUD operations, and user authentication.

---

## What I did so far
- Created the **project structure** following the intended organization.  
- Built **static pages** for the app (no backend yet):  
  - **Register & Login**  
  - **Home, Menu, Blog, About, Profile**  
  - **Pages for all entities**  
- Used **Bootstrap template** for a responsive and consistent design.  
- Each page is **separate HTML**, with JS and CSS included only once.  
- Planned the **database with 5 entities**:  
  - Users  
  - Products  
  - Orders  
  - Reviews  
  - Inventory  

![erd](/Bunny-and-Bloom-/frontend/assets/img/erd.png)

---

## Project Structure
project-folder/
│
├─ frontend/ # Frontend (client-side)
│ ├─ index.html # Main HTML file (entry point)
│ ├─ assets/ # Static assets (images, fonts, icons)
│ │ ├─ css/ # Stylesheets (global & component-specific)
│ │ ├─ js/ # JavaScript files for interactivity
│ ├─ pages/ # Individual HTML/component files for pages
│ ├─ services/ # API calls and frontend service logic
│ └─ utils/ # Utility functions (formatting, validation)
│
├─ backend/ # Backend (server-side)
│ ├─ rest/ # REST API endpoints
│ │ ├─ routes/ # API routes
│ │ ├─ services/ # Business logic
│ │ └─ dao/ # Data Access Objects for database
│ ├─ index.php # Backend entry point
│ ├─ .htaccess # Server configuration & URL rewriting
│ ├─ vendor/ # Composer dependencies
│ ├─ composer.json # PHP dependencies & metadata
│ └─ composer.lock # Locked dependency versions
│
├─ .gitignore # Files/folders to ignore in version control
└─ README.md # Project documentation

markdown
Copy code

---

## Next Steps
- Create the **database in MySQL**.  
- Connect **backend to frontend** with **FlightPHP and AJAX**.  
- Implement **CRUD operations** for all entities.  
- Add **authentication and role-based access**.