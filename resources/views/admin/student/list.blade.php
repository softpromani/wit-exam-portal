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
            fetch("{{ route('admin.student.list') }}")
                .then(response => response.json())
                .then(data => {
                    let table = new Tabulator("#studentTable", {
                        layout: "fitColumns",
                        pagination: "remote",
                        paginationSize: 10,
                        paginationSizeSelector: [10, 25, 50, 100],
                        ajaxURL: "{{ route('admin.student.list') }}",
                        ajaxParams: function () {
                            return { search: document.getElementById("search").value };
                        },
                        ajaxResponse: function(url, params, response) {
                            return { data: response.data, last_page: response.last_page };
                        },
                        columns: data.columns // Get columns from the server
                    });

                    document.getElementById("search").addEventListener("keyup", function () {
                        table.setData("{{ route('admin.student.list') }}", { search: this.value });
                    });
                });
        });
    </script>
@endsection