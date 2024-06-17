@if ($message = Session::get('success'))
<x-auth-session-status class="text-2xl" :status="$message"/>
@endif


@if ($message = Session::get('error'))
<x-auth-session-status class="text-red-600 text-2xl" :status="$message"/>
@endif


@if ($message = Session::get('warning'))
<x-auth-session-status class="text-red-300 text-2xl" :status="$message"/>
@endif


@if ($message = Session::get('info'))
<x-auth-session-status class="text-indigo-300 text-2xl" :status="$message"/>
@endif


@if ($errors->any())
<x-auth-session-status class="text-red-600 text-2xl" :status="$message"/>
@endif
