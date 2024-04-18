//Captura de div
const texto = document.querySelector(".texto");

//Array de frases
const array = [
  "Tu hogar, nuestra pasión",
  "Donde los sueños encuentran un hogar",
  "Encuentra tu lugar en el mundo",
  "Hacemos que encontrar un hogar sea fácil y emocionante",
];

let arrIndex = 0;
let arrChar = 0;

frases();

//Función que inserta texto en bucle
function frases() {
  arrChar++;

  texto.innerHTML = `<h3>${array[arrIndex].slice(0, arrChar)}</h3>`;

  if (arrChar >= array[arrIndex].length) {
    arrIndex = (arrIndex + 1) % array.length;
    arrChar = 0;
  }

  setTimeout(frases, 150);
}
