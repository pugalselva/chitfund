<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chit-Funds</title>
    <style>
        body {
            background: #eef4ff;
            font-family: system-ui, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-card {
            width: 380px;
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
            text-align: center;
        }

        .login-card h2 {
            margin-bottom: 6px;
        }

        .login-card p {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 20px;
        }

        .role-switch {
            background: #f1f5f9;
            border-radius: 30px;
            display: flex;
            padding: 5px;
            margin-bottom: 20px;
        }

        .role-switch button {
            flex: 1;
            border: none;
            background: transparent;
            padding: 10px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 500;
        }

        .role-switch .active {
            background: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .1);
        }

        .form-group {
            text-align: left;
            margin-bottom: 14px;
        }

        .form-group label {
            font-size: 13px;
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 15px 12px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            width: 100%;
            /* padding:10px 40px 10px 12px; */
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 16px;
            user-select: none;
            opacity: .6;
        }

        .toggle-password:hover {
            opacity: 1;
        }


        .demo {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 15px;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            cursor: pointer;
        }
    </style>

</head>

<body>
    <div class="login-card">
        <h2>Chit Fund System</h2>
        <p>Login to manage your chit fund groups</p>

        <!-- ROLE SWITCH -->
        <div class="role-switch">
            <button id="adminBtn" class="active">Admin</button>
            <button id="memberBtn">Member</button>
        </div>

        <form id="loginForm">
            <input type="hidden" name="role" id="role" value="admin">

            <div class="form-group">
                <label>Login ID</label>
                <input type="text" name="login_id" id="login_id">
            </div>

            <div class="form-group password-group">
                <label>Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password">
                    <span class="toggle-password" id="togglePassword">👁</span>
                </div>
            </div>

            <div class="demo">Demo password: demo</div>

            <button type="submit" class="login-btn" id="loginBtn">
                Login as Admin
            </button>
        </form>

    </div>

    <script src="assets/js/script.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const loginId = document.getElementById('login_id').value;
            const password = document.getElementById('password').value;
            const role = document.getElementById('role').value;

            fetch('auth/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `login_id=${loginId}&password=${password}&role=${role}`
                })
                .then(res => res.text())
                .then(result => {
                    if (result === 'success') {
                        if (role === 'admin') {
                            window.location.href = 'admin/dashboard.php';
                        } else {
                            window.location.href = 'member/dashboard.php';
                        }
                    } else if (result === 'wrong_password') {
                        alert('Incorrect password');
                    } else if (result === 'not_found') {
                        alert('User not found or inactive');
                    } else {
                        alert('Login failed');
                    }
                });
        });
    </script>

</body>

</html>
