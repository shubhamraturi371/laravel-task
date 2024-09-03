<!-- emails/campaign.blade.php -->
@component('mail::message')
    # Hello {{ $data->name }}

    We would like to introduce you to {{ $data->subject }}.

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
