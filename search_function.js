function view_full(src){
    sessionStorage.setItem('ID', src.name);
    window.open(encodeURI('./show_'+src.className.slice(0,-1)+'_detail.html?'+event.target.className+'_id='+event.target.name));
}

function update_this(e, col_row, val_row){
    let ans = confirm("確定修改此欄位？一但修改便無法復原");
    if(ans){
        //delete
        alert("修改成功");

        let form = new FormData();
        //fetch, if success->delete row
        form.append("action_type", "update");
        form.append("table", e.className);
        form.append("ID", e.name);
        
        let columns = [];
        let values = [];
        for(let item=0; item<col_row.length - 3; item++) {
            columns.push(col_row[item].innerText);
            values.push(val_row[item].innerText);
        }
        form.append("action_columns", JSON.stringify(columns));
        form.append("values", JSON.stringify(values));

        fetch("db_action.php", {
            method: "POST",
            body: form,
        })
        .then((res) => {
            return res.json();
        })
        .then((txt) => {
            console.log(txt);
        });
    }
}

function delete_this(e){
    let ans = confirm("確定刪除此欄位？一但刪除便無法復原");
    if(ans){
        //delete
        alert("刪除成功");
        let delete_target = document.getElementById(e.name);
        let form = new FormData();
        //fetch, if success->delete row
        form.append("action_type", "delete");
        form.append("table", e.className);
        form.append("ID", e.name);

        fetch("db_action.php", {
            method: "POST",
            body: form,
        })
        .then((res) => {
            return res.json();
        })
        .then((txt) => {
            delete_target.remove();
        });
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
    let tbl = document.getElementById("albums");
    tbl.innerHTML = '';
    
    if(album_obj.stat == 0) {
        tbl.innerText = album_obj.description;
        return;
    }

    let total_column = album_obj.description.length;
    let new_row = tbl.insertRow(-1);
    for(let j=0;j<total_column;j++){
        if(album_obj.description[j].substr(-2) == 'id' || album_obj.description[j] == 'album_group') {
            continue;
        }
        let new_cell = new_row.insertCell(-1);
        new_cell.innerHTML = album_obj.description[j];
    }
    let view_but = new_row.insertCell(-1);
    let update_but = new_row.insertCell(-1);
    let delete_but = new_row.insertCell(-1);
    view_but.innerHTML = "詳細資料";
    update_but.innerText = "更新";
    delete_but.innerHTML = "刪除";

    let row_length = album_obj.rows.length;
    for(let i=0;i<row_length;i++){
        let new_row = tbl.insertRow(-1);
        let this_id = '';
        new_row.id = album_obj.rows[i][0];
        for(let j=0;j<total_column;j++){
            if(album_obj.description[j].substr(-4) == 's_id') {
                this_id = album_obj.rows[i][j];
                continue;
            }
            if(album_obj.description[j].substr(-2) == 'id' || album_obj.description[j] == 'album_group') {
                continue;
            }
            let new_cell = new_row.insertCell(-1);
            new_cell.setAttribute("contenteditable", true);
            new_cell.innerHTML = album_obj.rows[i][j];
        }
        let view_but = new_row.insertCell(-1);
        let update_but = new_row.insertCell(-1);
        let delete_but = new_row.insertCell(-1);
        view_but.innerHTML = '<input type="button" class="'+document.getElementById("table").value+'" name="'+this_id+'" onclick="view_full(this)" value="view"></input>';
        update_but.innerHTML = '<input type="button" class="'+document.getElementById("table").value+'" name="'+this_id+'" onclick="update_this(this, document.getElementById(\'albums\').rows[0].cells, document.getElementById(\'albums\').rows['+(i+1)+'].cells)" value="update"></input>';
        delete_but.innerHTML = '<input type="button" class="'+document.getElementById("table").value+'" name="'+this_id+'" onclick="delete_this(this)" value="delete"></input>';
    }
    document.getElementById("tbl_holder").appendChild(tbl);
}