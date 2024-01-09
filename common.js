function jump(){
    //console.log(event.target.name);
    window.location.href= event.target.name+".html";
}

function check_form(form){
    for (var pair of form.entries()) {
        console.log(pair[0]+ ', ' + pair[1]); 
    }
}

function autoChangeTable(menu) {
    menu.addEventListener('change', function() {
        let url = this.value;
        if (url) {
            window.location.href = 'search_' + url + '.html';
        }
    });
}