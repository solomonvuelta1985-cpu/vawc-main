package com.vawassessment.baggao;

import android.Manifest;
import android.content.pm.PackageManager;
import android.os.Build;
import android.os.Bundle;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;

/**
 * Main Activity for VAW Assessment WebView App
 */
public class MainActivity extends AppCompatActivity {
    private WebView webView;
    private static final int STORAGE_PERMISSION_CODE = 100;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        // Request storage permissions
        requestStoragePermission();

        // Initialize WebView
        initializeWebView();
    }

    /**
     * Initialize WebView with proper settings
     */
    private void initializeWebView() {
        webView = findViewById(R.id.webview);

        // Enable JavaScript
        WebSettings webSettings = webView.getSettings();
        webSettings.setJavaScriptEnabled(true);
        webSettings.setDomStorageEnabled(true);
        webSettings.setAllowFileAccess(true);
        webSettings.setAllowContentAccess(true);

        // Set WebView client
        webView.setWebViewClient(new WebViewClient());

        // **IMPORTANT: Add JavaScript Interface**
        // This makes the Android.saveFile() function available to JavaScript
        AndroidFileInterface fileInterface = new AndroidFileInterface(this);
        webView.addJavascriptInterface(fileInterface, "Android");

        // Load the HTML file
        // Option 1: From assets folder
        webView.loadUrl("file:///android_asset/vaw_assessment_app.html");

        // Option 2: From a web server (if hosted online)
        // webView.loadUrl("http://yourserver.com/vaw_assessment_app.html");

        // Option 3: From external storage (if you copied the file there)
        // webView.loadUrl("file:///storage/emulated/0/Documents/vaw_assessment_app.html");
    }

    /**
     * Request storage permission for Android 6.0+
     */
    private void requestStoragePermission() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            if (ContextCompat.checkSelfPermission(this, Manifest.permission.WRITE_EXTERNAL_STORAGE)
                    != PackageManager.PERMISSION_GRANTED) {

                ActivityCompat.requestPermissions(this,
                        new String[]{
                                Manifest.permission.WRITE_EXTERNAL_STORAGE,
                                Manifest.permission.READ_EXTERNAL_STORAGE
                        },
                        STORAGE_PERMISSION_CODE);
            }
        }
    }

    /**
     * Handle permission request result
     */
    @Override
    public void onRequestPermissionsResult(int requestCode, String[] permissions, int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);

        if (requestCode == STORAGE_PERMISSION_CODE) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                Toast.makeText(this, "✅ Storage permission granted", Toast.LENGTH_SHORT).show();
            } else {
                Toast.makeText(this, "❌ Storage permission denied - App may not work properly", Toast.LENGTH_LONG).show();
            }
        }
    }

    /**
     * Handle back button
     */
    @Override
    public void onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack();
        } else {
            super.onBackPressed();
        }
    }
}
