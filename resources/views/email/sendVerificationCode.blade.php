@extends('email.layouts.app')

@section('content')
    <table cellspacing="0" cellpadding="0" width="500" align="center" border="0" bgcolor="#ffffff">
                <tr>
                    <td colspan="5" style="text-align:center; padding-top:78px; padding-bottom:38px;"><span style= "font-family:'Open Sans', sans-serif; font-size: 26px; line-height: 26px; font-weight: normal; color: #333333;">
                                                    {{config('app.name')}} {{trans('email.verificationCodeMessage')}}</span></td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align:center;padding-bottom:16px;">
                        <span style= "font-family:'Open Sans', sans-serif; font-size: 18px; line-height: 26px; font-weight: normal; color: #333333;">
                            {{trans('email.yourVerificationCodeIsMessage')}}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align:center;padding-bottom:38px;">
                        <table cellspacing="0" cellpadding="0" width="275px" height="52px;" align="center" border="0" bgcolor="#eeeeee">
                            <tr>
                                <td>
                                    <span style="font-family:'Open Sans', sans-serif; text-align:center; color:{{config('emailTemplate.content_verification_code')}}; font-size:24px; font-weight:bold; letter-spacing:3px;">{{$verification_code}}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
@endsection

@section('warning')
    <span style= "font-family:'Open Sans', sans-serif; font-size: 16px; line-height: 26px; font-weight: bold; color: #ff0000;">
        {{trans('email.noteMessage')}}
    </span>
    <span style= "font-family:'Open Sans', sans-serif; font-size: 16px; line-height: 26px; font-weight: normal; color: #535353;">
        {{trans('email.verificationCodeExpiresMessage')}}
    </span>
@endsection
