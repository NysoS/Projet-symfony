function deleteVille(el) {
console.log(el.dataset.villeid);
console.log(el.dataset.villenom);

let villeId = el.dataset.villeid;

// recuperation du titre
titleElement = document.getElementById('exampleModalLabelNomVille');
aElement = document.getElementById('exampleBtnNomVille');
titleElement.innerText = 'Voulez-vous supprimer la ville "' + el.dataset.villenom + '" ? ';

p ="{{ path('app_ville_delete', {id:maVille}) }}";
p.replace('maVille', villeId);
aElement.href = p;
}
