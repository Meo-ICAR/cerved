@extends('layouts.app')

@section('title', 'Modifica Report')

@section('content')
    @include('reports.partials.edit-form')
@endsection

@push('scripts')
    <script src="https://cdn.tiny.cloud/1/9idy32wo5hex1gtp8pdi5akgk3oxu44e4ndyj3cfrr5yx4bb/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '#annotation',
                plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                menubar: false,
                height: 300,
                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',
                setup: function(editor) {
                    editor.on('change', function() {
                        editor.save();
                    });
                }
            });
        });
    </script>
@endpush
