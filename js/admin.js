async function valida_sessao_admin() {
  try {
    const retorno = await fetch("../php/valida_sessao_admin.php", { credentials: "include" });
    const resposta = await retorno.json();

    if (resposta.status === "erro") {
      alert("Acesso negado.")
      window.location.href = "../login/";
    }
  } catch (err) {
    console.error("Erro ao validar sessão:", err);
    window.location.href = "../login/";
  }
}
valida_sessao_admin();
