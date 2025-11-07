// Selecionando os elementos do DOM
const card = document.querySelector('.card');
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
const flipToRegisterLink = document.getElementById('flipToRegister');
const flipToLoginLink = document.getElementById('flipToLogin');


flipToRegisterLink.addEventListener('click', (event) => {
    event.preventDefault(); 
    card.classList.add('is-flipped');
});

flipToLoginLink.addEventListener('click', (event) => {
    event.preventDefault(); 
    card.classList.remove('is-flipped');
});