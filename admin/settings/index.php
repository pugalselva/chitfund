<!DOCTYPE html>
<html>
<head>
<title>Settings</title>
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="wrapper">
<?php include '../layout/sidebar.php'; ?>

<div class="main">

<div class="topbar">
    <div>
        <div class="page-title">Settings</div>
        <div class="page-subtitle">Configure system settings and preferences</div>
    </div>
</div>

<div class="content">

<div class="settings-box">
    <h4>System Configuration</h4><br>

    <table class="settings-table">
        <thead>
            <tr>
                <th>Setting</th>
                <th>Value</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td><b>default_foreman_commission</b></td>
                <td>
                    <input class="setting-input" value="5">
                </td>
                <td>Default foreman commission percentage</td>
                <td>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </td>
                <td>
                    <button class="save-btn" onclick="saveSetting('default_foreman_commission')">
                        Save
                    </button>
                </td>
            </tr>

            <tr>
                <td><b>payment_grace_period</b></td>
                <td>
                    <input class="setting-input" value="5">
                </td>
                <td>Payment grace period in days</td>
                <td>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </td>
                <td>
                    <button class="save-btn" onclick="saveSetting('payment_grace_period')">
                        Save
                    </button>
                </td>
            </tr>

            <tr>
                <td><b>minimum_bid_difference</b></td>
                <td>
                    <input class="setting-input" value="1000">
                </td>
                <td>Minimum bid difference amount</td>
                <td>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </td>
                <td>
                    <button class="save-btn" onclick="saveSetting('minimum_bid_difference')">
                        Save
                    </button>
                </td>
            </tr>

            <tr>
                <td><b>auto_auction_reminder</b></td>
                <td>
                    <input class="setting-input" value="true">
                </td>
                <td>Send automatic auction reminders</td>
                <td>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </td>
                <td>
                    <button class="save-btn" onclick="saveSetting('auto_auction_reminder')">
                        Save
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

</div>
</div>
</div>

<script>
function saveSetting(key){
    alert("Setting saved: " + key);
    // Later:
    // fetch('save_settings.php', {method:'POST', body:...})
}
</script>

</body>
</html>
