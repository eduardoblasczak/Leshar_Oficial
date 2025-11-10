document.addEventListener('DOMContentLoaded', () => {
    valida_sessao();

    document.getElementById("sair").addEventListener("click", () => {
        logoff();
    });
});

async function logoff() {
    const retorno = await fetch('../php/logoff.php');
    const resposta = await retorno.json();
    if (resposta.status == "ok") {
        alert("Você saiu do sistema.");
        window.location.href = "../login/";
    }  
}   