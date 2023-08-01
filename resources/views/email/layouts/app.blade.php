<html>
<head>
    <title>Email</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
</head>
<body>
<table cellspacing="0" cellpadding="0" border="0" align="center" bgcolor="#f3f3f3" width="600">
    <tr>
        <td>
            <table cellspacing="0" cellpadding="0" width="600" align="center" border="0" bgcolor="#f3f3f3">
                <tr height="100">
                    <td width="50"></td>
                    <td with="164" height="35">
                        <a href="{{config('app.url')}}" style="text-decoration: none;" target="_blank">
                            <img src="{{HelperFunctions::getEmailTemplateImages('storage/image/logo/','emailTemplate.logo_name','images/logo/logo1.png')}}">
                        </a>
                    </td>
                    <td width="165"></td>
                    <td width="171">
                    </td>
                    <td width="50"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table cellspacing="0" cellpadding="0" width="500" align="center" border="0" bgcolor="#f3f3f3">
                <tr height="184">
                    <td colspan="3" bgcolor="{{config('emailTemplate.envelope_bgcolor')}}" style="text-align:center;">
                        <img src="{{HelperFunctions::getEmailTemplateImages('storage/image/email/','emailTemplate.envelope_icon_name','images/email/emailTemplateEnvelope.png')}}">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            @yield('content')
        </td>
    </tr>
    <tr>
        <td>
            <table cellspacing="0" cellpadding="0" width="600" align="center" border="0" bgcolor="#f3f3f3">
                <tr>
                    <td colspan="5" style="text-align:center; padding-top:40px; padding-bottom:60px;">
                        @yield('warning')
                    </td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align:center;padding-bottom: 20px;">
                        <span style="color:#6a6a6a; font-family:'Open Sans', sans-serif; font-size: 12px; line-height: 20px; font-weight: normal;">{{trans('email.copyright@2018')}} <a href="{{config('app.url')}}">{{config('app.url')}}</a>,{{trans('email.allRightsReserved')}}</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>