var table_colums = {
    "albums": [["名稱", "創作者", "類型"], ['<input type="text" id="albums_name"></input>', '<input type="text" id="artists"></input>', '<select id="album_type"><option value="single">single</option><option vlaue="album">album</option></select>']],
    "tracks": [["名稱", "所屬專輯", "長度"], ['<input type="text" id="tracks_name"></input>', '<input type="text" id="albums_name"></input>', '<input type="number" id="min_tracks_duration"></input><lable>分</lable><input type="number" id="sec_tracks_duration"></input><lable>秒</lable>']],
    "artists": [["名稱", "追蹤人數"], ['<input type="text" id="artists_name"></input>', '<input type="number" id="followers"></input>']]
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