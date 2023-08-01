@extends('email.layouts.app')

@section('content')
    <div class="container" style="text-align: center; margin-top: 20px; margin-left: 50px; margin-right: 50px">

        <table width="100%">
            <tr>
                <td>
                    <p style="text-align: left">
                        @if (! empty($greeting))
                            # {{ $greeting }}
                        @else
                            @if ($level == 'error')
                                Whoops!
                            @else
                                Hello,
                            @endif
                        @endif
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        @foreach ($introLines as $line)
                            {{ $line }}
                        @endforeach
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    {{-- Action Button --}}
                    @isset($actionText)
                        <?php
                        switch ($level) {
                            case 'success':
                                $color = 'green';
                                break;
                            case 'error':
                                $color = 'red';
                                break;
                            default:
                                $color = 'blue';
                        }
                        ?>
                        <a href="{{ $actionUrl }}"
                           class="button button-{{ $color or 'blue' }}"
                           target="_blank"
                           style=" margin-top: 10px; margin-bottom: 10px"
                        >
                            {{ $actionText }}
                        </a>
                    @endisset
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        @foreach ($outroLines as $line)
                            {{ $line }}
                        @endforeach

                        @if (! empty($salutation))
                            {{ $salutation }}
                        @else
                            Regards,<br>{{ config('app.name') }}
                        @endif

                        @isset($actionText)
                            @component('mail::subcopy')
                                If you’re having trouble clicking the "{{ $actionText }}" button, copy and paste the URL
                                below
                                into your web browser: [{{ $actionUrl }}]({{ $actionUrl }})
                            @endcomponent
                        @endisset

                    </p>
                </td>
            </tr>
        </table>
    </div>
@endsection
