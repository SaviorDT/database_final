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
function make_table(album_obj){
    let tbl0 = document.getElementById("albums");
    document.getElementById("tbl_holder").removeChild(tbl0);
    let tbl = document.createElement("table");
    tbl.id = "albums";
    //name first, then value
    let total_column = album_obj.description.length;
    let new_row = tbl.insertRow(-1);
    for(let j=1;j<total_column;j++){
        let new_cell = new_row.insertCell(-1);
        new_cell.innerHTML = album_obj.description[j];
    }
    let view_but = new_row.insertCell(-1);
    let delete_but = new_row.insertCell(-1);
    view_but.innerHTML = "詳細資料";
    delete_but.innerHTML = "刪除";

    let row_length = album_obj.rows.length;
    for(let i=0;i<row_length;i++){
        let new_row = tbl.insertRow(-1);
        new_row.id = album_obj.rows[i][0];
        for(let j=1;j<total_column;j++){
            let new_cell = new_row.insertCell(-1);
            new_cell.innerHTML = album_obj.rows[i][j];
        }
        let view_but = new_row.insertCell(-1);
        let delete_but = new_row.insertCell(-1);
        view_but.innerHTML = '<input type="button" name="'+album_obj.rows[i][0]+'" onclick="view_full(this)" value="view"></input>';
        delete_but.innerHTML = '<input type="button" name="'+album_obj.rows[i][0]+'" onclick="delete_this(this)" value="delete"></input>';
    }
    document.getElementById("tbl_holder").appendChild(tbl);
}