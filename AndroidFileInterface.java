package com.vawassessment.baggao;

import android.content.Context;
import android.os.Environment;
import android.webkit.JavascriptInterface;
import android.widget.Toast;
import android.util.Log;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;

/**
 * Android WebView JavaScript Interface for VAW Assessment App
 * This class handles file saving to designated folders on Android devices
 */
public class AndroidFileInterface {
    private Context context;
    private static final String TAG = "VAW_FileInterface";
    private static final String BASE_FOLDER = "VAW_Assessments_Baggao";

    public AndroidFileInterface(Context context) {
        this.context = context;
    }

    /**
     * Save file to designated folder
     * Called from JavaScript: Android.saveFile(folderPath, filename, content)
     *
     * @param folderPath Subfolder path (e.g., "VAW_Assessments/barangay")
     * @param filename Name of the file
     * @param content JSON content to save
     * @return true if successful, false otherwise
     */
    @JavascriptInterface
    public boolean saveFile(String folderPath, String filename, String content) {
        try {
            Log.d(TAG, "Saving file: " + filename + " to: " + folderPath);

            // Get the app's external storage directory
            // This will be: /storage/emulated/0/Documents/VAW_Assessments_Baggao/
            File documentsDir = Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_DOCUMENTS);

            // Create base folder
            File baseFolder = new File(documentsDir, BASE_FOLDER);
            if (!baseFolder.exists()) {
                boolean created = baseFolder.mkdirs();
                Log.d(TAG, "Base folder created: " + created);
            }

            // Create subfolder (barangay, progress, or final)
            String subfolderName = folderPath.replace("VAW_Assessments/", "");
            File targetFolder = new File(baseFolder, subfolderName);
            if (!targetFolder.exists()) {
                boolean created = targetFolder.mkdirs();
                Log.d(TAG, "Subfolder created: " + created + " at " + targetFolder.getAbsolutePath());
            }

            // Create the file
            File file = new File(targetFolder, filename);

            // Write content
            FileOutputStream fos = new FileOutputStream(file);
            fos.write(content.getBytes());
            fos.close();

            Log.d(TAG, "✅ File saved successfully: " + file.getAbsolutePath());

            // Show toast notification
            showToast("✅ Saved: " + filename);

            return true;

        } catch (IOException e) {
            Log.e(TAG, "❌ Error saving file: " + e.getMessage());
            e.printStackTrace();
            showToast("❌ Failed to save file");
            return false;
        } catch (Exception e) {
            Log.e(TAG, "❌ Unexpected error: " + e.getMessage());
            e.printStackTrace();
            showToast("❌ Error: " + e.getMessage());
            return false;
        }
    }

    /**
     * Get the full path to the VAW Assessments folder
     * @return Full path as string
     */
    @JavascriptInterface
    public String getStoragePath() {
        File documentsDir = Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_DOCUMENTS);
        File baseFolder = new File(documentsDir, BASE_FOLDER);
        return baseFolder.getAbsolutePath();
    }

    /**
     * Check if external storage is available
     * @return true if writable, false otherwise
     */
    @JavascriptInterface
    public boolean isStorageAvailable() {
        String state = Environment.getExternalStorageState();
        return Environment.MEDIA_MOUNTED.equals(state);
    }

    /**
     * Show a toast message
     */
    private void showToast(final String message) {
        // Run on UI thread
        if (context instanceof android.app.Activity) {
            ((android.app.Activity) context).runOnUiThread(new Runnable() {
                @Override
                public void run() {
                    Toast.makeText(context, message, Toast.LENGTH_SHORT).show();
                }
            });
        }
    }

    /**
     * Delete a specific file (useful for cleanup)
     * @param folderPath Subfolder path
     * @param filename Name of the file to delete
     * @return true if successful
     */
    @JavascriptInterface
    public boolean deleteFile(String folderPath, String filename) {
        try {
            File documentsDir = Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_DOCUMENTS);
            File baseFolder = new File(documentsDir, BASE_FOLDER);
            String subfolderName = folderPath.replace("VAW_Assessments/", "");
            File targetFolder = new File(baseFolder, subfolderName);
            File file = new File(targetFolder, filename);

            if (file.exists()) {
                boolean deleted = file.delete();
                Log.d(TAG, "File deleted: " + deleted);
                return deleted;
            }
            return false;
        } catch (Exception e) {
            Log.e(TAG, "Error deleting file: " + e.getMessage());
            return false;
        }
    }

    /**
     * Get list of all saved files in a folder
     * @param folderPath Subfolder path
     * @return Comma-separated list of filenames
     */
    @JavascriptInterface
    public String listFiles(String folderPath) {
        try {
            File documentsDir = Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_DOCUMENTS);
            File baseFolder = new File(documentsDir, BASE_FOLDER);
            String subfolderName = folderPath.replace("VAW_Assessments/", "");
            File targetFolder = new File(baseFolder, subfolderName);

            if (targetFolder.exists() && targetFolder.isDirectory()) {
                File[] files = targetFolder.listFiles();
                if (files != null && files.length > 0) {
                    StringBuilder fileList = new StringBuilder();
                    for (File file : files) {
                        if (file.isFile()) {
                            fileList.append(file.getName()).append(",");
                        }
                    }
                    return fileList.toString();
                }
            }
            return "";
        } catch (Exception e) {
            Log.e(TAG, "Error listing files: " + e.getMessage());
            return "";
        }
    }
}
