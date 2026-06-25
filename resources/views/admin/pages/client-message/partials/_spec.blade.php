@if ($type)
    <div class="alert alert-primary">
        <strong>Format</strong>
        <div>{!! $type->format ?: 'No format guidance provided.' !!}</div>
    </div>
    <div class="alert alert-danger">
        <strong>Restriction</strong>
        <div>{!! $type->restriction ?: 'No restrictions provided.' !!}</div>
    </div>
    <div class="alert alert-warning">
        <strong>Mandatory</strong>
        <div>{!! $type->mandatory ?: 'Nothing mandatory specified.' !!}</div>
    </div>
@endif
