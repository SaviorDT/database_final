var table_colums = {
    "albums": [["專輯名稱", "類型", "釋出日期", "知名度"], ['<input type="text" name="albums_name"></input>', '<input type="text" name="album_type"></input>', '<input type="date" name="release_date"></input>', '<input type="number" name="albums_popularity" min="0" max="100"></input>'], ['albums_name', 'album_type', 'release_date', 'albums_popularity']],
    "tracks": [["單曲名稱", "所在唱片號", "長度(毫秒)", "是否兒童不宜", "預覽網址", "在唱片第幾首", "知名度", "能否播放"], 
        ['<input type="text" name="tracks_name"></input>', '<input type="number" name="disc_number" min="1"></input>', '<input type="number" name="tracks_duration" min="0"></input>', '<input type="number" name="explicit" min="0" max="1"></input>', '<input type="text" name="preview_url"></input>', '<input type="number" name="track_number" min="0"></input>', '<input type="number" name="tracks_popularity" min="0" max="100"></input>', '<input type="number" name="is_playable" min="0" max="1"></input>'], 
        ['tracks_name', 'disc_number', 'tracks_duration', 'explicit', 'preview_url', 'track_number', 'tracks_popularity', 'is_playable']],
    "artists": [["創作者名稱", "知名度", "追蹤人數"], ['<input type="text" name="artists_name"></input>', '<input type="number" name="artists_popularity" min="0" max="100"></input>', '<input type="number" name="followers" min="0"></input>'], ['artists_name', 'artists_popularity', 'followers']]
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
    let tbl_list = document.querySelectorAll(".insert");
    for(let i=0;i<tbl_list.length;i++){
        let form = new FormData();
        form.append("action_type", "insert");
        let current_table = tbl_list[i];
        form.append("table", current_table.name);

        let columns = [];
        let values = [];
        for(let cell of current_table.rows[1].cells){
            let input = cell.children[0];
            if(input.value == '') {
                continue;
            }
            columns.push(input.name);
            values.push(input.value);
        }
        form.append("action_columns", JSON.stringify(columns));
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
            console.log(txt);
        });
    }
}