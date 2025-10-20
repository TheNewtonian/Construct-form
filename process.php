<?php

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // **IMPORTANT:** Paste your Google Apps Script Web App URL here
    $google_app_script_url = "https://script.google.com/macros/s/YOUR_UNIQUE_WEB_APP_ID/exec";

    // 1. Get and sanitize the form data
    // We use htmlspecialchars to prevent XSS attacks
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '';
    $service_type = isset($_POST['service_type']) ? htmlspecialchars($_POST['service_type']) : '';
    $project_details = isset($_POST['project_details']) ? htmlspecialchars($_POST['project_details']) : '';

    // 2. Prepare the data to be sent (as an associative array)
    // The keys (e.g., 'name', 'email') MUST match the parameters in your Apps Script (e.g., data.name, data.email)
    $post_data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'service_type' => $service_type,
        'project_details' => $project_details
    ];

    // 3. Use cURL to send the POST request to Google Apps Script
    $ch = curl_init($google_app_script_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects, Google Scripts does this
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    
    // Execute the cURL session
    $response = curl_exec($ch);
    
    // Check for cURL errors
    if(curl_errno($ch)) {
        // If there's an error, redirect back with an error status
        curl_close($ch);
        header("Location: index.php?status=error"); // Change to index.html if you're not using .php for the form
        exit;
    }

    // Close cURL session
    curl_close($ch);
    
    // 4. Decode the JSON response from Google Apps Script
    $result = json_decode($response, true);

    // 5. Redirect based on the response
    if (isset($result['result']) && $result['result'] == 'success') {
        // Success! Redirect back to the form page with a success message
        header("Location: index.php?status=success"); // Change to index.html if you're not using .php for the form
    } else {
        // Something went wrong with the Apps Script
        header("Location: index.php?status=error"); // Change to index.html if you're not using .php for the form
    }
    exit;

} else {
    // If someone tries to access this script directly, send them back
    header("Location: index.php"); // Change to index.html if you're not using .php for the form
    exit;
}
?>