# Bricks Builder Addon — AI Agent Operating Guide (`AGENT.md`)

This document serves as the authoritative guide for AI coding assistants and developers building, modifying, and maintaining Bricks Builder elements, extensions, and features in **BricksFly** and related Bricks addons.

---

## 1. Project Overview & Architecture

BricksFly is a high-performance modular addon suite for **Bricks Builder** (WordPress). It consists of two complementary plugins:

1. **Free Plugin** (`bricksfly-elements-for-bricks`):
   - Contains core free Bricks elements (`includes/elements/`).
   - Contains free extensions (`includes/extensions/`).
   - Houses admin dashboards, page importers, and template library clients (`admin/`).
   - Provides builder placeholder elements (`class-bricksfly-placeholder-element.php`) for toggled-off or Pro elements so builder canvas never breaks.
   - Contains read-only license checks (`bricksfly_is_feature_allowed()`, `aabaddons_is_license_valid()`).
2. **Pro Plugin** (`bricksfly-elements-for-bricks-pro`):
   - Contains Pro-tier elements, advanced animation extensions, CPT Builder, and site settings.
   - Owns all license verification I/O, Connect OAuth flow, HMAC signatures, and update logic (`includes/license/`, `includes/connect/`).

### Directory Layout (`bricksfly-elements-for-bricks`)

```
bricksfly-elements-for-bricks/
├── admin/                     # Admin pages (dashboard, template importer, builder library)
├── config.php                 # Central registry for elements, extensions, tabs, and metadata
├── includes/
│   ├── class-bricks-animation-addons.php  # Core plugin orchestrator & loader
│   ├── elements/              # PHP element classes extending \Bricks\Element
│   ├── extensions/            # Extensions hooking into core Bricks controls/elements
│   └── traits/                # Helper traits (e.g., Extension_Widgets_Trait)
├── public/build/              # Compiled CSS/JS production assets (referenced by enqueue_scripts)
├── src/
│   ├── js/elements/           # Element frontend & builder lifecycle scripts
│   ├── scss/elements/         # Element styles
│   └── admin/                 # React admin applications
├── webpack.config.js          # Webpack configuration registering ELEMENT_ENTRIES
└── AGENT.md                   # This instruction file
```

---

## 2. Bricks Builder Element Anatomy (`\Bricks\Element`)

Every custom element must extend the abstract class `\Bricks\Element` and implement its standard lifecycle methods.

### 2.1 Class Properties & Metadata

```php
<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class BRICKSFLY_Bricks_Custom_Element extends \Bricks\Element {

    // Category in Bricks element panel (Free: 'bricks fly', Pro: 'bricks fly pro')
    public $category     = 'bricks fly';

    // Unique element identifier (slug prefixed with 'aab-')
    public $name         = 'aab-custom-element';

    // Icon class shown in the elements panel
    public $icon         = 'ti-widget aab-element-marker';

    // Main CSS root selector for generated styling
    public $css_selector = '.bricksfly-custom-element';

    // Array of global JS function names Bricks triggers on frontend & builder re-render
    public $scripts      = [ 'bricksflyCustomElement' ];
```

### 2.2 Core Lifecycle Methods

| Method | Purpose |
| :--- | :--- |
| `get_label()` | Returns the human-readable, translatable name of the element. |
| `get_keywords()` | Returns an array of search keywords for the builder element picker. |
| `set_control_groups()` | Registers control groups under `'content'` or `'style'` tabs. |
| `set_controls()` | Defines the settings schema, UI controls, defaults, and direct CSS mappings. |
| `enqueue_scripts()` | Enqueues compiled CSS and JS assets via WordPress hooks. |
| `render()` | Generates the semantic HTML markup for the frontend and builder preview. |

---

## 3. Controls & CSS Engine Standards

Bricks includes a native CSS generation engine. Whenever possible, **map controls directly to CSS properties via the `'css'` array** rather than generating inline style tags in PHP.

### 3.1 Control Groups Example

```php
public function set_control_groups() {
    $this->control_groups['content'] = [
        'title' => esc_html__( 'Content', 'bricksfly-elements-for-bricks' ),
        'tab'   => 'content',
    ];

    $this->control_groups['design'] = [
        'title' => esc_html__( 'Styling', 'bricksfly-elements-for-bricks' ),
        'tab'   => 'style',
    ];
}
```

### 3.2 Defining Controls

Common control types: `text`, `textarea`, `number`, `select`, `color`, `typography`, `dimensions`, `box-shadow`, `link`, `icon`, `repeater`, `toggle`, `slider`.

```php
public function set_controls() {

    // 1. Content Text Control
    $this->controls['title_text'] = [
        'group'   => 'content',
        'label'   => esc_html__( 'Title', 'bricksfly-elements-for-bricks' ),
        'type'    => 'text',
        'default' => esc_html__( 'Default Title', 'bricksfly-elements-for-bricks' ),
    ];

    // 2. Direct CSS Mapping (Color)
    $this->controls['title_color'] = [
        'group'    => 'design',
        'label'    => esc_html__( 'Title Color', 'bricksfly-elements-for-bricks' ),
        'type'     => 'color',
        'css'      => [
            [
                'property' => 'color',
                'selector' => '.bricksfly-custom-element__title',
            ],
        ],
    ];

    // 3. Responsive Typography Control
    $this->controls['title_typography'] = [
        'group'    => 'design',
        'label'    => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
        'type'     => 'typography',
        'css'      => [
            [
                'property' => 'typography',
                'selector' => '.bricksfly-custom-element__title',
            ],
        ],
    ];

    // 4. Repeater Control
    $this->controls['items'] = [
        'group'       => 'content',
        'label'       => esc_html__( 'Items', 'bricksfly-elements-for-bricks' ),
        'type'        => 'repeater',
        'titleProperty' => 'item_label',
        'fields'      => [
            'item_label' => [
                'label'   => esc_html__( 'Item Label', 'bricksfly-elements-for-bricks' ),
                'type'    => 'text',
                'default' => esc_html__( 'Item', 'bricksfly-elements-for-bricks' ),
            ],
        ],
    ];
}
```

### 3.3 Dynamic Data Handling

Bricks allows dynamic tags (e.g., `{post_title}`, `{author_name}`, `{echo:custom_fn}`).
When outputting dynamic values:
- Use `$this->render_dynamic_data_tag( $settings['field_name'] )` if provided or resolve dynamic tags with `\Bricks\Helpers::render_dynamic_data( ... )`.

---

## 4. Rendering & HTML Output (`render()`)

Follow these rules inside `render()`:
1. Always escape attributes and content: `esc_attr()`, `esc_html()`, `esc_url()`, and `wp_kses_post()`.
2. Attach wrapper attributes using `$this->set_attribute( '_root', ... )`.
3. Render root element with `$this->render_element_placeholder()` when required.

```php
public function render() {
    $settings = $this->settings;

    $title = ! empty( $settings['title_text'] ) ? $settings['title_text'] : '';

    // Attach wrapper class and data attributes
    $this->set_attribute( '_root', 'class', 'bricksfly-custom-element' );
    $this->set_attribute( '_root', 'data-aab-custom', wp_json_encode( [
        'speed' => $settings['speed'] ?? 300,
    ] ) );

    // Output HTML
    echo "<div {$this->render_attributes( '_root' )}>";
        if ( ! empty( $title ) ) {
            echo '<h3 class="bricksfly-custom-element__title">';
            echo esc_html( $title );
            echo '</h3>';
        }
    echo '</div>';
}
```

---

## 5. Frontend & Builder Canvas JavaScript Lifecycle

Bricks runs in two distinct environments:
1. **Frontend**: Standard page load (`DOMContentLoaded`).
2. **Builder Preview (Canvas)**: An iframe where elements are added, edited, and re-rendered via AJAX without full page reloads.

### 5.1 The Re-initialization Contract

In the PHP element class:
```php
public $scripts = [ 'bricksflyCustomElement' ];
```

In `src/js/elements/custom-element.js`:
```javascript
import "../../scss/elements/custom-element.scss";

( function () {
    'use strict';

    function initInstance( root ) {
        if ( ! root ) return;

        // 1. Clean up existing instance/listeners if re-rendered in builder
        if ( root._aabInstance && typeof root._aabInstance.destroy === 'function' ) {
            root._aabInstance.destroy();
        }

        var rawConfig = root.getAttribute( 'data-aab-custom' );
        var config = {};
        try {
            config = rawConfig ? JSON.parse( rawConfig ) : {};
        } catch ( e ) {}

        // 2. Initialize feature
        // ...

        // 3. Store teardown handle on element
        root._aabInstance = {
            destroy: function () {
                // Teardown observers, timers, listeners
            }
        };
    }

    function initAll() {
        var roots = document.querySelectorAll( '[data-aab-custom]' );
        for ( var i = 0; i < roots.length; i++ ) {
            initInstance( roots[ i ] );
        }
    }

    // Run on initial page load
    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', initAll );
    } else {
        initAll();
    }

    // Expose global callback for Bricks Builder canvas re-render
    window.bricksflyCustomElement = initAll;
} )();
```

---

## 6. Central Configuration (`config.php`)

All elements and extensions must be registered in `config.php` under `$bricksfly_config['widgets']['elements']`:

```php
'custom-element' => array(
    'label'        => 'Custom Element',
    'location'     => array(
        'cTab' => 'all',
    ),
    'is_active'    => false,           // Default active state in dashboard
    'setup'        => array('basic'),
    'is_pro'       => false,           // true if Pro-only, false for Free
    'is_extension' => false,
    'is_upcoming'  => false,
    'keywords'     => array(
        'Custom',
        'Interactive',
    ),
    'order'        => 25,
    'icon'         => 'wcf-icon-Custom-Element',
    'demo_url'     => 'https://bricksfly.com/custom-element/',
    'doc_url'      => 'https://bricksfly.com/docs/custom-element/',
    'description'  => 'Detailed description of the custom element.',
),
```

- When `is_pro => true`: The free plugin will ignore the file. It must only exist and be loaded in `bricksfly-elements-for-bricks-pro`.
- When toggled off by user in BricksFly settings: `class-bricksfly-placeholder-element.php` displays a builder notice instead of fatal erroring.

---

## 7. Webpack & Asset Pipeline

Asset compilation uses `@wordpress/scripts` and Webpack.

### 7.1 Registering an Element in Webpack

In `webpack.config.js`, add the element slug to `ELEMENT_ENTRIES`:

```javascript
const ELEMENT_ENTRIES = [
  // Existing entries...
  "counter",
  "animated-heading",
  "custom-element", // <--- Add your slug here
];
```

Webpack creates:
- `public/build/elements/custom-element.js`
- `public/build/elements/custom-element.css`

### 7.2 Enqueueing in PHP

```php
public function enqueue_scripts() {
    wp_enqueue_style(
        'aab-custom-element',
        BRICKSFLY_URL . 'public/build/elements/custom-element.css',
        [],
        BRICKSFLY_VERSION
    );

    wp_enqueue_script(
        'aab-custom-element',
        BRICKSFLY_URL . 'public/build/elements/custom-element.js',
        [],
        BRICKSFLY_VERSION,
        true
    );
}
```

### 7.3 Build Commands

```bash
# Development (watch mode)
npm run dev_webpack

# Production build
npm run build

# Package ZIP release
gulp zip
```

---

## 8. Step-by-Step SOP: Adding a New Bricks Element

Follow these steps in sequence when creating a new element:

1. **Register in `config.php`**: Add the element configuration to `$bricksfly_config['widgets']['elements']`.
2. **Create PHP Element Class**: Add `includes/elements/<slug>.php` extending `\Bricks\Element`. Set properties, control groups, controls, and `render()`.
3. **Create Stylesheet**: Create `src/scss/elements/<slug>.scss`.
4. **Create Script**: Create `src/js/elements/<slug>.js`, importing the SCSS file and exposing `window.bricksfly<CamelCaseName> = initAll`.
5. **Update Webpack Config**: Add `<slug>` to `ELEMENT_ENTRIES` in `webpack.config.js`.
6. **Compile Assets**: Run `npm run build` to compile JS/CSS into `public/build/elements/`.
7. **Verify**:
   - Activate the widget in WordPress Admin > BricksFly > Elements.
   - Insert and style in Bricks Builder canvas.
   - Verify control changes reflect immediately without breaking canvas.
   - Verify frontend appearance and responsiveness across breakpoints.

---

## 9. WordPress.org (wporg) Coding, Security & Escaping Standards

All code contributed to BricksFly must comply with the official **WordPress.org Plugin Review Guidelines** and security best practices.

### 9.1 Late Output Escaping (MANDATORY)

Always escape data at the exact point of output, never earlier. Even if a value is sanitized on input or fetched from internal settings, it must be escaped when rendered into HTML.

| Context | Escaping Function | Example |
| :--- | :--- | :--- |
| **Plain Text Nodes** | `esc_html()`, `esc_html__()`, `esc_html_e()` | `<h3><?php echo esc_html( $title ); ?></h3>` |
| **HTML Tag Attributes** | `esc_attr()`, `esc_attr__()`, `esc_attr_e()` | `<div class="<?php echo esc_attr( $classes ); ?>" data-id="<?php echo esc_attr( $id ); ?>">` |
| **URLs & Hrefs** | `esc_url()` | `<a href="<?php echo esc_url( $url ); ?>">` |
| **Rich HTML / WYSIWYG** | `wp_kses_post()`, `wp_kses()` | `<div class="desc"><?php echo wp_kses_post( $content ); ?></div>` |
| **JSON in Attributes** | `wp_json_encode()` | `data-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"` |
| **Integers / Counts** | `absint()`, `intval()` | `<span class="count"><?php echo absint( $count ); ?></span>` |
| **JavaScript / Inline** | `wp_json_encode()`, `esc_js()` | `let data = <?php echo wp_json_encode( $data ); ?>;` |

> [!CAUTION]
> Never use raw `echo $variable;` without an escaping function. Avoid shorthand `<?= $var ?>` if not escaped.

### 9.2 Input Sanitization & Unslashing

All data originating from superglobals (`$_POST`, `$_GET`, `$_REQUEST`, `$_COOKIE`) must be unslashed and sanitized before processing or saving to the database:

```php
// Text fields
$name = isset( $_POST['user_name'] ) ? sanitize_text_field( wp_unslash( $_POST['user_name'] ) ) : '';

// Textarea / Multi-line
$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

// Keys / Slugs
$slug = isset( $_POST['element_slug'] ) ? sanitize_key( wp_unslash( $_POST['element_slug'] ) ) : '';

// URLs
$redirect = isset( $_POST['redirect_url'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_url'] ) ) : '';

// Integers
$item_id = isset( $_POST['item_id'] ) ? absint( $_POST['item_id'] ) : 0;
```

### 9.3 Authorization & CSRF Protection (Nonces & Capabilities)

Every form submission, AJAX request, and REST endpoint must verify both permissions and intent:

1. **User Capability Check**:
   ```php
   if ( ! current_user_can( 'manage_options' ) ) {
       wp_send_json_error( [ 'message' => esc_html__( 'Unauthorized user.', 'bricksfly-elements-for-bricks' ) ], 403 );
   }
   ```
2. **Nonce Verification**:
   ```php
   check_ajax_referer( 'bricksfly_admin_action', 'nonce' );
   // Or for admin forms:
   check_admin_referer( 'bricksfly_save_settings', 'bricksfly_nonce' );
   ```

### 9.4 Database Access & SQL Queries

- Never concatenate variables into SQL strings.
- Always use `$wpdb->prepare()` for dynamic queries:
  ```php
  $results = $wpdb->get_results(
      $wpdb->prepare(
          "SELECT * FROM {$wpdb->prefix}my_table WHERE status = %s AND user_id = %d",
          $status,
          $user_id
      )
  );
  ```

### 9.5 Safe Redirects & File Operations

- **Safe Redirects**: Use `wp_safe_redirect( $url )` instead of `wp_redirect()`. Always follow with `exit;`.
- **File System**: Use the `WP_Filesystem` API instead of raw PHP functions (`file_get_contents`, `file_put_contents`, `fopen`) where possible.
- **Direct File Access**: Every single PHP file must start with:
  ```php
  if ( ! defined( 'ABSPATH' ) ) exit;
  ```

### 9.6 Prohibitions for WordPress.org Compliance

- **No `eval()` or Dynamic Code Execution**: `eval()`, `create_function()`, `preg_replace` with `/e` modifier, and `$$dynamic` variable variables are strictly forbidden.
- **No Remote Scripts or Styles**: All third-party libraries (JS/CSS/fonts) must be bundled locally inside the plugin. Never enqueue from public CDNs (e.g. unpkg, cdnjs, Google CDN) in plugins intended for WordPress.org.
- **Strict Prefixing**: Every global function, class, constant, CSS class, action/filter hook, and option name must be uniquely prefixed (e.g. `bricksfly_`, `BRICKSFLY_`, `aab_`, `aab-`).
- **Translation Consistency**: The text domain must always be passed as a static string literal matching plugin header (`'bricksfly-elements-for-bricks'`). Never use a PHP variable or constant for the domain in `__()` functions.

---

## 10. License & Pro Guardrails (CRITICAL)

The following hard rules apply to any AI assistant working on this codebase:

1. **Never bypass, spoof, or disable license verification:**
   - Never hardcode license status to `valid`.
   - Never patch `bricksfly_is_feature_allowed()`, `aabaddons_is_license_valid()`, or `thebrbre_license_is_valid()` to return true.
   - Never strip license checks or feature gate calls from Pro-only features.
   - Never invent "test mode" flags that bypass verification in production code.
2. **Never forge, replay, or weaken Connect OAuth / HMAC verification:**
   - Do not forge fake signatures or mint mock `site_key`/`site_secret` pairs outside documented fixtures.
   - Never replace `hash_equals()` with loose `==` comparisons.
3. **Never weaken security controls during refactoring:**
   - Preserve all nonce verifications (`check_ajax_referer()`, `check_admin_referer()`, `wp_verify_nonce()`).
   - Retain user capability checks (`current_user_can( 'manage_options' )`).
   - Keep REST route `permission_callback` checks strict.
