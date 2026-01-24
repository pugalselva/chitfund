<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Login - Chit Fund Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f3f4f6;
            /* Slate 100 */
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
        }

        .login-header {
            background: #4f46e5;
            /* Member Brand Color */
            padding: 30px;
            text-align: center;
            color: white;
        }

        .login-header i {
            font-size: 3rem;
            margin-bottom: 10px;
            color: rgba(255, 255, 255, 0.9);
        }

        .login-body {
            background: white;
            padding: 40px 30px;
        }

        .form-floating>.form-control {
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .form-floating>.form-control:focus {
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            border-color: #4f46e5;
        }

        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
            transform: translateY(-1px);
        }

        .input-group-text {
            background-color: transparent;
            border-left: none;
            border-radius: 0 8px 8px 0;
            cursor: pointer;
        }

        .password-input {
            border-right: none;
            border-radius: 8px 0 0 8px !important;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="login-header">
            <i class="fa-solid fa-user-circle"></i>
            <h3 class="fw-bold mb-1">Member Login</h3>
            <p class="text-white-50 mb-0 small">Access your chit groups and auctions</p>
        </div>

        <div class="login-body">

            <form id="loginForm">
                <input type="hidden" name="role" id="role" value="member">

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="login_id" id="login_id" placeholder="Login ID"
                        required>
                    <label for="login_id">Member ID / Mobile</label>
                </div>

                <div class="input-group mb-4">
                    <div class="form-floating flex-grow-1">
                        <input type="password" class="form-control password-input" name="password" id="password"
                            placeholder="Password" required>
                        <label for="password">Password (UTR ID)</label>
                    </div>
                    <span class="input-group-text border-start-0" id="togglePassword">
                        <i class="far fa-eye text-muted"></i>
                    </span>
                </div>

                <button type="submit" class="btn btn-primary" id="loginBtn">
                    Sign In
                </button>
            </form>

            <div class="text-center mt-4">
                <a href="../index.php" class="small text-decoration-none text-muted">
                    <i class="fas fa-arrow-left me-1"></i> Back to Main Login
                </a>
            </div>
        </div>
    </div>

    <script>
        // Password Toggle
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        // Login Submission
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const loginId = document.getElementById('login_id').value;
            const password = document.getElementById('password').value;
            const role = document.getElementById('role').value; // 'member'
            const btn = document.getElementById('loginBtn');

            // Loading state
            const originalText = btn.textContent;
            btn.textContent = 'Verifying...';
            btn.disabled = true;

            fetch('../auth/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `login_id=${encodeURIComponent(loginId)}&password=${encodeURIComponent(password)}&role=${role}`
            })
                .then(res => res.text())
                .then(result => {
                    if (result.trim() === 'success') {
                        window.location.href = 'dashboard.php';
                    } else if (result.trim() === 'wrong_password') {
                        alert('Incorrect password');
                    } else if (result.trim() === 'not_found') {
                        alert('User not found or inactive');
                    } else {
                        alert('Login failed: ' + result);
                    }
                })
                .catch(err => {
                    alert('An error occurred. Please try again.');
                    console.error(err);
                })
                .finally(() => {
                    btn.textContent = originalText;
                    btn.disabled = false;
                });
        });
    </script>
</body>

</html>