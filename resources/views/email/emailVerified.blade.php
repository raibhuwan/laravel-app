{{--<h1>LoveLock</h1>
<p>
    Your Email is successfully verified.
</p>--}}
@extends('email.layouts.app')

@section('content')
    <table cellspacing="0" cellpadding="0" width="500" align="center" border="0" bgcolor="#ffffff">
        <tr>
            <td colspan="5" style="text-align:center; padding-top:78px; padding-bottom:38px;">
                <span style= "font-family:'Open Sans', sans-serif; font-size: 26px; line-height: 26px; font-weight: normal; color: #333333;">
                    {{config('app.name')}}
                </span>
            </td>
        </tr>
        <tr>
            <td colspan="5" style="text-align:center;padding-bottom:16px;">
                <span style= "font-family:'Open Sans', sans-serif; font-size: 18px; line-height: 26px; font-weight: normal; color: #333333;">
                    {{trans('email.emailSuccessfullyVerified')}}
                </span>
            </td>
        </tr>
        <tr>
            <td colspan="5" style="text-align:center;padding-bottom:38px;">
                <table width="275px" height="52px;" align="center" border="0" style="font-weight: bold;">
                    <tr>
                        <td>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection