# Schematic

A WordPress theme for a technical blog. It is a journal, not a marketing page: a numbered index, a lead note, series that stay in order, and a heading outline beside long articles.

The palette is warm paper and oxide ink. Type is Fraunces for titles, Source Serif 4 for body text, and IBM Plex Mono for metadata and code. A night theme follows the system preference and can be switched per browser.

## Install

1. Copy the `schematic` directory into `wp-content/themes/`.
2. In **Appearance → Themes**, activate Schematic.
3. Set the site tagline under **Settings → General**. The theme uses it as the deck under the title.
4. Assign a **Primary** menu under **Appearance → Menus**. Pages are listed if no menu is assigned.
5. Write notes as posts. Put a manual excerpt on a post when you want a deck under the title.
6. Group a sequence with the **Series** taxonomy (Posts → Series). Parts are ordered by publish date, oldest first.

Requires WordPress 6.4+ and PHP 7.4+.

## What you get

- Journal home with a lead note and a numbered index
- Topics (categories), tags, author archives, and series archives
- Reading time, word count, and article JSON-LD
- Sticky “On this page” outline generated from `h2` and `h3` headings
- Previous / next part inside a series
- Related notes from the same topic
- Code blocks with a copy button
- Note, warning, pull-quote, and definition block patterns
- Night theme, stored in `localStorage`
- Editor styles and a color palette that match the front

## Sample notes

From the WordPress directory, with [WP-CLI](https://wp-cli.org/) installed and the theme active:

```bash
wp eval-file /path/to/aswordpress/demo/seed.php
```

The script is idempotent. It adds topics, a two-part series, five notes, an About page, and a primary menu if you do not already have one.

## Fonts

Fraunces, Source Serif 4, and IBM Plex Mono are bundled under the SIL Open Font License. See `schematic/assets/fonts/OFL.txt`.

## License

The theme is GPL-2.0-or-later.
