package id.mil.rindam.sipandu;

import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.content.Context;
import android.content.SharedPreferences;
import android.graphics.Bitmap;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.view.View;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceError;
import android.webkit.WebResourceRequest;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

public class MainActivity extends AppCompatActivity {

    private WebView webView;
    private SwipeRefreshLayout swipeRefreshLayout;
    private ProgressBar progressBar;
    private LinearLayout offlineLayout;
    private TextView currentServerUrlText;
    private Button btnRetry, btnOfflineDemo, btnChangeIp;

    private static final String PREF_NAME = "SIPANDU_CONFIG";
    private static final String KEY_SERVER_URL = "server_url";
    private String currentServerUrl;

    @SuppressLint("SetJavaScriptEnabled")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        webView = findViewById(R.id.webView);
        swipeRefreshLayout = findViewById(R.id.swipeRefreshLayout);
        progressBar = findViewById(R.id.progressBar);
        offlineLayout = findViewById(R.id.offlineLayout);
        currentServerUrlText = findViewById(R.id.currentServerUrlText);
        btnRetry = findViewById(R.id.btnRetry);
        btnOfflineDemo = findViewById(R.id.btnOfflineDemo);
        btnChangeIp = findViewById(R.id.btnChangeIp);

        // Ambil URL server tersimpan
        SharedPreferences prefs = getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        currentServerUrl = prefs.getString(KEY_SERVER_URL, getString(R.string.server_url_default));
        currentServerUrlText.setText(currentServerUrl);

        setupWebView();
        setupListeners();

        loadServerUrl();
    }

    @SuppressLint("SetJavaScriptEnabled")
    private void setupWebView() {
        WebSettings settings = webView.getSettings();
        settings.setJavaScriptEnabled(true);
        settings.setDomStorageEnabled(true);
        settings.setDatabaseEnabled(true);
        settings.setAllowFileAccess(true);
        settings.setAllowContentAccess(true);
        settings.setUseWideViewPort(true);
        settings.setLoadWithOverviewMode(true);
        settings.setSupportZoom(true);
        settings.setBuiltInZoomControls(true);
        settings.setDisplayZoomControls(false);
        settings.setCacheMode(WebSettings.LOAD_DEFAULT);
        settings.setMixedContentMode(WebSettings.MIXED_CONTENT_ALWAYS_ALLOW);

        // Identitas User-Agent Android SIPANDU
        settings.setUserAgentString(settings.getUserAgentString() + " SIPANDU-Android-App/1.0");

        webView.setWebViewClient(new WebViewClient() {
            @Override
            public void onPageStarted(WebView view, String url, Bitmap favicon) {
                super.onPageStarted(view, url, favicon);
                progressBar.setVisibility(View.VISIBLE);
            }

            @Override
            public void onPageFinished(WebView view, String url) {
                super.onPageFinished(view, url);
                progressBar.setVisibility(View.GONE);
                swipeRefreshLayout.setRefreshing(false);

                if (!url.startsWith("file:///android_asset/")) {
                    offlineLayout.setVisibility(View.GONE);
                    webView.setVisibility(View.VISIBLE);
                }
            }

            @Override
            public void onReceivedError(WebView view, WebResourceRequest request, WebResourceError error) {
                if (request.isForMainFrame()) {
                    showOfflineScreen();
                }
            }
        });

        webView.setWebChromeClient(new WebChromeClient() {
            @Override
            public void onProgressChanged(WebView view, int newProgress) {
                progressBar.setProgress(newProgress);
                if (newProgress == 100) {
                    progressBar.setVisibility(View.GONE);
                }
            }
        });
    }

    private void setupListeners() {
        swipeRefreshLayout.setOnRefreshListener(() -> {
            if (offlineLayout.getVisibility() == View.VISIBLE) {
                loadServerUrl();
            } else {
                webView.reload();
            }
        });

        btnRetry.setOnClickListener(v -> loadServerUrl());

        btnOfflineDemo.setOnClickListener(v -> {
            offlineLayout.setVisibility(View.GONE);
            webView.setVisibility(View.VISIBLE);
            webView.loadUrl("file:///android_asset/offline_demo/index.html");
            Toast.makeText(this, "Menjalankan Mode Demo Offline", Toast.LENGTH_SHORT).show();
        });

        btnChangeIp.setOnClickListener(v -> showChangeIpDialog());
    }

    private void loadServerUrl() {
        offlineLayout.setVisibility(View.GONE);
        webView.setVisibility(View.VISIBLE);
        currentServerUrlText.setText(currentServerUrl);
        webView.loadUrl(currentServerUrl);
    }

    private void showOfflineScreen() {
        webView.setVisibility(View.GONE);
        offlineLayout.setVisibility(View.VISIBLE);
        swipeRefreshLayout.setRefreshing(false);
        progressBar.setVisibility(View.GONE);
    }

    private void showChangeIpDialog() {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle("Pengaturan IP Server SIPANDU");
        builder.setMessage("Ketikkan alamat server lokal / Wi-Fi laptop (contoh: http://192.168.1.15:8000)");

        final EditText input = new EditText(this);
        input.setText(currentServerUrl);
        input.setSelection(currentServerUrl.length());
        builder.setView(input);

        builder.setPositiveButton("Simpan & Hubungkan", (dialog, which) -> {
            String newUrl = input.getText().toString().trim();
            if (!newUrl.startsWith("http://") && !newUrl.startsWith("https://")) {
                newUrl = "http://" + newUrl;
            }
            currentServerUrl = newUrl;
            getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE)
                    .edit()
                    .putString(KEY_SERVER_URL, currentServerUrl)
                    .apply();

            Toast.makeText(this, "Alamat server diperbarui: " + currentServerUrl, Toast.LENGTH_SHORT).show();
            loadServerUrl();
        });

        builder.setNegativeButton("Batal", (dialog, which) -> dialog.cancel());
        builder.show();
    }

    @Override
    public void onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack();
        } else {
            super.onBackPressed();
        }
    }
}