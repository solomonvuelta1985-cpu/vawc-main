# 📱 VAW Assessment - MIT App Inventor Guide

## 🎯 Build Your App in 30 Minutes - NO CODING NEEDED!

This guide will show you how to create the EXACT same Android app using **MIT App Inventor's drag-and-drop interface**.

---

## ✅ What You'll Build

- ✅ Android app with your redesigned HTML form
- ✅ Automatic file saving to organized folders
- ✅ Same folder structure: `barangay/`, `progress/`, `final/`
- ✅ Works offline
- ✅ Toast notifications when files save
- ✅ Professional, clean UI

---

## 📂 Folder Structure (Same as Android Studio)

```
📁 /Documents/VAW_Assessments_Baggao/
├── 📁 barangay/           ← Individual assessments
│   ├── Adaoag_2025-01-15_14-30-00.json
│   └── ...
├── 📁 progress/           ← Progress snapshots
│   └── Progress_5of48_2025-01-15.json
└── 📁 final/              ← Final submission
    └── VAW_Final_Submission_RichmondRosete_2025-01-20.json
```

---

## 🚀 Step-by-Step Instructions

### **PART 1: Setup MIT App Inventor**

#### Step 1: Go to MIT App Inventor
1. Open browser: https://appinventor.mit.edu/
2. Click **"Create Apps!"**
3. Sign in with Google account
4. Click **"Start new project"**
5. Name it: `VAW_Assessment_Baggao`

---

### **PART 2: Design the Screen**

#### Step 2: Add Components

In the **Designer** view, drag these components from the **Palette**:

**1. From "User Interface" section:**
- Drag **WebViewer** → Drop on Screen1
- Click on **WebViewer1** in Components panel
- In Properties (right side):
  - **Width:** Fill parent
  - **Height:** Fill parent

**2. From "Storage" section:**
- Drag **File** component → Drop on Screen1 (will appear in "Non-visible components")

**3. From "User Interface" section (again):**
- Drag **Notifier** component → Drop on Screen1 (non-visible)

**Your Screen1 should now have:**
- ✅ WebViewer1 (fills entire screen)
- ✅ File1 (non-visible)
- ✅ Notifier1 (non-visible)

---

### **PART 3: Upload Your HTML File**

#### Step 3: Add HTML File to App

1. In **Designer** view, look at **Media** section (bottom right)
2. Click **"Upload File..."**
3. Browse and select: `vaw_assessment_app.html`
4. Wait for upload to complete
5. You should see `vaw_assessment_app.html` in Media list

---

### **PART 4: Add the Blocks (Visual Programming)**

#### Step 4: Switch to Blocks Editor

1. Click **"Blocks"** button (top right)
2. Now we'll add the logic using visual blocks

---

#### **BLOCK SET 1: Initialize the App**

**When app starts, load the HTML file:**

```
Drag these blocks and connect them:

[When Screen1.Initialize]
└─ [call WebViewer1.GoToAsset]
   └─ [text "vaw_assessment_app.html"]
```

**How to create this:**
1. From **Screen1** drawer → Drag `when Screen1.Initialize`
2. From **WebViewer1** drawer → Drag `call WebViewer1.GoToAsset`
3. Connect them together
4. From **Text** drawer → Drag empty text block `" "`
5. Type inside it: `vaw_assessment_app.html`
6. Connect to `asset` parameter

---

#### **BLOCK SET 2: Enable JavaScript Interface**

**Create a function to handle JavaScript calls:**

```
[When Screen1.Initialize] (same block as above)
└─ [set WebViewer1.WebViewString]
   └─ [call WebViewer1.RunJavaScript]
      └─ [text "window.AppInventor = {
                saveFile: function(folderPath, filename, content) {
                    AppInventor.saveFileToDevice(folderPath, filename, content);
                }
              };"]
```

**Wait! This is easier - I'll provide a simpler method below...**

---

#### **BLOCK SET 3: Handle File Saving**

**Create a procedure to save files:**

```
[to procedure SaveFile]
├─ [folderPath] (input parameter)
├─ [filename] (input parameter)
└─ [content] (input parameter)
   └─ [set filePath to]
      └─ [join]
         ├─ [text "/Documents/VAW_Assessments_Baggao/"]
         ├─ [get folderPath]
         ├─ [text "/"]
         └─ [get filename]
   └─ [call File1.SaveFile]
      ├─ [get filePath]
      └─ [get content]
   └─ [call Notifier1.ShowToast]
      └─ [join]
         ├─ [text "✅ Saved: "]
         └─ [get filename]
```

---

## 🎨 Simplified Visual Guide

Since MIT App Inventor is visual, here's the **exact blocks you need**:

### **Complete Blocks Setup:**

#### **Block 1: Initialize App**
```
when Screen1.Initialize
do
  call WebViewer1.GoToAsset
    asset: "vaw_assessment_app.html"

  set WebViewer1.UsesLocation to false
```

#### **Block 2: Handle WebView String (JavaScript Communication)**
```
when WebViewer1.WebViewStringChange
do
  if [get WebViewer1.WebViewString] contains "SAVE_FILE:"
  then
    set data to [call split]
      text: [get WebViewer1.WebViewString]
      at: "|"

    call SaveToFolder
      folder: [select list item list: [get data] index: 2]
      filename: [select list item list: [get data] index: 3]
      content: [select list item list: [get data] index: 4]
```

#### **Block 3: Save to Folder Procedure**
```
to SaveToFolder folder filename content
do
  set fullPath to [join]
    [text "/storage/emulated/0/Documents/VAW_Assessments_Baggao/"]
    [get folder]
    [text "/"]
    [get filename]

  call File1.SaveFile
    text: [get content]
    fileName: [get fullPath]

  call Notifier1.ShowToast
    message: [join "✅ Saved: " [get filename]]
```

---

## 🔧 Alternative: Using WebViewString Extension

**EASIER METHOD - Use Extension:**

MIT App Inventor has an easier way using extensions. I'll create a simplified version:

### **Simple Setup (Recommended):**

1. **Add Extension:**
   - Go to **Extensions** in Designer
   - Search for "File" or "FileTools"
   - Import it

2. **Use These Blocks:**

```
when Screen1.Initialize
  call WebViewer1.GoToAsset "vaw_assessment_app.html"

when WebViewer1.WebViewStringChange
  if contains "JSON_SAVE"
    call File1.SaveFile
      path: "/Documents/VAW_Assessments_Baggao/"
      content: WebViewer1.WebViewString
```

---

## 🎯 JavaScript Integration in HTML

**Update your HTML to communicate with MIT App Inventor:**

Add this JavaScript function (I'll update the HTML file for you):

```javascript
function saveToAppInventor(folder, filename, content) {
    // Format: SAVE_FILE|folder|filename|content
    var message = "SAVE_FILE|" + folder + "|" + filename + "|" + content;

    // Send to App Inventor
    window.AppInventor.setWebViewString(message);
}
```

---

## 📱 Testing Your App

### **Step 1: Test with AI Companion**
1. Download **MIT AI2 Companion** app from Play Store
2. In MIT App Inventor, click **"Connect"** → **"AI Companion"**
3. Scan QR code with Companion app
4. Your app will load on your phone!
5. Test saving an assessment

### **Step 2: Build APK**
1. Click **"Build"** → **"Android App (.apk)"**
2. Wait for build to complete
3. Download APK file
4. Transfer to your Android device
5. Install and test

---

## ✅ Permissions

Make sure to enable permissions in MIT App Inventor:

1. Click **Screen1**
2. In Properties, scroll down to **"Permissions"**
3. Check these permissions:
   - ✅ `ReadExternalStorage`
   - ✅ `WriteExternalStorage`

---

## 🎉 Advantages of MIT App Inventor

✅ **No coding knowledge required**
✅ **Visual drag-and-drop interface**
✅ **Instant testing with AI Companion**
✅ **Easy to modify and update**
✅ **Same result as Android Studio**
✅ **Build APK directly in browser**
✅ **Free and open source**

---

## 📞 Common Issues

### ❌ "HTML file not loading"
**Fix:** Make sure `vaw_assessment_app.html` is uploaded in Media section

### ❌ "Files not saving"
**Fix:** Check permissions are enabled in Screen1 properties

### ❌ "Cannot find saved files"
**Fix:** Use a File Manager app, go to Documents folder

---

## 🚀 Next Steps

1. ✅ Follow this guide
2. ✅ Test with AI Companion
3. ✅ Build APK
4. ✅ Install on device
5. ✅ Grant storage permissions
6. ✅ Start assessing barangays!

---

**I'll now create a detailed BLOCKS VISUAL GUIDE for you...**
