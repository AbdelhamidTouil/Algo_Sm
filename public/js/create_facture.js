    
  
    function myFunction() {
    var facturVar=document.getElementById("facture_type").value;
    var fournisseurVar = document.getElementById("fournisseur");
    var clientVar = document.getElementById("client");
   
    //var next = document.getElementsByClassName("btn btn-secondary sw-btn-next");
   if(facturVar == 'Vente')
   {
       clientVar.style.display = "block";
       fournisseurVar.style.display = "none";
      // next.style.color = "red";

      
   }
   else if(facturVar =="Achat"){
        clientVar.style.display = "none";
        fournisseurVar.style.display = "block";
   }
       
 }
