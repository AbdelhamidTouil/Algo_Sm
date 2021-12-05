function myFunction() {
    var facturVar = document.getElementById("facture_type").value;
    var fournisseurVar = document.getElementById("fournisseur");
    var clientVar = document.getElementById("client");

    //var next = document.getElementsByClassName("btn btn-secondary sw-btn-next");
    if (facturVar == '0') {
        clientVar.style.display = "block";
        fournisseurVar.style.display = "none";
        // next.style.color = "red";


    } else if (facturVar == "1") {
        clientVar.style.display = "none";
        fournisseurVar.style.display = "block";
    }
}