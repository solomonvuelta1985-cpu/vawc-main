# 🚀 Quick Start - VAW Assessment Android App

## 📋 What You'll Get

✅ **Automatic file organization** in designated folders
✅ **No more messy downloads folder**
✅ **Works offline** - saves locally on device
✅ **Backup system** - files persist even if app crashes
✅ **Easy file retrieval** - organized by type

---

## 📂 Folder Structure

All files saved to: `/Documents/VAW_Assessments_Baggao/`

```
📁 VAW_Assessments_Baggao/
│
├── 📁 barangay/           ← Individual assessments (auto-saved)
│   ├── Adaoag_2025-01-15_14-30-00.json
│   ├── Alba_2025-01-16_09-15-00.json
│   └── ...
│
├── 📁 progress/           ← Progress snapshots (auto-saved)
│   ├── Progress_5of48_2025-01-15.json
│   └── Progress_10of48_2025-01-16.json
│
└── 📁 final/              ← Final submission
    └── VAW_Final_Submission_RichmondRosete_2025-01-20.json
```

---

## ⚡ 3-Step Setup

### 1️⃣ Create Android Project
```
Android Studio → New Project → Empty Activity
Name: VAW Assessment Baggao
Package: com.vawassessment.baggao
Language: Java
```

### 2️⃣ Copy Files
```
✅ AndroidFileInterface.java    → app/src/main/java/com/vawassessment/baggao/
✅ MainActivity.java            → app/src/main/java/com/vawassessment/baggao/
✅ activity_main.xml            → app/src/main/res/layout/
✅ AndroidManifest.xml          → app/src/main/
✅ vaw_assessment_app.html      → app/src/main/assets/
```

### 3️⃣ Run App
```
Connect Android device → Click Run (green ▶️)
Grant storage permission when prompted
```

---

## 🎯 How It Works

### When User Saves Assessment:

```
User clicks "Save"
    ↓
JavaScript calls: saveToDesignatedFolder()
    ↓
Checks: Is this Android app?
    ↓
✅ YES → Android.saveFile() → Saves to designated folder
❌ NO  → Browser download → Downloads folder (fallback)
```

### File Types:

| Type | When Created | Folder |
|------|--------------|--------|
| **Individual** | After saving each barangay | barangay/ |
| **Progress** | After saving each barangay | progress/ |
| **Final** | After completing all 48 barangays | final/ |

---

## 📱 Using the App

### First Time Setup:
1. Install app on device
2. Open app
3. **IMPORTANT:** Grant storage permission when asked
4. Start filling assessments

### Daily Use:
1. Open app
2. Select barangay
3. Fill assessment form
4. Click "Save Assessment"
5. ✅ Toast message: "Saved: filename.json"
6. Files automatically organized in folders

### Accessing Files:
1. Open **Files** or **My Files** app on Android
2. Go to **Documents**
3. Open **VAW_Assessments_Baggao** folder
4. Browse files by type (barangay/progress/final)

---

## 🔍 Where Are My Files?

### On Device:
```
Open: Files app → Documents → VAW_Assessments_Baggao
```

### Using Computer (USB):
```
1. Connect phone to computer via USB
2. Select "File Transfer" mode
3. Open: Internal Storage → Documents → VAW_Assessments_Baggao
4. Copy files to computer
```

### Using Android Studio:
```
Device File Explorer → sdcard → Documents → VAW_Assessments_Baggao
Right-click file → Save As
```

---

**Ready to go! 🚀**
