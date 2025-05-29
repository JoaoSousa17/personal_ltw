function togglePasswordVisibility(icon) {
    let input = icon.previousElementSibling;
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

async function handleSignup(event) {
    event.preventDefault();

    const name = document.querySelector('#signup-form input[name="name"]').value;
    const email = document.querySelector('#signup-form input[name="email"]').value;
    const phone = document.querySelector('#signup-form input[name="phone"]').value;
    const password1 = document.querySelector('#signup-form input[name="password"]').value;
    const password2 = document.querySelector('#signup-form input[name="password2"]').value;

    if (!name || !email || !phone || !password1 || !password2) {
        alert("Preencha todos os campos.");
        return;
    }

    if (password1 !== password2) {
        alert("As senhas não coincidem.");
        return;
    }

    if (!validateEmail(email)) {
        alert("Por favor, insira um email válido.");
        return;
    }

    event.target.submit();
}

async function handleLogin(event) {
    event.preventDefault();

    const email = document.querySelector('#login-form input[name="email"]').value;
    const password = document.querySelector('#login-form input[name="password"]').value;

    if (!email || !password) {
        alert("Preencha todos os campos.");
        return;
    }

    if (!validateEmail(email)) {
        alert("Por favor, insira um email válido.");
        return;
    }

    event.target.submit();
}

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showTab(tabName) {
    const loginForm = document.getElementById('login-form');
    const signupForm = document.getElementById('signup-form');
    const loginTab = document.getElementById('login-tab');
    const signupTab = document.getElementById('signup-tab');
    const underline = document.querySelector('.tab-underline');

    // Alternar visibilidade dos formulários
    loginForm.classList.add('hidden');
    signupForm.classList.add('hidden');
    document.getElementById(tabName).classList.remove('hidden');

    // Alternar aba ativa
    loginTab.classList.remove('active');
    signupTab.classList.remove('active');

    const activeTab = tabName === 'login-form' ? loginTab : signupTab;
    activeTab.classList.add('active');

    // Calcular nova posição horizontal da underline centralizada na aba
    const tabRect = activeTab.getBoundingClientRect();
    const tabsRect = activeTab.parentElement.getBoundingClientRect();
    const centerX = tabRect.left + tabRect.width / 2 - tabsRect.left;
    underline.style.left = `${centerX}px`;
    underline.style.transform = "translateX(-50%)";
}

document.addEventListener("DOMContentLoaded", function () {
    showTab('login-form');

    // Conecta os handlers aos formulários
    document.getElementById('login-form').addEventListener('submit', handleLogin);
    document.getElementById('signup-form').addEventListener('submit', handleSignup);
});
