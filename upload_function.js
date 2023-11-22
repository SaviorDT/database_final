var table_colums = {
    "albums": [["專輯名稱", "創作者", "類型", "釋出日期"], ['<input type="text" name="albums_name"></input>', '<input type="text" name="artists"></input>', '<select name="album_type"><option value="single">single</option><option vlaue="album">album</option></select>', '<input type="date" name="release_date"></input>'], ['albums_name', 'artists_name', 'album_type', 'release_date']],
    "tracks": [["單曲名稱", "所屬專輯", "長度", "", "釋出日期"], ['<input type="text" name="tracks_name"></input>', '<input type="text" name="albums_name"></input>', '<input type="number" name="tracks_duration" class="min"></input><lable>分</lable>', '<input type="number" name="tracks_duration" class="sec"></input><lable>秒</lable>', '<input type="date" name="release_date"></input>'], ['tracks_name', 'albums_name', 'tracks_duration', 'release_date']],
    "artists": [["創作者名稱", "追蹤人數"], ['<input type="text" name="artists_name"></input>', '<input type="number" name="followers"></input>'], ['artists_name', 'followers']]
}

function create_dbut(tbl){
    let delete_but = document.createElement("input");
    delete_but.type = "button";
    delete_but.id = "d"+flow;
    delete_but.value = "刪除上方表格";
    delete_but.addEventListener("click", function(e){
        delete_table(e.target);
    });
    tbl.appendChild(delete_but);
}

function create_colums(tbl, arr){
    for(let j=0;j<=1;j++){
        let row = tbl.insertRow(-1);
        for(let i=0;i<arr[j].length; i++){
            let cell = row.insertCell(-1);
            cell.innerHTML = arr[j][i];
        }
    }
}

function making_form(){
    let table_type = document.getElementById("upload_type").value;
    let tbl_holder = document.getElementById("tbl_holder");
    if(table_type != "0"){
        let new_table = document.createElement("table");
        new_table.id = flow;
        new_table.className = "insert";
        new_table.name = table_type;
        create_colums(new_table, table_colums[table_type]);
        create_dbut(new_table);
        tbl_holder.appendChild(new_table);
        flow++;
    }
    else{
        alert("請選擇欲上傳的類別");
    }
}

function delete_table(e){
    let eid = e.id;
    let id = eid.split("d");
    let tbl = document.getElementById(id[1]);
    tbl.removeChild(e);
    document.getElementById("tbl_holder").removeChild(tbl);
}

//document.getElementById("0").rows[1].cells[0]
function send_form(){
    let form = new FormData();
    form.append("action_type", "insert");
    let tbl_list = document.querySelectorAll(".insert");
    for(let i=0;i<tbl_list.length;i++){
        let current_table = tbl_list[i];
        form.append("action_columns", JSON.stringify(table_colums[current_table.name][2]));
        //form.append("table", current_table.name);
        if(current_table.name == "albums"){
            form.append("table", JSON.stringify(["albums", "artists"]));
        }
        else if(current_table.name == "tracks"){
            form.append("table", JSON.stringify(["tracks", "albums"]));
        }
        else{
            form.append("table", JSON.stringify(["artists"]));
        }

        let values = [];
        for(let j=0;j<table_colums[current_table.name][0].length;j++){
            let cur_cell = current_table.rows[1].cells[j].children[0];
            if(cur_cell.className == "min"){
                values.push(60*cur_cell.value);
            }
            else if(cur_cell.className == "sec"){
                values[values.length-1] = parseInt(values[values.length-1]) + parseInt(cur_cell.value);
            }
            else{
                values.push(cur_cell.value);
            }
        }
        form.append("values", JSON.stringify(values));
        //check_form(form);
        fetch("db_action.php", {
            method: "POST",
            body: form,
        })
        .then((res) => {
            return res.json();
        })
        .then((txt) => {
            console.log(txt.stat);
        });
    }
}