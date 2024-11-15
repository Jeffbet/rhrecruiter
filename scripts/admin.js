let userPermissions = [];

// Função de Login
function handleLogin(event) {
    event.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    if (username === "adminrecruiter" && password === "91085467Jl") {
        // Login como administrador principal
        document.getElementById('loginContainer').style.display = 'none';
        document.getElementById('adminDashboard').style.display = 'block';
        userPermissions = ["gerenciar_usuarios", "criar_vaga", "editar_vaga", "ver_candidaturas", "aprovar_vagas"];
        handleUserPermissions();
    } else {
        // Requisição AJAX para autenticação de usuário comum
        $.ajax({
            url: "/login.php",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({ username, password }),
            success: function(response) {
                if (response.success) {
                    document.getElementById('loginContainer').style.display = 'none';
                    document.getElementById('adminDashboard').style.display = 'block';
                    userPermissions = response.permissions || [];
                    handleUserPermissions();
                } else {
                    alert('Usuário ou senha inválidos');
                }
            },
            error: function(err) {
                alert("Erro ao autenticar: " + err.responseText);
            }
        });
    }
}

function showSection(sectionId) {
    // Ocultar todas as seções
    $(".content").hide();
    // Mostrar a seção clicada
    $("#" + sectionId).show();
}


// Adicionar eventos de clique para os itens do menu
$(document).ready(function() {
    $("#jobSectionLink").on("click", function() {
        showSection('jobSection');
    });

    $("#pendingJobsMenuItem").on("click", function() {
        showSection('pendingJobsSection');
        loadPendingJobs();
    });

    $("#manageUsersMenuItem").on("click", function() {
        showSection('manageUsersSection');
    });
});

function handleUserPermissions() {
    if (userPermissions.includes("gerenciar_usuarios")) {
        document.getElementById('manageUsersMenuItem').style.display = 'block';
    }

    if (userPermissions.includes("aprovar_vagas")) {
        document.getElementById('pendingJobsMenuItem').style.display = 'block';
    }
}

function createUser(event) {
    event.preventDefault();
    const username = $("#newUsername").val();
    const password = $("#newPassword").val();
    const permissions = [];

    if ($("#permCriarVaga").is(":checked")) permissions.push("criar_vaga");
    if ($("#permEditarVaga").is(":checked")) permissions.push("editar_vaga");
    if ($("#permVerCandidaturas").is(":checked")) permissions.push("ver_candidaturas");

    $.ajax({
        url: "/create_user.php",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({ username, password, permissions }),
        success: function(response) {
            if (response.success) {
                alert("Usuário criado com sucesso!");
                $("#createUserForm")[0].reset();
            } else {
                alert("Erro ao criar usuário: " + response.message);
            }
        },
        error: function(err) {
            alert("Erro ao criar usuário: " + err.responseText);
        }
    });
}

function handleLogout() {
    document.getElementById('adminDashboard').style.display = 'none';
    document.getElementById('loginContainer').style.display = 'block';
}

function loadPendingJobs() {
    $.ajax({
        url: "/vagas_api.php?status=pendente",
        method: "GET",
        success: function(response) {
            const jobs = response;
            const pendingJobList = $("#pendingJobList tbody");
            pendingJobList.empty();

            jobs.forEach((job) => {
                pendingJobList.append(`
                    <tr>
                        <td>${job.titulo}</td>
                        <td>${job.estado}</td>
                        <td>${job.modalidade}</td>
                        <td>
                            <button class="btn btn-success btn-sm" onclick="approveJob(${job.id})">Aprovar</button>
                            <button class="btn btn-danger btn-sm" onclick="rejectJob(${job.id})">Rejeitar</button>
                        </td>
                    </tr>
                `);
            });
        },
        error: function(err) {
            alert("Erro ao carregar as vagas pendentes: " + err.responseText);
        }
    });
}

// Aprovar vaga
function approveJob(id) {
    $.ajax({
        url: "/vagas_api.php",
        method: "PUT",
        contentType: "application/json",
        data: JSON.stringify({ id: id, status: 'aprovada' }),
        success: function(response) {
            alert("Vaga aprovada com sucesso!");
            loadPendingJobs();
        },
        error: function(err) {
            alert("Erro ao aprovar a vaga: " + err.responseText);
        }
    });
}

// Rejeitar vaga
function rejectJob(id) {
    $.ajax({
        url: "/vagas_api.php",
        method: "PUT",
        contentType: "application/json",
        data: JSON.stringify({ id: id, status: 'rejeitada' }),
        success: function(response) {
            alert("Vaga rejeitada com sucesso!");
            loadPendingJobs();
        },
        error: function(err) {
            alert("Erro ao rejeitar a vaga: " + err.responseText);
        }
    });
}

$(document).ready(function() {
    



    loadJobs();

    $("#jobForm").on("submit", function(event) {
        event.preventDefault();

        // Coletar dados do formulário
        const jobId = $("#jobId").val();
        const jobTitle = $("#jobTitle").val();
        const state = $("#state").val();
        const workMode = $("#workMode").val();
        const seniority = $("#seniority").val();
        const contractType = $("#contractType").val();
        const salary = $("#salary").val();
        const benefits = $("#benefits").val();
        const category = $("#category").val();
        const description = $("#description").val();
        const requirements = $("#requirements").val();
        const pcd = $("#pcd").val();

        const jobData = {
            id: jobId,
            jobTitle,
            state,
            workMode,
            seniority,
            contractType,
            salary,
            benefits,
            category,
            description,
            requirements,
            pcd
        };

        // Se tiver um ID, atualiza. Caso contrário, cria uma nova vaga.
        if (jobId) {
            $.ajax({
                url: "/vagas_api.php",
                method: "PUT",
                contentType: "application/json",
                data: JSON.stringify(jobData),
                success: function(response) {
                    alert("Vaga atualizada com sucesso!");
                    loadJobs();
                },
                error: function(err) {
                    alert("Erro ao atualizar a vaga: " + err.responseText);
                }
            });
        } else {
            $.ajax({
                url: "/vagas_api.php",
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify(jobData),
                success: function(response) {
                    alert("Vaga cadastrada com sucesso!");
                    loadJobs();
                },
                error: function(err) {
                    alert("Erro ao cadastrar a vaga: " + err.responseText);
                }
            });
        }
        $("#jobForm")[0].reset();
    });
});

function loadJobs() {
    $.ajax({
        url: "/vagas_api.php",
        method: "GET",
        success: function(response) {
            const jobs = response;
            const jobList = $("#jobList tbody");
            jobList.empty();

            jobs.forEach((job) => {
                jobList.append(`
                    <tr>
                        <td>${job.titulo}</td>
                        <td>${job.estado}</td>
                        <td>${job.modalidade}</td>
                        <td>${job.senioridade}</td>
                        <td>${job.tipo_contrato}</td>
                        <td>${job.salario}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="editJob(${job.id})">Editar</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteJob(${job.id})">Excluir</button>
                        </td>
                    </tr>
                `);
            });
        },
        error: function(err) {
            alert("Erro ao carregar as vagas: " + err.responseText);
        }
    });
}

function editJob(id) {
    $.ajax({
        url: `/vagas_api.php?id=${id}`,
        method: "GET",
        success: function(job) {
            $("#jobId").val(job.id);
            $("#jobTitle").val(job.titulo);
            $("#state").val(job.estado);
            $("#workMode").val(job.modalidade);
            $("#seniority").val(job.senioridade);
            $("#contractType").val(job.tipo_contrato);
            $("#salary").val(job.salario);
            $("#benefits").val(job.beneficios);
            $("#category").val(job.categoria);
            $("#description").val(job.descricao);
            $("#requirements").val(job.requisitos);
            $("#pcd").val(job.pcd);
        },
        error: function(err) {
            alert("Erro ao carregar a vaga: " + err.responseText);
        }
    });
}

function deleteJob(id) {
    if (confirm("Tem certeza que deseja excluir essa vaga?")) {
        $.ajax({
            url: "/vagas_api.php",
            method: "DELETE",
            contentType: "application/json",
            data: JSON.stringify({ id }),
            success: function(response) {
                alert("Vaga excluída com sucesso!");
                loadJobs();
            },
            error: function(err) {
                alert("Erro ao excluir a vaga: " + err.responseText);
            }
        });
    }
}



// Salvar vaga (Criar ou Atualizar)
function saveJob() {
    const jobId = $("#jobId").val();
    const jobTitle = $("#jobTitle").val();
    const state = $("#state").val();
    const workMode = $("#workMode").val();
    const seniority = $("#seniority").val();
    const contractType = $("#contractType").val();
    const salary = $("#salary").val();
    const benefits = $("#benefits").val();
    const category = $("#category").val();
    const description = $("#description").val();
    const requirements = $("#requirements").val();
    const pcd = $("#pcd").val();

    const jobData = {
        id: jobId,
        jobTitle,
        state,
        workMode,
        seniority,
        contractType,
        salary,
        benefits,
        category,
        description,
        requirements,
        pcd
    };

    // Se tiver um ID, atualiza. Caso contrário, cria uma nova vaga.
    if (jobId) {
        $.ajax({
            url: "/vagas_api.php",
            method: "PUT",
            contentType: "application/json",
            data: JSON.stringify(jobData),
            success: function(response) {
                alert("Vaga atualizada com sucesso!");
                loadJobs();
            },
            error: function(err) {
                alert("Erro ao atualizar a vaga: " + err.responseText);
            }
        });
    } else {
        $.ajax({
            url: "/vagas_api.php",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify(jobData),
            success: function(response) {
                alert("Vaga cadastrada com sucesso!");
                loadJobs();
            },
            error: function(err) {
                alert("Erro ao cadastrar a vaga: " + err.responseText);
            }
        });
    }
    $("#jobForm")[0].reset();
}
