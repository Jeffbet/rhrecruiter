document.addEventListener('DOMContentLoaded', () => {
    const initialJobs = [
        {
            jobTitle: "Desenvolvedor Backend",
            jobDescription: "O Desenvolvedor Backend será responsável por desenvolver e manter a lógica de servidor, bancos de dados e integração com APIs. Requisitos incluem experiência com Node.js, MongoDB, e implementação de microservices.",
            jobLocation: "Rio de Janeiro",
            jobWorkMode: "Híbrido",
            jobType: "Contrato PJ",
            jobSalary: "R$ 9000,00",
            jobCategory: "Tecnologia",
            createdDate: "05/07/2024"
        },
        {
            jobTitle: "Analista de Marketing",
            jobDescription: "O Analista de Marketing será responsável por desenvolver e executar estratégias de marketing digital.",
            jobLocation: "São Paulo",
            jobWorkMode: "Presencial",
            jobType: "CLT",
            jobSalary: "R$ 5000,00",
            jobCategory: "Marketing",
            createdDate: "10/07/2024"
        }
    ];

    const jobDetailWrapper = document.getElementById('jobDetailWrapper');
    const jobDetailContent = document.getElementById('jobDetailContent');

    function renderJobDetail(job) {
        jobDetailContent.innerHTML = `
            <div class="job-title">${job.jobTitle}</div>
            <div class="job-info"><strong>Localização:</strong> ${job.jobLocation}</div>
            <div class="job-info"><strong>Modalidade:</strong> ${job.jobWorkMode}</div>
            <div class="job-info"><strong>Descrição:</strong> ${job.jobDescription}</div>
            <div class="job-info"><strong>Salário:</strong> ${job.jobSalary}</div>
            <div class="job-info"><strong>Tipo de Vaga:</strong> ${job.jobType}</div>
            <button class="apply-button" onclick="openModal('${job.jobTitle}')">Candidatar-se</button>
            <div class="share-icons">
                <a href="#"><i class="fab fa-whatsapp share-icon"></i></a>
                <a href="#"><i class="fab fa-facebook-f share-icon"></i></a>
                <a href="#"><i class="fab fa-linkedin-in share-icon"></i></a>
                <a href="#"><i class="fab fa-instagram share-icon"></i></a>
            </div>
        `;
    }

    // Render the first job by default
    if (initialJobs.length > 0) {
        renderJobDetail(initialJobs[0]);
    }

    window.openModal = function(jobTitle) {
        document.getElementById('modalJobTitle').textContent = jobTitle;
        document.getElementById('applyModal').style.display = 'block';
    };

    window.closeModal = function() {
        document.getElementById('applyModal').style.display = 'none';
    };
});
