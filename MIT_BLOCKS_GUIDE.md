# 🧩 MIT App Inventor - Complete Blocks Guide

## 📋 Overview

This guide shows you **EXACTLY** which blocks to drag and how to connect them - **NO PROGRAMMING NEEDED!**

---

## 🎯 What You're Building

```
HTML Form (your redesigned UI)
      ↓
Saves assessment
      ↓
JavaScript sends message to App Inventor
      ↓
App Inventor receives via WebViewString
      ↓
Saves to designated folder
      ↓
Shows toast notification ✅
```

---

## 📱 Components Needed

### In Designer Tab:

1. **WebViewer1** (visible - fills screen)
2. **File1** component (non-visible)
3. **Notifier1** component (non-visible)

---

## 🧩 BLOCKS TO CREATE

### **BLOCK GROUP 1: Initialize the App**

When the app starts, load the HTML file:

```
┌─────────────────────────────────────────┐
│ when Screen1.Initialize                 │
│   do                                     │
│     ┌─────────────────────────────────┐ │
│     │ set WebViewer1.WebViewString to │ │
│     │   text ""                        │ │
│     └─────────────────────────────────┘ │
│     ┌─────────────────────────────────┐ │
│     │ call WebViewer1.GoToUrl         │ │
│     │   url: "file:///android_asset/  │ │
│     │        vaw_assessment_app.html" │ │
│     └─────────────────────────────────┘ │
└─────────────────────────────────────────┘
```

**How to build this:**

1. Click **Screen1** drawer on left
2. Drag `when Screen1.Initialize` block
3. Click **WebViewer1** drawer
4. Drag `set WebViewer1.WebViewString to` block
5. Connect inside the `Initialize` block
6. Click **Text** drawer
7. Drag empty text `" "` block
8. Connect to WebViewString (leave it empty)
9. Click **WebViewer1** drawer again
10. Drag `call WebViewer1.GoToUrl` block
11. Connect below the WebViewString block
12. Click **Text** drawer
13. Drag text block, type: `file:///android_asset/vaw_assessment_app.html`
14. Connect to `url` parameter

---

### **BLOCK GROUP 2: Listen for Save Messages**

Detect when JavaScript wants to save a file:

```
┌─────────────────────────────────────────────────┐
│ when WebViewer1.WebViewStringChange             │
│   do                                             │
│     ┌─────────────────────────────────────────┐ │
│     │ if contains                              │ │
│     │   ┌─ get WebViewer1.WebViewString       │ │
│     │   └─ text "SAVE_FILE"                   │ │
│     │ then                                     │ │
│     │   ┌───────────────────────────────────┐ │ │
│     │   │ set global messageData to         │ │ │
│     │   │   call list split at first        │ │ │
│     │   │     ┌─ get WebViewer1.WebViewString│ │ │
│     │   │     └─ text "|"                    │ │ │
│     │   └───────────────────────────────────┘ │ │
│     │   ┌───────────────────────────────────┐ │ │
│     │   │ call SaveFileToFolder             │ │ │
│     │   └───────────────────────────────────┘ │ │
│     └─────────────────────────────────────────┘ │
└─────────────────────────────────────────────────┘
```

**How to build this:**

1. Click **WebViewer1** drawer
2. Drag `when WebViewer1.WebViewStringChange` block
3. Click **Control** drawer
4. Drag `if then` block, connect inside
5. Click **Text** drawer
6. Drag `contains` block, connect to `if` test
7. Click **WebViewer1** drawer
8. Drag `get WebViewer1.WebViewString`, connect to `contains`
9. Click **Text** drawer
10. Drag text block, type `SAVE_FILE`, connect to `contains`
11. Click **Variables** drawer
12. Click **Initialize global name to**, name it `messageData`
13. Click **Lists** drawer
14. Drag `split at first` block
15. Connect `get WebViewer1.WebViewString` to first slot
16. Click **Text**, drag text `|`, connect to delimiter
17. Click **Procedures** drawer
18. Drag `call SaveFileToFolder` (we'll create this next)

---

### **BLOCK GROUP 3: Create Save Procedure**

Create a reusable procedure to save files:

```
┌──────────────────────────────────────────────────┐
│ to procedure SaveFileToFolder                    │
│   do                                              │
│     ┌────────────────────────────────────────┐   │
│     │ set global folder to                   │   │
│     │   select list item                     │   │
│     │     list: get global messageData       │   │
│     │     index: 2                           │   │
│     └────────────────────────────────────────┘   │
│     ┌────────────────────────────────────────┐   │
│     │ set global filename to                 │   │
│     │   select list item                     │   │
│     │     list: get global messageData       │   │
│     │     index: 3                           │   │
│     └────────────────────────────────────────┘   │
│     ┌────────────────────────────────────────┐   │
│     │ set global content to                  │   │
│     │   select list item                     │   │
│     │     list: get global messageData       │   │
│     │     index: 4                           │   │
│     └────────────────────────────────────────┘   │
│     ┌────────────────────────────────────────┐   │
│     │ set global fullPath to                 │   │
│     │   join                                 │   │
│     │     ┌─ text "/storage/emulated/0/      │   │
│     │     │       Documents/VAW_Assessments_ │   │
│     │     │       Baggao/"                   │   │
│     │     ├─ get global folder               │   │
│     │     ├─ text "/"                        │   │
│     │     └─ get global filename             │   │
│     └────────────────────────────────────────┘   │
│     ┌────────────────────────────────────────┐   │
│     │ call File1.SaveFile                    │   │
│     │   text: get global content             │   │
│     │   fileName: get global fullPath        │   │
│     └────────────────────────────────────────┘   │
│     ┌────────────────────────────────────────┐   │
│     │ call Notifier1.ShowToast               │   │
│     │   message: join                        │   │
│     │     ┌─ text "✅ Saved: "               │   │
│     │     └─ get global filename             │   │
│     └────────────────────────────────────────┘   │
└──────────────────────────────────────────────────┘
```

**How to build this:**

1. Click **Procedures** drawer
2. Drag `to procedure` block
3. Click the word "procedure", rename to `SaveFileToFolder`
4. Click **Variables** drawer
5. Create these global variables (click "Initialize global name to"):
   - `folder`
   - `filename`
   - `content`
   - `fullPath`
6. Click **Lists** drawer
7. Drag `select list item` block
8. Set index to `2`, connect `get global messageData`
9. Assign to `folder` variable
10. Repeat for index `3` → `filename`
11. Repeat for index `4` → `content`
12. Click **Text** drawer
13. Drag `join` block
14. Add 4 items (click blue star icon)
15. Connect text blocks with the path parts
16. Assign to `fullPath` variable
17. Click **File1** drawer
18. Drag `call File1.SaveFile` block
19. Connect `get global content` and `get global fullPath`
20. Click **Notifier1** drawer
21. Drag `call Notifier1.ShowToast`
22. Create join with "✅ Saved: " and filename

---

## 🎨 Simplified Alternative (Easier!)

If the above seems complex, use this **SIMPLER version**:

### **Simple 3-Block Setup:**

```
BLOCK 1: Initialize
┌─────────────────────────────────┐
│ when Screen1.Initialize         │
│   call WebViewer1.GoToUrl       │
│     "file:///android_asset/     │
│      vaw_assessment_app.html"   │
└─────────────────────────────────┘

BLOCK 2: Detect Save
┌─────────────────────────────────┐
│ when WebViewer1.AfterURLChange  │
│   if contains URL "SAVE_FILE"   │
│     call ParseAndSave           │
└─────────────────────────────────┘

BLOCK 3: Save File
┌─────────────────────────────────┐
│ to ParseAndSave                 │
│   call File1.SaveFile           │
│   call Notifier1.ShowToast      │
└─────────────────────────────────┘
```

---

## 🔧 Using File Component

### **Set File Component Properties:**

In **Designer** → Click **File1** → Properties:

1. **Scope:** Set to `Shared`
2. This allows saving to Documents folder

---

## ✅ Testing Checklist

After building blocks:

1. ✅ Connect AI Companion
2. ✅ App loads HTML correctly
3. ✅ Fill out a test assessment
4. ✅ Click "Save Assessment"
5. ✅ See toast message: "✅ Saved: filename"
6. ✅ Check Documents folder for file

---

## 🎯 Complete Block List

Here's ALL the blocks you need in order:

### **From Screen1 drawer:**
- `when Screen1.Initialize`

### **From WebViewer1 drawer:**
- `set WebViewer1.WebViewString to`
- `call WebViewer1.GoToUrl`
- `when WebViewer1.WebViewStringChange`
- `get WebViewer1.WebViewString`

### **From Text drawer:**
- Empty text `" "`
- `contains`
- `join`
- Text blocks with your paths and messages

### **From Lists drawer:**
- `split at first`
- `select list item`

### **From Variables drawer:**
- `initialize global` (for folder, filename, content, fullPath)
- `set global`
- `get global`

### **From File1 drawer:**
- `call File1.SaveFile`

### **From Notifier1 drawer:**
- `call Notifier1.ShowToast`

### **From Procedures drawer:**
- `to procedure` (create SaveFileToFolder)

### **From Control drawer:**
- `if then`

---

## 💾 File Saving Details

### **Message Format from JavaScript:**

```
SAVE_FILE|folder|filename|content
```

Example:
```
SAVE_FILE|barangay|Adaoag_2025-01-15.json|{...json data...}
```

### **Parsing in App Inventor:**

```
Split message by "|" character:
Position 1: "SAVE_FILE" (command)
Position 2: "barangay" (folder)
Position 3: "Adaoag_2025-01-15.json" (filename)
Position 4: "{...}" (content)
```

---

## 🏗️ Full Path Construction

```
Base: /storage/emulated/0/Documents/
App Folder: VAW_Assessments_Baggao/
Subfolder: barangay/ or progress/ or final/
Filename: [from JavaScript]

Result: /storage/emulated/0/Documents/VAW_Assessments_Baggao/barangay/Adaoag_2025-01-15.json
```

---

## 🐛 Debugging Tips

### **Check if message is received:**

Add this test block:

```
when WebViewer1.WebViewStringChange
  call Notifier1.ShowAlert
    message: get WebViewer1.WebViewString
```

This will show you what JavaScript is sending.

### **Check file path:**

Add this before `call File1.SaveFile`:

```
call Notifier1.ShowAlert
  message: get global fullPath
```

This shows the exact path being used.

---

## 🎉 You're Done!

Your blocks are now ready to:
- ✅ Load HTML form
- ✅ Receive save commands from JavaScript
- ✅ Parse folder, filename, and content
- ✅ Save to designated folders
- ✅ Show success notifications

---

## 📱 Next: Build & Test

1. Click **Connect** → **AI Companion**
2. Test on your phone
3. If works → Build APK
4. Deploy to field workers!

**Your MIT App Inventor app is complete!** 🚀
