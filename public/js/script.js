window.addEventListener("DOMContentLoaded", function() {

  console.log("DOM entièrement chargé");

  let listMedias = document.getElementById("list-medias");

  document.getElementById("togg1").addEventListener("click", () => {
    if(listMedias.classList.contains('d-sm-none')) {
      console.log("coucou j'étais en d-sm-none, mais plus depuis le click !")
      listMedias.classList.remove('d-sm-none');
    } else {
      console.log("cachés, les médias !")
      listMedias.classList.add('d-sm-none');
    }
  })

});
