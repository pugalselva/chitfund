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
// Toast Notification
let toastTimer;

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const msg   = document.getElementById('toastMsg');
    const icon  = toast.querySelector('.toast-icon');

    msg.textContent = message;

    toast.classList.remove('success','error','warning');

    if(type === 'error'){
        toast.classList.add('error');
        icon.className = 'fas fa-exclamation-circle toast-icon';
    } 
    else if(type === 'warning'){
        toast.classList.add('warning');
        icon.className = 'fas fa-triangle-exclamation toast-icon';
    }
    else{
        icon.className = 'fas fa-check-circle toast-icon';
    }

    toast.style.display = 'flex';

    clearTimeout(toastTimer);
    toastTimer = setTimeout(hideToast, 2500);
}

function hideToast(){
    document.getElementById('toast').style.display = 'none';
}
