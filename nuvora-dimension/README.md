# Dimension WordPress Theme

A fully functional WordPress theme based on the **Dimension** template by HTML5 UP.

---

## Installation

1. Upload the `dimension-theme` folder to `/wp-content/themes/`
2. Go to **Appearance → Themes** and activate **Dimension**
3. Go to **Settings → Reading** → set **Front page displays** to "A static page", then create a new page and assign it as the Front page
4. Go to **Appearance → Theme Options** to configure everything

---

## Theme Options (Appearance → Theme Options)

| Setting | Description |
|---|---|
| Site Title | The large heading in the hero |
| Subtitle / Tagline | Text below the title |
| Logo Icon Class | FontAwesome icon (e.g. `fa-gem`, `fa-rocket`) |
| Background Color | Dark background color |
| Background Image | Optional fullscreen background photo |
| Accent Color | Button/highlight color |
| Navigation Panels | One per line: `Label,panel-id` |
| Footer Text | Copyright text in footer |
| Social Links | Twitter, Facebook, Instagram, GitHub, LinkedIn |
| Contact Form | Enable/disable + recipient email |

---

## Gutenberg Blocks

Two custom blocks are included under the **Dimension Theme** category:

### 1. Dimension Panel
The main building block. Add one per navigation section:
- Set the **Panel ID** to match your nav item (e.g. `intro`, `work`, `about`)
- Set the **Panel Title**
- Optionally add an image
- Write your content inside
- Toggle on **Contact Form** or **Social Icons** as needed

### 2. Dimension Hero Header
Informational block — shows a preview of the hero header. Actual output is controlled by Theme Options.

---

## How to Build Your Homepage

1. Create a new Page (e.g. "Home")
2. Set it as your Front Page (Settings → Reading)
3. In the Gutenberg editor, add **Dimension Panel** blocks — one per section
4. Set Panel IDs to match your nav items in Theme Options
5. In **Theme Options → Navigation Panels**, add lines like:
   ```
   Intro,intro
   Work,work
   About,about
   Contact,contact
   ```

---

## Navigation Panel Format

In Theme Options, each line follows this format:
```
Label,panel-id
```
Example:
```
Intro,intro
Work,work
About,about
Contact,contact
Portfolio,portfolio
```

---

## License

Based on Dimension by HTML5 UP — Creative Commons Attribution 3.0 (https://html5up.net/license)
WordPress theme port created with ❤️
