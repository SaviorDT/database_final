function jump(){
    //console.log(event.target.name);
    window.location.href= event.target.name+".html";
}

function check_form(form){
    for (var pair of form.entries()) {
        console.log(pair[0]+ ', ' + pair[1]); 
    }
}