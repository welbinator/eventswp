# EventsWP

**EventsWP** is a lightweight, developer-friendly WordPress plugin that allows you to create and manage events with custom fields and taxonomies, and display them beautifully using dynamic Gutenberg blocks and a FullCalendar-powered calendar page.

---

## 🔧 Features

- Custom Post Type: `eventswp-event`
- Custom Taxonomies: `event-category`, `event-type`
- Custom Fields via block editor sidebar:
  - Event date
  - Start and end time
  - Venue name and address
  - Contact phone/email
  - Google Map toggle
- Dynamic Events Block with:
  - Grid/List layout toggle
  - Adjustable columns (Grid)
  - Category filtering via checkboxes
- Calendar page with:
  - FullCalendar integration
  - Custom REST endpoint for optimized loading
  - Responsive list view on mobile
  - Time range and title displayed for each event

---

## 📦 Installation

1. Upload the plugin folder to `/wp-content/plugins/eventswp`
2. Activate the plugin via **Plugins > Installed Plugins**
3. Go to **Settings > EventsWP** to:
   - Enter your Google Maps API key (optional)
   - Choose a page to use as the event calendar

---

## 🧱 Blocks

### `Events Block`
Add this block to any page or post to display a list or grid of events.

- **Layout Options**: Grid or List
- **Columns**: 1–6 (for Grid)
- **Category Filter**: Choose specific categories to display

---

## 📅 Calendar Page

- Assign any page as the **Calendar Page** via plugin settings.
- That page will automatically output a responsive FullCalendar calendar.
- Events are loaded using a **custom REST API endpoint** (`/wp-json/eventswp/v1/calendar-events`) for performance.
- The calendar auto-switches to list view on small screens.

---
