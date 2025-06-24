document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('planForm');
  const message = document.getElementById('planMessage');

  form.addEventListener('submit', async function (e) {
    e.preventDefault();

    const fileInput = document.getElementById('planFile');
    const desc = document.getElementById('planDesc').value.trim();
    const file = fileInput.files[0];

    if (!file || !desc) {
      message.textContent = "Veuillez remplir tous les champs.";
      return;
    }

    const formData = new FormData();
    formData.append('description', desc);
    formData.append('plan', file);

    try {
      const res = await fetch('plans.php', {
        method: 'POST',
        body: formData
      });
      const text = await res.text();
      if (text.includes('succès')) {
        message.textContent = "✅ Plan ajouté avec succès !";
        form.reset();
      } else {
        message.textContent = text;
      }
    } catch (err) {
      message.textContent = "Erreur réseau ou serveur.";
    }
  });
});
