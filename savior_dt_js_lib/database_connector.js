function getRelationByID(target, source, this_ID, columns = null, order_by = null) {
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
    if(order_by !== null) {
        form_data.append('order', order_by);
    }
    
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
    .then((res) => {
        return res.json();
    });
}

async function changeRelationTable(table, columns, values) {
    let form_data = new FormData();
    form_data.append("action_type", "insert");
    form_data.append("table", table);
    form_data.append("action_columns", JSON.stringify(columns));
    form_data.append("values", JSON.stringify(values));

    let add_relate = true;
    await fetch("db_action.php", {
        method: "POST",
        body: form_data,
    })
    .then((res) => {
        return res.json();
    })
    .then((json) => {
        if(json.stat == 0) {
            deleteRelationTable(table, values);
            add_relate = false;
        }
    });
    return add_relate;
}

function updateRelationTable(table, old_values, update_column, new_value) {
    let form_data = new FormData();
    form_data.append('action_type', 'update');
    form_data.append('table', table);
    form_data.append('ID', old_values[0] + ',' + old_values[1]);
    form_data.append('action_columns', JSON.stringify([update_column]));
    form_data.append('values', JSON.stringify([new_value]));

    fetch('db_action.php', {
        method: "POST",
        body: form_data,
    })
    .then((res) => {
        return res.json();
    });
}

function deleteRelationTable(table, old_values) {
    let form_data = new FormData();
    form_data.append('action_type', 'delete');
    form_data.append('table', table);
    form_data.append('ID', old_values[0] + ',' + old_values[1]);

    fetch('db_action.php', {
        method: "POST",
        body: form_data,
    })
    .then((res) => {
        return res.json();
    });
}

function createData(table, columns, values) {
    let form_data = new FormData();
    form_data.append("action_type", "insert");
    form_data.append("table", table);
    form_data.append("action_columns", JSON.stringify(columns));
    form_data.append("values", JSON.stringify(values));

    return fetch("db_action.php", {
        method: "POST",
        body: form_data,
    })
    .then((res) => {
        return res.json();
    });
}

function updateData(table, id, columns, values) {
    let form_data = new FormData();
    form_data.append('action_type', 'update');
    form_data.append('table', table);
    form_data.append('ID', id);
    form_data.append('action_columns', JSON.stringify(columns));
    form_data.append('values', JSON.stringify(values));

    return fetch('db_action.php', {
        method: "POST",
        body: form_data,
    })
    .then((res) => {
        return res.json();
    });
}