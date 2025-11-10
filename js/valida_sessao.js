async function valida_sessao() {
  try {
    const retorno = await fetch("../php/valida_sessao.php", { credentials: "include" });
    const resposta = await retorno.json();

    if (resposta.status === "erro") {
      window.location.href = "../login/";
    }
  } catch (err) {
    console.error("Erro ao validar sessão:", err);
    window.location.href = "../login/";
  }
}
valida_sessao();
