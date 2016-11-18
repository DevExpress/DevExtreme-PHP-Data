$(function() {
    var url = "http://localhost/php/service.php";
    var db = DevExpress.data.AspNet.createStore({
        key: "ID",
        loadUrl: url,
        insertUrl: url,
        updateUrl: url,
        deleteUrl: url
    });
    $("#gridContainer").dxDataGrid({
        dataSource: { 
            store: db
        },
        height: "100%",
        columns: [{
            dataField: "ID",
            dataType: "number",
            allowEditing: false
        }, {
            dataField: "Name"
        }, {
            dataField: "Category"
        }, {
            dataField: "CustomerName"
        }, {
            dataField: "BDate",
            dataType: "date"
        }],
        groupPanel: {
            visible: true
        },
        sorting: {
            mode: "multiple"
        },
        searchPanel: {
            visible: true
        },
        scrolling: {
            mode: "virtual"
        },
        filterRow: {
            visible: true
        },
        editing: {
            mode: "batch",
            allowAdding: true,
            allowUpdating: true,
            allowDeleting: true
        },
        grouping: {
            autoExpandAll: false
        },
        pager: {
            showPageSizeSelector: true,
            showInfo: true
        },
        summary: {
            totalItems: [{
                column: "ID",
                summaryType: "sum"
            },
            {
                column: "ID",
                summaryType: "avg"
            }, 
            {
                column: "ID", 
                summaryType: "min"
            }, {
                column: "ID", 
                summaryType: "max"
            }],
            groupItems: [{
                summaryType: "count"
            },{
                column: "ID", 
                summaryType: "min"
            }, {
                column: "ID", 
                summaryType: "max"}]
            },
            remoteOperations: {
                filtering: true,
                grouping: true,
                groupPaging: true,
                paging: true,
                sorting: true,
                summary: true  
            },
            headerFilter: {
                visible: true
            }
    });
});