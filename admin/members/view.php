<!DOCTYPE html>
<html>

<head>
    <title>Member Profile</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div class="main">
            <div class="topbar">
                <div>
                    <h2>John Doe</h2>
                    <small>Member ID: M001</small>
                </div>

            </div>

            <div class="content">

                <div class="tabs">
                    <a href="javascript:void(0)" id="tabPersonal" class="active" onclick="showTab('personal')">👤 Personal
                        Details</a>

                    <a href="javascript:void(0)" id="tabBank" onclick="showTab('bank')">🏦 Bank Details</a>

                    <a href="javascript:void(0)" id="tabActivity" onclick="showTab('activity')">📊 Activity</a>
                </div>


                <div id="personal" class="tab-content active profile-box">

                    <h4>Personal Information</h4><br>

                    <div class="info-grid">
                        <div>
                            <b>Full Name</b><br>John Doe<br><br>
                            <b>Date of Birth</b><br>15/06/1985<br><br>
                            <b>Address</b><br>Chennai, Tamil Nadu
                        </div>

                        <div>
                            <b>Gender</b><br>Male<br><br>
                            <b>Age</b><br>40 years<br><br>
                            <b>Mobile</b><br>+91 98765 43210
                        </div>
                    </div>
                </div>
                <div id="bank" class="tab-content profile-box" style="display:none;">
                    <h4>Bank Account Details</h4><br>
                    <!-- your bank details HTML here -->
                </div>


                <br>

                <div id="activity" class="tab-content profile-box" style="display:none;">
                    <h4>Member Activity</h4><br>

                    <div class="info-grid">
                        <div class="stat-card stat-blue">
                            <h2>2</h2>
                            <small>Active Groups</small>
                        </div>

                        <div class="stat-card stat-green">
                            <h2>₹2.4L</h2>
                            <small>Total Contributions</small>
                        </div>

                        <div class="stat-card stat-purple">
                            <h2>24</h2>
                            <small>Member Since (months)</small>
                        </div>
                    </div>
                </div>
                <span class="badge active">Active</span>
            </div>
        </div>
    </div>

    <!-- script -->
    <script>
        function showTab(tab) {
            // hide all contents
            document.getElementById('personal').style.display = 'none';
            document.getElementById('bank').style.display = 'none';
            document.getElementById('activity').style.display = 'none';

            // remove active from tabs
            document.getElementById('tabPersonal').classList.remove('active');
            document.getElementById('tabBank').classList.remove('active');
            document.getElementById('tabActivity').classList.remove('active');

            // show selected
            document.getElementById(tab).style.display = 'block';

            if (tab === 'personal') {
                document.getElementById('tabPersonal').classList.add('active');
            }
            if (tab === 'bank') {
                document.getElementById('tabBank').classList.add('active');
            }
            if (tab === 'activity') {
                document.getElementById('tabActivity').classList.add('active');
            }
        }
    </script>


</body>

</html>
