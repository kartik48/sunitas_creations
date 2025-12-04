# Vite + Laravel Guide

## What You Have Already

Your project is already configured with Vite! Here's what's set up:

### 1. `package.json` - Node.js Dependencies
```json
{
  "scripts": {
    "dev": "vite",           // Start development server
    "build": "vite build"    // Build for production
  },
  "devDependencies": {
    "vite": "^7.0.7",              // The build tool
    "laravel-vite-plugin": "^2.0.0", // Laravel integration
    "tailwindcss": "^3.1.0",       // Tailwind CSS
    "@tailwindcss/forms": "^0.5.2"  // Form styling plugin
  }
}
```

### 2. `vite.config.js` - Vite Configuration
```js
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,  // Auto-refresh browser on file changes
        }),
    ],
});
```

### 3. `tailwind.config.js` - Tailwind Configuration
```js
export default {
    content: [
        './resources/views/**/*.blade.php',  // Scan all Blade files
    ],
    theme: {
        extend: {
            // Your custom theme goes here!
        },
    },
    plugins: [forms],  // Tailwind Forms plugin included
};
```

### 4. `resources/css/app.css` - Main CSS File
```css
@tailwind base;       // Reset + base styles
@tailwind components; // Component classes
@tailwind utilities;  // Utility classes like 'flex', 'text-center'
```

## How It Works: The Workflow

### Development Mode (npm run dev)
```
1. You edit: resources/css/app.css or .blade.php files
2. Vite detects changes instantly
3. Browser updates automatically (no refresh!)
4. Super fast feedback loop
```

### Production Mode (npm run build)
```
1. Vite scans all your Blade files
2. Finds which Tailwind classes you actually use
3. Generates tiny, optimized CSS file (~8KB)
4. Minifies JavaScript
5. Creates versioned files (app.abc123.css)
```

## Setting It Up (One-Time)

### Step 1: Install Node.js Dependencies
```bash
npm install
```
This installs all packages from package.json (~30 seconds)

### Step 2: Build Assets
```bash
npm run build
```
This creates `public/build/manifest.json` and compiled assets

## Development Workflow

### Start Dev Server
```bash
npm run dev
```
Keep this running while you code!

**What happens:**
- Vite watches your files for changes
- Updates browser instantly when you save
- Shows compilation errors in terminal
- Hot Module Replacement (HMR) = instant updates

### Edit Your Files
While `npm run dev` is running:
1. Edit any `.blade.php` file → Browser refreshes automatically
2. Edit `resources/css/app.css` → CSS updates without refresh
3. Edit `resources/js/app.js` → JS reloads automatically

## Upgrading Your Project to Use Vite

Let's upgrade your custom pages from CDN Tailwind to Vite!

### Current (CDN Approach):
```html
<!-- home.blade.php, shop.blade.php, product.blade.php -->
<script src="https://cdn.tailwindcss.com"></script>
<style>
    :root {
        --terracotta: #C85C3F;
        --warm-brown: #8B4513;
        --cream: #F5E6D3;
    }
</style>
```

### Better (Vite Approach):

**1. Add Your Warli Colors to Tailwind Config:**
```js
// tailwind.config.js
export default {
    content: ['./resources/views/**/*.blade.php'],
    theme: {
        extend: {
            colors: {
                'terracotta': '#C85C3F',
                'warm-brown': '#8B4513',
                'cream': '#F5E6D3',
                'ochre': '#CC7722',
                'dark-earth': '#3E2723',
            },
            fontFamily: {
                'serif': ['Georgia', 'serif'],
            },
        },
    },
    plugins: [forms],
};
```

**2. Use Vite in Your Views:**
```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Shop - Sunita's Creations</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream font-serif">
    <!-- Now use your custom colors directly! -->
    <nav class="bg-white">
        <h1 class="text-terracotta text-2xl font-bold">
            Sunita's Creations
        </h1>
        <span class="text-warm-brown text-sm">
            Authentic Warli & Rajasthani Art
        </span>
    </nav>
</body>
</html>
```

**Benefits:**
- `bg-terracotta` instead of `style="background-color: var(--terracotta)"`
- Auto-complete in VS Code
- Much smaller file size
- Production-ready

## Adding Custom CSS

### Option 1: Tailwind Config (Recommended)
```js
// tailwind.config.js
theme: {
    extend: {
        boxShadow: {
            'warli': '0 10px 25px rgba(200, 92, 63, 0.15)',
        },
    },
}
```
Use: `shadow-warli`

### Option 2: Custom CSS Classes
```css
/* resources/css/app.css */
@tailwind base;
@tailwind components;
@tailwind utilities;

@layer components {
    .card-hover {
        @apply transition-transform duration-300;
    }

    .card-hover:hover {
        @apply -translate-y-1 shadow-lg;
    }

    .warli-pattern {
        background-image: repeating-linear-gradient(
            45deg,
            transparent,
            transparent 35px,
            rgba(200, 92, 63, 0.05) 35px,
            rgba(200, 92, 63, 0.05) 70px
        );
    }
}
```

## Real Example: Before & After

### Before (CDN - 3MB):
```html
<script src="https://cdn.tailwindcss.com"></script>
<!-- Downloads entire Tailwind library -->
<!-- Page load: ~3MB CSS -->
```

### After (Vite - 8KB):
```html
@vite(['resources/css/app.css'])
<!-- Only includes classes you use -->
<!-- Page load: ~8KB CSS (375x smaller!) -->
```

## JavaScript with Vite

### Organize Your Code:
```js
// resources/js/components/productGallery.js
export function initGallery() {
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', (e) => {
            changeImage(e.target.dataset.src);
        });
    });
}

function changeImage(src) {
    document.getElementById('mainImage').src = src;
}
```

```js
// resources/js/app.js
import './bootstrap';
import { initGallery } from './components/productGallery';

// Run when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    initGallery();
});
```

### In Your Blade File:
```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

Vite automatically bundles and optimizes all your JS!

## Commands Reference

```bash
# Install dependencies (first time only)
npm install

# Development mode (auto-reload, fast)
npm run dev

# Production build (optimized, minified)
npm run build

# Install new package
npm install package-name --save-dev
```

## File Size Comparison

### Your Current Setup (CDN):
```
Initial page load:
- Tailwind CDN: 3,000 KB
- Total: 3,000 KB
```

### With Vite (Optimized):
```
Initial page load:
- app.css: 8 KB (only used classes)
- app.js: 2 KB
- Total: 10 KB (300x smaller!)
```

## Production Deployment

### Build for Production:
```bash
npm run build
```

This creates:
```
public/build/
├── manifest.json           # Maps files to versioned files
├── assets/
│   ├── app-abc123.css     # Minified CSS with hash
│   └── app-xyz789.js      # Minified JS with hash
```

### What Gets Committed to Git:
```
✅ resources/css/app.css
✅ resources/js/app.js
✅ tailwind.config.js
✅ vite.config.js
✅ package.json
❌ node_modules/          (add to .gitignore)
✅ public/build/           (commit built assets)
```

## Common Issues & Solutions

### Issue: "Vite manifest not found"
**Solution:**
```bash
npm install
npm run build
```

### Issue: Changes not showing
**Solution:**
- Make sure `npm run dev` is running
- Hard refresh browser (Cmd+Shift+R / Ctrl+Shift+F5)
- Check browser console for errors

### Issue: Tailwind classes not working
**Solution:**
- Check `tailwind.config.js` content paths include your files
- Restart `npm run dev`
- Build with `npm run build`

## Why Vite is Better Than Webpack

| Feature | Webpack (Old) | Vite (New) |
|---------|---------------|------------|
| Dev server start | 30-60 seconds | 1-2 seconds ⚡ |
| Hot reload | 3-5 seconds | Instant ⚡ |
| Production build | 2-5 minutes | 30 seconds ⚡ |
| Configuration | Complex | Simple ⚡ |

## Learning Resources

1. **Vite Docs**: https://vitejs.dev
2. **Laravel Vite**: https://laravel.com/docs/vite
3. **Tailwind CSS**: https://tailwindcss.com/docs

## Summary: Key Takeaways

✅ **Vite is FAST** - Instant hot reload, quick builds
✅ **Smaller bundles** - Only includes CSS you use (8KB vs 3MB)
✅ **Professional** - Industry standard for Laravel projects
✅ **Better DX** - Great developer experience with instant feedback
✅ **Production ready** - Automatic optimization and minification
✅ **Career valuable** - Expected skill for Laravel developers

## Next Steps

1. **Install Node.js** on your machine (if not already)
2. **Run `npm install`** in your project directory
3. **Run `npm run build`** to build assets
4. **Try `npm run dev`** and edit a file - see instant updates!
5. **Customize Tailwind config** with your Warli colors
6. **Migrate your views** from CDN to Vite

Vite is absolutely worth learning - it's fast, modern, and will make you more productive!
