# SEO in PHP — What We Added and Why

Search Engine Optimization (SEO) is about helping search engines understand your page so they can show it to the right people. PHP is excellent for SEO because it renders full HTML on the server — crawlers like Googlebot get the complete page immediately, with no JavaScript to wait for.

---

## 1. `<title>` Tag

```html
<title>Albums — Kutumba Band</title>
```

**What it is:** The text shown in the browser tab and as the clickable blue headline in Google search results.

**Rules:**
- Keep it under 60 characters or Google will truncate it.
- Every page should have a unique, descriptive title.
- Format: `Page Name — Site Name` is a common pattern.

**In our code:** Set via `$pageTitle` in each page file (e.g. `about.php` sets `$pageTitle = 'About Us'`). The layout combines it: `$pageTitle . ' — Kutumba Band'`.

---

## 2. `<meta name="description">`

```html
<meta name="description" content="Meet the musicians of Kutumba...">
```

**What it is:** The snippet of text Google shows below the title in search results. It doesn't affect ranking directly, but it affects click-through rate — a good description convinces people to click.

**Rules:**
- 150–160 characters max.
- Describe what's on *this specific page*, not just the site.
- Include natural keywords but write for humans, not robots.

**In our code:** Defined in `public/index.php` in the `$pageMeta` array — one description per route. Passed to `layout.php` as `$metaDesc`.

---

## 3. `<link rel="canonical">`

```html
<link rel="canonical" href="http://localhost/.../index.php?page=about">
```

**What it is:** Tells Google which URL is the "official" version of this page.

**Why it matters:** The same page might be reachable via multiple URLs — `?page=about`, `?page=about&ref=twitter`, with or without a trailing slash. Without a canonical tag, Google might split your "ranking power" across all those URLs. With it, all credit goes to one URL.

**In our code:** Built automatically in `layout.php` from `$_SERVER['REQUEST_URI']` and `$currentPage`.

---

## 4. Open Graph Tags (`og:`)

```html
<meta property="og:title"       content="Albums — Kutumba Band">
<meta property="og:description" content="Explore albums by Kutumba...">
<meta property="og:image"       content="images/og-default.jpg">
<meta property="og:type"        content="website">
<meta property="og:url"         content="https://...">
```

**What it is:** A protocol created by Facebook (now used by all social platforms) that controls how your link looks when shared on WhatsApp, Facebook, LinkedIn, Slack, etc.

**Why it matters:** Without OG tags, WhatsApp just shows the raw URL. With them, it shows a card with your title, description, and a large image.

**`og:image` size:** Should be **at least 1200 × 630 px** for best display. A blurry or missing image looks unprofessional.

**In our code:** Set in `layout.php`. The `$ogImage` variable can be overridden per page (e.g., an albums page could use the most recent album cover).

---

## 5. Twitter Card Tags

```html
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:title"       content="...">
<meta name="twitter:description" content="...">
<meta name="twitter:image"       content="...">
```

**What it is:** Same idea as Open Graph but specifically for X (formerly Twitter). If both are present, Twitter prefers its own tags; other platforms fall back to `og:` tags.

`summary_large_image` = show a big image above the title (vs. `summary` = small thumbnail beside text).

---

## 6. Image `alt` Attribute

```html
<img src="members/rajan.jpg" alt="Rajan Shrestha playing sarangi, Kutumba member">
```

**What it is:** Text that describes an image for:
1. Screen readers (visually impaired users)
2. Search engines (Google Images ranking)
3. Broken image fallback (shown if the image fails to load)

**Rules:**
- Be specific and descriptive: `"Rajan Shrestha playing sarangi"` not `"photo"` or `"image1.jpg"`.
- Include relevant keywords naturally: the instrument name, the person's role.
- Don't start with "Image of..." — Google already knows it's an image.
- Decorative images that add no meaning: use `alt=""` (empty, not missing).

**In our code:** Added `alt_text` column to `members`, `instruments`, and `albums` tables. Admins fill this in the edit form. If left empty, a fallback is auto-generated from the name + role.

---

## 7. Structured Data — JSON-LD

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "MusicGroup",
  "name": "Kutumba",
  "description": "Nepal's premier folk instrumental ensemble...",
  "genre": ["Folk music", "Traditional music", "World music"],
  "member": [
    {
      "@type": "OrganizationRole",
      "roleName": "Sarangi",
      "member": { "@type": "Person", "name": "Rajan Shrestha" }
    }
  ]
}
</script>
```

**What it is:** Machine-readable metadata embedded in the page using the [Schema.org](https://schema.org) vocabulary. Google reads this to understand *what type of thing* your page is — not just what words appear on it.

**Why JSON-LD?** Three formats exist (Microdata, RDFa, JSON-LD). Google recommends JSON-LD because it's separate from the HTML, easier to write, and easier to update without touching the visible content.

**`@context`:** The vocabulary we're using (Schema.org has types for Person, Organization, MusicGroup, Event, Product, Recipe, Movie, etc.)

**`@type`:** The specific type. `MusicGroup` tells Google this is a band, not a company. This can unlock a "Knowledge Panel" in search results showing the band photo, members, and genre.

**Common types for web projects:**
| @type | Used for |
|---|---|
| `Organization` | Company, NGO |
| `MusicGroup` | Band, ensemble |
| `Person` | Author, artist profile |
| `Event` | Concert, conference |
| `Product` | Item for sale |
| `BreadcrumbList` | Navigation trail |
| `FAQPage` | FAQ section |

**In our code:** Set in `home.php` as `$jsonLd = [...]`. The member list is pulled dynamically from the DB. `layout.php` outputs it as a `<script type="application/ld+json">` block if `$jsonLd` is set.

---

## 8. `robots.txt`

```
User-agent: *
Disallow: /admin/
Allow: /
```

**What it is:** A plain text file at the root of your site that tells crawlers which URLs they are allowed to visit.

**`User-agent: *`** means "applies to all crawlers". You can target specific ones (`User-agent: Googlebot`).

**`Disallow`** does NOT prevent the page from being indexed if it's linked from elsewhere — it only prevents the crawler from visiting the URL. For true blocking, use `<meta name="robots" content="noindex">` on the page itself.

**Why block `/admin/`?** Admin pages contain no public content, shouldn't appear in Google, and appearing in search results is a minor security signal (it advertises that you have an admin panel).

---

## How PHP Helps SEO

| Advantage | Explanation |
|---|---|
| **Server-side rendering** | Crawlers get full HTML — no JavaScript to wait for |
| **Dynamic `<title>` and `<meta>`** | PHP can set different meta per page, per DB record |
| **Dynamic JSON-LD** | Pull real data (members, events) into structured data |
| **Fast TTFB** | PHP + MySQL is fast; Google uses page speed as a ranking signal |
| **Clean redirects** | PHP can do 301 redirects (permanent) which pass SEO value |

React/Next.js apps need SSR or SSG to match this. A plain PHP site gets it for free.

---

## Checklist — Is a Page SEO-Ready?

- [ ] Unique `<title>` under 60 chars
- [ ] `<meta name="description">` under 160 chars
- [ ] `<link rel="canonical">` present
- [ ] All images have a meaningful `alt` attribute
- [ ] One `<h1>` per page (not zero, not two)
- [ ] Open Graph tags for social sharing
- [ ] `/admin/` blocked in `robots.txt`
- [ ] JSON-LD structured data on key pages (home, about)
