@component('mail::message')

Comment: {{ $review->comment }}<br/>
Flagged Word/Phrase: {{ $review->flagged_phrase }}<br/>

@component('mail::button', ['url' => url('/backend/flagged-reviews')])
View Flagged Reviews
@endcomponent

@endcomponent
