document.addEventListener('DOMContentLoaded', () => {
    const initialJobs = [
        {
            jobTitle: "Desenvolvedor Backend",
            jobDescription: "O Desenvolvedor Backend será responsável por desenvolver e manter a lógica de servidor, bancos de dados e integração com APIs. Requisitos incluem experiência com Node.js, MongoDB, e implementação de microservices.",
            jobLocation: ["Rio de Janeiro"],
            jobWorkMode: ["Híbrido"],
            jobBenefits: ["Vale Refeição", "Plano de Saúde", "Gympass"],
            jobType: ["Contrato PJ"],
            jobSalary: "R$ 9000,00",
            jobCategory: ["Tecnologia"],
            companyName: "Innovative Solutions",
            createdDate: "05/07/2024"
        },
        {
            jobTitle: "Analista de Service Desk",
            jobDescription: "Ser responsável pelo atendimento aos usuários da plataforma pelos canais, chat e e-mail...",
            jobLocation: ["Minas Gerais"],
            jobWorkMode: ["Home Office"],
            jobBenefits: ["Vale Refeição", "Plano de Saúde", "Gympass"],
            jobType: ["Contrato PJ"],
            jobSalary: "R$ 2000,00",
            jobCategory: ["Tecnologia"],
            companyName: "Innovative Solutions",
            createdDate: "05/07/2024"
        },
        {
            jobTitle: "Analista de Marketing",
            jobDescription: "O Analista de Marketing será responsável por desenvolver e executar estratégias de marketing digital...",
            jobLocation: ["São Paulo"],
            jobWorkMode: ["Presencial"],
            jobBenefits: ["Vale Refeição", "Vale Transporte", "Plano Odontológico"],
            jobType: ["CLT"],
            jobSalary: "R$ 5000,00",
            jobCategory: ["Marketing"],
            companyName: "Market Leaders",
            createdDate: "10/07/2024"
        },
        // Adicione outras vagas conforme necessário...
    ];

    const jobList = document.getElementById('jobList');

    initialJobs.forEach(job => {
        const jobWrapper = document.createElement('div');
        jobWrapper.className = 'job-wrapper';

        const jobTitle = document.createElement('h3');
        jobTitle.className = 'job-title';
        jobTitle.textContent = job.jobTitle;
        jobTitle.addEventListener('click', () => openModal(job.jobTitle));
        jobWrapper.appendChild(jobTitle);

        const jobDetails = document.createElement('div');
        jobDetails.className = 'job-details';

        const jobDescription = document.createElement('p');
        jobDescription.textContent = job.jobDescription;
        jobDetails.appendChild(jobDescription);

        const jobLocation = document.createElement('p');
        jobLocation.textContent = `Localização: ${job.jobLocation.join(', ')}`;
        jobDetails.appendChild(jobLocation);

        const jobSalary = document.createElement('p');
        jobSalary.textContent = `Salário: ${job.jobSalary}`;
        jobDetails.appendChild(jobSalary);

        jobWrapper.appendChild(jobDetails);
        jobList.appendChild(jobWrapper);
    });

    const applyModal = document.getElementById('applyModal');
    const modalJobTitle = document.getElementById('modalJobTitle');
    const closeButton = document.querySelector('.close-button');
    const applyForm = document.getElementById('applyForm');

    function openModal(jobTitle) {
        modalJobTitle.textContent = jobTitle;
        applyModal.style.display = 'block';
    }

    function closeModal() {
        applyModal.style.display = 'none';
    }

    closeButton.addEventListener('click', closeModal);

    window.onclick = function(event) {
        if (event.target == applyModal) {
            applyModal.style.display = 'none';
        }
    };

    applyForm.addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Sua candidatura foi enviada com sucesso!');
        closeModal();
        applyForm.reset();
    });

    // Exemplo de uso de ScrollReveal
    ScrollReveal().reveal('.job-wrapper', { delay: 200 });
    ScrollReveal().reveal('.filter-wrapper', { delay: 300 });
    ScrollReveal().reveal('.job-list-title', { delay: 400 });
});
