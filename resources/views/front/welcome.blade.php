<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hydro power</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <style>
            *{
                box-sizing: border-box;
                padding: 0;
                margin: 0;
            }


        .main_sec{
            font-family: "Rubik", sans-serif;
          background-position: center;
          background-size: 100%;
          background-repeat: no-repeat;
          height: 100dvh;
          display: flex;
          align-items: center;
          justify-content: center;
          position: relative;
        }

        .main_sec div{
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            padding: 2rem 4rem;
            width: max-content;
        }

        .main_sec a:link,
        .main_sec a:visited{
            padding: 1rem;
            font-size: 24px;
            text-decoration: none;
            font-weight: 600;
        }


        .main_sec a:hover,
        .main_sec a:active{
        }

        .main_sec .footer{
            position: absolute;
            bottom: 0;
            padding: 20px;
            text-align: center;
            font-size: 16px;
            font-weight: 500;
            background: #000000a5;
            color: #eee;
            width: 100%;
        }
    </style>

</head>
<body>
    <section class="main_sec" style="background-image: url({{asset('assets/images/hydropowerMainBackground.jpg')}});">
        <div>
            <a href="{{ route('login') }}">Login</a>
        </div>

        <div class="footer">
            &copy;copyright 2024. Designed and Developed by Matinsoftech.
        </div>
    </section>
</body>
</html>
