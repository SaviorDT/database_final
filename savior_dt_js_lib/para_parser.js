function stringInputToPost(input, column_name, columns, values) {
    if(input.value != '') {
        columns.push(column_name);
        values.push(input.value);
    }
}

function rangeInputToPost(input1, input2, column_name, columns, values) {
    if(input1.value != '' || input2.value != '') {
        columns.push(column_name);
        values.push('[' + input1.value + ',' + input2.value + ']');
    }
}

function optionInputToPost(select, column_name, columns, values) {
    if(select.value != -1) {
        columns.push(column_name);
        values.push(select.value);
    }
}