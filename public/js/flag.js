let valueToImage = {
  en : "/uploads/images/us-flag.png",
  fr : "/uploads/images/france-flag.png"
};

let selectFlagOptions = document.querySelector('select');
let optionValue1 = selectFlagOptions.options[0];
let optionValue2 = selectFlagOptions.options[1];

function getCurrentURL () {
  return window.location.href;
}

const url = getCurrentURL();

if(url.includes("en")) {
  optionValue1.value = "en";
  optionValue1.innerText = "EN";
  optionValue2.value = "fr";
  optionValue2.innerText = "FR";
} else {
  optionValue1.value = "fr";
  optionValue1.innerText = "FR";
  optionValue2.value = "en";
  optionValue2.innerText = "EN";
}

if (optionValue1.value === "en" && optionValue2.value === "fr") {
  document.getElementById("flag").src = valueToImage["en"];
} 

if (optionValue1.value === "fr" && optionValue2.value === "en") {
  document.getElementById("flag").src = valueToImage["fr"];
} 
 
function selectFlag(select) {
  let valeur = select.options[select.selectedIndex].value;
  document.getElementById("flag").src = valueToImage[valeur];
}