function fetch_all(oURL){
    var sURL = oURL.split("=");
    var targetID = sURL[1];
    var targetTYPE = sURL[0].split("?")[1];
    //console.log(targetID, targetTYPE);
    let form = new FormData();
    form.append("action_type", "select");
    if(targetTYPE == "albums_id"){
        //ask for all song inside
    }
    else if(targetType == "tracks_id"){
        //ask for single track
    }
    else{
        //ask for artists
    }
    
    fetch("db_action.php", {
        method: "POST",
        body: form,
    })
    .then((res) => {
        return res.json();
    })
    .then((txt) => {
        //console.log(txt);
        //make_table(txt);
    });

}