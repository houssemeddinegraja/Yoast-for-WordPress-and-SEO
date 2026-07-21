# The Architecture of WordPress Hooks: Actions vs. Filters

This document explains the fundamental mechanics of WordPress backend development, specifically how the server generates pages dynamically and how developers use **Hooks** (Actions and Filters) to safely modify a website without touching core files.

---

## Core Concept: The Dynamic CMS

To understand WordPress, you must first unlearn how traditional websites work.

**There are no `.html` files in WordPress.**

If you search a WordPress server for `homepage.html` or `blog-post.html`, you will not find them. Instead, WordPress operates as a **Dynamic Content Management System (CMS)**:

1. **The Database:** All text, settings, and content are stored as raw data in a database.
2. **The PH:** The server uses PHP scripts to fetch that raw data.
3. **On-The-Fly Generation:** The exact moment a user clicks a link, the server grabs the raw data, wraps it in HTML and CSS, and serves it to the browser. Once the user leaves, that specific HTML instance effectively vanishes.

Because HTML is generated fresh for every single visitor, we do not edit static files. If we want to change the website, we write **Plugins (PHP)** to intercept the server *while* it is building the page.

---

## The Millisecond Timeline of a Page Load

To understand how custom code works, look at the exact timeline of a server building a webpage. It happens in milliseconds:

1. **The Request:** A visitor clicks a link. The server wakes up to build the page.
2. **The Database Fetch:** WordPress pulls the raw text and data from the database.
3. **Filter Hooks Trigger:** Before the data is wrapped in HTML, WordPress pauses. Custom **Filters** intercept this raw data, modify it, and return it.
4. **The HTML Wrapping:** WordPress takes the (now modified) data and starts building the physical HTML layout (`<header>`, `<body>`, etc.).
5. **Action Hooks Trigger:** As WordPress reaches specific structural milestones (like the `<head>` or the footer), it pauses. Custom **Actions** inject brand-new pieces of HTML into those exact spots.
6. **The Final Delivery:** The completed HTML file is sent across the internet to the visitor's browser.

---

## The Two Types of Hooks

WordPress core developers left hundreds of intentional "pauses" along this timeline called **Hooks**. Developers use Plugins to "hook" their custom code into these pauses. There are two types:

### 1. Filters (`add_filter`)

- **When they happen:** *Before* the HTML structure is built.
- **What they do:** They intercept existing raw data from the database, modify it, and hand it back.
- **The Golden Rule:** A filter function MUST end with a `return` statement. If you don't return the data, it gets deleted from the webpage.

**Example: Appending SEO branding to a page title**

```php
// Intercept the title data before it is printed
add_filter('wpseo_title', 'custom_seo_branding');

function custom_seo_branding($title) {
    // Modify the raw data
    $modified_title = $title . ' | Secure Corporate Payroll';

    // Hand it back to WordPress
    return $modified_title;
}
```

### 2. Actions (`add_action`)

- **When they happen:** During the HTML construction.
- **What they do:** They wait for a specific layout milestone and execute a task or inject completely new code that did not exist before.
- **The Golden Rule:** Actions do not modify existing data. They execute commands (like `echo` to print HTML or `wp_mail` to send an email).

**Example: Injecting a visual banner into the footer**

```php
// Wait until WordPress is physically building the footer
add_action('wp_footer', 'inject_custom_banner');

function inject_custom_banner() {
    // Add brand new HTML to the page
    echo '<div class="custom-banner">Code Test Successful!</div>';
}
```

---

## The Mental Model: The Car Factory

If the webpage is a custom car being built on an assembly line:

- **Filters modify the raw materials.** The factory pulls raw leather to make seats. A Filter steps in and says, "Wait, dye this leather blue before you build the seat."
- **Actions add to the assembly line.** The car frame is moving down the line. The factory reaches the "Install Dashboard" stage. An Action steps in and says, "While you are building the dashboard, bolt this custom phone mount onto it."

---

## Summary Cheat Sheet

| Feature | Filter (`add_filter`) | Action (`add_action`) |
|---|---|---|
| **Primary Purpose** | Modify existing data. | Add new code or execute tasks. |
| **Timeline Placement** | After data fetch, before HTML wrapping. | During the HTML layout construction. |
| **Data Flow** | Intercepts → Changes → Returns. | Listens for milestone → Executes task. |
| **Code Requirement** | Must use `return`. | Usually uses `echo` or executes a function. |
