document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('planForm');
  const message = document.getElementById('planMessage');

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const fileInput = document.getElementById('planFile');
    const desc = document.getElementById('planDesc').value.trim();
    const file = fileInput.files[0];

    if (!file || !desc) {
      message.textContent = "Veuillez remplir tous les champs.";
      return;
    }

    const reader = new FileReader();
    reader.onload = function (event) {
      const dataUrl = event.target.result;

      // Création d'un objet plan
      const plan = {
        description: desc,
        fileName: file.name,
        fileData: dataUrl,
        date: new Date().toLocaleString()
      };

      // Sauvegarde dans localStorage
      let plans = JSON.parse(localStorage.getItem('plans')) || [];
      plans.push(plan);
      localStorage.setItem('plans', JSON.stringify(plans));

      message.textContent = "✅ Plan ajouté avec succès !";
      form.reset();
    };

    reader.readAsDataURL(file); // Encode le fichier en base64
  });
});
