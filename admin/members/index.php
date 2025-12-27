<!DOCTYPE html>
<html>

<head>
    <title>Members</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div class="main">
            <div class="topbar">
                <div>
                    <div class="page-title">Members</div>
                    <div class="page-subtitle">View all registered members</div>
                </div>
                <div class="topbar">
                    <div>
                        <b>Admin User</b><br>
                        sandy@gmail.com
                        <a href="../../logout.php" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>

                </div>
            </div>

            <div class="content">

                <a href="create.php"><button class="btn-primary">＋ Enroll Member</button></a>
                <div class="table-box">
                    <b>All Members (5)</b><br><br>

                    <table>
                        <thead>
                            <tr>
                                <th>Member ID</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Active Groups</th>
                                <th>Total Contributions</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>M001</td>
                                <td>John Doe</td>
                                <td>+91 98765 43210</td>
                                <td>john.doe@email.com</td>
                                <td>2</td>
                                <td>₹2,40,000</td>
                                <td><span class="badge active">Active</span></td>
                                <td><a href="view.php?id=M001">👁 View</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
