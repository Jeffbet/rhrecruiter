document.getElementById('talentPoolForm').addEventListener('submit', function (e) {
    e.preventDefault();
    
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const whatsapp = document.getElementById('whatsapp').value;
    const state = document.getElementById('state').value;
    const jobTitle = document.getElementById('jobTitle').value;
    const isPCD = document.getElementById('isPCD').checked ? 'Sim' : 'Não';
    const resume = document.getElementById('resume').files[0];

    // Logic to handle form submission (e.g., send data to backend)
    // For now, just showing the success modal

    document.getElementById('confirmationModal').style.display = 'flex';
});

document.getElementById('closeModal').addEventListener('click', function () {
    document.getElementById('confirmationModal').style.display = 'none';
});


document.addEventListener('DOMContentLoaded', function () {
    const menuButton = document.getElementById('menuButton');
    const navLinks = document.getElementById('navLinks');

    menuButton.addEventListener('click', function () {
        navLinks.classList.toggle('show');
    });
});
