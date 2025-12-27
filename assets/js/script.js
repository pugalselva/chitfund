// Login
 const adminBtn = document.getElementById('adminBtn');
        const memberBtn = document.getElementById('memberBtn');
        const roleInput = document.getElementById('role');
        const loginBtn = document.getElementById('loginBtn');

        adminBtn.onclick = () => {
            adminBtn.classList.add('active');
            memberBtn.classList.remove('active');
            roleInput.value = 'admin';
            loginBtn.innerText = 'Login as Admin';
        };

        memberBtn.onclick = () => {
            memberBtn.classList.add('active');
            adminBtn.classList.remove('active');
            roleInput.value = 'member';
            loginBtn.innerText = 'Login as Member';
        };
// passowrd hide
const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.onclick = () => {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                togglePassword.innerText = '🙈';
            } else {
                passwordInput.type = 'password';
                togglePassword.innerText = '👁';
            }
        };