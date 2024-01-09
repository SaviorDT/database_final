function autoUpdateSuggest(input_field, datalist, table, column) {
    input_field.addEventListener('input', () => {
        let form_data = new FormData();
        form_data.append('action_type', 'select');
        form_data.append('table', table);
        form_data.append('limit', 6);
        form_data.append('display_columns', JSON.stringify([column]));
        form_data.append('action_columns', JSON.stringify([column]));
        form_data.append('values', JSON.stringify([input_field.value + '%']));
        
        fetch('db_action.php', {
            method: "POST",
            body: form_data,
        })
        .then((res) => {
            return res.json();
        })
        .then((json) => {
            updateSuggest(datalist, json.rows);
        })
    })
}

function updateSuggest(datalist, suggests) {
    datalist.innerHTML = '';

    suggests.forEach(function(item) {
        let option = document.createElement('option');
        option.value = item[0];
        datalist.appendChild(option);
    });
}