<?php
/**
 * @var \App\ClientImport $import
 */
?>
@component('mail::message')
@if(!$import->exception)
Your csv contains invalid data. Please check row {{ $import->invalid_row['row_index'] + 1 }}:

@component('mail::table')
| Field       | Error Message         |
| ------------- | ------------- |
@foreach($import->errors as $field => $error)
| {{ array_get($import->invalid_row, 'row.' . $field, '') }} | {{ $error[0] }} |
@endforeach
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@else
Error importing csv. This is probably due to invalid names in the header row.

{{ config('app.name') }}
@endif
@endcomponent
