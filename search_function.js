function view_full(){
    window.open(encodeURI('./view_full.html?'+'targetID='+event.target.name));
}

function delete_this(e){
    let ans = confirm("確定刪除此欄位？一但刪除便無法復原");
    if(ans){
        //delete
        alert("刪除成功");
        let delete_target = document.getElementById(e.name);
        delete_target.remove();
    }
}

function date_form(){
    if(event.target.value == "between"){
        document.getElementById("second_date").innerHTML = '~ '+'<input type="date" name="s_date" id="s_date"></input>';
    }
    else{
        document.getElementById("second_date").innerHTML = ' ';
    }
}

function insert_newrow(album_obj){
    let tbl = document.getElementById("albums");
    let new_row = tbl.insertRow(-1);
    new_row.id = album_obj.albums_id;
    let name = new_row.insertCell(-1);
    let type = new_row.insertCell(-1);
    let date = new_row.insertCell(-1);
    let view_but = new_row.insertCell(-1);
    let delete_but = new_row.insertCell(-1);
    name.innerHTML = album_obj.albums_name;
    type.innerHTML = album_obj.albums_type;
    date.innerHTML = album_obj.release_date;
    view_but.innerHTML = '<input type="button" name="'+album_obj.albums_id+'" onclick="view_full()" value="view"></input>';
    delete_but.innerHTML = '<input type="button" name="'+album_obj.albums_id+'" onclick="delete_this(this)" value="delete"></input>';
}