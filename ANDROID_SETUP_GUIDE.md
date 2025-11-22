# 📱 VAW Assessment Android App - Setup Guide

## 🎯 Overview

This guide will help you create an Android app that saves JSON files to a **designated folder** on the device.

### 📂 Folder Structure on Android Device:
```
/storage/emulated/0/Documents/VAW_Assessments_Baggao/
├── barangay/          (Individual barangay assessments)
│   ├── Adaoag_2025-01-15_2025-01-15T14-30-00.json
│   ├── Alba_2025-01-16_2025-01-16T09-15-00.json
│   └── ...
├── progress/          (Consolidated progress files)
│   ├── Progress_5of48_2025-01-15_2025-01-15T16-00-00.json
│   └── Progress_10of48_2025-01-16_2025-01-16T18-00-00.json
└── final/             (Final submission files)
    └── VAW_Final_Submission_RichmondRosete_2025-01-20.json
```

---

## 🚀 Quick Setup Steps

### 1️⃣ Create Android Studio Project

1. **Open Android Studio**
2. **Create New Project**
   - Choose: **"Empty Activity"**
   - Name: `VAW Assessment Baggao`
   - Package name: `com.vawassessment.baggao`
   - Language: **Java**
   - Minimum SDK: **API 21 (Android 5.0)**

---

### 2️⃣ Add Files to Your Project

#### **Step 1: Copy HTML File**
```
app/src/main/assets/vaw_assessment_app.html
```
- Create `assets` folder if it doesn't exist:
  - Right-click on `app/src/main` → New → Directory → Name it `assets`
- Copy your `vaw_assessment_app.html` file into this folder

#### **Step 2: Add Java Files**

**File:** `app/src/main/java/com/vawassessment/baggao/AndroidFileInterface.java`
- Copy the `AndroidFileInterface.java` file I provided

**File:** `app/src/main/java/com/vawassessment/baggao/MainActivity.java`
- Copy the `MainActivity.java` file I provided

#### **Step 3: Update AndroidManifest.xml**

**File:** `app/src/main/AndroidManifest.xml`
- Replace with the `AndroidManifest.xml` file I provided

---

### 3️⃣ Update Layout File

**File:** `app/src/main/res/layout/activity_main.xml`

```xml
<?xml version="1.0" encoding="utf-8"?>
<RelativeLayout xmlns:android="http://schemas.android.com/apk/res/android"
    android:layout_width="match_parent"
    android:layout_height="match_parent">

    <WebView
        android:id="@+id/webview"
        android:layout_width="match_parent"
        android:layout_height="match_parent" />

</RelativeLayout>
```

---

### 4️⃣ Update build.gradle (Module: app)

**File:** `app/build.gradle`

```gradle
android {
    compileSdk 33

    defaultConfig {
        applicationId "com.vawassessment.baggao"
        minSdk 21
        targetSdk 33
        versionCode 1
        versionName "1.0"
    }

    buildTypes {
        release {
            minifyEnabled false
            proguardFiles getDefaultProguardFile('proguard-android-optimize.txt'), 'proguard-rules.pro'
        }
    }
}

dependencies {
    implementation 'androidx.appcompat:appcompat:1.6.1'
    implementation 'com.google.android.material:material:1.9.0'
}
```

---

## 📱 How It Works

### JavaScript → Android Communication

#### **From HTML (JavaScript):**
```javascript
// Automatically called when saving assessment
saveToDesignatedFolder(filename, json, 'barangay');
```

#### **Calls Android Method:**
```java
@JavascriptInterface
public boolean saveFile(String folderPath, String filename, String content) {
    // Saves to: /Documents/VAW_Assessments_Baggao/barangay/filename.json
}
```

---

## 🧪 Testing the App

### **Step 1: Build & Run**
1. Connect your Android device via USB (enable USB Debugging)
2. Or use an Android Emulator
3. Click **Run** (green play button) in Android Studio

### **Step 2: Grant Permissions**
- When the app first runs, it will ask for **Storage Permission**
- **IMPORTANT:** Click **"Allow"** to enable file saving

### **Step 3: Test File Saving**
1. Complete an assessment for any barangay
2. Click **"Save Assessment"**
3. You should see a toast message: **"✅ Saved: [filename]"**

### **Step 4: Verify Files**
Use a **File Manager** app on your Android device:

1. Open **Files** or **My Files** app
2. Navigate to: **Documents** → **VAW_Assessments_Baggao**
3. You should see folders: `barangay/`, `progress/`, `final/`
4. Check inside each folder for JSON files

---

## 🔍 Viewing Saved Files on Android

### **Option 1: Using File Manager App**
- Most Android devices have a built-in **"Files"** or **"My Files"** app
- Path: `Internal Storage → Documents → VAW_Assessments_Baggao`

### **Option 2: Using Android Studio (while device is connected)**
1. Open **Device File Explorer** (bottom-right corner)
2. Navigate to: `sdcard → Documents → VAW_Assessments_Baggao`
3. Right-click on any file → **Save As** to copy to your computer

### **Option 3: Using ADB (Command Line)**
```bash
# List all barangay files
adb shell ls /sdcard/Documents/VAW_Assessments_Baggao/barangay/

# Pull all files to your computer
adb pull /sdcard/Documents/VAW_Assessments_Baggao/ ./vaw_exports/
```

---

## 🐛 Troubleshooting

### ❌ Problem: Files are not being saved

**Solution 1: Check Permissions**
```
Settings → Apps → VAW Assessment → Permissions → Storage → Allow
```

**Solution 2: Check Android Version**
- For **Android 11+**, you may need to grant additional permissions
- Add this to `AndroidManifest.xml`:
```xml
<uses-permission android:name="android.permission.MANAGE_EXTERNAL_STORAGE" />
```

**Solution 3: Check LogCat**
- In Android Studio, open **Logcat** (bottom panel)
- Filter by: `VAW_FileInterface`
- Look for error messages

---

### ❌ Problem: "Android is not defined" error

**Solution:**
- Make sure you're running the app through the **Android WebView**
- The app won't work in a regular browser
- The JavaScript automatically falls back to browser downloads if not in Android app

---

### ❌ Problem: Cannot find files on device

**Solution:**
1. Open **Device File Explorer** in Android Studio
2. Navigate to: `sdcard/Documents/`
3. Look for `VAW_Assessments_Baggao` folder
4. If it doesn't exist, the app doesn't have write permissions

---

## 📊 File Naming Convention

### **Barangay Files:**
```
Adaoag_2025-01-15_2025-01-15T14-30-00.json
│      │          └── Timestamp (hour-minute-second)
│      └── Date (YYYY-MM-DD)
└── Barangay Name (cleaned)
```

### **Progress Files:**
```
Progress_5of48_2025-01-15_2025-01-15T16-00-00.json
│        │      │          └── Timestamp
│        │      └── Date
│        └── Completed count
```

### **Final Submission:**
```
VAW_Final_Submission_RichmondRosete_2025-01-20.json
```

---

## 🔐 Security & Permissions

### Required Permissions:
- `WRITE_EXTERNAL_STORAGE` - Save files to device
- `READ_EXTERNAL_STORAGE` - Read saved files
- `INTERNET` - (Optional) If loading HTML from web server

### Storage Scope:
- Files are saved in the **public Documents folder**
- Users can access files through any file manager
- Files persist even if app is uninstalled (user data)

---

## 🎨 Customization

### Change Base Folder Name:
In `AndroidFileInterface.java`:
```java
private static final String BASE_FOLDER = "VAW_Assessments_Baggao";
// Change to:
private static final String BASE_FOLDER = "YourFolderName";
```

### Change Subfolder Names:
In JavaScript (HTML file):
```javascript
saveToDesignatedFolder(filename, json, 'barangay');  // Current
saveToDesignatedFolder(filename, json, 'custom_folder');  // Custom
```

---

## 📦 Exporting Data

### Method 1: USB File Transfer
1. Connect Android device to computer via USB
2. Select **"File Transfer"** mode
3. Navigate to: `Internal Storage → Documents → VAW_Assessments_Baggao`
4. Copy all files to your computer

### Method 2: Using ADB
```bash
adb pull /sdcard/Documents/VAW_Assessments_Baggao/ ./backup/
```

### Method 3: Cloud Upload (Future Enhancement)
You can add functionality to upload files to:
- Google Drive
- Dropbox
- OneDrive
- Your own server

---

## 📱 App Distribution

### **Option 1: Install via USB**
1. Build APK: **Build → Build Bundle(s) / APK(s) → Build APK(s)**
2. Copy APK to device
3. Install manually (enable "Unknown Sources")

### **Option 2: Google Play Store**
1. Create a Google Play Developer account
2. Build signed release APK
3. Upload to Play Store

### **Option 3: Internal Distribution**
1. Use **Firebase App Distribution**
2. Or host APK on your own server

---

## ✅ Checklist

Before deploying:
- [ ] Test on actual Android device
- [ ] Verify all 3 folders are created (barangay, progress, final)
- [ ] Test saving multiple assessments
- [ ] Test with no internet connection
- [ ] Verify files can be opened and read
- [ ] Check file sizes are reasonable
- [ ] Test on different Android versions (5.0 - 13.0)
- [ ] Grant storage permissions
- [ ] Test emergency backup downloads

---

## 📞 Support

If you encounter issues:

1. **Check LogCat** in Android Studio
2. **Enable Developer Mode** on device
3. **Check storage space** on device
4. **Verify permissions** in app settings

---

## 🎉 Success!

Your VAW Assessment app now saves files to a clean, organized folder structure on Android devices! 🚀

All JSON backups are safely stored in:
```
📁 Documents/VAW_Assessments_Baggao/
```
