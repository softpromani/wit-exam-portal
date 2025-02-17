@extends('admin.includes.master')
@section('style_area')
<link href="https://unpkg.com/tabulator-tables@5.5.0/dist/css/tabulator.min.css" rel="stylesheet">
<script src="https://unpkg.com/tabulator-tables@5.5.0/dist/js/tabulator.min.js"></script>
@endsection
@section('content')
<div id="studentTable"></div>
@endsection
@section('script_section')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let table = new Tabulator("#studentTable", {
            layout: "fitColumns",
            pagination: "remote",
            paginationSize: 10,
            ajaxURL: "{{route('admin.student.ajax-list')}}", // Change this URL based on your route
            ajaxParams: function() {
                let searchInput = document.getElementById("search");
                return {
                    search: searchInput ? searchInput.value : "" // Prevent error if input is missing
                };
            },
            columns: [],
            ajaxResponse: function(url, params, response) {
                console.log(response); // Debugging: check the response in console
                if (response.columns) {
                    this.setColumns(response.columns);
                    // this.setData(response.data);
                }
                return response;
            }

        });

        document.getElementById("search").addEventListener("input", function() {
            table.setData();
        });
    });
</script>
@endsection