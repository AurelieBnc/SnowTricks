window.addEventListener("DOMContentLoaded", function() {

  console.log("DOM entièrement chargé");
  /** Toggle Button in Edit Trick Template */
  var button = document.getElementById('toggleButton');
  var listMedias = document.getElementById('list-medias');

  button.addEventListener('click', function () {
    listMedias.style.display = (listMedias.style.display === 'none') ? 'block' : 'none';
    button.textContent = (listMedias.style.display === 'none') ? 'Afficher les médias' : 'Cacher les médias';
  });

});

