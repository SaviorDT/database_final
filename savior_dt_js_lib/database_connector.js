function getRelationByID(target, source, this_ID, columns = null) {
    let form_data = new FormData();
    form_data.append('action_type', 'select');
    form_data.append('table', source);
    form_data.append('join', JSON.stringify([target]));
    if(columns === null) {
        form_data.append('display_columns', JSON.stringify([target + '_name']));
    }
    else {
        form_data.append('display_columns', JSON.stringify(columns));
    }
    form_data.append('action_columns', JSON.stringify([source + '_id']));
    form_data.append('values', JSON.stringify([this_ID]));
    
    return fetch('db_action.php', {
        method: "POST",
        body: form_data,
    })
    .then((res) => {
        return res.json();
    });
}

function getDataWithID(table, this_id, columns = null) {
    let form_data = new FormData();
    form_data.append('action_type', 'select');
    form_data.append('table', table);
    form_data.append('action_columns', '["'+table+'_id"]');
    form_data.append('values', '["'+this_id+'"]');

    if(columns !== null) {
        form_data.append('display_columns', JSON.stringify(columns));
    }

    return fetch('db_action.php', {
        method: "POST",
        body: form_data,
    })
    .then(async (res) => {
        return res.json();
    });
}