@component('mail::message')
# Your login details:
## Username: **{{ $employee->email }}**
## Password: **{{ $password }}**

You can use this credentials to login to your account on [{{ config('app.name') }}]({{ env('APP_URL') }})

@component('mail::button', ['url' => env('APP_URL')])
Go to the app
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
