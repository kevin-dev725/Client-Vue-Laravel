<?php
/**
* @var \App\QuickbooksImport $import
*/
?>
@component('mail::message')
@if($import->status === \App\QuickbooksImport::STATUS_ERROR)
Something went wrong during the Quickbooks import. Please try again.
@elseif($import->status === \App\QuickbooksImport::STATUS_FINISHED_WITH_ERROR)
Quickbooks import finished with some errors:
@component('mail::table')
| Customer       | Error         |
| ------------- |:-------------:|
@foreach($import->errors as $error)
| {{ $error['customer'] }} | @foreach($error['error'] as $field => $error_message) {{ str_replace('_', ' ', title_case($field)) }}: {{ $error_message[0] }} <br/> @endforeach |
@endforeach
@endcomponent
@else
Quickbooks import finished with no issues.
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
