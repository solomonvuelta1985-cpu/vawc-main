# ⚡ MIT App Inventor - 30 Minute Quick Start

## 🎯 Build Your VAW Assessment App in 30 Minutes!

---

## ✅ What You'll Build

Professional Android app that saves files to organized folders:

```
📁 /Documents/VAW_Assessments_Baggao/
├── 📁 barangay/   (individual assessments)
├── 📁 progress/   (progress snapshots)
└── 📁 final/      (final submission)
```

---

## ⚡ 5-Minute Setup

### **Step 1: Open MIT App Inventor**
1. Go to: https://appinventor.mit.edu/
2. Click **"Create Apps!"**
3. Sign in with Google account

### **Step 2: Create New Project**
1. Click **"Start new project"**
2. Name: `VAW_Assessment_Baggao`
3. Click **OK**

---

## 🎨 10-Minute Design

### **Step 3: Add Components**

**Drag these from Palette (left side):**

1. From **"User Interface"**:
   - Drag **WebViewer** → Screen
   - Properties: Width = **Fill parent**, Height = **Fill parent**

2. From **"Storage"**:
   - Drag **File** → Screen (goes to non-visible area)

3. From **"User Interface"** again:
   - Drag **Notifier** → Screen (goes to non-visible area)

**Your component tree should show:**
```
Screen1
├── WebViewer1
├── File1 (non-visible)
└── Notifier1 (non-visible)
```

### **Step 4: Upload HTML**
1. Look for **Media** panel (bottom right)
2. Click **"Upload File..."**
3. Select: `vaw_assessment_app.html`
4. Wait for upload ✅

### **Step 5: Configure File Component**
1. Click **File1** in Components
2. In Properties (right):
   - Set **Scope** to **"Shared"**

---

## 🧩 15-Minute Blocks

### **Step 6: Switch to Blocks**
Click **"Blocks"** button (top right)

### **Step 7: Build Block Set 1 - Initialize**

**What it does:** Loads your HTML when app starts

**Blocks needed:**
```
when Screen1.Initialize
└─ call WebViewer1.GoToUrl
   └─ "file:///android_asset/vaw_assessment_app.html"
```

**How to build:**
1. **Screen1** drawer → `when Screen1.Initialize`
2. **WebViewer1** drawer → `call WebViewer1.GoToUrl`
3. **Text** drawer → text block, type the path above
4. Connect them together

---

### **Step 8: Build Block Set 2 - Receive Messages**

**What it does:** Listens for save commands from JavaScript

**Blocks needed:**
```
when WebViewer1.WebViewStringChange
└─ if [contains WebViewer1.WebViewString "SAVE_FILE"]
   └─ then
      ├─ set global data to [split WebViewer1.WebViewString at "|"]
      └─ call SaveToFolder
```

**How to build:**
1. **WebViewer1** → `when WebViewer1.WebViewStringChange`
2. **Control** → `if then` block
3. **Text** → `contains` block
4. **WebViewer1** → `get WebViewer1.WebViewString`
5. **Text** → `"SAVE_FILE"`
6. **Variables** → `initialize global data to`
7. **Lists** → `split text at "|"`
8. Connect all together

---

### **Step 9: Build Block Set 3 - Save Function**

**What it does:** Saves files to designated folders

**Blocks needed:**
```
to procedure SaveToFolder
├─ set folder to [select list item [data] index 2]
├─ set filename to [select list item [data] index 3]
├─ set content to [select list item [data] index 4]
├─ set path to [join "/storage/.../Baggao/" folder "/" filename]
├─ call File1.SaveFile [content] [path]
└─ call Notifier1.ShowToast ["✅ Saved: " filename]
```

**How to build:**
1. **Procedures** → `to procedure`, name it `SaveToFolder`
2. **Variables** → Create globals: `folder`, `filename`, `content`, `path`
3. **Lists** → `select list item` (use 3 times for index 2, 3, 4)
4. **Text** → `join` block with path parts
5. **File1** → `call File1.SaveFile`
6. **Notifier1** → `call Notifier1.ShowToast`
7. Connect everything in order

**Full path to use in join block:**
```
/storage/emulated/0/Documents/VAW_Assessments_Baggao/
```

---

## 🧪 Test Your App

### **Step 10: Test with AI Companion**

1. Install **MIT AI2 Companion** from Play Store
2. In App Inventor, click **Connect** → **AI Companion**
3. Scan QR code with Companion app
4. App loads on your phone instantly! 🎉

### **Step 11: Test Functionality**

1. Select a barangay
2. Fill out assessment
3. Click **"Save Assessment"**
4. Should see toast: **"✅ Saved: filename.json"**
5. Open **Files** app on phone
6. Navigate to: **Documents** → **VAW_Assessments_Baggao**
7. Verify file is there ✅

---

## 📦 Build APK

### **Step 12: Create Installable App**

1. Click **Build** → **Android App (.apk)**
2. Wait 2-3 minutes for build
3. Download APK when ready
4. Transfer to Android device
5. Install (enable "Install from Unknown Sources" if needed)
6. Grant storage permission when app asks

---

## ✅ Verification Checklist

Make sure everything works:

- [ ] App opens without errors
- [ ] HTML form loads and displays correctly
- [ ] Can select barangay from dropdown
- [ ] Can fill out all form fields
- [ ] Can navigate between form steps
- [ ] Save button works
- [ ] Toast notification appears
- [ ] Files appear in Documents folder
- [ ] Files are in correct subfolders
- [ ] Files contain correct JSON data
- [ ] Works without internet connection

---

## 📂 Expected Folder Structure

After saving assessments, you should see:

```
📱 Your Android Phone
└── 📁 Internal Storage
    └── 📁 Documents
        └── 📁 VAW_Assessments_Baggao
            ├── 📁 barangay
            │   ├── Adaoag_2025-01-15_14-30-00.json
            │   ├── Alba_2025-01-16_09-15-00.json
            │   └── ...
            ├── 📁 progress
            │   ├── Progress_5of48_2025-01-15.json
            │   └── Progress_10of48_2025-01-16.json
            └── 📁 final
                └── VAW_Final_Submission_RichmondRosete_2025-01-20.json
```

---

## 🐛 Quick Troubleshooting

| Problem | Solution |
|---------|----------|
| **HTML not loading** | Check file is in Media, check filename spelling |
| **Files not saving** | Grant storage permission, check File1 Scope = "Shared" |
| **Toast not showing** | Check blocks are connected, check WebViewString |
| **Wrong folder** | Verify path in join block is correct |
| **"SAVE_FILE" error** | Check split delimiter is pipe "|" character |

---

## 🎯 Block Summary

You need exactly **3 block groups**:

1. **Initialize** (1 block) - Loads HTML
2. **Receive** (1 block) - Detects save command
3. **Save** (1 procedure) - Saves to folder

**Total: ~20 blocks**
**Time: ~15 minutes**

---

## 📱 Sharing Your App

### **Option 1: Direct Install**
- Share APK file via Bluetooth, email, or USB
- Users install directly

### **Option 2: QR Code**
- App Inventor provides QR code
- Users scan to install

### **Option 3: Google Play Store**
- Sign APK with key
- Upload to Play Store (requires developer account)

---

## 🎉 You're Done!

**Congratulations!** You've built a professional Android app in 30 minutes!

Your app now:
- ✅ Loads professional clean UI
- ✅ Saves files to organized folders
- ✅ Works offline
- ✅ Shows notifications
- ✅ Is installable on any Android device

---

## 📚 Additional Resources

**Need more help?**
- `MIT_APP_INVENTOR_GUIDE.md` - Detailed guide
- `MIT_BLOCKS_GUIDE.md` - Visual block instructions
- `WHICH_METHOD_TO_USE.md` - Compare methods

**Want to customize?**
- Change folder names in join block
- Change toast messages
- Add more features with blocks

---

## 🚀 Next Steps

1. ✅ Test thoroughly
2. ✅ Build APK
3. ✅ Install on field workers' devices
4. ✅ Grant permissions
5. ✅ Start collecting data!

**Your VAW Assessment app is ready for deployment!** 🎊
