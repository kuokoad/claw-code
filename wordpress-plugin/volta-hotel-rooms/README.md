# Volta Hotel – Rooms Manager Plugin

WordPress plugin for the **Volta Serene Hotel** site. Manages the Rooms & Suites section as a Custom Post Type with an Elementor widget and `[volta_rooms]` shortcode.

## Installation

1. Copy the `volta-hotel-rooms/` folder into `/wp-content/plugins/`.
2. Activate via **Plugins → Installed Plugins**.
3. Default room categories are seeded on activation (Villa, Chalet, Suite, Deluxe, Executive, Honeymoon).

## Adding Rooms

Go to **Rooms → Add New**:

| Field | Description |
|---|---|
| Title | Room name, e.g. "SEN Villas" |
| Featured Image | Main card photo |
| Body / Editor | Detailed room description (shown on single room page) |
| Room Category | Tag the room (villa / chalet / suite …) |
| Nightly Price (GHS) | Numeric, e.g. `2315` |
| Price Prefix | e.g. `from` |
| Capacity | e.g. `2 Adults` |
| Room Size | e.g. `52 m²` |
| Location / Wing | e.g. `Kabakaba Hills, Ho` |
| Bed Type | e.g. `King bed` |
| View / Highlight | e.g. `Hill view` |
| Card Badge | Short badge shown on the image, e.g. `For stay` |
| Booking / Enquiry URL | Direct link to booking engine for this room |
| Gallery IDs | Comma-separated WP attachment IDs for gallery carousel |

## Displaying Rooms

### Option A — Elementor Widget

Open any page in Elementor → search **"Hotel Rooms Grid"** in the widget panel → drag on to canvas.

Widget controls:
- Show/hide category filter tabs
- Columns (2 or 3)
- Max rooms, category filter, sort order
- Full style controls for pills, cards, price colour, CTA colour

### Option B — Shortcode

```
[volta_rooms]
[volta_rooms show_filter="yes" columns="3"]
[volta_rooms categories="villa,suite" limit="4" orderby="meta_value_num" order="ASC"]
```

| Attribute | Values | Default |
|---|---|---|
| `show_filter` | `yes` / `no` | `yes` |
| `columns` | `2` / `3` | `3` |
| `limit` | integer / `-1` (all) | `-1` |
| `categories` | comma-separated slugs | all |
| `orderby` | `date` / `title` / `meta_value_num` / `menu_order` | `date` |
| `order` | `ASC` / `DESC` | `ASC` |

## WordPress → Elementor Page Build Guide

| Design Section | Build Approach |
|---|---|
| Navigation + Hero | Elementor Theme Builder → Header template |
| **Rooms & Suites** | **This plugin – Elementor widget or shortcode** |
| About / Story | Elementor section (text + counter widgets) |
| Dining (3 venues) | Elementor Cards widget or Image Box |
| Experiences / Nearby | Elementor Icon Box grid |
| Wellness | Elementor Image Box grid |
| Conferences | Elementor section with Counter + Text |
| Contact / Footer | Elementor Theme Builder → Footer template |
