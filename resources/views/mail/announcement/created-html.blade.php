<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" media="all"
          href="/assets/application-mailer-dbc5154d3c4160e8fa7ef52fa740fa402760c39b5d22c8f6d64ad5999499d263.css"/>
</head>
<!-- Edit the code below this line -->
<body class="bg-light">
<preview>New Announcement!</preview>
<div class="container">
    <img class="mx-auto mt-4 mb-3" width="150" height="60" src="https://cuic.us/storage/img/old-format/cuic-black.png"/>
    <div class="card w-100 mb-4">
        <div class="card-body">
            <div class="text-center">
                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="paper-plane"
                     class="svg-inline--fa fa-paper-plane fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 512 512" style="width: 50px">
                    <path fill="currentColor"
                          d="M476 3.2L12.5 270.6c-18.1 10.4-15.8 35.6 2.2 43.2L121 358.4l287.3-253.2c5.5-4.9 13.3 2.6 8.6 8.3L176 407v80.5c0 23.6 28.5 32.9 42.5 15.8L282 426l124.6 52.2c14.2 6 30.4-2.9 33-18.2l72-432C515 7.8 493.3-6.8 476 3.2z"></path>
                </svg>
            </div>

            <h2 class="text-center py-3">{{ $announcement['subject'] }}</h2>
            <div class="container">
                <div class="col">
                    New Announcement read more <a class="btn btn-primary btn-sm float-right"
                                                  href="{{ URL::to('/') }}/dashboard/annoucment/{{ $announcement['id'] }}">here</a>
                </div>
            </div>
        </div>
    </div>
    <table class="table-unstyled text-muted mb-4">
        <tbody>
        <tr>
            <td>© CUIC 2019</td>
            <td>
            </td>
        </tr>
        <tr>
            <td>1240 N Van Buren St</td>
        </tr>
        <tr>
            <td>Anaheim, CA 92807</td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
