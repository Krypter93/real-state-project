//Enlace a redes sociales
const enlaces = document.querySelectorAll("i");

enlaces.forEach((enlace) => {
  enlace.addEventListener("click", (event) => {
    event.target.classList.forEach((clase) => {
      if (clase == "face") {
        window.open("https://www.facebook.com/", "_blank");
      } else if (clase == "xt") {
        window.open("https://twitter.com/", "_blank");
      } else if (clase == "inst") {
        window.open("https://www.instagram.com/", "_blank");
      } else if (clase == "tk") {
        window.open("https://www.tiktok.com/explore", "_blank");
      } else if (clase == "yt") {
        window.open("https://www.youtube.com/", "_blank");
      } else if (clase == "index") {
        /* Enlace a página de bienvenida */
        window.location.href = "../index.php";
      }
    });
  });
});
