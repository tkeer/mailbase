<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Mail Viewer</title>

    <link href="https://use.fontawesome.com/releases/v5.0.4/css/all.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .mail:hover, .mail.unread:hover {
            background-color: #e8e8ed !important;
        }

        .mail {
            cursor: pointer;
        }

        .mail.selected {
            background-color: #6c757d52 !important;
        }

        .mail.unread {
            background-color: white !important;
            border-right: #4f4f59 1px solid !important;
        }

        .pagination-links nav {
            float: right;
        }

        .pagination-links .page-link {
            color: dimgrey;
        }

        .pagination-links .active .page-link {
            background-color: #e8e8ed;
            border-color: #e8e8ed;
        }

        .pagination-links .active .page-link {
            background-color: #e8e8ed;
        }


        .pagination-links .page-link:focus {
            border-color: #e8e8ed;
            box-shadow: none;
        }


    </style>
</head>
<body style="overflow: hidden">

<div class="row bg-light border" style="height: 60px">
    <h3 style="margin: auto">{{ config('app.name') }} - Outgoing mails</h3>
</div>

<div class="row p-5">
    <div class="col-md-3">

        <div class="container-fluid" style="max-height: 650px; overflow-y: scroll">

            @forelse($mails as $mail)
                <div class="container bg-light p-2 flex-column border-bottom mail mail--js {{ $mail->is_read ?: 'unread' }}" data-id="{{ $mail->id }}">
                    <div>
                        {{ $mail->subject }}
                    </div>
                    <div style="display: flex; flex-wrap: wrap; justify-content: space-between">
                        <div>
                            @foreach($mail->to as $email => $name)
                                <span>{{ $email }}</span>
                            @endforeach
                        </div>
                        <div>
                            <span>{{ $mail->sent_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @empty
                No mail found
            @endforelse
            <div class="pagination-links">
                {{ $mails->links() }}
            </div>

        </div>

    </div>


    <div class="col-md-9">

        <div class="row flex-column pl-3">
            <h3 id="mail-subject"></h3>
            <p class="m-0">
                <span><strong>From:</strong></span>
                <span id="mail-from"></span>
            </p>
            <p class="m-0">
                <span><strong>To:</strong></span>
                <span id="mail-to"></span>
            </p>
        </div>

        <iframe class="border" width="100%" height="650px" id="mail-iframe">

        </iframe>

    </div>
</div>

<script
    src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
    crossorigin="anonymous">
</script>

<script>
  $(function () {
    $(".mail").click(function () {
      $('.mail.selected').removeClass('selected')
      $(this).addClass('selected').removeClass('unread')
      $.get('/mailbase/' + $(this).data('id'), function (response) {
        $("#mail-iframe").attr('srcdoc', response.body)
        $("#mail-subject").html(response.subject)
        $("#mail-to").text(response.htmlTo);
        $("#mail-from").text(response.htmlFrom)
      })
    }).first().click();
  })

</script>

</body>
</html>
