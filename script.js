document.addEventListener("DOMContentLoaded", function () {
  // Salvar o conteúdo quando o usuário clicar em salvar
  document
    .getElementById("save-custom-content")
    .addEventListener("click", function () {
      // Recuperar o conteúdo do editor
      var content = document.getElementById("custom_script_content").value;

      // Use AJAX para enviar o conteúdo para o WordPress
      var data = {
        action: "save_custom_content",
        post_id: custom_html_script_ajax_object.post_id,
        custom_content: content,
        nonce: custom_html_script_ajax_object.nonce,
      };

      fetch(custom_html_script_ajax_object.ajaxurl, {
        method: "POST",
        body: new URLSearchParams(data),
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
      })
        .then(function (response) {
          // Atualize a página após salvar
          window.location.reload();
        })
        .catch(function (error) {
          console.error("Erro ao salvar conteúdo personalizado:", error);
        });
    });
});
