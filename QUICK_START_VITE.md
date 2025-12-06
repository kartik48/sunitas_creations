# Quick Start: Setting Up Vite on Your Mac

Follow these steps in order on your local machine.

## ☐ Step 1: Install Node.js

### Check if you already have it:
```bash
node --version
```

If you see a version number (like v20.x.x), skip to Step 2!

### If not installed, use Homebrew:
```bash
# Install Homebrew (if you don't have it)
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install Node.js
brew install node

# Verify
node --version
npm --version
```

**Expected output:**
```
v20.11.0  (or similar)
10.4.0    (or similar)
```

---

## ☐ Step 2: Navigate to Your Project

```bash
cd /Users/kartik/Sunitas_Creations
```

Or drag the folder to Terminal!

---

## ☐ Step 3: Install Dependencies

```bash
npm install
```

**What you'll see:**
```
added 234 packages in 45s
```

**What it does:**
- Downloads Vite, Tailwind, and all packages
- Creates `node_modules/` folder
- Takes about 30-60 seconds

**Note:** The `node_modules/` folder is already in `.gitignore` - don't commit it!

---

## ☐ Step 4: Build Assets for Production

```bash
npm run build
```

**What you'll see:**
```
vite v7.0.7 building for production...
✓ built in 2.34s
✓ resources/css/app.css → public/build/assets/app-abc123.css (8.2 KB)
✓ resources/js/app.js → public/build/assets/app-xyz789.js (1.8 KB)
```

**What it creates:**
- `public/build/manifest.json` ← This is what tests need!
- `public/build/assets/app-*.css` ← Optimized CSS (~8KB)
- `public/build/assets/app-*.js` ← Optimized JS (~2KB)

---

## ☐ Step 5: Verify Tests Now Pass

```bash
php artisan test
```

**Expected result:**
```
  PASS  Tests\Feature\ShopPageTest
  ✓ shop page loads successfully
  ✓ shop displays active products
  ... (all 15 tests passing)

  PASS  Tests\Feature\HomePageTest
  ✓ homepage loads successfully
  ... (all 8 tests passing)

  Tests:  83 passed (350 assertions)
  Duration: 8.24s
```

🎉 All tests should be GREEN now!

---

## ☐ Step 6: Try Development Mode (Hot Reload!)

### Start the dev server:
```bash
npm run dev
```

**What you'll see:**
```
VITE v7.0.7  ready in 312 ms

➜  Local:   http://localhost:5173/
➜  Network: use --host to expose

LARAVEL v12.0.0  plugin v2.0.0

➜  APP_URL: http://localhost:8000
```

**IMPORTANT:** Keep this terminal window open!

### In a NEW terminal window, start Laravel:
```bash
cd /Users/kartik/Sunitas_Creations
php artisan serve
```

### Now visit:
```
http://localhost:8000
```

### Try the magic:
1. Open `resources/views/shop.blade.php` in VS Code
2. Change something (like a text color)
3. **Save the file**
4. **Watch your browser update automatically!** 🎉

No manual refresh needed!

---

## 🎯 Understanding the Commands

### `npm install`
- **When to run:** First time, or after pulling new code
- **What it does:** Installs dependencies from package.json
- **How long:** 30-60 seconds
- **Run again?** Only when package.json changes

### `npm run build`
- **When to run:** Before deploying, before running tests
- **What it does:** Creates optimized production files
- **How long:** 2-5 seconds
- **Output:** Files in `public/build/`

### `npm run dev`
- **When to run:** During development
- **What it does:** Watches files, hot-reloads changes
- **How long:** Runs until you stop it (Ctrl+C)
- **Keep open:** Yes! Run in background while coding

---

## 🚀 Your New Development Workflow

### Starting work:
```bash
# Terminal 1: Start Vite
npm run dev

# Terminal 2: Start Laravel
php artisan serve
```

### While coding:
- Edit any `.blade.php`, `.css`, or `.js` file
- **Save**
- **Browser updates instantly!** ⚡

### Before committing:
```bash
# Build production assets
npm run build

# Run tests
php artisan test

# Commit (including public/build/ files)
git add .
git commit -m "Your message"
git push
```

---

## 📊 Before & After Comparison

### Before (CDN Tailwind):
```
Page load: 3,000 KB (entire Tailwind library)
Tests: 32 failing (missing Vite manifest)
```

### After (With Vite):
```
Page load: 10 KB (only CSS you use - 300x smaller!)
Tests: 83 passing ✅
Development: Hot reload (instant updates)
```

---

## 🛠️ Troubleshooting

### Issue: "command not found: npm"
**Solution:** Node.js not installed. Go back to Step 1.

### Issue: "Cannot find module 'vite'"
**Solution:** Run `npm install` again.

### Issue: Tests still failing
**Solution:** Make sure you ran `npm run build` successfully.

### Issue: Changes not appearing in browser
**Solution:**
- Make sure `npm run dev` is running
- Hard refresh browser (Cmd+Shift+R)
- Check terminal for errors

### Issue: "Port 5173 already in use"
**Solution:** Stop other Vite processes or use a different port:
```bash
npm run dev -- --port 5174
```

---

## ✅ Success Checklist

After completing all steps, you should have:

- ☐ Node.js installed (`node --version` works)
- ☐ Dependencies installed (`node_modules/` folder exists)
- ☐ Assets built (`public/build/manifest.json` exists)
- ☐ All 83 tests passing (green)
- ☐ Hot reload working (`npm run dev` and browser auto-updates)

---

## 🎓 What You've Learned

1. ✅ **Node.js** - JavaScript runtime for build tools
2. ✅ **npm** - Package manager (like Composer for PHP)
3. ✅ **Vite** - Fast build tool for frontend assets
4. ✅ **Hot Module Replacement** - Instant updates without refresh
5. ✅ **Production optimization** - Tree-shaking, minification
6. ✅ **Modern workflow** - Industry-standard Laravel development

---

## 🚀 Next Steps

Once Vite is working:
1. Read through `VITE_GUIDE.md` for deeper understanding
2. Customize `tailwind.config.js` with more Warli colors
3. Try adding custom CSS in `resources/css/app.css`
4. Experiment with hot reload - it's addictively fast!

---

**Need help?** Check `VITE_GUIDE.md` for detailed explanations!
